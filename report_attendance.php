<?php require_once 'common_file.php'; 
$from_page = 'Attendance Report';

if ($user_role != 'admin' && !$is_management && !checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'report_attendance', PERMISSION_VIEW)) { 
    exit('Unauthorized'); 
}

$from_date = date('Y-m-d', strtotime('-30 days'));
$to_date = date('Y-m-d');
$report_type = 'staff'; // default

if(isset($_POST['report_type']) && !empty($_POST['report_type'])) {
    $report_type = $bf->sanitize($_POST['report_type']);
}

if(isset($_POST['from_date']) && !empty($_POST['from_date'])) {
    $from_date = $bf->sanitize($_POST['from_date']);
}

if(isset($_POST['to_date']) && !empty($_POST['to_date'])) {
    $to_date = $bf->sanitize($_POST['to_date']);
}

$staff_id = '';
if(isset($_POST['staff_id']) && !empty($_POST['staff_id'])) {
    $staff_id = $bf->sanitize($_POST['staff_id']);
}

$student_id = '';
if(isset($_POST['student_id']) && !empty($_POST['student_id'])) {
    $student_id = $bf->sanitize($_POST['student_id']);
}

// Fetch staff list for filter dropdown
$staff_list = $bf->getTableRecords($GLOBALS['staff_table'], 'deleted', 0, 'staff_name ASC');

// Fetch students list for filter dropdown
$students_options = [];
$t_students = $bf->getTableRecords($GLOBALS['enrollment_table'], 'deleted', 0);
if (!empty($t_students)) {
    foreach ($t_students as $ts) {
        $dec_id = $bf->encode_decode('decrypt', $ts['student_id']);
        $students_options[$ts['student_id']] = $dec_id . ' - ' . $ts['student_name'] . ' (Training)';
    }
}
$i_students = $bf->getTableRecords($GLOBALS['enrollment_internship_table'], 'deleted', 0);
if (!empty($i_students)) {
    foreach ($i_students as $is) {
        $dec_id = $bf->encode_decode('decrypt', $is['student_id']);
        $students_options[$is['student_id']] = $dec_id . ' - ' . $is['student_name'] . ' (Internship)';
    }
}
asort($students_options);

// Fetch report data
$attendance_data = [];
$total_present_days = 0;
$total_absent_days = 0;
$unique_dates = [];
$total_marked = 0;

if ($report_type === 'staff') {
    $where = "a.deleted = 0 AND a.company_id = :comp_id";
    $params = [':comp_id' => $_SESSION['company_id']];

    if (!empty($staff_id)) {
        $where .= " AND a.staff_id = :staff_id";
        $params[':staff_id'] = $staff_id;
    }
    if (!empty($from_date)) {
        $where .= " AND a.attendance_date >= :from_date";
        $params[':from_date'] = $from_date;
    }
    if (!empty($to_date)) {
        $where .= " AND a.attendance_date <= :to_date";
        $params[':to_date'] = $to_date;
    }

    $query = "SELECT a.* 
              FROM {$GLOBALS['attendance_table']} a 
              WHERE $where 
              ORDER BY a.attendance_date DESC, a.staff_name ASC";
    $attendance_data = $bf->getQueryRecords($query, $params);
} else {
    $where = "sa.deleted = 0 AND sa.company_id = :comp_id";
    $params = [':comp_id' => $_SESSION['company_id']];

    if (!empty($student_id)) {
        $where .= " AND sa.student_id = :student_id";
        $params[':student_id'] = $student_id;
    }
    if (!empty($from_date)) {
        $where .= " AND sa.attendance_date >= :from_date";
        $params[':from_date'] = $from_date;
    }
    if (!empty($to_date)) {
        $where .= " AND sa.attendance_date <= :to_date";
        $params[':to_date'] = $to_date;
    }

    $query = "SELECT sa.* 
              FROM {$GLOBALS['student_attendance_table']} sa 
              WHERE $where 
              ORDER BY sa.attendance_date DESC, sa.id ASC";
    $attendance_data = $bf->getQueryRecords($query, $params);
}

