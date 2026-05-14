<?php
require_once 'common_file.php';

if ($user_role != 'admin') { exit('Unauthorized'); }

$action = $_REQUEST['action'] ?? '';

if ($action == 'add' && $user_role == 'admin') {
    $name = $bf->sanitize($_POST['name'] ?? '');
    $mobile = $bf->sanitize($_POST['mobile'] ?? '');
    $username = $bf->sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $bf->sanitize($_POST['role'] ?? 'staff');

    $errors = [];
    $err = $valid->valid_name($name, "Full Name");
    if ($err) $errors['name'] = $err;
    $err = $valid->valid_mobile($mobile, "Mobile Number");
    if ($err) $errors['mobile'] = $err;
    
    if (empty($username)) {
        $errors['username'] = "Enter Username";
    } else {
        // Check uniqueness
        $stmt = $bf->con->prepare("SELECT id FROM " . $GLOBALS['user_table'] . " WHERE (username = :username OR mobile = :mobile) AND deleted = 0");
        $stmt->execute([':username' => $username, ':mobile' => $mobile]);
        if ($stmt->fetch()) {
            $errors['username'] = "Username or Mobile already exists";
        }
    }

    $err = $valid->valid_password($password, "Password");
    if ($err) $errors['password'] = $err;

    if (empty($errors)) {
        $data = [
            'name' => $name,
            'mobile' => $mobile,
            'username' => $username,
            'password' => $bf->encode_decode('encrypt', $password),
            'role' => $role,
            'created_date_time' => date('Y-m-d H:i:s'),
            'updated_date_time' => date('Y-m-d H:i:s')
        ];
        $bf->InsertSQL($GLOBALS['user_table'], $data, 'user_id', 'unique_number', 'ADD USER');
        echo json_encode(['status' => 'success']);
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
                            <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; background: #ef4444;" onclick="deleteRecord('user', '<?php echo $u['id']; ?>')">Delete</button>
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
    $bf->UpdateSQL($GLOBALS['user_table'], ['deleted' => 1], "id = :id", [':id' => $id]);
    echo "Success";
}
?>
