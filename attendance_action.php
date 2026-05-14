<?php
require_once 'common_file.php';

$action = $_REQUEST['action'] ?? '';

// 1. List View
if ($action == 'list') {
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
    $search = isset($_POST['search']) ? $_POST['search'] : '';
    $start = ($page - 1) * $limit;

    $result = $bf->getAttendanceList($start, $limit, $search);
    $all_records = $result['data'];
    $total_records = $result['total_records'];
    $total_pages = ceil($total_records / $limit);

    // Group by attendance_date
    $grouped = [];
    foreach ($all_records as $row) {
        $date = $row['attendance_date'];
        $grouped[$date][] = $row;
    }

    ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>S.no</th>
                    <th>Date</th>
                    <th>Staff Name</th>
                    <th>Staff Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($all_records)) { ?>
                    <tr><td colspan="6" style="text-align:center">No records found.</td></tr>
                <?php } else { 
                    $sno = $start + 1;
                    foreach ($grouped as $date => $staff_records) {
                        $rowspan = count($staff_records);
                        $first = true;
                        $att_id = $staff_records[0]['attendance_id'];

                        foreach ($staff_records as $row) {
                            echo '<tr>';
                            if ($first) {
                                echo '<td rowspan="' . $rowspan . '">' . $sno++ . '</td>';
                                echo '<td rowspan="' . $rowspan . '">' . date('d-m-Y', strtotime($date)) . '</td>';
                            }
                            
                            echo '<td>' . $row['staff_name'] . '</td>';
                            echo '<td>' . $row['staff_role'] . '</td>';
                            
                            $desc = '';
                            switch($row['present_code']) {
                                case 'PP': $desc = '<span style="color:#10b981; font-weight:600;">Full day present</span>'; break;
                                case 'AA': $desc = '<span style="color:#ef4444; font-weight:600;">Full day absent</span>'; break;
                                case 'PA': $desc = '<span style="color:#f59e0b; font-weight:600;">FN - P | AN - A</span>'; break;
                                case 'AP': $desc = '<span style="color:#f59e0b; font-weight:600;">FN - A | AN - P</span>'; break;
                            }
                            echo '<td>' . $desc . '</td>';

                            if ($first) {
                                echo '<td rowspan="' . $rowspan . '">
                                        <div style="display:flex; gap:0.5rem;">
                                            <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;" onclick="ShowPage(\'attendance\', \'' . $att_id . '\')">Edit</button>
                                            <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; background: #ef4444;" onclick="deleteRecord(\'attendance\', \'' . $att_id . '\')">Delete</button>
                                        </div>
                                      </td>';
                            }
                            echo '</tr>';
                            $first = false;
                        }
                    }
                } ?>
            </tbody>
        </table>
    </div>

    <div class="pagination-container">
        <div class="pagination-info">
            Showing <?php echo ($total_records > 0) ? $start + 1 : 0; ?> to <?php echo min($start + $limit, $total_records); ?> of <?php echo $total_records; ?> entries
        </div>
        <div class="pagination-buttons">
            <button class="page-btn" <?php echo ($page <= 1) ? 'disabled' : ''; ?> onclick="loadData('attendance', <?php echo $page - 1; ?>, $('#attendance_limit').val(), $('#attendance_search').val())">
                <i class="fas fa-chevron-left"></i>
            </button>
            <?php 
            $start_page = max(1, $page - 2);
            $end_page = min($total_pages, $start_page + 4);
            if ($end_page - $start_page < 4) $start_page = max(1, $end_page - 4);
            for ($i = $start_page; $i <= $end_page; $i++) { ?>
                <button class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>" onclick="loadData('attendance', <?php echo $i; ?>, $('#attendance_limit').val(), $('#attendance_search').val())">
                    <?php echo $i; ?>
                </button>
            <?php } ?>
            <button class="page-btn" <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?> onclick="loadData('attendance', <?php echo $page + 1; ?>, $('#attendance_limit').val(), $('#attendance_search').val())">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
    <?php
    exit();
}

