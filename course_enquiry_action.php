<?php require_once 'common_file.php'; 
$action = $_REQUEST['action'] ?? '';

if ($action == 'set_conversion_session') {
    $enquiry_id = $bf->sanitize($_POST['enquiry_id'] ?? '');
    $_SESSION['from_enquiry'] = $enquiry_id;
    echo "Success";
    exit;
}

$enquiry_id = ""; $name = ""; $mobile_number = ""; $degree_completed = ""; $address = ""; $course_id = "";

if (isset($_REQUEST['view_course_enquiry_id'])) {
    $view_course_enquiry_id = $_REQUEST['view_course_enquiry_id'];

    if (!empty($view_course_enquiry_id)) {
        $enquiry_list = $bf->getTableRecords(
            $GLOBALS['course_enquiry_table'],
            'id',
            $view_course_enquiry_id
        );

        if (!empty($enquiry_list)) {
            $data = $enquiry_list[0];
            $enquiry_id = $data['enquiry_id'] ?? '';
            $name = $data['name'] ?? '';
            $mobile_number = $data['mobile_number'] ?? '';
            $degree_completed = $data['degree_completed'] ?? '';
            $address = $data['address'] ?? '';
            $course_id = $data['course_id'] ?? '';
        }
    } else {
        $enquiry_id = $bf->automate_number($GLOBALS['course_enquiry_table'], 'enquiry_id', '', '');
    }
    ?>

    <div class="header">
        <h2>
            <?php echo empty($view_course_enquiry_id) ? "New Course Enquiry" : "Update Course Enquiry"; ?>
        </h2>
    </div>

    <div class="module-section form-section">
        <form
            name="course_enquiry_form"
            id="course_enquiry_form"
            method="POST"
            enctype="multipart/form-data"
            onsubmit="event.preventDefault(); formSubmit('course_enquiry_form', 'course_enquiry_action.php', 'course_enquiry.php', 'course_enquiry');"
        >
            <input type="hidden" name="edit_course_enquiry_id" value="<?php echo htmlspecialchars($view_course_enquiry_id); ?>">

            <div class="form-row">
                <div class="form-group col-4">
                    <label>Enquiry ID</label>
                    <input
                        type="text"
                        name="enquiry_id"
                        class="form-input"
                        value="<?php echo htmlspecialchars($enquiry_id); ?>"
                        readonly
                    >
                </div>

                <div class="form-group col-4">
                    <label>Name *</label>
                    <input
                        type="text"
                        name="name"
                        class="form-input"
                        value="<?php echo htmlspecialchars($name); ?>"
                        onkeypress="return allowLettersOnly(event)"
                        required
                    >
                    <span id="error-name" class="error-msg"></span>
                </div> 

                <div class="form-group col-4">
                    <label>Mobile Number *</label>
                    <input
                        type="text"
                        name="mobile_number"
                        class="form-input"
                        maxlength="10"
                        value="<?php echo htmlspecialchars($mobile_number); ?>"
                        onkeypress="return allowNumbersOnly(event)"
                        required
                    >
                    <span id="error-mobile_number" class="error-msg"></span>
                </div>

                <div class="form-group col-4">
                    <label>Degree Completed *</label>
                    <input
                        type="text"
                        name="degree_completed"
                        class="form-input"
                        value="<?php echo htmlspecialchars($degree_completed); ?>"
                        required
                    >
                    <span id="error-degree_completed" class="error-msg"></span>
                </div>

                <div class="form-group col-4">
                    <label>Course *</label>
                    <select name="course_id" class="form-input" required>
                        <option value="">Select Course</option>
                        <?php
                        $course_list = $bf->getTableRecords($GLOBALS['course_table']);
                        if (!empty($course_list)) {
                            foreach ($course_list as $course) {
                                ?>
                                <option value="<?php echo htmlspecialchars($course['course_id']); ?>" <?php echo ($course_id == $course['course_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($course['course_name']); ?>
                                </option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                    <span id="error-course_id" class="error-msg"></span>
                </div>

                <div class="form-group col-8">
                    <label>Address *</label>
                    <textarea name="address" class="form-input" style="height: 42px;" required><?php echo htmlspecialchars($address); ?></textarea>
                    <span id="error-address" class="error-msg"></span>
                </div>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn-add">
                    <?php echo empty($view_course_enquiry_id) ? "Add Enquiry" : "Update Enquiry"; ?>
                </button>

                <a
                    href="course_enquiry.php"
                    class="btn-add"
                    style="background: #ef4444; font-size: 0.75rem;"
                >
                    Cancel
                </a>
            </div>
        </form>
    </div>

<?php 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name']) && !isset($_POST['action'])) {
    $enquiry_id = $bf->sanitize($_POST['enquiry_id'] ?? '');
    $name = $bf->sanitize($_POST['name'] ?? '');
    $mobile_number = $bf->sanitize($_POST['mobile_number'] ?? '');
    $degree_completed = $bf->sanitize($_POST['degree_completed'] ?? '');
    $address = $bf->sanitize($_POST['address'] ?? '');
    $course_id = $bf->sanitize($_POST['course_id'] ?? '');
    $edit_course_enquiry_id = $bf->sanitize($_POST['edit_course_enquiry_id'] ?? '');

    $errors = [];
    $res = $valid->valid_name($name, 'Name');
    if ($res) $errors['name'] = $res;

    $res = $valid->valid_mobile($mobile_number, 'Mobile Number');
    if ($res) $errors['mobile_number'] = $res;

    $res = $valid->common_validation($degree_completed, 'Degree Completed', 'text');
    if ($res) $errors['degree_completed'] = $res;

    $res = $valid->common_validation($address, 'Address', 'text');
    if ($res) $errors['address'] = $res;

    $res = $valid->common_validation($course_id, 'Course', 'select');
    if ($res) $errors['course_id'] = $res;

    if (empty($errors)) {
        $data = [
            'name' => $name,
            'mobile_number' => $mobile_number,
            'degree_completed' => $degree_completed,
            'address' => $address,
            'course_id' => $course_id,
            'updated_date_time' => date('Y-m-d H:i:s')
        ];

        if (empty($edit_course_enquiry_id)) {
            $data['enquiry_id'] = $enquiry_id;
            $data['company_id'] = $_SESSION['company_id'] ?? $bf->getCompanyId();
            $data['converted_type'] = 'none';
            $data['deleted'] = 0;
            $data['created_date_time'] = date('Y-m-d H:i:s');

            $bf->InsertSQL(
                $GLOBALS['course_enquiry_table'],
                $data,
                '',
                '',
                'ADD COURSE ENQUIRY'
            );

            echo json_encode([
                'status' => 'success',
                'message' => 'Course Enquiry added successfully'
            ]);
        } else {
            $bf->UpdateSQL(
                $GLOBALS['course_enquiry_table'],
                $data,
                "id = :id",
                [':id' => $edit_course_enquiry_id]
            );

            echo json_encode([
                'status' => 'success',
                'message' => 'Course Enquiry updated successfully'
            ]);
        }
        exit;
    } else {
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

    $result = $bf->getTableList($GLOBALS['course_enquiry_table'], ['name', 'mobile_number', 'degree_completed'], $start, $limit, $search);
    $enquiries = $result['data'];
    $total_records = $result['total_records'];
    $total_pages = ceil($total_records / $limit);

    if (empty($enquiries)) { ?>
        <div class="table-responsive">
            <table><tr><td style="text-align:center">No course enquiries found.</td></tr></table>
        </div>
    <?php } else {
        ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Sno</th>
                        <th>Enquiry ID</th>
                        <th>Name</th>
                        <th>Mobile Number</th>
                        <th>Degree Completed</th>
                        <th>Address</th>
                        <th>Course Name</th>
                        <th>Created Date</th>
                        <th>Converted Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $sno = $start + 1;
                foreach ($enquiries as $u) { 
                    $course_name = $bf->getTableColumnValue($GLOBALS['course_table'], 'course_id', $u['course_id'], 'course_name');
                    $converted_type = $u['converted_type'] ?? 'none';
                    $converted_id = $u['converted_id'] ?? '';
                    $created_date = !empty($u['created_date_time']) ? date('d-m-Y H:i', strtotime($u['created_date_time'])) : '';

                    $converted_id = $bf->encode_decode("decrypt", $u['converted_id']) ?? '';
                ?>
                    <tr>
                        <td><?php echo $sno++; ?></td>
                        <td><span style="font-weight: 600; color: var(--text-dark);"><?php echo htmlspecialchars($u['enquiry_id'] ?? ''); ?></span></td>
                        <td><span style="font-weight: 600; color: var(--primary);"><?php echo htmlspecialchars($u['name']); ?></span></td>
                        <td><?php echo htmlspecialchars($u['mobile_number']); ?></td>
                        <td><?php echo htmlspecialchars($u['degree_completed']); ?></td>
                        <td><?php echo htmlspecialchars($u['address']); ?></td>
                        <td><?php echo htmlspecialchars($course_name); ?></td>
                        <td><?php echo htmlspecialchars($created_date); ?></td>
                        <td>
                            <?php if ($converted_type === 'enrollment') { ?>
                                <span class="status-badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600;">Converted to Enrollment (<?php echo htmlspecialchars($converted_id); ?>)</span>
                            <?php } elseif ($converted_type === 'internship') { ?>
                                <span class="status-badge" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600;">Converted to Internship (<?php echo htmlspecialchars($converted_id); ?>)</span>
                            <?php } else { ?>
                                <span class="status-badge" style="background: rgba(100, 116, 139, 0.1); color: #64748b; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600;">Not Converted</span>
                            <?php } ?>
                        </td>
                        <td>
                            <div style="display:flex; gap:0.5rem; align-items: center;">
                                <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'course_enquiry', PERMISSION_EDIT)): ?>
                                    <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;" onclick="ShowPage('course_enquiry', '<?php echo $u['id']; ?>')">Edit</button>
                                <?php endif; ?>
                                <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'course_enquiry', PERMISSION_DELETE)): ?>
                                    <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; background: #ef4444;" onclick="deleteRecord('course_enquiry', '<?php echo $u['id']; ?>')">Delete</button>
                                <?php endif; ?>
                                
                                <?php if ($converted_type === 'none') { ?>
                                    <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'course_enquiry', PERMISSION_EDIT)): ?>
                                        <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; background: #8b5cf6;" onclick="convertEnquiry('<?php echo htmlspecialchars($u['enquiry_id'] ?? ''); ?>')">Convert</button>
                                    <?php endif; ?>
                                <?php } else { ?>
                                    <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; background: #cbd5e1; color: #94a3b8; cursor: not-allowed;" disabled>Convert</button>
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
                <button class="page-btn" <?php echo ($page <= 1) ? 'disabled' : ''; ?> onclick="loadData('course_enquiry', <?php echo $page - 1; ?>, $('#course_enquiry_limit').val(), $('#course_enquiry_search').val())">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <?php 
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $start_page + 4);
                if ($end_page - $start_page < 4) $start_page = max(1, $end_page - 4);
                for ($i = $start_page; $i <= $end_page; $i++) { ?>
                    <button class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>" onclick="loadData('course_enquiry', <?php echo $i; ?>, $('#course_enquiry_limit').val(), $('#course_enquiry_search').val())">
                        <?php echo $i; ?>
                    </button>
                <?php } ?>
                <button class="page-btn" <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?> onclick="loadData('course_enquiry', <?php echo $page + 1; ?>, $('#course_enquiry_limit').val(), $('#course_enquiry_search').val())">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    <?php
    }
    exit;
}

if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    $id = $bf->sanitize($_POST['id'] ?? '');
    $data = ['deleted' => 1, 'updated_date_time' => date('Y-m-d H:i:s')];
    $bf->UpdateSQL($GLOBALS['course_enquiry_table'], $data, "id = :id", [':id' => $id]);
    echo "Success";
}
?>
