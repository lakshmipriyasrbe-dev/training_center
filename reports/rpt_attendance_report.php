<?php
require_once '../common_file.php';
require_once '../fpdf/fpdf.php';

if ($user_role != 'admin' && !$is_management) { exit('Unauthorized'); }

$report_type = $_GET['report_type'] ?? 'staff';
$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';
$staff_id = $_GET['staff_id'] ?? '';
$student_id = $_GET['student_id'] ?? '';

// Setup parameters and dates
if (empty($from_date)) $from_date = date('Y-m-d', strtotime('-30 days'));
if (empty($to_date)) $to_date = date('Y-m-d');

$attendance_data = [];

if ($report_type === 'staff') {
    $where = "a.deleted = 0 AND a.company_id = :comp_id";
    $params = [':comp_id' => $_SESSION['company_id']];

    if (!empty($staff_id)) {
        $where .= " AND a.staff_id = :staff_id";
        $params[':staff_id'] = $staff_id;
    }
    if (!empty($from_date)) {
        $where .= " AND a.attendance_date >= :from_date";
        $params[':from_date'] = $from_date;
    }
    if (!empty($to_date)) {
        $where .= " AND a.attendance_date <= :to_date";
        $params[':to_date'] = $to_date;
    }

    $query = "SELECT a.* 
              FROM {$GLOBALS['attendance_table']} a 
              WHERE $where 
              ORDER BY a.attendance_date DESC, a.staff_name ASC";
    $attendance_data = $bf->getQueryRecords($query, $params);
} else {
    $where = "sa.deleted = 0 AND sa.company_id = :comp_id";
    $params = [':comp_id' => $_SESSION['company_id']];

    if (!empty($student_id)) {
        $where .= " AND sa.student_id = :student_id";
        $params[':student_id'] = $student_id;
    }
    if (!empty($from_date)) {
        $where .= " AND sa.attendance_date >= :from_date";
        $params[':from_date'] = $from_date;
    }
    if (!empty($to_date)) {
        $where .= " AND sa.attendance_date <= :to_date";
        $params[':to_date'] = $to_date;
    }

    $query = "SELECT sa.* 
              FROM {$GLOBALS['student_attendance_table']} sa 
              WHERE $where 
              ORDER BY sa.attendance_date DESC, sa.id ASC";
    $attendance_data = $bf->getQueryRecords($query, $params);
}

// Calculate summary stats
$total_present = 0;
$total_absent = 0;
$total_marked = 0;
$unique_dates = [];

if (!empty($attendance_data)) {
    foreach ($attendance_data as $row) {
        $total_marked++;
        $unique_dates[$row['attendance_date']] = true;
        switch ($row['present_code']) {
            case 'PP':
                $total_present += 1;
                break;
            case 'AA':
                $total_absent += 1;
                break;
            case 'PA':
            case 'AP':
                $total_present += 0.5;
                $total_absent += 0.5;
                break;
        }
    }
}
$total_days = count($unique_dates);
$attendance_rate = $total_marked > 0 ? ($total_present / $total_marked) * 100 : 0;

// Initialize PDF
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->setTitle('Attendance Report', true);

$from_page = ucfirst($report_type) . ' Attendance Report';
$date_display = "From: " . date('d-m-Y', strtotime($from_date)) . "   To: " . date('d-m-Y', strtotime($to_date));

require_once 'rpt_header.php';
$pdf->setY($title_report_y);

// Summary Stats Panel
$pdf->SetDrawColor(226, 232, 240);
$pdf->SetFillColor(248, 250, 252);
$pdf->SetLineWidth(0.3);
$pdf->SetFont('Arial', 'B', 8.5);

$pdf->Cell(45, 10, 'Total Marked Days: ' . $total_days, 1, 0, 'C', true);
$pdf->Cell(45, 10, 'Total Present: ' . $total_present . ' Days', 1, 0, 'C', true);
$pdf->Cell(45, 10, 'Total Absent: ' . $total_absent . ' Days', 1, 0, 'C', true);
$pdf->Cell(0, 10, 'Present Rate: ' . number_format($attendance_rate, 1) . '%', 1, 1, 'C', true);
$pdf->Ln(4);

// Table Headers
$pdf->SetFont('Arial', 'B', 9);
if ($report_type === 'staff') {
    $widths = [10, 30, 40, 45, 40, 25];
    $headers = ['S.No', 'Date', 'Staff ID', 'Staff Name', 'Staff Role', 'Status'];
} else {
    $widths = [10, 28, 35, 45, 27, 25, 20];
    $headers = ['S.No', 'Date', 'Student ID', 'Student Name', 'Course Type', 'Trainer', 'Status'];
}

$pdf->SetFillColor(14, 165, 233);
$pdf->SetTextColor(255, 255, 255);
for ($i = 0; $i < count($headers); $i++) {
    $pdf->Cell($widths[$i], 8.5, $headers[$i], 1, 0, 'C', true);
}
$pdf->Ln();

$pdf->SetTextColor(30, 41, 59);
$pdf->SetFont('Arial', '', 8.5);
$sno = 1;

