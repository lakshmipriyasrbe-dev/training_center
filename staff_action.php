<?php require_once 'common_file.php'; 
if ($user_role != 'admin' && !$is_management) { header("Location: dashboard.php"); exit(); }
$action = $_REQUEST['action'] ?? '';

$staff_name = ""; $staff_number = ""; $role_id = ""; $course_id_arr = []; 
$salary = ""; $staff_username = ""; $staff_password = ""; $address = "";

if(isset($_REQUEST['view_staff_id'])) {
    $view_staff_id = $_REQUEST['view_staff_id'];
    $staff_list = $bf->getTableRecords($GLOBALS['staff_table'], 'id', $view_staff_id);

    if(!empty($staff_list)) {
        foreach($staff_list as $data) {
            if(!empty($data['staff_name'])) $staff_name = $data['staff_name'];
            if(!empty($data['staff_number'])) $staff_number = $data['staff_number'];
            if(!empty($data['role_id'])) $role_id = $data['role_id'];
            if(!empty($data['course_id'])) $course_id_arr = explode(',', $data['course_id']);
            if(!empty($data['salary'])) $salary = $data['salary'];
            if(!empty($data['username'])) $staff_username = $data['username'];
            if(!empty($data['password'])) $staff_password = $bf->encode_decode('decrypt', $data['password']);
            if(!empty($data['address'])) $address = $data['address'];
        }
    }
    
    $roles = $bf->getTableRecords($GLOBALS['role_table']);
    $courses = $bf->getTableRecords($GLOBALS['course_table']);
?>

    <div class="header">
        <h2><?php echo empty($view_staff_id) ? "New Staff" : "Update Staff"; ?></h2>
    </div>

    <div class="module-section form-section">
        <form name="staff_form" id="staff_form" method="POST" enctype="multipart/form-data" onsubmit="event.preventDefault(); formSubmit('staff_form', 'staff_action.php', 'staff.php', 'staff');">
            <input type="hidden" name="edit_staff_id" value ="<?php echo $view_staff_id; ?>">

            <div class="form-row">
                <div class="form-group col-4">
                    <label>Staff Name *</label>
                    <input type="text" name="staff_name" class="form-input" value="<?php echo $staff_name; ?>" onkeypress="return allowLettersOnly(event)">
                    <span id="error-staff_name" class="error-msg"></span>
                </div> 

                <div class="form-group col-4">
                    <label>Contact Number *</label>
                    <input type="text" name="staff_number" class="form-input" value="<?php echo $staff_number; ?>" onkeypress="return allowNumbersOnly(event)" maxlength="10">
                    <span id="error-staff_number" class="error-msg"></span>
                </div>

                <div class="form-group col-4">
                    <label>Role *</label>
                    <select name="role_id" class="form-input">
                        <option value="">Select Role</option>
                        <?php foreach($roles as $role) { ?>
                            <option value="<?php echo $role['role_id']; ?>" <?php echo ($role_id == $role['role_id']) ? 'selected' : ''; ?>>
                                <?php echo $role['role_name']; ?>
                            </option>
                        <?php } ?>
                    </select>
                    <span id="error-role_id" class="error-msg"></span>
                </div>                 
            </div>
            
            <div class="form-row">
                <div class="form-group col-4">
                    <label>Course </label>
                    <select name="course_id[]" id="course_id" class="form-input" multiple="multiple" style="width: 100%;">
                        <?php foreach($courses as $course) { ?>
                            <option value="<?php echo $course['course_id']; ?>" <?php echo in_array($course['course_id'], $course_id_arr) ? 'selected' : ''; ?>>
                                <?php echo $course['course_name']; ?>
                            </option>
                        <?php } ?>
                    </select>
                    <span id="error-course_id" class="error-msg"></span>
                </div>

                <div class="form-group col-4">
                    <label>Salary *</label>
                    <input type="text" name="salary" class="form-input" value="<?php echo $salary; ?>" onkeypress="return allowNumbersOnly(event)">
                    <span id="error-salary" class="error-msg"></span>
                </div>
                
                <div class="form-group col-4">
                    <label>Username *</label>
                    <input type="text" name="username" class="form-input" value="<?php echo $staff_username; ?>">
                    <span id="error-username" class="error-msg"></span>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group col-4">
                    <label>Password *</label>
                    <input type="password" name="password" class="form-input" value="<?php echo $staff_password; ?>">
                    <span id="error-password" class="error-msg"></span>
                </div>
                
                <div class="form-group col-4">
                    <label>Address </label>
                    <textarea name="address" class="form-input" rows="3"><?php echo $address; ?></textarea>
                    <span id="error-address" class="error-msg"></span>
                </div>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn-add">
                    <?php echo empty($view_staff_id) ? "Add Staff" : "Update Staff"; ?>
                </button>
                <?php if(!empty($view_staff_id)) { ?>
                    <a href="staff.php" class="btn-add" style="background: #64748b; font-size: 0.75rem;">Cancel</a>
                <?php } ?>
            </div>
        </form>
        <script>
            setTimeout(function() {
                if($.fn.select2) {
                    $('#course_id').select2({
                        placeholder: "Select Courses",
                        width: '100%'
                    });
                }
            }, 50);
        </script>
    </div>
<?php }

