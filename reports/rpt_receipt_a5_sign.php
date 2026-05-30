<?php
require_once '../common_file.php';
require_once '../fpdf/fpdf.php';

if ($user_role != 'admin' && !$is_management) { exit('Unauthorized'); }

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

// Fetch student details
$student_table = $receipt['course_type'] === 'internship' ? $GLOBALS['enrollment_internship_table'] : $GLOBALS['enrollment_table'];
$stmt = $bf->con->prepare("SELECT * FROM $student_table WHERE student_id = :student_id AND deleted = 0");
$stmt->execute([':student_id' => $receipt['student_id']]);
$student_record = $stmt->fetchAll();
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

// Create FPDF Document (A5 Landscape)
$pdf = new FPDF('L', 'mm', 'A5');
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 4);
$pdf->AddPage();
$pdf->SetTitle('Receipt - ' . $receipt['payment_id'], true);

$from_page = 'RECEIPT';
require_once 'rpt_header_a5.php';

$margin_left = 12;

// Student Details (Left) and Receipt Details (Right)
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(14, 165, 233); // Sky Blue Primary
$pdf->SetXY($margin_left, $pdf->GetY());
$pdf->Cell(45, 5, 'STUDENT DETAILS', 0, 0, 'L');
$pdf->SetXY(85, $pdf->GetY());
$pdf->Cell(50, 5, 'RECEIPT DETAILS', 0, 1, 'L');

$pdf->SetFont('Arial', '', 8);

// Student Details Columns
$student_y = $pdf->GetY() + 1;
$pdf->SetXY($margin_left, $student_y);
$pdf->SetTextColor(100, 116, 139); // Slate Label
$pdf->Cell(15, 4.5, 'ID:', 0, 0);
$pdf->SetTextColor(30, 41, 59); // Charcoal Value
$pdf->Cell(45, 4.5, $bf->encode_decode('decrypt', $student['student_id'] ?? ''), 0, 1);

$pdf->SetXY($margin_left, $pdf->GetY());
$pdf->SetTextColor(100, 116, 139);
$pdf->Cell(15, 4.5, 'Name:', 0, 0);
$pdf->SetTextColor(30, 41, 59);
$pdf->Cell(45, 4.5, substr($student['student_name'] ?? '', 0, 25), 0, 1);

$pdf->SetXY($margin_left, $pdf->GetY());
$pdf->SetTextColor(100, 116, 139);
$pdf->Cell(15, 4.5, 'Mobile:', 0, 0);
$pdf->SetTextColor(30, 41, 59);
$pdf->Cell(45, 4.5, $student['mobile_number'] ?? '', 0, 1);

$pdf->SetXY($margin_left, $pdf->GetY());
$pdf->SetTextColor(100, 116, 139);
$pdf->Cell(15, 4.5, 'Course:', 0, 0);
$pdf->SetTextColor(30, 41, 59);
$course_data = $bf->getTableRecords($GLOBALS['course_table'], 'course_id', $student['course_id'] ?? '');
$course_name = !empty($course_data) ? substr($course_data[0]['course_name'], 0, 22) : '';
$pdf->Cell(45, 4.5, $course_name, 0, 1);

// Receipt Details Columns
$pdf->SetXY(85, $student_y);
$pdf->SetTextColor(100, 116, 139);
$pdf->Cell(18, 4.5, 'Receipt No:', 0, 0);
$pdf->SetTextColor(30, 41, 59);
$pdf->Cell(0, 4.5, $receipt['payment_id'] ?? '', 0, 1);

$pdf->SetXY(85, $student_y + 4.5);
$pdf->SetTextColor(100, 116, 139);
$pdf->Cell(18, 4.5, 'Date:', 0, 0);
$pdf->SetTextColor(30, 41, 59);
$pdf->Cell(0, 4.5, date('d-m-Y', strtotime($receipt['payment_date'] ?? '')), 0, 1);

