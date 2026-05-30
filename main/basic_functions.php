<?php
    include_once("config.php");
    include_once("label.php");
    
    class Basic_Functions extends Database {
        public $con;
        
        public function __construct() {
            $this->con = $this->connect();
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
        }

        public function getCompanyId() {
            return $_SESSION['company_id'] ?? null;
        }

        public function sanitize($data) {
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    $data[$key] = $this->sanitize($value);
                }
            } else {
                $data = trim($data);
                $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
            }
            return $data;
        }

        public function InsertSQL($table, $data, $custom_id = '', $unique_number = '', $action = '') {
            $con = $this->con;
            $last_insert_id = "";

            if (!empty($data)) {
                $company_id = $this->getCompanyId();
                if ($company_id && !isset($data['company_id']) && $table !== $GLOBALS['company_table'] && $table !== $GLOBALS['role_table'] && $table !== $GLOBALS['user_table']) {
                    $data['company_id'] = $company_id;
                }
                $columns = array_keys($data);
                $placeholders = [];
                $params = [];
                
                foreach ($data as $key => $value) {
                    $placeholders[] = ":$key";
                    $params[":$key"] = $value;
                }
                
                $sql = "INSERT INTO $table (" . implode(",", $columns) . ") VALUES (" . implode(",", $placeholders) . ")";
                $stmt = $con->prepare($sql);
                
                if ($stmt->execute($params)) {
                    $last_insert_id = $con->lastInsertId();
                    
                    if (!empty($custom_id) && is_numeric($last_insert_id)) {
                        $custom_id_value = date("dmYhis") . "_" . str_pad($last_insert_id, 2, "0", STR_PAD_LEFT);
                        $custom_id_value = $this->encode_decode('encrypt', $custom_id_value);
                        
                        $update_data = [$custom_id => $custom_id_value];
                        
                        if (!empty($unique_number)) {
                            $last_record_id = $this->getLastRecordIDFromTable($table);
                            $unique_number_value = $this->automate_number($table, $unique_number, $last_record_id, $last_insert_id);
                            if (!empty($unique_number_value)) {
                                $unique_number_value = $this->encode_decode('encrypt', strtoupper($unique_number_value));
                                $update_data[$unique_number] = $unique_number_value;
                            }
                        }
                        
                        $this->UpdateSQL($table, $update_data, "id = :id", [":id" => $last_insert_id]);
                        $this->add_log($table, $last_insert_id, $sql, $action);
                    } else {
                        $this->add_log($table, $last_insert_id, $sql, $action);
                    }
                } else {
                    $last_insert_id = "Unable to insert the data";
                }
            }
            return $last_insert_id;
        }

        public function UpdateSQL($table, $data, $where_clause, $where_params = []) {
            $con = $this->con;
            $set_parts = [];
            $params = [];

            foreach ($data as $column => $value) {
                $set_parts[] = "$column = :upd_$column";
                $params[":upd_$column"] = $value;
            }
            
            $company_id = $this->getCompanyId();
            if ($company_id && $table !== $GLOBALS['company_table'] && $table !== $GLOBALS['role_table'] && $table !== $GLOBALS['user_table']) {
                if (!empty($where_clause)) {
                    $where_clause = "($where_clause) AND company_id = :comp_company_id";
                } else {
                    $where_clause = "company_id = :comp_company_id";
                }
                $where_params[':comp_company_id'] = $company_id;
            }

            $sql = "UPDATE $table SET " . implode(", ", $set_parts) . " WHERE $where_clause";
            // echo $sql; // For debugging
            // print_r($params); // For debugging
            $stmt = $con->prepare($sql);
            
            $all_params = array_merge($params, $where_params);
            if ($stmt->execute($all_params)) {
                $rowCount = $stmt->rowCount();
                $this->add_log($table, 'Multiple/Where', $sql, 'UPDATE');
                return $rowCount;
            }
            return false;
        }

        public function getLastRecordIDFromTable($table) {
            $stmt = $this->con->prepare("SELECT id FROM $table ORDER BY id DESC LIMIT 1");
            $stmt->execute();
            return $stmt->fetchColumn() ?: 0;
        }

        // public function automate_number($table, $field, $last_id, $current_id) {
        //     $prefixes = [
        //         $GLOBALS['user_table'] => "USR",
        //         $GLOBALS['task_table'] => "TSK",
        //         $GLOBALS['report_table'] => "REP",
        //         $GLOBALS['enrollment_table'] => "EN"
        //     ];
            
        //     $prefix = $prefixes[$table] ?? "TC";
            
        //     // Calculate Financial Year
        //     $current_month = (int)date('m');
        //     $current_year = (int)date('Y');
            
        //     if ($current_month >= 4) {
        //         $fy_start_year = $current_year;
        //         $fy_end_year = $current_year + 1;
        //     } else {
        //         $fy_start_year = $current_year - 1;
        //         $fy_end_year = $current_year;
        //     }
            
        //     $fy_suffix = substr($fy_start_year, -2) . '-' . substr($fy_end_year, -2);
            
        //     // We need to count how many records exist for this FY
        //     $fy_start_date = $fy_start_year . "-04-01 00:00:00";
        //     $fy_end_date = $fy_end_year . "-03-31 23:59:59";
            
        //     $stmt = $this->con->prepare("SELECT COUNT(id) FROM $table WHERE created_date_time >= :start AND created_date_time <= :end");
        //     $stmt->execute([':start' => $fy_start_date, ':end' => $fy_end_date]);
        //     $count = $stmt->fetchColumn();
            
        //     $next_number = $count > 0 ? $count : 1;
            
        //     return $prefix . str_pad($next_number, 3, "0", STR_PAD_LEFT) . "/" . $fy_suffix;
        // }
        public function automate_number($table, $field, $last_id, $current_id) {

            $prefixes = [
                $GLOBALS['user_table'] => "USR",
                $GLOBALS['task_table'] => "TSK",
                $GLOBALS['report_table'] => "REP",
                $GLOBALS['enrollment_table'] => "ENT",
                $GLOBALS['enrollment_internship_table'] => "ENI",
                $GLOBALS['attendance_table'] => "ATT",
                $GLOBALS['payment_table'] => "REC",
                $GLOBALS['payroll_table'] => "PR",
                $GLOBALS['student_attendance_table'] => "SATT",
                $GLOBALS['event_table'] => "EVT"
            ];

            $prefix = $prefixes[$table] ?? "";

            // Financial Year
            $current_month = date('m');
            $current_year = date('Y');

            if ($current_month >= 4) {
                $fy_start_year = $current_year;
                $fy_end_year = $current_year + 1;
            } else {
                $fy_start_year = $current_year - 1;
                $fy_end_year = $current_year;
            }

            $fy_suffix = substr($fy_start_year, -2) . '-' . substr($fy_end_year, -2);

            $fy_start_date = $fy_start_year . "-04-01 00:00:00";
            $fy_end_date = $fy_end_year . "-03-31 23:59:59";

            $stmt = $this->con->prepare("
                SELECT COUNT(id) as total 
                FROM $table 
                WHERE created_date_time >= :start 
                AND created_date_time <= :end
            ");

            // echo $stmt->queryString;

            $stmt->execute([
                ':start' => $fy_start_date,
                ':end' => $fy_end_date
            ]);            

            // Echo Params
            // echo "<pre>";
            // print_r($params);
            // echo "</pre>";

            $count = $stmt->fetchColumn();

            $next_number = $count + 1;

            return $prefix . str_pad($next_number, 3, "0", STR_PAD_LEFT) . "/" . $fy_suffix;
        }

        public function encode_decode($action, $string) {
            $salt = $GLOBALS['salt'] ?? "default_salt";
            $key = hash('sha256', $salt);
            $iv = substr(hash('sha256', $salt), 0, 16);

            if ($action == 'encrypt') {
                return base64_encode(openssl_encrypt($string, "AES-256-CBC", $key, 0, $iv));
            } else if ($action == 'decrypt') {
                return openssl_decrypt(base64_decode($string), "AES-256-CBC", $key, 0, $iv);
            }
            return $string;
        }

        public function getTableRecords($table, $field = '', $value = '', $orderby = 'id DESC') {
            $con = $this->con;
            $sql = "SELECT * FROM $table WHERE deleted = :deleted";
            $params = [":deleted" => 0];
            
            $company_id = $this->getCompanyId();
            if ($company_id && $table !== $GLOBALS['company_table'] && $table !== $GLOBALS['role_table'] && $table !== $GLOBALS['user_table'] && $table !== $GLOBALS['course_table']) {
                $sql .= " AND company_id = :comp_company_id";
                $params[':comp_company_id'] = $company_id;
            }

            if ($table === $GLOBALS['user_table']) {
                $sql .= " AND LOWER(role) != 'admin'";
            }
            if ($table === $GLOBALS['role_table']) {
                $sql .= " AND LOWER(role_name) != 'admin'";
            }
            
            if (!empty($field)) {
                $sql .= " AND $field = :$field";
                $params[":$field"] = $value;
            }
            
            $sql .= " ORDER BY $orderby";
            // echo $sql;
            // print_r($params);
            $stmt = $con->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        }

        public function isRecordLinked($relations) {
            foreach ($relations as $rel) {
                $table = $rel['table'];
                $column = $rel['column'];
                $value = $rel['value'];
                $label = $rel['label'] ?? $table;

                $sql = "SELECT COUNT(*) FROM {$table} WHERE {$column} = :val AND deleted = 0";
                $params = [':val' => $value];

                $stmt = $this->con->prepare($sql);
                $stmt->execute($params);
                $count = $stmt->fetchColumn();
                if ($count > 0) {
                    return $label;
                }
            }
            return false;
        }

        public function getTableColumnValue($table, $field, $value, $return_field) {

            $con = $this->con;

            $sql = "SELECT {$return_field}
                    FROM {$table}
                    WHERE {$field} = :value
                    AND deleted = :deleted";

            $params = array(
                ':value' => $value,
                ':deleted' => 0
            );

            $company_id = $this->getCompanyId();
            if ($company_id && $table !== $GLOBALS['company_table'] && $table !== $GLOBALS['role_table'] && $table !== $GLOBALS['user_table']) {
                $sql .= " AND company_id = :comp_company_id";
                $params[':comp_company_id'] = $company_id;
            }
            
            $sql .= " LIMIT 1";

            $stmt = $con->prepare($sql);
            $stmt->execute($params);

            // echo $this->debugQuery($sql, $params);

            $result = $stmt->fetch();

            return !empty($result[$return_field]) ? $result[$return_field] : "";
        }

        public function getQueryRecords($query, $params = []) {
            $con = $this->con;
            $stmt = $con->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        }

        public function add_log($table, $id, $query, $action) {
            $log_dir = __DIR__ . '/logs';
            if (!is_dir($log_dir)) {
                mkdir($log_dir, 0777, true);
            }
            
            $log_file = $log_dir . '/activity_log.csv';
            $file_exists = file_exists($log_file);
            
            $handle = fopen($log_file, 'a');
            if (!$file_exists) {
                fputcsv($handle, ['Timestamp', 'Table', 'Record ID', 'User', 'Action', 'Query']);
            }
            
            $user = $_SESSION['username'] ?? 'Guest/System';
            $timestamp = date('Y-m-d H:i:s');
            
            fputcsv($handle, [$timestamp, $table, $id, $user, $action, $query]);
            fclose($handle);
            return true;
        }

        public function db_backup() {
            $db_config = $this->getDbConfig();
            $backup_dir = __DIR__ . '/backup';
            if (!is_dir($backup_dir)) {
                mkdir($backup_dir, 0777, true);
            }

            $filename = $backup_dir . '/backup_' . $db_config['dbname'] . '_' . date('Y-m-d_H-i-s') . '.sql';
            
            // Note: In a real environment, you'd use mysqldump.
            // Here I'll simulate or use a simple PHP-based export if needed, 
            // but usually mysqldump is preferred if available.
            // For now, I'll just log that backup was triggered.
            
            $this->add_log('DATABASE', '0', 'BACKUP TRIGGERED', 'BACKUP');
            
            // Attempt simple backup if shell_exec is allowed
            $command = "mysqldump --user={$db_config['user']} --password={$db_config['pass']} --host={$db_config['host']} {$db_config['dbname']} > $filename";
            @shell_exec($command);
            
            return $filename;
        }

        public function get_role_permissions($role_id) {
            $role_table = $GLOBALS['role_table'];
            $stmt = $this->con->prepare("SELECT permissions FROM $role_table WHERE id = :role_id AND deleted = 0");
            $stmt->execute([':role_id' => $role_id]);
            $result = $stmt->fetch();
            
            if ($result && !empty($result['permissions'])) {
                return json_decode($result['permissions'], true);
            }
            return [];
        }   

        // Check permission
        public function check_permission($action, $page = '') {
            if (!isset($_SESSION['user_id'])) return false;
            if ($GLOBALS['user_role'] === 'admin') return true;

            $user_id = $_SESSION['user_id'];
            $permissions = $this->get_role_permissions($_SESSION['role_id']);
            
            // If no permission set, deny by default
            if (empty($permissions)) return false;

            // Check global permissions
            if (isset($permissions['global']) && is_array($permissions['global'])) {
                foreach ($permissions['global'] as $global_action) {
                    if ($global_action === $action) return true;
                }
            }

            // Check page specific permissions
            if (!empty($page) && isset($permissions['pages']) && is_array($permissions['pages'])) {
                foreach ($permissions['pages'] as $page_config) {
                    if ($page_config['page'] === $page) {
                        if (isset($page_config['actions']) && is_array($page_config['actions'])) {
                            foreach ($page_config['actions'] as $page_action) {
                                if ($page_action === $action) return true;
                            }
                        }
                        // If page exists but action not found, deny (specific control)
                        return false;
                    }
                }
            }
            return false;
        }

        public function check_unique_username($username, $exclude_table = '', $exclude_id = '') {
            // Check user table
            $query_user = "SELECT id FROM " . $GLOBALS['user_table'] . " WHERE LOWER(username) = :username AND deleted = 0";
            $params_user = [':username' => strtolower($username)];
            if ($exclude_table == $GLOBALS['user_table'] && !empty($exclude_id)) {
                $query_user .= " AND id != :id";
                $params_user[':id'] = $exclude_id;
            }
            $result_user = $this->getQueryRecords($query_user, $params_user);
            if (!empty($result_user)) return $result_user[0]['id'];

            // Check staff table
            $query_staff = "SELECT id FROM " . $GLOBALS['staff_table'] . " WHERE LOWER(username) = :username AND deleted = 0";
            $params_staff = [':username' => strtolower($username)];
            if ($exclude_table == $GLOBALS['staff_table'] && !empty($exclude_id)) {
                $query_staff .= " AND id != :id";
                $params_staff[':id'] = $exclude_id;
            }
            $result_staff = $this->getQueryRecords($query_staff, $params_staff);
            if (!empty($result_staff)) return $result_staff[0]['id'];

            return false;
        }

        public function check_unique_mobile($mobile, $exclude_table = '', $exclude_id = '') {
            // Check user table
            $query_user = "SELECT id FROM " . $GLOBALS['user_table'] . " WHERE mobile = :mobile AND deleted = 0";
            $params_user = [':mobile' => $mobile];
            if ($exclude_table == $GLOBALS['user_table'] && !empty($exclude_id)) {
                $query_user .= " AND id != :id";
                $params_user[':id'] = $exclude_id;
            }
            $result_user = $this->getQueryRecords($query_user, $params_user);
            if (!empty($result_user)) return $result_user[0]['id'];

            // Check staff table
            $query_staff = "SELECT id FROM " . $GLOBALS['staff_table'] . " WHERE staff_number = :mobile AND deleted = 0";
            $params_staff = [':mobile' => $mobile];
            if ($exclude_table == $GLOBALS['staff_table'] && !empty($exclude_id)) {
                $query_staff .= " AND id != :id";
                $params_staff[':id'] = $exclude_id;
            }
            $result_staff = $this->getQueryRecords($query_staff, $params_staff);
            if (!empty($result_staff)) return $result_staff[0]['id'];

            return false;
        }

        public function debugQuery($query, $params) {
            foreach ($params as $k => $v) {
                $value = is_numeric($v) ? $v : "'".$v."'";
                $query = str_replace($k, $value, $query);
            }
            return "<pre>".$query."</pre>";
        }

        /* --- PAGINATION & FILTERING --- */

        public function getPaginatedResults($query, $params, $start, $limit) {
            // Count total records (ignoring limit)
            $count_query = "SELECT COUNT(*) FROM (" . $query . ") as total_count";
            $stmt_count = $this->con->prepare($count_query);
            $stmt_count->execute($params);
            $total_records = $stmt_count->fetchColumn() ?: 0;
            
            // Fetch limited data
            $limited_query = $query . " LIMIT :start, :limit";
            $stmt = $this->con->prepare($limited_query);
            
            // Bind all original params
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            // Bind pagination params as integers
            $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            
            $stmt->execute();
            $data = $stmt->fetchAll();
            
            return [
                'data' => $data,
                'total_records' => $total_records
            ];
        }

        public function getStaffList($start = 0, $limit = 10, $search = '') {
            $where = "deleted = 0";
            $params = [];
            
            $company_id = $this->getCompanyId();
            if ($company_id) {
                $where .= " AND company_id = :comp_company_id";
                $params[':comp_company_id'] = $company_id;
            }
            
            if (!empty($search)) {
                $where .= " AND (staff_name LIKE :search OR staff_number LIKE :search OR username LIKE :search)";
                $params[':search'] = "%$search%";
            }
            
            $query = "SELECT * FROM " . $GLOBALS['staff_table'] . " WHERE $where ORDER BY id DESC";
            return $this->getPaginatedResults($query, $params, $start, $limit);
        }

        public function getTableList($table_name, $search_cols = [], $start = 0, $limit = 10, $search = '') {
            $where = "deleted = 0";
            $params = [];
            
            $company_id = $this->getCompanyId();
            if ($company_id && $table_name !== $GLOBALS['company_table'] && $table_name !== $GLOBALS['role_table'] && $table_name !== $GLOBALS['user_table'] && $table_name !== $GLOBALS['course_table']) {
                $where .= " AND company_id = :comp_company_id";
                $params[':comp_company_id'] = $company_id;
            }

            if ($table_name === $GLOBALS['user_table']) {
                $where .= " AND LOWER(role) != 'admin' AND LOWER(role) != 'director'";
            }
            if ($table_name === $GLOBALS['role_table']) {
                $where .= " AND LOWER(role_name) != 'admin'";
            }

            // if($table_name == $GLOBALS['user_table']) {
            //     $where .= "AND role != :excluded_role";
            //     $params[':excluded_role'] = 'student';
            // }
            
            if (!empty($search) && !empty($search_cols)) {
                $search_parts = [];
                foreach ($search_cols as $col) {
                    $search_parts[] = "$col LIKE :search";
                }
                $where .= " AND (" . implode(" OR ", $search_parts) . ")";
                $params[':search'] = "%$search%";
            }
            
            $query = "SELECT * FROM $table_name WHERE $where ORDER BY id DESC";
            return $this->getPaginatedResults($query, $params, $start, $limit);
        }

        public function getAttendanceList($start = 0, $limit = 10, $search = '') {
            $where = "deleted = 0";
            $params = [];
            
            $company_id = $this->getCompanyId();
            if ($company_id) {
                $where .= " AND company_id = :comp_company_id";
                $params[':comp_company_id'] = $company_id;
            }
            
            if (!empty($search)) {
                $where .= " AND (staff_name LIKE :search OR staff_number LIKE :search OR attendance_date LIKE :search)";
                $params[':search'] = "%$search%";
            }
            
            // Attendance needs to group by date as per previous implementation
            $query = "SELECT * FROM " . $GLOBALS['attendance_table'] . " WHERE $where ORDER BY attendance_date DESC, id ASC";
            return $this->getPaginatedResults($query, $params, $start, $limit);
        }

        public function getStudentAttendanceList($start = 0, $limit = 10, $search = '', $staff_id = '') {
            $where = "deleted = 0";
            $params = [];
            
            $company_id = $this->getCompanyId();
            if ($company_id) {
                $where .= " AND company_id = :comp_company_id";
                $params[':comp_company_id'] = $company_id;
            }

            // if($_SESSION['role_id'] == 'cFlxWHg1N2RYSzg5OEczMGFSTmNvaFJjU2tQdEJlZVJoR1ZLL3ZYZitzUT0=') {
            //     if (!empty($staff_id)) {
            //         $where .= " AND staff_id = :staff_id";
            //         $params[':staff_id'] = $staff_id;
            //     }
            // }            
            
            
            if (!empty($search)) {
                $where .= " AND (attendance_date LIKE :search OR attendance_number LIKE :search)";
                $params[':search'] = "%$search%";
            }
            
            $query = "SELECT * FROM " . $GLOBALS['student_attendance_table'] . " WHERE $where ORDER BY attendance_date DESC, id ASC";

            // echo $this->debugQuery($query, $params);
            return $this->getPaginatedResults($query, $params, $start, $limit);
        }

        public function getEnrollmentList($table_name, $start = 0, $limit = 10, $search = '') {
            $where = "deleted = 0";
            $params = [];
            
            $company_id = $this->getCompanyId();
            if ($company_id && $table_name !== $GLOBALS['company_table']) {
                $where .= " AND company_id = :comp_company_id";
                $params[':comp_company_id'] = $company_id;
            }
            
            if (!empty($search)) {
                $where .= " AND (student_name LIKE :search OR student_id LIKE :search OR mobile_number LIKE :search)";
                $params[':search'] = "%$search%";
            }
            
            $query = "SELECT * FROM $table_name WHERE $where ORDER BY id DESC";
            return $this->getPaginatedResults($query, $params, $start, $limit);
        }

        public function checkCompanyAlreadyExists($company_name, $branch) {

            $where = "";
            $query = "";
            $prev_id = "";
            $params = [];

            if(!empty($company_name) && !empty($branch)) {

                $where = "LOWER(company_name) = :company_name 
                        AND LOWER(branch) = :branch 
                        AND ";

                $params['company_name'] = $company_name;
                $params['branch'] = $branch;
            }

            $query = "SELECT company_id 
                    FROM ".$GLOBALS['company_table']." 
                    WHERE ".$where." deleted = :deleted";

            $params['deleted'] = 0;

            $result = $this->getQueryRecords($query, $params);

            if(!empty($result)) {

                foreach($result as $data) {
                    $prev_id = $data['company_id'];
                }
            }

            return $prev_id;
        }
    }
?>
