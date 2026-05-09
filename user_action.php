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
    $users = $bf->getTableRecords($GLOBALS['user_table'], 'deleted', 0);
    if (empty($users)) {
        echo "<p>No users found.</p>";
    } else {
        echo "<table>
                <tr>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Mobile</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>";
        foreach ($users as $u) {
            echo "<tr>
                    <td>" . $u['name'] . "</td>
                    <td>" . $u['username'] . "</td>
                    <td>" . $u['mobile'] . "</td>
                    <td><span style='color: var(--primary);'>" . ucfirst($u['role']) . "</span></td>
                    <td>
                        <button class='btn-add' style='background: #ef4444; font-size: 0.75rem;' onclick='deleteUser(" . $u['id'] . ")'>Delete</button>
                    </td>
                  </tr>";
        }
        echo "</table>";
    }
}

if ($action == 'delete') {
    $id = $bf->sanitize($_POST['id'] ?? '');
    $bf->UpdateSQL($GLOBALS['user_table'], ['deleted' => 1], "id = :id", [':id' => $id]);
    echo "Success";
}
?>
