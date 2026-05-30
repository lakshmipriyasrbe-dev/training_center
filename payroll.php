<?php require_once 'common_file.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Management - <?php echo get_company_name(); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .payroll-grid-container {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .filter-row {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        .filter-group {
            flex: 0 0 200px;
        }
        .filter-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-muted);
            font-size: 0.875rem;
        }
        .filter-group select.form-input {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23858796'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1.25rem;
            padding-right: 2.5rem !important;
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 0.75rem !important;
            background-color: #f8fafc !important;
            color: var(--text) !important;
            font-weight: 600 !important;
            cursor: pointer;
            height: 45px;
            transition: all 0.2s;
        }
        .filter-group select.form-input:focus {
            border-color: var(--primary) !important;
            background-color: #fff !important;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1) !important;
            outline: none;
        }
        .badge-success {
            background: #dcfce7;
            color: #166534;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .total-summary {
            margin-top: 1.5rem;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 0.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .summary-item {
            text-align: center;
        }
        .summary-label {
            display: block;
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
        }
        .summary-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary);
        }
        .btn-process {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-process:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content update_content">
        <div class="header">
            <h2>Payroll Management</h2>
        </div>

        <div class="module-section">
            <div class="section-title">
                Payroll History
                <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'payroll', PERMISSION_ADD)): ?>
                    <button class="btn-add" onclick="toggleView('form')">Add New Payroll</button>
                <?php endif; ?>
            </div>
            <div class="list-controls">
                <div class="entries-control">
                    Show 
                    <select id="payroll_limit" onchange="loadData('payroll', 1, this.value, $('#payroll_search').val())">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    entries
                </div>
                <div class="search-control">
                    <i class="fas fa-search"></i>
                    <input type="text" id="payroll_search" placeholder="Search history..." onkeyup="loadData('payroll', 1, $('#payroll_limit').val(), this.value)">
                </div>
            </div>

            <div id="payroll_list">
                <p style="text-align:center; padding: 2rem; color: var(--text-muted);">Loading History...</p>
            </div>
        </div>
    </div>

    <!-- Payroll Processing Section (Form View) -->
    <div class="main-content new_content" style="display: none;">
        <div class="header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2 style="margin:0;">Process Monthly Salary</h2>
            <button class="btn-add" style="background: #64748b;" onclick="toggleView('list')">Back to History</button>
        </div>

        <div class="payroll-grid-container">
            <div class="section-title" style="margin-bottom: 1.5rem; display: flex; justify-content: space-between;">
                <span>Process Payroll</span>
                <span id="total_days_display" style="font-size: 1rem; color: var(--primary);">Total Days: 0</span>
            </div>
            
            <div class="filter-row">
                <div class="filter-group">
                    <label>Select Month</label>
                    <select id="pay_month" class="form-input" onchange="loadPayrollGrid()">
                        <?php
                        $currentMonth = (int)date('m');
                        for ($m=1; $m<=12; $m++) {
                            $monthName = date("F", mktime(0, 0, 0, $m, 10));
                            $selected = ($m == $currentMonth) ? 'selected' : '';
                            echo "<option value='".str_pad($m, 2, '0', STR_PAD_LEFT)."' $selected>$monthName</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Select Year</label>
                    <select id="pay_year" class="form-input" onchange="updateMonthRestriction(); loadPayrollGrid();">
                        <?php
                        $realCurrentYear = (int)date('Y');
                        for ($y=$realCurrentYear-2; $y<=$realCurrentYear; $y++) {
                            $is_selected = ($y == 2026) ? 'selected' : '';
                            echo "<option value='$y' $is_selected>$y</option>";
                        }
                        ?>
                    </select>
                </div>
                <div style="flex: 2;"></div>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>S.no</th>
                            <th>Staff Name</th>
                            <th>Role</th>
                            <th>Monthly Salary</th>
                            <th>Per Day</th>
                            <th>CL</th>
                            <th>LOP Days</th>
                            <th>Deduction</th>
                            <th>Incentive</th>
                            <th>Total Salary</th>
                        </tr>
                    </thead>
                    <tbody id="payroll_grid_body">
                        <!-- Loaded via AJAX -->
                    </tbody>
                    <tfoot>
                        <tr style="font-weight: bold; background: #f8fafc;">
                            <td colspan="3" style="text-align: right;">Total</td>
                            <td id="total_monthly">0.00</td>
                            <td></td>
                            <td id="total_cl">0</td>
                            <td id="total_lop">0</td>
                            <td id="total_deduction">0.00</td>
                            <td id="total_incentive">0.00</td>
                            <td id="total_net" style="color: var(--primary); font-size: 1.1rem;">0.00</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="total-summary">
                <div style="display: flex; gap: 2rem;">
                    <div class="summary-item">
                        <span class="summary-label">Processed Staff</span>
                        <span class="summary-value" id="count_staff">0</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Total Payout</span>
                        <span class="summary-value" id="summary_net">₹0.00</span>
                    </div>
                </div>
                <div style="display: flex; gap: 1rem;">
                    <button class="btn-process" onclick="processPayroll()">
                        <i class="fas fa-check-circle"></i> Process & Save Payroll
                    </button>
                    <button id="btn_print_monthly" class="btn-process" style="background: #10b981; display: none;" onclick="printMonthlyPayroll()">
                        <i class="fas fa-print"></i> Print Monthly Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            loadData('payroll');
        });

        function viewPayroll(staff_id) {
            $('.update_content').hide();
            $('.new_content').show().html('<p style="text-align:center; padding: 2rem;">Loading details...</p>');
            
            $.ajax({
                url: 'payroll_action.php',
                type: 'POST',
                data: { action: 'view', staff_id: staff_id },
                success: function(res) {
                    $('.new_content').html(res);
                }
            });
        }

        function toggleView(view) {
            if (view === 'form') {
                $('.update_content').hide();
                $('.new_content').show();
                updateMonthRestriction();
                loadPayrollGrid();
            } else {
                $('.new_content').hide();
                $('.update_content').show();
                loadData('payroll');
            }
        }

        function updateMonthRestriction() {
            const selectedYear = parseInt($('#pay_year').val());
            const currentYear = new Date().getFullYear();
            const currentMonth = new Date().getMonth() + 1; // 1-12
            
            $('#pay_month option').each(function() {
                const monthVal = parseInt($(this).val());
                if (selectedYear > currentYear) {
                    $(this).prop('disabled', true);
                } else if (selectedYear === currentYear) {
                    if (monthVal > currentMonth) {
                        $(this).prop('disabled', true);
                    } else {
                        $(this).prop('disabled', false);
                    }
                } else {
                    $(this).prop('disabled', false);
                }
            });
            
            if ($('#pay_month option:selected').prop('disabled')) {
                $('#pay_month option:not([disabled]):last').prop('selected', true);
            }
        }

        function loadPayrollGrid() {
            const month = $('#pay_month').val();
            const year = $('#pay_year').val();
            const daysInMonth = new Date(year, month, 0).getDate();
            $('#total_days_display').text('Total Days: ' + daysInMonth);
            
            $('#payroll_grid_body').html('<tr><td colspan="10" style="text-align:center; padding: 2rem;">Loading Staff Data...</td></tr>');
            
            $.ajax({
                url: 'payroll_action.php',
                type: 'POST',
                data: { action: 'get_payroll_grid', month: month, year: year },
                success: function(res) {
                    $('#payroll_grid_body').html(res);
                    calculateGrandTotal();
                    
                    if ($('#month_any_paid').val() === '1') {
                        $('#btn_print_monthly').show();
                    } else {
                        $('#btn_print_monthly').hide();
                    }
                }
            });
        }

        function calculateGrandTotal() {
            let totalMonthly = 0;
            let totalCL = 0;
            let totalLOP = 0;
            let totalDeduction = 0;
            let totalIncentive = 0;
            let totalNet = 0;
            let staffCount = 0;

            $('.payroll-row').each(function() {
                const $row = $(this);
                const isUnprocessed = $row.find('.lop-val').length > 0;
                
                if (isUnprocessed) {
                    totalMonthly += parseFloat($row.find('.monthly-salary').text()) || 0;
                    totalCL += parseFloat($row.find('.cl-display').text()) || 0;
                    const lopVal = parseFloat($row.find('.lop-val').val()) || 0;
                    totalLOP += lopVal;
                    totalDeduction += parseFloat($row.find('.total-deduction').text()) || 0;
                    totalIncentive += parseFloat($row.find('.incentive-val').val()) || 0;
                    totalNet += parseFloat($row.find('.net-salary-display').text()) || 0;
                    staffCount++;
                }
            });

            $('#total_monthly').text(totalMonthly.toFixed(2));
            $('#total_cl').text(totalCL);
            $('#total_lop').text(totalLOP);
            $('#total_deduction').text(totalDeduction.toFixed(2));
            $('#total_incentive').text(totalIncentive.toFixed(2));
            $('#total_net').text(totalNet.toFixed(2));
            $('#count_staff').text(staffCount);
            $('#summary_net').text('₹' + totalNet.toFixed(2));
        }

        function processPayroll() {
            const month = $('#pay_month').val();
            const year = $('#pay_year').val();
            const payrollData = [];
            
            $('.payroll-row').each(function() {
                const $row = $(this);
                if ($row.find('.lop-val').length > 0) {
                    const lopVal = parseFloat($row.find('.lop-val').val()) || 0;
                    payrollData.push({
                        payroll_db_id: $row.data('payroll-db-id') || '',
                        staff_id: $row.data('staff-id'),
                        monthly_salary: parseFloat($row.find('.monthly-salary').text()),
                        per_day_salary: parseFloat($row.find('.per-day-salary').text()),
                        cl_days: parseFloat($row.find('.cl-display').text()),
                        lop_days: lopVal,
                        total_deduction: parseFloat($row.find('.total-deduction').text()),
                        incentive_amount: parseFloat($row.find('.incentive-val').val()),
                        total_references: parseInt($row.find('.ref-count-val').val()) || 0,
                        net_salary: parseFloat($row.find('.net-salary-display').text())
                    });
                }
            });

            if (payrollData.length === 0) {
                alert("No new payroll records to process.");
                return;
            }

            console.log("Processing Payroll Data:", payrollData);

            if (!confirm(`Are you sure you want to process payroll for ${payrollData.length} staff members?`)) return;

            $.ajax({
                url: 'payroll_action.php',
                type: 'POST',
                data: { 
                    action: 'save_payroll', 
                    month: month, 
                    year: year, 
                    payroll_data: JSON.stringify(payrollData) 
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        alert(res.message);
                        toggleView('list');
                    } else {
                        alert("Error: " + res.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    alert("An error occurred while processing payroll. Please check the console for details.");
                }
            });
        }

        function printMonthlyPayroll() {
            const month = $('#pay_month').val();
            const year = $('#pay_year').val();
            window.open(`reports/rpt_payroll_permonth.php?month=${month}&year=${year}`, '_blank');
        }

        function deletePayroll(id) {
            if (confirm('Are you sure you want to delete this payroll record?')) {
                $.ajax({
                    url: 'payroll_action.php',
                    type: 'POST',
                    data: { action: 'delete', id: id },
                    dataType: 'json',
                    success: function(res) {
                        alert(res.message);
                        loadData('payroll');
                    }
                });
            }
        }

        function recalculateRow($input) {
            const $row = $input.closest('.payroll-row');
            const monthlySalary = parseFloat($row.find('.monthly-salary').text()) || 0;
            const perDaySalary = parseFloat($row.find('.per-day-salary').text()) || 0;
            const lopVal = parseFloat($input.val()) || 0;
            const incentiveVal = parseFloat($row.find('.incentive-val').val()) || 0;

            const deduction = Math.round((perDaySalary * lopVal) * 100) / 100;
            const netSalary = Math.round((monthlySalary - deduction + incentiveVal) * 100) / 100;

            $row.find('.total-deduction').text(deduction.toFixed(2));
            $row.find('.net-salary-display').text(netSalary.toFixed(2));

            calculateGrandTotal();
        }

        function editPayroll(id) {
            $('.update_content').hide();
            $('.new_content').show().html('<p style="text-align:center; padding: 2rem;">Loading details...</p>');
            
            $.ajax({
                url: 'payroll_action.php',
                type: 'POST',
                data: { action: 'edit_view', id: id },
                success: function(res) {
                    $('.new_content').html(res);
                }
            });
        }

        function recalculateEditPayroll() {
            const monthlySalary = parseFloat($('#edit_monthly_salary').val()) || 0;
            const perDaySalary = parseFloat($('#edit_per_day_salary').val()) || 0;
            const lopVal = parseFloat($('#edit_lop_days').val()) || 0;
            const incentiveVal = parseFloat($('#edit_incentive_val').val()) || 0;

            const deduction = Math.round((perDaySalary * lopVal) * 100) / 100;
            const netSalary = Math.round((monthlySalary - deduction + incentiveVal) * 100) / 100;

            $('#edit_deduction_display').text('- ₹' + deduction.toFixed(2));
            $('#edit_net_salary_display').text('₹' + netSalary.toFixed(2));
            
            $('#submit_deduction').val(deduction);
            $('#submit_net_salary').val(netSalary);
        }

        function updatePayroll(id) {
            const lop_days = parseFloat($('#edit_lop_days').val()) || 0;
            const deduction = parseFloat($('#submit_deduction').val()) || 0;
            const net_salary = parseFloat($('#submit_net_salary').val()) || 0;

            $.ajax({
                url: 'payroll_action.php',
                type: 'POST',
                data: {
                    action: 'update_payroll',
                    id: id,
                    lop_days: lop_days,
                    total_deduction: deduction,
                    net_salary: net_salary
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        alert(res.message);
                        toggleView('list');
                    } else {
                        alert("Error: " + res.message);
                    }
                }
            });
        }
    </script>
    <script src="main/js/script.js"></script>
</body>
</html>
