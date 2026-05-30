<?php require_once 'common_file.php'; 
$action = $_REQUEST['action'] ?? '';

   $course_id = ""; $course_name = "";  $course_duration = ""; $course_fee = ""; 

if(isset($_REQUEST['view_course_id'])) {

        $view_course_id = $_REQUEST['view_course_id'];

        $course_list = $bf->getTableRecords(
            $GLOBALS['course_table'],
            'course_id',
            $view_course_id
        );

        if(!empty($course_list)) {
            foreach($course_list as $data) {

               if(!empty($data['course_name'])) {
                    $course_name = $data['course_name'];
                }

                if(!empty($data['course_duration'])) {
                    $course_duration = $data['course_duration'];
                }

                if(!empty($data['course_fee'])) {
                    $course_fee = $data['course_fee'];
                }

            }
        }
    ?>

    <!-- <div class="main-content"> -->

        <div class="header">
            <h2>
                <?php echo empty($view_course_id) ? "New course" : "Update course"; ?>
            </h2>
        </div>

        <div class="module-section form-section">

            <form
                name="course_form"
                id="course_form"
                method="POST"
                enctype="multipart/form-data"
                onsubmit="event.preventDefault(); formSubmit('course_form', 'course_action.php', 'course.php', 'course');"
            >

                <input type="hidden" name="edit_course_id" value ="<?php echo $view_course_id; ?>">

                <div class="form-row">

                    <div class="form-group col-4">
                        <label>Course Name *</label>

                        <input
                            type="text"
                            name="course_name"
                            class="form-input"
                            value="<?php echo $course_name; ?>"
                            onkeypress="return allowLettersOnly(event)"
                            
                        >

                        <span id="error-course_name" class="error-msg"></span>
                    </div> 

                    <div class="form-group col-4">
                        <label>Duration (Months)</label>

                        <input
                            type="text"
                            name="duration"
                            class="form-input" id="course_duration"
                               value="<?php echo $course_duration; ?>"
                            onkeypress="return allowNumbersOnly(event)"
                        >

                        <span id="error-duration" class="error-msg"></span>
                    </div>

                    <div class="form-group col-4">
                        <label>Fees Amount *</label>

                        <input
                            type="text"
                            name="fees_amount"
                            class="form-input" 
                                value="<?php echo $course_fee; ?>"
                            onkeypress="return allowNumbersOnly(event)"
                            
                        >

                        <span id="error-fees_amount" class="error-msg"></span>
                    </div>                 
                </div>

                <div class="form-buttons">

                    <button type="submit" class="btn-add">

                        <?php echo empty($view_course_id) ? "Add course" : "Update course"; ?>

                    </button>

                    <?php if(!empty($view_course_id)) { ?>

                        <a
                            href="course.php"
                            class="btn-add"
                            style="background: #ef4444; font-size: 0.75rem;"
                        >
                            Cancel
                        </a>

                    <?php } ?>

                </div>

            </form>

        </div>

    <!-- </div> -->

<?php }

