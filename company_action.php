<?php require_once 'common_file.php'; 
if ($user_role != 'admin' && !$is_management) { exit('Unauthorized'); }
$action = $_REQUEST['action'] ?? '';

$company_id = "";
$company_name = "";
$company_email = "";
$company_mobile = "";
$company_address = "";
$gst = "";
$branch = "";
$logo_image = "";

if (isset($_REQUEST['view_company_id']) && !isset($_POST['company_name'])) {
    $view_company_id = $_REQUEST['view_company_id'];
    $companies = $bf->getTableRecords(
        $GLOBALS['company_table'], 
        'company_id', 
        $view_company_id
    );

    if(!empty($companies)) {
        foreach($companies as $data) {
            if(!empty($data['company_name'])) {
                $company_name = $data['company_name'];
            }
            if(!empty($data['company_email'])) {
                $company_email = $data['company_email'];
            }
            if(!empty($data['company_mobile'])) {
                $company_mobile = $data['company_mobile'];
            }
            if(!empty($data['company_address'])) {
                $company_address = $data['company_address'];
            }
            if(!empty($data['gst'])) {
                $gst = $data['gst'];
            }
            if(!empty($data['branch'])) {
                $branch = $data['branch'];
            }
            if(!empty($data['logo_image'])) {
                $logo_image = $data['logo_image'];
            }
        }
    }
    $upload_dir = 'uploads/company_logos/';
    ?>

    <div class="header">
        <h2>
            <?php echo empty($view_company_id) ? "New Company" : "Update Company"; ?>
        </h2>
    </div>

    <div class="module-section form-section">

        <form
            name="company_form"
            id="company_form"
            method="POST"
            enctype="multipart/form-data"
            onsubmit="event.preventDefault(); formSubmit('company_form', 'company_action.php', 'company.php', 'company');"
        >

            <input type="hidden" name="view_company_id" value="<?php echo $view_company_id; ?>">

            <div class="form-row">
                <div class="form-group col-4">
                    <label>Company Name *</label>
                    <input
                        type="text"
                        name="company_name"
                        class="form-input"
                        value="<?php echo $company_name; ?>"
                    >
                    <span id="error-company_name" class="error-msg"></span>
                </div>

                <div class="form-group col-4">
                    <label>Email *</label>
                    <input
                        type="email"
                        name="company_email"
                        class="form-input"
                        value="<?php echo $company_email; ?>"
                    >
                    <span id="error-company_email" class="error-msg"></span>
                </div>

                <div class="form-group col-4">
                    <label>Mobile Number *</label>
                    <input
                        type="text"
                        name="company_mobile"
                        class="form-input"
                        value="<?php echo $company_mobile; ?>"
                        onkeypress="return allowNumbersOnly(event)"
                    >
                    <span id="error-company_mobile" class="error-msg"></span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-4">
                    <label>GST Number *</label>
                    <input
                        type="text"
                        name="gst"
                        class="form-input"
                        value="<?php echo $gst; ?>"
                    >
                    <span id="error-gst" class="error-msg"></span>
                </div>

                <div class="form-group col-4">
                    <label>Branch *</label>
                    <select name="branch" class="form-input">
                        <option value="">Select Branch</option>
                        <option value="Sivakasi" <?php echo ($branch == 'Sivakasi') ? 'selected' : ''; ?>>Sivakasi</option>
                        <option value="Srivilliputhur" <?php echo ($branch == 'Srivilliputhur') ? 'selected' : ''; ?>>Srivilliputhur</option>
                    </select>
                    <span id="error-branch" class="error-msg"></span>
                </div>

                <div class="form-group col-4">
                    <label>Company Logo</label>
                    <input
                        type="file"
                        name="logo_image"
                        class="form-input"
                        accept="image/*"
                    >
                    <span id="error-logo_image" class="error-msg"></span>
                    <?php if (!empty($logo_image)) { ?>
                        <img src="<?php echo $logo_image.$logo_image; ?>" alt="Company Logo" class="logo-preview">
                    <?php } ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-12">
                    <label>Address *</label>
                    <textarea
                        name="company_address"
                        class="form-input"
                        rows="4"
                    ><?php echo $company_address; ?></textarea>
                    <span id="error-company_address" class="error-msg"></span>
                </div>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn-add">
                    <?php echo empty($view_company_id) ? "Add Company" : "Update Company"; ?>
                </button>
                <?php if(!empty($view_company_id)) { ?>
                    <a
                        href="company.php"
                        class="btn-add"
                        style="background: #ef4444; font-size: 0.75rem;"
                    >
                        Cancel
                    </a>
                <?php } ?>
            </div>

        </form>
    </div>

<?php }

