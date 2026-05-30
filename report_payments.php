<?php require_once 'common_file.php'; 
$from_page = 'Payments Report';

$from_date = date('Y-m-d', strtotime('-30 days'));
$to_date = date('Y-m-d');

$bill_type = '';
if(isset($_POST['bill_type']) && !empty($_POST['bill_type'])) {
    $bill_type = $bf->sanitize($_POST['bill_type']);
}

$course_type = '';
if(isset($_POST['course_type']) && !empty($_POST['course_type'])) {
    $course_type = $bf->sanitize($_POST['course_type']);
}

$student_id = '';
if(isset($_POST['student_id']) && !empty($_POST['student_id'])) {
    $student_id = $bf->sanitize($_POST['student_id']);
}

$expense_category_id = '';
if(isset($_POST['expense_category_id']) && !empty($_POST['expense_category_id'])) {
    $expense_category_id = $bf->sanitize($_POST['expense_category_id']);
}

if(isset($_POST['from_date']) && !empty($_POST['from_date'])) {
    $from_date = $bf->sanitize($_POST['from_date']);
}

if(isset($_POST['to_date']) && !empty($_POST['to_date'])) {
    $to_date = $bf->sanitize($_POST['to_date']);
}

// Load expense categories for filter dropdown
$expense_category_list = $bf->getTableRecords($GLOBALS['expense_category_table'], 'deleted', 0);

// Load students for receipt filter dropdown
$all_payments = $bf->getQueryRecords("SELECT DISTINCT student_id, course_type FROM {$GLOBALS['payment_table']} WHERE deleted = 0");
$students_options = [];
foreach ($all_payments as $ap) {
    $sid = $ap['student_id'];
    $ctype = $ap['course_type'];
    $table = $ctype === 'internship' ? $GLOBALS['enrollment_internship_table'] : $GLOBALS['enrollment_table'];
    $id_field = $ctype === 'internship' ? 'enrollment_internship_id' : 'enrollment_id';
    $stu_rec = $bf->getTableRecords($table, $id_field, $sid);
    if(!empty($stu_rec)) {
        $dec_id = $bf->encode_decode('decrypt', $stu_rec[0]['student_id']);
        $students_options[$sid] = $dec_id . ' - ' . $stu_rec[0]['student_name'] . ' (' . ucfirst($ctype) . ')';
    }
}
asort($students_options);

