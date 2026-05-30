<?php require_once 'common_file.php'; 
$action = $_POST['action'] ?? $_REQUEST['action'] ?? '';

function getBankMap() {
    global $bf;
    $bank_map = [];
    $banks = $bf->getTableRecords($GLOBALS['bank_table'], 'deleted', 0);
    if (!empty($banks)) {
        foreach ($banks as $bank) {
            if (empty($bank['payment_mode'])) {
                continue;
            }
            $payment_mode_ids = array_filter(array_map('trim', explode(',', $bank['payment_mode'])));
            foreach ($payment_mode_ids as $payment_mode_id) {
                $bank_map[$payment_mode_id][] = [
                    'bank_id' => $bank['bank_id'],
                    'bank_name' => $bank['bank_name']
                ];
            }
        }
    }
    return $bank_map;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['expense_entry_date']) && !isset($_REQUEST['view_expense_entry_id'])) {
    // This branch is normally not used for form submission because AJAX sends view_expense_entry_id only on form display.
}

if (isset($_POST['expense_entry_date']) && isset($_POST['expense_category_id'])) {
    $expense_entry_date = $bf->sanitize($_POST['expense_entry_date'] ?? '');
    $expense_category_id = $bf->sanitize($_POST['expense_category_id'] ?? '');
    $description = $bf->sanitize($_POST['description'] ?? '');
    $payment_mode_ids = $_POST['payment_mode'] ?? [];
    $bank_ids = $_POST['bank'] ?? [];
    $amounts = $_POST['amount'] ?? [];
    $view_expense_entry_id = $bf->sanitize($_POST['view_expense_entry_id'] ?? '');

    $errors = [];

    if (empty($expense_entry_date)) {
        $errors['expense_entry_date'] = 'enter expense entry date';
    } else {
        $date_time = strtotime($expense_entry_date);
        if (!$date_time) {
            $errors['expense_entry_date'] = 'enter valid date';
        } elseif ($date_time > strtotime(date('Y-m-d'))) {
            $errors['expense_entry_date'] = 'future date is not allowed';
        }
    }

    if (empty($expense_category_id)) {
        $errors['expense_category_id'] = 'select expense category';
    } else {
        
        $category_record = $bf->getTableRecords($GLOBALS['expense_category_table'], 'expense_category_id', $expense_category_id);
        if (empty($category_record)) {
            $errors['expense_category_id'] = 'selected category not found';
        }
    }

    $payment_rows = [];
    $bank_map = getBankMap();
    $total_amount = 0;
    $valid_row_count = 0;

    $row_count = max(count($payment_mode_ids), count($bank_ids), count($amounts));
    for ($i = 0; $i < $row_count; $i++) {
        $mode = trim($payment_mode_ids[$i] ?? '');
        $bank = trim($bank_ids[$i] ?? '');
        $amount = trim($amounts[$i] ?? '');

        if ($mode === '' && $amount === '') {
            continue;
        }

        $row_errors = [];
        if (empty($mode)) {
            $row_errors[] = 'select payment mode';
        }

        if ($amount === '') {
            $row_errors[] = 'enter amount';
        } elseif (!is_numeric($amount) || floatval($amount) <= 0) {
            $row_errors[] = 'amount must be a positive number';
        }

        $allowed_banks = $mode !== '' ? ($bank_map[$mode] ?? []) : [];
        if (!empty($allowed_banks)) {
            if (empty($bank)) {
                $row_errors[] = 'select bank';
            } else {
                $valid_bank_ids = array_column($allowed_banks, 'bank_id');
                if (!in_array($bank, $valid_bank_ids)) {
                    $row_errors[] = 'invalid bank selection';
                }
            }
        } else {
            $bank = '';
        }

        // print_r($row_errors);

        if (!empty($row_errors)) {
            $errors['payment_rows'] = 'Please complete the payment row details';
        } else {
            $valid_row_count++;
            $payment_rows[] = [
                'payment_mode' => $mode,
                'bank' => $bank,
                'amount' => number_format((float)$amount, 2, '.', '')
            ];
            $total_amount += (float)$amount;
        }
    }

    if ($valid_row_count === 0) {
        $errors['payment_rows'] = 'add at least one payment mode with amount';
    }

    if (empty($errors)) {
        $uploaded_files = [];
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'doc', 'docx'];
        
        if (isset($_FILES['attachments'])) {
            $files = $_FILES['attachments'];
            $file_count = count($files['name']);
            
            for ($i = 0; $i < $file_count; $i++) {
                $file_name = $files['name'][$i];
                $file_tmp = $files['tmp_name'][$i];
                $file_error = $files['error'][$i];
                
                // Skip empty uploads safely
                if ($file_error === UPLOAD_ERR_NO_FILE || empty($file_name)) {
                    continue;
                }
                
                if ($file_error !== UPLOAD_ERR_OK) {
                    $errors['attachments'] = 'Error uploading file: ' . $file_name;
                    break;
                }
                
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                if (!in_array($file_ext, $allowed_extensions)) {
                    $errors['attachments'] = 'Invalid file type: ' . $file_name . '. Allowed: ' . implode(', ', $allowed_extensions);
                    break;
                }
                
                // Generate unique file name
                $unique_name = time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $file_name);
                
                $upload_path = 'upload/';
                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, true);
                }
                
                if (move_uploaded_file($file_tmp, $upload_path . $unique_name)) {
                    $uploaded_files[] = $unique_name;
                } else {
                    $errors['attachments'] = 'Failed to save uploaded file: ' . $file_name;
                    break;
                }
            }
        }

        if (!empty($errors)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'errors' => $errors]);
            exit;
        }

        // Fetch remaining existing attachments submitted from form
        $existing_attachments = $bf->sanitize($_POST['existing_attachments'] ?? '');
        
        $all_attachments = [];
        if (!empty($existing_attachments)) {
            $all_attachments = array_filter(array_map('trim', explode(',', $existing_attachments)));
        }
        $all_attachments = array_merge($all_attachments, $uploaded_files);

        $data = [
            'expense_category_id' => $expense_category_id,
            'expense_entry_date' => $expense_entry_date,
            'description' => $description,
            'payment_mode' => implode(',', array_column($payment_rows, 'payment_mode')),
            'bank' => implode(',', array_column($payment_rows, 'bank')),
            'amount' => implode(',', array_column($payment_rows, 'amount')),
            'total_amount' => number_format($total_amount, 2, '.', ''),
            'attachments' => implode(',', $all_attachments),
            'updated_date_time' => date('Y-m-d H:i:s')
        ];

        if (empty($view_expense_entry_id)) {
            $data['created_date_time'] = date('Y-m-d H:i:s');
            $data['expense_entry_number'] = $bf->automate_number($GLOBALS['expense_entry_table'], 'expense_entry_number', '', '');
            $bf->InsertSQL(
                $GLOBALS['expense_entry_table'],
                $data,
                'expense_entry_id',
                '',
                'ADD expense entry'
            );
            echo json_encode(['status' => 'success', 'message' => 'expense entry added successfully']);
        } else {
            $bf->UpdateSQL(
                $GLOBALS['expense_entry_table'],
                $data,
                'id = :id',
                [':id' => $view_expense_entry_id]
            );
            echo json_encode(['status' => 'success', 'message' => 'expense entry updated successfully']);
        }
        exit;
    }

    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'errors' => $errors]);
    exit;
}