if (isset($_POST['company_name'])) {
    $company_name = $bf->sanitize($_POST['company_name'] ?? '');
    $company_email = $bf->sanitize($_POST['company_email'] ?? '');
    $company_mobile = $bf->sanitize($_POST['company_mobile'] ?? '');
    $company_address = $bf->sanitize($_POST['company_address'] ?? '');
    $gst = $bf->sanitize($_POST['gst'] ?? '');
    $branch = $bf->sanitize($_POST['branch'] ?? '');
    $view_company_id = $bf->sanitize($_POST['view_company_id'] ?? '');

    $errors = [];
    
    if (empty($company_name)) {
        $errors['company_name'] = 'enter company name';
    } else {
        $res = $valid->valid_name($company_name, 'Company Name');
        if ($res) $errors['company_name'] = $res;
    }

    if (empty($company_email)) {
        $errors['company_email'] = 'enter company email';
    } elseif (!filter_var($company_email, FILTER_VALIDATE_EMAIL)) {
        $errors['company_email'] = 'invalid email format';
    }

    if (empty($company_mobile)) {
        $errors['company_mobile'] = 'enter mobile number';
    } elseif (!preg_match('/^[0-9]{10}$/', $company_mobile)) {
        $errors['company_mobile'] = 'mobile number must be 10 digits';
    }

    if (empty($company_address)) {
        $errors['company_address'] = 'enter company address';
    }

    if (empty($gst)) {
        $errors['gst'] = 'enter gst number';
    } else {
        $res = $valid->valid_gst($gst, 'GST Number');
        if ($res) $errors['gst'] = $res;
    }

    if (empty($branch)) {
        $errors['branch'] = 'select a branch';
    }

    $prev_id = "";

    if(!empty($company_mobile)) {
        $prev_id = $bf->getTableColumnValue($GLOBALS['company_table'], 'company_mobile', $company_mobile, 'company_id');

        if(!empty($prev_id) && ($prev_id != $view_company_id)) {
            $errors['company_mobile'] = 'Mobile Number Already Exists';
        }
    }  

    if(!empty($gst)) {
        $prev_id = $bf->getTableColumnValue($GLOBALS['company_table'], 'gst', $gst, 'company_id');

        if(!empty($prev_id) && ($prev_id != $view_company_id)) {
            $errors['gst'] = 'gst Already Exists';
        }
    } 

    // Handle logo image upload
    $logo_path = "";
    if (!empty($_FILES['logo_image']['name'])) {
        $file = $_FILES['logo_image'];
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_size = $file['size'];
        $file_error = $file['error'];

        // Validate file
        if ($file_error === UPLOAD_ERR_OK) {
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (!in_array($file_ext, $allowed_extensions)) {
                $errors['logo_image'] = 'only jpg, jpeg, png, gif files are allowed';
            } elseif ($file_size > 2097152) { // 2MB
                $errors['logo_image'] = 'file size must not exceed 2MB';
            } else {
                // Create uploads directory if it doesn't exist
                $upload_dir = 'uploads/company_logos/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                // Generate unique filename
                $new_filename = 'company_' . time() . '_' . uniqid() . '.' . $file_ext;
                // $upload_path = $upload_dir . $new_filename;
                $upload_path = $new_filename;


                if (move_uploaded_file($file_tmp, $upload_path)) {
                    $logo_path = $upload_path;
                } else {
                    $errors['logo_image'] = 'failed to upload image';
                }
            }
        } else {
            $errors['logo_image'] = 'file upload error';
        }
    }

    if (empty($errors)) {

        if(!empty($company_name) && (!empty($branch))) {

            $prev_id = $bf->checkCompanyAlreadyExists($company_name, $branch);

            if(!empty($prev_id) && ($prev_id != $view_company_id)) {
            echo json_encode([
                    'status' => 'error',
                    'message' => 'Company already exists in this branch'
                ]);
                exit();
            }
        }
        // Create encrypted company_details
        $company_details_text = $company_name . '$$$Address:' . $company_address . '$$$gst:' . $gst . '$$$Email:' . $company_email . '$$$mobile:' . $company_mobile;
        $company_details_encrypted = $bf->encode_decode('encrypt', $company_details_text);

        $data = [
            'company_name' => $company_name,
            'company_email' => $company_email,
            'company_mobile' => $company_mobile,
            'company_address' => $company_address,
            'gst' => $gst,
            'branch' => $branch,
            'company_details' => $company_details_encrypted,
            'created_date_time' => date('Y-m-d H:i:s'),
            'updated_date_time' => date('Y-m-d H:i:s')
        ];

        // Add logo path only if a new image was uploaded
        if (!empty($logo_path)) {
            $data['logo_image'] = $logo_path;
        }

        if (empty($view_company_id)) {
            $bf->InsertSQL(
                $GLOBALS['company_table'], 
                $data, 
                'company_id', 
                '', 
                'ADD COMPANY'
            );
            echo json_encode([
                'status' => 'success',
                'message' => 'Company added successfully'
            ]);
        } else {
            $bf->UpdateSQL(
                $GLOBALS['company_table'], 
                $data, 
                "company_id = :company_id", 
                [':company_id' => $view_company_id]
            );
            echo json_encode([
                'status' => 'success',
                'message' => 'Company updated successfully'
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

if (isset($_REQUEST['action']) && ($action == 'delete')) {
    // Delete not needed as per requirements, but keeping structure for future
    exit;
}

if ($action == 'list') {
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
    $search = isset($_POST['search']) ? $_POST['search'] : '';
    $start = ($page - 1) * $limit;

    $result = $bf->getTableList($GLOBALS['company_table'], ['company_name', 'company_email', 'branch', 'gst'], $start, $limit, $search);
    $companies = $result['data'];
    $total_records = $result['total_records'];
    $total_pages = ceil($total_records / $limit);

    if (empty($companies)) { ?>
        <div class="table-responsive">
            <table><tr><td style="text-align:center">No companies found.</td></tr></table>
        </div>
    <?php } else {
        ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Sno</th>
                        <th>Company Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Branch</th>
                        <th>GST</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $sno = $start + 1;
                foreach ($companies as $u) { 
                ?>
                    <tr>
                        <td><?php echo $sno++; ?></td>
                        <td><span style="font-weight: 600; color: var(--primary);"><?php echo $u['company_name']; ?></span></td>
                        <td><?php echo $u['company_email']; ?></td>
                        <td><?php echo $u['company_mobile']; ?></td>
                        <td><?php echo $u['branch']; ?></td>
                        <td><?php echo $u['gst']; ?></td>
                        <td>
                            <div style="display:flex; gap:0.5rem;">
                                <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;" onclick="ShowPage('company', '<?php echo $u['company_id']; ?>')">Edit</button>
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
                <button class="page-btn" <?php echo ($page <= 1) ? 'disabled' : ''; ?> onclick="loadData('company', <?php echo $page - 1; ?>, $('#company_limit').val(), $('#company_search').val())">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <?php 
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $start_page + 4);
                if ($end_page - $start_page < 4) $start_page = max(1, $end_page - 4);
                for ($i = $start_page; $i <= $end_page; $i++) { ?>
                    <button class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>" onclick="loadData('company', <?php echo $i; ?>, $('#company_limit').val(), $('#company_search').val())">
                        <?php echo $i; ?>
                    </button>
                <?php } ?>
                <button class="page-btn" <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?> onclick="loadData('company', <?php echo $page + 1; ?>, $('#company_limit').val(), $('#company_search').val())">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    <?php
    }
    exit;
}
?>
