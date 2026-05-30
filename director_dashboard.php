<?php
require_once 'common_file.php';

if ($user_role !== 'director') {
    header("Location: index.php");
    exit();
}

// Fetch all branches/companies
$companies = $bf->getQueryRecords("SELECT * FROM " . $GLOBALS['company_table'] . " WHERE deleted = 0");

$today = date('Y-m-d');
$week_start = date('Y-m-d', strtotime('monday this week'));
$week_end = date('Y-m-d', strtotime('sunday this week'));
$month_start = date('Y-m-01');
$month_end = date('Y-m-t');

// ----------------------------------------------------
// 1. Fetch Aggregated Metrics for Each Branch
// ----------------------------------------------------
$branch_metrics = [];
foreach ($companies as $comp) {
    $cid = $comp['company_id'];
    $branch_label = $comp['branch'] . " (" . $comp['company_name'] . ")";

    // Tasks Metrics
    $pending_tasks = $bf->getQueryRecords("SELECT COUNT(*) as total FROM " . $GLOBALS['task_table'] . " WHERE status != 'completed' AND deleted = 0 AND company_id = :cid", [':cid' => $cid])[0]['total'];
    $completed_tasks = $bf->getQueryRecords("SELECT COUNT(*) as total FROM " . $GLOBALS['task_table'] . " WHERE status = 'completed' AND deleted = 0 AND company_id = :cid", [':cid' => $cid])[0]['total'];
    $reports_submitted = $bf->getQueryRecords("SELECT COUNT(*) as total FROM " . $GLOBALS['report_table'] . " WHERE deleted = 0 AND company_id = :cid", [':cid' => $cid])[0]['total'];

    // Enquiries Metrics
    $enq_today = $bf->getQueryRecords("SELECT COUNT(*) as c FROM {$GLOBALS['course_enquiry_table']} WHERE DATE(created_date_time) = :today AND deleted = 0 AND company_id = :cid", [':today' => $today, ':cid' => $cid])[0]['c'];
    $enq_week  = $bf->getQueryRecords("SELECT COUNT(*) as c FROM {$GLOBALS['course_enquiry_table']} WHERE DATE(created_date_time) BETWEEN :ws AND :we AND deleted = 0 AND company_id = :cid", [':ws' => $week_start, ':we' => $week_end, ':cid' => $cid])[0]['c'];
    $enq_month = $bf->getQueryRecords("SELECT COUNT(*) as c FROM {$GLOBALS['course_enquiry_table']} WHERE DATE(created_date_time) BETWEEN :ms AND :me AND deleted = 0 AND company_id = :cid", [':ms' => $month_start, ':me' => $month_end, ':cid' => $cid])[0]['c'];

    // Enrollment Metrics
    $enroll_today = $bf->getQueryRecords("SELECT COUNT(*) as c FROM {$GLOBALS['enrollment_table']} WHERE DATE(created_date_time) = :today AND deleted = 0 AND company_id = :cid", [':today' => $today, ':cid' => $cid])[0]['c']
                  + $bf->getQueryRecords("SELECT COUNT(*) as c FROM {$GLOBALS['enrollment_internship_table']} WHERE DATE(created_date_time) = :today AND deleted = 0 AND company_id = :cid", [':today' => $today, ':cid' => $cid])[0]['c'];
    $enroll_week  = $bf->getQueryRecords("SELECT COUNT(*) as c FROM {$GLOBALS['enrollment_table']} WHERE DATE(created_date_time) BETWEEN :ws AND :we AND deleted = 0 AND company_id = :cid", [':ws' => $week_start, ':we' => $week_end, ':cid' => $cid])[0]['c']
                  + $bf->getQueryRecords("SELECT COUNT(*) as c FROM {$GLOBALS['enrollment_internship_table']} WHERE DATE(created_date_time) BETWEEN :ws AND :we AND deleted = 0 AND company_id = :cid", [':ws' => $week_start, ':we' => $week_end, ':cid' => $cid])[0]['c'];

    // Payments Metrics
    $pay_today = $bf->getQueryRecords("SELECT COALESCE(SUM(total_amount),0) as t FROM {$GLOBALS['payment_table']} WHERE DATE(payment_date) = :today AND deleted = 0 AND company_id = :cid", [':today' => $today, ':cid' => $cid])[0]['t'];
    $pay_week  = $bf->getQueryRecords("SELECT COALESCE(SUM(total_amount),0) as t FROM {$GLOBALS['payment_table']} WHERE DATE(payment_date) BETWEEN :ws AND :we AND deleted = 0 AND company_id = :cid", [':ws' => $week_start, ':we' => $week_end, ':cid' => $cid])[0]['t'];
    $pay_month = $bf->getQueryRecords("SELECT COALESCE(SUM(total_amount),0) as t FROM {$GLOBALS['payment_table']} WHERE DATE(payment_date) BETWEEN :ms AND :me AND deleted = 0 AND company_id = :cid", [':ms' => $month_start, ':me' => $month_end, ':cid' => $cid])[0]['t'];

    // Expenses Metrics
    $exp_today = $bf->getQueryRecords("SELECT COALESCE(SUM(total_amount),0) as t FROM {$GLOBALS['expense_entry_table']} WHERE DATE(expense_entry_date) = :today AND deleted = 0 AND company_id = :cid", [':today' => $today, ':cid' => $cid])[0]['t'];
    $exp_week  = $bf->getQueryRecords("SELECT COALESCE(SUM(total_amount),0) as t FROM {$GLOBALS['expense_entry_table']} WHERE DATE(expense_entry_date) BETWEEN :ws AND :we AND deleted = 0 AND company_id = :cid", [':ws' => $week_start, ':we' => $week_end, ':cid' => $cid])[0]['t'];
    $exp_month = $bf->getQueryRecords("SELECT COALESCE(SUM(total_amount),0) as t FROM {$GLOBALS['expense_entry_table']} WHERE DATE(expense_entry_date) BETWEEN :ms AND :me AND deleted = 0 AND company_id = :cid", [':ms' => $month_start, ':me' => $month_end, ':cid' => $cid])[0]['t'];

    $branch_metrics[] = [
        'company_id' => $cid,
        'label' => $branch_label,
        'logo' => $comp['logo_image'],
        'tasks' => ['pending' => $pending_tasks, 'completed' => $completed_tasks, 'reports' => $reports_submitted],
        'enquiries' => ['today' => $enq_today, 'week' => $enq_week, 'month' => $enq_month],
        'enrollments' => ['today' => $enroll_today, 'week' => $enroll_week],
        'payments' => ['today' => $pay_today, 'week' => $pay_week, 'month' => $pay_month],
        'expenses' => ['today' => $exp_today, 'week' => $exp_week, 'month' => $exp_month],
    ];
}

