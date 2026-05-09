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
            <div id="roleList">
                <p>Loading roles...</p>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            loadRoles();
        });

        function loadRoles() {
            $.get('role_action.php?action=list', function(data) {
                $('#roleList').html(data);
            });
        }
    </script>
</body>
</html>
