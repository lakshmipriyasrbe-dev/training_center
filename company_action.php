<?php
require_once 'common_file.php';
if ($user_role != 'admin') { exit('Unauthorized'); }

$id = $bf->sanitize($_POST['id'] ?? '');
$company_name = $bf->sanitize($_POST['company_name'] ?? '');
$company_email = $bf->sanitize($_POST['company_email'] ?? '');
$company_mobile = $bf->sanitize($_POST['company_mobile'] ?? '');
$company_address = $bf->sanitize($_POST['company_address'] ?? '');

// Uniqueness Checks
$check_query = "SELECT id FROM " . $GLOBALS['company_table'] . " WHERE (company_name = :name OR company_email = :email OR company_mobile = :mobile) AND deleted = 0";
$check_params = [':name' => $company_name, ':email' => $company_email, ':mobile' => $company_mobile];

if (!empty($id)) {
    $check_query .= " AND id != :id";
    $check_params[':id'] = $id;
}

$existing = $bf->getQueryRecords($check_query, $check_params);
if (!empty($existing)) {
    exit("Error: Company Name, Email, or Mobile already exists in the system.");
}

$data = [
    'company_name' => $company_name,
    'company_email' => $company_email,
    'company_mobile' => $company_mobile,
    'company_address' => $company_address,
    'updated_date_time' => date('Y-m-d H:i:s')
];

if (empty($id)) {
    $data['created_date_time'] = date('Y-m-d H:i:s');
    $bf->InsertSQL($GLOBALS['company_table'], $data, 'company_id', '', 'ADD COMPANY');
} else {
    $bf->UpdateSQL($GLOBALS['company_table'], $data, "id = :id", [':id' => $id]);
}

echo "Success";
?>
