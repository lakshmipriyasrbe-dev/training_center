<?php
require_once 'common_file.php';

if ($user_role != 'admin' && $user_role != 'director' && !$is_management) { exit('Unauthorized'); }

$action = $_REQUEST['action'] ?? '';

if ($user_role === 'director' && !isset($_POST['name'])) {
    exit('Unauthorized');
}

if (isset($_REQUEST['view_user_id']) && !isset($_POST['name'])) {
    $view_user_id = $_REQUEST['view_user_id'];
    
    $name = "";
    $mobile = "";
    $username = "";
    $role = "staff";
    $password_val = "";
    
    if (!empty($view_user_id)) {
        $user_records = $bf->getTableRecords($GLOBALS['user_table'], 'id', $view_user_id);
        if (!empty($user_records)) {
            $u_data = $user_records[0];
            $name = $u_data['name'] ?? '';
            $mobile = $u_data['mobile'] ?? '';
            $username = $u_data['username'] ?? '';
            $role = $u_data['role'] ?? 'staff';
            if (!empty($u_data['password'])) {
                $password_val = $bf->encode_decode('decrypt', $u_data['password']);
            }
        }
    }
    
    // Fetch active roles for the selection
    $roles = $bf->getTableRecords($GLOBALS['role_table'], 'deleted', 0);
    ?>
    <div class="header">
        <h2>
            <?php echo empty($view_user_id) ? "Create New User" : "Update User Details"; ?>
        </h2>
    </div>

    <div class="module-section form-section">
        <form
            name="user_form"
            id="user_form"
            method="POST"
            enctype="multipart/form-data"
            onsubmit="event.preventDefault(); formSubmit('user_form', 'user_action.php', 'users.php', 'user');"
        >
            <input type="hidden" name="view_user_id" value="<?php echo $view_user_id; ?>">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Full Name *</label>
                    <input type="text" name="name" class="form-input" value="<?php echo $name; ?>" required>
                    <span id="error-name" class="error-msg"></span>
                </div>
                <div class="form-group">
                    <label>Mobile *</label>
                    <input type="text" name="mobile" class="form-input" value="<?php echo $mobile; ?>" required>
                    <span id="error-mobile" class="error-msg"></span>
                </div>
                <div class="form-group">
                    <label>Role *</label>
                    <select name="role" class="form-input">
                        <?php foreach ($roles as $r): ?>
                        <option value="<?php echo $r['role_id']; ?>" <?php echo ($role == $r['role_name'] || (isset($u_data['role_id']) && $u_data['role_id'] == $r['role_id'])) ? 'selected' : ''; ?>>
                            <?php echo ucfirst($r['role_name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <span id="error-role" class="error-msg"></span>
                </div>
                <div class="form-group">
                    <label>Username *</label>
                    <input type="text" name="username" class="form-input" value="<?php echo $username; ?>" required>
                    <span id="error-username" class="error-msg"></span>
                </div>
                <div class="form-group">
                    <label>Password <?php echo empty($view_user_id) ? '*' : '(Leave blank to keep unchanged)'; ?></label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <input type="password" name="password" id="password_input" class="form-input" style="padding-right: 2.5rem;" value="<?php echo htmlspecialchars($password_val); ?>" <?php echo empty($view_user_id) ? 'required' : ''; ?>>
                        <span id="toggle_password_btn" style="position: absolute; right: 1rem; cursor: pointer; color: var(--text-muted); display: flex; align-items: center; justify-content: center; z-index: 10;">
                            <i class="fas fa-eye" id="password_eye_icon"></i>
                        </span>
                    </div>
                    <span id="error-password" class="error-msg"></span>
                </div>
            </div>

            <div class="form-buttons" style="margin-top: 1.5rem;">
                <button type="submit" class="btn-add">
                    <?php echo empty($view_user_id) ? "Create User" : "Update User"; ?>
                </button>
                <a href="users.php" class="btn-add" style="background: #ef4444; font-size: 0.75rem;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
    <script>
        document.getElementById('toggle_password_btn').addEventListener('click', function() {
            var passwordInput = document.getElementById('password_input');
            var eyeIcon = document.getElementById('password_eye_icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                eyeIcon.className = 'fas fa-eye';
            }
        });
    </script>
    <?php
    exit();
}

// Unified Save Handler (Add / Edit Save)
if (isset($_POST['name'])) {
    if ($user_role === 'director') {
        $allowed = false;
        $check_view_id = $bf->sanitize($_POST['view_user_id'] ?? '');
        if (!empty($check_view_id)) {
            $current_director_records = $bf->getTableRecords($GLOBALS['user_table'], 'user_id', $_SESSION['user_id']);
            if (!empty($current_director_records) && $check_view_id == $current_director_records[0]['id']) {
                $allowed = true;
            }
        }
        if (!$allowed) {
            echo json_encode(['status' => 'error', 'errors' => ['name' => 'Unauthorized operation']]);
            exit();
        }
    }

    $name = $bf->sanitize($_POST['name'] ?? '');
    $mobile = $bf->sanitize($_POST['mobile'] ?? '');
    $username = $bf->sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $bf->sanitize($_POST['role'] ?? 'staff');
    $view_user_id = $bf->sanitize($_POST['view_user_id'] ?? '');

    $errors = [];
    $err = $valid->valid_name($name, "Full Name");
    if ($err) $errors['name'] = $err;
    $err = $valid->valid_mobile($mobile, "Mobile Number");
    if ($err) $errors['mobile'] = $err;
    
    if (empty($username)) {
        $errors['username'] = "Enter Username";
    } else {
        // Check uniqueness ignoring edited user
        $query = "SELECT id FROM " . $GLOBALS['user_table'] . " WHERE (username = :username OR mobile = :mobile) AND deleted = 0";
        $params = [':username' => $username, ':mobile' => $mobile];
        if (!empty($view_user_id)) {
            $query .= " AND id != :view_user_id";
            $params[':view_user_id'] = $view_user_id;
        }
        $stmt = $bf->con->prepare($query);
        $stmt->execute($params);
        if ($stmt->fetch()) {
            $errors['username'] = "Username or Mobile already exists";
        }
    }

    if (empty($view_user_id) || !empty($password)) {
        $err = $valid->valid_password($password, "Password");
        if ($err) $errors['password'] = $err;
    }

    // If the logged-in user is a director and they are updating themselves, keep role as director
    $is_director_self_update = false;
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'director') {
        $current_director_records = $bf->getTableRecords($GLOBALS['user_table'], 'user_id', $_SESSION['user_id']);
        if (!empty($current_director_records) && $view_user_id == $current_director_records[0]['id']) {
            $is_director_self_update = true;
        }
    }

    if ($is_director_self_update) {
        $role_name = 'director';
        $selected_role_id = $_SESSION['role_id'] ?? null;
    } else {
        $selected_role_id = $role;
        $role_name = 'staff';
        if ($selected_role_id === 'admin' || strtolower($selected_role_id) === 'admin') {
            $role_name = 'admin';
        } else {
            $role_data = $bf->getTableRecords($GLOBALS['role_table'], 'role_id', $selected_role_id);
            if (!empty($role_data)) {
                $role_name = $role_data[0]['role_name'];
            }
        }
    }

    if (empty($errors)) {
        $data = [
            'name' => $name,
            'mobile' => $mobile,
            'username' => $username,
            'role' => $role_name,
            'role_id' => $selected_role_id,
            'updated_date_time' => date('Y-m-d H:i:s')
        ];
        
        if (!empty($password)) {
            $data['password'] = $bf->encode_decode('encrypt', $password);
        }

        if (empty($view_user_id)) {
            // New user addition
            $data['created_date_time'] = date('Y-m-d H:i:s');
            $bf->InsertSQL($GLOBALS['user_table'], $data, 'user_id', 'unique_number', 'ADD USER');
            echo json_encode(['status' => 'success', 'message' => 'User created successfully!']);
        } else {
            // Existing user update
            $bf->UpdateSQL($GLOBALS['user_table'], $data, "id = :id", [':id' => $view_user_id], 'UPDATE USER');
            echo json_encode(['status' => 'success', 'message' => 'User updated successfully!']);
        }
    } else {
        echo json_encode(['status' => 'error', 'errors' => $errors]);
    }
    exit();
}

if ($action == 'list') {
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
    $search = isset($_POST['search']) ? $_POST['search'] : '';
    $start = ($page - 1) * $limit;

    $result = $bf->getTableList($GLOBALS['user_table'], ['name', 'username', 'mobile'], $start, $limit, $search);
    // print_r($result);
    $users = $result['data'];
    $total_records = $result['total_records'];
    $total_pages = ceil($total_records / $limit);

    if (empty($users)) { ?>
        <div class="table-responsive">
            <table><tr><td style="text-align:center">No users found.</td></tr></table>
        </div>
    <?php } else {
        ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Sno</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Mobile</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $sno = $start + 1;
                foreach ($users as $u) { 
                ?>
                    <tr>
                        <td><?php echo $sno++; ?></td>
                        <td><strong style="color: var(--primary);"><?php echo $u['name']; ?></strong></td>
                        <td><?php echo $u['username']; ?></td>
                        <td><?php echo $u['mobile']; ?></td>
                        <td><span class="status-badge" style="background: var(--primary-light); color: var(--primary);"><?php echo ucfirst($u['role']); ?></span></td>
                        <td>
                            <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; background: #3b82f6; margin-right: 5px;" onclick="ShowPage('user', '<?php echo $u['id']; ?>')">Edit</button>
                            <!-- <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; background: #ef4444;" onclick="deleteRecord('user', '<?php echo $u['id']; ?>')">Delete</button> -->
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="pagination-container">
            <div class="pagination-info">
                Showing <?php echo ($total_records > 0) ? $start + 1 : 0; ?> to <?php echo min($start + $limit, $total_records); ?> of <?php echo $total_records; ?> entries
            </div>
            <div class="pagination-buttons">
                <button class="page-btn" <?php echo ($page <= 1) ? 'disabled' : ''; ?> onclick="loadData('user', <?php echo $page - 1; ?>, $('#user_limit').val(), $('#user_search').val())">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <?php 
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $start_page + 4);
                if ($end_page - $start_page < 4) $start_page = max(1, $end_page - 4);
                for ($i = $start_page; $i <= $end_page; $i++) { ?>
                    <button class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>" onclick="loadData('user', <?php echo $i; ?>, $('#user_limit').val(), $('#user_search').val())">
                        <?php echo $i; ?>
                    </button>
                <?php } ?>
                <button class="page-btn" <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?> onclick="loadData('user', <?php echo $page + 1; ?>, $('#user_limit').val(), $('#user_search').val())">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    <?php
    }
    exit;
}

if ($action == 'delete') {
    $id = $bf->sanitize($_POST['id'] ?? '');
    if ($id == $_SESSION['user_id']) {
        exit("Cannot delete currently logged-in user account.");
    }
    $bf->UpdateSQL($GLOBALS['user_table'], ['deleted' => 1], "id = :id", [':id' => $id]);
    echo "Success";
}
?>
