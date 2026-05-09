<?php
require_once 'common_file.php';

    if (isset($_SESSION['user_id'])) {
        // Record logout in DB
        if (isset($_SESSION['login_id'])) {
            $bf->UpdateSQL($GLOBALS['login_table'], ['logout_date_time' => date('Y-m-d H:i:s')], "id = :id", [':id' => $_SESSION['login_id']]);
        }

        // Perform DB backup on logout
        $backup_file = $bf->db_backup();
        $bf->add_log($GLOBALS['user_table'], $_SESSION['user_id'], 'USER LOGOUT - Backup created: ' . basename($backup_file), 'LOGOUT');
        
        session_destroy();
    }

    header("Location: index.php");
    exit();
?>
