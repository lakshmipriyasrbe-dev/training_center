# Training Center Management System

A comprehensive web application for managing training centers, staff, students, courses, and daily reports.

## Features

- **Role-Based Access Control**: Admin, Manager, and Trainer roles with granular permissions
- **User Management**: Add, edit, and delete users (admins, managers, trainers)
- **Role Management**: Create and manage roles with permission settings
- **Company Management**: Manage company profiles
- **Course Management**: Create and manage courses with syllabus and assignments
- **Student Management**: Add and manage students with assignments
- **Task Management**: Assign and track tasks for users
- **Daily Reports**: Users can submit daily progress reports
- **Attendance Tracking**: Record student attendance
- **Login/Logout**: Track user activity with login/logout timestamps

## Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- XAMPP or similar stack with:
  - Apache server
  - MySQL database
  - `mysqli` extension enabled
  - `json` extension enabled

## Installation

1.  **Database Setup**
    - Import the SQL file to create the database and tables:
      ```sql
      CREATE DATABASE training_center_db;
      USE training_center_db;
      -- Import SQL dump or create tables manually
      ```
    - Run the `sql/training_center.sql` file to create the database and tables:
      ```bash
      mysql -u root -p training_center_db < sql/training_center.sql
      ```
    
2.  **File Structure**
    ```
training_center/
├── main/                   # Core PHP modules
│   ├── db_connect.php      # Database connection
│   ├── validation.php      # Input validation class
│   ├── basic_functions.php # Helper functions
│   ├── login.php         # Authentication functions
│   └── permissions.php     # Permission handling
├── assets/               # CSS, JS, Images
│   ├── css/
│   │   └── style.css       # Main stylesheet
│   └── js/
│       └── script.js       # JavaScript functions
├── sql/                  # SQL database files
│   └── training_center.sql # Complete database schema
├── company.php           # Company management
├── courses.php           # Course management
├── students.php          # Student management
├── tasks.php             # Task management
├── daily_reports.php     # Daily reports
├── attendance.php        # Attendance tracking
├── users.php             # User management
├── registration.php      # New user registration
├── index.php             # Login page
├── dashboard.php         # Dashboard
├── sidebar.php           # Sidebar navigation
├── logout.php            # Logout handler
├── common_file.php       # Session setup and includes
└── config.php            # Configuration
```

3.  **Configure Database**
    - Open `main/db_connect.php` and update database credentials if needed:
      ```php
      $db_server = "localhost";
      $db_user = "root";
      $db_pass = "";        // Change if you have a password
      $db_name = "training_center_db";
      ```

4.  **Configure Paths**
    - Open `config.php` and ensure paths are correct:
      ```php
      $_SESSION['base_path'] = "C:/xampp/htdocs/priya/training_center";
      ```

## Usage

### Running the Application
1. Start Apache and MySQL in XAMPP Control Panel
2. Open your browser and navigate to:
   ```
   http://localhost/priya/training_center/
   ```

### Login
- **Admin Credentials**:
  - Username: `admin`
  - Password: `password`
  
- **Default Manager Credentials**:
  - Username: `manager1`
  - Password: `123456`
  
- **Default Trainer Credentials**:
  - Username: `trainer1`
  - Password: `123456`

### Key Pages

- **`index.php`** - Login page
- **`dashboard.php`** - Main dashboard with role-specific views
- **`users.php`** - View and manage all users
- **`registration.php`** - Add new users (Admin only)
- **`courses.php`** - Create and manage courses
- **`students.php`** - Manage student records
- **`tasks.php`** - View and manage tasks
- **`daily_reports.php`** - View daily reports
- **`attendance.php`** - Track student attendance
- **`company.php`** - Manage company information

### Adding New Users
1. Navigate to **Users** page
2. Click the **Add User** button
3. Fill in the form with user details
4. Click **Create User**

### Creating Courses
1. Navigate to **Courses** page
2. Click **Add Course**
3. Fill in course details
4. Add syllabus topics and assignments
5. Save the course

## Role Permissions

### Admin Role
- Full access to all features
- Can add, edit, and delete any record
- Manage users, roles, permissions
- Create and manage courses, students, tasks, reports, etc.

### Manager Role
- View dashboard and manage assignments
- Create and assign tasks to trainers
- View daily reports and attendance
- Can add and manage students
- Limited access to company and course management

### Trainer Role
- View assigned tasks and assignments
- Submit daily progress reports
- Mark attendance
- View student details
- Restricted access to core management features

## Configuration

### Database Settings
Edit `main/db_connect.php` for database credentials:
```php
$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "training_center_db";
```

### File Paths
Ensure paths are correct in `config.php`:
```php
$_SESSION['base_path'] = "C:/xampp/htdocs/priya/training_center";
```

## Development

### Using the Validation System

Validation is handled by the `validation` class in `main/validation.php`. Common validation methods:

```php
// String validation
$res = $valid->common_validation($variable, 'Field Name', '');

// Numeric validation
$res = $valid->check_number($variable, 'Field Name');

// Date validation
$res = $valid->check_date($variable, 'Field Name');

// Check if exists in database
$res = $valid->check_exists($variable, 'Field Name', $table, $column_name);
```

### Permission Checks

Use `check_permission` function from `main/basic_functions.php`:

```php
// Check if user has permission
if (!$bf->check_permission('add', 'courses')) {
    die("You don't have permission to perform this action");
}
```

## Troubleshooting

### Common Issues

1. **Error: Call to undefined function...**
   - Ensure `main/db_connect.php` is included in all files
   - Check that PHP extensions (`mysqli`, `json`) are enabled

2. **Session not working**
   - Check `common_file.php` for proper session setup
   - Ensure cookies are enabled in browser

3. **Database connection errors**
   - Verify MySQL is running
   - Check credentials in `db_connect.php`
   - Ensure database exists and tables are created

4. **Permissions not working**
   - Verify `check_permission` function is called in protected pages
   - Check role settings in `tc_roles` table

### Debugging Tips

- Enable error reporting in development:
  ```php
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
  ```
  
- Add logging to track function calls:
  ```php
