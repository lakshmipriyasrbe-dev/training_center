<?php require_once 'common_file.php'; 
if ($user_role != 'admin') { header("Location: dashboard.php"); exit(); }
$action = $_POST['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['closure_date']) && empty($action)) {
    $closure_date = $bf->sanitize($_POST['closure_date'] ?? '');
    $course_type = $bf->sanitize($_POST['course_type'] ?? '');
    $student_id = $bf->sanitize($_POST['student_id'] ?? '');
    $certificate_got = isset($_POST['certificate_got']) ? 1 : 2;
    $view_course_closure_id = $bf->sanitize($_POST['view_course_closure_id'] ?? '');
    $placement = isset($_POST['placement']) ? 1 : 2;
    $company_name = $bf->sanitize($_POST['company_name'] ?? '');
    $company_address = $bf->sanitize($_POST['company_address'] ?? '');
    $designation = $bf->sanitize($_POST['designation'] ?? '');
    $ctc = $bf->sanitize($_POST['ctc'] ?? '');

    $errors = [];

    if (empty($closure_date)) {
        $errors['closure_date'] = 'Please select closure date';
    } else {
        $today = date('Y-m-d');
        if (strtotime($closure_date) > strtotime($today)) {
            $errors['closure_date'] = 'Closure date cannot be a future date';
        }
    }

    if (empty($course_type) || !in_array($course_type, ['training', 'internship'])) {
        $errors['course_type'] = 'Please select a valid course type';
    }

    if (empty($student_id)) {
        $errors['student_id'] = 'Please select a student';
    } else {
        // Check if student ID exists in the respective enrollment table
        $enrollment_table = $course_type === 'internship' ? $GLOBALS['enrollment_internship_table'] : $GLOBALS['enrollment_table'];
        $student_id_encrypted = $bf->encode_decode('encrypt', $student_id);
        $student_records = $bf->getTableRecords($enrollment_table, 'student_id', $student_id_encrypted);
        if (empty($student_records)) {
            $errors['student_id'] = 'Selected student does not exist';
        }
    }   

    
    if($placement ==1) {
        if (empty($company_name)) {
            $errors['company_name'] = 'Please enter company name';
        } else {
            $res = $valid->common_validation($company_name, 'Company Name', 'text');
            if ($res) $errors['company_name'] = $res;
        }
        if (empty($company_address)) {
            $errors['company_address'] = 'Please enter company address';
        } else {
            $res = $valid->common_validation($company_address, 'Company Address', 'text');
            if ($res) $errors['company_address'] = $res;
        }
        if (empty($designation)) {
            $errors['designation'] = 'Please enter designation';
        } else {
            $res = $valid->common_validation($designation, 'Designation', 'text');
            if ($res) $errors['designation'] = $res;
        }
        if (empty($ctc)) {
            $errors['ctc'] = 'Please enter CTC';
        } else {
            $res = $valid->common_validation($ctc, 'CTC', 'text');
            if ($res) $errors['ctc'] = $res;
        }
    } else {
        $company_name = null;
        $company_address = null;
        $designation = null;
        $ctc = null;
    }

    if (empty($errors)) {
        // Fetch student name from enrollment table
        $enrollment_table = $course_type === 'internship' ? $GLOBALS['enrollment_internship_table'] : $GLOBALS['enrollment_table'];
        $student_id_encrypted = $bf->encode_decode('encrypt', $student_id);
        $student_records = $bf->getTableRecords($enrollment_table, 'student_id', $student_id_encrypted);
        $student_name = '';
        if (!empty($student_records)) {
            $student_name = $student_records[0]['student_name'] ?? '';
        } 

        // echo $student_name." hi"; exit;

        $data = [
            'course_closed' => 1,
            'closure_date' => $closure_date,
            'course_type' => $course_type,
            'student_id' => $student_id_encrypted,
            'student_name' => $student_name,
            'certificate_got' => $certificate_got,
            'placed' => $placement,
            'company_name' => $company_name,
            'company_address' => $company_address,
            'designation' => $designation,
            'ctc' => $ctc,
            'updated_date_time' => date('Y-m-d H:i:s')
        ];

        header('Content-Type: application/json');
        if (empty($view_course_closure_id)) {
            $data['created_date_time'] = date('Y-m-d H:i:s');
            $bf->InsertSQL(
                $GLOBALS['course_closure_table'],
                $data,
                'closure_id',
                '',
                'ADD COURSE CLOSURE'
            );
            
            // Mark course as closed in enrollment table
            $bf->UpdateSQL(
                $enrollment_table,
                ['course_closed' => 1],
                'student_id = :student_id',
                [':student_id' => $student_id_encrypted]
            );
            
            echo json_encode(['status' => 'success', 'message' => 'Course closure added successfully']);
        } else {
            $bf->UpdateSQL(
                $GLOBALS['course_closure_table'],
                $data,
                'id = :id',
                [':id' => $view_course_closure_id]
            );
            echo json_encode(['status' => 'success', 'message' => 'Course closure updated successfully']);
        }
        exit;
    }

    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'errors' => $errors]);
    exit;
}

