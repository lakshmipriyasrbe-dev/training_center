<?php require_once 'common_file.php'; 
if ($user_role != 'admin') { header("Location: dashboard.php"); exit(); }
$from_page = 'enrollment';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Management - Training Center</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content update_content">
        <div class="header">
            <h2><?php if(!empty($from_page))  echo ucfirst($from_page); ?></h2>
        </div>

        <div class="module-section">
            <div class="section-title">
                Active Training Enrollments
                <button class="btn-add" onclick="ShowPage('enrollment', '')">Add New Enrollment</button>
            </div>

            <div class="list-controls">
                <div class="entries-control">
                    Show 
                    <select id="enrollment_limit" onchange="loadData('enrollment', 1, this.value, $('#enrollment_search').val())">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    entries
                </div>
                <div class="search-control">
                    <i class="fas fa-search"></i>
                    <input type="text" id="enrollment_search" placeholder="Search enrollments..." onkeyup="loadData('enrollment', 1, $('#enrollment_limit').val(), this.value)">
                </div>
            </div>

            <div id="enrollment_list">
                <p style="text-align:center; padding: 2rem; color: var(--text-muted);">Loading Enrollments...</p>
            </div>
        </div>
    </div>
    <div class="main-content new_content" style="display: none;">
    </div>

    <script>
        $(document).ready(function() {
            loadData('enrollment');
        });
    </script>
    <script src="main/js/script.js"></script>
    <script src="main/js/keyboard_control.js"></script>
</body>
</html>
