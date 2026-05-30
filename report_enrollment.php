<?php require_once 'common_file.php'; 
$from_page = 'Enrollment Report';

$start = ""; $limit = ""; $from_date = ""; $to_date = "";
$courses = [];

$courses = $bf->getTableRecords($GLOBALS['course_table'], 'deleted', 0);
// print_r($courses);
$from_date = date('Y-m-d', strtotime('-30 days'));
$to_date = date('Y-m-d');

$course_type = '';
if(isset($_POST['course_type']) && !empty($_POST['course_type'])) {
    $course_type = $bf->sanitize($_POST['course_type']);
}

$course_id = '';
if(isset($_POST['course_id']) && !empty($_POST['course_id'])) {
    $course_id = $bf->sanitize($_POST['course_id']);
}

if(isset($_POST['from_date']) && !empty($_POST['from_date'])) {
    $from_date = $bf->sanitize($_POST['from_date']);
}

if(isset($_POST['to_date']) && !empty($_POST['to_date'])) {
    $to_date = $bf->sanitize($_POST['to_date']);
} 

$enrollments = $bf->getEnrollmentReportList($course_type, $course_id, $from_date, $to_date);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - <?php echo get_company_name(); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content update_content">
        <div class="header">
            <h2><?php if(!empty($from_page))  echo ucfirst($from_page); ?></h2>
        </div>

        <div class="module-section">

            <div id="course_list">
                <form name="enrollment_report_form" id="enrollment_report_form" method="post">
                    <div class="form-row">
                        <div class="form-group col-3">
                            <label>Course Type </label>
                            <select name="course_type" id="course_type" class="form-input" onchange="getReport()">
                                <option value="">Select</option>
                                <option value="training" <?php echo $course_type === 'training' ? 'selected' : ''; ?>>Training</option>
                                <option value="internship" <?php echo $course_type === 'internship' ? 'selected' : ''; ?>>Internship</option>
                            </select>
                        </div>
                        <div class="form-group col-3">
                            <label>Course  </label>
                            <select name="course_id" id="course_id" class="form-input" onchange="getReport()">
                                <option value="">Select</option>
                                <?php foreach ($courses as $data) { ?>
                                    <option value="<?php echo $data['course_id']; ?>" <?php echo $course_id === $data['course_id'] ? 'selected' : ''; ?>><?php echo $data['course_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group col-3">
                            <label>From Date </label>
                            <input
                                type="date"
                                name="from_date"
                                id="from_date"
                                class="form-input"
                                value="<?php echo $from_date; ?>"
                                max="<?php echo date('Y-m-d'); ?>" onchange="getReport()"
                            >
                        </div>
                        <div class="form-group col-3">
                            <label>To Date </label>
                            <input
                                type="date"
                                name="to_date"
                                id="to_date"
                                class="form-input"
                                value="<?php echo $to_date; ?>"
                                max="<?php echo date('Y-m-d'); ?>" onchange="getReport()"
                            >
                        </div>
                    </div>
                </form>
                <div class="report-actions">
    
                    <button type="button" class="btn-report btn-print"
                        onclick="window.open('reports/rpt_enrollment_report.php?course_type=<?php echo $course_type; ?>&course_id=<?php echo $course_id; ?>&from_date=<?php echo $from_date; ?>&to_date=<?php echo $to_date; ?>', '_blank')">
                        
                        <i class="fas fa-print"></i> Print Report
                    </button>

                    <button type="button" class="btn-report btn-excel"
                        onclick="getExcelReport();">
                        
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>

                </div>

                <table class="responsive table table-bordered" id="enrollment_report_table">
                    <thead>
                        <tr>
                            <th>Sno</th>                            
                            <th>Student ID</th>
                            <th>Enrollment ID</th>
                            <th>Student Details</th>
                            <th>Course Joined</th>
                            <th>Fees Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $sno = 1;
                            $grand_total_fees = 0;
                            $grand_total_paid = 0;
                            $grand_total_due = 0;
                            if(!empty($enrollments)) {
                                foreach ($enrollments as $u) { 
                                    $course_name = '';
                                    if(!empty($u['course_id'])) {
                                        $course_data = $bf->getTableRecords($GLOBALS['course_table'], 'course_id', $u['course_id']);
                                        if(!empty($course_data)) {
                                            $course_name = $course_data[0]['course_name'];
                                        }
                                    }
                                    $student_dec_id = $bf->encode_decode('decrypt', $u['student_id']);
                                    $enroll_dec_id = $bf->encode_decode('decrypt', $u['enroll_id']);
                                    $fees_amount = floatval($u['fees_amount'] ?? 0);
                                    $paid_amount = floatval($u['paid_amount'] ?? 0);
                                    $balance_amount = floatval($u['balance_amount'] ?? 0);
                                    $grand_total_fees += $fees_amount;
                                    $grand_total_paid += $paid_amount;
                                    $grand_total_due += $balance_amount;
                                ?>
                                    <tr>
                                        <td><?php echo $sno++; ?></td>
                                        <td>
                                            <span style="color: var(--primary); font-weight: 600;">
                                                <?php echo $student_dec_id; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span style="color: var(--text-muted); font-size: 0.85rem; font-family: monospace;">
                                                <?php echo $enroll_dec_id; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($u['student_name']); ?></strong>
                                            <br><span style="font-size: 0.8rem; color: var(--text-muted);"><?php echo $u['mobile_number']; ?></span>
                                        </td>
                                        <td>
                                            <strong><?php echo $course_name; ?></strong>
                                            <br><span style="font-size: 0.8rem; color: var(--text-muted);"><?php echo ucfirst($u['type']); ?> | DOJ: <?php echo date('d-m-Y', strtotime($u['enrollment_date'])); ?></span>
                                        </td>
                                        <td>
                                            <span style="font-size: 0.85rem;">
                                                Fee: <strong>₹<?php echo number_format($fees_amount, 2); ?></strong> | 
                                                Paid: <strong style="color: #10b981;">₹<?php echo number_format($paid_amount, 2); ?></strong> | 
                                                Due: <strong style="color: <?php echo $balance_amount > 0 ? '#ef4444' : '#10b981'; ?>;">₹<?php echo number_format($balance_amount, 2); ?></strong>
                                            </span>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <tr style="background: #f8fafc;">
                                    <td colspan="5" style="text-align: right; font-weight: bold; font-size: 1.05rem; color: var(--primary);">Grand Total</td>
                                    <td>
                                        <span style="font-size: 0.95rem; font-weight: 700;">
                                            Fee: ₹<?php echo number_format($grand_total_fees, 2); ?> | 
                                            Paid: <span style="color: #10b981;">₹<?php echo number_format($grand_total_paid, 2); ?></span> | 
                                            Due: <span style="color: <?php echo $grand_total_due > 0 ? '#ef4444' : '#10b981'; ?>;">₹<?php echo number_format($grand_total_due, 2); ?></span>
                                        </span>
                                    </td>
                                </tr>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="5" style="text-align:center; padding: 2rem; color: var(--text-muted);">No enrollments found.</td>
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
        function getReport() {
            if($('#enrollment_report_form').length > 0) {
                $('#enrollment_report_form').submit();
            }
        }

        function getExcelReport() {

            let table = document.getElementById("enrollment_report_table");

            let workbook = XLSX.utils.table_to_book(table, {
                sheet: "Enrollments"
            });

            XLSX.writeFile(workbook, "Enrollment_Report.xlsx");
            
        }
        
    </script>
</body>
</html>
