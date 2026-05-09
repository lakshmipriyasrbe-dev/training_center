<?php
require_once 'common_file.php';

if ($user_role != 'admin') { exit('Unauthorized'); }

$action = $_REQUEST['action'] ?? '';

if ($action == 'add') {
    $role_name = $bf->sanitize($_POST['role_name'] ?? '');
    $description = $bf->sanitize($_POST['description'] ?? '');

    if (!empty($role_name)) {
        // Check uniqueness
        $stmt = $bf->con->prepare("SELECT id FROM " . $GLOBALS['role_table'] . " WHERE role_name = :name AND deleted = 0");
        $stmt->execute([':name' => strtolower($role_name)]);
        if ($stmt->fetch()) {
            exit("Error: Role name already exists");
        }

        $data = [
            'role_name' => strtolower($role_name),
            'description' => $description,
            'created_date_time' => date('Y-m-d H:i:s')
        ];
        $bf->InsertSQL($GLOBALS['role_table'], $data,'role_id', '', 'ADD ROLE');
        echo "Success";
    }
}

if ($action == 'list') {
    $roles = $bf->getTableRecords($GLOBALS['role_table'], 'deleted', 0);
    if (empty($roles)) {
        echo "<p>No roles defined.</p>";
    } else {
        echo "<table>
                <tr>
                    <th>Role Name</th>
                    <th>Description</th>
                    <th>Created At</th>
                </tr>";
        foreach ($roles as $r) {
            echo "<tr>
                    <td><strong style='color: var(--primary);'>" . ucfirst($r['role_name']) . "</strong></td>
                    <td>" . $r['description'] . "</td>
                    <td style='color: var(--text-muted);'>" . $r['created_date_time'] . "</td>
                  </tr>";
        }
        echo "</table>";
    }
}
?>