// ----------------------------------------------------
// 2. Fetch Employee Attendance Records
// ----------------------------------------------------
$att_branch = $_POST['att_branch'] ?? '';
$att_search = $_POST['att_search'] ?? '';
$att_date   = $_POST['att_date'] ?? date('Y-m-d');

$att_where = "a.deleted = 0";
$att_params = [];

if (!empty($att_branch)) {
    $att_where .= " AND a.company_id = :att_branch";
    $att_params[':att_branch'] = $att_branch;
}
if (!empty($att_search)) {
    $att_where .= " AND (a.staff_name LIKE :att_search OR a.staff_number LIKE :att_search OR a.staff_role LIKE :att_search)";
    $att_params[':att_search'] = '%' . $att_search . '%';
}
if (!empty($att_date)) {
    $att_where .= " AND a.attendance_date = :att_date";
    $att_params[':att_date'] = $att_date;
}

$attendance_query = "SELECT a.*, c.branch as branch_name 
                     FROM {$GLOBALS['attendance_table']} a
                     LEFT JOIN {$GLOBALS['company_table']} c ON a.company_id = c.company_id
                     WHERE $att_where 
                     ORDER BY a.attendance_date DESC, a.id DESC LIMIT 100";
$attendance_records = $bf->getQueryRecords($attendance_query, $att_params);

