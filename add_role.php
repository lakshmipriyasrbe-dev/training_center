<?php require_once 'common_file.php'; 
if ($user_role != 'admin') { header("Location: dashboard.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Role - Training Center</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <h2>Create New Role</h2>
        </div>

        <div class="module-section" style="max-width: 600px;">
            <form id="roleForm">
                <div class="form-group">
                    <label>Role Name</label>
                    <input type="text" name="role_name" placeholder="e.g. Supervisor" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="5" placeholder="Describe the responsibilities of this role..."></textarea>
                </div>
                <div style="display: flex; gap: 1rem;margin-top: 1rem;" >
                    <button type="submit" class="btn-add" style="flex: 1;">Create Role</button>
                    <button type="button" class="btn-add" style="flex: 1; background: #64748b;" onclick="window.location.href='roles.php'">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $('#roleForm').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: 'role_action.php',
                type: 'POST',
                data: $(this).serialize() + '&action=add',
                success: function(response) {
                    if (response.trim() == 'Success') {
                        window.location.href = 'roles.php';
                    } else {
                        alert(response);
                    }
                }
            });
        });
    </script>
</body>
</html>
