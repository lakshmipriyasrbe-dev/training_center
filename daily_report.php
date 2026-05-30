<?php 
require_once 'common_file.php'; 
$from_page = 'daily_report';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Report - Student Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <!-- Main Table View -->
    <div class="main-content update_content">
        <div class="header">
            <h2>Daily Reporting System</h2>
            <div class="user-profile">
                <span><?php echo $username; ?></span>
                <div class="avatar"><?php echo substr($username, 0, 1); ?></div>
            </div>
        </div>

        <div class="module-section">
            <div class="section-title">
                My Daily Reports
                <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'daily_report', PERMISSION_ADD)): ?>
                    <button class="btn-add" onclick="ShowPage('daily_report', '')">Submit Daily Report</button>
                <?php endif; ?>
            </div>

            <div class="list-controls">
                <div class="entries-control">
                    Show 
                    <select id="daily_report_limit" onchange="loadData('daily_report', 1, this.value, $('#daily_report_search').val())">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    entries
                </div>
                <div class="search-control">
                    <i class="fas fa-search"></i>
                    <input type="text" id="daily_report_search" placeholder="Search reports..." onkeyup="loadData('daily_report', 1, $('#daily_report_limit').val(), this.value)">
                </div>
            </div>

            <div id="daily_report_list">
                <p style="text-align:center; padding: 2rem; color: var(--text-muted);">Loading Daily Reports...</p>
            </div>
        </div>
    </div>

    <!-- Form Add/Edit View Container (Dynamically populated via ShowPage) -->
    <div class="main-content new_content" style="display: none;">
    </div>

    <script>
        $(document).ready(function() {
            loadData('daily_report');
        });
    </script>
    <script src="main/js/script.js"></script>
    <script src="main/js/keyboard_control.js"></script>
</body>
</html>
