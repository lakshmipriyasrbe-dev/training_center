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
            <div id="userList">
                <p>Loading users...</p>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            loadUsers();
        });

        function loadUsers() {
            $.get('user_action.php?action=list', function(data) {
                $('#userList').html(data);
            });
        }

        function deleteUser(id) {
            if (confirm('Are you sure you want to delete this user?')) {
                $.post('user_action.php', { action: 'delete', id: id }, function() {
                    loadUsers();
                });
            }
        }
    </script>
</body>
</html>