$pdf->SetXY(85, $student_y + 9);
$pdf->SetTextColor(100, 116, 139);
$pdf->Cell(18, 4.5, 'Type:', 0, 0);
$pdf->SetTextColor(30, 41, 59);
$pdf->Cell(0, 4.5, ucfirst($receipt['course_type'] ?? ''), 0, 1);

$pdf->SetY($student_y + 19);

// Payment Details Table Header
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(14, 165, 233);
$pdf->Cell(0, 5, 'PAYMENT DETAILS', 0, 1, 'L');

$pdf->SetDrawColor(14, 165, 233); // Blue theme border
$pdf->SetLineWidth(0.3);
$pdf->SetFont('Arial', 'B', 8);

$col_widths = [15, 60, 60, 45];
$headers = ['S.No', 'Payment Mode', 'Bank', 'Amount'];

// Styled Table Header (Sky Blue fill, White bold text)
$pdf->SetFillColor(14, 165, 233);
$pdf->SetTextColor(255, 255, 255);
for ($i = 0; $i < count($headers); $i++) {
    $pdf->Cell($col_widths[$i], 5.5, $headers[$i], 1, 0, $i === 3 ? 'R' : ($i === 0 ? 'C' : 'L'), true);
}
$pdf->Ln();

// Table Body Rows
$pdf->SetFont('Arial', '', 8);
$pdf->SetDrawColor(226, 232, 240); // Soft grey grid borders
$pdf->SetLineWidth(0.2);
$serial_no = 1;
foreach ($amounts as $index => $amount) {
    // Alternating Row colors
    $fill = ($index % 2 === 0) ? false : true;
    $pdf->SetFillColor(248, 250, 252);
    $pdf->SetTextColor(30, 41, 59);
    
    $pdf->Cell($col_widths[0], 5, $serial_no++, 1, 0, 'C', $fill);
    $pdf->Cell($col_widths[1], 5, $payment_modes_data[$index] ?? 'N/A', 1, 0, 'L', $fill);
    $pdf->Cell($col_widths[2], 5, !empty($banks_data[$index]) ? $banks_data[$index] : '-', 1, 0, 'L', $fill);
    $pdf->Cell($col_widths[3], 5, 'Rs. ' . number_format((float)$amount, 2), 1, 1, 'R', $fill);
}

// Total Row
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetDrawColor(14, 165, 233); // Strong blue border for total
$pdf->SetFillColor(240, 249, 255); // Highlight fill
$pdf->SetTextColor(14, 165, 233); // Highlight text
$pdf->Cell($col_widths[0] + $col_widths[1] + $col_widths[2], 6, 'TOTAL AMOUNT', 1, 0, 'R', true);
$pdf->Cell($col_widths[3], 6, 'Rs. ' . number_format((float)($receipt['total_amount'] ?? 0), 2), 1, 1, 'R', true);
$pdf->Ln(4);

// Remarks
if (!empty($receipt['description'])) {
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetTextColor(14, 165, 233);
    $pdf->Cell(0, 4.5, 'Remarks:', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 7.5);
    $pdf->SetTextColor(71, 85, 105);
    $pdf->MultiCell(0, 4, $receipt['description'], 0, 'L');
}

// Signatures section at bottom of A5 Page
$pdf->SetAutoPageBreak(false);
$pdf->SetY(-22);
$pdf->SetFont('Arial', 'B', 7.5);
$pdf->SetTextColor(71, 85, 105);
$pdf->Cell(60, 4, 'Prepared By: __________________', 0, 0, 'L');
$pdf->Cell(60, 4, 'Verified By: __________________', 0, 0, 'C');
$pdf->Cell(0, 4, 'Authorized Signatory: __________________', 0, 1, 'R');

$pdf->SetY(-5);
$pdf->SetFont('Arial', 'I', 7);
$pdf->SetTextColor(148, 163, 184);
$pdf->Cell(0, 3, 'Generated on ' . date('d-m-Y H:i:s'), 0, 1, 'C');

$pdf->Output('', 'Receipt_' . str_replace('/', '_', $receipt['payment_id']) . '.pdf');
?>
