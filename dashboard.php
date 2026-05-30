<?php require_once 'common_file.php'; 

$is_management = false;
if (!empty($_SESSION['role_id'])) {
    $role_row = $bf->getQueryRecords("SELECT id FROM " . $GLOBALS['role_table'] . " WHERE role_id = :role_id LIMIT 1", [':role_id' => $_SESSION['role_id']]);
    if (!empty($role_row) && intval($role_row[0]['id']) === 4) {
        $is_management = true;
    }
}

if (($user_role === 'admin' || $is_management) && isset($_POST['change_company_id'])) {
    $_SESSION['company_id'] = $_POST['change_company_id'];
    header("Location: dashboard.php");
    exit();
}

// Fetch Stats
$today = date('Y-m-d');
$comp_id = $_SESSION['company_id'];
$pending_count = $bf->getQueryRecords("SELECT COUNT(*) as total FROM " . $GLOBALS['task_table'] . " WHERE status != 'completed' AND deleted = 0 AND company_id = :comp_id", [':comp_id' => $comp_id])[0]['total'];
$completed_count = $bf->getQueryRecords("SELECT COUNT(*) as total FROM " . $GLOBALS['task_table'] . " WHERE status = 'completed' AND deleted = 0 AND company_id = :comp_id", [':comp_id' => $comp_id])[0]['total'];
$report_count = $bf->getQueryRecords("SELECT COUNT(*) as total FROM " . $GLOBALS['report_table'] . " WHERE deleted = 0 AND company_id = :comp_id", [':comp_id' => $comp_id])[0]['total'];

// Role-Specific Metric Calculations
$student_attendance_percentage = 0;
$student_pending_tasks = 0;
$student_reports_count = 0;

$staff_pending_reviews = 0;
$staff_student_tasks = 0;
$staff_attendance_alerts = 0;

