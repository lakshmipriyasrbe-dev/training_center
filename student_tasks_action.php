<?php
require_once 'common_file.php';

$action = $_REQUEST['action'] ?? '';

if ($action == 'add' && (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'student_tasks', PERMISSION_ADD) || checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'student_tasks', PERMISSION_EDIT))) {
    $task_title = $bf->sanitize($_POST['task_title'] ?? '');
    $description = $bf->sanitize($_POST['description'] ?? '');
    $assigned_to_student = $bf->sanitize($_POST['assigned_to_student'] ?? '');
    $start_date = $bf->sanitize($_POST['start_date'] ?? '');
    $due_date = $bf->sanitize($_POST['due_date'] ?? '');
    $priority = $bf->sanitize($_POST['priority'] ?? 'Medium');
    $view_task_id = $bf->sanitize($_POST['view_task_id'] ?? '');

    $errors = [];
    if (empty($task_title)) {
        $errors['task_title'] = 'Enter task title';
    }
    if (empty($assigned_to_student)) {
        $errors['assigned_to_student'] = 'Select a student';
    }
    if (empty($start_date)) {
        $errors['start_date'] = 'Select start date';
    }
    if (empty($due_date)) {
        $errors['due_date'] = 'Select due date';
    } elseif (!empty($start_date) && strtotime($due_date) < strtotime($start_date)) {
        $errors['due_date'] = 'Due date cannot be before start date';
    }

    // Attachment processing (mirroring expense_entry_action.php)
    $uploaded_files = [];
    if (empty($errors) && isset($_FILES['attachments'])) {
        $files = $_FILES['attachments'];
        $file_count = count($files['name']);
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'doc', 'docx'];
        
        for ($i = 0; $i < $file_count; $i++) {
            $file_name = $files['name'][$i];
            $file_tmp = $files['tmp_name'][$i];
            $file_error = $files['error'][$i];
            
            if ($file_error === UPLOAD_ERR_NO_FILE || empty($file_name)) {
                continue;
            }
            
            if ($file_error !== UPLOAD_ERR_OK) {
                $errors['attachments'] = 'Error uploading file: ' . $file_name;
                break;
            }
            
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            if (!in_array($file_ext, $allowed_extensions)) {
                $errors['attachments'] = 'Invalid file type: ' . $file_name . '. Allowed: ' . implode(', ', $allowed_extensions);
                break;
            }
            
            $unique_name = time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $file_name);
            $upload_path = 'upload/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            
            if (move_uploaded_file($file_tmp, $upload_path . $unique_name)) {
                $uploaded_files[] = $unique_name;
            } else {
                $errors['attachments'] = 'Failed to save uploaded file: ' . $file_name;
                break;
            }
        }
    }

    if (!empty($errors)) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'errors' => $errors]);
        exit;
    }

    // Get existing attachments if editing
    $existing_attachments = '';
    if (!empty($view_task_id)) {
        $task_record = $bf->getTableRecords($GLOBALS['student_tasks_table'], 'id', $view_task_id);
        if (!empty($task_record)) {
            $existing_attachments = $task_record[0]['attachments'] ?? '';
        }
    }
    
    $all_attachments = [];
    if (!empty($existing_attachments)) {
        $all_attachments = array_filter(array_map('trim', explode(',', $existing_attachments)));
    }
    $all_attachments = array_merge($all_attachments, $uploaded_files);

    $data = [
        'task_title' => $task_title,
        'description' => $description,
        'assigned_to_student' => $assigned_to_student,
        'start_date' => $start_date,
        'due_date' => $due_date,
        'priority' => $priority,
        'attachments' => implode(',', $all_attachments),
        'updated_date_time' => date('Y-m-d H:i:s')
    ];

    if (empty($view_task_id)) {
        $data['assigned_by'] = $user_id;
        $data['status'] = 'Pending';
        $data['completion_percentage'] = 0;
        $data['created_date_time'] = date('Y-m-d H:i:s');
        $data['task_id'] = $bf->automate_number($GLOBALS['student_tasks_table'], 'task_id', '', '');
        $bf->InsertSQL($GLOBALS['student_tasks_table'], $data);
        echo json_encode(['status' => 'success', 'message' => 'Task assigned successfully']);
    } else {
        $bf->UpdateSQL($GLOBALS['student_tasks_table'], $data, 'id = :id', [':id' => $view_task_id]);
        echo json_encode(['status' => 'success', 'message' => 'Task updated successfully']);
    }
    exit;
}

