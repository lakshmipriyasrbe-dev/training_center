<?php
require_once '../common_file.php';
require_once '../fpdf/fpdf.php';

if ($user_role != 'admin' && !$is_management) { exit('Unauthorized'); }

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

// Voucher Details (Left) and Additional Details (Right)
$margin_left = 12;

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(14, 165, 233); // Sky Blue Primary
$pdf->SetXY($margin_left, $pdf->GetY());
$pdf->Cell(45, 5, 'VOUCHER DETAILS', 0, 0, 'L');
$pdf->SetXY(85, $pdf->GetY());
$pdf->Cell(50, 5, 'ADDITIONAL DETAILS', 0, 1, 'L');

$pdf->SetFont('Arial', '', 8);

// Voucher Details Columns
$detail_y = $pdf->GetY() + 1;
$pdf->SetXY($margin_left, $detail_y);
$pdf->SetTextColor(100, 116, 139); // Slate Label
$pdf->Cell(25, 4.5, 'Voucher No:', 0, 0);
$pdf->SetTextColor(30, 41, 59); // Charcoal Value
$pdf->Cell(45, 4.5, $expense_entry['expense_entry_number'], 0, 1);

$pdf->SetXY($margin_left, $pdf->GetY());
$pdf->SetTextColor(100, 116, 139);
$pdf->Cell(25, 4.5, 'Date:', 0, 0);
$pdf->SetTextColor(30, 41, 59);
$pdf->Cell(45, 4.5, date('d-m-Y', strtotime($expense_entry['expense_entry_date'])), 0, 1);

$pdf->SetXY($margin_left, $pdf->GetY());
$pdf->SetTextColor(100, 116, 139);
$pdf->Cell(25, 4.5, 'Category:', 0, 0);
$pdf->SetTextColor(30, 41, 59);
$pdf->Cell(45, 4.5, $category_name, 0, 1);

// Right Column: Description
if (!empty($expense_entry['description'])) {
    $pdf->SetXY(85, $detail_y);
    $pdf->SetTextColor(100, 116, 139);
    $pdf->Cell(18, 4.5, 'Description:', 0, 0);
    $pdf->SetTextColor(30, 41, 59);
    $pdf->SetXY(85 + 18, $detail_y);
    $pdf->MultiCell(80, 4, $expense_entry['description'], 0, 'L');
}

$pdf->SetY($detail_y + 19);

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
$pdf->Cell($col_widths[3], 6, 'Rs. ' . number_format((float)($expense_entry['total_amount'] ?? 0), 2), 1, 1, 'R', true);
$pdf->Ln(4);

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

$pdf->Output('', 'ExpenseVoucher_' . $expense_entry['expense_entry_number'] . '.pdf');
?>
