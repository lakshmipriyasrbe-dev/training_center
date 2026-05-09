<?php require_once 'common_file.php'; 

// Fetch Stats
$today = date('Y-m-d');
$pending_count = $bf->getQueryRecords("SELECT COUNT(*) as total FROM " . $GLOBALS['task_table'] . " WHERE status != 'completed' AND deleted = 0")[0]['total'];
$completed_count = $bf->getQueryRecords("SELECT COUNT(*) as total FROM " . $GLOBALS['task_table'] . " WHERE status = 'completed' AND deleted = 0")[0]['total'];
$report_count = $bf->getQueryRecords("SELECT COUNT(*) as total FROM " . $GLOBALS['report_table'] . " WHERE deleted = 0")[0]['total'];

// Fetch Today's Tasks
if ($user_role == 'admin') {
    $recent_tasks_query = "SELECT t.*, u.name as assignee_name FROM " . $GLOBALS['task_table'] . " t 
                           LEFT JOIN " . $GLOBALS['user_table'] . " u ON t.assigned_to = u.id 
                           WHERE t.deleted = 0 AND t.due_date = :today ORDER BY t.id DESC";
    $params = [':today' => $today];
} else {
    $recent_tasks_query = "SELECT t.*, u.name as assignee_name FROM " . $GLOBALS['task_table'] . " t 
                           LEFT JOIN " . $GLOBALS['user_table'] . " u ON t.assigned_to = u.id 
                           WHERE t.deleted = 0 AND t.assigned_to = :user_id AND t.due_date = :today ORDER BY t.id DESC";
    $params = [':today' => $today, ':user_id' => $user_id];
}
$recent_tasks = $bf->getQueryRecords($recent_tasks_query, $params);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Training Center</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <div>
                <h2 style="margin: 0;">Welcome, <?php echo $full_name; ?></h2>
                <p style="color: var(--text-muted); margin: 0.5rem 0 0 0;">Role: <?php echo ucfirst($user_role); ?></p>
            </div>
            <div class="user-profile">
                <span><?php echo $username; ?></span>
                <div class="avatar"><?php echo substr($username, 0, 1); ?></div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Pending Tasks</div>
                <div class="stat-value"><?php echo $pending_count; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Tasks Completed</div>
                <div class="stat-value"><?php echo $completed_count; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Reports Submitted</div>
                <div class="stat-value"><?php echo $report_count; ?></div>
            </div>
        </div>

        <div class="module-section">
            <div class="section-title">
                <?php echo ($user_role == 'admin') ? "All Employee Tasks (Today)" : "My Tasks (Today)"; ?>
                <button class="btn-add" onclick="window.location.href='tasks.php'">View All</button>
            </div>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; color: var(--text-muted); border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <th style="padding: 1rem 0;">Task Title</th>
                        <th>Assigned To</th>
                        <th>Due Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recent_tasks)): ?>
                    <tr><td colspan="4" style="padding: 1rem 0; text-align: center; color: var(--text-muted);">No tasks found.</td></tr>
                    <?php else: ?>
                    <?php foreach ($recent_tasks as $t): 
                        $assignee_name = $bf->getTableColumnValue($GLOBALS['staff_table'], 'staff_id', $t['assigned_to'], 'staff_name');    
                        $status_color = ($t['status'] == 'completed') ? '#10b981' : (($t['status'] == 'in_progress') ? '#06b6d4' : '#fbbf24');
                    ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td style="padding: 1rem 0;"><?php echo $t['title']; ?></td>
                        <td><?php echo $assignee_name ?: 'Unassigned'; ?></td>
                        <td><?php echo $t['due_date']; ?></td>
                        <td><span style="color: <?php echo $status_color; ?>;"><?php echo ucfirst($t['status']); ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
