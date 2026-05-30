<?php
require_once 'common_file.php';

$action = $_REQUEST['action'] ?? '';

// 1. List View
if ($action == 'list') {
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
    $search = isset($_POST['search']) ? $_POST['search'] : '';
    $start = ($page - 1) * $limit;
    
    $logged_in_staff_id = $_SESSION['user_id']; 

    $result = $bf->getStudentAttendanceList($start, $limit, $search, $logged_in_staff_id);

    $all_records = $result['data'];
    $total_records = $result['total_records'];
    $total_pages = ceil($total_records / $limit);

    // Group by attendance_number
    $grouped = [];
    foreach ($all_records as $row) {
        $num = $row['attendance_number'];
        $grouped[$num][] = $row;
    }

    ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>S.no</th>
                    <th>Date</th>
                    <th>Student Name</th>
                    <th>Student ID</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($grouped)) { ?>
                    <tr><td colspan="6" style="text-align:center">No records found.</td></tr>
                <?php } else { 
                    $sno = $start + 1;
                    foreach ($grouped as $num => $student_records) {
                        $rowspan = count($student_records);
                        $first = true;
                        $date = $student_records[0]['attendance_date'];

                        foreach ($student_records as $row) {
                            $sid = $row['student_id'];
                            
                            $student_id_decoded = $bf->encode_decode('decrypt', $sid);
                            $student_name = $bf->getTableColumnValue($GLOBALS['enrollment_table'], 'student_id', $sid, 'student_name');
                            if(empty($student_name)) {
                                $student_name = $bf->getTableColumnValue($GLOBALS['enrollment_internship_table'], 'student_id', $sid, 'student_name');
                            }
                            
                            echo '<tr>';
                            if ($first) {
                                echo '<td rowspan="' . $rowspan . '">' . $sno++ . '</td>';
                                echo '<td rowspan="' . $rowspan . '">' . date('d-m-Y', strtotime($date)) . '</td>';
                            }
                            
                            echo '<td>' . $student_name . '</td>';
                            echo '<td>' . $student_id_decoded . '</td>';
                            
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
                                        <div style="display:flex; gap:0.5rem;">';
                                if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'student_attendance', PERMISSION_EDIT)) {
                                    echo '<button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;" onclick="ShowPage(\'student_attendance\', \'' . $num . '\')">Edit</button>';
                                }
                                if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'student_attendance', PERMISSION_DELETE)) {
                                    echo '<button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; background: #ef4444;" onclick="deleteRecord(\'student_attendance\', \'' . $num . '\')">Delete</button>';
                                }
                                echo '  </div>
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
            <button class="page-btn" <?php echo ($page <= 1) ? 'disabled' : ''; ?> onclick="loadData('student_attendance', <?php echo $page - 1; ?>, $('#student_attendance_limit').val(), $('#student_attendance_search').val())">
                <i class="fas fa-chevron-left"></i>
            </button>
            <?php 
            $start_page = max(1, $page - 2);
            $end_page = min($total_pages, $start_page + 4);
            if ($end_page - $start_page < 4) $start_page = max(1, $end_page - 4);
            for ($i = $start_page; $i <= $end_page; $i++) { ?>
                <button class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>" onclick="loadData('student_attendance', <?php echo $i; ?>, $('#student_attendance_limit').val(), $('#student_attendance_search').val())">
                    <?php echo $i; ?>
                </button>
            <?php } ?>
            <button class="page-btn" <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?> onclick="loadData('student_attendance', <?php echo $page + 1; ?>, $('#student_attendance_limit').val(), $('#student_attendance_search').val())">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
    <?php
    exit();
}

