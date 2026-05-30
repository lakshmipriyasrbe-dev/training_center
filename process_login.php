<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
        $found_user = null;
        $role_type = ''; // 'admin', 'staff', 'student'

        // 1. Try finding in tc_users (Admin)
        $query_admin = "SELECT * FROM " . $GLOBALS['user_table'] . " WHERE username = :username AND deleted = 0";
        $records = $bf->getQueryRecords($query_admin, [':username' => $username]);
        if (!empty($records)) {
            $found_user = $records[0];
            $role_type = 'admin';
        }

        // 2. Try finding in tc_staff (Staff)
        if (!$found_user) {
            $query_staff = "SELECT * FROM " . $GLOBALS['staff_table'] . " WHERE username = :username AND deleted = 0";
            $records = $bf->getQueryRecords($query_staff, [':username' => $username]);
            if (!empty($records)) {
                $found_user = $records[0];
                $role_type = 'staff';
            }
        }

        // 3. Try finding in tc_enrollment (Student)
        if (!$found_user) {
            $query_enr = "SELECT * FROM " . $GLOBALS['enrollment_table'] . " WHERE username = :username AND deleted = 0";
            $records = $bf->getQueryRecords($query_enr, [':username' => $username]);
            if (!empty($records)) {
                $found_user = $records[0];
                $role_type = 'student';
            }
        }

        // 4. Try finding in tc_enrollment_internship (Student - Internship)
        if (!$found_user) {
            $query_int = "SELECT * FROM " . $GLOBALS['enrollment_internship_table'] . " WHERE username = :username AND deleted = 0";
            $records = $bf->getQueryRecords($query_int, [':username' => $username]);
            if (!empty($records)) {
                $found_user = $records[0];
                $role_type = 'student';
            }
        }

        if (empty($found_user)) {
            $response['errors']['username'] = "Incorrect username";
        } else {
            // Check password using encryption method
            $encrypted_input = $bf->encode_decode('encrypt', $password);
            
            if ($found_user['password'] != $encrypted_input && $found_user['password'] != $password) {
                $response['errors']['password'] = "Password mismatch";
            } else {
                // Success
                if ($role_type === 'admin') {
                    $is_real_admin = false;
                    if (strtolower($found_user['role']) === 'admin' || intval($found_user['role_id'] ?? 0) === 1) {
                        $is_real_admin = true;
                    }
                    
                    if ($is_real_admin) {
                        $_SESSION['user_id'] = $found_user['user_id'];
                        $_SESSION['username'] = $found_user['username'];
                        $_SESSION['user_role'] = 'admin';
                        $_SESSION['full_name'] = $found_user['name'];
                        $_SESSION['role_id'] = 1;
                    } else {
                        // User in tc_users has a role other than admin (e.g. sub-admin or manager)
                        $_SESSION['user_id'] = $found_user['user_id'];
                        $_SESSION['username'] = $found_user['username'];
                        $_SESSION['role_id'] = $found_user['role_id'] ?? '';
                        
                        // Fetch the active role name dynamically matching role_id
                        $role_name = strtolower($found_user['role']);
                        if (!empty($found_user['role_id'])) {
                            $fetched_role = $bf->getTableColumnValue($GLOBALS['role_table'], 'role_id', $found_user['role_id'], 'role_name');
                            if (!empty($fetched_role)) {
                                $role_name = strtolower($fetched_role);
                            }
                        }
                        
                        $_SESSION['user_role'] = $role_name;
                        $_SESSION['full_name'] = $found_user['name'];
                    }
                } 
                elseif ($role_type === 'staff') {
                    $_SESSION['user_id'] = $found_user['staff_id'];
                    $_SESSION['username'] = $found_user['username'];
                    $_SESSION['role_id'] = $found_user['role_id'] ?? 0;
                    
                    // Fetch role based on role_id
                    $role_name = 'staff';
                    if (!empty($found_user['role_id'])) {
                        $fetched_role = $bf->getTableColumnValue($GLOBALS['role_table'], 'role_id', $found_user['role_id'], 'role_name');
                        if (!empty($fetched_role)) {
                            $role_name = strtolower($fetched_role);
                        } elseif (!empty($found_user['role'])) {
                            $role_name = strtolower($found_user['role']);
                        }
                    } elseif (!empty($found_user['role'])) {
                        $role_name = strtolower($found_user['role']);
                    }
                    $_SESSION['user_role'] = $role_name;
                    $_SESSION['full_name'] = $found_user['staff_name'];
                }
                elseif ($role_type === 'student') {
                    $_SESSION['user_id'] = $found_user['student_id'];
                    $_SESSION['username'] = $found_user['username'];
                    $_SESSION['user_role'] = 'student';
                    $_SESSION['full_name'] = $found_user['student_name'];

                    // Fetch the candidate role_id from tc_roles (primary key id = 3) and assign it to the session
                    $candidate_role = $bf->getQueryRecords(
                        "SELECT role_id FROM " . $GLOBALS['role_table'] . " WHERE id = 3 AND deleted = 0 LIMIT 1"
                    );
                    if (!empty($candidate_role)) {
                        $_SESSION['role_id'] = $candidate_role[0]['role_id'];
                    }
                }

                // Log entry depending on the source table
                $log_table = $GLOBALS['user_table'];
                if ($role_type === 'staff') {
                    $log_table = $GLOBALS['staff_table'];
                } elseif ($role_type === 'student') {
                    $log_table = (isset($found_user['enrollment_id']) && !empty($found_user['enrollment_id'])) ? $GLOBALS['enrollment_table'] : $GLOBALS['enrollment_internship_table'];
                }

                $bf->add_log($log_table, $found_user['id'], 'USER LOGIN SUCCESS', 'LOGIN');

                // Record login in DB
                $login_id = $bf->InsertSQL($GLOBALS['login_table'], [
                    'user_id' => $found_user['id'],
                    'login_date_time' => date('Y-m-d H:i:s')
                ], '', '', 'LOGIN_RECORD');
                $_SESSION['login_id'] = $login_id;
                
                // Set the company_id for the logged-in session
                $company_id = '';
                if (isset($found_user['company_id']) && !empty($found_user['company_id'])) {
                    $company_id = $found_user['company_id'];
                }
                
                // Fallback to Sivakasi branch default if the user has no specific company_id
                if (empty($company_id)) {
                    $company_id = 'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=';
                }
                
                $_SESSION['company_id'] = $company_id;

                $response['status'] = 'success';
                $response['message'] = 'Login successful!';
                $response['redirect'] = ($_SESSION['user_role'] === 'director') ? 'director_dashboard.php' : 'dashboard.php';
            }
        }
    }
} else {
    $response['message'] = 'Invalid request';
}

echo json_encode($response);
?>