if (isset($_POST['staff_name'])) {
    $staff_name = $bf->sanitize($_POST['staff_name'] ?? '');
    $staff_number = $bf->sanitize($_POST['staff_number'] ?? '');
    $role_id = $bf->sanitize($_POST['role_id'] ?? '');
    $course_id = isset($_POST['course_id']) ? implode(',', $_POST['course_id']) : '';
    $salary = $bf->sanitize($_POST['salary'] ?? '');
    $staff_username = $bf->sanitize($_POST['username'] ?? '');
    $staff_password = $_POST['password'] ?? '';
    $address = $bf->sanitize($_POST['address'] ?? '');
    $edit_staff_id = $bf->sanitize($_POST['edit_staff_id'] ?? '');

    // Validation
    $errors = [];
    $res = $valid->valid_name($staff_name, 'Staff Name');
    if ($res) $errors['staff_name'] = $res;

    $res = $valid->valid_mobile($staff_number, 'Staff Number');
    if ($res) $errors['staff_number'] = $res;

    $res = $valid->common_validation($role_id, 'Role', 'select');
    if ($res) $errors['role_id'] = $res;
    
    // $res = $valid->common_validation($course_id, 'Course', 'select');
    // if ($res) $errors['course_id'] = $res;

    $res = $valid->common_validation($salary, 'Salary', 'text');
    if ($res) $errors['salary'] = $res;

    $res = $valid->common_validation($staff_username, 'Username', 'text');
    if ($res) $errors['username'] = $res;
    
    $res = $valid->valid_password($staff_password, 'Password');
    if ($res) $errors['password'] = $res;

    // $res = $valid->common_validation($address, 'Address', 'text');
    // if ($res) $errors['address'] = $res;

    if(empty($errors)) {
        // Check username already exists in staff or user table
        $user_exist_id = $bf->check_unique_username($staff_username, $GLOBALS['staff_table'], $edit_staff_id);
        if($user_exist_id) {
            $errors['username'] = 'Username already exists';
        }

        // Check mobile already exists in staff or user table
        $mob_exist_id = $bf->check_unique_mobile($staff_number, $GLOBALS['staff_table'], $edit_staff_id);
        if($mob_exist_id) {
            $errors['staff_number'] = 'Mobile number already exists';
        }

        if(!empty($errors)) {
            echo json_encode([
                'status' => 'error',
                'errors' => $errors
            ]);
            exit;
        }

        $encrypted_password = $bf->encode_decode('encrypt', $staff_password);
        $role = $bf->getTableColumnValue($GLOBALS['role_table'], 'role_id', $role_id, 'role_name');

        $data = [
            'staff_name' => $staff_name,
            'staff_number' => $staff_number,
            'role_id' => $role_id,
            'role' => $role,
            'course_id' => $course_id,
            'salary' => $salary,
            'username' => $staff_username,
            'password' => $encrypted_password,
            'address' => $address,
            'updated_date_time' => date('Y-m-d H:i:s'),
        ];

        // echo $GLOBALS['bill_company_id']." hi";

        // INSERT
        if(empty($edit_staff_id)) {
            $data['created_date_time'] = date('Y-m-d H:i:s');
            $data['company_id'] = $GLOBALS['bill_company_id'];
            $bf->InsertSQL(
                $GLOBALS['staff_table'],
                $data,
                'staff_id',
                '',
                'ADD STAFF'
            );

            echo json_encode([
                'status' => 'success',
                'message' => 'Staff added successfully'
            ]);
        }
        // UPDATE
        else {
            $data['company_id'] = $GLOBALS['bill_company_id'];
            $bf->UpdateSQL(
                $GLOBALS['staff_table'],
                $data,
                "id = :id",
                [':id' => $edit_staff_id]
            );

            echo json_encode([
                'status' => 'success',
                'message' => 'Staff updated successfully'
            ]);
        }
        exit;
    }
    else {
        echo json_encode([
            'status' => 'error',
            'errors' => $errors
        ]);
        exit;
    }
} 

