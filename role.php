<?php 
require_once 'common_file.php'; 

// Restrict access to Admins and Management only
if ($user_role != 'admin' && !$is_management) { 
    header("Location: dashboard.php"); 
    exit(); 
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$edit_role_id = $_POST['role_id'] ?? $_GET['role_id'] ?? $_GET['id'] ?? '';

$role_name = '';
$description = '';

// Load data if editing
if ($action === 'edit' && !empty($edit_role_id)) {
    $role_data = $bf->getTableRecords($GLOBALS['role_table'], 'role_id', $edit_role_id);
    if (!empty($role_data)) {
        $role_name = $role_data[0]['role_name'];
        $description = $role_data[0]['description'];
    } else {
        header("Location: role.php");
        exit();
    }
}

// Fetch all active companies
$companies = $bf->getTableRecords($GLOBALS['company_table']);

// Fetch existing permissions for edit prefill
$existing_permissions = [];
if (!empty($edit_role_id)) {
    try {
        $table = $GLOBALS['role_permissions_table'] ?? 'tc_role_permissions';
        $stmt = $bf->con->prepare("
            SELECT company_id, permission_page, permission_action 
            FROM $table 
            WHERE role_id = :role_id
        ");
        $stmt->execute([':role_id' => $edit_role_id]);
        $perms = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($perms as $p) {
            $existing_permissions[$p['company_id']][$p['permission_page']] = explode('$$', $p['permission_action']);
        }
    } catch (PDOException $e) {
        // Fallback
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo empty($action) ? 'Role Management' : (ucfirst($action) . ' Role'); ?> - <?php echo get_company_name(); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Custom Premium Accordion styles */
        .company-accordion {
            margin-top: 1.5rem;
            width: 100%;
        }
        
        .accordion-item {
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            margin-bottom: 0.75rem;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            transition: all 0.2s ease;
        }
        
        .accordion-item:hover {
            border-color: var(--primary);
            box-shadow: 0 4px 8px rgba(0,0,0,0.04);
        }
        
        .accordion-header {
            background: #f8fafc;
            padding: 1rem 1.25rem;
            font-weight: 700;
            color: var(--primary);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            user-select: none;
            border-bottom: 1px solid transparent;
            font-size: 0.95rem;
        }
        
        .accordion-header.active {
            border-bottom-color: var(--border);
            background: #f0f9ff;
        }
        
        .accordion-content {
            display: none;
            padding: 1.25rem;
        }
        
        .accordion-arrow {
            transition: transform 0.3s ease;
            font-size: 0.85rem;
        }
        
        .accordion-arrow.rotate {
            transform: rotate(180deg);
        }
        
        .permission-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--primary);
        }
        
        .badge-info {
            background: var(--primary-light);
            color: var(--primary);
            padding: 0.2rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            margin-left: 0.5rem;
        }
        
        /* Table enhancements */
        .permission-table {
            min-width: 100% !important;
            margin-bottom: 0 !important;
        }
        
        .permission-table th {
            padding: 0.75rem 1rem;
            font-size: 0.8rem;
        }
        
        .permission-table td {
            padding: 0.75rem 1rem;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <?php if (empty($action)): ?>
            <!-- LIST ROLES VIEW -->
            <div class="header">
                <h2>Role & Permission Management</h2>
            </div>

            <div class="module-section">
                <div class="section-title">
                    Available Roles
                    <button class="btn-add" onclick="submitRoleAction('add')">Add New Role</button>
                </div>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 80px;">Sno</th>
                                <th>Role Name</th>
                                <th>Description</th>
                                <th style="width: 280px; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $roles = $bf->getTableRecords($GLOBALS['role_table']);
                            if (empty($roles)) {
                                echo '<tr><td colspan="4" style="text-align:center">No roles found.</td></tr>';
                            } else {
                                $sno = 1;
                                foreach ($roles as $r) { 
                                    if ($r['deleted'] == 1) continue;
                                    ?>
                                    <tr>
                                        <td><?php echo $sno++; ?></td>
                                        <td>
                                            <strong style="color: var(--primary); text-transform: uppercase;">
                                                <?php echo htmlspecialchars($r['role_name']); ?>
                                            </strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($r['description']); ?></td>
                                        <td style="text-align: center;">
                                            <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                                <button class="btn-add" style="padding: 0.3rem 0.8rem; font-size: 0.8rem; background: var(--primary);" onclick="submitRoleAction('edit', '<?php echo $r['role_id']; ?>')">
                                                    <i class="fas fa-key"></i> Edit Permissions
                                                </button>
                                                <?php 
                                                $relations = [
                                                    ['table' => $GLOBALS['staff_table'], 'column' => 'role_id', 'value' => $r['role_id'], 'label' => 'Staff'],
                                                    ['table' => $GLOBALS['user_table'], 'column' => 'role', 'value' => $r['role_name'], 'label' => 'User']
                                                ];
                                                $linked = $bf->isRecordLinked($relations);
                                                if ($linked) { ?>
                                                    <button class="btn-add" style="padding: 0.3rem 0.8rem; font-size: 0.8rem; background: #94a3b8; cursor: not-allowed;" title="Cannot delete: Linked with <?php echo $linked; ?> table" onclick="alert('Cannot delete: this role is linked with <?php echo $linked; ?>. Please delete linked <?php echo strtolower($linked); ?> records first.')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                <?php } else { ?>
                                                    <button class="btn-add" style="padding: 0.3rem 0.8rem; font-size: 0.8rem; background: #ef4444;" onclick="deleteRole('<?php echo $r['role_id']; ?>')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                <?php } ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php 
                                }
                            } 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        <?php else: ?>
            <!-- ADD/EDIT ROLE VIEW -->
            <div class="header">
                <h2><?php echo ($action === 'add') ? 'Create New Role' : 'Edit Role Permissions'; ?></h2>
            </div>

            <div class="module-section">
                <form id="rolePermissionForm">
                    <input type="hidden" name="action" value="<?php echo htmlspecialchars($action); ?>">
                    <input type="hidden" name="role_id" value="<?php echo htmlspecialchars($edit_role_id); ?>">
                    
                    <div class="form-row">
                        <div class="form-group col-6">
                            <label>Role Name *</label>
                            <input type="text" name="role_name" placeholder="e.g. Supervisor" value="<?php echo htmlspecialchars($role_name); ?>" required <?php echo ($action === 'edit' && strtolower($role_name) === 'admin') ? 'readonly style="background:#f1f5f9; cursor:not-allowed;"' : ''; ?>>
                        </div>
                        <div class="form-group col-6">
                            <label>Description</label>
                            <input type="text" name="description" placeholder="Describe this role..." value="<?php echo htmlspecialchars($description); ?>">
                        </div>
                    </div>

                    <h3 style="margin-top: 2rem; color: var(--primary); font-size: 1.1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">
                        Company-Wise Permission Setup
                    </h3>
                    
                    <?php if (empty($companies)): ?>
                        <p style="padding: 1rem; color: #ef4444; background: #fef2f2; border-radius: 0.35rem; margin-top: 1rem;">
                            No active companies found. Please setup companies first before creating permissions.
                        </p>
                    <?php else: ?>
                        <div class="company-accordion">
                            <?php foreach ($companies as $comp): ?>
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <span>
                                            <i class="fas fa-building"></i> 
                                            <?php echo htmlspecialchars($comp['company_name']); ?> 
                                            <span class="badge-info"><?php echo htmlspecialchars($comp['branch']); ?></span>
                                        </span>
                                        <i class="fas fa-chevron-down accordion-arrow"></i>
                                    </div>
                                    <div class="accordion-content">
                                        <div class="table-responsive" style="margin-bottom: 0;">
                                            <table class="permission-table">
                                                <thead>
                                                    <tr>
                                                        <th>Module / Page</th>
                                                        <th style="width: 120px; text-align: center;">Select All</th>
                                                        <th style="width: 100px; text-align: center;">Add</th>
                                                        <th style="width: 100px; text-align: center;">Edit</th>
                                                        <th style="width: 100px; text-align: center;">View</th>
                                                        <th style="width: 100px; text-align: center;">Delete</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    // print_r($modules);
                                                    foreach ($modules as $page_key => $page_display): 
                                                        // 1. Exclude admin-only core system modules
                                                        if (in_array($page_key, ['dashboard', 'company', 'roles', 'users', 'staff'])) {
                                                            continue;
                                                        }

                                                        $comp_id = $comp['company_id'];
                                                        // Prefill values
                                                        $saved_actions = $existing_permissions[$comp_id][$page_key] ?? [];
                                                        
                                                        $has_view = in_array(PERMISSION_VIEW, $saved_actions);
                                                        $has_add = in_array(PERMISSION_ADD, $saved_actions);
                                                        $has_edit = in_array(PERMISSION_EDIT, $saved_actions);
                                                        $has_delete = in_array(PERMISSION_DELETE, $saved_actions);
                                                        
                                                        // 2. Identify if it is a report module (contains 'report')
                                                        $is_report = (strpos($page_key, 'report') !== false) && ($page_key != 'daily_reports') && ($page_key != 'student_reports') && ($page_key != 'daily_report');
                                                        
                                                        $all_checked = !$is_report && ($has_view && $has_add && $has_edit && $has_delete);
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <strong style="color: #475569;"><?php echo $page_display; ?></strong>
                                                                <span style="display: block; font-size: 0.75rem; color: var(--text-muted);"><?php echo $page_key; ?></span>
                                                            </td>
                                                            <td style="text-align: center;">
                                                                <?php if (!$is_report): ?>
                                                                    <input type="checkbox" class="permission-checkbox select-all-chk" data-company="<?php echo $comp_id; ?>" data-page="<?php echo $page_key; ?>" <?php echo $all_checked ? 'checked' : ''; ?>>
                                                                <?php else: ?>
                                                                    <span style="color: var(--text-muted); font-size: 0.85rem;">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td style="text-align: center;">
                                                                <?php if (!$is_report): ?>
                                                                    <input type="checkbox" name="permissions[<?php echo $comp_id; ?>][<?php echo $page_key; ?>][]" value="<?php echo PERMISSION_ADD; ?>" class="permission-checkbox action-chk add-chk" data-company="<?php echo $comp_id; ?>" data-page="<?php echo $page_key; ?>" <?php echo $has_add ? 'checked' : ''; ?>>
                                                                <?php else: ?>
                                                                    <span style="color: var(--text-muted); font-size: 0.85rem;">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td style="text-align: center;">
                                                                <?php if (!$is_report): ?>
                                                                    <input type="checkbox" name="permissions[<?php echo $comp_id; ?>][<?php echo $page_key; ?>][]" value="<?php echo PERMISSION_EDIT; ?>" class="permission-checkbox action-chk edit-chk" data-company="<?php echo $comp_id; ?>" data-page="<?php echo $page_key; ?>" <?php echo $has_edit ? 'checked' : ''; ?>>
                                                                <?php else: ?>
                                                                    <span style="color: var(--text-muted); font-size: 0.85rem;">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td style="text-align: center;">
                                                                <input type="checkbox" name="permissions[<?php echo $comp_id; ?>][<?php echo $page_key; ?>][]" value="<?php echo PERMISSION_VIEW; ?>" class="permission-checkbox action-chk view-chk" data-company="<?php echo $comp_id; ?>" data-page="<?php echo $page_key; ?>" <?php echo $has_view ? 'checked' : ''; ?>>
                                                            </td>
                                                            <td style="text-align: center;">
                                                                <?php if (!$is_report): ?>
                                                                    <input type="checkbox" name="permissions[<?php echo $comp_id; ?>][<?php echo $page_key; ?>][]" value="<?php echo PERMISSION_DELETE; ?>" class="permission-checkbox action-chk delete-chk" data-company="<?php echo $comp_id; ?>" data-page="<?php echo $page_key; ?>" <?php echo $has_delete ? 'checked' : ''; ?>>
                                                                <?php else: ?>
                                                                    <span style="color: var(--text-muted); font-size: 0.85rem;">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="form-buttons">
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save"></i> Save Permissions
                        </button>
                        <button type="button" class="btn-save" style="background: #64748b;" onclick="window.location.href='role.php'">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <script>
        $(document).ready(function() {
            // Custom Accordion Interactivity
            $('.accordion-header').click(function() {
                var content = $(this).next('.accordion-content');
                var arrow = $(this).find('.accordion-arrow');
                
                $(this).toggleClass('active');
                content.slideToggle(250);
                arrow.toggleClass('rotate');
            });
            
            // Open first company accordion by default
            $('.accordion-header').first().addClass('active').next('.accordion-content').show().find('.accordion-arrow').addClass('rotate');

            // --- CHECKBOX LOGIC RULES ---
            
            // Sync View checkbox on Add/Edit/Delete checkbox changes
            $(document).on('change', '.action-chk', function() {
                var company = $(this).data('company');
                var page = $(this).data('page');
                var action = $(this).val();
                
                if ($(this).is(':checked')) {
                    // Rule 3, 4, 5: If Add (A), Edit (E), or Delete (D) checked -> View (V) automatically checked
                    if (action === 'A' || action === 'E' || action === 'D') {
                        $('input.view-chk[data-company="' + company + '"][data-page="' + page + '"]').prop('checked', true);
                    }
                } else {
                    // Rule 6: If View (V) unchecked -> Add/Edit/Delete automatically unchecked
                    if (action === 'V') {
                        $('input.action-chk[data-company="' + company + '"][data-page="' + page + '"]').not('.view-chk').prop('checked', false);
                    }
                }
                
                // Recalculate Select All state for this page row
                updateSelectAllState(company, page);
            });
            
            // Rule 1 & 2: Select All Toggling
            $(document).on('change', '.select-all-chk', function() {
                var isChecked = $(this).is(':checked');
                var company = $(this).data('company');
                var page = $(this).data('page');
                
                // Toggle all action checkboxes for this page and company
                $('input.action-chk[data-company="' + company + '"][data-page="' + page + '"]').prop('checked', isChecked);
            });

            // Helper to keep Select All checkbox in sync
            function updateSelectAllState(company, page) {
                var allChecked = true;
                $('input.action-chk[data-company="' + company + '"][data-page="' + page + '"]').each(function() {
                    if (!$(this).is(':checked')) {
                        allChecked = false;
                    }
                });
                
                $('input.select-all-chk[data-company="' + company + '"][data-page="' + page + '"]').prop('checked', allChecked);
            }

            // Sync Select All status on page load (pre-fill sync)
            $('.select-all-chk').each(function() {
                var company = $(this).data('company');
                var page = $(this).data('page');
                updateSelectAllState(company, page);
            });

            // --- FORM AJAX SUBMISSION ---
            $('#rolePermissionForm').submit(function(e) {
                e.preventDefault();
                
                if ($('.action-chk:checked').length === 0) {
                    alert("Error: Please select at least one permission before saving.");
                    return false;
                }
                
                $.ajax({
                    url: 'role_save.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.trim() === 'Success') {
                            window.location.href = 'role.php';
                        } else {
                            alert(response);
                        }
                    },
                    error: function() {
                        alert("An error occurred while saving the role permissions.");
                    }
                });
            });
        });

        function submitRoleAction(action, roleId = '') {
            var form = $('<form>', {
                action: 'role.php',
                method: 'POST'
            });
            form.append($('<input>', {
                type: 'hidden',
                name: 'action',
                value: action
            }));
            if (roleId) {
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'role_id',
                    value: roleId
                }));
            }
            $('body').append(form);
            form.submit();
        }

        // Delete Role Handler
        function deleteRole(roleId) {
            if (confirm("Are you sure you want to delete this role? This will also remove all its permissions.")) {
                $.ajax({
                    url: 'role_save.php',
                    type: 'POST',
                    data: { action: 'delete', role_id: roleId },
                    success: function(response) {
                        if (response.trim() === 'Success') {
                            location.reload();
                        } else {
                            alert(response);
                        }
                    }
                });
            }
        }
    </script>
</body>
</html>