$attendance_by_date = [];
foreach ($attendance_records as $rec) {
    $date = $rec['attendance_date'];
    $branch_id = $rec['company_id'];
    $branch_name = $rec['branch_name'] ?? 'Main';
    $key = $date . '_' . $branch_id;
    if (!isset($attendance_by_date[$key])) {
        $attendance_by_date[$key] = [
            'date' => $date,
            'branch_id' => $branch_id,
            'branch_name' => $branch_name,
            'total_staff' => 0,
            'present_staff' => 0,
            'records' => []
        ];
    }
    $attendance_by_date[$key]['total_staff']++;
    
    // Calculate precise presence weight (0.5 for Forenoon, 0.5 for Afternoon)
    $presence_weight = 0.0;
    if (strtoupper($rec['fn_present']) === 'P') {
        $presence_weight += 0.5;
    }
    if (strtoupper($rec['an_present']) === 'P') {
        $presence_weight += 0.5;
    }
    $attendance_by_date[$key]['present_staff'] += $presence_weight;
    
    $attendance_by_date[$key]['records'][] = $rec;
}

// ----------------------------------------------------
// 3. Fetch Employee Payroll Records
// ----------------------------------------------------
$pay_branch = $_POST['pay_branch'] ?? '';
$pay_search = $_POST['pay_search'] ?? '';
$pay_month  = $_POST['pay_month'] ?? date('m');
$pay_year   = $_POST['pay_year'] ?? date('Y');

$pay_where = "p.deleted = 0";
$pay_params = [];

if (!empty($pay_branch)) {
    $pay_where .= " AND p.company_id = :pay_branch";
    $pay_params[':pay_branch'] = $pay_branch;
}
if (!empty($pay_search)) {
    $pay_where .= " AND (s.staff_name LIKE :pay_search OR s.staff_number LIKE :pay_search OR r.role_name LIKE :pay_search)";
    $pay_params[':pay_search'] = '%' . $pay_search . '%';
}
if (!empty($pay_month)) {
    $pay_where .= " AND p.month = :pay_month";
    $pay_params[':pay_month'] = $pay_month;
}
if (!empty($pay_year)) {
    $pay_where .= " AND p.year = :pay_year";
    $pay_params[':pay_year'] = $pay_year;
}

$payroll_query = "SELECT p.*, s.staff_name, s.staff_number, r.role_name, c.branch as branch_name 
                  FROM {$GLOBALS['payroll_table']} p 
                  JOIN {$GLOBALS['staff_table']} s ON p.staff_id = s.staff_id 
                  LEFT JOIN {$GLOBALS['role_table']} r ON s.role_id = r.id 
                  LEFT JOIN {$GLOBALS['company_table']} c ON p.company_id = c.company_id
                  WHERE $pay_where 
                  ORDER BY p.year DESC, p.month DESC, p.id DESC LIMIT 100";
$payroll_records = $bf->getQueryRecords($payroll_query, $pay_params);

