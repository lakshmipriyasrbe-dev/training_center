<?php require_once 'common_file.php'; 
if ($user_role != 'admin') { exit('Unauthorized'); }
$action = $_REQUEST['action'] ?? '';

$payment_mode_id = "";
$payment_mode_name = "";

if (isset($_REQUEST['view_payment_mode_id']) && !isset($_POST['payment_mode_name'])) {
    $view_payment_mode_id = $_REQUEST['view_payment_mode_id'];
    $payment_modes = $bf->getTableRecords(
        $GLOBALS['payment_mode_table'], 
        'payment_mode_id', 
        $view_payment_mode_id
    );

    if(!empty($payment_modes)) {
        foreach($payment_modes as $data) {
            if(!empty($data['payment_mode_name'])) {
                $payment_mode_name = $data['payment_mode_name'];
            }
        }
    }
    ?>

    <div class="header">
        <h2>
            <?php echo empty($view_payment_mode_id) ? "New payment mode" : "Update payment mode"; ?>
        </h2>
    </div>

    <div class="module-section form-section">

        <form
            name="payment_mode_form"
            id="payment_mode_form"
            method="POST"
            enctype="multipart/form-data"
            onsubmit="event.preventDefault(); formSubmit('payment_mode_form', 'payment_mode_action.php', 'payment_mode.php', 'payment_mode');"
        >

            <input type="hidden" name="view_payment_mode_id" value="<?php echo $view_payment_mode_id; ?>">

            <div class="form-row">
                <div class="form-group col-4">
                    <label>Payment Mode Name *</label>
                    <input
                        type="text"
                        name="payment_mode_name"
                        class="form-input"
                        value="<?php echo $payment_mode_name; ?>"
                    >
                    <span id="error-payment_mode_name" class="error-msg"></span>
                </div>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn-add">
                    <?php echo empty($view_payment_mode_id) ? "Add payment mode" : "Update payment mode"; ?>
                </button>
                <?php if(!empty($view_payment_mode_id)) { ?>
                    <a
                        href="payment_mode.php"
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

if (isset($_POST['payment_mode_name'])) {
    $payment_mode_name = $bf->sanitize($_POST['payment_mode_name'] ?? '');
    $view_payment_mode_id = $bf->sanitize($_POST['view_payment_mode_id'] ?? '');

    $errors = [];
    if (empty($payment_mode_name)) {
        $errors['payment_mode_name'] = 'enter payment mode name';
    } else {
        $res = $valid->valid_name($payment_mode_name, 'Payment Mode Name');
        if ($res) $errors['payment_mode_name'] = $res;
    }

    if (empty($errors)) {
        $query = "SELECT payment_mode_id FROM " . $GLOBALS['payment_mode_table'] . " WHERE LOWER(payment_mode_name) = :payment_mode_name AND deleted = 0";
        if (!empty($view_payment_mode_id)) {
            $query .= " AND payment_mode_id != :payment_mode_id";
        }

        $stmt = $bf->con->prepare($query);
        $params = [':payment_mode_name' => strtolower($payment_mode_name)];
        if (!empty($view_payment_mode_id)) {
            $params[':payment_mode_id'] = $view_payment_mode_id;
        }
        $stmt->execute($params);

        if ($stmt->fetch()) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Payment mode name already exists'
            ]);
            exit;
        }

        $data = [
            'payment_mode_name' => $payment_mode_name,
            'created_date_time' => date('Y-m-d H:i:s'),
            'updated_date_time' => date('Y-m-d H:i:s')
        ];

        if (empty($view_payment_mode_id)) {
            $bf->InsertSQL(
                $GLOBALS['payment_mode_table'], 
                $data, 
                'payment_mode_id', 
                '', 
                'ADD PAYMENT MODE'
            );
            echo json_encode([
                'status' => 'success',
                'message' => 'Payment mode added successfully'
            ]);
        } else {
            $bf->UpdateSQL(
                $GLOBALS['payment_mode_table'], 
                $data, 
                "payment_mode_id = :payment_mode_id", 
                [':payment_mode_id' => $view_payment_mode_id]
            );
            echo json_encode([
                'status' => 'success',
                'message' => 'Payment mode updated successfully'
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
            $GLOBALS['payment_mode_table'], 
            [
                'deleted' => 1,
                'updated_date_time' => date('Y-m-d H:i:s')
            ],
            "payment_mode_id = :payment_mode_id",
            [':payment_mode_id' => $id]
        );
    }
    exit;
}

if ($action == 'list') {
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
    $search = isset($_POST['search']) ? $_POST['search'] : '';
    $start = ($page - 1) * $limit;

    $result = $bf->getTableList($GLOBALS['payment_mode_table'], ['payment_mode_name'], $start, $limit, $search);
    $payment_modes = $result['data'];
    $total_records = $result['total_records'];
    $total_pages = ceil($total_records / $limit);

    if (empty($payment_modes)) { ?>
        <div class="table-responsive">
            <table><tr><td style="text-align:center">No payment modes found.</td></tr></table>
        </div>
    <?php } else {
        ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Sno</th>
                        <th>Payment Mode</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $sno = $start + 1;
                foreach ($payment_modes as $u) { 
                ?>
                    <tr>
                        <td><?php echo $sno++; ?></td>
                        <td><span style="font-weight: 600; color: var(--primary);"><?php echo $u['payment_mode_name']; ?></span></td>
                        <td><?php echo date('d-m-Y H:i', strtotime($u['created_date_time'])); ?></td>
                        <td>
                            <div style="display:flex; gap:0.5rem;">
                                <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;" onclick="ShowPage('payment_mode', '<?php echo $u['payment_mode_id']; ?>')">Edit</button>
                                <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; background: #ef4444;" onclick="deleteRecord('payment_mode', '<?php echo $u['payment_mode_id']; ?>')">Delete</button>
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
                <button class="page-btn" <?php echo ($page <= 1) ? 'disabled' : ''; ?> onclick="loadData('payment_mode', <?php echo $page - 1; ?>, $('#payment_mode_limit').val(), $('#payment_mode_search').val())">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <?php 
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $start_page + 4);
                if ($end_page - $start_page < 4) $start_page = max(1, $end_page - 4);
                for ($i = $start_page; $i <= $end_page; $i++) { ?>
                    <button class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>" onclick="loadData('payment_mode', <?php echo $i; ?>, $('#payment_mode_limit').val(), $('#payment_mode_search').val())">
                        <?php echo $i; ?>
                    </button>
                <?php } ?>
                <button class="page-btn" <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?> onclick="loadData('payment_mode', <?php echo $page + 1; ?>, $('#payment_mode_limit').val(), $('#payment_mode_search').val())">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    <?php
    }
    exit;
}
?>