if ($user_role === 'student') {
    // 1. Calculate Attendance Percentage
    $encrypted_student_id = $bf->encode_decode('encrypt', $username);
    $attendance_records = $bf->getQueryRecords("SELECT present_code FROM " . $GLOBALS['student_attendance_table'] . " WHERE student_id = :student_id AND deleted = 0", [':student_id' => $encrypted_student_id]);
    
    if (!empty($attendance_records)) {
        $total_days = count($attendance_records);
        $present_weight = 0;
        foreach ($attendance_records as $rec) {
            $code = strtoupper($rec['present_code']);
            if ($code === 'PP') {
                $present_weight += 1.0;
            } elseif ($code === 'PA' || $code === 'AP') {
                $present_weight += 0.5;
            }
        }
        $student_attendance_percentage = round(($present_weight / $total_days) * 100, 1);
    }

    // 2. Pending tasks for student
    $student_pending_tasks = $bf->getQueryRecords("SELECT COUNT(*) as total FROM " . $GLOBALS['student_tasks_table'] . " WHERE assigned_to_student = :student_id AND status != 'Completed' AND deleted = 0", [':student_id' => $username])[0]['total'];

    // 3. Reports submitted
    $student_reports_count = $bf->getQueryRecords("SELECT COUNT(*) as total FROM " . $GLOBALS['student_reports_table'] . " WHERE student_id = :student_id AND deleted = 0", [':student_id' => $username])[0]['total'];

} elseif ($user_role === 'staff') {
    // Get staff's assigned student plain IDs
    $query_training = "SELECT student_id FROM " . $GLOBALS['enrollment_table'] . " WHERE staff_id = :staff_id AND deleted = 0";
    $query_intern = "SELECT student_id FROM " . $GLOBALS['enrollment_internship_table'] . " WHERE staff_id = :staff_id AND deleted = 0";
    
    $training_students = $bf->getQueryRecords($query_training, [':staff_id' => $user_id]);
    $intern_students = $bf->getQueryRecords($query_intern, [':staff_id' => $user_id]);
    
    $assigned_student_ids = [];
    $assigned_student_encrypts = [];
    foreach ($training_students as $s) {
        $assigned_student_encrypts[] = $s['student_id'];
        $assigned_student_ids[] = $bf->encode_decode('decrypt', $s['student_id']);
    }
    foreach ($intern_students as $s) {
        $assigned_student_encrypts[] = $s['student_id'];
        $assigned_student_ids[] = $bf->encode_decode('decrypt', $s['student_id']);
    }

    if (!empty($assigned_student_ids)) {
        // Pending reports review count
        $placeholders = [];
        $params_reports = [];
        foreach ($assigned_student_ids as $idx => $sid) {
            $key = ":sid_" . $idx;
            $placeholders[] = $key;
            $params_reports[$key] = $sid;
        }
        $params_reports[':status'] = 'Pending';
        $staff_pending_reviews = $bf->getQueryRecords("SELECT COUNT(*) as total FROM " . $GLOBALS['student_reports_table'] . " WHERE status = :status AND student_id IN (" . implode(',', $placeholders) . ") AND deleted = 0", $params_reports)[0]['total'];

        // Total assigned student tasks count
        $params_tasks = [];
        foreach ($assigned_student_ids as $idx => $sid) {
            $key = ":sid_" . $idx;
            $params_tasks[$key] = $sid;
        }
        $staff_student_tasks = $bf->getQueryRecords("SELECT COUNT(*) as total FROM " . $GLOBALS['student_tasks_table'] . " WHERE assigned_to_student IN (" . implode(',', $placeholders) . ") AND deleted = 0", $params_tasks)[0]['total'];

        // Attendance alerts (< 75%)
        foreach ($assigned_student_encrypts as $enc_sid) {
            $att_records = $bf->getQueryRecords("SELECT present_code FROM " . $GLOBALS['student_attendance_table'] . " WHERE student_id = :student_id AND deleted = 0", [':student_id' => $enc_sid]);
            if (!empty($att_records)) {
                $total_days = count($att_records);
                $present_weight = 0;
                foreach ($att_records as $rec) {
                    $code = strtoupper($rec['present_code']);
                    if ($code === 'PP') $present_weight += 1.0;
                    elseif ($code === 'PA' || $code === 'AP') $present_weight += 0.5;
                }
                $percentage = ($present_weight / $total_days) * 100;
                if ($percentage < 75) {
                    $staff_attendance_alerts++;
                }
            }
        }
    }
}

// Fetch Analytics Data (Admin & Management)
$course_prefs = [];
$course_stats = [];

