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
    $GLOBALS['course_table'] = $table_prefix . 'course';
    $GLOBALS['staff_table'] = $table_prefix . 'staff';
    $GLOBALS['payment_mode_table'] = $table_prefix . 'payment_mode';
    $GLOBALS['bank_table'] = $table_prefix . 'bank';

    // Security
    $GLOBALS['salt'] = "TrainingCenter_2026_Secure_Salt_!@#";
?>
