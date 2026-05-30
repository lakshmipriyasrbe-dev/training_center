<?php
require_once '../common_file.php';
require_once '../fpdf/fpdf.php';

if ($user_role != 'admin' && !$is_management) { exit('Unauthorized'); }

$bill_type = '';
if(isset($_GET['bill_type']) && !empty($_GET['bill_type'])) {
    $bill_type = $bf->sanitize($_GET['bill_type']);
}

$course_type = '';
if(isset($_GET['course_type']) && !empty($_GET['course_type'])) {
    $course_type = $bf->sanitize($_GET['course_type']);
}

$student_id = '';
if(isset($_GET['student_id']) && !empty($_GET['student_id'])) {
    $student_id = $bf->sanitize($_GET['student_id']);
}

$expense_category_id = '';
if(isset($_GET['expense_category_id']) && !empty($_GET['expense_category_id'])) {
    $expense_category_id = $bf->sanitize($_GET['expense_category_id']);
}

$from_date = '';
if(isset($_GET['from_date']) && !empty($_GET['from_date'])) {
    $from_date = $bf->sanitize($_GET['from_date']);
}

$to_date = '';
if(isset($_GET['to_date']) && !empty($_GET['to_date'])) {
    $to_date = $bf->sanitize($_GET['to_date']);
}

// Fetch combined report data
$payments = $bf->getPaymentReportList($course_type, $student_id, $from_date, $to_date, $bill_type, $expense_category_id);

$pdf = new FPDF('L', 'mm', 'A4'); // Landscape for more columns
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->setTitle('Payments Report', true);
$from_page = 'Payments Report';

// Build filter display text
$filter_parts = [];
if (!empty($bill_type)) $filter_parts[] = "Type: " . $bill_type;
if (!empty($course_type)) $filter_parts[] = "Course: " . ucfirst($course_type);

$date_display = "";
if(!empty($from_date) && !empty($to_date)) {
    $date_display = "From: " . date('d-m-Y', strtotime($from_date)) . "   To: " . date('d-m-Y', strtotime($to_date));
} elseif(!empty($from_date)) {
    $date_display = "From: " . date('d-m-Y', strtotime($from_date));
} elseif(!empty($to_date)) {
    $date_display = "To: " . date('d-m-Y', strtotime($to_date));
}

// ---- HEADER ----
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

$pdf->Rect(10, 10, 277, 32);

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

$pdf->SetXY(45, 10);
if(!empty($company)) {
    $pdf->SetFont('Arial', 'B', 22);
    $pdf->Cell(232, 10, strtoupper($company['company_name']), 0, 1, 'C');

    $email  = $company['company_email'] ?? '';
    $mobile = $company['company_mobile'] ?? '';
    $pdf->SetFont('Arial', '', 11);
    $pdf->SetX(45);
    $pdf->Cell(232, 6, "Email: $email | Mobile: $mobile", 0, 1, 'C');

    if(!empty($company['company_address'])) {
        $pdf->SetX(45);
        $pdf->Cell(232, 6, $company['company_address'], 0, 1, 'C');
    }
}

$pdf->SetFont('Arial', 'B', 11);
$title_text = $from_page . ' ' . $date_display;
if (!empty($filter_parts)) {
    $title_text .= '  |  ' . implode(', ', $filter_parts);
}
$pdf->Cell(277, 10, $title_text, 1, 1, 'C');
$title_report_y = $pdf->GetY();

// ---- TABLE HEADER ----
$pdf->setY($title_report_y);
$pdf->SetFont('Arial', 'B', 9);
$pdf->cell(12, 8, 'S.No', 1, 0, 'C');
$pdf->cell(28, 8, 'Bill ID', 1, 0, 'C');
$pdf->cell(22, 8, 'Date', 1, 0, 'C');
$pdf->cell(22, 8, 'Type', 1, 0, 'C');
$pdf->cell(90, 8, 'Bill Details', 1, 0, 'C');
$pdf->cell(35, 8, 'Credit', 1, 0, 'C');
$pdf->cell(35, 8, 'Debit', 1, 0, 'C');
$pdf->cell(0, 8, 'Balance', 1, 1, 'C');

$sno = 0;
$total_credit = 0;
$total_debit = 0;

