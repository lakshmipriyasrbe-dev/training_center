<?php
require_once '../common_file.php';
require_once '../fpdf/fpdf.php';

if ($user_role != 'admin' && !$is_management) { exit('Unauthorized'); }

$period = $_GET['period'] ?? 'today';
$type = $_GET['type'] ?? 'enrollment'; // enrollment or enquiry
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

// Fetch records
$records = [];
if ($type === 'enrollment') {
    $q1 = "SELECT student_id, enrollment_id as enroll_id, student_name as name, mobile_number as mobile, doj as date, course_id, 'Training' as source_type 
           FROM {$GLOBALS['enrollment_table']} 
           WHERE DATE(created_date_time) BETWEEN :start AND :end AND deleted = 0 AND company_id = :cid";
    $q2 = "SELECT student_id, enrollment_internship_id as enroll_id, student_name as name, mobile_number as mobile, doj as date, course_id, 'Internship' as source_type 
           FROM {$GLOBALS['enrollment_internship_table']} 
           WHERE DATE(created_date_time) BETWEEN :start AND :end AND deleted = 0 AND company_id = :cid";
    
    $records1 = $bf->getQueryRecords($q1, [':start' => $start_date, ':end' => $end_date, ':cid' => $comp_id]);
    $records2 = $bf->getQueryRecords($q2, [':start' => $start_date, ':end' => $end_date, ':cid' => $comp_id]);
    $records = array_merge($records1, $records2);
} else {
    $q = "SELECT enquiry_id as enroll_id, name, mobile_number as mobile, DATE(created_date_time) as date, course_id, 'Enquiry' as source_type 
          FROM {$GLOBALS['course_enquiry_table']} 
          WHERE DATE(created_date_time) BETWEEN :start AND :end AND deleted = 0 AND company_id = :cid";
    $records = $bf->getQueryRecords($q, [':start' => $start_date, ':end' => $end_date, ':cid' => $comp_id]);
}

$pdf = new FPDF('L', 'mm', 'A5');
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 5);
$pdf->AddPage();

$from_page = strtoupper($period) . ' ' . strtoupper($type) . ' REPORT (' . $date_desc . ')';
require_once 'rpt_header_a5.php';

$pdf->SetFont('Arial', 'B', 8);
$pdf->SetDrawColor(180, 180, 180);
$pdf->SetLineWidth(0.3);

if ($type === 'enrollment') {
    $widths = [10, 32, 50, 28, 45, 25];
    $headers = ['S.No', 'Student ID', 'Name', 'Mobile', 'Course', 'Type'];
} else {
    $widths = [10, 35, 55, 30, 60];
    $headers = ['S.No', 'Enquiry ID', 'Name', 'Mobile', 'Course'];
}

// Print headers
for ($i = 0; $i < count($headers); $i++) {
    $pdf->Cell($widths[$i], 5.5, $headers[$i], 1, 0, 'C');
}
$pdf->Ln();

$pdf->SetFont('Arial', '', 7.5);
$sno = 1;

if (empty($records)) {
    $pdf->Cell(array_sum($widths), 6, 'No records found for this period.', 1, 1, 'C');
} else {
    foreach ($records as $r) {
        $student_id_dec = isset($r['student_id']) ? $bf->encode_decode('decrypt', $r['student_id']) : '';
        $enroll_id_dec = isset($r['enroll_id']) ? $bf->encode_decode('decrypt', $r['enroll_id']) : $r['enroll_id'];
        
        $id_str = !empty($student_id_dec) ? $student_id_dec . ' (' . $enroll_id_dec . ')' : $enroll_id_dec;
        
        $course_name = $bf->getTableColumnValue($GLOBALS['course_table'], 'course_id', $r['course_id'], 'course_name') ?? 'N/A';
        
        $pdf->Cell($widths[0], 5, $sno++, 1, 0, 'C');
        $pdf->Cell($widths[1], 5, substr($id_str, 0, 20), 1, 0, 'L');
        $pdf->Cell($widths[2], 5, substr($r['name'] ?? '', 0, 25), 1, 0, 'L');
        $pdf->Cell($widths[3], 5, $r['mobile'] ?? '', 1, 0, 'C');
        $pdf->Cell($widths[4], 5, substr($course_name, 0, 30), 1, 0, 'L');
        if ($type === 'enrollment') {
            $pdf->Cell($widths[5], 5, $r['source_type'] ?? '', 1, 1, 'C');
        } else {
            $pdf->Ln();
        }
    }
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

$pdf->Output('', 'Dashboard_' . $type . '_' . $period . '.pdf');
?>
