<?php
    require_once 'common_file.php';
    $con = $bf->con;

    // Check if any users exist using global table name
    $users = $bf->getQueryRecords("SELECT COUNT(*) as total FROM " . $GLOBALS['user_table'] . " WHERE deleted = 0");
    $userCount = $users[0]['total'];
    $showLogin = ($userCount > 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo get_company_name(); ?> | <?php echo $showLogin ? 'Login' : 'Initialization'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        :root {
            --primary: #0ea5e9;
            --primary-hover: #0284c7;
            --bg-gradient: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            --glass: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.5);
            --text: #0f172a;
            --text-muted: #64748b;
        }

        body {
            margin: 0;
            font-family: 'Outfit', sans-serif;
            background: var(--bg-gradient);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text);
            overflow: hidden;
        }

        .auth-container {
            background: var(--glass);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            padding: 3rem;
            border-radius: 2rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.8s ease-out;
        }

        .header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(to right, #0ea5e9, #38bdf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
        }

        .header p {
            text-align: center;
            color: var(--text-muted);
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            color: var(--text);
            font-family: inherit;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
        }

        .btn-auth {
            width: 100%;
            padding: 0.75rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 1rem;
        }

        .btn-auth:hover {
            background: var(--primary-hover);
        }

        .error-msg {
            color: #ef4444;
            font-size: 0.8rem;
            margin-top: 0.25rem;
            display: none;
        }

        .hidden { display: none; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="header">
            <h1><?php echo get_company_name(); ?></h1>
            <p><?php echo $showLogin ? 'Sign in to your account' : 'System Setup: Create Admin'; ?></p>
        </div>

        <?php if ($showLogin): ?>
        <!-- Login Form -->
        <form id="loginForm">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Enter username" required>
                <div class="error-msg" id="err-login-user"></div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
                <div class="error-msg" id="err-login-pass"></div>
            </div>
            <button type="submit" class="btn-auth">Sign In</button>
        </form>
        <?php else: ?>
        <!-- Initial Registration Form -->
        <form id="regForm">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" placeholder="Admin Name" required>
            </div>
            <div class="form-group">
                <label>Mobile</label>
                <input type="text" name="mobile" placeholder="10-digit number" required>
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Choose username" required>
                <div class="error-msg" id="err-reg-user"></div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-auth">Create Admin Account</button>
        </form>
        <?php endif; ?>
    </div>

    <script>
        $('#loginForm').submit(function(e) {
            e.preventDefault();
            $('.error-msg').hide();
            $.ajax({
                url: 'process_login.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        window.location.href = res.redirect || 'dashboard.php';
                    } else if (res.errors) {
                        if (res.errors.username) $('#err-login-user').text(res.errors.username).show();
                        if (res.errors.password) $('#err-login-pass').text(res.errors.password).show();
                    }
                }
            });
        });

        $('#regForm').submit(function(e) {
            e.preventDefault();
            $('.error-msg').hide();
            $.ajax({
                url: 'process_registration.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        location.reload();
                    } else if (res.errors && res.errors.username) {
                        $('#err-reg-user').text(res.errors.username).show();
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
