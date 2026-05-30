<?php require_once 'common_file.php'; 
$action = $_REQUEST['action'] ?? '';

$bank_id = "";
$bank_name = "";
$account_name = "";
$account_number = "";
$ifsc_code = "";
$payment_modes = [];
$branch = "";

if (isset($_REQUEST['view_bank_id']) && !isset($_POST['bank_name'])) {
    $view_bank_id = $_REQUEST['view_bank_id'];
    $banks = $bf->getTableRecords(
        $GLOBALS['bank_table'], 
        'bank_id', 
        $view_bank_id
    );

    if(!empty($banks)) {
        foreach($banks as $data) {
            if(!empty($data['bank_name'])) {
                $bank_name = $data['bank_name'];
            }
            if(!empty($data['account_name'])) {
                $account_name = $data['account_name'];
            }
            if(!empty($data['account_number'])) {
                $account_number = $data['account_number'];
            }
            if(!empty($data['ifsc_code'])) {
                $ifsc_code = $data['ifsc_code'];
            }
            if(!empty($data['payment_mode'])) {
                $payment_modes = array_map('trim', explode(',', $data['payment_mode']));
            }
            if(!empty($data['branch'])) {
                $branch = $data['branch'];
            }
        }
    }
    ?>

    <div class="header">
        <h2>
            <?php echo empty($view_bank_id) ? "New bank" : "Update bank"; ?>
        </h2>
    </div>

    <div class="module-section form-section">

        <form
            name="bank_form"
            id="bank_form"
            method="POST"
            enctype="multipart/form-data"
            onsubmit="event.preventDefault(); formSubmit('bank_form', 'bank_action.php', 'bank.php', 'bank');"
        >

            <input type="hidden" name="view_bank_id" value="<?php echo $view_bank_id; ?>">

            <div class="form-row">
                <div class="form-group col-4">
                    <label>Bank Name *</label>
                    <input
                        type="text"
                        name="bank_name"
                        class="form-input"
                        value="<?php echo $bank_name; ?>"
                    >
                    <span id="error-bank_name" class="error-msg"></span>
                </div>

                <div class="form-group col-4">
                    <label>Account Name *</label>
                    <input
                        type="text"
                        name="account_name"
                        class="form-input"
                        value="<?php echo $account_name; ?>"
                    >
                    <span id="error-account_name" class="error-msg"></span>
                </div>

                <div class="form-group col-4">
                    <label>Account Number *</label>
                    <input
                        type="text"
                        name="account_number"
                        class="form-input"
                        value="<?php echo $account_number; ?>"
                        onkeypress="return allowNumbersOnly(event)"
                    >
                    <span id="error-account_number" class="error-msg"></span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-4">
                    <label>IFSC Code </label>
                    <input
                        type="text"
                        name="ifsc_code"
                        class="form-input"
                        value="<?php echo $ifsc_code; ?>"
                    >
                    <span id="error-ifsc_code" class="error-msg"></span>
                </div>

                <div class="form-group col-4">
                    <label>Payment Modes *</label>
                    <select id="payment_modes" name="payment_modes[]" class="form-input" multiple>
                        <?php
                        $payment_mode_list = $bf->getTableRecords($GLOBALS['payment_mode_table'], 'deleted', 0);

                        foreach($payment_mode_list as $pm) {
                            if(trim(strtolower($pm['payment_mode_name'])) == 'cash') continue;
                            $selected = in_array($pm['payment_mode_id'], $payment_modes) ? 'selected' : '';

                            echo "<option value='{$pm['payment_mode_id']}' {$selected}>
                                    {$pm['payment_mode_name']}
                                </option>";
                        }
                        ?>
                        </select>
                    <span id="error-payment_modes" class="error-msg"></span>
                </div>

                <div class="form-group col-4">
                    <label>Branch </label>
                    <input
                        type="text"
                        name="branch"
                        class="form-input"
                        value="<?php echo $branch; ?>"
                    >
                    <span id="error-branch" class="error-msg"></span>
                </div>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn-add">
                    <?php echo empty($view_bank_id) ? "Add bank" : "Update bank"; ?>
                </button>
                <?php if(!empty($view_bank_id)) { ?>
                    <a
                        href="bank.php"
                        class="btn-add"
                        style="background: #ef4444; font-size: 0.75rem;"
                    >
                        Cancel
                    </a>
                <?php } ?>
            </div>

        </form>
        <script>
            setTimeout(function() {
                if($.fn.select2) {
                    $('#payment_modes').select2({
                        placeholder: "Select Payment Modes",
                        width: '100%'
                    });
                }
            }, 50);
        </script>

    </div>

<?php }

