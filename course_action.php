<?php require_once 'common_file.php'; 
if ($user_role != 'admin') { header("Location: dashboard.php"); exit(); }
$action = $_GET['action'] ?? '';

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

if (isset($action) &&  ($action == 'list')) {
    $courses = $bf->getTableRecords($GLOBALS['course_table'], 'deleted', 0);
    // print_r($courses);
    $sno = 0;
    if (empty($courses)) {
        echo "<p>No courses found.</p>";
    } else { ?> 
        <table>

            <tr>
                <th>Sno</th>
                <th>Name</th>
                <th>Duration</th>
                <th>Fees Amount</th>
                <th>Action</th>
            </tr>

            <?php foreach ($courses as $u) { $sno++; ?>

                <tr>

                    <td>
                        <?php echo $sno; ?>
                    </td>

                    <td>
                        <?php echo $u['course_name']; ?>
                    </td>

                    <td>
                        <?php echo $u['course_duration']; ?>
                    </td>

                    <td>
                        <?php echo $u['course_fee']; ?>
                    </td>

                    <td>
                        <button
                            class="btn-add"
                            onclick="Javacript:ShowPage('course', '<?php echo $u['course_id']; ?>')"
                        >
                            Edit
                        </button>
                        <button
                            class="btn-add"
                            style="background: #ef4444; font-size: 0.75rem;"
                            onclick="deleteRecord('course','<?php echo $u['course_id']; ?>')"
                        >
                            Delete
                        </button>

                    </td>

                </tr>

            <?php } ?>

        </table>

    <?php
    }
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