// 2. Form View (Add/Edit)
if (isset($_POST['view_attendance_id'])) {
    $att_id = $_POST['view_attendance_id'];
    $is_edit = !empty($att_id);
    
    $attendance_date = date('Y-m-d');
    $existing_attendance = [];
    
    if ($is_edit) {
        $records = $bf->getTableRecords($GLOBALS['attendance_table'], 'attendance_id', $att_id);
        if (!empty($records)) {
            $attendance_date = $records[0]['attendance_date'];
            foreach ($records as $r) {
                $existing_attendance[$r['staff_id']] = $r;
            }
        }
    }

    $staff_list = $bf->getTableRecords($GLOBALS['staff_table'], 'deleted', 0, 'staff_name ASC');

    ?>
    <div class="header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2 style="margin:0;"><?php echo $is_edit ? 'Edit' : 'Mark'; ?> Attendance</h2>
        <button class="btn-add" style="background: #64748b;" onclick="$('.new_content').hide(); $('.update_content').show();">Back to List</button>
    </div>

    <div class="module-section">
        <form id="attendance_form" onsubmit="event.preventDefault(); formSubmit('attendance_form', 'attendance_action.php', 'attendance.php', 'attendance');">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="old_attendance_id" value="<?php echo $att_id; ?>">
            
            <div class="form-row" style="margin-bottom: 2rem;">
                <div class="col-4">
                    <div class="form-group">
                        <label>Attendance Date</label>
                        <input type="date" name="attendance_date" value="<?php echo $attendance_date; ?>" max="<?php echo date('Y-m-d'); ?>" required <?php echo $is_edit ? 'readonly' : ''; ?>>
                    </div>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Staff Name</th>
                            <th>Staff Role</th>
                            <th style="text-align:center">FN</th>
                            <th style="text-align:center">AN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($staff_list)): ?>
                            <tr><td colspan="5" style="text-align:center">No staff members found.</td></tr>
                        <?php else: ?>
                            <?php 
                            $sno_inner = 1;
                            foreach ($staff_list as $staff): 
                                $sid = $staff['staff_id'];
                                $fn_checked = 'checked';
                                $an_checked = 'checked';
                                
                                if (isset($existing_attendance[$sid])) {
                                    $fn_checked = ($existing_attendance[$sid]['fn_present'] == 'P') ? 'checked' : '';
                                    $an_checked = ($existing_attendance[$sid]['an_present'] == 'P') ? 'checked' : '';
                                }
                                
                                $role_name = $bf->getTableColumnValue($GLOBALS['role_table'], 'id', $staff['role_id'], 'role_name');
                            ?>
                            <tr>
                                <td><?php echo $sno_inner++; ?></td>
                                <td><?php echo $staff['staff_name']; ?></td>
                                <td><?php echo $role_name; ?></td>
                                <td style="text-align:center">
                                    <input type="checkbox" name="fn_<?php echo $sid; ?>" <?php echo $fn_checked; ?>>
                                </td>
                                <td style="text-align:center">
                                    <input type="checkbox" name="an_<?php echo $sid; ?>" <?php echo $an_checked; ?>>
                                </td>
                                <input type="hidden" name="staff_ids[]" value="<?php echo $sid; ?>">
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn-add"><?php echo $is_edit ? 'Update' : 'Save'; ?> Attendance</button>
            </div>
        </form>
    </div>
    <?php
    exit();
}

// 3. Save Action
if ($action == 'save') {
    $attendance_date = $_POST['attendance_date'];
    $staff_ids = $_POST['staff_ids'] ?? [];
    $old_att_id = $_POST['old_attendance_id'] ?? '';

    if (empty($staff_ids)) {
        echo json_encode(['status' => 'error', 'message' => 'No staff members to mark.']);
        exit();
    }

    // Duplicate check for new entries
    if (empty($old_att_id)) {
        $check = $bf->getQueryRecords("SELECT id FROM " . $GLOBALS['attendance_table'] . " WHERE attendance_date = :date AND deleted = 0", [':date' => $attendance_date]);
        if (!empty($check)) {
            echo json_encode(['status' => 'error', 'message' => 'Attendance already marked for ' . date('d-m-Y', strtotime($attendance_date)) . '.']);
            exit();
        }
    }

    $new_session_id = $old_att_id;

    foreach ($staff_ids as $sid) {
        $fn = isset($_POST['fn_' . $sid]) ? 'P' : 'A';
        $an = isset($_POST['an_' . $sid]) ? 'P' : 'A';
        $p_code = $fn . $an;

        $staff_info_list = $bf->getQueryRecords("SELECT staff_name, staff_number, role_id FROM " . $GLOBALS['staff_table'] . " WHERE staff_id = :sid", [':sid' => $sid]);
        if(empty($staff_info_list)) continue;
        
        $staff_info = $staff_info_list[0];
        $role_name = $bf->getTableColumnValue($GLOBALS['role_table'], 'id', $staff_info['role_id'], 'role_name');

        $data = [
            'updated_date_time' => $GLOBALS['create_date_time_label'],
            'attendance_date' => $attendance_date,
            'staff_name' => $staff_info['staff_name'],
            'staff_number' => $staff_info['staff_number'],
            'staff_role' => $role_name,
            'fn_present' => $fn,
            'an_present' => $an,
            'present_code' => $p_code,
            'deleted' => 0
        ];

        $existing = $bf->getQueryRecords("SELECT id FROM " . $GLOBALS['attendance_table'] . " WHERE staff_id = :sid AND attendance_date = :date AND deleted = 0", [':sid' => $sid, ':date' => $attendance_date]);

        if (!empty($existing)) {
            if (!empty($old_att_id)) $data['attendance_id'] = $old_att_id;
            $bf->UpdateSQL($GLOBALS['attendance_table'], $data, "id = :id", [':id' => $existing[0]['id']]);
        } else {
            $data['created_date_time'] = $GLOBALS['create_date_time_label'];
            $data['staff_id'] = $sid;
            if (!empty($old_att_id)) {
                $data['attendance_id'] = $old_att_id;
                $bf->InsertSQL($GLOBALS['attendance_table'], $data);
            } else {
                if (empty($new_session_id)) {
                    $new_session_id = $bf->InsertSQL($GLOBALS['attendance_table'], $data, 'attendance_id');
                } else {
                    $data['attendance_id'] = $new_session_id;
                    $bf->InsertSQL($GLOBALS['attendance_table'], $data);
                }
            }
        }
    }

    echo json_encode(['status' => 'success', 'message' => 'Attendance saved successfully.']);
    exit();
}

// 4. Delete Action
if ($action == 'delete') {
    $att_id = $_POST['id'];
    $data = ['deleted' => 1, 'updated_date_time' => $GLOBALS['create_date_time_label']];
    $bf->UpdateSQL($GLOBALS['attendance_table'], $data, "attendance_id = :id", [':id' => $att_id]);
    echo json_encode(['status' => 'success', 'message' => 'Attendance record deleted.']);
    exit();
}
?>
