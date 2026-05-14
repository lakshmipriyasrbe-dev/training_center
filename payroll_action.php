<?php require_once 'common_file.php'; 
if ($user_role != 'admin') { header("Location: dashboard.php"); exit(); }

$action = $_REQUEST['action'] ?? '';

if ($action == 'get_payroll_grid') {
    $month = $_REQUEST['month'] ?? date('m');
    $year = $_REQUEST['year'] ?? date('Y');
    
    $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    
    // Fetch all active staff
    $staff_query = "SELECT s.*, r.role_name FROM " . $GLOBALS['staff_table'] . " s 
                    LEFT JOIN " . $GLOBALS['role_table'] . " r ON s.role_id = r.id 
                    WHERE s.deleted = 0 ORDER BY s.staff_name ASC";
    $staff_list = $bf->getQueryRecords($staff_query);
    
    $html = "";
    $sno = 1;
    
    if (!empty($staff_list)) {
        foreach ($staff_list as $staff) {
            $pk_id = $staff['id'];
            $staff_id_str = $staff['staff_id'];
            $monthly_salary = $staff['salary'] ?? 0;
            $per_day_salary = $monthly_salary > 0 ? round($monthly_salary / $days_in_month, 2) : 0;
            
            // Calculate Incentives
            $ref_count = 0;
            
            // Enrollment references
            $enroll_ref = $bf->getQueryRecords("SELECT COUNT(*) as total FROM " . $GLOBALS['enrollment_table'] . " 
                                                WHERE referred_staff_id = '$staff_id_str' 
                                                AND MONTH(created_date_time) = '$month' 
                                                AND YEAR(created_date_time) = '$year' 
                                                AND deleted = 0");
            $ref_count += $enroll_ref[0]['total'] ?? 0;
            
            // Internship references
            $intern_ref = $bf->getQueryRecords("SELECT COUNT(*) as total FROM " . $GLOBALS['enrollment_internship_table'] . " 
                                                WHERE referred_staff_id = '$staff_id_str' 
                                                AND MONTH(created_date_time) = '$month' 
                                                AND YEAR(created_date_time) = '$year' 
                                                AND deleted = 0");
            $ref_count += $intern_ref[0]['total'] ?? 0;
            
            $incentive = $ref_count * 1000;
            
            // Check if already paid
            $check_paid = $bf->getQueryRecords("SELECT id FROM " . $GLOBALS['payroll_table'] . " 
                                               WHERE staff_id = '$pk_id' 
                                               AND month = '$month' 
                                               AND year = '$year' 
                                               AND deleted = 0");
            
            $is_paid = !empty($check_paid);
            $row_style = $is_paid ? "background-color: #f1f5f9; opacity: 0.8;" : "";
            $status_label = $is_paid ? "<span class='badge badge-success'>Paid</span>" : "";
            
            $decrypted_staff_id = $bf->encode_decode('decrypt', $staff_id_str);
            
            $html .= "<tr style='$row_style' class='payroll-row' data-staff-id='$pk_id'>
                        <td>$sno</td>
                        <td>{$staff['staff_name']} ($decrypted_staff_id) $status_label</td>
                        <td>{$staff['role_name']}</td>
                        <td class='monthly-salary'>$monthly_salary</td>
                        <td class='per-day-salary'>$per_day_salary</td>
                        <td class='cl-display'>0</td>
                        <td>";
            
            if ($is_paid) {
                $html .= "Processed";
            } else {
                $html .= "<input type='number' name='lop_input' class='form-input lop-input' style='width: 70px;' min='0' max='$days_in_month' value='0' onchange='calculateRow(this)'>";
            }
            
            $html .= "</td>
                        <td class='total-deduction'>0</td>
                        <td class='incentive-display' title='$ref_count References'>$incentive</td>
                        <td class='net-salary-display'>".($monthly_salary + $incentive)."</td>
                        <input type='hidden' class='staff-id-val' value='$pk_id'>
                        <input type='hidden' class='incentive-val' value='$incentive'>
                        <input type='hidden' class='ref-count-val' value='$ref_count'>
                      </tr>";
            $sno++;
        }
    } else {
        $html = "<tr><td colspan='10' style='text-align:center;'>No staff found</td></tr>";
    }
    
    echo $html;
    exit();
}

if ($action == 'save_payroll') {
    $month = $_POST['month'];
    $year = $_POST['year'];
    $payroll_data = $_POST['payroll_data']; // JSON string from frontend
    
    $data_arr = json_decode($payroll_data, true);
    $success_count = 0;
    
    foreach ($data_arr as $row) {
        $staff_id = $row['staff_id'];
        
        // Final duplicate check
        $check = $bf->getQueryRecords("SELECT id FROM " . $GLOBALS['payroll_table'] . " 
                                      WHERE staff_id = '$staff_id' AND month = '$month' AND year = '$year' AND deleted = 0");
        if (!empty($check)) continue;
        
        $insert_data = [
            'staff_id' => $staff_id,
            'month' => $month,
            'year' => $year,
            'monthly_salary' => $row['monthly_salary'],
            'per_day_salary' => $row['per_day_salary'],
            'cl_days' => $row['cl_days'],
            'lop_days' => $row['lop_days'],
            'total_deduction' => $row['total_deduction'],
            'incentive_amount' => $row['incentive_amount'],
            'total_references' => $row['total_references'],
            'net_salary' => $row['net_salary'],
            'payment_date' => date('Y-m-d'),
            'created_date_time' => $GLOBALS['create_date_time_label']
        ];
        
        $res = $bf->InsertSQL($GLOBALS['payroll_table'], $insert_data, 'payroll_id', 'payroll_number', 'payroll');
        if ($res && is_numeric($res)) {
            $success_count++;
        } else {
            $errors[] = "Failed to insert for staff ID $staff_id: " . $res;
        }
    }
    
    if ($success_count > 0) {
        echo json_encode(['status' => 'success', 'message' => "$success_count records processed successfully." . (!empty($errors) ? " (Some errors occurred)" : "")]);
    } else {
        echo json_encode(['status' => 'error', 'message' => "Failed to process payroll. " . implode(", ", $errors ?? [])]);
    }
    exit();
}

if ($action == 'list') {
    $page = $_REQUEST['page'] ?? 1;
    $limit = $_REQUEST['limit'] ?? 10;
    $search = $_REQUEST['search'] ?? '';
    $start = ($page - 1) * $limit;
    
    $where = "p.deleted = 0";
    if (!empty($search)) {
        $where .= " AND (s.staff_name LIKE '%$search%' OR p.payroll_id LIKE '%$search%')";
    }
    
    $query = "SELECT p.*, s.staff_name, r.role_name 
              FROM " . $GLOBALS['payroll_table'] . " p 
              JOIN " . $GLOBALS['staff_table'] . " s ON p.staff_id = s.id 
              LEFT JOIN " . $GLOBALS['role_table'] . " r ON s.role_id = r.id 
              WHERE $where ORDER BY p.id DESC";
    
    $result = $bf->getPaginatedResults($query, [], $start, $limit);
    $records = $result['data'];
    $total_records = $result['total_records'];
    $total_pages = ceil($total_records / $limit);
    
    $html = '<div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>S.no</th>
                            <th>Payroll ID</th>
                            <th>Staff Name</th>
                            <th>Month/Year</th>
                            <th>Salary</th>
                            <th>LOP/CL</th>
                            <th>Incentive</th>
                            <th>Net Paid</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>';
    
    if (!empty($records)) {
        $sno = ($page - 1) * $limit + 1;
        foreach ($records as $row) {
            $month_name = date("F", mktime(0, 0, 0, $row['month'], 10));
            $display_payroll_id = $bf->encode_decode('decrypt', $row['payroll_id']);
            $html .= '<tr>
                        <td>' . $sno++ . '</td>
                        <td>' . $display_payroll_id . '</td>
                        <td>' . $row['staff_name'] . '</td>
                        <td>' . $month_name . ' ' . $row['year'] . '</td>
                        <td>₹' . $row['monthly_salary'] . '</td>
                        <td>' . $row['lop_days'] . ' / ' . $row['cl_days'] . '</td>
                        <td>₹' . $row['incentive_amount'] . '</td>
                        <td><strong>₹' . $row['net_salary'] . '</strong></td>
                        <td>' . date('d-m-Y', strtotime($row['payment_date'])) . '</td>
                        <td>
                            <button class="btn-add" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;" onclick="viewPayroll(' . $row['id'] . ')"><i class="fas fa-eye"></i></button>
                            <a href="payroll_print.php?id=' . $row['id'] . '" target="_blank" class="btn-add" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: #10b981; text-decoration: none;"><i class="fas fa-print"></i></a>
                            <button class="btn-delete" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;" onclick="deletePayroll(' . $row['id'] . ')"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>';
        }
    } else {
        $html .= '<tr><td colspan="10" style="text-align:center;">No records found</td></tr>';
    }
    
    $html .= '</tbody></table></div>';
    
    // Pagination
    $html .= '<div class="pagination-container">
                <div class="pagination-info">Showing ' . count($records) . ' of ' . $total_records . ' entries</div>
                <div class="pagination-buttons">';
    
    for ($i = 1; $i <= $total_pages; $i++) {
        $active = ($i == $page) ? 'active' : '';
        $html .= '<button class="' . $active . '" onclick="loadData(\'payroll\', ' . $i . ', ' . $limit . ', \'' . $search . '\')">' . $i . '</button>';
    }
    
    $html .= '</div></div>';
    
    echo $html;
    exit();
}

if ($action == 'view') {
    $id = $_REQUEST['id'];
    $query = "SELECT p.*, s.staff_name, s.staff_id as s_id, r.role_name 
              FROM " . $GLOBALS['payroll_table'] . " p 
              JOIN " . $GLOBALS['staff_table'] . " s ON p.staff_id = s.id 
              LEFT JOIN " . $GLOBALS['role_table'] . " r ON s.role_id = r.id 
              WHERE p.id = '$id'";
    $data = $bf->getQueryRecords($query);
    
    if (empty($data)) {
        echo "Record not found";
        exit();
    }
    
    $row = $data[0];
    $month_name = date("F", mktime(0, 0, 0, $row['month'], 10));
    $p_id = $bf->encode_decode('decrypt', $row['payroll_id']);
    $s_id = $bf->encode_decode('decrypt', $row['s_id']);
    
    ?>
    <div class="header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2 style="margin:0;">View Payroll Details</h2>
        <div style="display: flex; gap: 1rem;">
            <a href="payroll_print.php?id=<?php echo $id; ?>" target="_blank" class="btn-add" style="background: #10b981; text-decoration: none;">
                <i class="fas fa-print"></i> Print PDF
            </a>
            <button class="btn-add" style="background: #64748b;" onclick="toggleView('list')">Back to History</button>
        </div>
    </div>

    <div class="module-section">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <div class="card" style="padding: 1.5rem; border: 1px solid var(--border); border-radius: 1rem;">
                <h3 style="margin-top:0; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem;">Staff Information</h3>
                <p><strong>Name:</strong> <?php echo $row['staff_name']; ?></p>
                <p><strong>Staff ID:</strong> <?php echo $s_id; ?></p>
                <p><strong>Role:</strong> <?php echo $row['role_name']; ?></p>
            </div>
            <div class="card" style="padding: 1.5rem; border: 1px solid var(--border); border-radius: 1rem;">
                <h3 style="margin-top:0; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem;">Payment Information</h3>
                <p><strong>Payroll ID:</strong> <?php echo $p_id; ?></p>
                <p><strong>Month/Year:</strong> <?php echo $month_name . ' ' . $row['year']; ?></p>
                <p><strong>Payment Date:</strong> <?php echo date('d-m-Y', strtotime($row['payment_date'])); ?></p>
            </div>
        </div>

        <div class="table-responsive" style="margin-top: 2rem;">
            <table class="table">
                <thead style="background: #f8fafc;">
                    <tr>
                        <th>Description</th>
                        <th style="text-align: right;">Amount / Days</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Monthly Salary</td>
                        <td style="text-align: right;">₹<?php echo number_format($row['monthly_salary'], 2); ?></td>
                    </tr>
                    <tr>
                        <td>Per Day Salary</td>
                        <td style="text-align: right;">₹<?php echo number_format($row['per_day_salary'], 2); ?></td>
                    </tr>
                    <tr>
                        <td>CL Taken</td>
                        <td style="text-align: right;"><?php echo $row['cl_days']; ?> Day(s)</td>
                    </tr>
                    <tr>
                        <td>LOP Days (Deducted)</td>
                        <td style="text-align: right;"><?php echo $row['lop_days']; ?> Day(s)</td>
                    </tr>
                    <tr style="color: #ef4444;">
                        <td>Total Deduction</td>
                        <td style="text-align: right;">- ₹<?php echo number_format($row['total_deduction'], 2); ?></td>
                    </tr>
                    <tr style="color: #10b981;">
                        <td>Incentives (<?php echo $row['total_references']; ?> References)</td>
                        <td style="text-align: right;">+ ₹<?php echo number_format($row['incentive_amount'], 2); ?></td>
                    </tr>
                    <tr style="font-size: 1.25rem; font-weight: 700; background: #f0f9ff;">
                        <td>Net Paid Amount</td>
                        <td style="text-align: right; color: var(--primary);">₹<?php echo number_format($row['net_salary'], 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php
    exit();
}

if ($action == 'delete') {
    $id = $_REQUEST['id'];
    $data = ['deleted' => 1, 'updated_date_time' => $GLOBALS['create_date_time_label']];
    if ($bf->UpdateSQL($GLOBALS['payroll_table'], $data, "id = :id", [":id" => $id])) {
        echo json_encode(['status' => 'success', 'message' => 'Record deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete record']);
    }
    exit();
}
?>