if (isset($_REQUEST['view_expense_entry_id'])) {
    $view_expense_entry_id = $bf->sanitize($_REQUEST['view_expense_entry_id'] ?? '');
    $expense_entry_id = '';
    $expense_entry_date = date('Y-m-d');
    $description = '';
    $payment_mode_ids = [];
    $bank_ids = [];
    $amounts = []; $expense_category_id = "";
    $total_amount = '';
    $attachments_str = '';

    if (!empty($view_expense_entry_id)) {
        $expense_entry_list = $bf->getTableRecords($GLOBALS['expense_entry_table'], 'id', $view_expense_entry_id);
        if (!empty($expense_entry_list)) {
            $expense_entry = $expense_entry_list[0];
            if (!empty($expense_entry['expense_entry_id'])) {
                $expense_entry_id = $expense_entry['expense_entry_id'];
            }
            if (!empty($expense_entry['expense_category_id'])) {
                $expense_category_id = $expense_entry['expense_category_id'];
            }
            if (!empty($expense_entry['expense_entry_date'])) {
                $expense_entry_date = $expense_entry['expense_entry_date'];
            }
            if (!empty($expense_entry['course_type'])) {
                $course_type = $expense_entry['course_type'];
            }
            
            if (!empty($expense_entry['description'])) {
                $description = $expense_entry['description'];
            }
            if (!empty($expense_entry['payment_mode'])) {
                $payment_mode_ids = array_map('trim', explode(',', $expense_entry['payment_mode']));
            }
            if (!empty($expense_entry['bank'])) {
                $bank_ids = array_map('trim', explode(',', $expense_entry['bank']));
            }
            if (!empty($expense_entry['amount'])) {
                $amounts = array_map('trim', explode(',', $expense_entry['amount']));
            }
            if (!empty($expense_entry['total_amount'])) {
                $total_amount = $expense_entry['total_amount'];
            }
            if (!empty($expense_entry['attachments'])) {
                $attachments_str = $expense_entry['attachments'];
            }
        }
    }

    $expense_catgory_list = $bf->getTableRecords($GLOBALS['expense_category_table'], 'deleted', 0);
    $students_internship = $bf->getTableRecords($GLOBALS['enrollment_internship_table'], 'deleted', 0);
    $payment_modes = $bf->getTableRecords($GLOBALS['payment_mode_table'], 'deleted', 0);
    $bank_map = getBankMap();

    $payment_rows = [];
    $existing_rows = max(count($payment_mode_ids), count($bank_ids), count($amounts), 1);
    for ($i = 0; $i < $existing_rows; $i++) {
        $payment_rows[] = [
            'payment_mode' => $payment_mode_ids[$i] ?? '',
            'bank' => $bank_ids[$i] ?? '',
            'amount' => $amounts[$i] ?? ''
        ];
    }

    $payment_mode_options = '';
    foreach ($payment_modes as $pm) {
        $selected = '';
        $payment_mode_options .= "<option value='" . $pm['payment_mode_id'] . "'>" . $pm['payment_mode_name'] . "</option>\n";
    }

    $bank_map_json = json_encode($bank_map);
    $category_list_json = json_encode($expense_catgory_list);

    ?>

    <div class="header">
        <h2><?php echo empty($view_expense_entry_id) ? 'New expense_entry' : 'Update expense_entry'; ?></h2>
    </div>

    <div class="module-section form-section">
        <form
            name="expense_entry_form"
            id="expense_entry_form"
            method="POST"
            enctype="multipart/form-data"
            onsubmit="event.preventDefault(); formSubmit('expense_entry_form', 'expense_entry_action.php', 'expense_entry.php', 'expense_entry');"
        >
            <input type="hidden" name="view_expense_entry_id" value="<?php echo $view_expense_entry_id; ?>">

            <div class="form-row">
                <div class="form-group col-4">
                    <label>expense_entry Date *</label>
                    <input
                        type="date"
                        name="expense_entry_date"
                        id="expense_entry_date"
                        class="form-input"
                        value="<?php echo $expense_entry_date; ?>"
                        max="<?php echo date('Y-m-d'); ?>"
                    >
                    <span id="error-expense_entry_date" class="error-msg"></span>
                </div>

                <div class="form-group col-4">
                    <label>Category Type *</label>
                    <select name="expense_category_id" id="expense_category_id" class="form-input">
                        <option value="">Select</option>

                            <?php
                                $category_list = $bf->getTableRecords($GLOBALS['expense_category_table']);
                                if(!empty($category_list)) {
                                    foreach($category_list as $category) {
                            ?>

                            <option
                                value="<?php echo $category['expense_category_id']; ?>"
                                <?php if($expense_category_id == $category['expense_category_id']) { ?>
                                    selected
                                <?php } ?>
                            >
                                <?php echo $category['expense_category_name']; ?>
                            </option>

                            <?php
                                    }
                                }
                            ?>
                    </select>
                    <span id="error-expense_category_id" class="error-msg"></span>
                </div>

                <div class="form-group col-12">
                    <label>Description</label>
                    <textarea
                        name="description"
                        class="form-input"
                        rows="3"
                    ><?php echo $description; ?></textarea>
                    <span id="error-description" class="error-msg"></span>
                </div>

                <div class="form-group col-12">
                    <label>Attachments</label>
                    <style>
                        .premium-upload-zone {
                            border: 2px dashed rgba(59, 130, 246, 0.4);
                            background: rgba(59, 130, 246, 0.02);
                            border-radius: 8px;
                            padding: 1.5rem;
                            text-align: center;
                            cursor: pointer;
                            transition: all 0.3s ease;
                            margin-bottom: 0.5rem;
                        }
                        .premium-upload-zone:hover, .premium-upload-zone.dragover {
                            border-color: #3b82f6;
                            background: rgba(59, 130, 246, 0.06);
                            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.08);
                        }
                        .premium-upload-zone .upload-icon {
                            font-size: 2rem;
                            color: #3b82f6;
                            margin-bottom: 0.5rem;
                            transition: transform 0.3s ease;
                        }
                        .premium-upload-zone:hover .upload-icon {
                            transform: translateY(-4px);
                        }
                        .premium-upload-zone .upload-text {
                            font-size: 0.95rem;
                            font-weight: 600;
                            color: #1e293b;
                        }
                        .premium-upload-zone .upload-subtext {
                            font-size: 0.8rem;
                            color: #64748b;
                            margin-top: 0.25rem;
                        }

                        /* Previews Container */
                        .upload-previews-container {
                            display: flex;
                            flex-wrap: wrap;
                            gap: 1rem;
                            margin-top: 0.75rem;
                            margin-bottom: 0.75rem;
                        }

                        /* Preview Card */
                        .preview-card {
                            position: relative;
                            width: 80px;
                            height: 80px;
                            border-radius: 8px;
                            border: 1px solid rgba(0, 0, 0, 0.08);
                            background: #fff;
                            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                            justify-content: center;
                            overflow: visible;
                            transition: transform 0.2s ease, box-shadow 0.2s ease;
                        }
                        .preview-card:hover {
                            transform: translateY(-2px);
                            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
                        }
                        .preview-card img {
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                            border-radius: 8px;
                        }
                        .preview-card .doc-icon {
                            font-size: 1.75rem;
                            color: #3b82f6;
                            margin-bottom: 2px;
                        }
                        .preview-card .file-ext-badge {
                            position: absolute;
                            bottom: 4px;
                            background: #3b82f6;
                            color: #fff;
                            font-size: 0.65rem;
                            font-weight: 700;
                            padding: 1px 4px;
                            border-radius: 3px;
                            text-transform: uppercase;
                        }
                        .preview-card .file-name-tooltip {
                            position: absolute;
                            top: 100%;
                            left: 50%;
                            transform: translateX(-50%) translateY(4px);
                            background: #1e293b;
                            color: #fff;
                            font-size: 0.7rem;
                            padding: 3px 6px;
                            border-radius: 4px;
                            white-space: nowrap;
                            opacity: 0;
                            pointer-events: none;
                            transition: opacity 0.2s ease, transform 0.2s ease;
                            z-index: 10;
                            max-width: 120px;
                            overflow: hidden;
                            text-overflow: ellipsis;
                        }
                        .preview-card:hover .file-name-tooltip {
                            opacity: 1;
                            transform: translateX(-50%) translateY(8px);
                        }

                        /* Delete Close Button */
                        .preview-card .btn-remove-preview {
                            position: absolute;
                            top: -6px;
                            right: -6px;
                            width: 20px;
                            height: 20px;
                            border-radius: 50%;
                            background: #ef4444;
                            color: #fff;
                            border: 1px solid #fff;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 0.7rem;
                            cursor: pointer;
                            box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
                            transition: transform 0.2s ease, background-color 0.2s ease;
                            z-index: 5;
                        }
                        .preview-card .btn-remove-preview:hover {
                            transform: scale(1.15);
                            background: #dc2626;
                        }
                    </style>

                    <input type="hidden" name="existing_attachments" id="existing_attachments" value="<?php echo htmlspecialchars($attachments_str); ?>">
                    <input type="file" id="attachments_input" name="attachments[]" class="form-input" style="display: none;" multiple onchange="handleFileSelect(event)">
                    
                    <div class="premium-upload-zone" onclick="document.getElementById('attachments_input').click();">
                        <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                        <div class="upload-text">Click to browse or drag & drop files here</div>
                        <div class="upload-subtext">Allowed: jpg, jpeg, png, webp, pdf, doc, docx</div>
                    </div>

                    <div id="previews_container" class="upload-previews-container"></div>
                    <span id="error-attachments" class="error-msg"></span>
                </div>

                <div class="form-group col-12">
                    <label>Payment Details *</label>
                    <div class="payment-table-wrapper">
                        <table class="payment-table">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Payment Mode</th>
                                    <th>Bank</th>
                                    <th>Amount</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="payment_rows_body">
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" style="text-align: right; font-weight: 700;">Total Amount</td>
                                    <td>
                                        <input type="text" id="total_amount" class="form-input" name="total_amount" value="<?php echo $total_amount; ?>" readonly>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div style="margin-top: 0.75rem; display: flex; justify-content: flex-end; gap: 0.5rem;">
                        <button type="button" class="btn-add" onclick="addPaymentRow();">Add Payment Line</button>
                    </div>
                    <span id="error-payment_rows" class="error-msg"></span>
                </div>

            </div>

            <div class="form-buttons">
                <button type="submit" class="btn-add"><?php echo empty($view_expense_entry_id) ? 'Add expense entry' : 'Update expense entry'; ?></button>
                <?php if (!empty($view_expense_entry_id)) { ?>
                    <a href="expense_entry.php" class="btn-add" style="background: #ef4444; font-size: 0.75rem;">Cancel</a>
                <?php } ?>
            </div>
        </form>
    </div>

    <script>
        const paymentModeOptions = `<?php echo addslashes($payment_mode_options); ?>`;
        const bankMap = <?php echo $bank_map_json; ?>;
        let paymentRows = <?php echo json_encode($payment_rows); ?>;
        let selectedCategory = '<?php echo $expense_category_id; ?>';

        function addPaymentRow() {
            paymentRows.push({ payment_mode: '', bank: '', amount: '' });
            renderPaymentRows();
        }

        function removePaymentRow(index) {
            paymentRows.splice(index, 1);
            renderPaymentRows();
        }

        function renderPaymentRows() {
            const tbody = document.getElementById('payment_rows_body');
            let html = '';
            paymentRows.forEach(function(row, index) {
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>
                            <select name="payment_mode[]" class="form-input" onchange="onPaymentModeChange(${index}, this.value)">
                                <option value="">Select</option>
                                ${paymentModeOptions}
                            </select>
                        </td>
                        <td id="bank_cell_${index}">
                            <input type="hidden" name="bank[]" value="">-
                        </td>
                        <td>
                            <input type="text" name="amount[]" class="form-input" value="${row.amount}" oninput="updateTotal();" onkeypress="return allowNumbersOnly(event)">
                        </td>
                        <td>
                            <button type="button" class="btn-add" style="background: #ef4444; font-size: 0.75rem;" onclick="removePaymentRow(${index});">Remove</button>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
            restoreSelections();
            updateTotal();
        }

        function restoreSelections() {
            paymentRows.forEach(function(row, index) {
                // Restore payment mode
                const select = document.querySelector(`#payment_rows_body tr:nth-child(${index + 1}) select[name='payment_mode[]']`);
                if (select && row.payment_mode) {
                    select.value = row.payment_mode;
                }
                // Restore bank dropdown
                if (row.payment_mode) {
                    const bankOptions = getBankOptions(row.payment_mode);
                    const bankCell = document.getElementById('bank_cell_' + index);
                    if (bankCell && bankOptions.length > 0) {
                        let selectHtml = '<select name="bank[]" class="form-input">';
                        selectHtml += '<option value="">Select</option>';
                        bankOptions.forEach(function(bank) {
                            const selected = (bank.bank_id == row.bank) ? ' selected' : '';
                            selectHtml += `<option value="${bank.bank_id}"${selected}>${bank.bank_name}</option>`;
                        });
                        selectHtml += '</select>';
                        bankCell.innerHTML = selectHtml;
                    }
                }
            });
        }

        function getBankOptions(paymentModeId) {
            if (!paymentModeId) {
                return [];
            }
            return bankMap[paymentModeId] || [];
        }

        function onPaymentModeChange(index, paymentModeId) {
            paymentRows[index].payment_mode = paymentModeId;
            paymentRows[index].bank = '';
            const bankOptions = getBankOptions(paymentModeId);
            const bankCell = document.getElementById('bank_cell_' + index);
            if (bankCell) {
                if (bankOptions.length > 0) {
                    let selectHtml = '<select name="bank[]" class="form-input">';
                    selectHtml += '<option value="">Select</option>';
                    bankOptions.forEach(function(bank) {
                        selectHtml += `<option value="${bank.bank_id}">${bank.bank_name}</option>`;
                    });
                    selectHtml += '</select>';
                    bankCell.innerHTML = selectHtml;
                } else {
                    bankCell.innerHTML = '<input type="hidden" name="bank[]" value="">-';
                }
            }
        }

        function updateTotal() {
            let total = 0;
            document.querySelectorAll('input[name="amount[]"]').forEach(function(input, index) {
                const amount = parseFloat(input.value) || 0;
                total += amount;
                paymentRows[index].amount = input.value;
            });
            document.getElementById('total_amount').value = total.toFixed(2);
        }

        document.addEventListener('change', function(event) {
            const target = event.target;
            if (target.name === 'bank[]') {
                const row = target.closest('tr');
                const rowIndex = Array.from(row.parentNode.children).indexOf(row);
                paymentRows[rowIndex].bank = target.value;
            }
        });

        // ------------------ Attachments Preview JS ------------------
        let selectedFiles = [];
        let existingFiles = <?php echo json_encode(!empty($attachments_str) ? array_filter(array_map('trim', explode(',', $attachments_str))) : []); ?>;

        function handleFileSelect(event) {
            const files = event.target.files;
            addFiles(files);
        }

        function addFiles(files) {
            const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'doc', 'docx'];
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const ext = file.name.split('.').pop().toLowerCase();
                if (!allowedExtensions.includes(ext)) {
                    alert('Invalid file type: ' + file.name + '. Allowed: ' + allowedExtensions.join(', '));
                    continue;
                }
                if (!selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
                    selectedFiles.push(file);
                }
            }
            updateFileInputAndPreviews();
        }

        function removeNewFile(index) {
            selectedFiles.splice(index, 1);
            updateFileInputAndPreviews();
        }

        function removeExistingFile(fileName) {
            existingFiles = existingFiles.filter(f => f !== fileName);
            document.getElementById('existing_attachments').value = existingFiles.join(',');
            renderAllPreviews();
        }

        function updateFileInputAndPreviews() {
            const fileInput = document.getElementById('attachments_input');
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            fileInput.files = dt.files;
            renderAllPreviews();
        }

        function renderAllPreviews() {
            const container = document.getElementById('previews_container');
            if (!container) return;
            container.innerHTML = '';

            // Render existing files
            existingFiles.forEach(function(file) {
                const ext = file.split('.').pop().toLowerCase();
                const card = document.createElement('div');
                card.className = 'preview-card';
                
                let contentHtml = '';
                if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
                    contentHtml = `<a href="upload/${file}" target="_blank"><img src="upload/${file}" alt="${file}"></a>`;
                } else {
                    let iconClass = 'fa-file';
                    if (ext === 'pdf') iconClass = 'fa-file-pdf';
                    if (['doc', 'docx'].includes(ext)) iconClass = 'fa-file-word';
                    contentHtml = `
                        <a href="upload/${file}" target="_blank" style="text-decoration:none; display:flex; flex-direction:column; align-items:center;">
                            <i class="fas ${iconClass} doc-icon"></i>
                            <span class="file-ext-badge">${ext}</span>
                        </a>
                    `;
                }
                
                card.innerHTML = `
                    ${contentHtml}
                    <button type="button" class="btn-remove-preview" onclick="removeExistingFile('${file}')" title="Delete Attachment"><i class="fas fa-times"></i></button>
                    <div class="file-name-tooltip">${file}</div>
                `;
                container.appendChild(card);
            });

            // Render newly selected files
            selectedFiles.forEach(function(file, index) {
                const ext = file.name.split('.').pop().toLowerCase();
                const card = document.createElement('div');
                card.className = 'preview-card';
                
                const objectUrl = URL.createObjectURL(file);
                let contentHtml = '';
                if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
                    contentHtml = `<a href="${objectUrl}" target="_blank"><img src="${objectUrl}" alt="${file.name}"></a>`;
                } else {
                    let iconClass = 'fa-file';
                    if (ext === 'pdf') iconClass = 'fa-file-pdf';
                    if (['doc', 'docx'].includes(ext)) iconClass = 'fa-file-word';
                    contentHtml = `
                        <a href="${objectUrl}" target="_blank" download="${file.name}" style="text-decoration:none; display:flex; flex-direction:column; align-items:center;">
                            <i class="fas ${iconClass} doc-icon"></i>
                            <span class="file-ext-badge">${ext}</span>
                        </a>
                    `;
                }
                
                card.innerHTML = `
                    ${contentHtml}
                    <button type="button" class="btn-remove-preview" onclick="removeNewFile(${index})" title="Remove Attachment"><i class="fas fa-times"></i></button>
                    <div class="file-name-tooltip">${file.name}</div>
                `;
                container.appendChild(card);
            });
        }

        // Dropzone drag & drop support
        const uploadZone = document.querySelector('.premium-upload-zone');
        if (uploadZone) {
            ['dragenter', 'dragover'].forEach(eventName => {
                uploadZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    uploadZone.classList.add('dragover');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    uploadZone.classList.remove('dragover');
                }, false);
            });

            uploadZone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                if (dt && dt.files) {
                    addFiles(dt.files);
                }
            }, false);
        }

        renderAllPreviews();

        // Execute immediately (page loaded via AJAX, DOMContentLoaded already fired)
        renderPaymentRows();
    </script>

