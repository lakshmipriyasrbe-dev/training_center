Build a complete Role-Based Permission system in PHP + MySQL for my existing project.

Important:
Do NOT create permission_pages table.

All page/module names are already maintained manually inside label.php.

Example:

$modules = [
   'dashboard' => 'Dashboard',
   'enrollment_training' => 'Enrollment Training',
   'enrollment_internship' => 'Enrollment Internship',
   'course' => 'Course',
   'daily_report' => 'Daily Report',
   'student_report' => 'Student Report',
   'task' => 'Task'
];

Whenever I create a new screen/table manually, I add that page inside this $modules array.

Also create global permission constants in label.php:

define('PERMISSION_ADD', 'A');
define('PERMISSION_EDIT', 'E');
define('PERMISSION_VIEW', 'V');
define('PERMISSION_DELETE', 'D');

Also create:

$permissionActions = [
    PERMISSION_ADD => 'Add',
    PERMISSION_EDIT => 'Edit',
    PERMISSION_VIEW => 'View',
    PERMISSION_DELETE => 'Delete'
];

Need to use these constants everywhere instead of directly writing A/E/V/D.

Example:
checkPermission($company_id, $role_id, 'task', PERMISSION_VIEW)

Requirement:

Permission should be based on:
- company_id
- role_id
- page_name

Same role can have different permissions in different companies.

Example:
Company 1 → Manager → Dashboard + Task + Course
Company 2 → Manager → Dashboard only

Create role permission screen dynamically by looping $modules array.

Columns:
Page | Select All | Add | Edit | View | Delete

Rows should render dynamically from $modules.

Checkbox Logic:

1. Select All checked:
Add + Edit + View + Delete checked

2. Select All unchecked:
all unchecked

3. If Add checked:
View auto checked

4. If Edit checked:
View auto checked

5. If Delete checked:
View auto checked

6. If View unchecked:
Add/Edit/Delete unchecked automatically

Database:

Create only role_permissions table:

id
role_id
company_id
permission_page
permission_action
created_at

Store permission_action like:

V$$A$$E$$D

Examples:

dashboard => V
task => V$$D
student_report => V$$A$$E
course => V$$A$$E$$D

Store one record per page.

Edit Role:

While editing:
Fetch saved permissions using company_id + role_id
and pre-check checkboxes automatically.

Sidebar:

sidebar.php should loop $modules array.

Before showing menu call:

checkPermission($company_id, $role_id, $page_name, PERMISSION_VIEW)

If true → show menu
Else hide menu

Need reusable helper function:

checkPermission($company_id, $role_id, $access_page, $access_action)

Return true/false

Use same function in all pages:

Add button:
checkPermission(..., PERMISSION_ADD)

Edit button:
checkPermission(..., PERMISSION_EDIT)

Delete button:
checkPermission(..., PERMISSION_DELETE)

Page access:
checkPermission(..., PERMISSION_VIEW)

Generate complete code file-by-file for:

- label.php
- role.php
- role_save.php
- sidebar.php
- permission_helper.php
- jQuery checkbox logic
- role edit permission prefill
- save/update logic

Use:
PHP
MySQL
Bootstrap
jQuery
mysqli

Need clean reusable production-ready code with comments.

check image, this table with accordion type (since multiple company we have) i need to get add permission, view permission, edit permission, delete permission 

if we have any doubt ask me before implement
no need to check by yourself, i will check and tell the error
