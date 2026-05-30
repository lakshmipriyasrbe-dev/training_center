<?php require_once 'common_file.php'; 
$from_page = 'event';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management - <?php echo get_company_name(); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content update_content">
        <div class="header">
            <h2>Event Management</h2>
        </div>

        <div class="module-section">
            <div class="section-title">
                Active Events
                <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'event', PERMISSION_ADD)): ?>
                    <button class="btn-add" onclick="ShowPage('event', '')">Add New Event</button>
                <?php endif; ?>
            </div>
            <div class="list-controls">
                <div class="entries-control">
                    Show 
                    <select id="event_limit" onchange="loadData('event', 1, this.value, $('#event_search').val())">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    entries
                </div>
                <div class="search-control">
                    <i class="fas fa-search"></i>
                    <input type="text" id="event_search" placeholder="Search events..." onkeyup="loadData('event', 1, $('#event_limit').val(), this.value)">
                </div>
            </div>

            <div id="event_list">
                <p style="text-align:center; padding: 2rem; color: var(--text-muted);">Loading Events...</p>
            </div>
        </div>
    </div>
    <div class="main-content new_content" style="display: none;"></div>

    <script>
        $(document).ready(function() {
            loadData('event');
        });
    </script>
    <script src="main/js/script.js"></script>
    <script src="main/js/keyboard_control.js"></script>
</body>
</html>
