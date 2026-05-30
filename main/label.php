<?php
    date_default_timezone_set('Asia/Calcutta');
    $GLOBALS['create_date_time_label'] = date('Y-m-d H:i:s');

    // Tables Prefix
    $table_prefix = "tc_";
    
    // Global Table Names
    $GLOBALS['user_table'] = $table_prefix . 'users';
    $GLOBALS['role_table'] = $table_prefix . 'roles';
    $GLOBALS['task_table'] = $table_prefix . 'tasks';
    $GLOBALS['report_table'] = $table_prefix . 'daily_reports';
    $GLOBALS['login_table'] = $table_prefix . 'logins';
    $GLOBALS['company_table'] = $table_prefix . 'company';
    $GLOBALS['enrollment_table'] = $table_prefix . 'enrollment';
    $GLOBALS['enrollment_internship_table'] = $table_prefix . 'enrollment_internship';
    $GLOBALS['course_closure_table'] = $table_prefix . 'course_closure';
    $GLOBALS['attendance_table'] = $table_prefix . 'attendance';
    $GLOBALS['payment_table'] = $table_prefix . 'payment';
    $GLOBALS['course_table'] = $table_prefix . 'course';
    $GLOBALS['staff_table'] = $table_prefix . 'staff';
    $GLOBALS['payment_mode_table'] = $table_prefix . 'payment_mode';
    $GLOBALS['bank_table'] = $table_prefix . 'bank';
    $GLOBALS['attendance_table'] = $table_prefix . 'attendance';
    $GLOBALS['payroll_table'] = $table_prefix . 'payroll';
    $GLOBALS['student_attendance_table'] = $table_prefix . 'student_attendance';
    $GLOBALS['expense_category_table'] = $table_prefix . 'expense_category';
    $GLOBALS['expense_entry_table'] = $table_prefix . 'expense_entry';
    $GLOBALS['student_tasks_table'] = $table_prefix . 'student_tasks';
    $GLOBALS['student_reports_table'] = $table_prefix . 'student_reports';
    $GLOBALS['task_comments_table'] = $table_prefix . 'task_comments';
    $GLOBALS['course_enquiry_table'] = $table_prefix . 'course_enquiry';
    $GLOBALS['role_permissions_table'] = $table_prefix . 'role_permissions';
    $GLOBALS['event_table'] = $table_prefix . 'event';

    // Security
    $GLOBALS['salt'] = "TrainingCenter_2026_Secure_Salt_!@#";
    
    // Global Permission Constants
    define('PERMISSION_ADD', 'A');
    define('PERMISSION_EDIT', 'E');
    define('PERMISSION_VIEW', 'V');
    define('PERMISSION_DELETE', 'D');

    // Modules Array
    $modules = [
        'dashboard' => 'Dashboard',
        
        // Creation Pages
        'company' => 'Company Details',
        'roles' => 'Role Management',
        'users' => 'User Management',
        'staff' => 'Staff Management',

        // Management Pages
        'course' => 'Courses',
        'course_closure' => 'Course Closure',
        'tasks' => 'Tasks',
        'event' => 'Events',
        
        // Students Pages
        'enrollment' => 'Training Enrollment',
        'enrollment_internship' => 'Internship Enrollment',
        'course_enquiry' => 'Course Enquiry',

        // Payroll Pages
        'attendance' => 'Attendance',
        'payroll' => 'Process Payroll',

        // Payments Pages
        'receipt' => 'Receipts',
        'payment_mode' => 'Payment Modes',
        'bank' => 'Banks',

        // Expenses Pages
        'expense_category' => 'Expense Category',
        'expense_entry' => 'Expense Entry',

        // Reports Pages
        'daily_reports' => 'Daily Staff Log',
        'report_enrollment' => 'Enrollment Report',
        'report_payroll' => 'Payroll Report',
        'report_payments' => 'Payments Report',
        'report_attendance' => 'Attendance Report',
        'report_placement' => 'Placement Report',

        // Student-specific / Trainer pages
        'student_attendance' => 'Student Attendance',
        'student_tasks' => 'Student Tasks',
        'student_reports' => 'Student Daily Report',
        'daily_report' => 'Daily Report',
        'student_profile' => 'My Profile'
    ];

    // Permission Display Names
    $permissionActions = [
        PERMISSION_ADD => 'Add',
        PERMISSION_EDIT => 'Edit',
        PERMISSION_VIEW => 'View',
        PERMISSION_DELETE => 'Delete'
    ];
?>
