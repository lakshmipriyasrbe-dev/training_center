<?php
require_once '../common_file.php';
require_once '../fpdf/fpdf.php';

if ($user_role != 'admin' && !$is_management) { exit('Unauthorized'); }

$staff_id = '';
if(isset($_GET['staff_id']) && !empty($_GET['staff_id'])) {
    $staff_id = $bf->sanitize($_GET['staff_id']);
}

$month = '';
if(isset($_GET['month']) && !empty($_GET['month'])) {
    $month = $bf->sanitize($_GET['month']);
}

$year = '';
if(isset($_GET['year']) && !empty($_GET['year'])) {
    $year = $bf->sanitize($_GET['year']);
}

$payroll = $bf->getPayrollReportList($staff_id, $month, $year);

$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->setTitle('Payroll Report', true);
$from_page = 'Payroll Report';

$date_display = "";
if(!empty($month) && !empty($year)) {
    $monthName = date("F", mktime(0, 0, 0, (int)$month, 10));
    $date_display = "For: " . $monthName . " " . $year;
} elseif(!empty($month)) {
    $monthName = date("F", mktime(0, 0, 0, (int)$month, 10));
    $date_display = "Month: " . $monthName;
} elseif(!empty($year)) {
    $date_display = "Year: " . $year;
}

// echo $date_display." hi";

require_once 'rpt_header.php';
$pdf->setY($title_report_y);
$pdf->setFont('Arial', 'B', 10);
$pdf->cell(10, 10, 'S.No', 1, 0, 'C');
$pdf->cell(45, 10, 'Staff Details', 1, 0, 'C');
$pdf->cell(25, 10, 'Payment Date', 1, 0, 'C');
$pdf->cell(30, 10, 'Leave Details', 1, 0, 'C');
$pdf->cell(25, 10, 'Deductions', 1, 0, 'C');
$pdf->cell(25, 10, 'Incentive', 1, 0, 'C');
$pdf->cell(0, 10, 'Total Salary', 1, 1, 'C');

$sno = 0; $grand_lop = 0; $grand_cl = 0; $grand_deduction = 0; $grand_incentive = 0; $grand_salary = 0;

if(!empty($payroll)) {
    foreach ($payroll as $payroll_data) {
        $sno++;
        $staff_name = $bf->getTableColumnValue($GLOBALS['staff_table'], 'staff_id', $payroll_data['staff_id'], 'staff_name') ?? 'N/A';
        
        $pdf->setFont('Arial', '', 9);
        $row_start_y = $pdf->getY();
        $pdf->setX(10);
        $pdf->cell(10, 6, $sno, 0, 0, 'C');
        
        $pdf->Multicell(45, 6, $staff_name, 0, 'L');
        $staff_y = $pdf->getY();
        
        $pdf->setXY(65, $row_start_y); 
        $payment_date = !empty($payroll_data['payment_date']) ? date('d-m-Y', strtotime($payroll_data['payment_date'])) : '-';
        $pdf->Multicell(25, 6, $payment_date, 0, 'C');
        $date_y = $pdf->getY();
        
        $pdf->setXY(90, $row_start_y); 
        $leave_details = "LOP : " . ($payroll_data['lop_days'] ?? 0) . " days\nCL : " . ($payroll_data['cl_days'] ?? 0) . " days";
        $grand_lop += $payroll_data['lop_days'] ?? 0;
        $grand_cl += $payroll_data['cl_days'] ?? 0;
        $pdf->Multicell(30, 5, $leave_details, 0, 'L');
        $leave_y = $pdf->getY();
        
        $pdf->setXY(120, $row_start_y); 
        $pdf->Multicell(25, 6, ($payroll_data['total_deduction'] ?? '0'), 0, 'C');
        $grand_deduction += $payroll_data['total_deduction'] ?? 0;
        $deduction_y = $pdf->getY();
        
        $pdf->setXY(145, $row_start_y); 
        $pdf->Multicell(25, 6, ($payroll_data['incentive_amount'] ?? '0'), 0, 'C');
        $grand_incentive += $payroll_data['incentive_amount'] ?? 0;
        $incentive_y = $pdf->getY();
        
        $pdf->setXY(170, $row_start_y); 
        $pdf->Multicell(0, 6, ($payroll_data['net_salary'] ?? '0'), 0, 'C');
        $grand_salary += $payroll_data['net_salary'] ?? 0;
        $salary_y = $pdf->getY();
        
        $max_y = max([$staff_y, $date_y, $leave_y, $deduction_y, $incentive_y, $salary_y]); 
        
        $pdf->setY($row_start_y);
        $pdf->setX(10);
        $pdf->cell(10, ($max_y - $row_start_y), '', '1', 0, 'C');
        $pdf->cell(45, ($max_y - $row_start_y), '', '1', 0, 'C');
        $pdf->cell(25, ($max_y - $row_start_y), '', '1', 0, 'C');
        $pdf->cell(30, ($max_y - $row_start_y), '', '1', 0, 'C');
        $pdf->cell(25, ($max_y - $row_start_y), '', '1', 0, 'C');
        $pdf->cell(25, ($max_y - $row_start_y), '', '1', 0, 'C');
        $pdf->cell(0, ($max_y - $row_start_y), '', '1', 1, 'C');
    }
    
    $row_end_y = $pdf->getY();
    $pdf->setFont('Arial', 'B', 10); 
    $pdf->setX(10);
    $pdf->cell(80, 10, 'Grand Total', 0, 0, 'R'); 
    $grand_y1 = $pdf->getY();
    
    $pdf->setXY(90, $row_end_y);
    $grand_leave = "LOP : " . $grand_lop . " days\nCL : " . $grand_cl . " days";
    $pdf->Multicell(30, 5, $grand_leave, 0, 'L');
    $grand_y2 = $pdf->getY();
    
    $pdf->setXY(120, $row_end_y);
    $pdf->Multicell(25, 10, $grand_deduction, 0, 'C');
    $grand_y3 = $pdf->getY();
    
    $pdf->setXY(145, $row_end_y);
    $pdf->Multicell(25, 10, $grand_incentive, 0, 'C');
    $grand_y4 = $pdf->getY();
    
    $pdf->setXY(170, $row_end_y);
    $pdf->Multicell(0, 10, $grand_salary, 0, 'C');
    $grand_y5 = $pdf->getY();
    
    $max_y = max([$grand_y1, $grand_y2, $grand_y3, $grand_y4, $grand_y5]);
    
    $pdf->setXY(10, $row_end_y);
    $pdf->cell(80, ($max_y - $row_end_y), '', '1', 0, 'C');
    $pdf->cell(30, ($max_y - $row_end_y), '', '1', 0, 'C');
    $pdf->cell(25, ($max_y - $row_end_y), '', '1', 0, 'C');
    $pdf->cell(25, ($max_y - $row_end_y), '', '1', 0, 'C');
    $pdf->cell(0, ($max_y - $row_end_y), '', '1', 1, 'C');
    
} else {
    $pdf->cell(0, 10, 'No records found', 1, 1, 'C');
}

$pdf->setXY(10, 10);
$pdf->cell(190, 258, '', 1, 1, 'C');
$pdf->Ln(2);
$pdf->setX(10);
$pdf->setFont('Arial', 'I', 8);
$pdf->setTextColor(148, 163, 184);
$pdf->cell(160, 5, 'Report generated on ' . date('d-m-Y H:i:s'), 0, 0, 'L');
$pdf->setX(170);
$pdf->Cell(0, 5, 'Page ' . $pdf->PageNo() . ' / {nb}', 0, 1, 'C');
$pdf->output();
?>