// 2. Form View (Add/Edit)
if (isset($_POST['view_student_attendance_id'])) {
    $att_number = $_POST['view_student_attendance_id'];
    $is_edit = !empty($att_number);
    
    $attendance_date = date('Y-m-d');
    $existing_attendance = [];
    
    if ($is_edit) {
        $records = $bf->getTableRecords($GLOBALS['student_attendance_table'], 'attendance_number', $att_number);
        if (!empty($records)) {
            $attendance_date = $records[0]['attendance_date'];
            foreach ($records as $r) {
                $existing_attendance[$r['student_id']] = $r;
            }
        }
    }

    $logged_in_staff_id = $_SESSION['user_id']; 

    if($_SESSION['role_id'] == 'cFlxWHg1N2RYSzg5OEczMGFSTmNvaFJjU2tQdEJlZVJoR1ZLL3ZYZitzUT0=') {
        $query = "
            SELECT student_id, student_name, 'Training' as type FROM " . $GLOBALS['enrollment_table'] . " 
            WHERE staff_id = :staff_id AND deleted = 0 
            AND (course_closed IS NULL OR course_closed != 1)
            
            UNION ALL 
            
            SELECT student_id, student_name, 'Internship' as type FROM " . $GLOBALS['enrollment_internship_table'] . " 
            WHERE staff_id = :staff_id AND deleted = 0
            AND (course_closed IS NULL OR course_closed != 1)
        ";
         $student_list = $bf->getQueryRecords($query, [':staff_id' => $logged_in_staff_id]);
    } else {
        $query = "
            SELECT student_id, student_name, 'Training' as type FROM " . $GLOBALS['enrollment_table'] . " 
            WHERE company_id = :company_id AND deleted = 0 
            AND (course_closed IS NULL OR course_closed != 1)
            
            UNION ALL 
            
            SELECT student_id, student_name, 'Internship' as type FROM " . $GLOBALS['enrollment_internship_table'] . " 
            WHERE company_id = :company_id AND deleted = 0
            AND (course_closed IS NULL OR course_closed != 1)
        ";
        $student_list = $bf->getQueryRecords($query, [':company_id' => $_SESSION['company_id']]);
    }    
    
   

    ?>
    <div class="header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2 style="margin:0;"><?php echo $is_edit ? 'Edit' : 'Mark'; ?> Student Attendance</h2>
        <button class="btn-add" style="background: #64748b;" onclick="$('.new_content').hide(); $('.update_content').show();">Back to List</button>
    </div>

    <div class="module-section">
        <form id="student_attendance_form" onsubmit="event.preventDefault(); formSubmit('student_attendance_form', 'student_attendance_action.php', 'student_attendance.php', 'student_attendance');">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="old_attendance_number" value="<?php echo $att_number; ?>">
            
            <div class="form-row" style="margin-bottom: 2rem;">
                <div class="col-4">
                    <div class="form-group">
                        <label>Attendance Date</label>
                        <input type="date" name="attendance_date" class="form-input" value="<?php echo $attendance_date; ?>" max="<?php echo date('Y-m-d'); ?>" required <?php echo $is_edit ? 'readonly' : ''; ?>>
                    </div>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Enrollment Type</th>
                            <th style="text-align:center">FN</th>
                            <th style="text-align:center">AN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($student_list)): ?>
                            <tr><td colspan="6" style="text-align:center">No assigned students found.</td></tr>
                        <?php else: ?>
                            <?php 
                            $sno_inner = 1;
                            foreach ($student_list as $student): 
                                $sid = $student['student_id'];
                                $fn_checked = 'checked';
                                $an_checked = 'checked';
                                
                                if (isset($existing_attendance[$sid])) {
                                    $fn_checked = ($existing_attendance[$sid]['fn_present'] == 'P') ? 'checked' : '';
                                    $an_checked = ($existing_attendance[$sid]['an_present'] == 'P') ? 'checked' : '';
                                }
                                
                                $student_id_decoded = $bf->encode_decode('decrypt', $sid);
                            ?>
                            <tr>
                                <td><?php echo $sno_inner++; ?></td>
                                <td><?php echo $student_id_decoded; ?></td>
                                <td><?php echo $student['student_name']; ?></td>
                                <td><?php echo $student['type']; ?></td>
                                <td style="text-align:center">
                                    <input type="checkbox" name="fn_<?php echo $sid; ?>" <?php echo $fn_checked; ?>>
                                </td>
                                <td style="text-align:center">
                                    <input type="checkbox" name="an_<?php echo $sid; ?>" <?php echo $an_checked; ?>>
                                </td>
                                <input type="hidden" name="student_ids[]" value="<?php echo $sid; ?>">
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
    $student_ids = $_POST['student_ids'] ?? [];
    $old_att_number = $_POST['old_attendance_number'] ?? '';
    $logged_in_staff_id = $_SESSION['user_id'];

    if (empty($student_ids)) {
        echo json_encode(['status' => 'error', 'message' => 'No students to mark.']);
        exit();
    }

    // Duplicate check for new entries
    if (empty($old_att_number)) {
        $check = $bf->getQueryRecords("SELECT id FROM " . $GLOBALS['student_attendance_table'] . " WHERE attendance_date = :date AND staff_id = :staff_id AND deleted = 0", [':date' => $attendance_date, ':staff_id' => $logged_in_staff_id]);
        if (!empty($check)) {
            echo json_encode(['status' => 'error', 'message' => 'Attendance already marked for ' . date('d-m-Y', strtotime($attendance_date)) . '.']);
            exit();
        }
    }

    $new_session_number = $old_att_number;

    foreach ($student_ids as $sid) {
        $fn = isset($_POST['fn_' . $sid]) ? 'P' : 'A';
        $an = isset($_POST['an_' . $sid]) ? 'P' : 'A';
        $p_code = $fn . $an;

        $data = [
            'updated_date_time' => $GLOBALS['create_date_time_label'],
            'attendance_date' => $attendance_date,
            'staff_id' => $logged_in_staff_id,
            'student_id' => $sid,
            'fn_present' => $fn,
            'an_present' => $an,
            'present_code' => $p_code,
            'deleted' => 0
        ];
        
        $existing = $bf->getQueryRecords("SELECT id FROM " . $GLOBALS['student_attendance_table'] . " WHERE student_id = :sid AND attendance_date = :date AND staff_id = :staff_id AND deleted = 0", [':sid' => $sid, ':date' => $attendance_date, ':staff_id' => $logged_in_staff_id]);

        if (!empty($existing)) {
            if (!empty($old_att_number)) $data['attendance_number'] = $old_att_number;
            $bf->UpdateSQL($GLOBALS['student_attendance_table'], $data, "id = :id", [':id' => $existing[0]['id']]);
        } else {
            $data['created_date_time'] = $GLOBALS['create_date_time_label'];
            if (!empty($old_att_number)) {
                $data['attendance_number'] = $old_att_number;
                $bf->InsertSQL($GLOBALS['student_attendance_table'], $data);
            } else {
                if (empty($new_session_number)) {
                    $new_session_number = $bf->automate_number($GLOBALS['student_attendance_table'], 'attendance_number', '', '');
                    $data['attendance_number'] = $new_session_number;
                    $bf->InsertSQL($GLOBALS['student_attendance_table'], $data);
                } else {
                    $data['attendance_number'] = $new_session_number;
                    $bf->InsertSQL($GLOBALS['student_attendance_table'], $data);
                }
            }
        }
    }

    echo json_encode(['status' => 'success', 'message' => 'Attendance saved successfully.']);
    exit();
}

// 4. Delete Action
if ($action == 'delete') {
    $att_number = $_POST['id'];
    $data = ['deleted' => 1, 'updated_date_time' => $GLOBALS['create_date_time_label']];
    $bf->UpdateSQL($GLOBALS['student_attendance_table'], $data, "attendance_number = :id", [':id' => $att_number]);
    echo json_encode(['status' => 'success', 'message' => 'Attendance record deleted.']);
    exit();
}
?>