// Fetch combined report data
$payments = $bf->getPaymentReportList($course_type, $student_id, $from_date, $to_date, $bill_type, $expense_category_id);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments Report - <?php echo get_company_name(); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .credit-amount { color: #10b981; font-weight: 700; }
        .debit-amount { color: #ef4444; font-weight: 700; }
        .balance-credit { color: #10b981; font-weight: 700; }
        .balance-debit { color: #ef4444; font-weight: 700; }
        .bill-type-receipt { background: rgba(16,185,129,0.12); color: #10b981; padding: 0.2rem 0.6rem; border-radius: 0.3rem; font-weight: 700; font-size: 0.8rem; }
        .bill-type-expense { background: rgba(239,68,68,0.12); color: #ef4444; padding: 0.2rem 0.6rem; border-radius: 0.3rem; font-weight: 700; font-size: 0.8rem; }
        .summary-cards { display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
        .summary-card { flex: 1; min-width: 180px; padding: 1rem 1.25rem; border-radius: 0.75rem; border: 1px solid var(--border); }
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
            <h2><?php if(!empty($from_page)) echo ucfirst($from_page); ?></h2>
        </div>

        <div class="module-section">
            <div id="course_list">
                <form name="payment_report_form" id="payment_report_form" method="post">
                    <div class="filter-row">
                        <div class="form-group">
                            <label>Bill Type</label>
                            <select name="bill_type" id="bill_type" class="form-input" onchange="onBillTypeChange()">
                                <option value="">All</option>
                                <option value="Receipt" <?php echo $bill_type === 'Receipt' ? 'selected' : ''; ?>>Receipt</option>
                                <option value="Expense" <?php echo $bill_type === 'Expense' ? 'selected' : ''; ?>>Expense</option>
                            </select>
                        </div>

                        <!-- Receipt-specific filters -->
                        <div class="form-group" id="filter_course_type" style="<?php echo ($bill_type === 'Receipt' || $bill_type === '') ? '' : 'display:none;'; ?>">
                            <label>Course Type</label>
                            <select name="course_type" id="course_type" class="form-input" onchange="getReport()">
                                <option value="">All</option>
                                <option value="training" <?php echo $course_type === 'training' ? 'selected' : ''; ?>>Training</option>
                                <option value="internship" <?php echo $course_type === 'internship' ? 'selected' : ''; ?>>Internship</option>
                            </select>
                        </div>
                        <div class="form-group" id="filter_student" style="<?php echo ($bill_type === 'Receipt' || $bill_type === '') ? '' : 'display:none;'; ?>">
                            <label>Student</label>
                            <select name="student_id" id="student_id" class="form-input" onchange="getReport()">
                                <option value="">All Students</option>
                                <?php foreach ($students_options as $sid => $sname) { ?>
                                    <option value="<?php echo $sid; ?>" <?php echo $student_id === $sid ? 'selected' : ''; ?>><?php echo $sname; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Expense-specific filters -->
                        <div class="form-group" id="filter_expense_category" style="<?php echo $bill_type === 'Expense' ? '' : 'display:none;'; ?>">
                            <label>Expense Category</label>
                            <select name="expense_category_id" id="expense_category_id" class="form-input" onchange="getReport()">
                                <option value="">All Categories</option>
                                <?php if(!empty($expense_category_list)) {
                                    foreach($expense_category_list as $category) { ?>
                                        <option value="<?php echo $category['expense_category_id']; ?>"
                                            <?php echo ($expense_category_id == $category['expense_category_id']) ? 'selected' : ''; ?>>
                                            <?php echo $category['expense_category_name']; ?>
                                        </option>
                                <?php } } ?>
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

                <?php
                    // Calculate summary totals
                    $total_credit = 0;
                    $total_debit = 0;
                    if (!empty($payments)) {
                        foreach ($payments as $p) {
                            $total_credit += $p['credit'];
                            $total_debit += $p['debit'];
                        }
                    }
                    $net_balance = $total_credit - $total_debit;
                ?>

                <!-- Summary Cards -->
                <div class="summary-cards">
                    <div class="summary-card" style="background: rgba(16,185,129,0.06);">
                        <div class="label">Total Credit (Receipts)</div>
                        <div class="value credit-amount">₹<?php echo number_format($total_credit, 2); ?></div>
                    </div>
                    <div class="summary-card" style="background: rgba(239,68,68,0.06);">
                        <div class="label">Total Debit (Expenses)</div>
                        <div class="value debit-amount">₹<?php echo number_format($total_debit, 2); ?></div>
                    </div>
                    <div class="summary-card" style="background: <?php echo $net_balance >= 0 ? 'rgba(16,185,129,0.06)' : 'rgba(239,68,68,0.06)'; ?>;">
                        <div class="label">Net Balance</div>
                        <div class="value <?php echo $net_balance >= 0 ? 'balance-credit' : 'balance-debit'; ?>">
                            ₹<?php echo number_format(abs($net_balance), 2); ?>
                            <span style="font-size: 0.75rem; font-weight: 600;"><?php echo $net_balance >= 0 ? '(Cr)' : '(Dr)'; ?></span>
                        </div>
                    </div>
                </div>

                <div class="report-actions">
                    <button type="button" class="btn-report btn-print"
                        onclick="window.open('reports/rpt_payments_report.php?bill_type=<?php echo $bill_type; ?>&course_type=<?php echo $course_type; ?>&student_id=<?php echo $student_id; ?>&expense_category_id=<?php echo $expense_category_id; ?>&from_date=<?php echo $from_date; ?>&to_date=<?php echo $to_date; ?>', '_blank')">
                        <i class="fas fa-print"></i> Print Report
                    </button>
                    <button type="button" class="btn-report btn-excel"
                        onclick="getExcelReport();">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                </div>

                <table class="responsive table table-bordered" id="payment_report_table">
                    <thead>
                        <tr>
                            <th>Sno</th>
                            <th>Bill ID</th>
                            <th>Bill Date</th>
                            <th>Bill Type</th>
                            <th>Bill Details</th>
                            <th>Credit</th>
                            <th>Debit</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $sno = 1;
                            if(!empty($payments)) {
                                foreach ($payments as $p) {
                        ?>
                                    <tr>
                                        <td><?php echo $sno++; ?></td>
                                        <td><span style="font-weight: 600;"><?php echo htmlspecialchars($p['bill_id']); ?></span></td>
                                        <td><?php echo date('d M Y', strtotime($p['bill_date'])); ?></td>
                                        <td>
                                            <span class="<?php echo $p['bill_type'] === 'Receipt' ? 'bill-type-receipt' : 'bill-type-expense'; ?>">
                                                <?php echo $p['bill_type']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($p['bill_details']); ?></td>
                                        <td>
                                            <?php if($p['credit'] > 0) { ?>
                                                <span class="credit-amount">₹<?php echo number_format($p['credit'], 2); ?></span>
                                            <?php } else { echo '-'; } ?>
                                        </td>
                                        <td>
                                            <?php if($p['debit'] > 0) { ?>
                                                <span class="debit-amount">₹<?php echo number_format($p['debit'], 2); ?></span>
                                            <?php } else { echo '-'; } ?>
                                        </td>
                                        <td>
                                            <span class="<?php echo $p['balance'] >= 0 ? 'balance-credit' : 'balance-debit'; ?>">
                                                ₹<?php echo number_format(abs($p['balance']), 2); ?>
                                                <?php echo $p['balance'] >= 0 ? '(Cr)' : '(Dr)'; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <tr style="background: #f8fafc;">
                                    <td colspan="5" style="text-align: right; font-weight: bold; font-size: 1.05rem; color: var(--primary);">Grand Total</td>
                                    <td><span class="credit-amount" style="font-size: 1.05rem;">₹<?php echo number_format($total_credit, 2); ?></span></td>
                                    <td><span class="debit-amount" style="font-size: 1.05rem;">₹<?php echo number_format($total_debit, 2); ?></span></td>
                                    <td>
                                        <span class="<?php echo $net_balance >= 0 ? 'balance-credit' : 'balance-debit'; ?>" style="font-size: 1.05rem;">
                                            ₹<?php echo number_format(abs($net_balance), 2); ?>
                                            <?php echo $net_balance >= 0 ? '(Cr)' : '(Dr)'; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="8" style="text-align:center; padding: 2rem; color: var(--text-muted);">No records found.</td>
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
        function onBillTypeChange() {
            var billType = $('#bill_type').val();
            
            if (billType === 'Receipt') {
                $('#filter_course_type').show();
                $('#filter_student').show();
                $('#filter_expense_category').hide();
                // Reset expense category
                $('#expense_category_id').val('');
            } else if (billType === 'Expense') {
                $('#filter_course_type').hide();
                $('#filter_student').hide();
                $('#filter_expense_category').show();
                // Reset receipt filters
                $('#course_type').val('');
                $('#student_id').val('');
            } else {
                // All - show no specific filters
                $('#filter_course_type').hide();
                $('#filter_student').hide();
                $('#filter_expense_category').hide();
                // Reset all specific filters
                $('#course_type').val('');
                $('#student_id').val('');
                $('#expense_category_id').val('');
            }
            
            getReport();
        }

        function getReport() {
            if($('#payment_report_form').length > 0) {
                $('#payment_report_form').submit();
            }
        }

        function getExcelReport() {
            let table = document.getElementById("payment_report_table");
            let workbook = XLSX.utils.table_to_book(table, {
                sheet: "Payments Report"
            });
            XLSX.writeFile(workbook, "Payments_Report.xlsx");
        }
    </script>
</body>
</html>
