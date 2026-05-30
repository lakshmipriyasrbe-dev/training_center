<?php
    include_once("config.php");
    include_once("label.php");
    include_once("basic_functions.php");
    
    class Report_Functions extends Basic_Functions {
        public $con;
        
        public function __construct() {
            $this->con = $this->connect();
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
        }

        public function getEnrollmentReportList($course_type, $course_id, $from_date, $to_date) {
            $where = [];
            $params = [];

            if(!empty($course_type)) {
                $where[] = "type = :course_type";
                $params[':course_type'] = $course_type;
            }

            if(!empty($course_id)) {
                $where[] = "course_id = :course_id";
                $params[':course_id'] = $course_id;
            }

            if(!empty($from_date)) {
                $where[] = "enrollment_date >= :from_date";
                $params[':from_date'] = $from_date;
            }

            if(!empty($to_date)) {
                $where[] = "enrollment_date <= :to_date";
                $params[':to_date'] = $to_date;
            }

            $whereClause = "";

            if(!empty($where)) {
                $whereClause = " WHERE " . implode(" AND ", $where);
            }

            $query = "SELECT enroll_id, student_id, student_name, mobile_number, course_id, enrollment_date, fees_amount, type FROM (
                SELECT e.enrollment_id AS enroll_id, e.student_id, e.student_name, e.mobile_number, e.course_id, e.doj AS enrollment_date, e.fees_amount, 'training' AS type 
                FROM {$GLOBALS['enrollment_table']} e WHERE e.deleted = 0 AND e.company_id = '{$_SESSION['company_id']}'
                UNION ALL 
                SELECT ei.enrollment_internship_id AS enroll_id, ei.student_id, ei.student_name, ei.mobile_number, ei.course_id, ei.doj AS enrollment_date, ei.fees_amount, 'internship' AS type 
                FROM {$GLOBALS['enrollment_internship_table']} ei WHERE ei.deleted = 0 AND ei.company_id = '{$_SESSION['company_id']}'
            ) AS enrollments $whereClause ORDER BY enrollment_date ASC";

            // echo $this->debugQuery($query, $params);

            $enrollments = $this->getQueryRecords($query, $params);

            // Calculate paid_amount from payment table for each enrollment
            foreach ($enrollments as &$enrollment) {
                $paid_query = "SELECT COALESCE(SUM(total_amount), 0) as total_paid FROM {$GLOBALS['payment_table']} WHERE (enrollment_id = :enroll_id OR student_id = :enroll_id) AND deleted = 0";
                $paid_params = [':enroll_id' => $enrollment['enroll_id']];
                $paid_result = $this->getQueryRecords($paid_query, $paid_params);
                $enrollment['paid_amount'] = floatval($paid_result[0]['total_paid'] ?? 0);
                $enrollment['balance_amount'] = floatval($enrollment['fees_amount'] ?? 0) - $enrollment['paid_amount'];
            }
            unset($enrollment);

            return $enrollments;
        }   

        function getPayrollReportList($staff_id, $month, $year) {
            $where = ["p.company_id = :comp_id"];
            $params = [':comp_id' => $_SESSION['company_id']];

            if(!empty($staff_id)) {
                $where[] = "p.staff_id = :staff_id";
                $params[':staff_id'] = $staff_id;
            }

            if(!empty($month)) {
                $where[] = "month = :month";
                $params[':month'] = $month;
            }

            if(!empty($year)) {
                $where[] = "year = :year";
                $params[':year'] = $year;
            }

            $whereClause = "";

            if(!empty($where)) {
                $whereClause = " WHERE " . implode(" AND ", $where);
            }

            $query = "SELECT p.staff_id, p.payroll_number, p.cl_days, p.lop_days, p.total_deduction, p.incentive_amount, p.payment_date, p.net_salary FROM {$GLOBALS['payroll_table']} p LEFT JOIN {$GLOBALS['staff_table']} s ON p.staff_id = s.staff_id $whereClause ORDER BY p.payment_date DESC";

            // echo $this->debugQuery($query, $params);

            $payrolls = $this->getQueryRecords($query, $params);

            return $payrolls;
        }

        public function getPaymentReportList($course_type, $student_id, $from_date, $to_date, $bill_type = '', $expense_category_id = '') {
            $results = [];

            // ---- Fetch Receipts (Credits) from payment_table ----
            if (empty($bill_type) || $bill_type == 'Receipt') {
                $where = ["deleted = 0", "company_id = :comp_id"];
                $params = [':comp_id' => $_SESSION['company_id']];

                if (!empty($course_type)) {
                    $where[] = "course_type = :course_type";
                    $params[':course_type'] = $course_type;
                }
                if (!empty($student_id)) {
                    $where[] = "student_id = :student_id";
                    $params[':student_id'] = $student_id;
                }
                if (!empty($from_date)) {
                    $where[] = "payment_date >= :from_date";
                    $params[':from_date'] = $from_date;
                }
                if (!empty($to_date)) {
                    $where[] = "payment_date <= :to_date";
                    $params[':to_date'] = $to_date;
                }

                $whereClause = " WHERE " . implode(" AND ", $where);
                $query = "SELECT * FROM {$GLOBALS['payment_table']} $whereClause ORDER BY payment_date ASC";
                $receipts = $this->getQueryRecords($query, $params);

                foreach ($receipts as $r) {
                    // Resolve student name
                    $student_name = '';
                    $student_dec_id = '';
                    $table = ($r['course_type'] === 'internship') ? $GLOBALS['enrollment_internship_table'] : $GLOBALS['enrollment_table'];
                    $id_field = ($r['course_type'] === 'internship') ? 'enrollment_internship_id' : 'enrollment_id';
                    $stu_rec = $this->getTableRecords($table, $id_field, $r['student_id']);
                    if (!empty($stu_rec)) {
                        $student_name = $stu_rec[0]['student_name'];
                        $student_dec_id = $this->encode_decode('decrypt', $stu_rec[0]['student_id']);
                    }

                    $results[] = [
                        'bill_id' => $r['payment_id'] ?? '',
                        'bill_date' => $r['payment_date'],
                        'bill_type' => 'Receipt',
                        'bill_details' => $student_dec_id . ' - ' . $student_name . ' (' . ucfirst($r['course_type']) . ')',
                        'credit' => floatval($r['total_amount']),
                        'debit' => 0,
                        'raw' => $r
                    ];
                }
            }

            // ---- Fetch Expenses (Debits) from expense_entry_table ----
            if (empty($bill_type) || $bill_type == 'Expense') {
                $where = ["deleted = 0", "company_id = :comp_id"];
                $params = [':comp_id' => $_SESSION['company_id']];

                if (!empty($expense_category_id)) {
                    $where[] = "expense_category_id = :expense_category_id";
                    $params[':expense_category_id'] = $expense_category_id;
                }
                if (!empty($from_date)) {
                    $where[] = "expense_entry_date >= :from_date";
                    $params[':from_date'] = $from_date;
                }
                if (!empty($to_date)) {
                    $where[] = "expense_entry_date <= :to_date";
                    $params[':to_date'] = $to_date;
                }

                $whereClause = " WHERE " . implode(" AND ", $where);
                $query = "SELECT * FROM {$GLOBALS['expense_entry_table']} $whereClause ORDER BY expense_entry_date ASC";
                $expenses = $this->getQueryRecords($query, $params);

                foreach ($expenses as $e) {
                    // Resolve category name
                    $category_name = '';
                    if (!empty($e['expense_category_id'])) {
                        $category_name = $this->getTableColumnValue($GLOBALS['expense_category_table'], 'expense_category_id', $e['expense_category_id'], 'expense_category_name') ?: '';
                    }
                    $detail = $category_name;
                    if (!empty($e['description'])) {
                        $detail .= (!empty($detail) ? ' - ' : '') . $e['description'];
                    }

                    $results[] = [
                        'bill_id' => $e['expense_entry_number'] ?? '',
                        'bill_date' => $e['expense_entry_date'],
                        'bill_type' => 'Expense',
                        'bill_details' => $detail,
                        'credit' => 0,
                        'debit' => floatval($e['total_amount']),
                        'raw' => $e
                    ];
                }
            }

            // Sort all records by date ascending
            usort($results, function($a, $b) {
                return strtotime($a['bill_date']) - strtotime($b['bill_date']);
            });

            // Calculate running balance (Credit - Debit)
            $balance = 0;
            foreach ($results as &$row) {
                $balance += $row['credit'] - $row['debit'];
                $row['balance'] = $balance;
            }
            unset($row);

            return $results;
        }

        public function getPlacementReportList($from_date, $to_date) {
            $where = ["deleted = 0", "placed = 1", "company_id = :comp_id"];
            $params = [':comp_id' => $_SESSION['company_id']];

            if(!empty($from_date)) {
                $where[] = "closure_date >= :from_date";
                $params[':from_date'] = $from_date;
            }

            if(!empty($to_date)) {
                $where[] = "closure_date <= :to_date";
                $params[':to_date'] = $to_date;
            }

            $whereClause = " WHERE " . implode(" AND ", $where);
            $query = "SELECT * FROM {$GLOBALS['course_closure_table']} $whereClause ORDER BY closure_date ASC";

            return $this->getQueryRecords($query, $params);
        }
    }
