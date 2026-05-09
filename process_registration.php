<?php
header('Content-Type: application/json');
require_once 'common_file.php';

if (isset($_GET['check_first'])) {
    $users = $bf->getQueryRecords("SELECT COUNT(*) as total FROM " . $GLOBALS['user_table'] . " WHERE deleted = 0");
    echo json_encode(['is_first' => ($users[0]['total'] == 0)]);
    exit();
}

if (isset($_GET['get_roles'])) {
    $roles = $bf->getTableRecords($GLOBALS['role_table'], 'deleted', 0);
    echo json_encode($roles);
    exit();
}

$response = ['status' => 'error', 'message' => '', 'errors' => []];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $bf->sanitize($_POST['name'] ?? '');
    $mobile = $bf->sanitize($_POST['mobile'] ?? '');
    $username = $bf->sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation
    $errors = [];
    $err = $valid->valid_name($name, "Full Name");
    if ($err) $errors['name'] = $err;

    $err = $valid->valid_mobile($mobile, "Mobile Number");
    if ($err) $errors['mobile'] = $err;

    if (empty($username)) {
        $errors['username'] = "Enter the Username";
    } else {
        $stmt = $bf->con->prepare("SELECT id FROM " . $GLOBALS['user_table'] . " WHERE (username = :username OR mobile = :mobile) AND deleted = 0");
        $stmt->execute([':username' => $username, ':mobile' => $mobile]);
        if ($stmt->fetch()) {
            $errors['username'] = "Username or Mobile already exists";
        }
    }

    $err = $valid->valid_password($password, "Password");
    if ($err) $errors['password'] = $err;

    if (empty($errors)) {
        // Only the first user can register via this page
        $users_count = $bf->getQueryRecords("SELECT COUNT(*) as total FROM " . $GLOBALS['user_table'] . " WHERE deleted = 0");
        if ($users_count[0]['total'] > 0) {
            $response['message'] = "System already initialized. Please contact Admin to create users.";
            echo json_encode($response);
            exit();
        }
        $role = 'admin';

        $data = [
            'name' => $name,
            'mobile' => $mobile,
            'username' => $username,
            'password' => $bf->encode_decode('encrypt', $password),
            'role' => $role,
            'created_date_time' => date('Y-m-d H:i:s'),
            'updated_date_time' => date('Y-m-d H:i:s')
        ];

        $insert_id = $bf->InsertSQL($GLOBALS['user_table'], $data, 'user_id', 'unique_number', 'REGISTER');

        if (is_numeric($insert_id)) {
            $response['status'] = 'success';
            $response['message'] = 'Registration successful! You can now login.';
        } else {
            $response['message'] = $insert_id;
        }
    } else {
        $response['errors'] = $errors;
    }
}

echo json_encode($response);
?>
