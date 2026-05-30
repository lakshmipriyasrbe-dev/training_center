<?php
$pdf->setXY(10, 10);
$pdf->SetFont('Arial', 'B', 14);
$pdf->setTitle($from_page);

$company = $bf->getTableRecords($GLOBALS['company_table'], 'deleted', 0)[0] ?? [];

$header_start_y = $pdf->getY();

if(!empty($company)) {

    // Company Name
    if(!empty($company['company_name'])) {
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, strtoupper($company['company_name']), 0, 1, 'C');
    }

    // Email + Mobile
    $email  = $company['company_email'] ?? 'N/A';
    $mobile = $company['company_mobile'] ?? 'N/A';

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 5, "Email: $email | Mobile: $mobile", 0, 1, 'C');

    // Address
    if(!empty($company['company_address'])) {
        $pdf->Cell(0, 5, $company['company_address'], 0, 1, 'C');
    }
}

$header_end_y = $pdf->getY();
// Draw line below header
$pdf->setY($header_start_y);
$pdf->cell(0, $header_end_y - $header_start_y, '', '1', 1, 'C');
$pdf->setFont('Arial', 'B', 10);
$pdf->cell(0, 10, $from_page . ' ' . $date_display, 1, 1, 'C');
$title_report_y =  $pdf->getY();
?>