if ($user_role == 'admin' || $is_management) {
    $q_prefs = "SELECT c.course_name, c.course_id,
        (SELECT COUNT(*) FROM {$GLOBALS['enrollment_table']} e WHERE e.course_id = c.course_id AND e.deleted = 0 AND e.company_id = :comp_id) + 
        (SELECT COUNT(*) FROM {$GLOBALS['enrollment_internship_table']} ei WHERE ei.course_id = c.course_id AND ei.deleted = 0 AND ei.company_id = :comp_id) as student_count
        FROM {$GLOBALS['course_table']} c 
        WHERE c.deleted = 0 ORDER BY student_count DESC";
    $course_prefs_raw = $bf->getQueryRecords($q_prefs, [':comp_id' => $comp_id]);
    
    foreach ($course_prefs_raw as $c) {
        if ($c['student_count'] > 0) {
            $course_prefs[] = $c;
            $course_stats[$c['course_id']] = [
                'course_name' => $c['course_name'],
                'enrolled_count' => $c['student_count'],
                'placed_count' => 0
            ];
        }
    }

    $q_placed = "SELECT student_id, course_type FROM {$GLOBALS['course_closure_table']} WHERE placed = 1 AND deleted = 0 AND company_id = :comp_id";
    $placed_students = $bf->getQueryRecords($q_placed, [':comp_id' => $comp_id]);
    foreach ($placed_students as $ps) {
        $table = $ps['course_type'] == 'internship' ? $GLOBALS['enrollment_internship_table'] : $GLOBALS['enrollment_table'];
        $stu_rec = $bf->getTableRecords($table, 'student_id', $ps['student_id']);
        if (!empty($stu_rec)) {
            $cid = $stu_rec[0]['course_id'];
            if (isset($course_stats[$cid])) {
                $course_stats[$cid]['placed_count']++;
            }
        }
    }
    $course_stats = array_values($course_stats);

    // --- Admin Dashboard Summary Counts ---
    $week_start = date('Y-m-d', strtotime('monday this week'));
    $week_end = date('Y-m-d', strtotime('sunday this week'));
    $month_start = date('Y-m-01');
    $month_end = date('Y-m-t');

    // Enrollment counts (training + internship)
    $enroll_today = $bf->getQueryRecords("SELECT COUNT(*) as c FROM {$GLOBALS['enrollment_table']} WHERE DATE(doj) = :today AND deleted = 0 AND company_id = :cid", [':today' => $today, ':cid' => $comp_id])[0]['c']
                  + $bf->getQueryRecords("SELECT COUNT(*) as c FROM {$GLOBALS['enrollment_internship_table']} WHERE DATE(doj) = :today AND deleted = 0 AND company_id = :cid", [':today' => $today, ':cid' => $comp_id])[0]['c'];
    $enroll_week  = $bf->getQueryRecords("SELECT COUNT(*) as c FROM {$GLOBALS['enrollment_table']} WHERE DATE(doj) BETWEEN :ws AND :we AND deleted = 0 AND company_id = :cid", [':ws' => $week_start, ':we' => $week_end, ':cid' => $comp_id])[0]['c']
                  + $bf->getQueryRecords("SELECT COUNT(*) as c FROM {$GLOBALS['enrollment_internship_table']} WHERE DATE(doj) BETWEEN :ws AND :we AND deleted = 0 AND company_id = :cid", [':ws' => $week_start, ':we' => $week_end, ':cid' => $comp_id])[0]['c'];

    // Enquiry counts
    $enquiry_today = $bf->getQueryRecords("SELECT COUNT(*) as c FROM {$GLOBALS['course_enquiry_table']} WHERE DATE(created_date_time) = :today AND deleted = 0 AND company_id = :cid", [':today' => $today, ':cid' => $comp_id])[0]['c'];
    $enquiry_week  = $bf->getQueryRecords("SELECT COUNT(*) as c FROM {$GLOBALS['course_enquiry_table']} WHERE DATE(created_date_time) BETWEEN :ws AND :we AND deleted = 0 AND company_id = :cid", [':ws' => $week_start, ':we' => $week_end, ':cid' => $comp_id])[0]['c'];
    $enquiry_month = $bf->getQueryRecords("SELECT COUNT(*) as c FROM {$GLOBALS['course_enquiry_table']} WHERE DATE(created_date_time) BETWEEN :ms AND :me AND deleted = 0 AND company_id = :cid", [':ms' => $month_start, ':me' => $month_end, ':cid' => $comp_id])[0]['c'];

    // Expense totals
    $expense_today = $bf->getQueryRecords("SELECT COALESCE(SUM(total_amount),0) as t FROM {$GLOBALS['expense_entry_table']} WHERE DATE(expense_entry_date) = :today AND deleted = 0 AND company_id = :cid", [':today' => $today, ':cid' => $comp_id])[0]['t'];
    $expense_week  = $bf->getQueryRecords("SELECT COALESCE(SUM(total_amount),0) as t FROM {$GLOBALS['expense_entry_table']} WHERE DATE(expense_entry_date) BETWEEN :ws AND :we AND deleted = 0 AND company_id = :cid", [':ws' => $week_start, ':we' => $week_end, ':cid' => $comp_id])[0]['t'];
    $expense_month = $bf->getQueryRecords("SELECT COALESCE(SUM(total_amount),0) as t FROM {$GLOBALS['expense_entry_table']} WHERE DATE(expense_entry_date) BETWEEN :ms AND :me AND deleted = 0 AND company_id = :cid", [':ms' => $month_start, ':me' => $month_end, ':cid' => $comp_id])[0]['t'];

    // Payment totals
    $payment_today = $bf->getQueryRecords("SELECT COALESCE(SUM(total_amount),0) as t FROM {$GLOBALS['payment_table']} WHERE DATE(payment_date) = :today AND deleted = 0 AND company_id = :cid", [':today' => $today, ':cid' => $comp_id])[0]['t'];
    $payment_week  = $bf->getQueryRecords("SELECT COALESCE(SUM(total_amount),0) as t FROM {$GLOBALS['payment_table']} WHERE DATE(payment_date) BETWEEN :ws AND :we AND deleted = 0 AND company_id = :cid", [':ws' => $week_start, ':we' => $week_end, ':cid' => $comp_id])[0]['t'];
    $payment_month = $bf->getQueryRecords("SELECT COALESCE(SUM(total_amount),0) as t FROM {$GLOBALS['payment_table']} WHERE DATE(payment_date) BETWEEN :ms AND :me AND deleted = 0 AND company_id = :cid", [':ms' => $month_start, ':me' => $month_end, ':cid' => $comp_id])[0]['t'];
}

