<?php
require_once 'common_file.php';
$action = $_REQUEST['action'] ?? '';

if ($action == 'add') {
    $report_date = $bf->sanitize($_POST['report_date'] ?? '');
    $activity_details = $bf->sanitize($_POST['activity_details'] ?? '');
    $hours_spent = $bf->sanitize($_POST['hours_spent'] ?? '');

    $errors = [];
    $res = $valid->common_validation($report_date, 'Date', '');
    if ($res) $errors['report_date'] = $res;
    $res = $valid->common_validation($activity_details, 'Activity Details', '');
    if ($res) $errors['activity_details'] = $res;
    $res = $valid->common_validation($hours_spent, 'Hours Spent', '');
    if ($res) $errors['hours_spent'] = $res;

    if (empty($errors)) {
        $data = [
            'user_id' => $user_id,
            'report_date' => $report_date,
            'activity_details' => $activity_details,
            'hours_spent' => $hours_spent,
            'created_date_time' => date('Y-m-d H:i:s'),
            'updated_date_time' => date('Y-m-d H:i:s')
        ];
        $bf->InsertSQL($GLOBALS['report_table'], $data, 'custom_id', 'unique_number', 'ADD REPORT');
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

    $query = "FROM " . $GLOBALS['report_table'] . " r 
              JOIN " . $GLOBALS['user_table'] . " u ON r.user_id = u.id 
              WHERE r.deleted = :deleted";
    $params = [':deleted' => 0];

    if ($user_role != 'admin') {
        $query .= " AND r.user_id = :user_id";
        $params[':user_id'] = $user_id;
    }

    if (!empty($search)) {
        $query .= " AND (u.name LIKE :search OR r.activity_details LIKE :search)";
        $params[':search'] = "%$search%";
    }

    // Get total count
    $count_query = "SELECT COUNT(*) as total " . $query;
    $total_records = $bf->getQueryRecords($count_query, $params)[0]['total'];
    $total_pages = ceil($total_records / $limit);

    // Get paginated data
    $data_query = "SELECT r.*, u.name as user_name, u.role as user_role " . $query . " ORDER BY r.report_date DESC LIMIT $start, $limit";
    $reports = $bf->getQueryRecords($data_query, $params);

    if (empty($reports)) { ?>
        <div class="table-responsive">
            <table><tr><td style="text-align:center">No reports found.</td></tr></table>
        </div>
    <?php } else {
        ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Activity</th>
                        <th>Hours</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                foreach ($reports as $r) { 
                ?>
                    <tr>
                        <td><?php echo date('d-m-Y', strtotime($r['report_date'])); ?></td>
                        <td><strong style="color: var(--primary);"><?php echo $r['user_name']; ?></strong></td>
                        <td><span class="status-badge" style="background: var(--primary-light); color: var(--primary);"><?php echo ucfirst($r['user_role']); ?></span></td>
                        <td style="max-width: 300px; white-space: normal; line-height: 1.4;"><?php echo nl2br($r['activity_details']); ?></td>
                        <td><?php echo $r['hours_spent']; ?> hrs</td>
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
                <button class="page-btn" <?php echo ($page <= 1) ? 'disabled' : ''; ?> onclick="loadData('report', <?php echo $page - 1; ?>, $('#report_limit').val(), $('#report_search').val())">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <?php 
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $start_page + 4);
                if ($end_page - $start_page < 4) $start_page = max(1, $end_page - 4);
                for ($i = $start_page; $i <= $end_page; $i++) { ?>
                    <button class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>" onclick="loadData('report', <?php echo $i; ?>, $('#report_limit').val(), $('#report_search').val())">
                        <?php echo $i; ?>
                    </button>
                <?php } ?>
                <button class="page-btn" <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?> onclick="loadData('report', <?php echo $page + 1; ?>, $('#report_limit').val(), $('#report_search').val())">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    <?php
    }
    exit;
}
?>