if ($action == 'list') {
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
    $search = isset($_POST['search']) ? $_POST['search'] : '';
    $start = ($page - 1) * $limit;

    $result = $bf->getStaffList($start, $limit, $search);
    $staffs = $result['data'];
    $total_records = $result['total_records'];
    $total_pages = ceil($total_records / $limit);

    $roles = $bf->getTableRecords($GLOBALS['role_table']);
    $role_map = [];
    foreach($roles as $r) {
        $role_map[$r['role_id']] = $r['role_name'];
    }

    ?>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Sno</th>
                    <th>Name</th>
                    <th>Number</th>
                    <th>Role</th>
                    <th>Salary</th>
                    <th>Username</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($staffs)) { ?>
                    <tr><td colspan="7" style="text-align:center">No staff found.</td></tr>
                <?php } else { 
                    $sno = $start;
                    foreach ($staffs as $s) { $sno++; ?>
                    <tr>
                        <td><?php echo $sno; ?></td>
                        <td><?php echo $s['staff_name']; ?></td>
                        <td><?php echo $s['staff_number']; ?></td>
                        <td><?php echo $role_map[$s['role_id']] ?? $s['role_id']; ?></td>
                        <td><?php echo $s['salary']; ?></td>
                        <td><?php echo $s['username']; ?></td>
                        <td>
                            <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;" onclick="ShowPage('staff', '<?php echo $s['id']; ?>')">Edit</button>
                            <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; background: #ef4444;" onclick="deleteRecord('staff','<?php echo $s['id']; ?>')">Delete</button>
                        </td>
                    </tr>
                <?php } } ?>
            </tbody>
        </table>
    </div>

    <div class="pagination-container">
        <div class="pagination-info">
            Showing <?php echo ($total_records > 0) ? $start + 1 : 0; ?> to <?php echo min($start + $limit, $total_records); ?> of <?php echo $total_records; ?> entries
        </div>
        <div class="pagination-buttons">
            <button class="page-btn" <?php echo ($page <= 1) ? 'disabled' : ''; ?> onclick="loadData('staff', <?php echo $page - 1; ?>, $('#staff_limit').val(), $('#staff_search').val())">
                <i class="fas fa-chevron-left"></i>
            </button>
            <?php 
            // Show max 5 page buttons
            $start_page = max(1, $page - 2);
            $end_page = min($total_pages, $start_page + 4);
            if ($end_page - $start_page < 4) $start_page = max(1, $end_page - 4);

            for ($i = $start_page; $i <= $end_page; $i++) { ?>
                <button class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>" onclick="loadData('staff', <?php echo $i; ?>, $('#staff_limit').val(), $('#staff_search').val())">
                    <?php echo $i; ?>
                </button>
            <?php } ?>
            <button class="page-btn" <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?> onclick="loadData('staff', <?php echo $page + 1; ?>, $('#staff_limit').val(), $('#staff_search').val())">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
    <?php
    exit;
}

if ($action == 'delete') {
    $id = $bf->sanitize($_POST['id'] ?? '');
    $data = ['deleted' => 1, 'updated_date_time' => $GLOBALS['create_date_time_label']];
    $bf->UpdateSQL($GLOBALS['staff_table'], $data, "id = :id", [':id' => $id]);
    echo "Success";
    exit;
}
?>
