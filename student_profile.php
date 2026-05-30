<?php 
require_once 'common_file.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Student Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <h2>My Profile Settings</h2>
            <div class="user-profile">
                <span><?php echo $username; ?></span>
                <div class="avatar"><?php echo substr($username, 0, 1); ?></div>
            </div>
        </div>

        <div class="module-section" style="max-width: 600px; margin: 2rem auto 0 auto; padding: 2rem; border-radius: 1rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);">
            <div class="section-title" style="margin-bottom: 2rem; justify-content: center; font-size: 1.5rem; color: var(--primary);">
                Update Login Credentials
            </div>

            <div id="alert_box" style="display:none; padding:1rem; border-radius:0.5rem; margin-bottom:1.5rem; font-weight:600; font-size:0.85rem; text-align:center;"></div>

            <form id="profile_update_form" onsubmit="submitProfileForm(event)">
                <input type="hidden" name="action" value="update_profile">

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="font-weight: 600; color: #475569; margin-bottom: 0.5rem; display: block;">Username *</label>
                    <input type="text" name="username" class="form-input" value="<?php echo htmlspecialchars($username); ?>" style="padding:0.75rem 1rem; border-radius: 8px;" required>
                    <small style="color:var(--text-muted); display:block; margin-top:0.25rem;">This is your plain-text login identifier (e.g. your Student ID).</small>
                </div>

                <div class="form-group" style="margin-bottom: 2rem;">
                    <label style="font-weight: 600; color: #475569; margin-bottom: 0.5rem; display: block;">New Password *</label>
                    <input type="password" name="password" class="form-input" placeholder="Enter new strong password" style="padding:0.75rem 1rem; border-radius: 8px;" required>
                    <small style="color:var(--text-muted); display:block; margin-top:0.25rem;">Minimum 6 characters recommended.</small>
                </div>

                <div class="form-buttons" style="justify-content: center; margin-top: 2rem;">
                    <button type="submit" class="btn-add" style="padding: 0.75rem 2.5rem; font-size: 0.95rem; border-radius: 8px; font-weight:700; width:100%;">Update Credentials</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function submitProfileForm(e) {
            e.preventDefault();
            $('#alert_box').hide();
            
            $.post('student_profile_action.php', $('#profile_update_form').serialize(), function(response) {
                try {
                    let res = JSON.parse(response);
                    if(res.status === 'success') {
                        $('#alert_box').css({'background': '#d1fae5', 'color': '#065f46', 'border': '1px solid #a7f3d0'}).text(res.message).show();
                        // Clear password field
                        $('input[type="password"]').val('');
                        // Reload top banner details
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        if (res.errors) {
                            let firstErr = Object.values(res.errors)[0];
                            $('#alert_box').css({'background': '#fee2e2', 'color': '#991b1b', 'border': '1px solid #fca5a5'}).text(firstErr).show();
                        } else {
                            $('#alert_box').css({'background': '#fee2e2', 'color': '#991b1b', 'border': '1px solid #fca5a5'}).text(res.message || 'Error updating credentials').show();
                        }
                    }
                } catch(err) {
                    $('#alert_box').css({'background': '#fee2e2', 'color': '#991b1b', 'border': '1px solid #fca5a5'}).text('Server error occurred.').show();
                }
            });
        }
    </script>
    <script src="main/js/script.js"></script>
    <script src="main/js/keyboard_control.js"></script>
</body>
</html>