if ($action == 'list') {
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
    $search = isset($_POST['search']) ? $_POST['search'] : '';
    $start = ($page - 1) * $limit;

    $query = "FROM " . $GLOBALS['student_tasks_table'] . " t WHERE t.deleted = 0 AND t.company_id = :comp_id";
    $params = [':comp_id' => $_SESSION['company_id']];

    if ($user_role == 'student') {
        $query .= " AND t.assigned_to_student = :student_id";
        $params[':student_id'] = $username;
    } elseif ($user_role == 'staff') {
        // Staff can only see tasks they assigned or tasks of their assigned students
        $query_training = "SELECT student_id FROM " . $GLOBALS['enrollment_table'] . " WHERE staff_id = :staff_id AND deleted = 0";
        $query_intern = "SELECT student_id FROM " . $GLOBALS['enrollment_internship_table'] . " WHERE staff_id = :staff_id AND deleted = 0";
        
        $training_students = $bf->getQueryRecords($query_training, [':staff_id' => $user_id]);
        $intern_students = $bf->getQueryRecords($query_intern, [':staff_id' => $user_id]);
        
        $assigned_student_ids = ['__NONE__'];
        foreach ($training_students as $s) {
            $assigned_student_ids[] = $bf->encode_decode('decrypt', $s['student_id']);
        }
        foreach ($intern_students as $s) {
            $assigned_student_ids[] = $bf->encode_decode('decrypt', $s['student_id']);
        }

        $placeholders = [];
        foreach ($assigned_student_ids as $idx => $sid) {
            $key = ":sid_" . $idx;
            $placeholders[] = $key;
            $params[$key] = $sid;
        }

        $query .= " AND (t.assigned_by = :assigned_by OR t.assigned_to_student IN (" . implode(',', $placeholders) . "))";
        $params[':assigned_by'] = $user_id;
    }

    if (!empty($search)) {
        $query .= " AND (t.task_title LIKE :search OR t.description LIKE :search OR t.assigned_to_student LIKE :search)";
        $params[':search'] = "%$search%";
    }

    // Get total count
    $count_query = "SELECT COUNT(*) as total " . $query;
    $total_records = $bf->getQueryRecords($count_query, $params)[0]['total'];
    $total_pages = ceil($total_records / $limit);

    // Get paginated data
    $data_query = "SELECT t.* " . $query . " ORDER BY t.created_date_time DESC LIMIT $start, $limit";
    $tasks = $bf->getQueryRecords($data_query, $params);

    if (empty($tasks)) { ?>
        <div class="table-responsive">
            <table><tr><td style="text-align:center">No tasks found.</td></tr></table>
        </div>
    <?php } else { ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Task ID</th>
                        <th>Title</th>
                        <th>Student ID</th>
                        <th>Assigned By</th>
                        <th>Dates</th>
                        <th>Priority</th>
                        <th>Progress</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                foreach ($tasks as $t) { 
                    $assigner_name = 'Admin';
                    if ($t['assigned_by'] != 1) {
                        $assigner_name = $bf->getTableColumnValue($GLOBALS['staff_table'], 'staff_id', $t['assigned_by'], 'staff_name') ?: 'Staff';
                    }
                    $status_color = ($t['status'] == 'Completed') ? '#10b981' : (($t['status'] == 'In Progress') ? '#06b6d4' : (($t['status'] == 'Delayed') ? '#ef4444' : '#fbbf24'));
                    $priority_color = ($t['priority'] == 'High') ? '#ef4444' : (($t['priority'] == 'Medium') ? '#f59e0b' : '#10b981');
                ?>
                    <tr>
                        <td><strong><?php echo $t['task_id']; ?></strong></td>
                        <td>
                            <strong style="color: var(--primary);"><?php echo $t['task_title']; ?></strong>
                            <p style="margin:0.25rem 0 0 0; font-size:0.75rem; color:var(--text-muted); max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?php echo htmlspecialchars($t['description']); ?>">
                                <?php echo $t['description']; ?>
                            </p>
                        </td>
                        <td><span style="font-weight:600; color: #475569;"><?php echo $t['assigned_to_student']; ?></span></td>
                        <td><?php echo $assigner_name; ?></td>
                        <td>
                            <span style="font-size:0.75rem; display:block;">Start: <?php echo date('d-m-Y', strtotime($t['start_date'])); ?></span>
                            <span style="font-size:0.75rem; display:block; font-weight: 600; color: #ef4444;">Due: <?php echo date('d-m-Y', strtotime($t['due_date'])); ?></span>
                        </td>
                        <td><span class="status-badge" style="background: <?php echo $priority_color; ?>15; color: <?php echo $priority_color; ?>; font-weight:700;"><?php echo $t['priority']; ?></span></td>
                        <td>
                            <div style="width: 100px; background: #e2e8f0; height: 8px; border-radius: 4px; overflow: hidden; margin-bottom:0.25rem;">
                                <div style="width: <?php echo $t['completion_percentage']; ?>%; background: var(--primary); height: 100%;"></div>
                            </div>
                            <span style="font-size:0.75rem; font-weight:600;"><?php echo $t['completion_percentage']; ?>%</span>
                        </td>
                        <td><span class="status-badge" style="background: <?php echo $status_color; ?>15; color: <?php echo $status_color; ?>; font-weight:700;"><?php echo $t['status']; ?></span></td>
                        <td>
                            <div style="display:flex; gap:0.25rem; flex-wrap: wrap;">
                                <button class="btn-add" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: #6366f1;" onclick="openComments(<?php echo $t['id']; ?>, '<?php echo htmlspecialchars($t['task_title'], ENT_QUOTES); ?>')">
                                    <i class="fas fa-comments"></i> Chat
                                </button>
                                <?php if ($user_role == 'student') { ?>
                                    <button class="btn-add" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;" onclick="updateProgressModal(<?php echo $t['id']; ?>, <?php echo $t['completion_percentage']; ?>, '<?php echo $t['status']; ?>')">
                                        Update
                                    </button>
                                <?php } else { ?>
                                    <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'student_tasks', PERMISSION_EDIT)): ?>
                                        <button class="btn-add" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;" onclick="ShowPage('student_tasks', '<?php echo $t['id']; ?>')">
                                            Edit
                                        </button>
                                    <?php endif; ?>
                                    <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'student_tasks', PERMISSION_DELETE)): ?>
                                        <button class="btn-add" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: #ef4444;" onclick="deleteRecord('student_tasks', '<?php echo $t['id']; ?>')">
                                            Delete
                                        </button>
                                    <?php endif; ?>
                                <?php } ?>
                            </div>
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
                <button class="page-btn" <?php echo ($page <= 1) ? 'disabled' : ''; ?> onclick="loadData('student_tasks', <?php echo $page - 1; ?>, $('#student_tasks_limit').val(), $('#student_tasks_search').val())">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <?php 
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $start_page + 4);
                if ($end_page - $start_page < 4) $start_page = max(1, $end_page - 4);
                for ($i = $start_page; $i <= $end_page; $i++) { ?>
                    <button class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>" onclick="loadData('student_tasks', <?php echo $i; ?>, $('#student_tasks_limit').val(), $('#student_tasks_search').val())">
                        <?php echo $i; ?>
                    </button>
                <?php } ?>
                <button class="page-btn" <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?> onclick="loadData('student_tasks', <?php echo $page + 1; ?>, $('#student_tasks_limit').val(), $('#student_tasks_search').val())">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    <?php
    }
    exit;
}

