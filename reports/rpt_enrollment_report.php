<?php
require_once '../common_file.php';
require_once '../fpdf/fpdf.php';

if ($user_role != 'admin' && !$is_management) { exit('Unauthorized'); }

$course_type = ''; $grand_total_due = 0; $grand_total_paid = 0; $grand_total_fees = 0;
if(isset($_GET['course_type']) && !empty($_GET['course_type'])) {
    $course_type = $bf->sanitize($_GET['course_type']);
}

$course_id = '';
if(isset($_GET['course_id']) && !empty($_GET['course_id'])) {
    $course_id = $bf->sanitize($_GET['course_id']);
}

$from_date = '';
if(isset($_GET['from_date']) && !empty($_GET['from_date'])) {
    $from_date = $bf->sanitize($_GET['from_date']);
}

$to_date = '';
if(isset($_GET['to_date']) && !empty($_GET['to_date'])) {
    $to_date = $bf->sanitize($_GET['to_date']);
} 
$enrollments = $bf->getEnrollmentReportList($course_type, $course_id, $from_date, $to_date);

require_once '../fpdf/fpdf.php';
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->setTitle('Enrollment Report', true);
$from_page = 'Enrollment Report';
$date_display = "";
if(!empty($from_date) && !empty($to_date)) {
    $date_display = "From: " . date('d-m-Y', strtotime($from_date)) . " To: " . date('d-m-Y', strtotime($to_date));
} elseif(!empty($from_date)) {
    $date_display = "From: " . date('d-m-Y', strtotime($from_date));
} elseif(!empty($to_date)) {
    $date_display = "To: " . date('d-m-Y', strtotime($to_date));
}
require_once 'rpt_header.php';
$pdf->setY($title_report_y);
$pdf->cell(10, 10, 'S.No', 1, 0, 'C');
$pdf->cell(50, 10, 'Student Details', 1, 0, 'C');
$pdf->cell(30, 10, 'DOJ', 1, 0, 'C');
$pdf->cell(30, 10, 'Course Joined', 1, 0, 'C'); 
$pdf->cell(0, 10, 'Fees Details', 1, 1, 'C');  

$sno = 0; $student_details = "";$student_id = ""; $course_name = ""; $doj = "";
if(!empty($enrollments)) {
    foreach ($enrollments as $enrollment) {
        $sno++;
        $course_name = $bf->getTableColumnValue($GLOBALS['course_table'], 'course_id', $enrollment['course_id'], 'course_name') ?? 'N/A';
        $pdf->setFont('Arial', '', 9);
        $row_start_y = $pdf->getY();
        $pdf->setX(10);
        $pdf->cell(10, 6, $sno, 0, 0, 'C');
        $student_id = $bf->encode_decode("decrypt", $enrollment['student_id']) ?? 'N/A';
        $enroll_id = $bf->encode_decode("decrypt", $enrollment['enroll_id']) ?? 'N/A';
        $student_details = $student_id . " (" . $enroll_id . ") - " . $enrollment['student_name'] . "\n" . $enrollment['mobile_number'];
        $pdf->Multicell(50, 4, $student_details, 0, 'C');
        $id_y = $pdf->getY();
        $pdf->setXY(70, $row_start_y);
        $pdf->Multicell(30, 4, date('d-m-Y', strtotime($enrollment['enrollment_date'])), 0, 'C');
        $name_y = $pdf->getY();
        $pdf->setXY(100, $row_start_y); 
        $pdf->Multicell(30, 6, $course_name, 0, 'C');
        $date_y = $pdf->getY();
        $pdf->setXY(130, $row_start_y);  
        $fees_details =
            "Fee: " . ($enrollment['fees_amount'] ?? '0') .
            " | Paid: " . ($enrollment['paid_amount'] ?? '0') .
            " | Due: " . ($enrollment['balance_amount'] ?? '0');

            $grand_total_fees += $enrollment['fees_amount'] ?? 0;
            $grand_total_paid += $enrollment['paid_amount'] ?? 0;   
            $grand_total_due += $enrollment['balance_amount'] ?? 0;
        $pdf->Multicell(0, 6, $fees_details, 0, 'L');
        $course_y = $pdf->getY();

        $max_y = max([$id_y, $name_y, $date_y, $course_y]); 
        $pdf->setY($row_start_y);
        $pdf->setX(10);
        $pdf->cell(10, ($max_y - $row_start_y), '', '1', 0, 'C');
        $pdf->cell(50, ($max_y - $row_start_y), '', '1', 0, 'C');
        $pdf->cell(30, ($max_y - $row_start_y), '', '1', 0, 'C');
        $pdf->cell(30, ($max_y - $row_start_y), '', '1', 0, 'C');
        $pdf->cell(0, ($max_y - $row_start_y), '', '1', 1, 'C');
            
    }
    $row_end_y = $pdf->getY();
    if(!empty($enrollments)) {
        $pdf->setFont('Arial', 'B', 10); 
        $pdf->cell(120, 10, 'Grand Total', 0, 0, 'C');
        $grand_y = $pdf->getY();
        $pdf->setXY(130, $row_end_y);
        $fees_summary = "Fee: " . $grand_total_fees . " | Paid: " . $grand_total_paid . " | Due: " . $grand_total_due;
        $pdf->Multicell(0, 10, $fees_summary, 0, 'C');
        $grand_display_y = $pdf->getY();
        $max_y = max(array($grand_y, $grand_display_y));
        $pdf->setXY(10, $row_end_y);
        $pdf->cell(120, ($max_y - $row_end_y), '', '1', 0, 'C');
        $pdf->cell(0, ($max_y - $row_end_y), '', '1', 1, 'C');
    }
} else {
    $pdf->cell(0, 10, 'No records found', 1, 1, 'C');
}   
$pdf->setXY(10, 10);
$pdf->cell(190, 258, '', 1, 1, 'C');
$pdf->Ln(2);
$pdf->setX(10);
$pdf->setFont('Arial', 'I', 8);
$pdf->setTextColor(148, 163, 184);
$pdf->cell(160, 5, 'Report generated on ' . date('d-m-Y H:i:s'), 0, 0, 'L');
$pdf->setX(170);
$pdf->Cell(0, 5, 'Page ' . $pdf->PageNo() . ' / {nb}', 0, 1, 'C');
$pdf->output();
?>