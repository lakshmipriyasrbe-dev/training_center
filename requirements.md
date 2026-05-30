Enhance the existing enrollment and receipt flow without affecting the current receipt module functionality.

Current Forms:

* `enrollment_form` → submits to `enrollment_action.php`
* `enrollment_internship_form` → submits to `enrollment_internship_action.php`
* Existing receipt form → submits to `receipt_action.php`

New Requirement:
After successful enrollment save, if `paid_amount > 0`, automatically open the existing receipt modal and load the existing receipt form.

Implementation Requirements:

1. Enrollment Save Flow

* After enrollment is successfully inserted:

  * Get:

    * `student_id`
    * `enrollment_id`
    * `paid_amount`
    * `course_type`
* `course_type` values:

  * `"training"` for normal enrollment form
  * `"internship"` for internship enrollment form

2. Auto Open Receipt Modal

* If `paid_amount > 0`:

  * Automatically open the existing receipt modal.
  * Load the existing receipt form using AJAX or existing modal loading method.
* If `paid_amount <= 0`:

  * Do not open receipt modal.

3. Auto Fill Receipt Form
   Pre-fill these fields in receipt form:

* `student_id`
* `course_type`
* `enrollment_id`
* `total_amount` = enrollment `paid_amount`

4. Receipt Amount Restriction (Important)
   Receipt amount entry is mandatory when enrollment `paid_amount > 0`.

Since multiple payment modes are available (cash/card/upi/bank/etc):

* Sum of all payment mode amounts must exactly equal enrollment `paid_amount`.
* Example:

  * Enrollment paid_amount = 1000
  * Receipt payment split:

    * Cash = 600
    * UPI = 400
  * Total = 1000 → valid

Invalid cases:

* Total < paid_amount
* Total > paid_amount

Show validation error and prevent receipt save if mismatch occurs.

5. Database Changes

* Save `enrollment_id` in receipt table.
* Add column if not already available.

6. Existing Receipt Module
   Do NOT affect existing receipt functionality.

* Existing standalone receipt creation should continue working normally.
* Existing receipt edit/view logic should remain unchanged.

7. Preferred User Flow

* Submit enrollment form
* Enrollment saved successfully
* If paid_amount > 0:

  * Automatically open receipt modal
  * Receipt form already populated
  * User completes payment mode details
  * Save receipt

8. Keep Existing Structure

* Do not rewrite the entire module.
* Reuse existing modal, receipt form, validation, and receipt_action.php logic as much as possible.
* Only extend the current flow with minimum required changes.