if ($action == 'update_progress' && $user_role == 'student') {
    $id = $bf->sanitize($_POST['id'] ?? '');
    $completion_percentage = (int)($_POST['completion_percentage'] ?? 0);
    $status = $bf->sanitize($_POST['status'] ?? 'Pending');

    if ($completion_percentage < 0) $completion_percentage = 0;
    if ($completion_percentage > 100) $completion_percentage = 100;

    // Verify task belongs to this student
    $task = $bf->getTableRecords($GLOBALS['student_tasks_table'], 'id', $id);
    if (!empty($task) && $task[0]['assigned_to_student'] == $username) {
        $bf->UpdateSQL($GLOBALS['student_tasks_table'], [
            'completion_percentage' => $completion_percentage,
            'status' => $status,
            'updated_date_time' => date('Y-m-d H:i:s')
        ], 'id = :id', [':id' => $id]);
        echo json_encode(['status' => 'success', 'message' => 'Progress updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized action']);
    }
    exit;
}

if ($action == 'delete' && checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'student_tasks', PERMISSION_DELETE)) {
    $id = $bf->sanitize($_POST['id'] ?? '');
    $bf->UpdateSQL($GLOBALS['student_tasks_table'], [
        'deleted' => 1,
        'updated_date_time' => date('Y-m-d H:i:s')
    ], 'id = :id', [':id' => $id]);
    echo json_encode(['status' => 'success', 'message' => 'Task deleted successfully']);
    exit;
}

