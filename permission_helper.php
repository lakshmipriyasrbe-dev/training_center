<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Reusable helper function to check role-based permissions
 *
 * @param string $company_id The ID of the company
 * @param mixed $role_id The ID of the role
 * @param string $access_page The name of the module/page
 * @param string $access_action The action being requested (A/E/V/D)
 * @return bool True if permitted, otherwise false
 */
function checkPermission($company_id, $role_id, $access_page, $access_action) {
    // If the logged in user is admin, they have absolute access to everything
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        return true;
    }

    // Dashboard view is globally permitted to any logged-in user
    if ($access_page === 'dashboard' && $access_action === PERMISSION_VIEW) {
        return true;
    }

    // Students/candidates have full access to view, add, and edit their daily reports, view tasks, and manage their profile
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'student') {
        if ($access_page === 'daily_report' && in_array($access_action, [PERMISSION_VIEW, PERMISSION_ADD, PERMISSION_EDIT])) {
            return true;
        }
        if ($access_page === 'student_tasks' && $access_action === PERMISSION_VIEW) {
            return true;
        }
        if ($access_page === 'student_profile' && in_array($access_action, [PERMISSION_VIEW, PERMISSION_EDIT])) {
            return true;
        }
    }

    if (empty($company_id) || empty($role_id) || empty($access_page) || empty($access_action)) {
        return false;
    }

    global $bf;
    
    // Ensure we have access to the database connection
    if (!isset($bf) || empty($bf->con)) {
        require_once 'main/basic_functions.php';
        $bf = new Basic_Functions();
    }

    // Check if the role is the management role (table auto-increment id = 4)
    $is_management = false;
    try {
        $stmt_role = $bf->con->prepare("SELECT id FROM tc_roles WHERE role_id = :role_id LIMIT 1");
        $stmt_role->execute([':role_id' => $role_id]);
        $role_row = $stmt_role->fetch(PDO::FETCH_ASSOC);
        if ($role_row && intval($role_row['id']) === 4) {
            $is_management = true;
        }
    } catch (Exception $e) {
        // Fallback
    }

    // Management role has global access to Dashboard, Creation menu, and All Reports
    if ($is_management) {
        $management_allowed = [
            'dashboard',
            'company',
            'roles',
            'role',
            'users',
            'staff',
            'report_enrollment',
            'report_payroll',
            'report_payments',
            'report_attendance',
            'report_placement'
        ];
        if (in_array($access_page, $management_allowed)) {
            return true;
        }
    }

    try {
        $table = $GLOBALS['role_permissions_table'] ?? 'tc_role_permissions';
        
        // Query to check if the role has permissions for this page in this company
        $stmt = $bf->con->prepare("
            SELECT permission_action 
            FROM $table 
            WHERE role_id = :role_id 
              AND company_id = :company_id 
              AND permission_page = :page 
            LIMIT 1
        ");
        
        $stmt->execute([
            ':role_id' => $role_id,
            ':company_id' => $company_id,
            ':page' => $access_page
        ]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row && !empty($row['permission_action'])) {
            // Actions are saved as a string joined by '$$', e.g., 'V$$A$$E'
            $actions = explode('$$', $row['permission_action']);
            return in_array($access_action, $actions);
        }
    } catch (PDOException $e) {
        // Fallback or log if needed
    }

    return false;
}
?>
