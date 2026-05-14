<?php
require_once '../common_file.php';
require_once '../fpdf/fpdf.php';

if ($user_role != 'admin') { exit('Unauthorized'); }

$receipt_id = $_REQUEST['receipt_id'] ?? '';

if (empty($receipt_id)) {
    exit('Receipt ID not provided');
}

// Fetch receipt data
$receipt_list = $bf->getTableRecords($GLOBALS['payment_table'], 'id', $receipt_id);
if (empty($receipt_list)) {
    exit('Receipt not found');
}

$receipt = $receipt_list[0];

// Fetch company details
$company_list = $bf->getTableRecords($GLOBALS['company_table'], 'deleted', 0);
$company = !empty($company_list) ? $company_list[0] : [];

// Fetch student details
$student_table = $receipt['course_type'] === 'internship' ? $GLOBALS['enrollment_internship_table'] : $GLOBALS['enrollment_table'];
$id_field = $receipt['course_type'] === 'internship' ? 'enrollment_internship_id' : 'enrollment_id';
$student_record = $bf->getTableRecords($student_table, $id_field, $receipt['student_id']);
$student = !empty($student_record) ? $student_record[0] : [];

// Decode payment details
$payment_mode_ids = array_filter(array_map('trim', explode(',', $receipt['payment_mode'] ?? '')));
$bank_ids = array_filter(array_map('trim', explode(',', $receipt['bank'] ?? '')));
$amounts = array_filter(array_map('trim', explode(',', $receipt['amount'] ?? '')));

// Fetch payment mode and bank names
$payment_modes_data = [];
$banks_data = [];

if (!empty($payment_mode_ids)) {
    foreach ($payment_mode_ids as $pm_id) {
        $pm_record = $bf->getTableRecords($GLOBALS['payment_mode_table'], 'payment_mode_id', $pm_id);
        if (!empty($pm_record)) {
            $payment_modes_data[] = $pm_record[0]['payment_mode_name'];
        }
    }
}

if (!empty($bank_ids)) {
    foreach ($bank_ids as $bank_id) {
        if (!empty($bank_id)) {
            $bank_record = $bf->getTableRecords($GLOBALS['bank_table'], 'bank_id', $bank_id);
            if (!empty($bank_record)) {
                $banks_data[] = $bank_record[0]['bank_name'];
            } else {
                $banks_data[] = '';
            }
        } else {
            $banks_data[] = '';
        }
    }
}

// Create PDF
class PDF extends FPDF {
    private $company;
    private $receipt;
    private $student;
    private $payment_modes;
    private $banks;
    private $amounts;
    private $margin = 10;

    public function __construct($company, $receipt, $student, $payment_modes, $banks, $amounts) {
        parent::__construct('L', 'mm', 'A5');
        $this->SetMargins($this->margin, $this->margin, $this->margin);
        $this->company = $company;
        $this->receipt = $receipt;
        $this->student = $student;
        $this->payment_modes = $payment_modes;
        $this->banks = $banks;
        $this->amounts = $amounts;
    }

    public function Header() {
        // Draw border
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.5);
        $this->Rect($this->margin, $this->margin, $this->w - 2*$this->margin, $this->h - 2*$this->margin);
        
        // Company Details - Top
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 6, $this->company['company_name'] ?? 'TRAINING CENTER', 0, 1, 'C');
        
        $this->SetFont('Arial', '', 8);
        $this->Cell(0, 3.5, $this->company['company_address'] ?? '', 0, 1, 'C');
        $this->Cell(0, 3.5, 'Ph: ' . ($this->company['company_mobile'] ?? '') . ' | Email: ' . ($this->company['company_email'] ?? ''), 0, 1, 'C');
        
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, 'RECEIPT', 0, 1, 'C');
        
        $this->SetDrawColor(180, 180, 180);
        $this->Line($this->margin + 2, $this->GetY(), $this->w - $this->margin - 2, $this->GetY());
        $this->Ln(2);
    }

    public function Footer() {
        $this->SetY(-30);
        $this->SetFont('Arial', '', 7);
        $this->SetDrawColor(180, 180, 180);
        $this->Line($this->margin + 2, $this->GetY(), $this->w - $this->margin - 2, $this->GetY());
        
        $this->Ln(2);
        $this->Cell(50, 4, 'Authorized By: ________________', 0, 0, 'L');
        $this->Cell(50, 4, 'Verified By: ________________', 0, 1, 'R');
        
        $this->SetY(-8);
        $this->SetFont('Arial', '', 7);
        $this->Cell(0, 3, 'Receipt # ' . substr($this->receipt['receipt_id'] ?? '', 0, 15) . ' | Page ' . $this->PageNo(), 0, 1, 'C');
    }
}

$pdf = new PDF($company, $receipt, $student, $payment_modes_data, $banks_data, $amounts);
$pdf->AddPage();

