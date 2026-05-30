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

/* ---------- PAGE BORDER (A5 Landscape: 210 x 148 mm) ---------- */
// Modern Sky Blue Border (matches WeGrow branding --primary: #0ea5e9)
$pdf->SetDrawColor(14, 165, 233);
$pdf->SetLineWidth(0.8);
$pdf->Rect(10, 10, 190, 128);

// Subtle Top Accent Fill
$pdf->SetFillColor(14, 165, 233);
$pdf->Rect(10, 10, 190, 2.5, 'F');

/* ---------- LOGO (First Left Corner) ---------- */
$logo_path = "";
if (!empty($company['logo_image'])) {
    $logo_path = "../" . $company['logo_image'];
}
if (empty($logo_path) || !file_exists($logo_path)) {
    $logo_path = "../main/images/logo.png";
}
if(file_exists($logo_path)) {
    $pdf->Image($logo_path, 14, 14, 20);
}

/* ---------- COMPANY DETAILS (Right Side) ---------- */
$pdf->SetY(13);
if(!empty($company)) {
    // Company Name in premium Bold Sky Blue
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->SetTextColor(14, 165, 233);
    $pdf->SetX(10);
    $pdf->Cell(188, 6, strtoupper($company['company_name']), 0, 1, 'R');

    // Email + Mobile in elegant Slate Grey
    $email  = $company['company_email'] ?? '';
    $mobile = $company['company_mobile'] ?? '';
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetTextColor(100, 116, 139);
    $pdf->SetX(10);
    $pdf->Cell(188, 4.5, "Email: $email | Mobile: $mobile", 0, 1, 'R');

    // Address
    if(!empty($company['company_address'])) {
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor(100, 116, 139);
        $pdf->SetX(10);
        $pdf->Cell(188, 4, $company['company_address'], 0, 1, 'R');
    }
}
$pdf->Ln(2);

// Title divider line - clean and subtle
$pdf->SetDrawColor(226, 232, 240);
$pdf->SetLineWidth(0.3);
$pdf->Line(12, $pdf->GetY(), 198, $pdf->GetY());
$pdf->Ln(1.5);

// Solid filled block with blue text for the Document Title
$pdf->SetFillColor(240, 249, 255);
$pdf->SetTextColor(14, 165, 233);
$pdf->SetDrawColor(224, 242, 254);
$pdf->SetLineWidth(0.3);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6.5, $from_page, 1, 1, 'C', true);

$pdf->Ln(3.5);
?>
