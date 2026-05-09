<?php
header('Content-Type: application/json');
require_once 'common_file.php';

$response = ['status' => 'error', 'message' => '', 'errors' => []];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $username = $bf->sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? ''; // Don't sanitize password yet as it might contain special chars for encryption

    if (empty($username)) {
        $response['errors']['username'] = "Enter the Username";
    }
    if (empty($password)) {
        $response['errors']['password'] = "Enter the Password";
    }

    if (empty($response['errors'])) {
        // Fetch user using parameterized query
        $query = "SELECT * FROM " . $GLOBALS['user_table'] . " WHERE username = :username AND deleted = :deleted";
        $params = [':username' => $username, ':deleted' => 0];
        $users = $bf->getQueryRecords($query, $params);

        if(empty($users)) {
            // Try fetching with email as well
            $query_email = "SELECT * FROM " . $GLOBALS['staff_table'] . " WHERE username = :username AND deleted = :deleted";
            $params_email = [':username' => $username, ':deleted' => 0];
            $users = $bf->getQueryRecords($query_email, $params_email);
        }

        if (empty($users)) {
            $response['errors']['username'] = "Incorrect username";
        } else {
            $user = $users[0];
            // Check password using encryption method from SmartClinic
            $encrypted_input = $bf->encode_decode('encrypt', $password);
            
            // For demo purposes, we check if it matches. 
            // In a real scenario, we'd hash or use the encrypted value.
            if ($user['password'] != $encrypted_input && $user['password'] != $password) {
                $response['errors']['password'] = "Password mismatch";
            } else {
                // Success
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                if(isset($user['user_id'])) {
                    $_SESSION['user_id'] = $user['user_id']; // Normalize ID field
                } elseif(isset($user['staff_id'])) {
                    $_SESSION['user_id'] = $user['staff_id']; // Normalize ID field for staff
                }
                $_SESSION['username'] = $user['username'];
                if(isset($user['role'])) {
                    $_SESSION['user_role'] = $user['role'];
                } else {
                    $_SESSION['user_role'] = 'staff'; // Default role if not set
                }
                if(isset($user['name'])) {
                    $_SESSION['full_name'] = $user['name'];
                } else {
                    $_SESSION['full_name'] = $user['staff_name']; // Fallback to username if name not set
                }

                $bf->add_log($GLOBALS['user_table'], $user['id'], 'USER LOGIN SUCCESS', 'LOGIN');

                // Record login in DB
                $login_id = $bf->InsertSQL($GLOBALS['login_table'], [
                    'user_id' => $user['id'],
                    'login_date_time' => date('Y-m-d H:i:s')
                ], '', '', 'LOGIN_RECORD');
                $_SESSION['login_id'] = $login_id;

                $response['status'] = 'success';
                $response['message'] = 'Login successful!';
            }
        }
    }
} else {
    $response['message'] = 'Invalid request';
}

echo json_encode($response);
?>
