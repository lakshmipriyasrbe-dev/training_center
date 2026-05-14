<?php
require_once 'common_file.php';
require_once 'fpdf/fpdf.php';

if ($user_role != 'admin') { exit('Unauthorized'); }

$id = $_GET['id'] ?? '';
if (empty($id)) { exit('ID missing'); }

// Fetch Company Info
$company = $bf->getTableRecords($GLOBALS['company_table'], 'deleted', 0)[0] ?? [];

// Fetch Payroll & Staff Info
$query = "SELECT p.*, s.staff_name, s.staff_id as s_id, r.role_name 
          FROM " . $GLOBALS['payroll_table'] . " p 
          JOIN " . $GLOBALS['staff_table'] . " s ON p.staff_id = s.id 
          LEFT JOIN " . $GLOBALS['role_table'] . " r ON s.role_id = r.id 
          WHERE p.id = '$id'";
$data = $bf->getQueryRecords($query);

if (empty($data)) { exit('Record not found'); }

$row = $data[0];
$month_name = date("F", mktime(0, 0, 0, $row['month'], 10));
$p_id = $bf->encode_decode('decrypt', $row['payroll_id']);
$s_id = $bf->encode_decode('decrypt', $row['s_id']);

class PDF extends FPDF {
    function Header() {
        global $company;
        
        // Page Border
        $this->SetDrawColor(14, 165, 233);
        $this->SetLineWidth(0.5);
        $this->Rect(10, 10, 190, 277);
        
        $this->SetY(15);
        $this->SetFont('Arial', 'B', 20);
        $this->SetTextColor(14, 165, 233); // Primary color
        $this->Cell(0, 10, strtoupper($company['company_name'] ?? 'TRAINING CENTER'), 0, 1, 'C');
        
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(100, 116, 139);
        $address = $company['company_address'] ?? 'Company Address';
        $this->Cell(0, 5, $address, 0, 1, 'C');
        $this->Cell(0, 5, 'Email: ' . ($company['company_email'] ?? 'N/A') . ' | Mobile: ' . ($company['company_mobile'] ?? 'N/A'), 0, 1, 'C');
        
        $this->Ln(5);
        $this->SetDrawColor(226, 232, 240);
        $this->Line(20, $this->GetY(), 190, $this->GetY());
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-20);
        $this->SetX(15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(148, 163, 184);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . ' / {nb}', 0, 0, 'C');
        $this->SetX(-70);
        $this->Cell(60, 10, 'Generated on ' . date('d-m-Y H:i:s'), 0, 0, 'R');
    }
}

$pdf = new PDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();

// Title
$pdf->SetFont('Arial', 'B', 16);
$pdf->SetTextColor(30, 41, 59);
$pdf->Cell(0, 10, 'PAYSLIP - ' . strtoupper($month_name) . ' ' . $row['year'], 0, 1, 'L');
$pdf->Ln(5);

// Info Section
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(248, 250, 252);
$pdf->Cell(0, 10, ' STAFF & PAYMENT DETAILS', 1, 1, 'L', true);

$pdf->SetFont('Arial', '', 11);
// Staff Details
$pdf->Cell(50, 10, ' Staff Name:', 'L', 0);
$pdf->Cell(0, 10, $row['staff_name'], 'R', 1);

$pdf->Cell(50, 10, ' Staff ID:', 'L', 0);
$pdf->Cell(0, 10, $s_id, 'R', 1);

$pdf->Cell(50, 10, ' Role:', 'L', 0);
$pdf->Cell(0, 10, $row['role_name'], 'R', 1);

// Payment Details
$pdf->Cell(50, 10, ' Payroll ID:', 'L', 0);
$pdf->Cell(0, 10, $p_id, 'R', 1);

$pdf->Cell(50, 10, ' Payment Date:', 'L', 0);
$pdf->Cell(0, 10, date('d-m-Y', strtotime($row['payment_date'])), 'R', 1);

$pdf->Cell(50, 10, ' Month / Year:', 'LB', 0);
$pdf->Cell(0, 10, $month_name . ' ' . $row['year'], 'RB', 1);

$pdf->Ln(10);

// Salary Breakdown Table
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(14, 165, 233);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(130, 12, ' DESCRIPTION', 1, 0, 'L', true);
$pdf->Cell(60, 12, ' AMOUNT / DAYS ', 1, 1, 'R', true);

$pdf->SetTextColor(30, 41, 59);
$pdf->SetFont('Arial', '', 11);

$details = [
    ['Monthly Salary', 'Rs. ' . number_format($row['monthly_salary'], 2)],
    ['Per Day Salary', 'Rs. ' . number_format($row['per_day_salary'], 2)],
    ['Casual Leave (CL) Taken', $row['cl_days'] . ' Day(s)'],
    ['Loss of Pay (LOP) Days', $row['lop_days'] . ' Day(s)'],
    ['Total Deductions', '- Rs. ' . number_format($row['total_deduction'], 2)],
    ['Incentives (' . $row['total_references'] . ' References)', '+ Rs. ' . number_format($row['incentive_amount'], 2)]
];

foreach ($details as $detail) {
    $pdf->Cell(130, 10, ' ' . $detail[0], 1, 0, 'L');
    $pdf->Cell(60, 10, $detail[1] . ' ', 1, 1, 'R');
}

// Net Salary
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetFillColor(240, 249, 255);
$pdf->Cell(130, 14, ' NET PAID AMOUNT', 1, 0, 'L', true);
$pdf->SetTextColor(14, 165, 233);
$pdf->Cell(60, 14, 'Rs. ' . number_format($row['net_salary'], 2) . ' ', 1, 1, 'R', true);

$pdf->Ln(20);
$pdf->SetTextColor(30, 41, 59);
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 10, 'This is a computer-generated payslip and does not require a physical signature.', 0, 1, 'C');

$pdf->Output('I', 'Payslip_' . $p_id . '.pdf');
?>
