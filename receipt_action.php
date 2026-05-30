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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receipt_date']) && !isset($_REQUEST['view_receipt_id'])) {
    // This branch is normally not used for form submission because AJAX sends view_receipt_id only on form display.
}

if (isset($_POST['receipt_date']) && isset($_POST['course_type'])) {
    $receipt_date = $bf->sanitize($_POST['receipt_date'] ?? '');
    $course_type = $bf->sanitize($_POST['course_type'] ?? '');
    $student_id = $bf->sanitize($_POST['student_id'] ?? '');
    $description = $bf->sanitize($_POST['description'] ?? '');
    $payment_mode_ids = $_POST['payment_mode'] ?? [];
    $bank_ids = $_POST['bank'] ?? [];
    $amounts = $_POST['amount'] ?? [];
    $view_receipt_id = $bf->sanitize($_POST['view_receipt_id'] ?? '');
    $enrollment_id = $bf->sanitize($_POST['enrollment_id'] ?? '');
    $enrollment_paid_amount = $bf->sanitize($_POST['enrollment_paid_amount'] ?? '');

    $errors = [];

    if (empty($receipt_date)) {
        $errors['receipt_date'] = 'enter receipt date';
    } else {
        $date_time = strtotime($receipt_date);
        if (!$date_time) {
            $errors['receipt_date'] = 'enter valid date';
        } elseif ($date_time > strtotime(date('Y-m-d'))) {
            $errors['receipt_date'] = 'future date is not allowed';
        }
    }

    if (empty($course_type) || !in_array($course_type, ['training', 'internship'])) {
        $errors['course_type'] = 'select course type';
    }

    if (empty($student_id)) {
        $errors['student_id'] = 'select student';
    } else {
        $student_table = $course_type === 'internship' ? $GLOBALS['enrollment_internship_table'] : $GLOBALS['enrollment_table'];
        $id_field = $course_type === 'internship' ? 'enrollment_internship_id' : 'enrollment_id';
        $student_record = $bf->getTableRecords($student_table, $id_field, $student_id);
        if (empty($student_record)) {
            $errors['student_id'] = 'selected student not found';
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

    // Validate total matches enrollment paid_amount when coming from enrollment
    if (!empty($enrollment_paid_amount) && $valid_row_count > 0) {
        $expected = number_format((float)$enrollment_paid_amount, 2, '.', '');
        $actual = number_format($total_amount, 2, '.', '');
        if ($expected !== $actual) {
            $errors['payment_rows'] = 'Total amount (₹' . $actual . ') must exactly equal enrollment paid amount (₹' . $expected . ')';
        }
    }

    // Validate that total paid amount does not exceed the course fees
    if (empty($errors) && !empty($student_id) && $valid_row_count > 0) {
        $fees_amount = 0.0;
        if ($course_type === 'training') {
            $enroll_rec = $bf->getTableRecords($GLOBALS['enrollment_table'], 'enrollment_id', $student_id);
            if (!empty($enroll_rec)) {
                $fees_amount = floatval($enroll_rec[0]['fees_amount']);
            }
        } else {
            $enroll_rec = $bf->getTableRecords($GLOBALS['enrollment_internship_table'], 'enrollment_internship_id', $student_id);
            if (!empty($enroll_rec)) {
                $fees_amount = floatval($enroll_rec[0]['fees_amount']);
            }
        }

        // Sum existing payments for this enrollment_id, excluding current receipt if editing
        $pay_query = "SELECT SUM(CAST(total_amount AS DECIMAL(10,2))) as total_paid FROM " . $GLOBALS['payment_table'] . " WHERE enrollment_id = :enrollment_id AND deleted = 0";
        $pay_params = [':enrollment_id' => $student_id];
        if (!empty($view_receipt_id)) {
            $pay_query .= " AND id != :receipt_id";
            $pay_params[':receipt_id'] = $view_receipt_id;
        }
        $pay_res = $bf->getQueryRecords($pay_query, $pay_params);
        $total_paid_so_far = !empty($pay_res[0]['total_paid']) ? floatval($pay_res[0]['total_paid']) : 0.0;

        if (($total_paid_so_far + $total_amount) > $fees_amount) {
            $errors['payment_rows'] = 'Total paid amount (₹' . number_format($total_paid_so_far + $total_amount, 2) . ') cannot exceed the total fees amount (₹' . number_format($fees_amount, 2) . '). Already paid: ₹' . number_format($total_paid_so_far, 2);
        }
    }

    if (empty($errors)) {
        $actual_student_id = '';
        if ($course_type === 'training') {
            $enroll_rec = $bf->getTableRecords($GLOBALS['enrollment_table'], 'enrollment_id', $student_id);
            if (!empty($enroll_rec)) {
                $actual_student_id = $enroll_rec[0]['student_id'];
            }
        } else {
            $enroll_rec = $bf->getTableRecords($GLOBALS['enrollment_internship_table'], 'enrollment_internship_id', $student_id);
            if (!empty($enroll_rec)) {
                $actual_student_id = $enroll_rec[0]['student_id'];
            }
        }

        $data = [
            'course_type' => $course_type,
            'payment_date' => $receipt_date,
            'student_id' => $actual_student_id, // store actual student_id
            'enrollment_id' => $student_id, // store unique enrollment_id
            'description' => $description,
            'payment_mode' => implode(',', array_column($payment_rows, 'payment_mode')),
            'bank' => implode(',', array_column($payment_rows, 'bank')),
            'amount' => implode(',', array_column($payment_rows, 'amount')),
            'total_amount' => number_format($total_amount, 2, '.', ''),
            'updated_date_time' => date('Y-m-d H:i:s')
        ];

        if (empty($view_receipt_id)) {
            $data['created_date_time'] = date('Y-m-d H:i:s');
            $data['payment_id'] = $bf->automate_number($GLOBALS['payment_table'], 'payment_id', '', '');
            $bf->InsertSQL(
                $GLOBALS['payment_table'],
                $data,
                '',
                '',
                'ADD RECEIPT'
            );
            echo json_encode(['status' => 'success', 'message' => 'Receipt added successfully']);
        } else {
            $bf->UpdateSQL(
                $GLOBALS['payment_table'],
                $data,
                'id = :id',
                [':id' => $view_receipt_id]
            );
            echo json_encode(['status' => 'success', 'message' => 'Receipt updated successfully']);
        }
        exit;
    }

    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'errors' => $errors]);
    exit;
}

