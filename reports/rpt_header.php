<?php

$pdf->SetTitle($from_page);

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

/* ---------- HEADER BORDER ---------- */

$pdf->Rect(10, 10, 190, 32);

/* ---------- LOGO ---------- */

$logo_path = "";
if (!empty($company['logo_image'])) {
    $logo_path = "../" . $company['logo_image'];
}
if (empty($logo_path) || !file_exists($logo_path)) {
    $logo_path = "../main/images/logo.png";
}

if(file_exists($logo_path)) {
    $pdf->Image($logo_path, 13, 13, 22);
}

/* ---------- COMPANY DETAILS ---------- */

$pdf->SetXY(45, 10);

if(!empty($company)) {

    // Company Name
    $pdf->SetFont('Arial', 'B', 22);
    $pdf->Cell(145, 10, strtoupper($company['company_name']), 0, 1, 'C');

    // Email + Mobile
    $email  = $company['company_email'] ?? '';
    $mobile = $company['company_mobile'] ?? '';

    $pdf->SetFont('Arial', '', 11);
    $pdf->SetX(45);
    $pdf->Cell(145, 6, "Email: $email | Mobile: $mobile", 0, 1, 'C');

    // Address
    if(!empty($company['company_address'])) {
        $pdf->SetX(45);
        $pdf->Cell(145, 6, $company['company_address'], 0, 1, 'C');
    }
}

/* ---------- REPORT TITLE ---------- */

// $pdf->Ln(8);

$pdf->SetFont('Arial', 'B', 11);

$pdf->Cell(190, 10, $from_page . ' ' . $date_display, 1, 1, 'C');

$title_report_y = $pdf->GetY();

?>