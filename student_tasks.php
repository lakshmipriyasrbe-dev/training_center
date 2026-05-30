<?php 
require_once 'common_file.php'; 
$from_page = 'student_tasks';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Tasks - <?php echo get_company_name(); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Modern styles for comments and progress modals */
        .comments-box {
            display: flex;
            flex-direction: column;
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            padding: 1rem;
            background: #f8fafc;
            margin-bottom: 1rem;
        }
        .comments-box::-webkit-scrollbar {
            width: 5px;
        }
        .comments-box::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .comment-input-area {
            display: flex;
            gap: 0.5rem;
        }
        .comment-input-area textarea {
            flex-grow: 1;
            resize: none;
            padding: 0.5rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 0.5rem;
            font-family: inherit;
            font-size: 0.85rem;
        }
        .comment-input-area textarea:focus {
            border-color: var(--primary);
            outline: none;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <!-- Main Table View -->
    <div class="main-content update_content">
        <div class="header">
            <h2>Student Tasks</h2>
            <div class="user-profile">
                <span><?php echo $username; ?></span>
                <div class="avatar"><?php echo substr($username, 0, 1); ?></div>
            </div>
        </div>

        <div class="module-section">
            <div class="section-title">
                Assigned Student Tasks
                <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'student_tasks', PERMISSION_ADD)): ?>
                    <button class="btn-add" onclick="ShowPage('student_tasks', '')">Assign Task</button>
                <?php endif; ?>
            </div>

            <div class="list-controls">
                <div class="entries-control">
                    Show 
                    <select id="student_tasks_limit" onchange="loadData('student_tasks', 1, this.value, $('#student_tasks_search').val())">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    entries
                </div>
                <div class="search-control">
                    <i class="fas fa-search"></i>
                    <input type="text" id="student_tasks_search" placeholder="Search tasks..." onkeyup="loadData('student_tasks', 1, $('#student_tasks_limit').val(), this.value)">
                </div>
            </div>

            <div id="student_tasks_list">
                <p style="text-align:center; padding: 2rem; color: var(--text-muted);">Loading Student Tasks...</p>
            </div>
        </div>
    </div>

    <!-- Form Add/Edit View Container (Dynamically populated via ShowPage) -->
    <div class="main-content new_content" style="display: none;">
    </div>

    <!-- Student Update Progress Modal -->
    <div id="progressModal" class="modal">
        <div class="modal-content" style="max-width: 450px;">
            <h3 style="margin-top: 0; margin-bottom: 1.5rem; color: var(--primary);">Update Task Progress</h3>
            <form id="progress_form" onsubmit="submitProgress(event)">
                <input type="hidden" name="action" value="update_progress">
                <input type="hidden" id="progress_task_id" name="id">
                
                <div class="form-group">
                    <label>Completion Percentage (0-100) *</label>
                    <input type="number" id="progress_percent" name="completion_percentage" class="form-input" min="0" max="100" required>
                </div>
                
                <div class="form-group">
                    <label>Status *</label>
                    <select id="progress_status" name="status" class="form-input" required>
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                        <option value="Delayed">Delayed</option>
                    </select>
                </div>

                <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 1.5rem;">
                    <button type="button" class="btn-add" style="background: #64748b;" onclick="$('#progressModal').hide()">Cancel</button>
                    <button type="submit" class="btn-add">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Comments / Discussion Chat Modal -->
    <div id="commentsModal" class="modal">
        <div class="modal-content" style="max-width: 550px;">
            <h3 style="margin-top: 0; margin-bottom: 0.5rem; color: var(--primary);" id="comments_task_title">Task Discussion</h3>
            <p style="font-size:0.8rem; color:var(--text-muted); margin-bottom: 1rem; margin-top: 0;">Post queries or status updates about the task directly.</p>
            
            <div class="comments-box" id="comments_container">
                <p style="text-align:center; color:var(--text-muted); font-size:0.85rem;">Loading discussion...</p>
            </div>

            <form id="comment_form" onsubmit="submitComment(event)">
                <input type="hidden" id="comment_task_id" name="task_id">
                <input type="hidden" name="action" value="add_comment">
                <div class="comment-input-area">
                    <textarea name="comment" id="comment_text" rows="2" placeholder="Write a comment or update..." required></textarea>
                    <button type="submit" class="btn-add" style="display: flex; align-items: center; justify-content: center; height: auto; padding: 0 1.25rem;"><i class="fas fa-paper-plane"></i></button>
                </div>
            </form>

            <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem;">
                <button type="button" class="btn-add" style="background: #64748b;" onclick="$('#commentsModal').hide()">Close</button>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            loadData('student_tasks');
        });

        // Student Progress Modal handlers
        function updateProgressModal(id, current_percent, current_status) {
            $('#progress_task_id').val(id);
            $('#progress_percent').val(current_percent);
            $('#progress_status').val(current_status);
            $('#progressModal').css('display', 'flex');
        }

        function submitProgress(e) {
            e.preventDefault();
            $.post('student_tasks_action.php', $('#progress_form').serialize(), function(response) {
                try {
                    let res = JSON.parse(response);
                    if (res.status === 'success') {
                        $('#progressModal').hide();
                        loadData('student_tasks');
                    } else {
                        alert(res.message || 'Error updating progress');
                    }
                } catch(err) {
                    alert('Server error occurred.');
                }
            });
        }

        // Task discussion board handlers
        function openComments(id, title) {
            $('#comment_task_id').val(id);
            $('#comments_task_title').text('Discussion: ' + title);
            $('#commentsModal').css('display', 'flex');
            loadComments(id);
        }

        function loadComments(taskId) {
            $('#comments_container').html('<p style="text-align:center; color:var(--text-muted); font-size:0.85rem; padding:1rem;">Loading conversation...</p>');
            $.post('student_tasks_action.php', { action: 'get_comments', task_id: taskId }, function(html) {
                $('#comments_container').html(html);
                // Scroll to bottom
                let box = document.getElementById('comments_container');
                box.scrollTop = box.scrollHeight;
            });
        }

        function submitComment(e) {
            e.preventDefault();
            let comment = $('#comment_text').val().trim();
            if(comment === '') return;
            
            let taskId = $('#comment_task_id').val();
            $.post('student_tasks_action.php', $('#comment_form').serialize(), function(response) {
                try {
                    let res = JSON.parse(response);
                    if(res.status === 'success') {
                        $('#comment_text').val('');
                        loadComments(taskId);
                    } else {
                        alert(res.message || 'Error sending comment');
                    }
                } catch(err) {
                    loadComments(taskId);
                    $('#comment_text').val('');
                }
            });
        }
    </script>
    <script src="main/js/script.js"></script>
    <script src="main/js/keyboard_control.js"></script>
</body>
</html>
