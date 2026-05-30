<?php
require_once 'common_file.php';

if ($user_role != 'admin' && !$is_management) { exit('Unauthorized'); }

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
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
    $search = isset($_POST['search']) ? $_POST['search'] : '';
    $start = ($page - 1) * $limit;

    $result = $bf->getTableList($GLOBALS['role_table'], ['role_name', 'description'], $start, $limit, $search);
    $roles = $result['data'];
    $total_records = $result['total_records'];
    $total_pages = ceil($total_records / $limit);

    if (empty($roles)) { ?>
        <div class="table-responsive">
            <table><tr><td style="text-align:center">No roles found.</td></tr></table>
        </div>
    <?php } else {
        ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Sno</th>
                        <th>Role Name</th>
                        <th>Description</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $sno = $start + 1;
                foreach ($roles as $u) { 
                ?>
                    <tr>
                        <td><?php echo $sno++; ?></td>
                        <td><strong style="color: var(--primary);"><?php echo ucfirst($u['role_name']); ?></strong></td>
                        <td><?php echo $u['description']; ?></td>
                        <td style="color: var(--text-muted);"><?php echo date('d-m-Y H:i', strtotime($u['created_date_time'])); ?></td>
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
                <button class="page-btn" <?php echo ($page <= 1) ? 'disabled' : ''; ?> onclick="loadData('role', <?php echo $page - 1; ?>, $('#role_limit').val(), $('#role_search').val())">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <?php 
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $start_page + 4);
                if ($end_page - $start_page < 4) $start_page = max(1, $end_page - 4);
                for ($i = $start_page; $i <= $end_page; $i++) { ?>
                    <button class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>" onclick="loadData('role', <?php echo $i; ?>, $('#role_limit').val(), $('#role_search').val())">
                        <?php echo $i; ?>
                    </button>
                <?php } ?>
                <button class="page-btn" <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?> onclick="loadData('role', <?php echo $page + 1; ?>, $('#role_limit').val(), $('#role_search').val())">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    <?php
    }
    exit;
}
?>
