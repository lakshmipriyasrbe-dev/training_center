<?php
require_once '../common_file.php';
require_once '../fpdf/fpdf.php';

if ($user_role != 'admin' && !$is_management) { exit('Unauthorized'); }

$from_date = '';
if(isset($_GET['from_date']) && !empty($_GET['from_date'])) {
    $from_date = $bf->sanitize($_GET['from_date']);
}

$to_date = '';
if(isset($_GET['to_date']) && !empty($_GET['to_date'])) {
    $to_date = $bf->sanitize($_GET['to_date']);
}

$placements = $bf->getPlacementReportList($from_date, $to_date);

$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->setTitle('Placement Report', true);
$from_page = 'Placement Report';

$date_display = "";
if(!empty($from_date) && !empty($to_date)) {
    $date_display = "From: " . date('d-m-Y', strtotime($from_date)) . "   To: " . date('d-m-Y', strtotime($to_date));
} elseif(!empty($from_date)) {
    $date_display = "From: " . date('d-m-Y', strtotime($from_date));
} elseif(!empty($to_date)) {
    $date_display = "To: " . date('d-m-Y', strtotime($to_date));
}

require_once 'rpt_header.php';
$pdf->setY($title_report_y);
$pdf->cell(10, 10, 'S.No', 1, 0, 'C');
$pdf->cell(25, 10, 'Student ID', 1, 0, 'C');
$pdf->cell(40, 10, 'Student Name', 1, 0, 'C');
$pdf->cell(35, 10, 'Course Finished', 1, 0, 'C');
$pdf->cell(40, 10, 'Company Details', 1, 0, 'C');
$pdf->cell(20, 10, 'CTC', 1, 0, 'C');
$pdf->cell(20, 10, 'Date', 1, 1, 'C');

$sno = 0;

if(!empty($placements)) {
    foreach ($placements as $p) {
        $sno++;
        
        $student_dec_id = $bf->encode_decode('decrypt', $p['student_id']);
        
        $course_name = 'N/A';
        $table = $p['course_type'] === 'internship' ? $GLOBALS['enrollment_internship_table'] : $GLOBALS['enrollment_table'];
        $stu_rec = [];
        $stmt = $bf->con->prepare("SELECT * FROM $table WHERE student_id = :student_id AND deleted = 0");
        $stmt->execute([':student_id' => $p['student_id']]);
        $stu_rec = $stmt->fetchAll();
        if(!empty($stu_rec)) {
            $c_rec = $bf->getTableRecords($GLOBALS['course_table'], 'course_id', $stu_rec[0]['course_id']);
            if(!empty($c_rec)) $course_name = $c_rec[0]['course_name'];
        }
        
        $company_details = $p['company_name'] . "\n(" . $p['designation'] . ")";
        
        $pdf->setFont('Arial', '', 9);
        $row_start_y = $pdf->getY();
        $pdf->setX(10);
        $pdf->cell(10, 6, $sno, 0, 0, 'C');
        
        $pdf->Multicell(25, 6, $student_dec_id, 0, 'C');
        $id_y = $pdf->getY();
        
        $pdf->setXY(45, $row_start_y); 
        $pdf->Multicell(40, 6, $p['student_name'], 0, 'L');
        $name_y = $pdf->getY();
        
        $pdf->setXY(85, $row_start_y); 
        $pdf->Multicell(35, 6, $course_name, 0, 'L');
        $course_y = $pdf->getY();
        
        $pdf->setXY(120, $row_start_y); 
        $pdf->Multicell(40, 5, $company_details, 0, 'L');
        $company_y = $pdf->getY();
        
        $pdf->setXY(160, $row_start_y); 
        $pdf->Multicell(20, 6, $p['ctc'], 0, 'C');
        $ctc_y = $pdf->getY();
        
        $pdf->setXY(180, $row_start_y); 
        $pdf->Multicell(20, 6, date('d-m-y', strtotime($p['closure_date'])), 0, 'C');
        $date_y = $pdf->getY();
        
        $max_y = max([$id_y, $name_y, $course_y, $company_y, $ctc_y, $date_y]); 
        
        $pdf->setY($row_start_y);
        $pdf->setX(10);
        $pdf->cell(10, ($max_y - $row_start_y), '', '1', 0, 'C');
        $pdf->cell(25, ($max_y - $row_start_y), '', '1', 0, 'C');
        $pdf->cell(40, ($max_y - $row_start_y), '', '1', 0, 'C');
        $pdf->cell(35, ($max_y - $row_start_y), '', '1', 0, 'C');
        $pdf->cell(40, ($max_y - $row_start_y), '', '1', 0, 'C');
        $pdf->cell(20, ($max_y - $row_start_y), '', '1', 0, 'C');
        $pdf->cell(20, ($max_y - $row_start_y), '', '1', 1, 'C');
    }
} else {
    $pdf->cell(0, 10, 'No placement records found', 1, 1, 'C');
}

$pdf->setXY(10, 10);
$pdf->cell(190, 258, '', 1, 1, 'C');
$pdf->Ln(2);
$pdf->setX(10);
$pdf->setFont('Arial', 'I', 8);
$pdf->setTextColor(148, 163, 184);
$pdf->cell(160, 5, 'Report generated on ' . date('d-m-Y H:i:s'), 0, 0, 'L');
$pdf->cell(0, 5, 'Page ' . $pdf->PageNo() . ' / {nb}', 0, 1, 'R');

$pdf->Output('I', 'Placement_Report.pdf');
?>