$margin_left = 12;

// Student Details (Left) and Receipt Details (Right)
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetXY($margin_left, $pdf->GetY());
$pdf->Cell(45, 5, 'STUDENT DETAILS', 0, 0, 'L');
$pdf->SetXY(75, $pdf->GetY());
$pdf->Cell(50, 5, 'RECEIPT DETAILS', 0, 1, 'L');

$pdf->SetFont('Arial', '', 7.5);

// Student Details
$student_y = $pdf->GetY() + 1;
$pdf->SetXY($margin_left, $student_y);
$pdf->Cell(12, 4, 'ID:', 0, 0);
$pdf->Cell(33, 4, $bf->encode_decode('decrypt', $student['student_id'] ?? ''), 0, 1);

$pdf->SetXY($margin_left, $pdf->GetY());
$pdf->Cell(12, 4, 'Name:', 0, 0);
$pdf->Cell(33, 4, substr($student['student_name'] ?? '', 0, 20), 0, 1);

$pdf->SetXY($margin_left, $pdf->GetY());
$pdf->Cell(12, 4, 'Mobile:', 0, 0);
$pdf->Cell(33, 4, $student['mobile_number'] ?? '', 0, 1);

$pdf->SetXY($margin_left, $pdf->GetY());
$pdf->Cell(12, 4, 'Course:', 0, 0);
$course_data = $bf->getTableRecords($GLOBALS['course_table'], 'course_id', $student['course_id'] ?? '');
$course_name = !empty($course_data) ? substr($course_data[0]['course_name'], 0, 18) : '';
$pdf->Cell(33, 4, $course_name, 0, 1);

// Receipt Details (on the right, aligned to student_y)
$pdf->SetXY(75, $student_y);
$pdf->Cell(12, 4, 'Receipt #:', 0, 0);
$pdf->Cell(38, 4, substr($receipt['payment_id'] ?? '', 0, 18), 0, 1);

$pdf->SetXY(75, $student_y + 4);
$pdf->Cell(12, 4, 'Date:', 0, 0);
$pdf->Cell(38, 4, date('d/m/Y', strtotime($receipt['payment_date'] ?? '')), 0, 1);

$pdf->SetXY(75, $student_y + 8);
$pdf->Cell(12, 4, 'Type:', 0, 0);
$pdf->Cell(38, 4, ucfirst($receipt['course_type'] ?? ''), 0, 1);

$pdf->Ln(3);

// Payment Details Table
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(0, 4, '', 0, 1, 'L');
$pdf->Cell(0, 4, 'PAYMENT DETAILS', 0, 1, 'L');

$pdf->SetDrawColor(100, 100, 100);
$pdf->SetLineWidth(0.3);
$pdf->SetFont('Arial', 'B', 7.5);

$col_widths = [12, 28, 28, 30];
$headers = ['S.No', 'Payment Mode', 'Bank', 'Amount'];

for ($i = 0; $i < count($headers); $i++) {
    $pdf->Cell($col_widths[$i], 5, $headers[$i], 1, 0, $i === 3 ? 'R' : 'L');
}
$pdf->Ln();

$pdf->SetFont('Arial', '', 7.5);
$serial_no = 1;
foreach ($amounts as $index => $amount) {
    $pdf->Cell($col_widths[0], 4, $serial_no++, 1, 0, 'C');
    $pdf->Cell($col_widths[1], 4, substr($payment_modes_data[$index] ?? '', 0, 20), 1, 0, 'L');
    $pdf->Cell($col_widths[2], 4, substr($banks_data[$index] ?? '', 0, 20), 1, 0, 'L');
    $pdf->Cell($col_widths[3], 4, number_format((float)$amount, 2), 1, 1, 'R');
}

// Total Amount Row
$pdf->SetFont('Arial', 'B', 7.5);
$pdf->Cell($col_widths[0] + $col_widths[1] + $col_widths[2], 4, 'TOTAL', 1, 0, 'R');
$pdf->Cell($col_widths[3], 4, number_format((float)($receipt['total_amount'] ?? 0), 2), 1, 1, 'R');

$pdf->Ln(2);

// Remarks
if (!empty($receipt['description'])) {
    $pdf->SetFont('Arial', 'B', 7.5);
    $pdf->Cell(0, 4, 'Remarks:', 0, 1, 'L');
    
    $pdf->SetFont('Arial', '', 7);
    $pdf->MultiCell(0, 3, substr($receipt['description'], 0, 150), 0, 'L');
}

$pdf->Output('D', 'Receipt_' . str_replace('/', '_', $receipt['payment_id']) . '.pdf');
?>

$description = $receipt['description'] ?? '';
$pdf->MultiCell(0, 3, $description, 0, 'L');

$pdf->Output('D', 'Receipt_' . str_replace('/', '_', $receipt['payment_id']) . '.pdf');
?>
