<?php
require_once 'common_file.php';

// Restrict access to Admins only
if ($user_role != 'admin') { 
    exit('Unauthorized'); 
}

$action = $_POST['action'] ?? '';
$role_id = $_POST['role_id'] ?? '';

$table_permissions = $GLOBALS['role_permissions_table'] ?? 'tc_role_permissions';

if ($action === 'delete') {
    $role_id = $bf->sanitize($_POST['role_id'] ?? '');
    
    if (!empty($role_id)) {
        // Fetch role_name to check user table link
        $role_name = $bf->getTableColumnValue($GLOBALS['role_table'], 'role_id', $role_id, 'role_name');
        
        $relations = [
            ['table' => $GLOBALS['staff_table'], 'column' => 'role_id', 'value' => $role_id, 'label' => 'Staff'],
            ['table' => $GLOBALS['user_table'], 'column' => 'role', 'value' => $role_name, 'label' => 'User']
        ];
        $linked = $bf->isRecordLinked($relations);
        if ($linked) {
            exit("Error: Cannot delete role because it is linked with $linked table.");
        }

        // Soft delete the role
        $bf->UpdateSQL($GLOBALS['role_table'], ['deleted' => 1], "role_id = :role_id", [':role_id' => $role_id]);
        
        // Delete all associated permissions from permissions table
        $stmt = $bf->con->prepare("DELETE FROM $table_permissions WHERE role_id = :role_id");
        $stmt->execute([':role_id' => $role_id]);
        
        echo "Success";
        exit();
    } else {
        exit("Error: Invalid role ID for deletion");
    }
}

if ($action === 'add' || $action === 'edit') {
    $role_name = $bf->sanitize($_POST['role_name'] ?? '');
    $description = $bf->sanitize($_POST['description'] ?? '');

    if (empty($role_name)) {
        exit("Error: Role Name is required");
    }

    // Process Add Mode
    if ($action === 'add') {
        // Check uniqueness of role name
        $stmt = $bf->con->prepare("SELECT id FROM " . $GLOBALS['role_table'] . " WHERE LOWER(role_name) = :name AND deleted = 0");
        $stmt->execute([':name' => strtolower($role_name)]);
        if ($stmt->fetch()) {
            exit("Error: Role name already exists");
        }

        $data = [
            'role_name' => strtolower($role_name),
            'description' => $description,
            'created_date_time' => date('Y-m-d H:i:s')
        ];
        
        // InsertSQL returns the last inserted numeric ID
        $inserted_id = $bf->InsertSQL($GLOBALS['role_table'], $data, 'role_id', '', 'ADD ROLE');
        
        if (is_numeric($inserted_id)) {
            $role_id = $bf->getTableColumnValue($GLOBALS['role_table'], 'id', $inserted_id, 'role_id');
        } else {
            exit("Error: Failed to create role record");
        }
    } else {
        // Process Edit Mode (Update role description & name)
        if (empty($role_id)) {
            exit("Error: Missing Role ID for update");
        }

        // Fetch the old role name first
        $old_role_name = $bf->getTableColumnValue($GLOBALS['role_table'], 'role_id', $role_id, 'role_name');
        
        if ($old_role_name !== $role_name) {
            // Block renaming core admin role
            if (strtolower($old_role_name) === 'admin') {
                exit("Error: System core role (admin) cannot be renamed.");
            }

            // Check uniqueness of new role name
            $stmt = $bf->con->prepare("SELECT id FROM " . $GLOBALS['role_table'] . " WHERE LOWER(role_name) = :name AND role_id != :role_id AND deleted = 0");
            $stmt->execute([':name' => strtolower($role_name), ':role_id' => $role_id]);
            if ($stmt->fetch()) {
                exit("Error: Role name already exists");
            }

            // Update user table links to point to the new role name
            $stmt_user = $bf->con->prepare("UPDATE " . $GLOBALS['user_table'] . " SET role = :new_role WHERE role = :old_role");
            $stmt_user->execute([':new_role' => strtolower($role_name), ':old_role' => $old_role_name]);
        }

        $data = [
            'role_name' => strtolower($role_name),
            'description' => $description,
            'updated_date_time' => date('Y-m-d H:i:s')
        ];
        
        $bf->UpdateSQL($GLOBALS['role_table'], $data, "role_id = :role_id", [':role_id' => $role_id]);
    }

    // --- SAVE ASSOCIATED ROLE PERMISSIONS ---
    
    // Validate that at least one permission is checked
    $has_permissions = false;
    if (isset($_POST['permissions']) && is_array($_POST['permissions'])) {
        foreach ($_POST['permissions'] as $company_id => $pages) {
            if (is_array($pages)) {
                foreach ($pages as $page_key => $actions) {
                    if (is_array($actions) && !empty($actions)) {
                        $has_permissions = true;
                        break 2;
                    }
                }
            }
        }
    }

    if (!$has_permissions) {
        exit("Error: Please select at least one permission before saving.");
    }
    
    // 1. First, delete all existing permissions for this role_id across all companies
    $stmt = $bf->con->prepare("DELETE FROM $table_permissions WHERE role_id = :role_id");
    $stmt->execute([':role_id' => $role_id]);

    // 2. Insert new permissions
    if (isset($_POST['permissions']) && is_array($_POST['permissions'])) {
        foreach ($_POST['permissions'] as $company_id => $pages) {
            if (is_array($pages)) {
                foreach ($pages as $page_key => $actions) {
                    if (is_array($actions) && !empty($actions)) {
                        // Clean up / sort actions for consistency (e.g. V always before A, E, D if present)
                        $order = ['V', 'A', 'E', 'D'];
                        $sorted_actions = [];
                        foreach ($order as $o) {
                            if (in_array($o, $actions)) {
                                $sorted_actions[] = $o;
                            }
                        }
                        
                        $permission_action_str = implode('$$', $sorted_actions);
                        
                        $permission_data = [
                            'role_id' => $role_id,
                            'company_id' => $company_id,
                            'permission_page' => $page_key,
                            'permission_action' => $permission_action_str,
                            'created_date_time' => date('Y-m-d H:i:s')
                        ];
                        
                        $bf->InsertSQL($table_permissions, $permission_data, '', '', 'ADD ROLE PERMISSION');
                    }
                }
            }
        }
    }

    echo "Success";
    exit();
}

exit("Error: Invalid action");
?>