if ($action == 'add_comment') {
    $task_id = $bf->sanitize($_POST['task_id'] ?? '');
    $comment = $bf->sanitize($_POST['comment'] ?? '');

    if (empty($comment)) {
        echo json_encode(['status' => 'error', 'message' => 'Comment cannot be empty']);
        exit;
    }

    $data = [
        'task_id' => $task_id,
        'user_role' => $user_role,
        'username' => $username,
        'comment' => $comment,
        'created_date_time' => date('Y-m-d H:i:s')
    ];
    $bf->InsertSQL($GLOBALS['task_comments_table'], $data);
    echo json_encode(['status' => 'success']);
    exit;
}

if ($action == 'get_comments') {
    $task_id = $bf->sanitize($_GET['task_id'] ?? $_POST['task_id'] ?? '');
    $comments = $bf->getQueryRecords("SELECT * FROM " . $GLOBALS['task_comments_table'] . " WHERE task_id = :task_id AND deleted = 0 ORDER BY created_date_time ASC", [':task_id' => $task_id]);

    if (empty($comments)) {
        echo '<p style="text-align:center; color:var(--text-muted); font-size:0.85rem; padding: 1rem;">No comments yet. Start the conversation!</p>';
    } else {
        foreach ($comments as $c) {
            $is_self = ($c['username'] == $username);
            $role_badge = ucfirst($c['user_role']);
            $bg_color = $is_self ? 'var(--primary)' : '#f1f5f9';
            $text_color = $is_self ? '#fff' : '#1e293b';
            $align = $is_self ? 'flex-end' : 'flex-start';
            ?>
            <div style="display:flex; flex-direction:column; align-items: <?php echo $align; ?>; margin-bottom: 1rem; max-width: 80%; align-self: <?php echo $align; ?>;">
                <div style="font-size:0.7rem; color:var(--text-muted); margin-bottom:0.25rem;">
                    <strong><?php echo $c['username']; ?></strong> (<?php echo $role_badge; ?>) &bull; <?php echo date('d-m-Y H:i', strtotime($c['created_date_time'])); ?>
                </div>
                <div style="background: <?php echo $bg_color; ?>; color: <?php echo $text_color; ?>; padding: 0.75rem 1rem; border-radius: 0.75rem; font-size: 0.85rem; line-height: 1.4;">
                    <?php echo nl2br(htmlspecialchars($c['comment'])); ?>
                </div>
            </div>
            <?php
        }
    }
    exit;
}