if (isset($_POST['bank_name'])) {
    $bank_name = $bf->sanitize($_POST['bank_name'] ?? '');
    $account_name = $bf->sanitize($_POST['account_name'] ?? '');
    $account_number = $bf->sanitize($_POST['account_number'] ?? '');
    $ifsc_code = $bf->sanitize($_POST['ifsc_code'] ?? '');
    $payment_modes = $_POST['payment_modes'] ?? [];
    $branch = $bf->sanitize($_POST['branch'] ?? '');
    $view_bank_id = $bf->sanitize($_POST['view_bank_id'] ?? '');

    $errors = [];
    if (empty($bank_name)) {
        $errors['bank_name'] = 'enter bank name';
    } else {
        $res = $valid->valid_name($bank_name, 'Bank Name');
        if ($res) $errors['bank_name'] = $res;
    }

    if (empty($account_name)) {
        $errors['account_name'] = 'enter account name';
    } else {
        $res = $valid->valid_name($account_name, 'Account Name');
        if ($res) $errors['account_name'] = $res;
    }

    if (empty($account_number)) {
        $errors['account_number'] = 'enter account number';
    } elseif (!preg_match('/^[0-9]{8,18}$/', $account_number)) {
        $errors['account_number'] = 'Account number must be 8 to 18 digits';
    }

    // if (empty($ifsc_code)) {
    //     $errors['ifsc_code'] = 'enter ifsc code';
    // } 
    if (!empty($ifsc_code) && !preg_match('/^[A-Za-z0-9]+$/', $ifsc_code)) {
        $errors['ifsc_code'] = 'IFSC code should contain only letters and numbers';
    }

    if (empty($payment_modes)) {
        $errors['payment_modes'] = 'select at least one payment mode';
    }

    // if (empty($branch)) {
    //     $errors['branch'] = 'enter branch';
    // } 
    if (!empty($branch)) {
        $res = $valid->valid_name($branch, 'Branch');
        if ($res) $errors['branch'] = $res;
    }

    if(!empty($account_number)) {

        $query = "SELECT bank_id FROM " . $GLOBALS['bank_table'] . " WHERE account_number = :account_number AND deleted = 0";
        if (!empty($view_bank_id)) {
            $query .= " AND bank_id != :bank_id";
        }
        $stmt = $bf->con->prepare($query);
        $params = [':account_number' => $account_number];
        if (!empty($view_bank_id)) {
            $params[':bank_id'] = $view_bank_id;
        }
        $stmt->execute($params);

        if ($stmt->fetch()) {
            // echo json_encode([
            //     'status' => 'error',
            //     'message' => 'Account number already exists'
            // ]);
            $errors['account_number'] = 'Account number already exists';
            // exit;
        }
    }

    if (empty($errors)) {
        // Check account number unique
        

        $payment_modes_str = implode(',', $payment_modes);

        $data = [
            'bank_name' => $bank_name,
            'account_name' => $account_name,
            'account_number' => $account_number,
            'ifsc_code' => $ifsc_code,
            'payment_mode' => $payment_modes_str,
            'branch' => $branch,
            'created_date_time' => date('Y-m-d H:i:s'),
            'updated_date_time' => date('Y-m-d H:i:s')
        ];

        if (empty($view_bank_id)) {
            $bf->InsertSQL(
                $GLOBALS['bank_table'], 
                $data, 
                'bank_id', 
                '', 
                'ADD BANK'
            );
            echo json_encode([
                'status' => 'success',
                'message' => 'Bank added successfully'
            ]);
        } else {
            $bf->UpdateSQL(
                $GLOBALS['bank_table'], 
                $data, 
                "bank_id = :bank_id", 
                [':bank_id' => $view_bank_id]
            );
            echo json_encode([
                'status' => 'success',
                'message' => 'Bank updated successfully'
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
    $id = $bf->sanitize($_REQUEST['id'] ?? '');
    if (!empty($id)) {
        $bf->UpdateSQL(
            $GLOBALS['bank_table'], 
            [
                'deleted' => 1,
                'updated_date_time' => date('Y-m-d H:i:s')
            ],
            "bank_id = :bank_id",
            [':bank_id' => $id]
        );
    }
    exit;
}

if ($action == 'list') {
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
    $search = isset($_POST['search']) ? $_POST['search'] : '';
    $start = ($page - 1) * $limit;

    $result = $bf->getTableList($GLOBALS['bank_table'], ['bank_name', 'account_number', 'branch'], $start, $limit, $search);
    $banks = $result['data'];
    $total_records = $result['total_records'];
    $total_pages = ceil($total_records / $limit);

    if (empty($banks)) { ?>
        <div class="table-responsive">
            <table><tr><td style="text-align:center">No banks found.</td></tr></table>
        </div>
    <?php } else {
        ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Sno</th>
                        <th>Bank Name</th>
                        <th>Account Name</th>
                        <th>Account Number</th>
                        <th>Branch</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $sno = $start + 1;
                foreach ($banks as $u) { 
                ?>
                    <tr>
                        <td><?php echo $sno++; ?></td>
                        <td><span style="font-weight: 600; color: var(--primary);"><?php echo $u['bank_name']; ?></span></td>
                        <td><?php echo $u['account_name']; ?></td>
                        <td><?php echo $u['account_number']; ?></td>
                        <td><?php echo $u['branch']; ?></td>
                        <td>
                            <div style="display:flex; gap:0.5rem;">
                                <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'bank', PERMISSION_EDIT)): ?>
                                    <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;" onclick="ShowPage('bank', '<?php echo $u['bank_id']; ?>')">Edit</button>
                                <?php endif; ?>
                                <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'bank', PERMISSION_DELETE)): ?>
                                    <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; background: #ef4444;" onclick="deleteRecord('bank', '<?php echo $u['bank_id']; ?>')">Delete</button>
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
                <button class="page-btn" <?php echo ($page <= 1) ? 'disabled' : ''; ?> onclick="loadData('bank', <?php echo $page - 1; ?>, $('#bank_limit').val(), $('#bank_search').val())">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <?php 
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $start_page + 4);
                if ($end_page - $start_page < 4) $start_page = max(1, $end_page - 4);
                for ($i = $start_page; $i <= $end_page; $i++) { ?>
                    <button class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>" onclick="loadData('bank', <?php echo $i; ?>, $('#bank_limit').val(), $('#bank_search').val())">
                        <?php echo $i; ?>
                    </button>
                <?php } ?>
                <button class="page-btn" <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?> onclick="loadData('bank', <?php echo $page + 1; ?>, $('#bank_limit').val(), $('#bank_search').val())">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    <?php
    }
    exit;
}
?>
