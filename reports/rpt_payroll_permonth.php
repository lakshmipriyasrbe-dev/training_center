<?php
require_once '../common_file.php';
require_once '../fpdf/fpdf.php';

if ($user_role != 'admin' && !$is_management) { exit('Unauthorized'); }

$month = $_REQUEST['month'] ?? '';
$year = $_REQUEST['year'] ?? '';

if (empty($month) || empty($year)) {
    exit('Month and Year not provided');
}

// Fetch payroll data for the month
$query = "SELECT p.*, s.staff_name, s.staff_id as s_id, r.role_name 
          FROM " . $GLOBALS['payroll_table'] . " p 
          JOIN " . $GLOBALS['staff_table'] . " s ON p.staff_id = s.staff_id 
          LEFT JOIN " . $GLOBALS['role_table'] . " r ON s.role_id = r.role_id 
          WHERE p.month = '$month' AND p.year = '$year' AND p.deleted = 0 
          ORDER BY s.staff_name ASC";
$records = $bf->getQueryRecords($query);

if (empty($records)) {
    exit('No payroll records found for the selected month');
}

// Fetch company details
$company_id = $_SESSION['company_id'] ?? $GLOBALS['bill_company_id'] ?? '';
$company = [];
if (!empty($company_id)) {
    $company_list = $bf->getTableRecords($GLOBALS['company_table'], 'company_id', $company_id);
    $company = !empty($company_list) ? $company_list[0] : [];
}
if (empty($company)) {
    $company_list = $bf->getTableRecords($GLOBALS['company_table'], 'deleted', 0);
    $company = !empty($company_list) ? $company_list[0] : [];
}

$payroll_number = $bf->encode_decode('decrypt', $records[0]['payroll_number']);
$month_name = date("F", mktime(0, 0, 0, (int)$month, 10));

class PDF extends FPDF {
    private $company;
    private $month_year;
    private $payroll_number;
    private $margin = 8;

    public function __construct($company, $month_year, $payroll_number) {
        parent::__construct('P', 'mm', 'A4');
        $this->SetMargins($this->margin, $this->margin, $this->margin);
        $this->company = $company;
        $this->month_year = $month_year;
        $this->payroll_number = $payroll_number;
    }

    public function Header() {
        // Outer border
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.4);
        $this->Rect($this->margin, $this->margin, $this->w - 2*$this->margin, $this->h - 2*$this->margin);

        /* ---------- LOGO ---------- */
        $logo_path = "";
        if (!empty($this->company['logo_image'])) {
            $logo_path = "../" . $this->company['logo_image'];
        }
        if (empty($logo_path) || !file_exists($logo_path)) {
            $logo_path = "../main/images/logo.png";
        }
        if(file_exists($logo_path)) {
            $this->Image($logo_path, $this->margin + 5, $this->margin + 5, 20);
        }

        // Header Section
        $this->SetFont('Arial', 'B', 14);
        $this->SetX($this->margin + 30);
        $this->Cell(0, 8, strtoupper($this->company['company_name'] ?? get_company_name()), 0, 1, 'C');
        
        $this->SetFont('Arial', '', 9);
        $this->SetX($this->margin + 30);
        $this->Cell(0, 5, $this->company['company_address'] ?? '', 0, 1, 'C');
        $this->SetX($this->margin + 30);
        $this->Cell(0, 5, 'Ph: ' . ($this->company['company_mobile'] ?? '') . ' | Email: ' . ($this->company['company_email'] ?? ''), 0, 1, 'C');
        
