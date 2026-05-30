<?php
require_once 'common_file.php';

if ($user_role !== 'student') {
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized action']));
}

$action = $_POST['action'] ?? $_REQUEST['action'] ?? '';

if ($action == 'update_profile') {
    $new_username = $bf->sanitize($_POST['username'] ?? '');
    $new_password = $_POST['password'] ?? '';

    $errors = [];
    if (empty($new_username)) {
        $errors['username'] = 'Enter new username';
    } else {
        // Check uniqueness across admin, staff, and other students
        $is_taken = false;
        
        // Check admin
        $stmt = $bf->con->prepare("SELECT id FROM " . $GLOBALS['user_table'] . " WHERE username = :new_username AND deleted = 0");
        $stmt->execute([':new_username' => $new_username]);
        if ($stmt->fetch()) {
            $is_taken = true;
        }
        
        // Check staff
        if (!$is_taken) {
            $stmt = $bf->con->prepare("SELECT id FROM " . $GLOBALS['staff_table'] . " WHERE username = :new_username AND deleted = 0");
            $stmt->execute([':new_username' => $new_username]);
            if ($stmt->fetch()) {
                $is_taken = true;
            }
        }
        
        // Check other student enrollments
        if (!$is_taken) {
            $stmt = $bf->con->prepare("SELECT id FROM " . $GLOBALS['enrollment_table'] . " WHERE username = :new_username AND username != :current_username AND deleted = 0");
            $stmt->execute([':new_username' => $new_username, ':current_username' => $username]);
            if ($stmt->fetch()) {
                $is_taken = true;
            }
        }
        
        // Check other student internship enrollments
        if (!$is_taken) {
            $stmt = $bf->con->prepare("SELECT id FROM " . $GLOBALS['enrollment_internship_table'] . " WHERE username = :new_username AND username != :current_username AND deleted = 0");
            $stmt->execute([':new_username' => $new_username, ':current_username' => $username]);
            if ($stmt->fetch()) {
                $is_taken = true;
            }
        }
        
        if ($is_taken) {
            $errors['username'] = 'This username is already taken';
        }
    }

    if (empty($new_password)) {
        $errors['password'] = 'Enter new password';
    } elseif (strlen($new_password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters';
    }

    if (!empty($errors)) {
        echo json_encode(['status' => 'error', 'errors' => $errors]);
        exit;
    }

    // Begin atomic cascading updates across all student tables to preserve data integrity
    try {
        $bf->con->beginTransaction();

        $encrypted_old_username = $bf->encode_decode('encrypt', $username);
        $encrypted_new_username = $bf->encode_decode('encrypt', $new_username);
        $encrypted_new_password = $bf->encode_decode('encrypt', $new_password);

        // 1. Update tc_enrollment (username, password, student_id)
        $bf->UpdateSQL($GLOBALS['enrollment_table'], [
            'username' => $new_username,
            'password' => $encrypted_new_password,
            'student_id' => $encrypted_new_username,
            'updated_date_time' => date('Y-m-d H:i:s')
        ], "student_id = :old_student_id AND deleted = 0", [':old_student_id' => $encrypted_old_username]);

        // 2. Update tc_enrollment_internship (username, password, student_id)
        $bf->UpdateSQL($GLOBALS['enrollment_internship_table'], [
            'username' => $new_username,
            'password' => $encrypted_new_password,
            'student_id' => $encrypted_new_username,
            'updated_date_time' => date('Y-m-d H:i:s')
        ], "student_id = :old_student_id AND deleted = 0", [':old_student_id' => $encrypted_old_username]);

        // 3. Update tc_student_tasks
        $bf->UpdateSQL($GLOBALS['student_tasks_table'], [
            'assigned_to_student' => $new_username,
            'updated_date_time' => date('Y-m-d H:i:s')
        ], "assigned_to_student = :old_student_id AND deleted = 0", [':old_student_id' => $username]);

        // 4. Update tc_student_reports
        $bf->UpdateSQL($GLOBALS['student_reports_table'], [
            'student_id' => $new_username,
            'updated_date_time' => date('Y-m-d H:i:s')
        ], "student_id = :old_student_id AND deleted = 0", [':old_student_id' => $username]);

        // 5. Update tc_student_attendance
        $bf->UpdateSQL($GLOBALS['student_attendance_table'], [
            'student_id' => $encrypted_new_username,
            'updated_date_time' => date('Y-m-d H:i:s')
        ], "student_id = :old_student_id AND deleted = 0", [':old_student_id' => $encrypted_old_username]);

        $bf->con->commit();

        // Sync active session credentials so student stays authenticated seamlessly
        $_SESSION['username'] = $new_username;
        $_SESSION['user_id'] = $encrypted_new_username;

        echo json_encode(['status' => 'success', 'message' => 'Profile updated and reference tables cascaded successfully!']);
    } catch (Exception $e) {
        $bf->con->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Failed to process cascading updates: ' . $e->getMessage()]);
    }
    exit;
}
?>