// ======================== PAYMENT MODAL FOR ENROLLMENT ========================
if ($action == 'get_payment_modal') {
    $enrollment_id = $bf->sanitize($_POST['enrollment_id'] ?? '');
    $enrollment_paid_amount = $bf->sanitize($_POST['paid_amount'] ?? '');
    $modal_course_type = $bf->sanitize($_POST['course_type'] ?? 'training');
    $modal_student_id = $bf->sanitize($_POST['student_id'] ?? '');

    $students_training = $bf->getTableRecords($GLOBALS['enrollment_table'], 'deleted', 0);
    $students_internship = $bf->getTableRecords($GLOBALS['enrollment_internship_table'], 'deleted', 0);
    $payment_modes = $bf->getTableRecords($GLOBALS['payment_mode_table'], 'deleted', 0);
    $bank_map = getBankMap();

    $payment_mode_options = '';
    foreach ($payment_modes as $pm) {
        $payment_mode_options .= "<option value='" . $pm['payment_mode_id'] . "'>" . $pm['payment_mode_name'] . "</option>\n";
    }

    $courses = [];
    $course_records = $bf->getTableRecords($GLOBALS['course_table'], 'deleted', 0);
    if (!empty($course_records)) {
        foreach ($course_records as $c) {
            $courses[$c['course_id']] = $c['course_name'];
        }
    }

    $student_lists_js = ['training' => [], 'internship' => []];
    foreach ($students_training as $row) {
        $course_name = $courses[$row['course_id']] ?? '';
        $student_lists_js['training'][] = [
            'id' => $row['enrollment_id'],
            'student_id' => $row['student_id'],
            'student_id_decrypted' => $bf->encode_decode('decrypt', $row['student_id']),
            'label' => $bf->encode_decode('decrypt', $row['enrollment_id']) . ' - ' . $row['student_name'] . ' - ' . $course_name
        ];
    }
    foreach ($students_internship as $row) {
        $course_name = $courses[$row['course_id']] ?? '';
        $student_lists_js['internship'][] = [
            'id' => $row['enrollment_internship_id'],
            'student_id' => $row['student_id'],
            'student_id_decrypted' => $bf->encode_decode('decrypt', $row['student_id']),
            'label' => $bf->encode_decode('decrypt', $row['enrollment_internship_id']) . ' - ' . $row['student_name'] . ' - ' . $course_name
        ];
    }

    $bank_map_json = json_encode($bank_map);
    $student_lists_json = json_encode($student_lists_js);
    $initial_row_amount = floatval($enrollment_paid_amount) > 0 ? number_format((float)$enrollment_paid_amount, 2, '.', '') : '';
    $payment_rows_json = json_encode([['payment_mode' => '', 'bank' => '', 'amount' => $initial_row_amount]]);
    ?>

    <div class="header" style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Payment Receipt</h2>
        <button type="button" class="btn-add" style="background: #ef4444; font-size: 0.8rem; padding: 0.4rem 1rem;" onclick="closePaymentModal()">✕ Close</button>
    </div>

    <div style="background: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: 0.5rem; padding: 0.75rem 1rem; margin-bottom: 1rem; font-size: 0.9rem; color: #1e40af;">
        <strong>Enrollment Payment:</strong> Total receipt amount must equal ₹<?php echo number_format((float)$enrollment_paid_amount, 2); ?>
    </div>

    <form name="modal_receipt_form" id="modal_receipt_form" method="POST" onsubmit="event.preventDefault(); submitModalReceipt();">
        <input type="hidden" name="enrollment_id" value="<?php echo $enrollment_id; ?>">
        <input type="hidden" name="enrollment_paid_amount" value="<?php echo $enrollment_paid_amount; ?>">
        <input type="hidden" name="view_receipt_id" value="">

        <div class="form-row">
            <div class="form-group col-4">
                <label>Receipt Date *</label>
                <input type="date" name="receipt_date" id="modal_receipt_date" class="form-input" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>">
                <span id="error-receipt_date" class="error-msg"></span>
            </div>

            <div class="form-group col-4">
                <label>Course Type *</label>
                <select name="course_type" id="modal_course_type" class="form-input" onchange="onModalCourseTypeChange()">
                    <option value="">Select</option>
                    <option value="training" <?php echo $modal_course_type === 'training' ? 'selected' : ''; ?>>Training</option>
                    <option value="internship" <?php echo $modal_course_type === 'internship' ? 'selected' : ''; ?>>Internship</option>
                </select>
                <span id="error-course_type" class="error-msg"></span>
            </div>

            <div class="form-group col-4">
                <label>Student *</label>
                <select name="student_id" id="modal_student_id" class="form-input">
                    <option value="">Select</option>
                </select>
                <span id="error-student_id" class="error-msg"></span>
            </div>

            <div class="form-group col-12">
                <label>Description</label>
                <textarea name="description" class="form-input" rows="2">Enrollment payment</textarea>
                <span id="error-description" class="error-msg"></span>
            </div>

            <div class="form-group col-12">
                <label>Payment Details * <span style="font-weight: 400; color: #6b7280; font-size: 0.85em;">(Must total ₹<?php echo number_format((float)$enrollment_paid_amount, 2); ?>)</span></label>
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
                        <tbody id="modal_payment_rows_body">
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align: right; font-weight: 700;">Total Amount</td>
                                <td>
                                    <input type="text" id="modal_total_amount" class="form-input" name="total_amount" value="" readonly>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div style="margin-top: 0.75rem; display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" class="btn-add" onclick="addModalPaymentRow();">Add Payment Line</button>
                </div>
                <span id="error-payment_rows" class="error-msg"></span>
                <div id="modal_amount_status" style="margin-top: 0.5rem; font-size: 0.85rem;"></div>
            </div>
        </div>

        <div class="form-buttons">
            <button type="submit" class="btn-add" id="modal_submit_btn">Save Receipt</button>
        </div>

        <div id="modal-success-msg" class="success-msg hidden" style="margin-top: 1rem; padding: 0.75rem; border-radius: 0.5rem; text-align: center;"></div>
    </form>

    <script>
        const modalPaymentModeOptions = `<?php echo addslashes($payment_mode_options); ?>`;
        const modalStudentLists = <?php echo $student_lists_json; ?>;
        const modalBankMap = <?php echo $bank_map_json; ?>;
        let modalPaymentRows = <?php echo $payment_rows_json; ?>;
        let modalSelectedCourseType = '<?php echo $modal_course_type; ?>';
        let modalSelectedStudentId = '<?php echo $modal_student_id; ?>';
        const enrollmentPaidAmount = parseFloat('<?php echo $enrollment_paid_amount; ?>') || 0;

        function populateModalStudentOptions() {
            const studentSelect = document.getElementById('modal_student_id');
            const list = modalStudentLists[modalSelectedCourseType] || [];
            studentSelect.innerHTML = '<option value="">Select</option>';
            list.forEach(function(student) {
                const selected = (student.id === modalSelectedStudentId || 
                                  student.student_id === modalSelectedStudentId || 
                                  student.student_id_decrypted === modalSelectedStudentId) ? 'selected' : '';
                studentSelect.innerHTML += `<option value="${student.id}" ${selected}>${student.label}</option>`;
            });
        }

        function onModalCourseTypeChange() {
            modalSelectedCourseType = document.getElementById('modal_course_type').value;
            modalSelectedStudentId = '';
            populateModalStudentOptions();
        }

        function addModalPaymentRow() {
            modalPaymentRows.push({ payment_mode: '', bank: '', amount: '' });
            renderModalPaymentRows();
        }

        function removeModalPaymentRow(index) {
            modalPaymentRows.splice(index, 1);
            renderModalPaymentRows();
        }

        function renderModalPaymentRows() {
            const tbody = document.getElementById('modal_payment_rows_body');
            let html = '';
            modalPaymentRows.forEach(function(row, index) {
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>
                            <select name="payment_mode[]" class="form-input" onchange="onModalPaymentModeChange(${index}, this.value)">
                                <option value="">Select</option>
                                ${modalPaymentModeOptions}
                            </select>
                        </td>
                        <td id="modal_bank_cell_${index}">
                            -
                        </td>
                        <td>
                            <input type="text" name="amount[]" class="form-input" value="${row.amount}" oninput="updateModalTotal();" onkeypress="return allowNumbersOnly(event)">
                        </td>
                        <td>
                            <button type="button" class="btn-add" style="background: #ef4444; font-size: 0.75rem;" onclick="removeModalPaymentRow(${index});">Remove</button>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
            restoreModalSelections();
            updateModalTotal();
        }

        function restoreModalSelections() {
            modalPaymentRows.forEach(function(row, index) {
                const select = document.querySelector(`#modal_payment_rows_body tr:nth-child(${index + 1}) select[name='payment_mode[]']`);
                if (select && row.payment_mode) {
                    select.value = row.payment_mode;
                }
                if (row.payment_mode) {
                    const bankOptions = getModalBankOptions(row.payment_mode);
                    const bankCell = document.getElementById('modal_bank_cell_' + index);
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

        function getModalBankOptions(paymentModeId) {
            if (!paymentModeId) return [];
            return modalBankMap[paymentModeId] || [];
        }

        function onModalPaymentModeChange(index, paymentModeId) {
            modalPaymentRows[index].payment_mode = paymentModeId;
            modalPaymentRows[index].bank = '';
            const bankOptions = getModalBankOptions(paymentModeId);
            const bankCell = document.getElementById('modal_bank_cell_' + index);
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
                    bankCell.innerHTML = '-';
                }
            }
        }

        function updateModalTotal() {
            let total = 0;
            document.querySelectorAll('#modal_receipt_form input[name="amount[]"]').forEach(function(input, index) {
                const amount = parseFloat(input.value) || 0;
                total += amount;
                modalPaymentRows[index].amount = input.value;
            });
            document.getElementById('modal_total_amount').value = total.toFixed(2);

            // Show amount match status
            const statusDiv = document.getElementById('modal_amount_status');
            const diff = enrollmentPaidAmount - total;
            if (total === 0) {
                statusDiv.innerHTML = '';
            } else if (Math.abs(diff) < 0.01) {
                statusDiv.innerHTML = '<span style="color: #10b981; font-weight: 600;">✓ Amount matches enrollment paid amount</span>';
            } else if (diff > 0) {
                statusDiv.innerHTML = '<span style="color: #f59e0b; font-weight: 600;">⚠ Remaining: ₹' + diff.toFixed(2) + '</span>';
            } else {
                statusDiv.innerHTML = '<span style="color: #ef4444; font-weight: 600;">✕ Exceeds by: ₹' + Math.abs(diff).toFixed(2) + '</span>';
            }
        }

        document.addEventListener('change', function(event) {
            const target = event.target;
            if (target.closest('#modal_receipt_form') && target.name === 'bank[]') {
                const row = target.closest('tr');
                const rowIndex = Array.from(row.parentNode.children).indexOf(row);
                modalPaymentRows[rowIndex].bank = target.value;
            }
        });

        function submitModalReceipt() {
            const form = document.getElementById('modal_receipt_form');
            const errorSpans = form.querySelectorAll('.error-msg');
            errorSpans.forEach(span => span.innerText = '');

            // Calculate total and validate against enrollmentPaidAmount
            let total = 0;
            document.querySelectorAll('#modal_receipt_form input[name="amount[]"]').forEach(function(input) {
                total += parseFloat(input.value) || 0;
            });
            const diff = Math.abs(enrollmentPaidAmount - total);
            if (enrollmentPaidAmount > 0 && diff >= 0.01) {
                const errorSpan = document.getElementById('error-payment_rows');
                if (errorSpan) {
                    errorSpan.innerText = 'Total amount (₹' + total.toFixed(2) + ') must exactly equal enrollment paid amount (₹' + enrollmentPaidAmount.toFixed(2) + ')';
                    errorSpan.style.color = '#ef4444';
                } else {
                    alert('Total amount (₹' + total.toFixed(2) + ') must exactly equal enrollment paid amount (₹' + enrollmentPaidAmount.toFixed(2) + ')');
                }
                return;
            }

            const submitBtn = document.getElementById('modal_submit_btn');
            submitBtn.disabled = true;
            submitBtn.innerText = 'Processing...';

            const formData = new FormData(form);

            fetch('receipt_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const msgDiv = document.getElementById('modal-success-msg');
                    if (msgDiv) {
                        msgDiv.innerText = data.message;
                        msgDiv.classList.remove('hidden');
                        msgDiv.style.background = 'rgba(16, 185, 129, 0.1)';
                        msgDiv.style.color = '#10b981';
                    }
                    setTimeout(function() {
                        closePaymentModal();
                    }, 1500);
                } else {
                    if (data.errors) {
                        for (const field in data.errors) {
                            const errorSpan = document.getElementById('error-' + field);
                            if (errorSpan) {
                                errorSpan.innerText = data.errors[field];
                                errorSpan.style.color = '#ef4444';
                            }
                        }
                    }
                    if (data.message) {
                        const msgDiv = document.getElementById('modal-success-msg');
                        if (msgDiv) {
                            msgDiv.innerText = data.message;
                            msgDiv.classList.remove('hidden');
                            msgDiv.style.background = 'rgba(239, 68, 68, 0.1)';
                            msgDiv.style.color = '#ef4444';
                        }
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Save Receipt';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An unexpected error occurred.');
                submitBtn.disabled = false;
                submitBtn.innerText = 'Save Receipt';
            });
        }

        // Initialize immediately
        populateModalStudentOptions();
        renderModalPaymentRows();
    </script>

<?php
    exit;
}

if (isset($_REQUEST['view_receipt_id'])) {
    $view_receipt_id = $bf->sanitize($_REQUEST['view_receipt_id'] ?? '');
    $receipt_id = '';
    $receipt_date = date('Y-m-d');
    $course_type = 'training';
    $student_id = '';
    $description = '';
    $payment_mode_ids = [];
    $bank_ids = [];
    $amounts = [];
    $total_amount = '';

    if (!empty($view_receipt_id)) {
        $receipt_list = $bf->getTableRecords($GLOBALS['payment_table'], 'id', $view_receipt_id);
        if (!empty($receipt_list)) {
            $receipt = $receipt_list[0];
            if (!empty($receipt['receipt_id'])) {
                $receipt_id = $receipt['receipt_id'];
            }
            if (!empty($receipt['receipt_date'])) {
                $receipt_date = $receipt['receipt_date'];
            }
            if (!empty($receipt['course_type'])) {
                $course_type = $receipt['course_type'];
            }
            if (!empty($receipt['student_id'])) {
                $student_id = $receipt['student_id'];
            }
            if (!empty($receipt['description'])) {
                $description = $receipt['description'];
            }
            if (!empty($receipt['payment_mode'])) {
                $payment_mode_ids = array_values(array_filter(array_map('trim', explode(',', $receipt['payment_mode']))));
            }
            if (!empty($receipt['bank'])) {
                $bank_ids = array_values(array_filter(array_map('trim', explode(',', $receipt['bank']))));
            }
            if (!empty($receipt['amount'])) {
                $amounts = array_values(array_filter(array_map('trim', explode(',', $receipt['amount']))));
            }
            if (!empty($receipt['total_amount'])) {
                $total_amount = $receipt['total_amount'];
            }
        }
    }

    $students_training = $bf->getTableRecords($GLOBALS['enrollment_table'], 'deleted', 0);
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

    $courses = [];
    $course_records = $bf->getTableRecords($GLOBALS['course_table'], 'deleted', 0);
    if (!empty($course_records)) {
        foreach ($course_records as $c) {
            $courses[$c['course_id']] = $c['course_name'];
        }
    }

    $student_lists_js = [
        'training' => [],
        'internship' => []
    ];
    foreach ($students_training as $row) {
        $course_name = $courses[$row['course_id']] ?? '';
        $student_lists_js['training'][] = [
            'id' => $row['enrollment_id'],
            'student_id' => $row['student_id'],
            'student_id_decrypted' => $bf->encode_decode('decrypt', $row['student_id']),
            'label' => $bf->encode_decode('decrypt', $row['enrollment_id']) . ' - ' . $row['student_name'] . ' - ' . $course_name
        ];
    }
    foreach ($students_internship as $row) {
        $course_name = $courses[$row['course_id']] ?? '';
        $student_lists_js['internship'][] = [
            'id' => $row['enrollment_internship_id'],
            'student_id' => $row['student_id'],
            'student_id_decrypted' => $bf->encode_decode('decrypt', $row['student_id']),
            'label' => $bf->encode_decode('decrypt', $row['enrollment_internship_id']) . ' - ' . $row['student_name'] . ' - ' . $course_name
        ];
    }

    $bank_map_json = json_encode($bank_map);
    $student_lists_json = json_encode($student_lists_js);

    ?>

    <div class="header">
        <h2><?php echo empty($view_receipt_id) ? 'New Receipt' : 'Update Receipt'; ?></h2>
    </div>

    <div class="module-section form-section">
        <form
            name="receipt_form"
            id="receipt_form"
            method="POST"
            enctype="multipart/form-data"
            onsubmit="event.preventDefault(); formSubmit('receipt_form', 'receipt_action.php', 'receipt.php', 'receipt');"
        >
            <input type="hidden" name="view_receipt_id" value="<?php echo $view_receipt_id; ?>">

            <div class="form-row">
                <div class="form-group col-4">
                    <label>Receipt Date *</label>
                    <input
                        type="date"
                        name="receipt_date"
                        id="receipt_date"
                        class="form-input"
                        value="<?php echo $receipt_date; ?>"
                        max="<?php echo date('Y-m-d'); ?>"
                    >
                    <span id="error-receipt_date" class="error-msg"></span>
                </div>

                <div class="form-group col-4">
                    <label>Course Type *</label>
                    <select name="course_type" id="course_type" class="form-input" onchange="onCourseTypeChange()">
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
                <button type="submit" class="btn-add"><?php echo empty($view_receipt_id) ? 'Add Receipt' : 'Update Receipt'; ?></button>
                <?php if (!empty($view_receipt_id)) { ?>
                    <a href="receipt.php" class="btn-add" style="background: #ef4444; font-size: 0.75rem;">Cancel</a>
                <?php } ?>
            </div>
        </form>
    </div>

    <script>
        const paymentModeOptions = `<?php echo addslashes($payment_mode_options); ?>`;
        const studentLists = <?php echo $student_lists_json; ?>;
        const bankMap = <?php echo $bank_map_json; ?>;
        let paymentRows = <?php echo json_encode($payment_rows); ?>;
        let selectedCourseType = '<?php echo $course_type; ?>';
        let selectedStudentId = '<?php echo $student_id; ?>';

        function populateStudentOptions() {
            const studentSelect = document.getElementById('student_id');
            const list = studentLists[selectedCourseType] || [];
            studentSelect.innerHTML = '<option value="">Select</option>';
            list.forEach(function(student) {
                const selected = (student.id === selectedStudentId || 
                                  student.student_id === selectedStudentId || 
                                  student.student_id_decrypted === selectedStudentId) ? 'selected' : '';
                studentSelect.innerHTML += `<option value="${student.id}" ${selected}>${student.label}</option>`;
            });
        }

        function onCourseTypeChange() {
            selectedCourseType = document.getElementById('course_type').value;
            selectedStudentId = '';
            populateStudentOptions();
        }

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
                            -
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
                    bankCell.innerHTML = '-';
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
            if (target.name === 'student_id') {
                selectedStudentId = target.value;
            }
        });

        // Execute immediately (page loaded via AJAX, DOMContentLoaded already fired)
        populateStudentOptions();
        renderPaymentRows();
        (function() {
            const courseTypeInput = document.getElementById('course_type');
            if (courseTypeInput) {
                courseTypeInput.value = selectedCourseType;
            }
        })();
    </script>

<?php }

if ($action == 'list') {
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
    $search = isset($_POST['search']) ? $_POST['search'] : '';
    $start = ($page - 1) * $limit;

    // Use getTableList for pagination but we need to handle student labels
    $result = $bf->getTableList($GLOBALS['payment_table'], ['payment_id', 'description'], $start, $limit, $search);
    $receipts = $result['data'];
    $total_records = $result['total_records'];
    $total_pages = ceil($total_records / $limit);

    if (empty($receipts)) { ?>
        <div class="table-responsive">
            <table><tr><td style="text-align:center">No receipts found.</td></tr></table>
        </div>
    <?php } else {
        ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Receipt No</th>
                        <th>Receipt Date</th>
                        <th>Course Type</th>
                        <th>Student</th>
                        <th>Total Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                foreach ($receipts as $receipt) {
                    $student_label = '';
                    if (!empty($receipt['student_id'])) {
                        $student_table = $receipt['course_type'] === 'internship' ? $GLOBALS['enrollment_internship_table'] : $GLOBALS['enrollment_table'];
                        $id_field = $receipt['course_type'] === 'internship' ? 'enrollment_internship_id' : 'enrollment_id';
                        $student_record = $bf->getTableRecords($student_table, $id_field, $receipt['enrollment_id']);
                        if (!empty($student_record)) {
                            $student_record = $student_record[0];
                            $student_label = $bf->encode_decode('decrypt', $student_record['student_id']) . ' - ' . $student_record['student_name'];
                        }
                    }
                ?>
                    <tr>
                        <td><span style="font-weight: 600; color: var(--primary);"><?php echo htmlspecialchars($receipt['payment_id']); ?></span></td>
                        <td><?php echo date('d-m-Y', strtotime($receipt['payment_date'])); ?></td>
                        <td><span class="status-badge" style="background: var(--primary-light); color: var(--primary);"><?php echo htmlspecialchars(ucfirst($receipt['course_type'])); ?></span></td>
                        <td><?php echo htmlspecialchars($student_label); ?></td>
                        <td><strong>₹<?php echo number_format($receipt['total_amount'], 2); ?></strong></td>
                        <td>
                            <div style="display:flex; gap:0.5rem; align-items:center;">
                                <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'receipt', PERMISSION_VIEW)): ?>
                                    <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;" onclick="window.open('reports/rpt_receipt_a5.php?receipt_id=<?php echo $receipt['id']; ?>', '_blank')">Print</button>
                                    
                                    <?php 
                                    if (!empty($student_record)) {
                                        $student_mobile = !empty($student_record['mobile_number']) ? preg_replace('/[^0-9]/', '', $student_record['mobile_number']) : '';
                                        if (strlen($student_mobile) == 10) {
                                            $student_mobile = '91' . $student_mobile;
                                        }

                                        // Fetch Course Name
                                        $course_data = $bf->getTableRecords($GLOBALS['course_table'], 'course_id', $student_record['course_id'] ?? '');
                                        $course_name = !empty($course_data) ? $course_data[0]['course_name'] : 'N/A';

                                        // Formulate itemized breakdown
                                        $payment_mode_ids = array_filter(array_map('trim', explode(',', $receipt['payment_mode'] ?? '')));
                                        $amounts = array_filter(array_map('trim', explode(',', $receipt['amount'] ?? '')));
                                        
                                        $pm_list_str = "";
                                        if (!empty($payment_mode_ids)) {
                                            foreach ($payment_mode_ids as $index => $pm_id) {
                                                $pm_rec = $bf->getTableRecords($GLOBALS['payment_mode_table'], 'payment_mode_id', $pm_id);
                                                $pm_name = !empty($pm_rec) ? $pm_rec[0]['payment_mode_name'] : 'Payment';
                                                $pm_amount = $amounts[$index] ?? 0;
                                                $pm_list_str .= "*   " . $pm_name . ":* Rs. " . number_format((float)$pm_amount, 2) . "\n";
                                            }
                                        }

                                        $wa_text = "Dear *" . ($student_record['student_name'] ?? 'Student') . "*, your payment has been received successfully! \n\n"
                                                 . "📝 *RECEIPT DETAILS*\n"
                                                 . "*   *Receipt No:* " . $receipt['payment_id'] . "\n"
                                                 . "*   *Date:* " . date('d-m-Y', strtotime($receipt['payment_date'])) . "\n"
                                                 . "*   *Course:* " . $course_name . " (" . ucfirst($receipt['course_type']) . ")\n\n"
                                                 . "💰 *PAYMENT DETAILS*\n"
                                                 . rtrim($pm_list_str, "\n") . "\n"
                                                 . "*   *Total Paid:* Rs. " . number_format((float)($receipt['total_amount'] ?? 0), 2) . "\n\n"
                                                 . "📄 *View/Download PDF Receipt:*\n"
                                                 . ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . "/priya/training_center/reports/rpt_receipt_a5.php?receipt_id=" . $receipt['id'] . "\n\n"
                                                 . "Thank you! 🙏";

                                        $wa_link = "https://api.whatsapp.com/send?phone=" . $student_mobile . "&text=" . urlencode($wa_text);
                                        ?>
                                        <a href="<?php echo htmlspecialchars($wa_link); ?>" target="_blank" class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; background: #25d366; text-decoration: none; color: white; display: inline-flex; align-items: center; gap: 0.25rem;">
                                            WhatsApp
                                        </a>
                                        <?php
                                    }
                                    ?>
                                <?php endif; ?>
                                <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'receipt', PERMISSION_DELETE)): ?>
                                    <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; background: #ef4444;" onclick="deleteRecord('receipt', '<?php echo $receipt['id']; ?>')">Delete</button>
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
                <button class="page-btn" <?php echo ($page <= 1) ? 'disabled' : ''; ?> onclick="loadData('receipt', <?php echo $page - 1; ?>, $('#receipt_limit').val(), $('#receipt_search').val())">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <?php 
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $start_page + 4);
                if ($end_page - $start_page < 4) $start_page = max(1, $end_page - 4);
                for ($i = $start_page; $i <= $end_page; $i++) { ?>
                    <button class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>" onclick="loadData('receipt', <?php echo $i; ?>, $('#receipt_limit').val(), $('#receipt_search').val())">
                        <?php echo $i; ?>
                    </button>
                <?php } ?>
                <button class="page-btn" <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?> onclick="loadData('receipt', <?php echo $page + 1; ?>, $('#receipt_limit').val(), $('#receipt_search').val())">
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
        $bf->UpdateSQL($GLOBALS['payment_table'], ['deleted' => 1, 'updated_date_time' => date('Y-m-d H:i:s')], 'id = :id', [':id' => $id]);
    }
    echo 'Success';
}
?>