<?php }

if ($action == 'list') {
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
    $search = isset($_POST['search']) ? $_POST['search'] : '';
    $start = ($page - 1) * $limit;

    // Use getTableList for pagination but we need to handle student labels
    $result = $bf->getTableList($GLOBALS['expense_entry_table'], ['expense_entry_number', 'description'], $start, $limit, $search);
    $expense_entrys = $result['data'];
    $total_records = $result['total_records'];
    $total_pages = ceil($total_records / $limit);

    if (empty($expense_entrys)) { ?>
        <div class="table-responsive">
            <table><tr><td style="text-align:center">No Expense Entry found.</td></tr></table>
        </div>
    <?php } else {
        ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Expense Entry No</th>
                        <th>Expense Entry Date</th>
                        <th>Expense Category name</th>
                        <th>Total Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                foreach ($expense_entrys as $expense_entry) {
                    $student_label = ''; $expense_category_name = "";
                    if (!empty($expense_entry['expense_category_id'])) {
                        $expense_category_name = $bf->getTableColumnValue($GLOBALS['expense_category_table'], 'expense_category_id', $expense_entry['expense_category_id'], 'expense_category_name');
                    }
                ?>
                    <tr>
                        <td><span style="font-weight: 600; color: var(--primary);"><?php echo htmlspecialchars($expense_entry['expense_entry_number']); ?></span></td>
                        <td><?php echo date('d-m-Y', strtotime($expense_entry['expense_entry_date'])); ?></td>
                        <td><?php echo $expense_category_name; ?></td>
                        <td><strong>₹<?php echo number_format($expense_entry['total_amount'], 2); ?></strong></td>
                        <td>
                            <div style="display:flex; gap:0.5rem;">
                                <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'expense_entry', PERMISSION_EDIT)): ?>
                                    <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; background: #3b82f6;" onclick="ShowPage('expense_entry', '<?php echo $expense_entry['id']; ?>')">Edit</button>
                                <?php endif; ?>
                                <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'expense_entry', PERMISSION_VIEW)): ?>
                                    <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;" onclick="window.open('reports/rpt_expense_entry_a5.php?expense_entry_id=<?php echo $expense_entry['id']; ?>', '_blank')">Print</button>
                                <?php endif; ?>
                                <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'expense_entry', PERMISSION_DELETE)): ?>
                                    <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; background: #ef4444;" onclick="deleteRecord('expense_entry', '<?php echo $expense_entry['id']; ?>')">Delete</button>
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
                <button class="page-btn" <?php echo ($page <= 1) ? 'disabled' : ''; ?> onclick="loadData('expense_entry', <?php echo $page - 1; ?>, $('#expense_entry_limit').val(), $('#expense_entry_search').val())">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <?php 
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $start_page + 4);
                if ($end_page - $start_page < 4) $start_page = max(1, $end_page - 4);
                for ($i = $start_page; $i <= $end_page; $i++) { ?>
                    <button class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>" onclick="loadData('expense_entry', <?php echo $i; ?>, $('#expense_entry_limit').val(), $('#expense_entry_search').val())">
                        <?php echo $i; ?>
                    </button>
                <?php } ?>
                <button class="page-btn" <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?> onclick="loadData('expense_entry', <?php echo $page + 1; ?>, $('#expense_entry_limit').val(), $('#expense_entry_search').val())">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    <?php
    }
    exit;
}

if (isset($action) && ($action == 'delete')) {
    $id = $bf->sanitize($_REQUEST['id'] ?? '');
    if (!empty($id)) {
        $bf->UpdateSQL($GLOBALS['expense_entry_table'], ['deleted' => 1, 'updated_date_time' => date('Y-m-d H:i:s')], 'id = :id', [':id' => $id]);
    }
    echo 'Success';
}
?>