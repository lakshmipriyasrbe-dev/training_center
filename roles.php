<?php require_once 'common_file.php'; 
if ($user_role != 'admin') { header("Location: dashboard.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Role Management - Training Center</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <h2>Role Management</h2>
        </div>

        <div class="module-section">
            <div class="section-title">
                Available Roles
                <button class="btn-add" onclick="window.location.href='add_role.php'">Add New Role</button>
            </div>
            <div class="list-controls">
                <div class="entries-control">
                    Show 
                    <select id="role_limit" onchange="loadData('role', 1, this.value, $('#role_search').val())">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    entries
                </div>
                <div class="search-control">
                    <i class="fas fa-search"></i>
                    <input type="text" id="role_search" placeholder="Search roles..." onkeyup="loadData('role', 1, $('#role_limit').val(), this.value)">
                </div>
            </div>

            <div id="role_list">
                <p style="text-align:center; padding: 2rem; color: var(--text-muted);">Loading Roles...</p>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            loadData('role');
        });
    </script>
    <script src="main/js/script.js"></script>
</body>
</html>