if (!empty($attendance_data)) {
    foreach ($attendance_data as $row) {
        $pdf->setFont('Arial', '', 8.5);
        $row_start_y = $pdf->getY();
        $pdf->setX(10);
        $pdf->cell($widths[0], 6.5, $sno++, 0, 0, 'C');
        
        $date_str = date('d-m-Y', strtotime($row['attendance_date']));
        $pdf->cell($widths[1], 6.5, $date_str, 0, 0, 'C');
        
        if ($report_type === 'staff') {
            $pdf->cell($widths[2], 6.5, $row['staff_number'], 0, 0, 'C');
            
            $pdf->setXY(10 + $widths[0] + $widths[1] + $widths[2], $row_start_y);
            $pdf->Multicell($widths[3], 6.5, $row['staff_name'], 0, 'L');
            $name_y = $pdf->getY();
            
            $pdf->setXY(10 + $widths[0] + $widths[1] + $widths[2] + $widths[3], $row_start_y);
            $pdf->Multicell($widths[4], 6.5, $row['staff_role'], 0, 'L');
            $role_y = $pdf->getY();
            
            $status_desc = '';
            switch($row['present_code']) {
                case 'PP': $status_desc = 'Present'; break;
                case 'AA': $status_desc = 'Absent'; break;
                case 'PA': $status_desc = 'FN - P | AN - A'; break;
                case 'AP': $status_desc = 'FN - A | AN - P'; break;
            }
            $pdf->setXY(10 + $widths[0] + $widths[1] + $widths[2] + $widths[3] + $widths[4], $row_start_y);
            $pdf->cell($widths[5], 6.5, $status_desc, 0, 1, 'C');
            $status_y = $pdf->getY();
            
            $max_y = max([$name_y, $role_y, $status_y]);
        } else {
            $student_name = 'N/A';
            $student_type = 'N/A';
            $student_dec_id = '';
            if (!empty($row['student_id'])) {
                $stu_rec = $bf->getTableRecords($GLOBALS['enrollment_table'], 'student_id', $row['student_id']);
                if (!empty($stu_rec)) {
                    $student_name = $stu_rec[0]['student_name'];
                    $student_type = 'Training';
                    $student_dec_id = $bf->encode_decode('decrypt', $stu_rec[0]['student_id']);
                } else {
                    $stu_rec = $bf->getTableRecords($GLOBALS['enrollment_internship_table'], 'student_id', $row['student_id']);
                    if (!empty($stu_rec)) {
                        $student_name = $stu_rec[0]['student_name'];
                        $student_type = 'Internship';
                        $student_dec_id = $bf->encode_decode('decrypt', $stu_rec[0]['student_id']);
                    }
                }
            }
            $trainer_name = 'N/A';
            if (!empty($row['staff_id'])) {
                $trainer_name = $bf->getTableColumnValue($GLOBALS['staff_table'], 'staff_id', $row['staff_id'], 'staff_name') ?? 'N/A';
            }
            
            $pdf->cell($widths[2], 6.5, $student_dec_id, 0, 0, 'C');
            
            $pdf->setXY(10 + $widths[0] + $widths[1] + $widths[2], $row_start_y);
            $pdf->Multicell($widths[3], 6.5, $student_name, 0, 'L');
            $name_y = $pdf->getY();
            
            $pdf->setXY(10 + $widths[0] + $widths[1] + $widths[2] + $widths[3], $row_start_y);
            $pdf->cell($widths[4], 6.5, $student_type, 0, 0, 'C');
            
            $pdf->setXY(10 + $widths[0] + $widths[1] + $widths[2] + $widths[3] + $widths[4], $row_start_y);
            $pdf->Multicell($widths[5], 6.5, $trainer_name, 0, 'L');
            $trainer_y = $pdf->getY();
            
            $status_desc = '';
            switch($row['present_code']) {
                case 'PP': $status_desc = 'Present'; break;
                case 'AA': $status_desc = 'Absent'; break;
                case 'PA': $status_desc = 'FN - P | AN - A'; break;
                case 'AP': $status_desc = 'FN - A | AN - P'; break;
            }
            $pdf->setXY(10 + $widths[0] + $widths[1] + $widths[2] + $widths[3] + $widths[4] + $widths[5], $row_start_y);
            $pdf->cell($widths[6], 6.5, $status_desc, 0, 1, 'C');
            $status_y = $pdf->getY();
            
            $max_y = max([$name_y, $trainer_y, $status_y]);
        }
        
        // Draw borders for columns to match custom grid style
        $pdf->setY($row_start_y);
        $pdf->setX(10);
        
        $cum_x = 10;
        for ($i = 0; $i < count($widths); $i++) {
            $pdf->Rect($cum_x, $row_start_y, $widths[$i], ($max_y - $row_start_y));
            $cum_x += $widths[$i];
        }
        
        $pdf->setY($max_y);
    }
} else {
    $pdf->cell(0, 10, 'No attendance records found', 1, 1, 'C');
}

// Draw Outer A4 page frame border
$pdf->setXY(10, 10);
$pdf->cell(190, 258, '', 1, 1, 'C');

$pdf->Ln(2);
$pdf->setX(10);
$pdf->setFont('Arial', 'I', 8);
$pdf->setTextColor(148, 163, 184);
$pdf->cell(160, 5, 'Report generated on ' . date('d-m-Y H:i:s'), 0, 0, 'L');
$pdf->setX(170);
$pdf->Cell(0, 5, 'Page ' . $pdf->PageNo() . ' / {nb}', 0, 1, 'C');

$pdf->output('', 'Attendance_Report_' . $report_type . '.pdf');
?>
