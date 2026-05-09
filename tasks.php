<?php require_once 'common_file.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks - Training Center</title>
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
                <?php if ($user_role == 'admin'): ?>
                <button class="btn-add" onclick="window.location.href='add_task.php'">Create New Task</button>
                <?php endif; ?>
            </div>
            <div id="taskList">
                <p>Loading tasks...</p>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            loadTasks();
        });

        function loadTasks() {
            $.get('task_action.php?action=list', function(data) {
                $('#taskList').html(data);
            });
        }

        function updateStatus(taskId, status) {
            $.post('task_action.php', { action: 'update_status', id: taskId, status: status }, function() {
                loadTasks();
            });
        }
    </script>
</body>
</html>
