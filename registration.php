<?php require_once 'common_file.php'; 
if ($user_role != 'admin') { header("Location: dashboard.php"); exit(); }

// Fetch roles for the dropdown
$roles = $bf->getTableRecords($GLOBALS['role_table'], 'deleted', 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - <?php echo get_company_name(); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <h2 style="margin-bottom: 2rem;">Create New User</h2>
        
        <div class="module-section">
            <form id="addUserForm">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Full Name</label>
                        <input type="text" name="name" required>
                        <div class="error-msg" id="err-name"></div>
                    </div>
                    <div class="form-group">
                        <label>Mobile</label>
                        <input type="text" name="mobile" required>
                        <div class="error-msg" id="err-mobile"></div>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role">
                            <?php foreach ($roles as $r): ?>
                            <option value="<?php echo $r['role_name']; ?>"><?php echo ucfirst($r['role_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" required>
                        <div class="error-msg" id="err-username"></div>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required>
                        <div class="error-msg" id="err-password"></div>
                    </div>
                </div>
                <button type="submit" class="btn-save">Create User</button>
            </form>
        </div>
    </div>

    <script>
        $('#addUserForm').submit(function(e) {
            e.preventDefault();
            $('.error-msg').text('');
            $.ajax({
                url: 'user_action.php',
                type: 'POST',
                data: $(this).serialize() + '&action=add',
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        alert('User created successfully!');
                        window.location.href = 'users.php';
                    } else if (res.errors) {
                        for (let key in res.errors) { $(`#err-${key}`).text(res.errors[key]); }
                    } else {
                        alert(res.message);
                    }
                }
            });
        });
    </script>
    <script src="main/js/script.js"></script>
</body>
</html>
