<?php require_once 'common_file.php'; 
$action = $_REQUEST['action'] ?? '';

$expense_category_id = "";
$expense_category_name = ""; $description = "";

if (isset($_REQUEST['view_expense_category_id']) && !isset($_POST['expense_category_name'])) {
    $view_expense_category_id = $_REQUEST['view_expense_category_id'];
    $expense_categorys = $bf->getTableRecords(
        $GLOBALS['expense_category_table'], 
        'expense_category_id', 
        $view_expense_category_id
    );

    if(!empty($expense_categorys)) {
        foreach($expense_categorys as $data) {
            if(!empty($data['expense_category_name'])) {
                $expense_category_name = $data['expense_category_name'];
            }
            if(!empty($data['description'])) {
                $description = $data['description'];
            }
        }
    }
    ?>

    <div class="header">
        <h2>
            <?php echo empty($view_expense_category_id) ? "New Expense Category" : "Update Expense Category"; ?>
        </h2>
    </div>

    <div class="module-section form-section">

        <form
            name="expense_category_form"
            id="expense_category_form"
            method="POST"
            enctype="multipart/form-data"
            onsubmit="event.preventDefault(); formSubmit('expense_category_form', 'expense_category_action.php', 'expense_category.php', 'expense_category');"
        >

            <input type="hidden" name="view_expense_category_id" value="<?php echo $view_expense_category_id; ?>">

            <div class="form-row">
                <div class="form-group col-4">
                    <label>Expense category Name *</label>
                    <input
                        type="text"
                        name="expense_category_name"
                        class="form-input"
                        value="<?php echo $expense_category_name; ?>"
                    >
                    <span id="error-expense_category_name" class="error-msg"></span>
                </div>

                <div class="form-group col-4">
                    <label>Description </label>
                    <div class="form-group">
                        <textarea name="description" rows="5" placeholder="Describe about the category..."></textarea>
                    </div>
                    <span id="error-description" class="error-msg"></span>
                </div>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn-add">
                    <?php echo empty($view_expense_category_id) ? "Add expense category" : "Update expense category"; ?>
                </button>
                <?php if(!empty($view_expense_category_id)) { ?>
                    <a
                        href="expense_category.php"
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

if (isset($_POST['expense_category_name'])) {
    $expense_category_name = $bf->sanitize($_POST['expense_category_name'] ?? '');
    $view_expense_category_id = $bf->sanitize($_POST['view_expense_category_id'] ?? '');
    $description = $bf->sanitize($_POST['description'] ?? '');

    $errors = [];
    if (empty($expense_category_name)) {
        $errors['expense_category_name'] = 'enter expense category name';
    } else {
        $res = $valid->valid_name($expense_category_name, 'expense_category Name');
        if ($res) $errors['expense_category_name'] = $res;
    }

    if (!empty($description)) {
        $res = $valid->valid_name($description, 'Description');
        if ($res) $errors['description'] = $res;
    }

    if(!empty($expense_category_name)) {

        $expense_category_name = strtolower($expense_category_name);

        $query = "SELECT expense_category_id FROM " . $GLOBALS['expense_category_table'] . " WHERE LOWER(expense_category_name) = :expense_category_name AND deleted = 0";
        if (!empty($view_expense_category_id)) {
            $query .= " AND expense_category_id != :expense_category_id";
        }
       
        $stmt = $bf->con->prepare($query);
        $params = [':expense_category_name' => $expense_category_name];
        if (!empty($view_expense_category_id)) {
            $params[':expense_category_id'] = $view_expense_category_id;
        }
        // echo $bf->debugQuery($query, $params);
        // exit();
        $stmt->execute($params);

        if ($stmt->fetch()) {
            // echo json_encode([
            //     'status' => 'error',
            //     'message' => 'Account number already exists'
            // ]);
            $errors['expense_category_name'] = 'Expense Category Name already exists';
            // exit;
        }
    }

    if (empty($errors)) {
        // Check account number unique
        $data = [
            'expense_category_name' => $expense_category_name,
            'description' => $description,
            'created_date_time' => date('Y-m-d H:i:s'),
            'updated_date_time' => date('Y-m-d H:i:s')
        ];

        if (empty($view_expense_category_id)) {
            $bf->InsertSQL(
                $GLOBALS['expense_category_table'], 
                $data, 
                'expense_category_id', 
                '', 
                'ADD expense_category'
            );
            echo json_encode([
                'status' => 'success',
                'message' => 'expense_category added successfully'
            ]);
        } else {
            $bf->UpdateSQL(
                $GLOBALS['expense_category_table'], 
                $data, 
                "expense_category_id = :expense_category_id", 
                [':expense_category_id' => $view_expense_category_id]
            );
            echo json_encode([
                'status' => 'success',
                'message' => 'expense_category updated successfully'
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
        $relations = [
            ['table' => $GLOBALS['expense_entry_table'], 'column' => 'expense_category_id', 'value' => $id, 'label' => 'Expense Entry']
        ];
        $linked = $bf->isRecordLinked($relations);
        if ($linked) {
            echo "Error: Cannot delete category because it is linked with $linked table.";
            exit;
        }

        $bf->UpdateSQL(
            $GLOBALS['expense_category_table'], 
            [
                'deleted' => 1,
                'updated_date_time' => date('Y-m-d H:i:s')
            ],
            "expense_category_id = :expense_category_id",
            [':expense_category_id' => $id]
        );
    }
    exit;
}

if ($action == 'list') {
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
    $search = isset($_POST['search']) ? $_POST['search'] : '';
    $start = ($page - 1) * $limit;

    $result = $bf->getTableList($GLOBALS['expense_category_table'], ['expense_category_name'], $start, $limit, $search);
    $expense_categorys = $result['data'];
    $total_records = $result['total_records'];
    $total_pages = ceil($total_records / $limit);

    if (empty($expense_categorys)) { ?>
        <div class="table-responsive">
            <table><tr><td style="text-align:center">No Expense Categorys found.</td></tr></table>
        </div>
    <?php } else {
        ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Sno</th>
                        <th>Expense Category Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $sno = $start + 1;
                foreach ($expense_categorys as $u) { 
                ?>
                    <tr>
                        <td><?php echo $sno++; ?></td>
                        <td><?php echo $u['expense_category_name']; ?></td>
                        <td>
                            <div style="display:flex; gap:0.5rem;">
                                <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'expense_category', PERMISSION_EDIT)): ?>
                                    <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;" onclick="ShowPage('expense_category', '<?php echo $u['expense_category_id']; ?>')">Edit</button>
                                <?php endif; ?>
                                <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'expense_category', PERMISSION_DELETE)): 
                                    $relations = [
                                        ['table' => $GLOBALS['expense_entry_table'], 'column' => 'expense_category_id', 'value' => $u['expense_category_id'], 'label' => 'Expense Entry']
                                    ];
                                    $linked = $bf->isRecordLinked($relations);
                                    if ($linked) { ?>
                                        <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; background: #94a3b8; cursor: not-allowed;" title="Cannot delete: Linked with <?php echo $linked; ?> table" onclick="alert('Cannot delete: this expense category is linked with <?php echo $linked; ?>. Please delete linked expense entries first.')">Delete</button>
                                    <?php } else { ?>
                                        <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; background: #ef4444;" onclick="deleteRecord('expense_category', '<?php echo $u['expense_category_id']; ?>')">Delete</button>
                                    <?php }
                                endif; ?>
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
                <button class="page-btn" <?php echo ($page <= 1) ? 'disabled' : ''; ?> onclick="loadData('expense_category', <?php echo $page - 1; ?>, $('#expense_category_limit').val(), $('#expense_category_search').val())">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <?php 
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $start_page + 4);
                if ($end_page - $start_page < 4) $start_page = max(1, $end_page - 4);
                for ($i = $start_page; $i <= $end_page; $i++) { ?>
                    <button class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>" onclick="loadData('expense_category', <?php echo $i; ?>, $('#expense_category_limit').val(), $('#expense_category_search').val())">
                        <?php echo $i; ?>
                    </button>
                <?php } ?>
                <button class="page-btn" <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?> onclick="loadData('expense_category', <?php echo $page + 1; ?>, $('#expense_category_limit').val(), $('#expense_category_search').val())">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    <?php
    }
    exit;
}
?>
