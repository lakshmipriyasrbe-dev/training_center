<?php require_once 'common_file.php'; 
if ($user_role != 'admin') { header("Location: dashboard.php"); exit(); }
$action = $_POST['action'] ?? '';

   $student_id = ""; $student_name = ""; $father_spouse_name = ""; $address = ""; $mobile_number = ""; $parent_contact_no = ""; $course_id = ""; $duration = ""; $from_time = ""; $to_time = ""; $staff_id = ""; $fees_type = ""; $fees_amount = ""; $paid_amount = "0"; $balance_amount = ""; $dob = ""; $doj = ""; $blood_group = ""; $candidate_photo = "";

    if(isset($_REQUEST['view_enrollment_id'])) {

        $view_enrollment_id = $_REQUEST['view_enrollment_id'];

        $enrollment_list = $bf->getTableRecords(
            $GLOBALS['enrollment_table'],
            'enrollment_id',
            $view_enrollment_id
        );

        if(!empty($enrollment_list)) {
            foreach($enrollment_list as $data) {

                if(!empty($data['student_id'])) {
                    $student_id = $bf->encode_decode('decrypt', $data['student_id']);
                }

                if(!empty($data['student_name'])) {
                    $student_name = $data['student_name'];
                }

                if(!empty($data['father_spouse_name'])) {
                    $father_spouse_name = $data['father_spouse_name'];
                }

                if(!empty($data['address'])) {
                    $address = $data['address'];
                }

                if(!empty($data['mobile_number'])) {
                    $mobile_number = $data['mobile_number'];
                }

                if(!empty($data['parent_contact_no'])) {
                    $parent_contact_no = $data['parent_contact_no'];
                }

                if(!empty($data['course_id'])) {
                    $course_id = $data['course_id'];
                }

                if(!empty($data['duration'])) {
                    $duration = $data['duration'];
                }

                if(!empty($data['from_time'])) {
                    $from_time = $data['from_time'];
                }

                if(!empty($data['to_time'])) {
                    $to_time = $data['to_time'];
                }

                if(!empty($data['staff_id'])) {
                    $staff_id = $data['staff_id'];
                }

                if(!empty($data['fees_type'])) {
                    $fees_type = $data['fees_type'];
                }

                if(!empty($data['fees_amount'])) {
                    $fees_amount = $data['fees_amount'];
                }

                if(!empty($data['paid_amount'])) {
                    $paid_amount = $data['paid_amount'];
                }

                if(!empty($data['balance_amount'])) {
                    $balance_amount = $data['balance_amount'];
                }

                if(!empty($data['dob'])) {
                    $dob = $data['dob'];
                }

                if(!empty($data['doj'])) {
                    $doj = $data['doj'];
                }

                if(!empty($data['blood_group'])) {
                    $blood_group = $data['blood_group'];
                }

                if(!empty($data['candidate_photo'])) {
                    $candidate_photo = $data['candidate_photo'];
                }
            }
        }
    
        if (empty($view_enrollment_id)) {
            // Calculate next student_id for display
            $current_month = (int)date('m');
            $current_year = (int)date('Y');
            if ($current_month >= 4) {
                $fy_start_year = $current_year;
                $fy_end_year = $current_year + 1;
            } else {
                $fy_start_year = $current_year - 1;
                $fy_end_year = $current_year;
            }
            $fy_suffix = substr($fy_start_year, -2) . '-' . substr($fy_end_year, -2);
            
            $fy_start_date = $fy_start_year . "-04-01 00:00:00";
            $fy_end_date = $fy_end_year . "-03-31 23:59:59";
            
            $stmt = $bf->con->prepare("SELECT COUNT(id) FROM " . $GLOBALS['enrollment_table'] . " WHERE created_date_time >= :start AND created_date_time <= :end");
            $stmt->execute([':start' => $fy_start_date, ':end' => $fy_end_date]);
            $count = $stmt->fetchColumn();
            $next_number = $count + 1;
            $student_id = "EN" . str_pad($next_number, 3, "0", STR_PAD_LEFT) . "/" . $fy_suffix;
        }
        ?>

    <!-- <div class="main-content"> -->

        <div class="header">
            <h2>
                <?php echo empty($view_enrollment_id) ? "New Enrollment" : "Update Enrollment"; ?>
            </h2>
        </div>

        <div class="module-section form-section">

            <form
                name="enrollment_form"
                id="enrollment_form"
                method="POST"
                enctype="multipart/form-data"
                onsubmit="event.preventDefault(); formSubmit('enrollment_form', 'enrollment_action.php', 'enrollment.php', 'enrollment');"
            >

                <input type="hidden" name="view_enrollment_id" value="<?php echo $view_enrollment_id; ?>">

                <div class="form-row">

                    <div class="form-group col-4">
                        <label>Student ID</label>

                        <input
                            type="text"
                            name="student_id"
                            class="form-input"
                            value="<?php echo $student_id; ?>"
                            readonly
                        >
                    </div>

                    <div class="form-group col-4">
                        <label>Student Name *</label>

                        <input
                            type="text"
                            name="student_name"
                            class="form-input"
                            value="<?php echo $student_name; ?>"
                            onkeypress="return allowLettersOnly(event)"
                            
                        >

                        <span id="error-student_name" class="error-msg"></span>
                    </div>

                    <div class="form-group col-4">
                        <label>Father / Spouse Name *</label>

                        <input
                            type="text"
                            name="father_spouse_name"
                            class="form-input"
                            value="<?php echo $father_spouse_name; ?>"
                            onkeypress="return allowLettersOnly(event)"
                            
                        >

                        <span id="error-father_spouse_name" class="error-msg"></span>
                    </div>

                    <div class="form-group col-4">
                        <label>Address *</label>

                        <textarea
                            name="address"
                            class="form-input"
                            
                        ><?php echo $address; ?></textarea>

                        <span id="error-address" class="error-msg"></span>
                    </div>

                    <div class="form-group col-4">
                        <label>Student Contact No *</label>

                        <input
                            type="text"
                            maxlength="10"
                            name="mobile_number"
                            class="form-input"
                            value="<?php echo $mobile_number; ?>"
                            onkeypress="return allowNumbersOnly(event)"
                            
                        >

                        <span id="error-mobile_number" class="error-msg"></span>
                    </div>

                    <div class="form-group col-4">
                        <label>Parent Contact No</label>

                        <input
                            type="text"
                            maxlength="10"
                            name="parent_contact_no"
                            class="form-input"
                            value="<?php echo $parent_contact_no; ?>"
                            onkeypress="return allowNumbersOnly(event)"
                        >

                        <span id="error-parent_contact_no" class="error-msg"></span>
                    </div>
                    <div class="form-group col-4">
                        <label>Course Name *</label>

                        <select name="course_id"
                            class="form-input" onchange="getCourseDetails(this.value)">
                            >

                            <option value="">Select</option>

                            <?php
                                $course_list = $bf->getTableRecords($GLOBALS['course_table']);
                                if(!empty($course_list)) {
                                    foreach($course_list as $course) {
                            ?>

                            <option
                                value="<?php echo $course['course_id']; ?>"
                                <?php if($course_id == $course['course_id']) { ?>
                                    selected
                                <?php } ?>
                            >
                                <?php echo $course['course_name']; ?>
                            </option>

                            <?php
                                    }
                                }
                            ?>

                        </select>

                        <span id="error-course_id" class="error-msg"></span>
                    </div>               
                    <div class="form-group col-4">
                        <label>Duration (Months)</label>

                        <input
                            type="text"
                            name="duration"
                            class="form-input" id="course_duration"
                            value="<?php echo $duration; ?>"
                            onkeypress="return allowNumbersOnly(event)"
                        >

                        <span id="error-duration" class="error-msg"></span>
                    </div>

                    <div class="form-group col-4">
                        <label>Time From</label>

                        <input
                            type="time"
                            name="from_time"
                            class="form-input"
                            value="<?php echo $from_time; ?>"
                        >

                        <span id="error-from_time" class="error-msg"></span>
                    </div>

                    <div class="form-group col-4">
                        <label>Time To</label>

                        <input
                            type="time"
                            name="to_time"
                            class="form-input"
                            value="<?php echo $to_time; ?>"
                        >

                        <span id="error-to_time" class="error-msg"></span>
                    </div>

                    <div class="form-group col-4">
                        <label>Assigned Staff</label>

                        <select
                            name="staff_id"
                            class="form-input"
                        >

                            <option value="">Select</option>

                            <?php
                                $staff_list = $bf->getTableRecords($GLOBALS['staff_table']);
                                if(!empty($staff_list)) {
                                    foreach($staff_list as $staff) {
                            ?>

                            <option
                                value="<?php echo $staff['staff_id']; ?>"
                                <?php if($staff_id == $staff['staff_id']) { ?>
                                    selected
                                <?php } ?>
                            >
                                <?php echo $staff['staff_name']; ?>
                            </option>

                            <?php
                                    }
                                }
                            ?>

                        </select>

                        <span id="error-staff_id" class="error-msg"></span>
                    </div>

                    <div class="form-group col-12">

                        <label>Fees Term *</label>

                        <div class="radio-group">

                            <label>
                                <input
                                    type="radio"
                                    name="fees_type"
                                    value="Full Payment"
                                    <?php if($fees_type == "Full Payment") { ?>
                                        checked
                                    <?php } ?>
                                    
                                >
                                Full Payment
                            </label>

                            <label>
                                <input
                                    type="radio"
                                    name="fees_type"
                                    value="Installment"
                                    <?php if($fees_type == "Installment") { ?>
                                        checked
                                    <?php } ?>
                                >
                                Installment
                            </label>

                        </div>

                        <span id="error-fees_type" class="error-msg"></span>

                    </div>

                    <div class="form-group col-4">
                        <label>Fees Amount *</label>

                        <input
                            type="text"
                            name="fees_amount"
                            class="form-input" id="course_fee"
                            value="<?php echo $fees_amount; ?>"
                            onkeypress="return allowNumbersOnly(event)"
                            
                        >

                        <span id="error-fees_amount" class="error-msg"></span>
                    </div>

                    <div class="form-group col-4">
                        <label>Paid Amount</label>

                        <input
                            type="text"
                            name="paid_amount"
                            class="form-input"
                            value="<?php echo $paid_amount; ?>"
                            onkeypress="return allowNumbersOnly(event)"
                        >

                        <span id="error-paid_amount" class="error-msg"></span>
                    </div>

                    <div class="form-group col-4">
                        <label>Balance Amount</label>

                        <input
                            type="text"
                            name="balance_amount"
                            class="form-input"
                            value="<?php echo $balance_amount; ?>"
                            readonly
                        >
                    </div>

                    <div class="form-group col-4">
                        <label>Date of Birth</label>

                        <input
                            type="date"
                            name="dob"
                            class="form-input"
                            value="<?php echo $dob; ?>"
                        >

                        <span id="error-dob" class="error-msg"></span>
                    </div>

                    <div class="form-group col-4">
                        <label>Date of Joining</label>

                        <input
                            type="date"
                            name="doj"
                            class="form-input"
                            value="<?php echo $doj; ?>"
                        >

                        <span id="error-doj" class="error-msg"></span>
                    </div>

                    <div class="form-group col-4">
                        <label>Blood Group</label>

                        <select
                            name="blood_group"
                            class="form-input"
                        >

                            <option value="">Select</option>

                            <?php
                                $blood_groups = array(
                                    'A+','A-','B+','B-',
                                    'AB+','AB-','O+','O-'
                                );

                                foreach($blood_groups as $group) {
                            ?>

                            <option
                                value="<?php echo $group; ?>"
                                <?php if($blood_group == $group) { ?>
                                    selected
                                <?php } ?>
                            >
                                <?php echo $group; ?>
                            </option>

                            <?php } ?>

                        </select>

                        <span id="error-blood_group" class="error-msg"></span>
                    </div>

                    <div class="form-group col-12">

                        <label>Student Photo</label>

                        <input
                            type="file"
                            name="candidate_photo"
                            class="form-input"
                            accept="image/*"
                        >

                        <?php if(!empty($candidate_photo)) { ?>

                            <br><br>

                            <img
                                src="uploads/<?php echo $candidate_photo; ?>"
                                width="80"
                            >

                        <?php } ?>

                        <span id="error-candidate_photo" class="error-msg"></span>

                    </div>

                </div>

                <div class="form-buttons">

                    <button type="submit" class="btn-add">

                        <?php echo empty($view_enrollment_id) ? "Add Enrollment" : "Update Enrollment"; ?>

                    </button>

                    <?php if(!empty($view_enrollment_id)) { ?>

                        <a
                            href="enrollment.php"
                            class="btn-add"
                                style="background: #ef4444; font-size: 0.75rem;"
                        >
                            Cancel
                        </a>

                    <?php } ?>

                </div>
                <script>
                   function calculateBalance() {

                        var feesAmount = parseFloat(document.querySelector('input[name="fees_amount"]').value) || 0;

                        var paidAmount = parseFloat(document.querySelector('input[name="paid_amount"]').value) || 0;

                        var balance = feesAmount - paidAmount;

                        document.querySelector('input[name="balance_amount"]').value =
                            balance.toFixed(2);
                    }


                    /* Fees Type Change */

                    document.querySelectorAll('input[name="fees_type"]').forEach(function(radio) {

                        radio.addEventListener('change', function() {

                            var feesAmount = parseFloat(document.querySelector('input[name="fees_amount"]').value) || 0;

                            if(this.value === 'Full Payment') {

                                document.querySelector('input[name="paid_amount"]').value =
                                    feesAmount.toFixed(2);

                                document.querySelector('input[name="paid_amount"]')
                                    .setAttribute('readonly', true);

                            } else {

                                document.querySelector('input[name="paid_amount"]').value = '';

                                document.querySelector('input[name="paid_amount"]')
                                    .removeAttribute('readonly');
                            }

                            calculateBalance();
                        });

                    });


                    /* Fees Amount Change */

                    document.querySelector('input[name="fees_amount"]').addEventListener('input', function() {

                        var selectedFeesType =
                            document.querySelector('input[name="fees_type"]:checked');

                        if(selectedFeesType &&
                        selectedFeesType.value === 'Full Payment') {

                            this.value = this.value || 0;

                            document.querySelector('input[name="paid_amount"]').value =
                                parseFloat(this.value || 0).toFixed(2);
                        }

                        calculateBalance();
                    });


                    /* Paid Amount Change */

                    document.querySelector('input[name="paid_amount"]').addEventListener('input', function() {

                        calculateBalance();
                    });
                </script>

            </form>

        </div>

    <!-- </div> -->

<?php }

