<?php require_once 'common_file.php'; 
$from_page = 'Report - Payroll';

$start = ""; $limit = ""; $month = ""; $year = ""; $grand_total = 0; $grand_lop = 0; $grand_cl = 0;$grand_deduction = 0; $grand_incentive = 0; $grand_salary = 0;
$month = date('m'); $year = date('Y'); $staffs = [];
$currentMonth = (int)date('m');
$realCurrentYear = (int)date('Y');

$staffs = $bf->getTableRecords($GLOBALS['staff_table'], 'deleted', 0);
// print_r($courses);


$staff_id = '';
if(isset($_POST['staff_id']) && !empty($_POST['staff_id'])) {
    $staff_id = $bf->sanitize($_POST['staff_id']);
}

$month = '';
if(isset($_POST['month']) && !empty($_POST['month'])) {
    $month = $bf->sanitize($_POST['month']);
    $currentMonth = (int)$month;
}

$year = '';
if(isset($_POST['year']) && !empty($_POST['year'])) {
    $year = $bf->sanitize($_POST['year']);
    $realCurrentYear = (int)$year;
}
$payroll = [];


$payroll = $bf->getPayrollReportList($staff_id, $month, $year);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report - Payroll</title>
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

            <div id="payroll_list">
                <form name="payroll_report_form" id="payroll_report_form" method="post">
                    <div class="form-row">
                        <div class="form-group col-3">
                            <label>Staff </label>
                            <select name="staff_id" id="staff_id" class="form-input" onchange="getReport()">
                                <option value="">Select</option>
                                <?php foreach ($staffs as $data) { ?>
                                    <option value="<?php echo $data['staff_id']; ?>" <?php echo $staff_id === $data['staff_id'] ? 'selected' : ''; ?>><?php echo $data['staff_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group col-3">
                            <label>Month </label>
                            <select name="month" id="month" class="form-input" onchange="getReport()">
                                <?php
                                    
                                    for ($m=1; $m<=$currentMonth; $m++) {
                                        $monthName = date("F", mktime(0, 0, 0, $m, 10));
                                        $selected = ($m == $currentMonth) ? 'selected' : '';
                                        echo "<option value='".str_pad($m, 2, '0', STR_PAD_LEFT)."' $selected>$monthName</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group col-3">
                            <label>Year </label>
                            <select name="year" id="year" class="form-input" onchange="getReport()">
                                <?php
                                    
                                    for ($y=$realCurrentYear-2; $y<=$realCurrentYear; $y++) {
                                        $is_selected = ($y == 2026) ? 'selected' : '';
                                        echo "<option value='$y' $is_selected>$y</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                </form>
                <div class="report-actions">
    
                    <button type="button" class="btn-report btn-print"
                        onclick="window.open('reports/rpt_payroll_report.php?staff_id=<?php echo $staff_id; ?>&month=<?php echo $currentMonth; ?>&year=<?php echo $realCurrentYear; ?>', '_blank')">
                        
                        <i class="fas fa-print"></i> Print Report
                    </button>

                    <button type="button" class="btn-report btn-excel"
                        onclick="getExcelReport();">
                        
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>

                </div>
                <table id="payroll-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Sno</th>                            
                            <th>staff Details</th>
                            <th>Payment Date</th>
                            <th>Leave Details</th>
                            <th>Total Deductions</th>
                            <th>Incentive Amount</th>
                            <th>Total Salary</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $sno = 1;
                            if(!empty($payroll)) {
                                foreach ($payroll as $payroll_data) { 
                                    $staff_name = '';
                                    $staff_name =  $bf->getTableColumnValue($GLOBALS['staff_table'], 'staff_id', $payroll_data['staff_id'], 'staff_name');
                                    ?>
                                    <tr>
                                        <td><?php echo $sno++; ?></td>
                                        <td>
                                            <?php echo $staff_name; ?><br>
                                        </td>
                                        <td><?php echo $payroll_data['payment_date']; ?></td>
                                        <td><?php echo " LOP : " . $payroll_data['lop_days']; ?> days
                                        <br> <?php echo " CL : " . $payroll_data['cl_days']; ?> days 
                                            <?php $grand_lop += $payroll_data['lop_days']; $grand_cl += $payroll_data['cl_days']; ?>                                        
                                        </td>
                                        <td><?php echo $payroll_data['total_deduction']; 
                                        $grand_deduction += $payroll_data['total_deduction']; ?></td>
                                        <td><?php echo $payroll_data['incentive_amount']; $grand_incentive += $payroll_data['incentive_amount']; ?></td>
                                        <td><?php echo $payroll_data['net_salary']; $grand_salary += $payroll_data['net_salary']; ?></td>
                                    </tr>
                                <?php } ?>
                                    <tr style="font-weight: 600; background-color: var(--light);">
                                        <td colspan="3" style="text-align: right;">Grand Total</td>
                                        <td><?php echo " LOP : " . $grand_lop; ?> days
                                            <br> <?php echo " CL : " . $grand_cl; ?> days 
                                        </td>
                                        <td><?php echo $grand_deduction; ?></td>
                                        <td><?php echo $grand_incentive; ?></td>
                                        <td><?php echo $grand_salary; ?></td>
                                    </tr>

                            <?php } else { ?>
                                <tr>
                                    <td colspan="7" style="text-align:center; padding: 2rem; color: var(--text-muted);">No payrolls found.</td>
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
            if($('#payroll_report_form').length > 0) {
                $('#payroll_report_form').submit();
            }
        }

        function getExcelReport() {

            let table = document.getElementById("payroll-table");

            let workbook = XLSX.utils.table_to_book(table, {
                sheet: "Payrolls"
            });

            XLSX.writeFile(workbook, "Payroll_Report.xlsx");
            
        }
    </script>
</body>
</html>
