<?php require_once 'common_file.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks - <?php echo get_company_name(); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <h2>Task Management</h2>
        </div>

        <div class="module-section">
            <div class="section-title">
                All Tasks
                <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'tasks', PERMISSION_ADD)): ?>
                <button class="btn-add" onclick="window.location.href='add_task.php'">Create New Task</button>
                <?php endif; ?>
            </div>
            <div class="list-controls">
                <div class="entries-control">
                    Show 
                    <select id="task_limit" onchange="loadData('task', 1, this.value, $('#task_search').val())">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    entries
                </div>
                <div class="search-control">
                    <i class="fas fa-search"></i>
                    <input type="text" id="task_search" placeholder="Search tasks..." onkeyup="loadData('task', 1, $('#task_limit').val(), this.value)">
                </div>
            </div>

            <div id="task_list">
                <p style="text-align:center; padding: 2rem; color: var(--text-muted);">Loading Tasks...</p>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            loadData('task');
        });

        function updateStatus(taskId, status) {
            $.post('task_action.php', { action: 'update_status', id: taskId, status: status }, function() {
                loadData('task');
            });
        }
    </script>
    <script src="main/js/script.js"></script>
</body>
</html>