        $this->Ln(3);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 7, 'MONTHLY PAYROLL REPORT - ' . strtoupper($this->month_year), 0, 1, 'C');
        
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 6, 'Payroll No: ' . $this->payroll_number, 0, 1, 'R');
        
        $this->SetDrawColor(180, 180, 180);
        $this->Line($this->margin + 2, $this->GetY(), $this->w - $this->margin - 2, $this->GetY());
        $this->Ln(4);
    }

    public function Footer() {
        $this->SetY(-30);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(97, 6, 'Authorized By: ________________', 0, 0, 'L');
        $this->Cell(97, 6, 'Verified By: ________________', 0, 1, 'R');
        
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 5, 'Generated on ' . date('d-m-Y H:i:s') . ' | Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF($company, "$month_name $year", $payroll_number);
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFillColor(240, 240, 240);

// Table Column Definitions [Label, Width, Align]
// Total Width: 194mm (for A4 210mm - 16mm margins)
$cols = [
    ['S.No', 10, 'C'],
    ['Staff Name', 48, 'L'],
    ['Role', 28, 'L'],
    ['Salary', 18, 'R'],
    ['Per Day', 15, 'R'],
    ['CL', 8, 'C'],
    ['LOP', 8, 'C'],
    ['Deduct', 18, 'R'],
    ['Incentive', 18, 'R'],
    ['Total Net', 23, 'R']
];
// Sum: 10+48+28+18+15+8+8+18+18+23 = 194mm

// Header Row
foreach ($cols as $col) {
    $pdf->Cell($col[1], 8, $col[0], 1, 0, 'C', true);
}
$pdf->Ln();

$pdf->SetFont('Arial', '', 8);
$sno = 1;
$t_salary = 0;
$t_deduction = 0;
$t_incentive = 0;
$t_net = 0;

foreach ($records as $row) {
    $pdf->Cell($cols[0][1], 7, $sno++, 1, 0, $cols[0][2]);
    $pdf->Cell($cols[1][1], 7, substr($row['staff_name'], 0, 30), 1, 0, $cols[1][2]);
    $pdf->Cell($cols[2][1], 7, substr($row['role_name'], 0, 18), 1, 0, $cols[2][2]);
    $pdf->Cell($cols[3][1], 7, number_format($row['monthly_salary'], 2), 1, 0, $cols[3][2]);
    $pdf->Cell($cols[4][1], 7, number_format($row['per_day_salary'], 2), 1, 0, $cols[4][2]);
    $pdf->Cell($cols[5][1], 7, (float)$row['cl_days'], 1, 0, $cols[5][2]);
    $pdf->Cell($cols[6][1], 7, (float)$row['lop_days'], 1, 0, $cols[6][2]);
    $pdf->Cell($cols[7][1], 7, number_format($row['total_deduction'], 2), 1, 0, $cols[7][2]);
    $pdf->Cell($cols[8][1], 7, number_format($row['incentive_amount'], 2), 1, 0, $cols[8][2]);
    $pdf->Cell($cols[9][1], 7, number_format($row['net_salary'], 2), 1, 1, $cols[9][2]);
    
    $t_salary += $row['monthly_salary'];
    $t_deduction += $row['total_deduction'];
    $t_incentive += $row['incentive_amount'];
    $t_net += $row['net_salary'];
}

// Grand Total Row
$pdf->SetFont('Arial', 'B', 8);
$grand_label_w = $cols[0][1] + $cols[1][1] + $cols[2][1];
$pdf->Cell($grand_label_w, 8, 'GRAND TOTAL', 1, 0, 'R', true);
$pdf->Cell($cols[3][1], 8, number_format($t_salary, 2), 1, 0, 'R', true);
$pdf->Cell($cols[4][1] + $cols[5][1] + $cols[6][1], 8, '', 1, 0, 'C', true);
$pdf->Cell($cols[7][1], 8, number_format($t_deduction, 2), 1, 0, 'R', true);
$pdf->Cell($cols[8][1], 8, number_format($t_incentive, 2), 1, 0, 'R', true);
$pdf->Cell($cols[9][1], 8, number_format($t_net, 2), 1, 1, 'R', true);

$pdf->Output('I', 'Payroll_' . $month_name . '_' . $year . '.pdf');
?>
