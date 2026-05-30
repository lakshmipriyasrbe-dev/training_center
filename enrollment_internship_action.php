<?php require_once 'common_file.php'; 
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'search_students') {
    $search = $bf->sanitize($_POST['search'] ?? '');
    
    // Search both training and internship enrollments for existing students
    $query1 = "SELECT * FROM " . $GLOBALS['enrollment_table'] . " WHERE student_name LIKE :search AND deleted = 0 AND company_id = :comp_id ORDER BY id DESC";
    $query2 = "SELECT * FROM " . $GLOBALS['enrollment_internship_table'] . " WHERE student_name LIKE :search AND deleted = 0 AND company_id = :comp_id ORDER BY id DESC";
    
    $params = [
        ':search' => '%' . $search . '%',
        ':comp_id' => $_SESSION['company_id']
    ];
    
    $records1 = $bf->getQueryRecords($query1, $params);
    $records2 = $bf->getQueryRecords($query2, $params);
    $records = array_merge($records1, $records2);
    
    $results = [];
    $seen = [];
    foreach ($records as $r) {
        $dec_id = $bf->encode_decode('decrypt', $r['student_id']);
        if (!in_array($dec_id, $seen)) {
            $seen[] = $dec_id;
            $results[] = [
                'student_id' => $dec_id,
                'student_name' => $r['student_name'],
                'father_spouse_name' => $r['father_spouse_name'],
                'address' => $r['address'],
                'mobile_number' => $r['mobile_number'],
                'parent_contact_no' => $r['parent_contact_no'] ?? '',
                'dob' => $r['dob'] ?? '',
                'blood_group' => $r['blood_group'] ?? ''
            ];
        }
    }
    header('Content-Type: application/json');
    echo json_encode($results);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_name'])) {
    $student_id = $bf->sanitize($_POST['student_id'] ?? '');
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
    $lead_source = $bf->sanitize($_POST['lead_source'] ?? '');
    $referred_staff_id = $bf->sanitize($_POST['referred_staff_id'] ?? '');
    $view_enrollment_internship_id = $bf->sanitize($_POST['view_enrollment_internship_id'] ?? '');
    
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

     if(!empty($lead_source)) {
        $res = $valid->common_validation($lead_source, 'Lead Source', 'select');
        if ($res) $errors['lead_source'] = $res;

        if($lead_source === 'reference') {
            $res = $valid->common_validation($referred_staff_id, 'Referred Staff', 'select');
            if ($res) $errors['referred_staff_id'] = $res;
        }
    }

    $candidate_photo = '';
    if (isset($_FILES['candidate_photo']['name']) && !empty($_FILES['candidate_photo']['name'])) {
        $file_name = $_FILES['candidate_photo']['name'];
        $file_tmp = $_FILES['candidate_photo']['tmp_name'];
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_name = time() . '.' . $ext;
        
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }
        
        if (move_uploaded_file($file_tmp, "uploads/" . $new_name)) {
            $candidate_photo = $new_name;
        }
    }

    if (empty($errors)) {
        $plain_student_id = $bf->sanitize($_POST['student_id'] ?? '');
        if (!empty($student_id)) {
            $student_id = $bf->encode_decode('encrypt', $student_id);
        }

        $data = [
            'student_id' => $student_id,
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
            'lead_source' => $lead_source,
            'referred_staff_id' => $referred_staff_id,              
            'updated_date_time' => date('Y-m-d H:i:s')
        ];
        
        if (!empty($candidate_photo)) {
            $data['candidate_photo'] = $candidate_photo;
        }

        header('Content-Type: application/json');
        if (empty($view_enrollment_internship_id)) {
            $data['created_date_time'] = date('Y-m-d H:i:s');
            
            // Custom enrollment number: ENI003/26-27
            $current_year_two = date('y');
            $next_year_two = str_pad((intval($current_year_two) + 1) % 100, 2, '0', STR_PAD_LEFT);
            $year_suffix = "/" . $current_year_two . "-" . $next_year_two;
            
            $max_query = $bf->getQueryRecords("SELECT MAX(id) as max_id FROM " . $GLOBALS['enrollment_internship_table']);
            $next_num = (!empty($max_query[0]['max_id']) ? intval($max_query[0]['max_id']) : 0) + 1;
            $seq_str = str_pad($next_num, 3, '0', STR_PAD_LEFT);
            $data['enrollment_number'] = "ENI" . $seq_str . $year_suffix;
            
            // Set student username and password directly in the enrollment internship table
            if (!empty($plain_student_id)) {
                $data['username'] = $plain_student_id;
                
                $existing_pwd = '';
                $check_enr = $bf->getQueryRecords("SELECT password FROM " . $GLOBALS['enrollment_table'] . " WHERE username = :username AND deleted = 0 LIMIT 1", [':username' => $plain_student_id]);
                if (!empty($check_enr)) {
                    $existing_pwd = $check_enr[0]['password'];
                } else {
                    $check_int = $bf->getQueryRecords("SELECT password FROM " . $GLOBALS['enrollment_internship_table'] . " WHERE username = :username AND deleted = 0 LIMIT 1", [':username' => $plain_student_id]);
                    if (!empty($check_int)) {
                        $existing_pwd = $check_int[0]['password'];
                    }
                }
                
                if (!empty($existing_pwd)) {
                    $data['password'] = $existing_pwd;
                } else {
                    $dob_plain = !empty($dob) ? date('dmY', strtotime($dob)) : $plain_student_id;
                    $data['password'] = $bf->encode_decode('encrypt', $dob_plain);
                }
            }

            $new_id = $bf->InsertSQL(
                $GLOBALS['enrollment_internship_table'], 
                $data, 
                'enrollment_internship_id', 
                '', 
                'ADD INTERNSHIP ENROLLMENT'
            );

            $from_enquiry = $bf->sanitize($_POST['from_enquiry'] ?? '');
            if (!empty($from_enquiry)) {
                $enrollment_record = $bf->getTableRecords($GLOBALS['enrollment_internship_table'], 'id', $new_id);
                $generated_enrollment_id = !empty($enrollment_record) ? $enrollment_record[0]['enrollment_internship_id'] : '';
                $bf->UpdateSQL($GLOBALS['course_enquiry_table'], [
                    'converted_type' => 'internship',
                    'converted_id' => $generated_enrollment_id
                ], "enquiry_id = :enq_id", [':enq_id' => $from_enquiry]);
            }
            
            if (floatval($paid_amount) > 0) {
                // Fetch the generated enrollment_internship_id from the inserted record
                $enrollment_record = $bf->getTableRecords($GLOBALS['enrollment_internship_table'], 'id', $new_id);
                $generated_enrollment_id = !empty($enrollment_record) ? $enrollment_record[0]['enrollment_internship_id'] : '';
                
                echo json_encode([
                    'status' => 'success_with_payment', 
                    'enrollment_id' => $generated_enrollment_id, 
                    'paid_amount' => $paid_amount, 
                    'course_type' => 'internship',
                    'student_id' => $student_id,
                    'message' => 'Internship Enrollment added. Please complete the receipt.'
                ]);
            } else {
                echo json_encode(['status' => 'success', 'message' => 'Internship Enrollment added successfully']);
            }
        } else {
            if (!empty($plain_student_id)) {
                $data['username'] = $plain_student_id;
            }

            $bf->UpdateSQL(
                $GLOBALS['enrollment_internship_table'], 
                $data, 
                "enrollment_internship_id = :id", 
                [':id' => $view_enrollment_internship_id]
            );
            
            echo json_encode(['status' => 'success', 'message' => 'Internship Enrollment updated successfully']);
        }
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'errors' => $errors]);
        exit;
    }
}

   $student_id = ""; $student_name = ""; $father_spouse_name = ""; $address = ""; $mobile_number = ""; $parent_contact_no = ""; $course_id = ""; $duration = ""; $from_time = ""; $to_time = ""; $staff_id = ""; $fees_type = ""; $fees_amount = ""; $paid_amount = "0"; $balance_amount = ""; $dob = ""; $doj = ""; $blood_group = ""; $candidate_photo = ""; $lead_source = ""; $referred_staff_id = "";

   $from_enquiry_id = $_SESSION['from_enquiry'] ?? '';
   if (!empty($from_enquiry_id)) { unset($_SESSION['from_enquiry']);
       $enquiry_records = $bf->getQueryRecords("SELECT * FROM " . $GLOBALS['course_enquiry_table'] . " WHERE enquiry_id = :enq_id AND deleted = 0 LIMIT 1", [':enq_id' => $from_enquiry_id]);
       if (!empty($enquiry_records)) {
           $enquiry = $enquiry_records[0];
           $student_name = $enquiry['name'];
           $mobile_number = $enquiry['mobile_number'];
           $address = $enquiry['address'];
           $course_id = $enquiry['course_id'];
           if (!empty($course_id)) {
               $course_info = $bf->getTableRecords($GLOBALS['course_table'], 'course_id', $course_id);
               if (!empty($course_info)) {
                   $fees_amount = $course_info[0]['course_fee'];
                   $duration = $course_info[0]['course_duration'];
               }
           }
       }
   }

    if(isset($_REQUEST['view_enrollment_internship_id'])) {

        $view_enrollment_internship_id = $_REQUEST['view_enrollment_internship_id'];

        $enrollment_list = $bf->getTableRecords(
            $GLOBALS['enrollment_internship_table'],
            'enrollment_internship_id',
            $view_enrollment_internship_id
        );

        if(!empty($enrollment_list)) {
            foreach($enrollment_list as $data) {

                if(!empty($data['student_id'])) {
                    $student_id = $bf->encode_decode('decrypt', $data['student_id']);
                }

                if(!empty($data['student_name'])) {
                    $student_name = $data['student_name'];
                }

                if(!empty($data['lead_source'])) {
                    $lead_source = $data['lead_source'];
                }

                if(!empty($data['referred_staff_id'])) {
                    $referred_staff_id = $data['referred_staff_id'];
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
    
        if (empty($view_enrollment_internship_id)) {
            $month_num = intval(date('n'));
            $month_char = chr(64 + $month_num);
            $prefix = "WGI" . $month_char . date('y');
            
            $records = $bf->getQueryRecords("SELECT student_id FROM " . $GLOBALS['enrollment_internship_table'] . " WHERE deleted = 0");
            $max_seq = 0;
            foreach ($records as $r) {
                if (!empty($r['student_id'])) {
                    $dec_id = $bf->encode_decode('decrypt', $r['student_id']);
                    if (strpos($dec_id, $prefix) === 0) {
                        $seq_part = intval(substr($dec_id, strlen($prefix)));
                        if ($seq_part > $max_seq) {
                            $max_seq = $seq_part;
                        }
                    }
                }
            }
            $next_seq = $max_seq + 1;
            $student_id = $prefix . str_pad($next_seq, 3, '0', STR_PAD_LEFT);
        }
        ?>

    <!-- <div class="main-content"> -->

        <div class="header">
            <h2>
                <?php echo empty($view_enrollment_internship_id) ? "New Internship Enrollment" : "Update Internship Enrollment"; ?>
            </h2>
        </div>

        <div class="module-section form-section">

            <form
                name="enrollment_form"
                id="enrollment_form"
                method="POST"
                enctype="multipart/form-data"
                onsubmit="event.preventDefault(); formSubmit('enrollment_form', 'enrollment_internship_action.php', 'enrollment_internship.php', 'enrollment_internship');"
            >

                <input type="hidden" name="view_enrollment_internship_id" value="<?php echo $view_enrollment_internship_id; ?>">
                <input type="hidden" name="from_enquiry" value="<?php echo htmlspecialchars($from_enquiry_id); ?>">

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

                    <div class="form-group col-4" style="position: relative;">
                        <style>
                            .suggestions-dropdown {
                                position: absolute;
                                top: 100%;
                                left: 0;
                                right: 0;
                                background: #ffffff;
                                border: 1px solid var(--border);
                                border-radius: 0.5rem;
                                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                                z-index: 1000;
                                max-height: 250px;
                                overflow-y: auto;
                                margin-top: 0.25rem;
                                padding: 0.5rem 0;
                            }
                            .suggestion-item {
                                padding: 0.75rem 1rem;
                                cursor: pointer;
                                font-size: 0.85rem;
                                color: var(--text);
                                transition: all 0.2s;
                                border-bottom: 1px solid #f1f5f9;
                            }
                            .suggestion-item:last-child {
                                border-bottom: none;
                            }
                            .suggestion-item:hover {
                                background: #f8fafc;
                                color: var(--primary);
                            }
                            .suggestion-item .student-details {
                                font-size: 0.75rem;
                                color: var(--text-muted);
                                margin-top: 0.25rem;
                                display: flex;
                                gap: 0.75rem;
                            }
                        </style>
                        <label>Student Name *</label>

                        <input
                            type="text"
                            name="student_name"
                            id="student_name_input"
                            class="form-input"
                            value="<?php echo $student_name; ?>"
                            onkeypress="return allowLettersOnly(event)"
                            autocomplete="off"
                        >
                        <div id="student_suggestions" class="suggestions-dropdown" style="display: none;"></div>

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

                    <div class="form-group col-4">
                        <label>How did you hear about us? </label>

                        <select name="lead_source" id="lead_source" class="form-input">
                            <option value="">Select</option>
                            <option value="facebook" <?php if(!empty($lead_source) && $lead_source == "facebook") { ?> selected <?php } ?>>Facebook</option>
                            <option value="instagram" <?php if(!empty($lead_source) && $lead_source == "instagram") { ?> selected <?php } ?>>Instagram</option>
                            <option value="website" <?php if(!empty($lead_source) && $lead_source == "website") { ?> selected <?php } ?>>Website</option>
                            <option value="reference" <?php if(!empty($lead_source) && $lead_source == "reference") { ?> selected <?php } ?>>Reference</option>
                        </select>

                        <span id="error-lead_source" class="error-msg"></span>
                    </div>

                    <div class="form-group col-4 <?php if(empty($lead_source) || $lead_source !== 'reference') { ?> d-none <?php } ?>" id="referred_staff_group">
                        <label>Referred Staff</label>

                        <select
                            name="referred_staff_id"
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
                                <?php if(!empty($referred_staff_id) && $referred_staff_id == $staff['staff_id']) { ?>
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

                        <span id="error-referred_staff_id" class="error-msg"></span>
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

                        <?php echo empty($view_enrollment_internship_id) ? "Add Internship Enrollment" : "Update Internship Enrollment"; ?>

                    </button>

                    <?php if(!empty($view_enrollment_internship_id)) { ?>

                        <a
                            href="enrollment_internship.php"
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

                    if(document.getElementById('lead_source')) {

                        document.getElementById('lead_source').addEventListener('change', function() {

                            var referredStaffGroup = document.getElementById('referred_staff_group');

                            if(this.value === 'reference') {
                                referredStaffGroup.classList.remove('d-none');
                            } else {
                                referredStaffGroup.classList.add('d-none');
                                document.querySelector('select[name="referred_staff_id"]').value = '';
                            }
                        });
                    }

                    // Autocomplete / suggestions for student name
                    (function() {
                        const nameInput = document.getElementById('student_name_input');
                        const suggestionsDiv = document.getElementById('student_suggestions');
                        const original_student_id = document.querySelector('input[name="student_id"]').value;
                        
                        if (nameInput && suggestionsDiv) {
                            nameInput.addEventListener('input', function() {
                                const query = this.value.trim();
                                if (query.length < 2) {
                                    suggestionsDiv.style.display = 'none';
                                    suggestionsDiv.innerHTML = '';
                                    
                                    if (query === '') {
                                        // Reset fields
                                        document.querySelector('input[name="student_id"]').value = original_student_id;
                                        document.querySelector('input[name="father_spouse_name"]').value = '';
                                        document.querySelector('textarea[name="address"]').value = '';
                                        document.querySelector('input[name="mobile_number"]').value = '';
                                        document.querySelector('input[name="parent_contact_no"]').value = '';
                                        document.querySelector('input[name="dob"]').value = '';
                                        document.querySelector('select[name="blood_group"]').value = '';
                                    }
                                    return;
                                }
                                
                                $.ajax({
                                    url: 'enrollment_internship_action.php',
                                    type: 'POST',
                                    data: {
                                        action: 'search_students',
                                        search: query
                                    },
                                    dataType: 'json',
                                    success: function(data) {
                                        if (data && data.length > 0) {
                                            suggestionsDiv.innerHTML = '';
                                            suggestionsDiv.style.display = 'block';
                                            
                                            data.forEach(function(student) {
                                                const item = document.createElement('div');
                                                item.className = 'suggestion-item';
                                                item.innerHTML = `
                                                    <div style="font-weight: 600;">${student.student_name}</div>
                                                    <div class="student-details">
                                                        <span>ID: ${student.student_id}</span>
                                                        <span>Phone: ${student.mobile_number}</span>
                                                    </div>
                                                `;
                                                
                                                item.addEventListener('click', function() {
                                                    document.querySelector('input[name="student_id"]').value = student.student_id;
                                                    nameInput.value = student.student_name;
                                                    document.querySelector('input[name="father_spouse_name"]').value = student.father_spouse_name;
                                                    document.querySelector('textarea[name="address"]').value = student.address;
                                                    document.querySelector('input[name="mobile_number"]').value = student.mobile_number;
                                                    document.querySelector('input[name="parent_contact_no"]').value = student.parent_contact_no || '';
                                                    document.querySelector('input[name="dob"]').value = student.dob || '';
                                                    document.querySelector('select[name="blood_group"]').value = student.blood_group || '';
                                                    document.querySelector('input[name="paid_amount"]').value = '0';
                                                    calculateBalance();
                                                    
                                                    suggestionsDiv.style.display = 'none';
                                                    suggestionsDiv.innerHTML = '';
                                                });
                                                
                                                suggestionsDiv.appendChild(item);
                                            });
                                        } else {
                                            suggestionsDiv.style.display = 'none';
                                            suggestionsDiv.innerHTML = '';
                                        }
                                    }
                                });
                            });
                            
                            document.addEventListener('click', function(e) {
                                if (e.target !== nameInput && e.target !== suggestionsDiv && !suggestionsDiv.contains(e.target)) {
                                    suggestionsDiv.style.display = 'none';
                                }
                            });
                        }
                    })();
                </script>

            </form>

        </div>

    <!-- </div> -->

<?php }

if ($action == 'list') {
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
    $search = isset($_POST['search']) ? $_POST['search'] : '';
    $start = ($page - 1) * $limit;

    $result = $bf->getEnrollmentList($GLOBALS['enrollment_internship_table'], $start, $limit, $search);
    $enrollments = $result['data'];
    $total_records = $result['total_records'];
    $total_pages = ceil($total_records / $limit);

    if (empty($enrollments)) { ?>
        <div class="table-responsive">
            <table><tr><td style="text-align:center">No internship enrollments found.</td></tr></table>
        </div>
    <?php } else {
        ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Sno</th>
                        <th>Enrollment No</th>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Mobile Number</th>
                        <th>Course Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $sno = $start + 1;
                foreach ($enrollments as $u) { 
                    $course_name = '';
                    if(!empty($u['course_id'])) {
                        $course_data = $bf->getTableRecords($GLOBALS['course_table'], 'course_id', $u['course_id']);
                        if(!empty($course_data)) {
                            $course_name = $course_data[0]['course_name'];
                        }
                    }
                ?>
                    <tr>
                        <td><?php echo $sno++; ?></td>
                        <td>
                            <span style="color: var(--primary); font-weight: 600;">
                                <?php echo htmlspecialchars($u['enrollment_number'] ?: 'N/A'); ?>
                            </span>
                        </td>
                        <td>
                            <span style="color: #64748b; font-weight: 600;">
                                <?php echo $bf->encode_decode('decrypt', $u['student_id']); ?>
                            </span>
                        </td>
                        <td><?php echo $u['student_name']; ?></td>
                        <td><?php echo $u['mobile_number']; ?></td>
                        <td><?php echo $course_name; ?></td>
                        <td>
                            <div style="display:flex; gap:0.5rem;">
                                <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'enrollment_internship', PERMISSION_EDIT)): ?>
                                    <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;" onclick="ShowPage('enrollment_internship', '<?php echo $u['enrollment_internship_id']; ?>')">Edit</button>
                                <?php endif; ?>
                                <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'enrollment_internship', PERMISSION_DELETE)): ?>
                                    <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; background: #ef4444;" onclick="deleteRecord('enrollment_internship', '<?php echo $u['id']; ?>')">Delete</button>
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
                <button class="page-btn" <?php echo ($page <= 1) ? 'disabled' : ''; ?> onclick="loadData('enrollment_internship', <?php echo $page - 1; ?>, $('#enrollment_internship_limit').val(), $('#enrollment_internship_search').val())">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <?php 
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $start_page + 4);
                if ($end_page - $start_page < 4) $start_page = max(1, $end_page - 4);
                for ($i = $start_page; $i <= $end_page; $i++) { ?>
                    <button class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>" onclick="loadData('enrollment_internship', <?php echo $i; ?>, $('#enrollment_internship_limit').val(), $('#enrollment_internship_search').val())">
                        <?php echo $i; ?>
                    </button>
                <?php } ?>
                <button class="page-btn" <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?> onclick="loadData('enrollment_internship', <?php echo $page + 1; ?>, $('#enrollment_internship_limit').val(), $('#enrollment_internship_search').val())">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    <?php
    }
    exit;
}

if (isset($action) &&  ($action == 'delete')) {
    $id = $bf->sanitize($_POST['id'] ?? '');
    $data = ['deleted' => 1, 'updated_date_time' => $GLOBALS['create_date_time_label']];
    $bf->UpdateSQL($GLOBALS['enrollment_internship_table'], $data, "id = :id", [':id' => $id]);
    echo "Success";
}
?>
