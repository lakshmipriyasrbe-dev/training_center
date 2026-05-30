<?php 
require_once 'common_file.php'; 

if ($user_role !== 'director') {
    header("Location: index.php");
    exit();
}

$user_records = $bf->getTableRecords($GLOBALS['user_table'], 'user_id', $user_id);
$u_data = !empty($user_records) ? $user_records[0] : [];
$name = $u_data['name'] ?? '';
$mobile = $u_data['mobile'] ?? '';
$username = $u_data['username'] ?? '';
$password_val = '';
if (!empty($u_data['password'])) {
    $password_val = $bf->encode_decode('decrypt', $u_data['password']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings - Director Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
            --glass-bg: rgba(255, 255, 255, 0.75);
            --glass-border: rgba(255, 255, 255, 0.5);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f1f5f9;
        }

        .profile-container {
            max-width: 650px;
            margin: 2rem auto;
            background: #ffffff;
            border-radius: 1.25rem;
            border: 1px solid rgba(0,0,0,0.06);
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .profile-header-gradient {
            background: var(--primary-gradient);
            padding: 2.5rem;
            color: white;
            text-align: center;
        }

        .profile-header-gradient h2 {
            margin: 0;
            font-size: 1.75rem;
            font-weight: 700;
        }

        .profile-header-gradient p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .profile-form-body {
            padding: 2.5rem;
        }

        .form-label-premium {
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.5rem;
            display: block;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-input-premium {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            border: 1.5px solid #e2e8f0;
            font-family: inherit;
            font-size: 0.9rem;
            background: #f8fafc;
            outline: none;
            transition: all 0.2s;
            box-sizing: border-box;
        }

        .form-input-premium:focus {
            border-color: #3b82f6;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .alert-box-premium {
            padding: 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
            font-size: 0.9rem;
            text-align: center;
            display: none;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .btn-submit-premium {
            width: 100%;
            padding: 0.85rem;
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.15);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-submit-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(59, 130, 246, 0.2);
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header" style="margin-bottom: 2rem;">
            <div>
                <h2 style="margin: 0; font-weight: 700; background: linear-gradient(to right, #0f172a, #334155); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">My Settings</h2>
                <p style="color: var(--text-muted); margin: 0.5rem 0 0 0;">Update your personal profile details and security credentials.</p>
            </div>
            <div class="user-profile">
                <span>Welcome, <?php echo htmlspecialchars($full_name); ?></span>
                <div class="avatar" style="background: var(--primary-gradient); color: white;"><?php echo substr($username, 0, 1); ?></div>
            </div>
        </div>

        <div class="profile-container">
            <div class="profile-header-gradient">
                <h2>Account Settings</h2>
                <p>Modify your account identity information</p>
            </div>

            <div class="profile-form-body">
                <div id="response_alert" class="alert-box-premium"></div>

                <form id="director_profile_form" onsubmit="saveProfile(event)">
                    <input type="hidden" name="view_user_id" value="<?php echo htmlspecialchars($u_data['id'] ?? ''); ?>">

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label class="form-label-premium">Full Name *</label>
                        <input type="text" name="name" class="form-input-premium" value="<?php echo htmlspecialchars($name); ?>" required>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label class="form-label-premium">Mobile *</label>
                        <input type="text" name="mobile" class="form-input-premium" value="<?php echo htmlspecialchars($mobile); ?>" required>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label class="form-label-premium">Username *</label>
                        <input type="text" name="username" class="form-input-premium" value="<?php echo htmlspecialchars($username); ?>" required>
                    </div>

                    <div class="form-group" style="margin-bottom: 2rem;">
                        <label class="form-label-premium">Password (Leave blank to keep unchanged)</label>
                        <div style="position: relative; display: flex; align-items: center;">
                            <input type="password" name="password" id="password_field" class="form-input-premium" style="padding-right: 2.5rem;" placeholder="••••••••">
                            <span id="eye_toggle" style="position: absolute; right: 1rem; cursor: pointer; color: #94a3b8; display: flex; align-items: center; justify-content: center; z-index: 10;">
                                <i class="fas fa-eye" id="eye_icon"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit-premium">Update Profile Settings</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('eye_toggle').addEventListener('click', function() {
            var pwdField = document.getElementById('password_field');
            var icon = document.getElementById('eye_icon');
            if (pwdField.type === 'password') {
                pwdField.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                pwdField.type = 'password';
                icon.className = 'fas fa-eye';
            }
        });

        function saveProfile(e) {
            e.preventDefault();
            var alertBox = $('#response_alert');
            alertBox.hide().removeClass('alert-success alert-error');

            $.ajax({
                url: 'user_action.php',
                type: 'POST',
                data: $('#director_profile_form').serialize(),
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        alertBox.addClass('alert-success').text(res.message || 'Profile updated successfully!').show();
                        $('#password_field').val('');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        if (res.errors) {
                            var errorMsg = Object.values(res.errors).join('<br>');
                            alertBox.addClass('alert-error').html(errorMsg).show();
                        } else {
                            alertBox.addClass('alert-error').text(res.message || 'An error occurred.').show();
                        }
                    }
                },
                error: function() {
                    alertBox.addClass('alert-error').text('Server error. Please try again.').show();
                }
            });
        }
    </script>
</body>
</html>
