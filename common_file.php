<?php
    require_once 'main/basic_functions.php';
    require_once 'main/report_functions.php';
    require_once 'main/validation.php';
    $bf = new Report_Functions();
    $valid = new validation();

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Safely initialize session keys to prevent "Undefined array key" notices globally
    if (!isset($_SESSION['role_id'])) {
        $_SESSION['role_id'] = 0;
    }
    if (!isset($_SESSION['company_id'])) {
        $_SESSION['company_id'] = '';
    }

    $current_page = basename($_SERVER['PHP_SELF']);
    $guest_pages = ['index.php', 'process_registration.php', 'process_login.php'];

    if (!isset($_SESSION['user_id']) && !in_array($current_page, $guest_pages)) {
        header("Location: index.php");
        exit();
    }

    if (isset($_SESSION['user_id']) && empty($_SESSION['company_id']) && !in_array($current_page, $guest_pages)) {
        // Destroy session and force login if company context is missing
        session_destroy();
        header("Location: index.php?error=MissingCompanyContext");
        exit();
    }

    $user_id = $_SESSION['user_id'] ?? 0;
    $username = $_SESSION['username'] ?? 'Guest';
    $user_role = $_SESSION['user_role'] ?? 'guest';
    $full_name = $_SESSION['full_name'] ?? 'Guest User';
    $GLOBALS['bill_company_id'] = $_SESSION['company_id'] ?? '';
    
    // Direct routing & restriction for Director role
    if ($user_role === 'director') {
        if (!in_array($current_page, $guest_pages) && $current_page !== 'director_dashboard.php' && $current_page !== 'director_profile.php' && $current_page !== 'user_action.php' && $current_page !== 'logout.php') {
            header("Location: director_dashboard.php");
            exit();
        }
    }
    
    // Check if user is in Management role (db role id = 4)
    $is_management = false;
    if (!empty($_SESSION['role_id'])) {
        $role_row = $bf->getQueryRecords("SELECT id FROM " . $GLOBALS['role_table'] . " WHERE role_id = :role_id LIMIT 1", [':role_id' => $_SESSION['role_id']]);
        if (!empty($role_row) && intval($role_row[0]['id']) === 4) {
            $is_management = true;
        }
    }
    // $bill_company_id = $_SESSION['company_id'] ?? ''; 

    // echo $bill_company_id." hi";

    // echo $_SESSION['role_id']." hi";

    if ($user_role === 'student') {
        // Allow utility/action pages that don't have their own module entry
        $student_utility_pages = [
            'student_tasks_action.php',
            'daily_report_action.php',
            'student_profile_action.php',
            'student_reports_action.php',
            'student_attendance_action.php',
            'logout.php'
        ];
        // Utility pages pass through; all other pages are validated by the URL-level permission gate below
        if (in_array($current_page, $student_utility_pages) || in_array($current_page, $guest_pages)) {
            // Allowed, continue
        } elseif ($current_page !== 'dashboard.php') {
            // For regular module pages, the dynamic gate below will handle them
            // No blanket redirect needed
        }
    }

    // Include the Role-Based Permission helper globally
    require_once 'permission_helper.php';

    // Automated URL-level View Permission security guard
    $page_key = str_replace('.php', '', $current_page);
    
    // Normalise special alias redirections
    if ($page_key === 'roles' || $page_key === 'role') {
        $page_key = 'roles';
    }

    // Exclude core system pages from dynamic database-driven permission checks
    $exempt_pages = ['dashboard', 'company', 'roles', 'role', 'users', 'staff'];

    if (isset($modules) && isset($modules[$page_key]) && !in_array($page_key, $exempt_pages)) {
        // Since admin has all permissions, bypass checkPermission entirely for admin and director at the URL level
        if ($user_role !== 'admin' && $user_role !== 'director') {
            $check_comp_id = $_SESSION['company_id'] ?? '';
            $check_role_id = $_SESSION['role_id'] ?? 0;
            
            if (!checkPermission($check_comp_id, $check_role_id, $page_key, PERMISSION_VIEW)) {
                header("Location: dashboard.php?error=AccessDenied");
                exit();
            }
        }
    }

    /**
     * Retrieves the current company name from the session company_id, 
     * falling back to 'WeGrow Skill Campus' if none is found.
     *
     * @return string
     */
    function get_company_name() {
        global $bf;
        $company_id = $_SESSION['company_id'] ?? '';
        if (!empty($company_id) && isset($bf)) {
            $company_name = $bf->getTableColumnValue($GLOBALS['company_table'], 'company_id', $company_id, 'company_name');
            if (!empty($company_name)) {
                return $company_name;
            }
        }
        return 'WeGrow Skill Campus';
    }
?>
