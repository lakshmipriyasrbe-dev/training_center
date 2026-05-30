<?php
require_once 'common_file.php';
$action = $_REQUEST['action'] ?? '';

if ($action == 'add' && checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'tasks', PERMISSION_ADD)) {
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
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
    $search = isset($_POST['search']) ? $_POST['search'] : '';
    $start = ($page - 1) * $limit;

    $query = "FROM " . $GLOBALS['task_table'] . " t 
              LEFT JOIN " . $GLOBALS['user_table'] . " u ON t.assigned_to = u.id 
              LEFT JOIN " . $GLOBALS['staff_table'] . " s ON t.assigned_to = s.staff_id 
              WHERE t.deleted = :deleted AND t.company_id = :comp_id";
    $params = [':deleted' => 0, ':comp_id' => $_SESSION['company_id']];

    $can_manage_all = ($user_role == 'admin') || checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'tasks', PERMISSION_ADD);
    if (!$can_manage_all) {
        $query .= " AND t.assigned_to = :user_id";
        $params[':user_id'] = $user_id;
    }

    if (!empty($search)) {
        $query .= " AND (t.title LIKE :search OR t.description LIKE :search)";
        $params[':search'] = "%$search%";
    }

    // Get total count
    $count_query = "SELECT COUNT(*) as total " . $query;
    $total_records = $bf->getQueryRecords($count_query, $params)[0]['total'];
    $total_pages = ceil($total_records / $limit);

    // Get paginated data
    $data_query = "SELECT t.*, u.name AS assignee_name, s.staff_name AS staff_name " . $query . " ORDER BY t.created_date_time DESC LIMIT $start, $limit";
    $tasks = $bf->getQueryRecords($data_query, $params);

    if (empty($tasks)) { ?>
        <div class="table-responsive">
            <table><tr><td style="text-align:center">No tasks found.</td></tr></table>
        </div>
    <?php } else {
        ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Assigned From</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                foreach ($tasks as $t) { 
                    $assignee_name = $bf->getTableColumnValue($GLOBALS['user_table'], 'user_id', $t['assigned_by'], 'name');
                    $status_color = ($t['status'] == 'completed') ? '#10b981' : (($t['status'] == 'in_progress') ? '#6366f1' : '#fbbf24');
                ?>
                    <tr>
                        <td><?php echo $t['id']; ?></td>
                        <td><strong style="color: var(--primary);"><?php echo $t['title']; ?></strong></td>
                        <td><?php echo $assignee_name; ?></td>
                        <td><?php echo date('d-m-Y', strtotime($t['due_date'])); ?></td>
                        <td><span class="status-badge" style="background: <?php echo $status_color; ?>15; color: <?php echo $status_color; ?>;"><?php echo ucfirst(str_replace('_', ' ', $t['status'])); ?></span></td>
                        <td>
                            <?php if ($user_role != 'admin' && $t['status'] != 'completed') {
                                $next_status = ($t['status'] == 'pending') ? 'in_progress' : 'completed';
                            ?>
                                <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;" onclick="updateStatus(<?php echo $t['id']; ?>, '<?php echo $next_status; ?>')">
                                    Mark as <?php echo ucfirst(str_replace('_', ' ', $next_status)); ?>
                                </button>
                            <?php } ?>
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
                <button class="page-btn" <?php echo ($page <= 1) ? 'disabled' : ''; ?> onclick="loadData('task', <?php echo $page - 1; ?>, $('#task_limit').val(), $('#task_search').val())">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <?php 
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $start_page + 4);
                if ($end_page - $start_page < 4) $start_page = max(1, $end_page - 4);
                for ($i = $start_page; $i <= $end_page; $i++) { ?>
                    <button class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>" onclick="loadData('task', <?php echo $i; ?>, $('#task_limit').val(), $('#task_search').val())">
                        <?php echo $i; ?>
                    </button>
                <?php } ?>
                <button class="page-btn" <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?> onclick="loadData('task', <?php echo $page + 1; ?>, $('#task_limit').val(), $('#task_search').val())">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    <?php
    }
    exit;
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
