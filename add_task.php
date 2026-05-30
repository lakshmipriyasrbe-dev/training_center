<?php require_once 'common_file.php'; 
if (!checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'tasks', PERMISSION_ADD)) { header("Location: dashboard.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Task - <?php echo get_company_name(); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <h2>Create New Task</h2>
        </div>

        <div class="module-section" style="max-width: 600px;">
            <form id="taskForm">
                <div class="form-group">
                    <label>Task Title</label>
                    <input type="text" name="title" placeholder="What needs to be done?" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="5" placeholder="Provide more details..."></textarea>
                </div>
                <div class="form-group">
                    <label>Assign To</label>
                    <select name="assigned_to" id="userSelect" required>
                        <option value="">Loading users...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Due Date</label>
                    <input type="date" name="due_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn-add" style="flex: 1;">Save Task</button>
                    <button type="button" class="btn-add" style="flex: 1; background: #64748b;" onclick="window.location.href='tasks.php'">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $.get('task_action.php?action=get_users', function(data) {
                $('#userSelect').html(data);
            });

            $('#taskForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'task_action.php',
                    type: 'POST',
                    data: $(this).serialize() + '&action=add',
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            window.location.href = 'tasks.php';
                        } else {
                            alert(res.message || 'Error saving task');
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