// Calculate metrics
if (!empty($attendance_data)) {
    foreach ($attendance_data as $row) {
        $total_marked++;
        $unique_dates[$row['attendance_date']] = true;
        
        switch ($row['present_code']) {
            case 'PP':
                $total_present_days += 1;
                break;
            case 'AA':
                $total_absent_days += 1;
                break;
            case 'PA':
            case 'AP':
                $total_present_days += 0.5;
                $total_absent_days += 0.5;
                break;
        }
    }
}
$total_days = count($unique_dates);
$attendance_rate = $total_marked > 0 ? ($total_present_days / $total_marked) * 100 : 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report - <?php echo get_company_name(); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .present-color { color: #10b981; font-weight: 700; }
        .absent-color { color: #ef4444; font-weight: 700; }
        .half-color { color: #f59e0b; font-weight: 700; }
        
        .summary-cards { display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
        .summary-card { flex: 1; min-width: 180px; padding: 1.25rem; border-radius: 0.75rem; border: 1px solid var(--border); }
        .summary-card .label { font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.25rem; }
        .summary-card .value { font-size: 1.3rem; font-weight: 700; }
        .filter-row { display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: flex-end; }
        .filter-row .form-group { flex: 1; min-width: 160px; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content update_content">
        <div class="header">
            <h2><?php echo htmlspecialchars($from_page); ?></h2>
        </div>

        <div class="module-section">
            <div id="attendance_report_container">
                <form name="attendance_report_form" id="attendance_report_form" method="post">
                    <div class="filter-row">
                        <div class="form-group">
                            <label>Report Type</label>
                            <select name="report_type" id="report_type" class="form-input" onchange="onReportTypeChange()">
                                <option value="staff" <?php echo $report_type === 'staff' ? 'selected' : ''; ?>>Staff Attendance</option>
                                <option value="student" <?php echo $report_type === 'student' ? 'selected' : ''; ?>>Student Attendance</option>
                            </select>
                        </div>

                        <!-- Staff Filter -->
                        <div class="form-group" id="filter_staff" style="<?php echo $report_type === 'staff' ? '' : 'display:none;'; ?>">
                            <label>Staff Member</label>
                            <select name="staff_id" id="staff_id" class="form-input" onchange="getReport()">
                                <option value="">All Staff Members</option>
                                <?php foreach ($staff_list as $staff) { ?>
                                    <option value="<?php echo $staff['staff_id']; ?>" <?php echo $staff_id === $staff['staff_id'] ? 'selected' : ''; ?>><?php echo $staff['staff_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Student Filter -->
                        <div class="form-group" id="filter_student" style="<?php echo $report_type === 'student' ? '' : 'display:none;'; ?>">
                            <label>Student</label>
                            <select name="student_id" id="student_id" class="form-input" onchange="getReport()">
                                <option value="">All Students</option>
                                <?php foreach ($students_options as $sid => $sname) { ?>
                                    <option value="<?php echo $sid; ?>" <?php echo $student_id === $sid ? 'selected' : ''; ?>><?php echo $sname; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Common date filters -->
                        <div class="form-group">
                            <label>From Date</label>
                            <input type="date" name="from_date" id="from_date" class="form-input"
                                value="<?php echo $from_date; ?>" max="<?php echo date('Y-m-d'); ?>" onchange="getReport()">
                        </div>
                        <div class="form-group">
                            <label>To Date</label>
                            <input type="date" name="to_date" id="to_date" class="form-input"
                                value="<?php echo $to_date; ?>" max="<?php echo date('Y-m-d'); ?>" onchange="getReport()">
                        </div>
                    </div>
                </form>

                <!-- Summary Cards -->
                <div class="summary-cards" style="margin-top: 1.5rem;">
                    <div class="summary-card" style="background: rgba(14,165,233,0.06);">
                        <div class="label">Total Marked Days</div>
                        <div class="value" style="color: var(--primary);"><?php echo $total_days; ?> Days</div>
                    </div>
                    <div class="summary-card" style="background: rgba(16,185,129,0.06);">
                        <div class="label">Total Present</div>
                        <div class="value present-color"><?php echo $total_present_days; ?> Days</div>
                    </div>
                    <div class="summary-card" style="background: rgba(239,68,68,0.06);">
                        <div class="label">Total Absent</div>
                        <div class="value absent-color"><?php echo $total_absent_days; ?> Days</div>
                    </div>
                    <div class="summary-card" style="background: rgba(16,185,129,0.06);">
                        <div class="label">Attendance Rate</div>
                        <div class="value present-color"><?php echo number_format($attendance_rate, 1); ?>%</div>
                    </div>
                </div>

                <div class="report-actions">
                    <button type="button" class="btn-report btn-print"
                        onclick="window.open('reports/rpt_attendance_report.php?report_type=<?php echo $report_type; ?>&staff_id=<?php echo $staff_id; ?>&student_id=<?php echo $student_id; ?>&from_date=<?php echo $from_date; ?>&to_date=<?php echo $to_date; ?>', '_blank')">
                        <i class="fas fa-print"></i> Print Report
                    </button>
                    <button type="button" class="btn-report btn-excel"
                        onclick="getExcelReport();">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                </div>

                <table class="responsive table table-bordered" id="attendance_report_table">
                    <thead>
                        <tr>
                            <th>Sno</th>
                            <th>Date</th>
                            <?php if ($report_type === 'staff'): ?>
                                <th>Staff ID</th>
                                <th>Staff Name</th>
                                <th>Staff Role</th>
                            <?php else: ?>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Course Type</th>
                                <th>Marked By</th>
                            <?php endif; ?>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $sno = 1;
                            if(!empty($attendance_data)) {
                                foreach ($attendance_data as $row) {
                                    $status_badge = '';
                                    switch ($row['present_code']) {
                                        case 'PP':
                                            $status_badge = '<span class="status-badge" style="background: rgba(16,185,129,0.12); color: #10b981; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 0.3rem; font-size: 0.8rem;">Full Present</span>';
                                            break;
                                        case 'AA':
                                            $status_badge = '<span class="status-badge" style="background: rgba(239,68,68,0.12); color: #ef4444; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 0.3rem; font-size: 0.8rem;">Full Absent</span>';
                                            break;
                                        case 'PA':
                                            $status_badge = '<span class="status-badge" style="background: rgba(245,158,11,0.12); color: #f59e0b; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 0.3rem; font-size: 0.8rem;">FN - P | AN - A</span>';
                                            break;
                                        case 'AP':
                                            $status_badge = '<span class="status-badge" style="background: rgba(245,158,11,0.12); color: #f59e0b; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 0.3rem; font-size: 0.8rem;">FN - A | AN - P</span>';
                                            break;
                                    }
                        ?>
                                    <tr>
                                        <td><?php echo $sno++; ?></td>
                                        <td><strong><?php echo date('d-m-Y', strtotime($row['attendance_date'])); ?></strong></td>
                                        <?php if ($report_type === 'staff'): ?>
                                            <td><span style="font-family: monospace; color: var(--text-muted);"><?php echo htmlspecialchars($row['staff_number']); ?></span></td>
                                            <td><strong><?php echo htmlspecialchars($row['staff_name']); ?></strong></td>
                                            <td><span class="status-badge" style="background: var(--primary-light); color: var(--primary);"><?php echo htmlspecialchars($row['staff_role']); ?></span></td>
                                        <?php else: 
                                            $student_name = 'N/A';
                                            $student_type = 'N/A';
                                            $student_dec_id = '';
                                            if (!empty($row['student_id'])) {
                                                $stu_rec = $bf->getTableRecords($GLOBALS['enrollment_table'], 'student_id', $row['student_id']);
                                                if (!empty($stu_rec)) {
                                                    $student_name = $stu_rec[0]['student_name'];
                                                    $student_type = 'Training';
                                                    $student_dec_id = $bf->encode_decode('decrypt', $stu_rec[0]['student_id']);
                                                } else {
                                                    $stu_rec = $bf->getTableRecords($GLOBALS['enrollment_internship_table'], 'student_id', $row['student_id']);
                                                    if (!empty($stu_rec)) {
                                                        $student_name = $stu_rec[0]['student_name'];
                                                        $student_type = 'Internship';
                                                        $student_dec_id = $bf->encode_decode('decrypt', $stu_rec[0]['student_id']);
                                                    }
                                                }
                                            }
                                            $trainer_name = 'N/A';
                                            if (!empty($row['staff_id'])) {
                                                $trainer_name = $bf->getTableColumnValue($GLOBALS['staff_table'], 'staff_id', $row['staff_id'], 'staff_name') ?? 'N/A';
                                            }
                                        ?>
                                            <td><span style="color: var(--primary); font-weight: 600;"><?php echo htmlspecialchars($student_dec_id); ?></span></td>
                                            <td><strong><?php echo htmlspecialchars($student_name); ?></strong></td>
                                            <td><span class="status-badge" style="background: var(--primary-light); color: var(--primary);"><?php echo $student_type; ?></span></td>
                                            <td><span style="color: var(--text-muted); font-size: 0.85rem;"><?php echo htmlspecialchars($trainer_name); ?></span></td>
                                        <?php endif; ?>
                                        <td><?php echo $status_badge; ?></td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="7" style="text-align:center; padding: 2rem; color: var(--text-muted);">No attendance records found.</td>
                                </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script src="main/js/script.js"></script>
    <script src="main/js/xlsx.full.min.js" type="text/javascript"></script>
    <script>
        function onReportTypeChange() {
            var reportType = $('#report_type').val();
            if (reportType === 'staff') {
                $('#filter_staff').show();
                $('#filter_student').hide();
                $('#student_id').val('');
            } else {
                $('#filter_staff').hide();
                $('#filter_student').show();
                $('#staff_id').val('');
            }
            getReport();
        }

        function getReport() {
            if($('#attendance_report_form').length > 0) {
                $('#attendance_report_form').submit();
            }
        }

        function getExcelReport() {
            let table = document.getElementById("attendance_report_table");
            let workbook = XLSX.utils.table_to_book(table, {
                sheet: "Attendance Report"
            });
            XLSX.writeFile(workbook, "Attendance_Report.xlsx");
        }
    </script>
</body>
</html>