$payroll_by_month = [];
foreach ($payroll_records as $rec) {
    $month_name = date("F", mktime(0, 0, 0, $rec['month'], 10));
    $period = $month_name . ' ' . $rec['year'];
    $branch_id = $rec['company_id'];
    $branch_name = $rec['branch_name'] ?? 'Main';
    $key = $rec['month'] . '_' . $rec['year'] . '_' . $branch_id;
    if (!isset($payroll_by_month[$key])) {
        $payroll_by_month[$key] = [
            'period' => $period,
            'branch_id' => $branch_id,
            'branch_name' => $branch_name,
            'total_salary' => 0.0,
            'total_incentive' => 0.0,
            'total_net' => 0.0,
            'staff_count' => 0,
            'records' => []
        ];
    }
    $payroll_by_month[$key]['total_salary'] += floatval($rec['monthly_salary']);
    $payroll_by_month[$key]['total_incentive'] += floatval($rec['incentive_amount']);
    $payroll_by_month[$key]['total_net'] += floatval($rec['net_salary']);
    $payroll_by_month[$key]['staff_count']++;
    $payroll_by_month[$key]['records'][] = $rec;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Director Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        /* Custom Premium Aesthetics */
        :root {
            --primary-gradient: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
            --secondary-gradient: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
            --accent-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --danger-gradient: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
            
            --glass-bg: rgba(255, 255, 255, 0.75);
            --glass-border: rgba(255, 255, 255, 0.5);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f1f5f9;
        }

        .director-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
            margin-top: 1rem;
        }

        .branch-card {
            background: #ffffff;
            border-radius: 1.25rem;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05), 0 4px 6px -4px rgba(0,0,0,0.03);
            border: 1px solid rgba(0,0,0,0.06);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 2rem;
        }

        .branch-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.08), 0 10px 10px -5px rgba(0,0,0,0.04);
        }

        .branch-header {
            background: linear-gradient(to right, #f8fafc, #f1f5f9);
            padding: 1.5rem 2rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .branch-header h3 {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .branch-header .branch-badge {
            background: var(--primary-gradient);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .metrics-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.25rem;
            padding: 2rem;
        }

        .metric-item {
            background: #ffffff;
            border: 1px solid #f1f5f9;
            border-radius: 1rem;
            padding: 1.25rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .metric-item::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 3px;
        }

        .metric-item.blue::before { background: var(--primary-gradient); }
        .metric-item.purple::before { background: var(--secondary-gradient); }
        .metric-item.green::before { background: var(--accent-gradient); }
        .metric-item.red::before { background: var(--danger-gradient); }

        .metric-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .metric-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .metric-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: baseline;
            gap: 0.5rem;
        }

        .metric-subtext {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 0.25rem;
        }

        .section-title-premium {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin: 2rem 0 1rem 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 0.75rem;
        }

        .premium-table-container {
            background: #ffffff;
            border-radius: 1.25rem;
            border: 1px solid rgba(0,0,0,0.06);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .table-responsive-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
        }

        .filter-panel {
            background: #f8fafc;
            padding: 1.25rem 2rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.375rem;
        }

        .filter-group label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
        }

        .filter-input {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            border: 1.5px solid #e2e8f0;
            font-family: inherit;
            font-size: 0.875rem;
            background: white;
            outline: none;
            min-width: 150px;
            transition: border-color 0.2s;
        }

        .filter-input:focus {
            border-color: #3b82f6;
        }

        .btn-filter {
            padding: 0.5rem 1.25rem;
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            height: 38px;
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.15);
            transition: opacity 0.2s;
        }

        .btn-filter:hover {
            opacity: 0.9;
        }

        .btn-clear {
            padding: 0.5rem 1.25rem;
            background: #e2e8f0;
            color: #475569;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            height: 38px;
            transition: background 0.2s;
        }

        .btn-clear:hover {
            background: #cbd5e1;
        }

        .premium-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .premium-table th {
            background: #f8fafc;
            padding: 1rem 1.5rem;
            font-size: 0.8rem;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            border-bottom: 1.5px solid #e2e8f0;
        }

        .premium-table td {
            padding: 1rem 1.5rem;
            font-size: 0.9rem;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }

        .premium-table tr:hover td {
            background: #f8fafc;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-pill.present {
            background-color: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .status-pill.absent {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .status-pill.half {
            background-color: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .badge-branch {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 600;
            border: 1px solid #e2e8f0;
            padding: 0.15rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
        }

        .toggle-icon {
            transition: transform 0.3s ease;
        }
        .accordion-toggle {
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .accordion-toggle:hover {
            background-color: #f8fafc !important;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header" style="margin-bottom: 2rem;">
            <div>
                <h2 style="margin: 0; font-weight: 700; background: linear-gradient(to right, #0f172a, #334155); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Director Overview Dashboard</h2>
                <p style="color: var(--text-muted); margin: 0.5rem 0 0 0;">Aggregated live branch insights, attendance profiles, and payroll histories.</p>
            </div>
            <div class="user-profile">
                <span>Welcome, <?php echo htmlspecialchars($full_name); ?></span>
                <div class="avatar" style="background: var(--primary-gradient); color: white;"><?php echo substr($username, 0, 1); ?></div>
            </div>
        </div>

        <!-- Section: Branch-Wise Metrics -->
        <div class="section-title-premium">
            <i class="fas fa-fw fa-city" style="color: #3b82f6;"></i> Branch-wise Insights
        </div>

        <?php foreach ($branch_metrics as $bm): ?>
            <div class="branch-card">
                <div class="branch-header">
                    <h3>
                        <?php if (!empty($bm['logo'])): ?>
                            <img src="<?php echo $bm['logo']; ?>" alt="Logo" style="height: 35px; border-radius: 4px;">
                        <?php else: ?>
                            🏢
                        <?php endif; ?>
                        <?php echo htmlspecialchars($bm['label']); ?>
                    </h3>
                    <span class="branch-badge">Active Branch</span>
                </div>
                
                <div class="metrics-container">
                    <!-- Task Metrics -->
                    <div class="metric-item blue">
                        <div class="metric-label">Tasks & logs</div>
                        <div class="metric-value">
                            <?php echo $bm['tasks']['pending']; ?> <span style="font-size: 0.8rem; font-weight: 400; color: #64748b; margin-left: 0.5rem;">Pending</span>
                        </div>
                        <div class="metric-subtext">Completed: <?php echo $bm['tasks']['completed']; ?> | Logs: <?php echo $bm['tasks']['reports']; ?></div>
                    </div>

                    <!-- Enquiry Metrics -->
                    <div class="metric-item purple">
                        <div class="metric-label">Enquiries</div>
                        <div class="metric-value"><?php echo $bm['enquiries']['month']; ?></div>
                        <div class="metric-subtext">Today: <?php echo $bm['enquiries']['today']; ?> | This Week: <?php echo $bm['enquiries']['week']; ?></div>
                    </div>

                    <!-- Enrollment Metrics -->
                    <div class="metric-item green">
                        <div class="metric-label">Enrollments</div>
                        <div class="metric-value"><?php echo $bm['enrollments']['today']; ?></div>
                        <div class="metric-subtext">This Week: <?php echo $bm['enrollments']['week']; ?> Total</div>
                    </div>

                    <!-- Payments Metrics -->
                    <div class="metric-item green">
                        <div class="metric-label">Payments Collection</div>
                        <div class="metric-value">₹<?php echo number_format($bm['payments']['month'], 2); ?></div>
                        <div class="metric-subtext">Today: ₹<?php echo number_format($bm['payments']['today'], 2); ?> | Week: ₹<?php echo number_format($bm['payments']['week'], 2); ?></div>
                    </div>

                    <!-- Expenses Metrics -->
                    <div class="metric-item red">
                        <div class="metric-label">Expenses Outflow</div>
                        <div class="metric-value">₹<?php echo number_format($bm['expenses']['month'], 2); ?></div>
                        <div class="metric-subtext">Today: ₹<?php echo number_format($bm['expenses']['today'], 2); ?> | Week: ₹<?php echo number_format($bm['expenses']['week'], 2); ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Section: Employee Attendance -->
        <div class="section-title-premium" id="attendance-section">
            <i class="fas fa-fw fa-user-check" style="color: #8b5cf6;"></i> Employee Attendance History
        </div>

        <div class="premium-table-container">
            <form method="POST" action="#attendance-section" class="filter-panel">
                <!-- Keep payroll parameters so filters don't conflict -->
                <input type="hidden" name="pay_branch" value="<?php echo htmlspecialchars($pay_branch); ?>">
                <input type="hidden" name="pay_search" value="<?php echo htmlspecialchars($pay_search); ?>">
                <input type="hidden" name="pay_month" value="<?php echo htmlspecialchars($pay_month); ?>">
                <input type="hidden" name="pay_year" value="<?php echo htmlspecialchars($pay_year); ?>">

                <div class="filter-group">
                    <label>Branch</label>
                    <select name="att_branch" class="filter-input">
                        <option value="">All Branches</option>
                        <?php foreach ($companies as $comp): ?>
                            <option value="<?php echo htmlspecialchars($comp['company_id']); ?>" <?php echo $att_branch === $comp['company_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($comp['branch']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Search Staff</label>
                    <input type="text" name="att_search" class="filter-input" placeholder="Name, Number, Role..." value="<?php echo htmlspecialchars($att_search); ?>">
                </div>

                <div class="filter-group">
                    <label>Date</label>
                    <input type="date" name="att_date" class="filter-input" value="<?php echo htmlspecialchars($att_date); ?>">
                </div>

                <button type="submit" class="btn-filter">Apply Filters</button>
                <a href="director_dashboard.php#attendance-section" class="btn-clear" style="line-height: 24px; text-decoration: none; text-align: center;">Reset</a>
            </form>

            <div class="table-responsive-wrapper">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Date</th>
                            <th>Branch</th>
                            <th>Present Ratio</th>
                            <th>Attendance Percentage</th>
                            <th style="text-align: right; padding-right: 2rem;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($attendance_by_date)): ?>
                            <tr><td colspan="6" style="text-align: center; padding: 2rem;">No attendance records found matching the filters.</td></tr>
                        <?php else: ?>
                            <?php $sno = 1; foreach ($attendance_by_date as $key => $day_data): 
                                $date_key = md5($key);
                            ?>
                                <tr class="accordion-toggle" onclick="toggleAccordion('att-day-<?php echo $date_key; ?>')">
                                    <td><?php echo $sno++; ?></td>
                                    <td><strong><?php echo date('d-m-Y', strtotime($day_data['date'])); ?></strong></td>
                                    <td><span class="badge-branch"><?php echo htmlspecialchars($day_data['branch_name']); ?></span></td>
                                    <td style="font-weight: 600; color: #2563eb;"><?php echo $day_data['present_staff'] . '/' . $day_data['total_staff']; ?> Present</td>
                                    <td>
                                        <?php 
                                        $pct = ($day_data['total_staff'] > 0) ? round(($day_data['present_staff'] / $day_data['total_staff']) * 100) : 0;
                                        if ($pct >= 90) {
                                            echo '<span class="status-pill present">' . $pct . '% Attendance</span>';
                                        } elseif ($pct >= 75) {
                                            echo '<span class="status-pill half">' . $pct . '% Attendance</span>';
                                        } else {
                                            echo '<span class="status-pill absent">' . $pct . '% Attendance</span>';
                                        }
                                        ?>
                                    </td>
                                    <td style="text-align: right; padding-right: 2rem; color: #64748b;">
                                        <span style="font-size: 0.85rem; font-weight: 600; margin-right: 0.5rem; color: #3b82f6;">View Details</span>
                                        <i class="fas fa-chevron-down toggle-icon" id="icon-att-day-<?php echo $date_key; ?>"></i>
                                    </td>
                                </tr>
                                <tr class="accordion-content" id="att-day-<?php echo $date_key; ?>" style="display: none; background: #f8fafc;">
                                    <td colspan="6" style="padding: 1.5rem;">
                                        <div style="background: white; border-radius: 0.75rem; border: 1px solid #e2e8f0; padding: 1.25rem; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);">
                                            <h4 style="margin: 0 0 1rem 0; color: #1e293b; font-size: 1rem;"><i class="fas fa-users" style="color: #6366f1; margin-right: 0.5rem;"></i> Staff Attendance Details for <?php echo date('d-m-Y', strtotime($day_data['date'])); ?> (<?php echo htmlspecialchars($day_data['branch_name']); ?> Branch)</h4>
                                            <table class="premium-table" style="width: 100%; margin: 0; box-shadow: none; border: 1px solid #f1f5f9;">
                                                <thead>
                                                    <tr style="background: #f8fafc;">
                                                        <th>Staff Number</th>
                                                        <th>Staff Name</th>
                                                        <th>Branch</th>
                                                        <th>Role</th>
                                                        <th>FN</th>
                                                        <th>AN</th>
                                                        <th>Overall Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($day_data['records'] as $rec): ?>
                                                        <tr>
                                                            <td><code style="color: #0284c7;"><?php echo htmlspecialchars($rec['staff_number']); ?></code></td>
                                                            <td><strong><?php echo htmlspecialchars($rec['staff_name']); ?></strong></td>
                                                            <td><span class="badge-branch"><?php echo htmlspecialchars($rec['branch_name'] ?? 'Main'); ?></span></td>
                                                            <td><span style="font-size: 0.85rem; color: #64748b;"><?php echo htmlspecialchars($rec['staff_role']); ?></span></td>
                                                            <td><?php echo $rec['fn_present'] === 'P' ? '<span style="color:#10b981; font-weight:700;">🟢 Present</span>' : '<span style="color:#ef4444; font-weight:700;">🔴 Absent</span>'; ?></td>
                                                            <td><?php echo $rec['an_present'] === 'P' ? '<span style="color:#10b981; font-weight:700;">🟢 Present</span>' : '<span style="color:#ef4444; font-weight:700;">🔴 Absent</span>'; ?></td>
                                                            <td>
                                                                <?php
                                                                switch ($rec['present_code']) {
                                                                    case 'PP':
                                                                        echo '<span class="status-pill present">Full day present</span>';
                                                                        break;
                                                                    case 'AA':
                                                                        echo '<span class="status-pill absent">Full day absent</span>';
                                                                        break;
                                                                    case 'PA':
                                                                        echo '<span class="status-pill half">FN - P | AN - A</span>';
                                                                        break;
                                                                    case 'AP':
                                                                        echo '<span class="status-pill half">FN - A | AN - P</span>';
                                                                        break;
                                                                    default:
                                                                        echo '<span class="status-pill">' . htmlspecialchars($rec['present_code']) . '</span>';
                                                                }
                                                                ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section: Employee Payroll -->
        <div class="section-title-premium" id="payroll-section">
            <i class="fas fa-fw fa-file-invoice-dollar" style="color: #10b981;"></i> Employee Payroll History
        </div>

        <div class="premium-table-container">
            <form method="POST" action="#payroll-section" class="filter-panel">
                <!-- Keep attendance parameters so filters don't conflict -->
                <input type="hidden" name="att_branch" value="<?php echo htmlspecialchars($att_branch); ?>">
                <input type="hidden" name="att_search" value="<?php echo htmlspecialchars($att_search); ?>">
                <input type="hidden" name="att_date" value="<?php echo htmlspecialchars($att_date); ?>">

                <div class="filter-group">
                    <label>Branch</label>
                    <select name="pay_branch" class="filter-input">
                        <option value="">All Branches</option>
                        <?php foreach ($companies as $comp): ?>
                            <option value="<?php echo htmlspecialchars($comp['company_id']); ?>" <?php echo $pay_branch === $comp['company_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($comp['branch']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Search Staff</label>
                    <input type="text" name="pay_search" class="filter-input" placeholder="Name, Number, Role..." value="<?php echo htmlspecialchars($pay_search); ?>">
                </div>

                <div class="filter-group">
                    <label>Month</label>
                    <select name="pay_month" class="filter-input">
                        <option value="">All Months</option>
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?php echo str_pad($m, 2, '0', STR_PAD_LEFT); ?>" <?php echo $pay_month === str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : ''; ?>>
                                <?php echo date("F", mktime(0, 0, 0, $m, 10)); ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Year</label>
                    <select name="pay_year" class="filter-input">
                        <option value="">All Years</option>
                        <?php for ($y = date('Y') - 2; $y <= date('Y'); $y++): ?>
                            <option value="<?php echo $y; ?>" <?php echo $pay_year == $y ? 'selected' : ''; ?>>
                                <?php echo $y; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <button type="submit" class="btn-filter">Apply Filters</button>
                <a href="director_dashboard.php#payroll-section" class="btn-clear" style="line-height: 24px; text-decoration: none; text-align: center;">Reset</a>
            </form>

            <div class="table-responsive-wrapper">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Month/Year</th>
                            <th>Branch</th>
                            <th>Staff Paid Count</th>
                            <th>Total Gross Salary</th>
                            <th>Total Incentives</th>
                            <th style="color: #2563eb; font-weight: 700;">Total Net Paid</th>
                            <th style="text-align: right; padding-right: 2rem;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($payroll_by_month)): ?>
                            <tr><td colspan="8" style="text-align: center; padding: 2rem;">No payroll records found matching the filters.</td></tr>
                        <?php else: ?>
                            <?php $sno = 1; foreach ($payroll_by_month as $key => $p_data): 
                                $period_key = md5($key);
                            ?>
                                <tr class="accordion-toggle" onclick="toggleAccordion('pay-month-<?php echo $period_key; ?>')">
                                    <td><?php echo $sno++; ?></td>
                                    <td><strong><?php echo htmlspecialchars($p_data['period']); ?></strong></td>
                                    <td><span class="badge-branch"><?php echo htmlspecialchars($p_data['branch_name']); ?></span></td>
                                    <td style="font-weight: 600; color: #475569;"><i class="fas fa-user-tie" style="margin-right: 0.5rem; color: #8b5cf6;"></i><?php echo $p_data['staff_count']; ?> Employees Paid</td>
                                    <td>₹<?php echo number_format($p_data['total_salary'], 2); ?></td>
                                    <td style="color: #10b981; font-weight: 600;">+ ₹<?php echo number_format($p_data['total_incentive'], 2); ?></td>
                                    <td style="color: #2563eb; font-weight: 700; font-size: 1rem;">₹<?php echo number_format($p_data['total_net'], 2); ?></td>
                                    <td style="text-align: right; padding-right: 2rem; color: #64748b;">
                                        <span style="font-size: 0.85rem; font-weight: 600; margin-right: 0.5rem; color: #3b82f6;">View Details</span>
                                        <i class="fas fa-chevron-down toggle-icon" id="icon-pay-month-<?php echo $period_key; ?>"></i>
                                    </td>
                                </tr>
                                <tr class="accordion-content" id="pay-month-<?php echo $period_key; ?>" style="display: none; background: #f8fafc;">
                                    <td colspan="8" style="padding: 1.5rem;">
                                        <div style="background: white; border-radius: 0.75rem; border: 1px solid #e2e8f0; padding: 1.25rem; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);">
                                            <h4 style="margin: 0 0 1rem 0; color: #1e293b; font-size: 1rem;"><i class="fas fa-file-invoice-dollar" style="color: #10b981; margin-right: 0.5rem;"></i> Detailed Payroll Summary for <?php echo htmlspecialchars($p_data['period']); ?> (<?php echo htmlspecialchars($p_data['branch_name']); ?> Branch)</h4>
                                            <table class="premium-table" style="width: 100%; margin: 0; box-shadow: none; border: 1px solid #f1f5f9;">
                                                <thead>
                                                    <tr style="background: #f8fafc;">
                                                        <th>Payroll ID</th>
                                                        <th>Staff Number</th>
                                                        <th>Staff Name</th>
                                                        <th>Branch</th>
                                                        <th>Role</th>
                                                        <th>Monthly Salary</th>
                                                        <th>CL / LOP</th>
                                                        <th>Incentive</th>
                                                        <th style="color: #2563eb; font-weight: 700;">Net Salary</th>
                                                        <th>Payment Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($p_data['records'] as $rec): 
                                                        $display_payroll_number = $bf->encode_decode('decrypt', $rec['payroll_number']);
                                                    ?>
                                                        <tr>
                                                            <td><code style="color: #6d28d9; font-weight:600;"><?php echo htmlspecialchars($display_payroll_number); ?></code></td>
                                                            <td><code style="color: #0284c7;"><?php echo htmlspecialchars($rec['staff_number']); ?></code></td>
                                                            <td><strong><?php echo htmlspecialchars($rec['staff_name']); ?></strong></td>
                                                            <td><span class="badge-branch"><?php echo htmlspecialchars($rec['branch_name'] ?? 'Main'); ?></span></td>
                                                            <td><span style="font-size: 0.85rem; color: #64748b;"><?php echo htmlspecialchars($rec['role_name'] ?? 'Staff'); ?></span></td>
                                                            <td>₹<?php echo number_format($rec['monthly_salary'], 2); ?></td>
                                                            <td><span style="color: #ef4444; font-weight:600;"><?php echo $rec['lop_days']; ?> LOP</span> / <span style="color: #10b981; font-weight:600;"><?php echo $rec['cl_days']; ?> CL</span></td>
                                                            <td style="color:#10b981; font-weight:600;">+ ₹<?php echo number_format($rec['incentive_amount'], 2); ?></td>
                                                            <td style="color: #2563eb; font-weight: 700;">₹<?php echo number_format($rec['net_salary'], 2); ?></td>
                                                            <td><strong><?php echo date('d-m-Y', strtotime($rec['payment_date'])); ?></strong></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="main/js/script.js"></script>
    <script>
        function toggleAccordion(id) {
            var content = document.getElementById(id);
            var icon = document.getElementById('icon-' + id);
            if (content.style.display === 'none') {
                content.style.display = 'table-row';
                if (icon) {
                    icon.style.transform = 'rotate(180deg)';
                }
            } else {
                content.style.display = 'none';
                if (icon) {
                    icon.style.transform = 'rotate(0deg)';
                }
            }
        }
    </script>
</body>
</html>
