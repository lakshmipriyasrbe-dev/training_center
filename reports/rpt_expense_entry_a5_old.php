<?php
require_once '../common_file.php';
require_once '../fpdf/fpdf.php';

if ($user_role != 'admin') { exit('Unauthorized'); }

$expense_entry_id = $_GET['expense_entry_id'] ?? $_REQUEST['expense_entry_id'] ?? '';
if (empty($expense_entry_id)) {
    exit('Expense entry ID missing');
}

// Fetch Expense Entry
$expense_entry_list = $bf->getTableRecords($GLOBALS['expense_entry_table'], 'id', $expense_entry_id);
if (empty($expense_entry_list)) {
    exit('Expense entry not found');
}
$expense_entry = $expense_entry_list[0];

// Fetch Company Info
$company_list = $bf->getTableRecords($GLOBALS['company_table'], 'deleted', 0);
$company = !empty($company_list) ? $company_list[0] : [];

// Fetch Expense Category
$category_name = 'N/A';
if (!empty($expense_entry['expense_category_id'])) {
    $category_name = $bf->getTableColumnValue($GLOBALS['expense_category_table'], 'expense_category_id', $expense_entry['expense_category_id'], 'expense_category_name') ?? 'N/A';
}

// Decode Payment details
$payment_mode_ids = array_filter(array_map('trim', explode(',', $expense_entry['payment_mode'] ?? '')));
$bank_ids = array_filter(array_map('trim', explode(',', $expense_entry['bank'] ?? '')));
$amounts = array_filter(array_map('trim', explode(',', $expense_entry['amount'] ?? '')));

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

// Create FPDF Document
$pdf = new FPDF('L', 'mm', 'A5');
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 4);
$pdf->AddPage();
$pdf->SetTitle('Expense Voucher - ' . $expense_entry['expense_entry_number'], true);

$from_page = 'EXPENSE VOUCHER';
require_once 'rpt_header_a5.php';

// Voucher Details
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->Cell(35, 5, 'Expense Voucher No:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 8.5);
$pdf->Cell(55, 5, $expense_entry['expense_entry_number'], 0, 0, 'L');

$pdf->SetFont('Arial', 'B', 8.5);
$pdf->Cell(35, 5, 'Expense Voucher Date:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 8.5);
$pdf->Cell(0, 5, date('d-m-Y', strtotime($expense_entry['expense_entry_date'])), 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 8.5);
$pdf->Cell(35, 5, 'Expense Category:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 8.5);
$pdf->Cell(0, 5, $category_name, 0, 1, 'L');

if (!empty($expense_entry['description'])) {
    $pdf->SetFont('Arial', 'B', 8.5);
    $pdf->Cell(35, 5, 'Description:', 0, 0, 'L');
    $pdf->SetFont('Arial', '', 8.5);
    $pdf->MultiCell(0, 5, $expense_entry['description'], 0, 'L');
}
$pdf->Ln(4);

// Payment Details Table
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->Cell(0, 5, 'PAYMENT DETAILS', 0, 1, 'L');

$pdf->SetDrawColor(180, 180, 180);
$pdf->SetLineWidth(0.3);
$pdf->SetFont('Arial', 'B', 8);

$col_widths = [15, 60, 60, 45];
$headers = ['S.No', 'Payment Mode', 'Bank', 'Amount'];

for ($i = 0; $i < count($headers); $i++) {
    $pdf->Cell($col_widths[$i], 5, $headers[$i], 1, 0, $i === 3 ? 'R' : 'L');
}
$pdf->Ln();

$pdf->SetFont('Arial', '', 8);
$serial_no = 1;
foreach ($amounts as $index => $amount) {
    $pdf->Cell($col_widths[0], 4.5, $serial_no++, 1, 0, 'C');
    $pdf->Cell($col_widths[1], 4.5, $payment_modes_data[$index] ?? 'N/A', 1, 0, 'L');
    $pdf->Cell($col_widths[2], 4.5, !empty($banks_data[$index]) ? $banks_data[$index] : '-', 1, 0, 'L');
    $pdf->Cell($col_widths[3], 4.5, 'Rs. ' . number_format((float)$amount, 2), 1, 1, 'R');
}

// Total Row
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell($col_widths[0] + $col_widths[1] + $col_widths[2], 5, 'TOTAL AMOUNT', 1, 0, 'R');
$pdf->Cell($col_widths[3], 5, 'Rs. ' . number_format((float)($expense_entry['total_amount'] ?? 0), 2), 1, 1, 'R');
$pdf->Ln(6);

// Signatures section at bottom of A5 Page
$pdf->SetAutoPageBreak(false);
$pdf->SetY(-22);
$pdf->SetFont('Arial', '', 7.5);
$pdf->Cell(60, 4, 'Prepared By: __________________', 0, 0, 'L');
$pdf->Cell(60, 4, 'Verified By: __________________', 0, 0, 'C');
$pdf->Cell(0, 4, 'Authorized Signatory: __________________', 0, 1, 'R');

$pdf->SetY(-5);
$pdf->SetFont('Arial', 'I', 7);
$pdf->SetTextColor(148, 163, 184);
$pdf->Cell(0, 3, 'Generated on ' . date('d-m-Y H:i:s'), 0, 1, 'C');

$pdf->Output('', 'ExpenseVoucher_' . $expense_entry['expense_entry_number'] . '.pdf');
?>