if (isset($_POST['student_name'])) {
    $student_name = $bf->sanitize($_POST['student_name'] ?? '');
    $father_spouse_name = $bf->sanitize($_POST['father_spouse_name'] ?? '');
    $address = $bf->sanitize($_POST['address'] ?? '');
    $mobile_number = $bf->sanitize($_POST['mobile_number'] ?? '');
    $parent_contact_no = $bf->sanitize($_POST['parent_contact_no'] ?? '');
    $course_id = $bf->sanitize($_POST['course_id'] ?? '');
    $duration = $bf->sanitize($_POST['duration'] ?? '');
    $from_time = $bf->sanitize($_POST['from_time'] ?? '');
    $to_time = $bf->sanitize($_POST['to_time'] ?? '');
    $staff_id = $bf->sanitize($_POST['staff_id'] ?? '');
    $fees_type = $bf->sanitize($_POST['fees_type'] ?? '');
    $fees_amount = $bf->sanitize($_POST['fees_amount'] ?? '');
    $paid_amount = $bf->sanitize($_POST['paid_amount'] ?? '0');
    $balance_amount = $bf->sanitize($_POST['balance_amount'] ?? '0');
    $dob = $bf->sanitize($_POST['dob'] ?? '');
    $doj = $bf->sanitize($_POST['doj'] ?? '');
    $blood_group = $bf->sanitize($_POST['blood_group'] ?? '');
    $view_enrollment_id = $bf->sanitize($_POST['view_enrollment_id'] ?? '');
    
    $errors = [];
    $res = $valid->valid_name($student_name, 'Student Name');
    if ($res) $errors['student_name'] = $res;

    $res = $valid->valid_name($father_spouse_name, 'Father / Spouse Name');
    if ($res) $errors['father_spouse_name'] = $res;

    $res = $valid->common_validation($address, 'Address', 'text');
    if ($res) $errors['address'] = $res;

    $res = $valid->valid_mobile($mobile_number, 'Student Contact No');
    if ($res) $errors['mobile_number'] = $res;

    $res = $valid->common_validation($course_id, 'Course Name', 'select');
    if ($res) $errors['course_id'] = $res;

    $res = $valid->common_validation($fees_type, 'Fees Term', 'radio');
    if ($res) $errors['fees_type'] = $res;

    $res = $valid->common_validation($fees_amount, 'Fees Amount', 'text');
    if ($res) $errors['fees_amount'] = $res;

    // file upload for photo
    $candidate_photo = '';
    if (isset($_FILES['candidate_photo']['name']) && !empty($_FILES['candidate_photo']['name'])) {
        $file_name = $_FILES['candidate_photo']['name'];
        $file_tmp = $_FILES['candidate_photo']['tmp_name'];
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_name = time() . '.' . $ext;
        
        // ensure dir exists
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }
        
        if (move_uploaded_file($file_tmp, "uploads/" . $new_name)) {
            $candidate_photo = $new_name;
        }
    }

    if(empty($errors)) {
        $data = [
            'student_name' => $student_name,
            'father_spouse_name' => $father_spouse_name,
            'address' => $address,
            'mobile_number' => $mobile_number,
            'parent_contact_no' => $parent_contact_no,
            'course_id' => $course_id,
            'duration' => $duration,
            'from_time' => $from_time,
            'to_time' => $to_time,
            'staff_id' => $staff_id,
            'fees_type' => $fees_type,
            'fees_amount' => $fees_amount,
            'paid_amount' => $paid_amount,
            'balance_amount' => $balance_amount,
            'dob' => $dob,
            'doj' => $doj,
            'blood_group' => $blood_group,
            'updated_date_time' => date('Y-m-d H:i:s')
        ];
        
        if (!empty($candidate_photo)) {
            $data['candidate_photo'] = $candidate_photo;
        }

        if(empty($view_enrollment_id)) {
            $data['created_date_time'] = date('Y-m-d H:i:s');
            $bf->InsertSQL(
                $GLOBALS['enrollment_table'],
                $data,
                'enrollment_id',
                'student_id',
                'ADD ENROLLMENT'
            );
            echo json_encode(['status' => 'success', 'message' => 'Enrollment added successfully']);
        } else {
            $bf->UpdateSQL(
                $GLOBALS['enrollment_table'],
                $data,
                "enrollment_id = :id",
                [':id' => $view_enrollment_id]
            );
            echo json_encode(['status' => 'success', 'message' => 'Enrollment updated successfully']);
        }
        exit;
    } else {
        echo json_encode(['status' => 'error', 'errors' => $errors]);
        exit;
    }
} 