$closure_date = ''; $course_type = ''; $student_id = ''; $certificate_got = 2; $view_course_closure_id = ''; $placement = 2; $company_name = ''; $company_address = ''; $designation = ''; $ctc = '';

if (isset($_REQUEST['view_course_closure_id'])) {
    $view_course_closure_id = $_REQUEST['view_course_closure_id'];
    $closures = $bf->getTableRecords($GLOBALS['course_closure_table'], 'id', $view_course_closure_id);
    if (!empty($closures)) {
        $closure = $closures[0];
        if (!empty($closure['closure_date'])) {
            $closure_date = $closure['closure_date'];
        }
        if (!empty($closure['course_type'])) {
            $course_type = $closure['course_type'];
        }
        if (!empty($closure['student_id'])) {
            $student_id = $bf->encode_decode('decrypt', $closure['student_id']);
        }
        if (!empty($closure['certificate_got'])) {
            $certificate_got = (int) $closure['certificate_got'];
        }
        if (!empty($closure['placed'])) {
            $placement = (int) $closure['placed'];
        }
        if (!empty($closure['company_name'])) {
            $company_name = $closure['company_name'];
        }
        if (!empty($closure['company_address'])) {
            $company_address = $closure['company_address'];
        }
        if (!empty($closure['designation'])) {
            $designation = $closure['designation'];
        }
        if (!empty($closure['ctc'])) {
            $ctc = $closure['ctc'];
        }
    }
}

$training_students = $bf->getTableRecords($GLOBALS['enrollment_table'], 'deleted', 0);
$internship_students = $bf->getTableRecords($GLOBALS['enrollment_internship_table'], 'deleted', 0);

// Get list of already closed students
$closed_closures = $bf->getTableRecords($GLOBALS['course_closure_table'], 'deleted', 0);
$closed_student_ids = [];
foreach ($closed_closures as $closure) {
    $closed_student_ids[] = $closure['student_id'];
}

// Filter out already closed students (unless it's the current edit record)
$training_students_filtered = [];
$internship_students_filtered = [];

foreach ($training_students as $row) {
    $sid = $bf->encode_decode('decrypt', $row['student_id']);
    // Include if not closed OR if it's the student being edited
    if (!in_array($sid, $closed_student_ids) || $sid === $student_id) {
        $training_students_filtered[] = $row;
    }
}

foreach ($internship_students as $row) {
    $sid = $bf->encode_decode('decrypt', $row['student_id']);
    // Include if not closed OR if it's the student being edited
    if (!in_array($sid, $closed_student_ids) || $sid === $student_id) {
        $internship_students_filtered[] = $row;
    }
}

$student_details_js = [
    'training' => [],
    'internship' => []
];

foreach ($training_students_filtered as $row) {
    $sid = $bf->encode_decode('decrypt', $row['student_id']);
    $course_name = $bf->getTableColumnValue($GLOBALS['course_table'], 'course_id', $row['course_id'], 'course_name');
    $trainer_name = $bf->getTableColumnValue($GLOBALS['staff_table'], 'staff_id', $row['staff_id'], 'staff_name');
    $student_details_js['training'][$sid] = [
        'student_name' => $row['student_name'] ?? '',
        'course_name' => $course_name ?? '',
        'trainer_name' => $trainer_name ?? '',
        'course_joining_date' => $row['doj'] ?? ''
    ];
}

