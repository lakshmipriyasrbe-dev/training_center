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
					if(date('Y-m-d', strtotime($field_value)) != $field_value) {
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

		public function valid_gst($field_value, $field_name) {
			$result = "";
			$field_value = trim($field_value);
			
			if(empty($field_value)) {
				$result = "Enter the " . $field_name;
				return $result;
			}

			// GSTIN must be exactly 15 characters
			if(strlen($field_value) != 15) {
				$result = $field_name . " must be exactly 15 characters";
				return $result;
			}

			// Convert to uppercase for validation
			$field_value = strtoupper($field_value);

			// State Code (First 2 digits): Should be between 01 and 37
			$state_code = substr($field_value, 0, 2);
			if(!preg_match("/^[0-9]{2}$/", $state_code)) {
				$result = $field_name . " state code (first 2 digits) must be numeric";
				return $result;
			}
			
			$state_code_int = (int)$state_code;
			if($state_code_int < 1 || $state_code_int > 37) {
				$result = $field_name . " state code must be between 01 and 37";
				return $result;
			}

			// PAN (Digits 3-12): Should be alphanumeric (10 characters)
			// Format: 5 letters + 4 digits + 1 letter (standard PAN format)
			$pan = substr($field_value, 2, 10);
			if(!preg_match("/^[A-Z0-9]{10}$/", $pan)) {
				$result = $field_name . " PAN (digits 3-12) must be alphanumeric";
				return $result;
			}

			// Entity Number (Digit 13): Should be a single digit (0-9)
			$entity_number = substr($field_value, 12, 1);
			if(!preg_match("/^[0-9]$/", $entity_number)) {
				$result = $field_name . " entity number (digit 13) must be a single digit";
				return $result;
			}

			// Default Alphabet (Digit 14): Usually 'Z' for standard taxpayers
			$default_alphabet = substr($field_value, 13, 1);
			if(!preg_match("/^[A-Z]$/", $default_alphabet)) {
				$result = $field_name . " default alphabet (digit 14) must be a letter";
				return $result;
			}

			// Checksum (Digit 15): Should be alphanumeric
			$checksum = substr($field_value, 14, 1);
			if(!preg_match("/^[A-Z0-9]$/", $checksum)) {
				$result = $field_name . " checksum (digit 15) must be alphanumeric";
				return $result;
			}

			// Validate GSTIN checksum using mod 11 algorithm
			// $result = $this->validate_gst_checksum($field_value, $field_name);
			
			return $result;
		}

		private function validate_gst_checksum($gstin, $field_name) {
			// GSTIN Checksum Validation using Luhn algorithm adapted for GSTIN
			// The first 14 characters are used to calculate the checksum (15th character)
			
			$gstin = strtoupper($gstin);
			$gstin_first_14 = substr($gstin, 0, 14);
			$provided_checksum = substr($gstin, 14, 1);
			
			// Character set for GSTIN: 0-9 and A-Z (36 characters)
			$charset = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			
			$multiplier = 2;
			$sum = 0;
			
			// Process each character from right to left (excluding the checksum digit)
			for ($i = strlen($gstin_first_14) - 1; $i >= 0; $i--) {
				$char = $gstin_first_14[$i];
				$digit = strpos($charset, $char);
				
				if ($digit === false) {
					return $field_name . " contains invalid characters";
				}
				
				$product = $digit * $multiplier;
				
				// If product is greater than 35, subtract 36
				if ($product > 35) {
					$product = $product - 36;
				}
				
				$sum += $product;
				
				// Alternate multiplier between 2 and 1
				$multiplier = ($multiplier == 2) ? 1 : 2;
			}
			
			// Calculate checksum
			$calculated_checksum_code = (36 - ($sum % 36)) % 36;
			$calculated_checksum = $charset[$calculated_checksum_code];
			
			// Validate checksum
			if ($calculated_checksum != $provided_checksum) {
				return $field_name . " checksum validation failed. Invalid GSTIN";
			}
			
			return "";
		}
	}
?>
