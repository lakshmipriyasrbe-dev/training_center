<?php
    require_once 'main/basic_functions.php';
    require_once 'main/validation.php';
    $bf = new Basic_Functions();
    $valid = new validation();

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $current_page = basename($_SERVER['PHP_SELF']);
    $guest_pages = ['index.php', 'process_registration.php', 'process_login.php'];

    if (!isset($_SESSION['user_id']) && !in_array($current_page, $guest_pages)) {
        header("Location: index.php");
        exit();
    }

    $user_id = $_SESSION['user_id'] ?? 0;
    $username = $_SESSION['username'] ?? 'Guest';
    $user_role = $_SESSION['user_role'] ?? 'guest';
    $full_name = $_SESSION['full_name'] ?? 'Guest User';
?>