foreach ($internship_students_filtered as $row) {
    $sid = $bf->encode_decode('decrypt', $row['student_id']);
    $course_name = $bf->getTableColumnValue($GLOBALS['course_table'], 'course_id', $row['course_id'], 'course_name');
    $trainer_name = $bf->getTableColumnValue($GLOBALS['staff_table'], 'staff_id', $row['staff_id'], 'staff_name');
    $student_details_js['internship'][$sid] = [
        'student_name' => $row['student_name'] ?? '',
        'course_name' => $course_name ?? '',
        'trainer_name' => $trainer_name ?? '',
        'course_joining_date' => $row['doj'] ?? ''
    ];
}

?>

<?php if (!isset($action) || $action !== 'list') { ?>

    <div class="header">
        <h2><?php echo empty($view_course_closure_id) ? 'New Course Closure' : 'Update Course Closure'; ?></h2>
    </div>

    <div class="module-section form-section">
        <form
            name="course_closure_form"
            id="course_closure_form"
            method="POST"
            onsubmit="event.preventDefault(); formSubmit('course_closure_form', 'course_closure_action.php', 'course_closure.php', 'course_closure');"
        >
            <input type="hidden" name="view_course_closure_id" value="<?php echo $view_course_closure_id; ?>">

            <div class="form-row">
                <div class="form-group col-4">
                    <label>Closure Date *</label>
                    <input
                        type="date"
                        name="closure_date"
                        id="closure_date"
                        class="form-input"
                        value="<?php echo $closure_date; ?>"
                        max="<?php echo date('Y-m-d'); ?>"
                    >
                    <span id="error-closure_date" class="error-msg"></span>
                </div>

                <div class="form-group col-4">
                    <label>Course Type *</label>
                    <select name="course_type" id="course_type" class="form-input" onchange="onCourseTypeChange();">
                        <option value="">Select</option>
                        <option value="training" <?php echo $course_type === 'training' ? 'selected' : ''; ?>>Training</option>
                        <option value="internship" <?php echo $course_type === 'internship' ? 'selected' : ''; ?>>Internship</option>
                    </select>
                    <span id="error-course_type" class="error-msg"></span>
                </div>

                <div class="form-group col-4">
                    <label>Student *</label>
                    <select name="student_id" id="student_id" class="form-input">
                        <option value="">Select</option>
                    </select>
                    <span id="error-student_id" class="error-msg"></span>
                    <a href="javascript:void(0);" id="get_student_details_link" onclick="showStudentDetails();" style="display: inline-block; margin-top: 0.5rem; color: var(--primary); font-size: 0.9rem;">Get student details</a>
                </div>

                <div class="form-group col-3">
                    <label>Certificate Got</label>
                    <div class="checkbox-group" style="margin-top: 0.5rem;">
                        <label>
                            <input
                                type="checkbox"
                                name="certificate_got"
                                value="1"
                                <?php echo $certificate_got === 1 ? 'checked' : ''; ?>
                            >
                            Got Certificate
                        </label>
                    </div>
                </div>
                <div class="form-group col-3">

                    <label>Placement</label>

                    <div class="checkbox-group" style="margin-top: 0.5rem;">

                        <label>
                            <input
                                type="checkbox"
                                name="placement"
                                value="1"
                                <?php echo $placement == 1 ? 'checked' : ''; ?>
                            >

                            Placement Got
                        </label>

                    </div>

                </div>
                <div class="form-group col-4 placed d-none">
                    <label>Company Name *</label>
                    <input type="text" name="company_name" class="form-input" value="<?php echo $company_name; ?>" onkeypress="return allowLettersOnly(event)">
                    <span id="error-company_name" class="error-msg"></span>
                </div> 
                <div class="form-group col-4 placed d-none">
                    <label>Company Address *</label>

                    <textarea
                        name="company_address"
                        class="form-input"
                        
                    ><?php echo $company_address; ?></textarea>

                    <span id="error-company_address" class="error-msg"></span>
                </div>
                <div class="form-group col-4 placed d-none">
                    <label>Designation *</label>
                    <input type="text" name="designation" class="form-input" value="<?php echo $designation; ?>" onkeypress="return allowLettersOnly(event)">
                    <span id="error-designation" class="error-msg"></span>
                </div>
                 <div class="form-group col-4 placed d-none">
                    <label>CTC/month *</label>
                    <input type="text" name="ctc" class="form-input" value="<?php echo $ctc; ?>" onkeypress="return allowNumbersOnly(event)">
                    <span id="error-ctc" class="error-msg"></span>
                </div>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn-add"><?php echo empty($view_course_closure_id) ? 'Add Closure' : 'Update Closure'; ?></button>
                <?php if (!empty($view_course_closure_id)) { ?>
                    <a href="course_closure.php" class="btn-add" style="background: #ef4444; font-size: 0.75rem;">Cancel</a>
                <?php } ?>
            </div>
        </form>
    </div>

    <div id="student_details_modal" class="modal">
        <div class="modal-content">
            <h3>Student Details</h3>
            <div id="student_details_body"></div>
            <div style="text-align: right; margin-top: 1rem;">
                <button class="btn-add" onclick="closeStudentDetails();">Close</button>
            </div>
        </div>
    </div>

    <script>
        const studentLists = <?php echo json_encode([ 'training' => array_map(function($row) use ($bf) {
            return [
                'id' => $bf->encode_decode('decrypt', $row['student_id']),
                'label' => $bf->encode_decode('decrypt', $row['student_id']) . ' - ' . $row['student_name']
            ];
        }, $training_students_filtered), 'internship' => array_map(function($row) use ($bf) {
            return [
                'id' => $bf->encode_decode('decrypt', $row['student_id']),
                'label' => $bf->encode_decode('decrypt', $row['student_id']) . ' - ' . $row['student_name']
            ];
        }, $internship_students_filtered) ]); ?>;

        const studentDetails = <?php echo json_encode($student_details_js); ?>;
        let selectedCourseType = '<?php echo $course_type; ?>';
        let selectedStudentId = '<?php echo addslashes($student_id); ?>';

        function populateStudentOptions() {
            const studentSelect = document.getElementById('student_id');
            const list = studentLists[selectedCourseType] || [];
            studentSelect.innerHTML = '<option value="">Select</option>';
            list.forEach(function(student) {
                const selected = student.id === selectedStudentId ? 'selected' : '';
                studentSelect.innerHTML += `<option value="${student.id}" ${selected}>${student.label}</option>`;
            });
        }

        function onCourseTypeChange() {
            selectedCourseType = document.getElementById('course_type').value;
            selectedStudentId = '';
            populateStudentOptions();
        }

        function showStudentDetails() {
            const courseType = document.getElementById('course_type').value;
            const studentId = document.getElementById('student_id').value;

            if (!courseType) {
                alert('Please select course type first');
                return;
            }
            if (!studentId) {
                alert('Please select a student first');
                return;
            }

            const details = studentDetails[courseType] && studentDetails[courseType][studentId];
            if (!details) {
                alert('Student details are not available');
                return;
            }

            const body = document.getElementById('student_details_body');
            body.innerHTML = `
                <p><strong>Student Name:</strong> ${details.student_name || 'N/A'}</p>
                <p><strong>Course Joined:</strong> ${details.course_name || 'N/A'}</p>
                <p><strong>Trainer:</strong> ${details.trainer_name || 'N/A'}</p>
                <p><strong>Course Joining Date:</strong> ${details.course_joining_date || 'N/A'}</p>
            `;

            document.getElementById('student_details_modal').style.display = 'flex';
        }

        function closeStudentDetails() {
            document.getElementById('student_details_modal').style.display = 'none';
        }

        document.getElementById('student_details_modal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeStudentDetails();
            }
        });

        populateStudentOptions();

        if(document.querySelector('input[name="placement"]')) {

            var placementCheckbox = document.querySelector('input[name="placement"]');

            placementCheckbox.addEventListener('change', function() {

                var placedFields = document.querySelectorAll('.placed');

                placedFields.forEach(function(field) {

                    if(placementCheckbox.checked) {

                        field.classList.remove('d-none');

                    } else {

                        field.classList.add('d-none');
                    }

                });

            });

            // Page load check
            placementCheckbox.dispatchEvent(new Event('change'));
        }
    </script>