// Fetch Today's Tasks based on role
if ($user_role === 'admin' || $is_management) {
    $recent_tasks_query = "SELECT t.*, u.name as assignee_name FROM " . $GLOBALS['task_table'] . " t 
                           LEFT JOIN " . $GLOBALS['user_table'] . " u ON t.assigned_to = u.id 
                           WHERE t.deleted = 0 AND t.company_id = :comp_id AND t.due_date = :today ORDER BY t.id DESC";
    $params = [':today' => $today, ':comp_id' => $comp_id];
} elseif ($user_role === 'staff' || $user_role == 'trainer') {
    $recent_tasks_query = "SELECT t.*, u.name as assignee_name FROM " . $GLOBALS['task_table'] . " t 
                           LEFT JOIN " . $GLOBALS['user_table'] . " u ON t.assigned_to = u.id 
                           WHERE t.deleted = 0 AND t.company_id = :comp_id AND t.assigned_to = :user_id AND t.due_date = :today ORDER BY t.id DESC";
    $params = [':today' => $today, ':user_id' => $user_id, ':comp_id' => $comp_id];
} else {
    $recent_tasks_query = "SELECT * FROM " . $GLOBALS['student_tasks_table'] . " 
                           WHERE deleted = 0 AND company_id = :comp_id AND assigned_to_student = :student_id AND due_date = :today 
                           ORDER BY id DESC";
    $params = [':today' => $today, ':student_id' => $username, ':comp_id' => $comp_id];
}
$recent_tasks = $bf->getQueryRecords($recent_tasks_query, $params);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo get_company_name(); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .charts-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        .chart-card {
            background: #ffffff;
            border: 1px solid rgba(0,0,0,0.05);
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            position: relative;
            overflow: hidden;
        }
        .chart-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 4px;
            background: linear-gradient(90deg, #1d4ed8, #0ea5e9, #f97316); /* Matches logo colors */
            opacity: 0.9;
        }
        .chart-card h3 {
            margin-top: 0;
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
        }
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        @media (max-width: 1024px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <div>
                <h2 style="margin: 0;">Welcome, <?php echo $full_name; ?></h2>
                <p style="color: var(--text-muted); margin: 0.5rem 0 0 0;">Role: <?php echo ucfirst($user_role); ?></p>
            </div>
            <div class="user-profile">
                <span><?php echo $username; ?></span>
                <div class="avatar"><?php echo substr($username, 0, 1); ?></div>
            </div>
        </div>

        <?php if ($user_role === 'admin' || $is_management): ?>
        <div class="branch-selector-card" style="background: linear-gradient(to right, #ffffff, #f8fafc); border: 1px solid rgba(0,0,0,0.08); border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
            <div style="display: flex; align-items: center; gap: 1.25rem;">
                <div style="width: 50px; height: 50px; border-radius: 12px; background: rgba(37, 99, 235, 0.1); color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    🏢
                </div>
                <div>
                    <h3 style="margin: 0; color: #0f172a; font-size: 1.15rem; font-weight: 600;">Active Branch / Company</h3>
                    <p style="margin: 0; color: #64748b; font-size: 0.9rem; margin-top: 0.25rem;">Switch branches to instantly load specific metrics and data.</p>
                </div>
            </div>
            <div>
                <?php 
                $companies = $bf->getTableRecords($GLOBALS['company_table']); 
                ?>
                <form method="POST" action="" id="companyForm" style="margin: 0;">
                    <div style="position: relative;">
                        <select name="change_company_id" onchange="document.getElementById('companyForm').submit()" style="appearance: none; -webkit-appearance: none; padding: 0.75rem 2.5rem 0.75rem 1.25rem; border-radius: 8px; border: 2px solid #3b82f6; font-family: 'Outfit'; min-width: 300px; cursor: pointer; background: white; font-weight: 600; color: #1e293b; font-size: 1rem; box-shadow: 0 4px 6px rgba(59, 130, 246, 0.1); outline: none; transition: all 0.2s;">
                            <?php foreach($companies as $comp): ?>
                                <option value="<?php echo htmlspecialchars($comp['company_id']); ?>" <?php echo ($_SESSION['company_id'] == $comp['company_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($comp['company_name'] . ' - ' . $comp['branch']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); pointer-events: none; color: #3b82f6;">
                            ▼
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <div class="stats-grid">
            <?php if ($user_role === 'student'): ?>
                <div class="stat-card">
                    <div class="stat-label">Attendance Percentage</div>
                    <div class="stat-value"><?php echo $student_attendance_percentage; ?>%</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Pending Student Tasks</div>
                    <div class="stat-value"><?php echo $student_pending_tasks; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Daily Reports Submitted</div>
                    <div class="stat-value"><?php echo $student_reports_count; ?></div>
                </div>
            <?php elseif ($user_role === 'staff'): ?>
                <div class="stat-card">
                    <div class="stat-label">Review Pending Reports</div>
                    <div class="stat-value"><?php echo $staff_pending_reviews; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Assigned Student Tasks</div>
                    <div class="stat-value"><?php echo $staff_student_tasks; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Attendance Alerts (< 75%)</div>
                    <div class="stat-value" style="color: <?php echo ($staff_attendance_alerts > 0) ? '#ef4444' : 'inherit'; ?>;"><?php echo $staff_attendance_alerts; ?></div>
                </div>
            <?php else: ?>
                <div class="stat-card">
                    <div class="stat-label">Pending Tasks</div>
                    <div class="stat-value"><?php echo $pending_count; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Tasks Completed</div>
                    <div class="stat-value"><?php echo $completed_count; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Reports Submitted</div>
                    <div class="stat-value"><?php echo $report_count; ?></div>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($user_role == 'admin' || $is_management): ?>
        <!-- Admin Summary Cards -->
        <style>
            .summary-section { margin-bottom: 1.5rem; }
            .summary-section-title {
                font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
                color: #64748b; margin-bottom: 0.75rem; padding-left: 0.25rem;
                display: flex; align-items: center; gap: 0.5rem;
            }
            .summary-section-title i { font-size: 1rem; }
            .summary-cards-row {
                display: grid; gap: 1rem;
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            }
            .summary-card {
                background: #fff; border: 1px solid rgba(0,0,0,0.06); border-radius: 0.75rem;
                padding: 1.25rem; position: relative; overflow: hidden; cursor: pointer;
                box-shadow: 0 2px 8px rgba(0,0,0,0.03); transition: all 0.25s ease;
            }
            .summary-card:hover {
                transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.08);
                border-color: var(--primary);
            }
            .summary-card::before {
                content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
            }
            .summary-card.blue::before  { background: linear-gradient(90deg, #3b82f6, #0ea5e9); }
            .summary-card.orange::before { background: linear-gradient(90deg, #f97316, #fb923c); }
            .summary-card.green::before  { background: linear-gradient(90deg, #10b981, #34d399); }
            .summary-card.purple::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }
            .summary-card .sc-label {
                font-size: 0.78rem; font-weight: 600; color: #64748b; text-transform: uppercase;
                letter-spacing: 0.03em; margin-bottom: 0.5rem;
            }
            .summary-card .sc-value {
                font-size: 1.6rem; font-weight: 700; color: #0f172a;
            }
            .summary-card .sc-download {
                position: absolute; top: 1rem; right: 1rem; color: #94a3b8; font-size: 0.9rem;
                transition: color 0.2s;
            }
            .summary-card:hover .sc-download { color: var(--primary); }
        </style>

        <!-- Enquiry & Enrollment Section -->
        <div class="summary-section">
            <div class="summary-section-title"><i class="fas fa-user-graduate"></i> Enquiry & Enrollment</div>
            <div class="summary-cards-row">
                <div class="summary-card blue" onclick="window.open('reports/rpt_dashboard_enquiry.php?period=today&type=enrollment','_blank')">
                    <div class="sc-download"><i class="fas fa-download"></i></div>
                    <div class="sc-label">Today Enrollment</div>
                    <div class="sc-value"><?php echo $enroll_today; ?></div>
                </div>
                <div class="summary-card purple" onclick="window.open('reports/rpt_dashboard_enquiry.php?period=today&type=enquiry','_blank')">
                    <div class="sc-download"><i class="fas fa-download"></i></div>
                    <div class="sc-label">Today Enquiry</div>
                    <div class="sc-value"><?php echo $enquiry_today; ?></div>
                </div>
                <div class="summary-card blue" onclick="window.open('reports/rpt_dashboard_enquiry.php?period=week&type=enrollment','_blank')">
                    <div class="sc-download"><i class="fas fa-download"></i></div>
                    <div class="sc-label">This Week Enrollment</div>
                    <div class="sc-value"><?php echo $enroll_week; ?></div>
                </div>
                <div class="summary-card purple" onclick="window.open('reports/rpt_dashboard_enquiry.php?period=week&type=enquiry','_blank')">
                    <div class="sc-download"><i class="fas fa-download"></i></div>
                    <div class="sc-label">This Week Enquiry</div>
                    <div class="sc-value"><?php echo $enquiry_week; ?></div>
                </div>
                <div class="summary-card purple" onclick="window.open('reports/rpt_dashboard_enquiry.php?period=month&type=enquiry','_blank')">
                    <div class="sc-download"><i class="fas fa-download"></i></div>
                    <div class="sc-label">This Month Enquiry</div>
                    <div class="sc-value"><?php echo $enquiry_month; ?></div>
                </div>
            </div>
        </div>

        <!-- Expense Section -->
        <div class="summary-section">
            <div class="summary-section-title"><i class="fas fa-wallet"></i> Expense</div>
            <div class="summary-cards-row">
                <div class="summary-card orange" onclick="window.open('reports/rpt_dashboard_expense.php?period=today','_blank')">
                    <div class="sc-download"><i class="fas fa-download"></i></div>
                    <div class="sc-label">Today Expense</div>
                    <div class="sc-value">₹<?php echo number_format($expense_today, 2); ?></div>
                </div>
                <div class="summary-card orange" onclick="window.open('reports/rpt_dashboard_expense.php?period=week','_blank')">
                    <div class="sc-download"><i class="fas fa-download"></i></div>
                    <div class="sc-label">This Week Expense</div>
                    <div class="sc-value">₹<?php echo number_format($expense_week, 2); ?></div>
                </div>
                <div class="summary-card orange" onclick="window.open('reports/rpt_dashboard_expense.php?period=month','_blank')">
                    <div class="sc-download"><i class="fas fa-download"></i></div>
                    <div class="sc-label">This Month Expense</div>
                    <div class="sc-value">₹<?php echo number_format($expense_month, 2); ?></div>
                </div>
            </div>
        </div>

        <!-- Payment Section -->
        <div class="summary-section">
            <div class="summary-section-title"><i class="fas fa-file-invoice-dollar"></i> Payment</div>
            <div class="summary-cards-row">
                <div class="summary-card green" onclick="window.open('reports/rpt_dashboard_payment.php?period=today','_blank')">
                    <div class="sc-download"><i class="fas fa-download"></i></div>
                    <div class="sc-label">Today Payment</div>
                    <div class="sc-value">₹<?php echo number_format($payment_today, 2); ?></div>
                </div>
                <div class="summary-card green" onclick="window.open('reports/rpt_dashboard_payment.php?period=week','_blank')">
                    <div class="sc-download"><i class="fas fa-download"></i></div>
                    <div class="sc-label">This Week Payment</div>
                    <div class="sc-value">₹<?php echo number_format($payment_week, 2); ?></div>
                </div>
                <div class="summary-card green" onclick="window.open('reports/rpt_dashboard_payment.php?period=month','_blank')">
                    <div class="sc-download"><i class="fas fa-download"></i></div>
                    <div class="sc-label">This Month Payment</div>
                    <div class="sc-value">₹<?php echo number_format($payment_month, 2); ?></div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($user_role == 'admin' || $is_management): ?>
        <div class="charts-grid">
            <div class="chart-card">
                <h3>Most Preferred Courses</h3>
                <div class="chart-container">
                    <canvas id="preferredCoursesChart"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <h3>Enrolled vs Placed Students</h3>
                <div class="chart-container">
                    <canvas id="enrolledPlacedChart"></canvas>
                </div>
            </div>
        </div>

        <script>
            // Data for Most Preferred Courses
            const prefsData = <?php echo json_encode($course_prefs); ?>;
            const labelsPref = prefsData.map(d => d.course_name);
            const dataPref = prefsData.map(d => parseInt(d.student_count));
            
            // Create nice colors and offset for highest value
            const maxVal = Math.max(...dataPref);
            const offsets = dataPref.map(v => v === maxVal ? 15 : 0);
            
            // Brand-friendly colors
            const prefColors = [
                'rgba(14, 165, 233, 0.85)',  // bright blue
                'rgba(249, 115, 22, 0.85)',  // orange
                'rgba(29, 78, 216, 0.85)',   // dark blue
                'rgba(16, 185, 129, 0.85)',  // emerald
                'rgba(139, 92, 246, 0.85)',  // purple
                'rgba(236, 72, 153, 0.85)'   // pink
            ];

            const ctx1 = document.getElementById('preferredCoursesChart').getContext('2d');
            new Chart(ctx1, {
                type: 'doughnut',
                data: {
                    labels: labelsPref,
                    datasets: [{
                        data: dataPref,
                        backgroundColor: prefColors.slice(0, dataPref.length),
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 10,
                        offset: offsets
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right', labels: { color: '#475569', padding: 20, font: { family: 'Outfit' } } },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) label += ': ';
                                    let val = context.raw;
                                    let sum = context.chart._metasets[context.datasetIndex].total;
                                    let percentage = ((val * 100) / sum).toFixed(1) + "%";
                                    return label + val + ' students (' + percentage + ')';
                                }
                            },
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            titleFont: { family: 'Outfit' },
                            bodyFont: { family: 'Outfit' },
                            padding: 10,
                            cornerRadius: 8
                        }
                    },
                    cutout: '60%',
                    animation: { animateScale: true, animateRotate: true }
                }
            });

            // Data for Enrolled vs Placed Students
            const statsData = <?php echo json_encode($course_stats); ?>;
            const labelsStats = statsData.map(d => d.course_name);
            const dataEnrolled = statsData.map(d => parseInt(d.enrolled_count));
            const dataPlaced = statsData.map(d => parseInt(d.placed_count));

            const ctx2 = document.getElementById('enrolledPlacedChart').getContext('2d');
            new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: labelsStats,
                    datasets: [
                        {
                            label: 'Enrolled Students',
                            data: dataEnrolled,
                            backgroundColor: 'rgba(14, 165, 233, 0.85)', // matching the blue
                            borderColor: 'rgba(14, 165, 233, 1)',
                            borderWidth: 1,
                            borderRadius: 4
                        },
                        {
                            label: 'Placed Students',
                            data: dataPlaced,
                            backgroundColor: 'rgba(249, 115, 22, 0.85)', // matching the orange
                            borderColor: 'rgba(249, 115, 22, 1)',
                            borderWidth: 1,
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { color: '#475569', font: { family: 'Outfit' } } },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            titleFont: { family: 'Outfit' },
                            bodyFont: { family: 'Outfit' },
                            padding: 10,
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: '#f1f5f9', drawBorder: false },
                            ticks: { color: '#64748b', font: { family: 'Outfit' } }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9', drawBorder: false },
                            ticks: { color: '#64748b', stepSize: 1, font: { family: 'Outfit' } }
                        }
                    },
                    animation: {
                        y: { duration: 1000, easing: 'easeOutQuart' }
                    }
                }
            });
        </script>
        <?php endif; ?>

        <div class="module-section" style="margin-top: 1.5rem;">
            <div class="section-title">
                <?php 
                if ($user_role == 'admin' || $is_management) {
                    echo "All Employee Tasks (Today)";
                    $view_all_url = "tasks.php";
                } elseif ($user_role == 'staff' || ($user_role == 'trainer')) {
                    echo "My Tasks (Today)";
                    $view_all_url = "tasks.php";
                } else {
                    echo "My Assigned Student Tasks (Today)";
                    $view_all_url = "student_tasks.php";
                }
                ?>
                <button class="btn-add" onclick="window.location.href='<?php echo $view_all_url; ?>'">View All</button>
            </div>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; color: var(--text-muted); border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <th style="padding: 1rem 0;">Task Title</th>
                        <th>Assigned To</th>
                        <th>Due Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recent_tasks)): ?>
                    <tr><td colspan="4" style="padding: 1rem 0; text-align: center; color: var(--text-muted);">No tasks found today.</td></tr>
                    <?php else: ?>
                    <?php foreach ($recent_tasks as $t): 
                        if ($user_role === 'student') {
                            $title_display = $t['task_title'];
                            $assignee_display = 'Me (' . $t['assigned_to_student'] . ')';
                            $status_color = ($t['status'] == 'Completed') ? '#10b981' : (($t['status'] == 'In Progress') ? '#06b6d4' : '#fbbf24');
                            $status_display = $t['status'];
                        } else {
                            $title_display = $t['title'];
                            $assignee_name = $bf->getTableColumnValue($GLOBALS['staff_table'], 'staff_id', $t['assigned_to'], 'staff_name');
                            $assignee_display = $assignee_name ?: 'Unassigned';
                            $status_color = ($t['status'] == 'completed') ? '#10b981' : (($t['status'] == 'in_progress') ? '#06b6d4' : '#fbbf24');
                            $status_display = ucfirst($t['status']);
                        }
                    ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td style="padding: 1rem 0; font-weight:600;"><?php echo $title_display; ?></td>
                        <td><?php echo $assignee_display; ?></td>
                        <td><?php echo date('d-m-Y', strtotime($t['due_date'])); ?></td>
                        <td><span style="color: <?php echo $status_color; ?>; font-weight:700;"><?php echo $status_display; ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
