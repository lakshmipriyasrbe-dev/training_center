<?php
require_once 'common_file.php';
$action = $_REQUEST['action'] ?? '';

if ($action == 'add' && $user_role == 'admin') {
    $title = $bf->sanitize($_POST['title'] ?? '');
    $description = $bf->sanitize($_POST['description'] ?? '');
    $assigned_to = $bf->sanitize($_POST['assigned_to'] ?? '');
    $due_date = $bf->sanitize($_POST['due_date'] ?? '');

    $errors = [];
    $res = $valid->common_validation($title, 'Title', '');
    if ($res) $errors['title'] = $res;

    if (empty($errors)) {
        $data = [
            'title' => $title,
            'description' => $description,
            'assigned_to' => $assigned_to,
            'assigned_by' => $user_id,
            'due_date' => $due_date,
            'status' => 'pending',
            'created_date_time' => date('Y-m-d H:i:s'),
            'updated_date_time' => date('Y-m-d H:i:s')
        ];
        $bf->InsertSQL($GLOBALS['task_table'], $data, 'custom_id', 'unique_number', 'ADD TASK');
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'errors' => $errors]);
    }
}

if ($action == 'list') {
    $query = "SELECT t.*, u.name AS assignee_name, s.staff_name AS staff_name          
          FROM " . $GLOBALS['task_table'] . " t LEFT JOIN " . $GLOBALS['user_table'] . " u 
                ON t.assigned_to = u.id LEFT JOIN " . $GLOBALS['staff_table'] . " s
                ON t.assigned_to = s.staff_id WHERE t.deleted = :deleted";

        $params = [':deleted' => 0];

    if ($user_role != 'admin') {
        $query .= " AND t.assigned_to = :user_id";
        $params[':user_id'] = $user_id;
    }

    // echo $bf->debugQuery($query, $params); // Debugging line to show the final query with parameters;

    $tasks = $bf->getQueryRecords($query, $params);

    if (empty($tasks)) {
        echo "<p style='color: var(--text-muted);'>No tasks found.</p>";
    } else {
        echo "<table style='width: 100%; border-collapse: collapse;'>
                <tr style='text-align: left; color: var(--text-muted); border-bottom: 1px solid rgba(255,255,255,0.1);'>
                    <th style='padding: 1rem;'>ID</th>
                    <th>Title</th>
                    <th>Assigned From</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>";
        foreach ($tasks as $t) {
            $assignee_name = $bf->getTableColumnValue($GLOBALS['user_table'], 'user_id', $t['assigned_by'], 'name');
            $status_color = ($t['status'] == 'completed') ? '#10b981' : (($t['status'] == 'in_progress') ? '#6366f1' : '#fbbf24');
            echo "<tr style='border-bottom: 1px solid rgba(255,255,255,0.05);'>
                    <td style='padding: 1rem;'>" . $t['id'] . "</td>
                    <td>" . $t['title'] . "</td>
                    <td>" . $assignee_name . "</td>
                    <td>" . $t['due_date'] . "</td>
                    <td><span style='color: $status_color;'>" . ucfirst($t['status']) . "</span></td>
                    <td>";
            if ($user_role != 'admin' && $t['status'] != 'completed') {
                $next_status = ($t['status'] == 'pending') ? 'in_progress' : 'completed';
                echo "<button class='btn-add' style='font-size: 0.75rem;' onclick='updateStatus(" . $t['id'] . ", \"$next_status\")'>Mark as " . ucfirst(str_replace('_', ' ', $next_status)) . "</button>";
            }
            echo "</td></tr>";
        }
        echo "</table>";
    }
}

if ($action == 'get_users') {
    $users = $bf->getTableRecords($GLOBALS['staff_table'], 'deleted', 0);
    if(!empty($users)) { ?>
        <option value="">Select Staff</option>
    <?php
    foreach ($users as $u) { ?>
            
            <option value="<?php echo $u['staff_id']; ?>"><?php echo $u['staff_name']; ?></option>
           
    <?php }
    } else {
        echo "<option value=''>No staff found</option>";
    }
}

if ($action == 'update_status') {
    $id = $bf->sanitize($_POST['id'] ?? '');
    $status = $bf->sanitize($_POST['status'] ?? '');
    
    $bf->UpdateSQL($GLOBALS['task_table'], ['status' => $status], "id = :id", [':id' => $id]);
    echo json_encode(['status' => 'success']);
}
?>