<?php }

if ($action == 'list') {
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
    $search = isset($_POST['search']) ? $_POST['search'] : '';
    $start = ($page - 1) * $limit;

    $result = $bf->getTableList($GLOBALS['course_closure_table'], ['student_name', 'course_type'], $start, $limit, $search);
    $closures = $result['data'];
    $total_records = $result['total_records'];
    $total_pages = ceil($total_records / $limit);

    if (empty($closures)) { ?>
        <div class="table-responsive">
            <table><tr><td style="text-align:center">No course closures found.</td></tr></table>
        </div>
    <?php } else {
        ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Closure Date</th>
                        <th>Course Type</th>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Certificate</th>
                        <th>Placed</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                foreach ($closures as $u) {
                    $certificate_text = $u['certificate_got'] == 1 ? 'Got' : 'Not Got';
                    $cert_style = $u['certificate_got'] == 1 ? 'background: #10b98115; color: #10b981;' : 'background: #ef444415; color: #ef4444;';
                    $placed_text = $u['placed'] == 1 ? 'Yes' : 'No';
                ?>
                    <tr>
                        <td><?php echo date('d-m-Y', strtotime($u['closure_date'])); ?></td>
                        <td><span class="status-badge" style="background: var(--primary-light); color: var(--primary);"><?php echo ucfirst($u['course_type']); ?></span></td>
                        <td><?php echo $bf->encode_decode('decrypt', $u['student_id']); ?></td>
                        <td><strong style="color: var(--primary);"><?php echo $u['student_name']; ?></strong></td>
                        <td><span class="status-badge" style="<?php echo $cert_style; ?>"><?php echo $certificate_text; ?></span></td>
                        <td><span class="status-badge" style="background: <?php echo $u['placed'] == 1 ? '#10b98115' : '#ef444415'; ?>; color: <?php echo $u['placed'] == 1 ? '#10b981' : '#ef4444'; ?>;"><?php echo $placed_text; ?></span></td>
                        <td>
                            <div style="display:flex; gap:0.5rem;">
                                <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;" onclick="ShowPage('course_closure', '<?php echo $u['id']; ?>')">Edit</button>
                                <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; background: #ef4444;" onclick="deleteRecord('course_closure', '<?php echo $u['id']; ?>')">Delete</button>
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
                <button class="page-btn" <?php echo ($page <= 1) ? 'disabled' : ''; ?> onclick="loadData('course_closure', <?php echo $page - 1; ?>, $('#course_closure_limit').val(), $('#course_closure_search').val())">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <?php 
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $start_page + 4);
                if ($end_page - $start_page < 4) $start_page = max(1, $end_page - 4);
                for ($i = $start_page; $i <= $end_page; $i++) { ?>
                    <button class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>" onclick="loadData('course_closure', <?php echo $i; ?>, $('#course_closure_limit').val(), $('#course_closure_search').val())">
                        <?php echo $i; ?>
                    </button>
                <?php } ?>
                <button class="page-btn" <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?> onclick="loadData('course_closure', <?php echo $page + 1; ?>, $('#course_closure_limit').val(), $('#course_closure_search').val())">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    <?php
    }
    exit;
}

if (isset($action) && ($action == 'delete')) {
    $id = $bf->sanitize($_POST['id'] ?? '');
    $data = ['deleted' => 1, 'updated_date_time' => $GLOBALS['create_date_time_label']];
    $bf->UpdateSQL($GLOBALS['course_closure_table'], $data, 'id = :id', [':id' => $id]);
    echo "Success";
}
?>