if (isset($action) &&  ($action == 'list')) {
    $enrollments = $bf->getTableRecords($GLOBALS['enrollment_table'], 'deleted', 0);
    if (empty($enrollments)) {
        echo "<p>No enrollments found.</p>";
    } else {
        ?>

        <table>

            <tr>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Mobile Number</th>
                <th>Course Name</th>
                <th>Action</th>
            </tr>

            <?php foreach ($enrollments as $u) { 
                $course_name = '';
                if(!empty($u['course_id'])) {
                    $course_data = $bf->getTableRecords($GLOBALS['course_table'], 'course_id', $u['course_id']);
                    if(!empty($course_data)) {
                        $course_name = $course_data[0]['course_name'];
                    }
                }
            ?>

                <tr>

                    <td>
                        <span style="color: var(--primary);">
                            <?php echo $bf->encode_decode('decrypt', $u['student_id']); ?>
                        </span>
                    </td>

                    <td>
                        <?php echo $u['student_name']; ?>
                    </td>

                    <td>
                        <?php echo $u['mobile_number']; ?>
                    </td>

                    <td>
                        <?php echo $course_name; ?>
                    </td>

                    <td>
                        <button class="btn-add" onclick="Javacript:ShowPage('enrollment', '<?php echo $u['enrollment_id']; ?>')">Edit</button>
                        <button
                            class="btn-add"
                            style="background: #ef4444; font-size: 0.75rem;"
                            onclick="deleteRecord('enrollment', '<?php echo $u['id']; ?>')"
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

if (isset($action) &&  ($action == 'delete')) {
    $id = $bf->sanitize($_POST['id'] ?? '');
    $data = ['deleted' => 1, 'updated_date_time' => $GLOBALS['create_date_time_label']];
    $bf->UpdateSQL($GLOBALS['enrollment_table'], $data, "id = :id", [':id' => $id]);
    echo "Success";
}
?>
