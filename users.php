<?php require_once 'common_file.php'; 
if ($user_role != 'admin') { header("Location: dashboard.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Training Center</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <h2>User Management</h2>
        </div>

        <div class="module-section">
            <div class="section-title">
                Active Users
                <button class="btn-add" onclick="window.location.href='registration.php'">Add New User</button>
            </div>
            <div class="list-controls">
                <div class="entries-control">
                    Show 
                    <select id="user_limit" onchange="loadData('user', 1, this.value, $('#user_search').val())">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    entries
                </div>
                <div class="search-control">
                    <i class="fas fa-search"></i>
                    <input type="text" id="user_search" placeholder="Search users..." onkeyup="loadData('user', 1, $('#user_limit').val(), this.value)">
                </div>
            </div>

            <div id="user_list">
                <p style="text-align:center; padding: 2rem; color: var(--text-muted);">Loading Users...</p>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            loadData('user');
        });
    </script>
</body>
</html>
