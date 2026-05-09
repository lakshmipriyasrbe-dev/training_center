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
    $query = "SELECT r.*, u.name as user_name, u.role as user_role FROM " . $GLOBALS['report_table'] . " r 
              JOIN " . $GLOBALS['user_table'] . " u ON r.user_id = u.id 
              WHERE r.deleted = :deleted";
    $params = [':deleted' => 0];

    if ($user_role != 'admin') {
        $query .= " AND r.user_id = :user_id";
        $params[':user_id'] = $user_id;
    }
    
    $query .= " ORDER BY r.report_date DESC";

    $reports = $bf->getQueryRecords($query, $params);

    if (empty($reports)) {
        echo "<p style='color: var(--text-muted);'>No reports found.</p>";
    } else {
        echo "<table style='width: 100%; border-collapse: collapse;'>
                <tr style='text-align: left; color: var(--text-muted); border-bottom: 1px solid rgba(255,255,255,0.1);'>
                    <th style='padding: 1rem;'>Date</th>
                    <th>User</th>
                    <th>Role</th>
                    <th>Activity</th>
                    <th>Hours</th>
                </tr>";
        foreach ($reports as $r) {
            echo "<tr style='border-bottom: 1px solid rgba(255,255,255,0.05);'>
                    <td style='padding: 1rem;'>" . $r['report_date'] . "</td>
                    <td>" . $r['user_name'] . "</td>
                    <td>" . ucfirst($r['user_role']) . "</td>
                    <td style='max-width: 300px;'>" . nl2br($r['activity_details']) . "</td>
                    <td>" . $r['hours_spent'] . "</td>
                </tr>";
        }
        echo "</table>";
    }
}
?>