if (isset($_POST['course_name'])) {
    $course_name = $bf->sanitize($_POST['course_name'] ?? '');
    $duration = $bf->sanitize($_POST['duration'] ?? '');
    $fees_amount = $bf->sanitize($_POST['fees_amount'] ?? '');
    $edit_course_id = $bf->sanitize($_POST['edit_course_id'] ?? '');

    // Validation
    $errors = [];
    $res = $valid->valid_name($course_name, 'Course Name');
    if ($res) $errors['course_name'] = $res;

    $res = $valid->common_validation($duration, 'Duration', 'text');
    if ($res) $errors['duration'] = $res;

    $res = $valid->common_validation($fees_amount, 'Fees Amount', 'text');
    if ($res) $errors['fees_amount'] = $res;

    if(empty($errors)) {

        // Check already exists
        $stmt = $bf->con->prepare("
            SELECT course_id 
            FROM " . $GLOBALS['course_table'] . " 
            WHERE LOWER(course_name) = :course_name 
            AND deleted = 0
        ");

        $stmt->execute([
            ':course_name' => strtolower($course_name)
        ]);

        if($stmt->fetch()) {

            echo json_encode([
                'status' => 'error',
                'message' => 'Course name already exists'
            ]);

            exit;
        }

        $data = [
            'course_name' => $course_name,
            'course_duration' => $duration,
            'course_fee' => $fees_amount,
            'created_date_time' => date('Y-m-d H:i:s'),
            'updated_date_time' => date('Y-m-d H:i:s'),
        ];

        // INSERT
        if(empty($edit_course_id)) {

            $bf->InsertSQL(
                $GLOBALS['course_table'],
                $data,
                'course_id',
                '',
                'ADD COURSE'
            );

            echo json_encode([
                'status' => 'success',
                'message' => 'Course added successfully'
            ]);

        }

        // UPDATE
        else {

            $bf->UpdateSQL(
                $GLOBALS['course_table'],
                $data,
                "course_id = :course_id",
                [
                    ':course_id' => $edit_course_id
                ]
            );

            echo json_encode([
                'status' => 'success',
                'message' => 'Course updated successfully'
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

    $result = $bf->getTableList($GLOBALS['course_table'], ['course_name'], $start, $limit, $search);
    $courses = $result['data'];
    $total_records = $result['total_records'];
    $total_pages = ceil($total_records / $limit);

    if (empty($courses)) { ?>
        <div class="table-responsive">
            <table><tr><td style="text-align:center">No courses found.</td></tr></table>
        </div>
    <?php } else {
        ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Sno</th>
                        <th>Name</th>
                        <th>Duration</th>
                        <th>Fees Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $sno = $start + 1;
                foreach ($courses as $u) { 
                ?>
                    <tr>
                        <td><?php echo $sno++; ?></td>
                        <td><span style="font-weight: 600; color: var(--primary);"><?php echo $u['course_name']; ?></span></td>
                        <td><?php echo $u['course_duration']; ?> Months</td>
                        <td>₹<?php echo number_format($u['course_fee'], 2); ?></td>
                        <td>
                            <div style="display:flex; gap:0.5rem;">
                                <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'course', PERMISSION_EDIT)): ?>
                                    <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;" onclick="ShowPage('course', '<?php echo $u['course_id']; ?>')">Edit</button>
                                <?php endif; ?>
                                <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'course', PERMISSION_DELETE)): ?>
                                    <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; background: #ef4444;" onclick="deleteRecord('course', '<?php echo $u['course_id']; ?>')">Delete</button>
                                <?php endif; ?>
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
                <button class="page-btn" <?php echo ($page <= 1) ? 'disabled' : ''; ?> onclick="loadData('course', <?php echo $page - 1; ?>, $('#course_limit').val(), $('#course_search').val())">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <?php 
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $start_page + 4);
                if ($end_page - $start_page < 4) $start_page = max(1, $end_page - 4);
                for ($i = $start_page; $i <= $end_page; $i++) { ?>
                    <button class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>" onclick="loadData('course', <?php echo $i; ?>, $('#course_limit').val(), $('#course_search').val())">
                        <?php echo $i; ?>
                    </button>
                <?php } ?>
                <button class="page-btn" <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?> onclick="loadData('course', <?php echo $page + 1; ?>, $('#course_limit').val(), $('#course_search').val())">
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
    $data = ['deleted' => 1, 'updated_date_time' => $GLOBALS['create_date_time_label']];
    $bf->UpdateSQL($GLOBALS['course_table'], $data, "course_id = :id", [':id' => $id]);
    echo "Success";
}

if (isset($_POST['action']) && $_POST['action'] == 'get_details') {
    $course_id = $bf->sanitize($_POST['course_id'] ?? '');
    $course = $bf->getTableRecords($GLOBALS['course_table'], 'course_id', $course_id);
    if(!empty($course)) {
        $course_fee = $course[0]['course_fee'] ?? '';
        $course_duration = $course[0]['course_duration'] ?? '';
       
        echo json_encode([
            'status' => 'success',
            'data' => [
                'course_fee' => $course_fee,
                'course_duration' => $course_duration
            ]
        ]);
    }   else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Course not found'
        ]);
    }
}
?>
