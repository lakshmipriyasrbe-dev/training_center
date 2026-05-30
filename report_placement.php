<?php require_once 'common_file.php'; 
$from_page = 'Placement Report';

$start = ""; $limit = ""; $from_date = ""; $to_date = "";
$from_date = date('Y-m-d', strtotime('-30 days'));
$to_date = date('Y-m-d'); 

if(isset($_POST['from_date']) && !empty($_POST['from_date'])) {
    $from_date = $bf->sanitize($_POST['from_date']);
}

if(isset($_POST['to_date']) && !empty($_POST['to_date'])) {
    $to_date = $bf->sanitize($_POST['to_date']);
} 

$placements = $bf->getPlacementReportList($from_date, $to_date);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Placement Report - <?php echo get_company_name(); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content update_content">
        <div class="header">
            <h2><?php if(!empty($from_page))  echo ucfirst($from_page); ?></h2>
        </div>

        <div class="module-section">
            <div id="course_list">
                <form name="placement_report_form" id="placement_report_form" method="post">
                    <div class="form-row">
                        <div class="form-group col-3">
                            <label>From Date </label>
                            <input
                                type="date"
                                name="from_date"
                                id="from_date"
                                class="form-input"
                                value="<?php echo $from_date; ?>"
                                max="<?php echo date('Y-m-d'); ?>" onchange="getReport()"
                            >
                        </div>
                        <div class="form-group col-3">
                            <label>To Date </label>
                            <input
                                type="date"
                                name="to_date"
                                id="to_date"
                                class="form-input"
                                value="<?php echo $to_date; ?>"
                                max="<?php echo date('Y-m-d'); ?>" onchange="getReport()"
                            >
                        </div>
                    </div>
                </form>
                <div class="report-actions">
                    <button type="button" class="btn-report btn-print"
                        onclick="window.open('reports/rpt_placement_report.php?from_date=<?php echo $from_date; ?>&to_date=<?php echo $to_date; ?>', '_blank')">
                        <i class="fas fa-print"></i> Print Report
                    </button>
                    <button type="button" class="btn-report btn-excel"
                        onclick="getExcelReport();">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                </div>

                <table class="responsive table table-bordered" id="placement_report_table">
                    <thead>
                        <tr>
                            <th>Sno</th>                            
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Course Finished</th>
                            <th>Company Placed</th>
                            <th>CTC</th>
                            <th>Placement Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $sno = 1;
                            if(!empty($placements)) {
                                foreach ($placements as $p) { 
                                    $student_dec_id = $bf->encode_decode('decrypt', $p['student_id']);
                                    
                                    // Get Course Name
                                    $table = $p['course_type'] === 'internship' ? $GLOBALS['enrollment_internship_table'] : $GLOBALS['enrollment_table'];
                                    $stu_rec = $bf->getTableRecords($table, 'student_id', $p['student_id']);
                                    $course_name = 'N/A';
                                    if(!empty($stu_rec)) {
                                        $c_rec = $bf->getTableRecords($GLOBALS['course_table'], 'course_id', $stu_rec[0]['course_id']);
                                        if(!empty($c_rec)) $course_name = $c_rec[0]['course_name'];
                                    }
                                ?>
                                    <tr>
                                        <td><?php echo $sno++; ?></td>
                                        <td>
                                            <span style="color: var(--primary); font-weight: 600;">
                                                <?php echo $student_dec_id; ?>
                                            </span>
                                        </td>
                                        <td><?php echo $p['student_name']; ?></td>
                                        <td><?php echo $course_name; ?></td>
                                        <td>
                                            <strong><?php echo $p['company_name']; ?></strong><br>
                                            <span style="font-size: 0.85rem; color: #64748b;"><?php echo $p['designation']; ?></span>
                                        </td>
                                        <td><strong>₹<?php echo $p['ctc']; ?></strong></td>
                                        <td><?php echo date('d M Y', strtotime($p['closure_date'])); ?></td>
                                    </tr>
                                <?php } 
                            } else { ?>
                                <tr>
                                    <td colspan="7" style="text-align:center; padding: 2rem; color: var(--text-muted);">No placement records found.</td>
                                </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script src="main/js/script.js"></script>
    <script src="main/js/xlsx.full.min.js" type="text/javascript"></script>
    <script>
        function getReport() {
            if($('#placement_report_form').length > 0) {
                $('#placement_report_form').submit();
            }
        }

        function getExcelReport() {
            let table = document.getElementById("placement_report_table");
            let workbook = XLSX.utils.table_to_book(table, {
                sheet: "Placement Report"
            });
            XLSX.writeFile(workbook, "Placement_Report.xlsx");
        }
    </script>
</body>
</html>
