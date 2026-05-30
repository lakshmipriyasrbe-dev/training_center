<?php
require_once '../common_file.php';
require_once '../fpdf/fpdf.php';

if ($user_role != 'admin' && !$is_management) { exit('Unauthorized'); }

$period = $_GET['period'] ?? 'today';
$comp_id = $_SESSION['company_id'];

$today = date('Y-m-d');
$week_start = date('Y-m-d', strtotime('monday this week'));
$week_end = date('Y-m-d', strtotime('sunday this week'));
$month_start = date('Y-m-01');
$month_end = date('Y-m-t');

$start_date = '';
$end_date = '';
$date_desc = '';

if ($period === 'today') {
    $start_date = $today;
    $end_date = $today;
    $date_desc = date('d-m-Y');
} elseif ($period === 'week') {
    $start_date = $week_start;
    $end_date = $week_end;
    $date_desc = date('d-m-Y', strtotime($week_start)) . ' to ' . date('d-m-Y', strtotime($week_end));
} elseif ($period === 'month') {
    $start_date = $month_start;
    $end_date = $month_end;
    $date_desc = date('F Y');
}

$q = "SELECT e.expense_entry_number as voucher_no, e.expense_entry_date as date, e.total_amount as amount, e.description, c.expense_category_name as category
      FROM {$GLOBALS['expense_entry_table']} e
      LEFT JOIN {$GLOBALS['expense_category_table']} c ON e.expense_category_id = c.expense_category_id
      WHERE DATE(e.expense_entry_date) BETWEEN :start AND :end AND e.deleted = 0 AND e.company_id = :cid
      ORDER BY e.expense_entry_date ASC, e.id ASC";

$records = $bf->getQueryRecords($q, [':start' => $start_date, ':end' => $end_date, ':cid' => $comp_id]);

$pdf = new FPDF('L', 'mm', 'A5');
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 5);
$pdf->AddPage();

$from_page = strtoupper($period) . ' EXPENSE REPORT (' . $date_desc . ')';
require_once 'rpt_header_a5.php';

$pdf->SetFont('Arial', 'B', 8);
$pdf->SetDrawColor(180, 180, 180);
$pdf->SetLineWidth(0.3);

$widths = [12, 30, 45, 25, 48, 30];
$headers = ['S.No', 'Voucher No', 'Category', 'Date', 'Description', 'Amount'];

for ($i = 0; $i < count($headers); $i++) {
    $pdf->Cell($widths[$i], 5.5, $headers[$i], 1, 0, $i === 5 ? 'R' : 'L');
}
$pdf->Ln();

$pdf->SetFont('Arial', '', 7.5);
$sno = 1;
$total_sum = 0;

if (empty($records)) {
    $pdf->Cell(array_sum($widths), 6, 'No records found for this period.', 1, 1, 'C');
} else {
    foreach ($records as $r) {
        $total_sum += $r['amount'];
        $pdf->Cell($widths[0], 5, $sno++, 1, 0, 'C');
        $pdf->Cell($widths[1], 5, $r['voucher_no'] ?? 'N/A', 1, 0, 'L');
        $pdf->Cell($widths[2], 5, substr($r['category'] ?? 'N/A', 0, 25), 1, 0, 'L');
        $pdf->Cell($widths[3], 5, date('d-m-Y', strtotime($r['date'])), 1, 0, 'C');
        $pdf->Cell($widths[4], 5, substr($r['description'] ?? '', 0, 30), 1, 0, 'L');
        $pdf->Cell($widths[5], 5, 'Rs. ' . number_format($r['amount'], 2), 1, 1, 'R');
    }
    
    // Grand Total Row
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell($widths[0] + $widths[1] + $widths[2] + $widths[3] + $widths[4], 5.5, 'TOTAL AMOUNT', 1, 0, 'R');
    $pdf->Cell($widths[5], 5.5, 'Rs. ' . number_format($total_sum, 2), 1, 1, 'R');
}

// Footer signatures
$pdf->SetAutoPageBreak(false);
$pdf->SetY(-20);
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(60, 4, 'Prepared By: __________________', 0, 0, 'L');
$pdf->Cell(60, 4, 'Verified By: __________________', 0, 0, 'C');
$pdf->Cell(0, 4, 'Authorized Signatory: __________________', 0, 1, 'R');

$pdf->SetY(-5);
$pdf->SetFont('Arial', 'I', 6.5);
$pdf->SetTextColor(148, 163, 184);
$pdf->Cell(0, 3, 'Generated on ' . date('d-m-Y H:i:s'), 0, 1, 'C');

$pdf->Output('', 'Dashboard_Expense_' . $period . '.pdf');
?>