if (isset($_POST['view_student_tasks_id'])) {
    $view_task_id = $_POST['view_student_tasks_id'];
    $task_data = [];
    if (!empty($view_task_id)) {
        $recs = $bf->getTableRecords($GLOBALS['student_tasks_table'], 'id', $view_task_id);
        if (!empty($recs)) {
            $task_data = $recs[0];
        }
    }

    // Load available students for the dropdown
    // Staff should only see their assigned students, admin sees all
    $students = [];
    $comp_id = $_SESSION['company_id'];
    if ($user_role == 'admin') {
        $q = "SELECT student_id, student_name, 'Training' as type FROM " . $GLOBALS['enrollment_table'] . " WHERE deleted = 0 AND company_id = :comp_id1 AND student_id NOT IN (SELECT student_id FROM " . $GLOBALS['course_closure_table'] . " WHERE deleted = 0)
              UNION ALL
              SELECT student_id, student_name, 'Internship' as type FROM " . $GLOBALS['enrollment_internship_table'] . " WHERE deleted = 0 AND company_id = :comp_id2 AND student_id NOT IN (SELECT student_id FROM " . $GLOBALS['course_closure_table'] . " WHERE deleted = 0)";
        $students = $bf->getQueryRecords($q, [':comp_id1' => $comp_id, ':comp_id2' => $comp_id]);
    } else {
        $q = "SELECT student_id, student_name, 'Training' as type FROM " . $GLOBALS['enrollment_table'] . " WHERE staff_id = :staff_id1 AND deleted = 0 AND company_id = :comp_id1 AND student_id NOT IN (SELECT student_id FROM " . $GLOBALS['course_closure_table'] . " WHERE deleted = 0)
              UNION ALL
              SELECT student_id, student_name, 'Internship' as type FROM " . $GLOBALS['enrollment_internship_table'] . " WHERE staff_id = :staff_id2 AND deleted = 0 AND company_id = :comp_id2 AND student_id NOT IN (SELECT student_id FROM " . $GLOBALS['course_closure_table'] . " WHERE deleted = 0)";
        $students = $bf->getQueryRecords($q, [':staff_id1' => $user_id, ':staff_id2' => $user_id, ':comp_id1' => $comp_id, ':comp_id2' => $comp_id]);
    }

    $is_edit = !empty($view_task_id);
    ?>
    <div class="header" style="display:flex; justify-content:space-between; align-items:center;">
        <h2><?php echo $is_edit ? 'Edit Assigned Task' : 'Assign New Task'; ?></h2>
        <button class="btn-add" style="background:#64748b;" onclick="$('.new_content').hide(); $('.update_content').show();">Back to List</button>
    </div>

    <div class="module-section">
        <form id="student_task_form" onsubmit="event.preventDefault(); formSubmit('student_task_form', 'student_tasks_action.php', 'student_tasks.php', 'student_tasks');" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="view_task_id" value="<?php echo $view_task_id; ?>">

            <div class="form-row">
                <div class="col-6">
                    <div class="form-group">
                        <label>Task Title *</label>
                        <input type="text" name="task_title" class="form-input" value="<?php echo $task_data['task_title'] ?? ''; ?>" required>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label>Assign to Student *</label>
                        <select name="assigned_to_student" class="form-input" required>
                            <option value="">Select Student</option>
                            <?php foreach ($students as $s) {
                                $decrypted_id = $bf->encode_decode('decrypt', $s['student_id']);
                                $sel = (isset($task_data['assigned_to_student']) && $task_data['assigned_to_student'] == $decrypted_id) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $decrypted_id; ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($s['student_name']); ?> (<?php echo $decrypted_id; ?> - <?php echo $s['type']; ?>)</option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-input" style="height:100px;"><?php echo $task_data['description'] ?? ''; ?></textarea>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Start Date *</label>
                        <input type="date" name="start_date" class="form-input" value="<?php echo $task_data['start_date'] ?? date('Y-m-d'); ?>" required>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Due Date *</label>
                        <input type="date" name="due_date" class="form-input" value="<?php echo $task_data['due_date'] ?? date('Y-m-d', strtotime('+3 days')); ?>" required>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Priority</label>
                        <select name="priority" class="form-input">
                            <option value="High" <?php echo (isset($task_data['priority']) && $task_data['priority'] == 'High') ? 'selected' : ''; ?>>High</option>
                            <option value="Medium" <?php echo (!isset($task_data['priority']) || $task_data['priority'] == 'Medium') ? 'selected' : ''; ?>>Medium</option>
                            <option value="Low" <?php echo (isset($task_data['priority']) && $task_data['priority'] == 'Low') ? 'selected' : ''; ?>>Low</option>
                        </select>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Attachments (Supports Multiple Files)</label>
                        <input type="file" name="attachments[]" class="form-input" multiple>
                        <?php if (!empty($task_data['attachments'])) {
                            echo '<div style="margin-top:0.75rem; display:flex; gap:0.5rem; flex-wrap:wrap;">';
                            $files = explode(',', $task_data['attachments']);
                            foreach ($files as $f) {
                                $f = trim($f);
                                if (!empty($f)) {
                                    echo '<a href="upload/' . $f . '" target="_blank" style="padding:0.35rem 0.75rem; background:#f1f5f9; border:1px solid #e2e8f0; border-radius:6px; font-size:0.75rem; text-decoration:none; color:var(--primary); font-weight:600;"><i class="fas fa-paperclip"></i> ' . $f . '</a>';
                                }
                            }
                            echo '</div>';
                        } ?>
                    </div>
                </div>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn-add"><?php echo $is_edit ? 'Update Task' : 'Assign Task'; ?></button>
            </div>
        </form>
    </div>
    <?php
    exit;
}
?>
