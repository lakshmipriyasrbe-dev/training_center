<?php
require_once 'main/basic_functions.php';
$bf = new Basic_Functions();

echo "\n=== Training Enrollments ===\n";
$enr = $bf->getQueryRecords("SELECT id, student_id, student_name, staff_id FROM tc_enrollment");
print_r($enr);

echo "\n=== Internship Enrollments ===\n";
$int_enr = $bf->getQueryRecords("SELECT id, student_id, student_name, staff_id FROM tc_enrollment_internship");
print_r($int_enr);
?>