if(!empty($payments)) {
    foreach ($payments as $p) {
        $sno++;
        $total_credit += $p['credit'];
        $total_debit += $p['debit'];

        // Check page break
        if ($pdf->getY() > 180) {
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->cell(12, 8, 'S.No', 1, 0, 'C');
            $pdf->cell(28, 8, 'Bill ID', 1, 0, 'C');
            $pdf->cell(22, 8, 'Date', 1, 0, 'C');
            $pdf->cell(22, 8, 'Type', 1, 0, 'C');
            $pdf->cell(90, 8, 'Bill Details', 1, 0, 'C');
            $pdf->cell(35, 8, 'Credit', 1, 0, 'C');
            $pdf->cell(35, 8, 'Debit', 1, 0, 'C');
            $pdf->cell(0, 8, 'Balance', 1, 1, 'C');
        }
        
        $pdf->SetFont('Arial', '', 8);
        $row_start_y = $pdf->getY();
        $pdf->setX(10);
        
        // S.No
        $pdf->cell(12, 6, $sno, 0, 0, 'C');
        
        // Bill ID
        $pdf->Multicell(28, 6, $p['bill_id'], 0, 'C');
        $id_y = $pdf->getY();
        
        // Date
        $pdf->setXY(50, $row_start_y);
        $pdf->Multicell(22, 6, date('d-m-Y', strtotime($p['bill_date'])), 0, 'C');
        $date_y = $pdf->getY();
        
        // Type
        $pdf->setXY(72, $row_start_y);
        $pdf->Multicell(22, 6, $p['bill_type'], 0, 'C');
        $type_y = $pdf->getY();
        
        // Bill Details
        $pdf->setXY(94, $row_start_y);
        $details_text = $p['bill_details'];
        if (strlen($details_text) > 60) {
            $details_text = substr($details_text, 0, 57) . '...';
        }
        $pdf->Multicell(90, 6, $details_text, 0, 'L');
        $details_y = $pdf->getY();
        
        // Credit
        $pdf->setXY(184, $row_start_y);
        $credit_text = $p['credit'] > 0 ? number_format($p['credit'], 2) : '-';
        $pdf->Multicell(35, 6, $credit_text, 0, 'R');
        $credit_y = $pdf->getY();
        
        // Debit
        $pdf->setXY(219, $row_start_y);
        $debit_text = $p['debit'] > 0 ? number_format($p['debit'], 2) : '-';
        $pdf->Multicell(35, 6, $debit_text, 0, 'R');
        $debit_y = $pdf->getY();
        
        // Balance
        $balance_text = number_format(abs($p['balance']), 2) . ($p['balance'] >= 0 ? ' Cr' : ' Dr');
        $pdf->setXY(254, $row_start_y);
        $pdf->Multicell(0, 6, $balance_text, 0, 'R');
        $balance_y = $pdf->getY();
        
        $max_y = max([$id_y, $date_y, $type_y, $details_y, $credit_y, $debit_y, $balance_y]);
        $row_height = $max_y - $row_start_y;
        
        // Draw cell borders
        $pdf->setY($row_start_y);
        $pdf->setX(10);
        $pdf->cell(12, $row_height, '', 1, 0, 'C');
        $pdf->cell(28, $row_height, '', 1, 0, 'C');
        $pdf->cell(22, $row_height, '', 1, 0, 'C');
        $pdf->cell(22, $row_height, '', 1, 0, 'C');
        $pdf->cell(90, $row_height, '', 1, 0, 'C');
        $pdf->cell(35, $row_height, '', 1, 0, 'C');
        $pdf->cell(35, $row_height, '', 1, 0, 'C');
        $pdf->cell(0, $row_height, '', 1, 1, 'C');
    }
    
    // Grand Total row
    $net_balance = $total_credit - $total_debit;
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->setX(10);
    $pdf->cell(174, 8, 'Grand Total', 1, 0, 'R');
    $pdf->cell(35, 8, number_format($total_credit, 2), 1, 0, 'R');
    $pdf->cell(35, 8, number_format($total_debit, 2), 1, 0, 'R');
    $pdf->cell(0, 8, number_format(abs($net_balance), 2) . ($net_balance >= 0 ? ' Cr' : ' Dr'), 1, 1, 'R');
    
} else {
    $pdf->SetFont('Arial', '', 9);
    $pdf->cell(0, 10, 'No records found', 1, 1, 'C');
}

// Footer
$pdf->Ln(3);
$pdf->setX(10);
$pdf->SetFont('Arial', 'I', 8);
$pdf->SetTextColor(148, 163, 184);
$pdf->cell(237, 5, 'Report generated on ' . date('d-m-Y H:i:s'), 0, 0, 'L');
$pdf->cell(0, 5, 'Page ' . $pdf->PageNo() . ' / {nb}', 0, 1, 'R');

$pdf->Output('I', 'Payments_Report.pdf');
?>
