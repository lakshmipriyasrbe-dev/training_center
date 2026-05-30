<?php
require_once 'common_file.php';

$action = $_REQUEST['action'] ?? '';

if ($action == 'add' && (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'daily_report', PERMISSION_ADD) || checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'daily_report', PERMISSION_EDIT))) {
    $report_date = $bf->sanitize($_POST['report_date'] ?? '');
    $task_id = $bf->sanitize($_POST['task_id'] ?? '');
    $work_done = $bf->sanitize($_POST['work_done'] ?? '');
    $view_report_id = $bf->sanitize($_POST['view_report_id'] ?? '');

    $errors = [];
    if (empty($report_date)) {
        $errors['report_date'] = 'Select report date';
    } else {
        $date_time = strtotime($report_date);
        if (!$date_time) {
            $errors['report_date'] = 'Enter a valid date';
        } elseif ($date_time > strtotime(date('Y-m-d'))) {
            $errors['report_date'] = 'Future date is not allowed';
        }
    }
    if (empty($task_id)) {
        $errors['task_id'] = 'Select your assigned task';
    }
    if (empty($work_done)) {
        $errors['work_done'] = 'Describe your work done today';
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
    if (!empty($view_report_id)) {
        $report_record = $bf->getTableRecords($GLOBALS['student_reports_table'], 'id', $view_report_id);
        if (!empty($report_record)) {
            $existing_attachments = $report_record[0]['attachment'] ?? '';
        }
    }
    
    $all_attachments = [];
    if (!empty($existing_attachments)) {
        $all_attachments = array_filter(array_map('trim', explode(',', $existing_attachments)));
    }
    $all_attachments = array_merge($all_attachments, $uploaded_files);

    $data = [
        'student_id' => $username,
        'report_date' => $report_date,
        'task_id' => $task_id,
        'work_done' => $work_done,
        'attachment' => implode(',', $all_attachments),
        'updated_date_time' => date('Y-m-d H:i:s')
    ];

    if (empty($view_report_id)) {
        $data['status'] = 'Pending';
        $data['remarks'] = '';
        $data['created_date_time'] = date('Y-m-d H:i:s');
        $data['report_id'] = $bf->automate_number($GLOBALS['student_reports_table'], 'report_id', '', '');
        $bf->InsertSQL($GLOBALS['student_reports_table'], $data);
        echo json_encode(['status' => 'success', 'message' => 'Report submitted successfully']);
    } else {
        // Only allow edit if Pending
        $check = $bf->getTableRecords($GLOBALS['student_reports_table'], 'id', $view_report_id);
        if (!empty($check) && $check[0]['status'] == 'Pending') {
            $bf->UpdateSQL($GLOBALS['student_reports_table'], $data, 'id = :id', [':id' => $view_report_id]);
            echo json_encode(['status' => 'success', 'message' => 'Report updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Approved reports cannot be edited']);
        }
    }
    exit;
}

if ($action == 'list') {
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
    $search = isset($_POST['search']) ? $_POST['search'] : '';
    $start = ($page - 1) * $limit;

    $query = "FROM " . $GLOBALS['student_reports_table'] . " r 
              LEFT JOIN " . $GLOBALS['student_tasks_table'] . " t ON r.task_id = t.id
              WHERE r.student_id = :student_id AND r.deleted = 0";
    $params = [':student_id' => $username];

    if (!empty($search)) {
        $query .= " AND (t.task_title LIKE :search OR r.work_done LIKE :search OR r.remarks LIKE :search)";
        $params[':search'] = "%$search%";
    }

    // Get total count
    $count_query = "SELECT COUNT(*) as total " . $query;
    $total_records = $bf->getQueryRecords($count_query, $params)[0]['total'];
    $total_pages = ceil($total_records / $limit);

    // Get paginated data
    $data_query = "SELECT r.*, t.task_title " . $query . " ORDER BY r.report_date DESC, r.created_date_time DESC LIMIT $start, $limit";
    $reports = $bf->getQueryRecords($data_query, $params);

    if (empty($reports)) { ?>
        <div class="table-responsive">
            <table><tr><td style="text-align:center">No reports found.</td></tr></table>
        </div>
    <?php } else { ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>SNo</th>
                        <th>Date</th>
                        <th>Task Associated</th>
                        <th>Work Done</th>
                        <th>Remarks / Feedback</th>
                        <th>Attachments</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $sno = $start + 1;
                foreach ($reports as $r) { 
                    $status_color = ($r['status'] == 'Approved') ? '#10b981' : '#fbbf24';
                ?>
                    <tr>
                        <td><?php echo $sno++; ?></td>
                        <td><strong><?php echo date('d-m-Y', strtotime($r['report_date'])); ?></strong></td>
                        <td><span style="color:var(--primary); font-weight:600;"><?php echo htmlspecialchars($r['task_title'] ?: 'General Task'); ?></span></td>
                        <td>
                            <p style="margin:0; font-size:0.85rem; line-height:1.4; color:#475569; max-width: 300px; white-space: pre-line;">
                                <?php echo htmlspecialchars($r['work_done']); ?>
                            </p>
                        </td>
                        <td>
                            <?php if (!empty($r['remarks'])) { ?>
                                <div style="background:#f8fafc; border-left: 3px solid #6366f1; padding: 0.5rem; border-radius: 4px; font-size: 0.8rem; color:#4f46e5;">
                                    <?php echo htmlspecialchars($r['remarks']); ?>
                                </div>
                            <?php } else { ?>
                                <span style="font-style:italic; color:var(--text-muted); font-size:0.8rem;">No comments yet</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if (!empty($r['attachment'])) {
                                $files = explode(',', $r['attachment']);
                                echo '<div style="display:flex; flex-direction:column; gap:0.25rem;">';
                                foreach ($files as $f) {
                                    $f = trim($f);
                                    if (!empty($f)) {
                                        echo '<a href="upload/' . $f . '" target="_blank" style="font-size:0.75rem; text-decoration:none; color:var(--primary); font-weight:600;"><i class="fas fa-paperclip"></i> ' . substr($f, 11) . '</a>';
                                    }
                                }
                                echo '</div>';
                            } else {
                                echo '<span style="color:var(--text-muted); font-size:0.75rem;">None</span>';
                            } ?>
                        </td>
                        <td><span class="status-badge" style="background: <?php echo $status_color; ?>15; color: <?php echo $status_color; ?>; font-weight:700;"><?php echo $r['status']; ?></span></td>
                        <td>
                            <?php if ($r['status'] == 'Pending') { ?>
                                <div style="display:flex; gap:0.25rem;">
                                    <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'daily_report', PERMISSION_EDIT)): ?>
                                        <button class="btn-add" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;" onclick="ShowPage('daily_report', '<?php echo $r['id']; ?>')">Edit</button>
                                    <?php endif; ?>
                                </div>
                            <?php } else { ?>
                                <span style="font-size:0.75rem; color:#10b981; font-weight:700;"><i class="fas fa-check-circle"></i> Locked</span>
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
                <button class="page-btn" <?php echo ($page <= 1) ? 'disabled' : ''; ?> onclick="loadData('daily_report', <?php echo $page - 1; ?>, $('#daily_report_limit').val(), $('#daily_report_search').val())">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <?php 
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $start_page + 4);
                if ($end_page - $start_page < 4) $start_page = max(1, $end_page - 4);
                for ($i = $start_page; $i <= $end_page; $i++) { ?>
                    <button class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>" onclick="loadData('daily_report', <?php echo $i; ?>, $('#daily_report_limit').val(), $('#daily_report_search').val())">
                        <?php echo $i; ?>
                    </button>
                <?php } ?>
                <button class="page-btn" <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?> onclick="loadData('daily_report', <?php echo $page + 1; ?>, $('#daily_report_limit').val(), $('#daily_report_search').val())">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    <?php
    }
    exit;
}

if (isset($_POST['view_daily_report_id'])) {
    $view_report_id = $_POST['view_daily_report_id'];
    $report_data = [];
    if (!empty($view_report_id)) {
        $recs = $bf->getTableRecords($GLOBALS['student_reports_table'], 'id', $view_report_id);
        if (!empty($recs)) {
            $report_data = $recs[0];
        }
    }

    // Fetch assigned student tasks for associate dropdown
    $tasks = $bf->getQueryRecords("SELECT id, task_title, task_id FROM " . $GLOBALS['student_tasks_table'] . " WHERE assigned_to_student = :student_id AND deleted = 0 ORDER BY id DESC", [':student_id' => $username]);
    $is_edit = !empty($view_report_id);
    ?>
    <div class="header" style="display:flex; justify-content:space-between; align-items:center;">
        <h2><?php echo $is_edit ? 'Edit Daily Report Submission' : 'Submit Daily Report'; ?></h2>
        <button class="btn-add" style="background:#64748b;" onclick="$('.new_content').hide(); $('.update_content').show();">Back to List</button>
    </div>

    <div class="module-section">
        <form id="daily_report_form" onsubmit="event.preventDefault(); formSubmit('daily_report_form', 'daily_report_action.php', 'daily_report.php', 'daily_report');" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="view_report_id" value="<?php echo $view_report_id; ?>">

            <div class="form-row">
                <div class="col-6">
                    <div class="form-group">
                        <label>Report Date *</label>
                        <input type="date" name="report_date" class="form-input" value="<?php echo $report_data['report_date'] ?? date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label>Associated Task *</label>
                        <select name="task_id" class="form-input" required>
                            <option value="">Select Task</option>
                            <?php foreach ($tasks as $t) {
                                $sel = (isset($report_data['task_id']) && $report_data['task_id'] == $t['id']) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $t['id']; ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($t['task_title']); ?> (<?php echo $t['task_id']; ?>)</option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label>Work Done Today *</label>
                        <textarea name="work_done" class="form-input" style="height:150px;" placeholder="Describe what tasks you worked on, milestones achieved, and status..." required><?php echo $report_data['work_done'] ?? ''; ?></textarea>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Attachments / Work Proofs (Supports Multiple Files)</label>
                        <input type="file" name="attachments[]" class="form-input" multiple>
                        <?php if (!empty($report_data['attachment'])) {
                            echo '<div style="margin-top:0.75rem; display:flex; gap:0.5rem; flex-wrap:wrap;">';
                            $files = explode(',', $report_data['attachment']);
                            foreach ($files as $f) {
                                $f = trim($f);
                                if (!empty($f)) {
                                    echo '<a href="upload/' . $f . '" target="_blank" style="padding:0.35rem 0.75rem; background:#f1f5f9; border:1px solid #e2e8f0; border-radius:6px; font-size:0.75rem; text-decoration:none; color:var(--primary); font-weight:600;"><i class="fas fa-paperclip"></i> ' . substr($f, 11) . '</a>';
                                }
                            }
                            echo '</div>';
                        } ?>
                    </div>
                </div>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn-add"><?php echo $is_edit ? 'Update Report' : 'Submit Report'; ?></button>
            </div>
        </form>
    </div>
    <?php
    exit;
}
?>
