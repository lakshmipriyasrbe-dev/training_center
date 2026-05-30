<?php
require_once 'common_file.php';

$action = $_REQUEST['action'] ?? '';

if ($action == 'list') {
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
    $search = isset($_POST['search']) ? $_POST['search'] : '';
    $status_filter = isset($_POST['status_filter']) ? $_POST['status_filter'] : '';
    $start = ($page - 1) * $limit;

    $query = "FROM " . $GLOBALS['student_reports_table'] . " r 
              LEFT JOIN " . $GLOBALS['student_tasks_table'] . " t ON r.task_id = t.id
              WHERE r.deleted = 0";
    $params = [];

    if ($user_role == 'trainer' || $user_role == 'staff') {
        // Staff only sees reports of their assigned students
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

        $query .= " AND r.student_id IN (" . implode(',', $placeholders) . ")";
    }

    if (!empty($search)) {
        $query .= " AND (r.student_id LIKE :search OR t.task_title LIKE :search OR r.work_done LIKE :search)";
        $params[':search'] = "%$search%";
    }

    if (!empty($status_filter)) {
        $query .= " AND r.status = :status_filter";
        $params[':status_filter'] = $status_filter;
    }

    // Get total count
    $count_query = "SELECT COUNT(*) as total " . $query;
    $total_records = $bf->getQueryRecords($count_query, $params)[0]['total'];
    $total_pages = ceil($total_records / $limit);

    // echo $bf->debugQuery($query, $params);

    // Get paginated data
    $data_query = "SELECT r.*, t.task_title " . $query . " ORDER BY r.status ASC, r.report_date DESC LIMIT $start, $limit";
    // echo $bf->debugQuery($data_query, $params);
    $reports = $bf->getQueryRecords($data_query, $params);

    // print_r($reports);

    if (empty($reports)) { ?>
        <div class="table-responsive">
            <table><tr><td style="text-align:center">No student reports found.</td></tr></table>
        </div>
    <?php } else { ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>SNo</th>
                        <th>Student ID</th>
                        <th>Report Date</th>
                        <th>Task Name</th>
                        <th>Work Done</th>
                        <th>Attachments</th>
                        <th>Current Feedback</th>
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
                        <td><strong style="color:#475569;"><?php echo $r['student_id']; ?></strong></td>
                        <td><strong><?php echo date('d-m-Y', strtotime($r['report_date'])); ?></strong></td>
                        <td><span style="color:var(--primary); font-weight:600;"><?php echo htmlspecialchars($r['task_title'] ?: 'General Task'); ?></span></td>
                        <td>
                            <p style="margin:0; font-size:0.85rem; line-height:1.4; color:#475569; max-width: 250px; white-space: pre-line;">
                                <?php echo htmlspecialchars($r['work_done']); ?>
                            </p>
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
                        <td>
                            <?php if (!empty($r['remarks'])) { ?>
                                <span style="font-size:0.8rem; color:#4f46e5; font-weight:600;"><?php echo htmlspecialchars($r['remarks']); ?></span>
                            <?php } else { ?>
                                <span style="font-style:italic; color:var(--text-muted); font-size:0.8rem;">No feedback given</span>
                            <?php } ?>
                        </td>
                        <td><span class="status-badge" style="background: <?php echo $status_color; ?>15; color: <?php echo $status_color; ?>; font-weight:700;"><?php echo $r['status']; ?></span></td>
                        <td>
                            <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'student_reports', PERMISSION_EDIT)): ?>
                                <button class="btn-add" style="padding:0.25rem 0.5rem; font-size:0.75rem; background:#6366f1;" onclick="openReviewModal(<?php echo $r['id']; ?>, '<?php echo $r['student_id']; ?>', '<?php echo htmlspecialchars($r['remarks'], ENT_QUOTES); ?>', '<?php echo $r['status']; ?>')">
                                    Review / Approve
                                </button>
                            <?php endif; ?>
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
                <button class="page-btn" <?php echo ($page <= 1) ? 'disabled' : ''; ?> onclick="loadData('student_reports', <?php echo $page - 1; ?>, $('#student_reports_limit').val(), $('#student_reports_search').val(), $('#student_reports_status').val())">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <?php 
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $start_page + 4);
                if ($end_page - $start_page < 4) $start_page = max(1, $end_page - 4);
                for ($i = $start_page; $i <= $end_page; $i++) { ?>
                    <button class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>" onclick="loadData('student_reports', <?php echo $i; ?>, $('#student_reports_limit').val(), $('#student_reports_search').val(), $('#student_reports_status').val())">
                        <?php echo $i; ?>
                    </button>
                <?php } ?>
                <button class="page-btn" <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?> onclick="loadData('student_reports', <?php echo $page + 1; ?>, $('#student_reports_limit').val(), $('#student_reports_search').val(), $('#student_reports_status').val())">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    <?php
    }
    exit;
}

if ($action == 'approve_report') {
    $id = $bf->sanitize($_POST['id'] ?? '');
    $remarks = $bf->sanitize($_POST['remarks'] ?? '');
    $status = $bf->sanitize($_POST['status'] ?? 'Approved');

    $bf->UpdateSQL($GLOBALS['student_reports_table'], [
        'remarks' => $remarks,
        'status' => $status,
        'updated_date_time' => date('Y-m-d H:i:s')
    ], 'id = :id', [':id' => $id]);

    echo json_encode(['status' => 'success', 'message' => 'Report updated and evaluated successfully']);
    exit;
}
?>
