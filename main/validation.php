<?php	
	class validation {

		public function common_validation($field_value, $field_name, $field_type) {
			$result = "";
			$field_value = trim($field_value);
			if(!empty($field_value)) {
				if(preg_match("/[\"\'\>\<]/", $field_value)) {
					$result = "(&lsquo; &ldquo; > <) not allowed";
				}
			}
			else {
				if($field_type == "select") {
					$result = "Select the ".$field_name;
				}
				else {
					$result = "Enter the ".$field_name;
				}
			}
			return $result;
		}

		public function valid_date($field_value, $field_name, $required) {
			$result = "";
			$field_value = trim($field_value);
			if(!empty($field_value)) {
				$result = $this->common_validation($field_value, $field_name, '');
				if(empty($result)) {
					if(date('d-m-Y', strtotime($field_value)) != $field_value) {
						$result = "Invalid ".$field_name;
					}
				}
			}
			else {
				if($required == 1) {
					$result = "Enter the ".$field_name;
				}
			}
			return $result;
		}

		public function valid_name($field_value, $field_name) {
			$result = "";
			$field_value = trim($field_value);
			if(!empty($field_value)) {
				if(!preg_match("/^[a-zA-Z\s]+$/", $field_value)) {
					$result = $field_name . " should only contain letters and spaces";
				}
			} else {
				$result = "Enter the " . $field_name;
			}
			return $result;
		}

		public function valid_mobile($field_value, $field_name) {
			$result = "";
			$field_value = trim($field_value);
			if(!empty($field_value)) {
				if(!preg_match("/^[0-9]{10}$/", $field_value)) {
					$result = $field_name . " should be exactly 10 digits";
				}
			} else {
				$result = "Enter the " . $field_name;
			}
			return $result;
		}

		public function valid_password($field_value, $field_name) {
			$result = "";
			$field_value = trim($field_value);
			if(!empty($field_value)) {
				if(strlen($field_value) < 8) {
					$result = $field_name . " should be at least 8 characters";
				} else if(!preg_match("/[A-Z]/", $field_value) || !preg_match("/[0-9]/", $field_value) || !preg_match("/[\W]/", $field_value)) {
					$result = $field_name . " must include at least one capital letter, one number, and one special character";
				}
			} else {
				$result = "Enter the " . $field_name;
			}
			return $result;
		}

		public function valid_datetime($datetime, $field_name) {
			$result = "";
			if (empty($datetime)) {
				return "Select the " . $field_name;
			}
			
			$selected_timestamp = strtotime($datetime);
			$current_timestamp = time();
			
			if ($selected_timestamp < $current_timestamp) {
				$result = $field_name . " cannot be in the past";
			}
			return $result;
		}
	}
?>
