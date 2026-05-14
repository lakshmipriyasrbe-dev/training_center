<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<button class="mobile-toggle" id="sidebarToggle">
    <i class="fas fa-bars"></i>
</button>

<div class="sidebar" id="sidebar">
    <div class="logo">
        <!-- <i class="fas fa-graduation-cap"></i> TC Admin -->
         <img src="main/images/logo.png" alt="Logo">
    </div>
    <ul class="nav-links">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">
                <i class="fas fa-fw fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        <?php if ($user_role == 'admin'): ?>
        <li class="nav-item">
            <a href="company.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'company.php') ? 'active' : ''; ?>">
                <i class="fas fa-fw fa-building"></i> Company Details
            </a>
        </li>
        <li class="nav-item">
            <a href="payment_mode.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'payment_mode.php') ? 'active' : ''; ?>">
                <i class="fas fa-fw fa-credit-card"></i> Payment Mode
            </a>
        </li>
        <li class="nav-item">
            <a href="bank.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'bank.php') ? 'active' : ''; ?>">
                <i class="fas fa-fw fa-university"></i> Bank
            </a>
        </li>
        <li class="nav-item">
            <a href="course.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'course.php') ? 'active' : ''; ?>">
                <i class="fas fa-fw fa-book"></i> Course
            </a>
        </li>
        <li class="nav-item">
            <a href="enrollment.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'enrollment.php') ? 'active' : ''; ?>">
                <i class="fas fa-fw fa-user-graduate"></i> Enrollment - Training
            </a>
        </li>
        <li class="nav-item">
            <a href="enrollment_internship.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'enrollment_internship.php') ? 'active' : ''; ?>">
                <i class="fas fa-fw fa-user-tie"></i> Enrollment - Internship
            </a>
        </li>
        <li class="nav-item">
            <a href="receipt.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'receipt.php') ? 'active' : ''; ?>">
                <i class="fas fa-fw fa-file-invoice-dollar"></i> Receipts
            </a>
        </li>
        <li class="nav-item">
            <a href="payroll.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'payroll.php') ? 'active' : ''; ?>">
                <i class="fas fa-fw fa-money-check-alt"></i> Payroll
            </a>
        </li>
        
        <li class="nav-item">
            <a href="users.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'users.php') ? 'active' : ''; ?>">
                <i class="fas fa-fw fa-users-cog"></i> User Management
            </a>
        </li>
        <li class="nav-item">
            <a href="roles.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'roles.php') ? 'active' : ''; ?>">
                <i class="fas fa-fw fa-user-shield"></i> Role Management
            </a>
        </li>
        <li class="nav-item">
            <a href="staff.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'staff.php') ? 'active' : ''; ?>">
                <i class="fas fa-fw fa-id-badge"></i> Staff Management
            </a>
        </li>
        <li class="nav-item">
            <a href="attendance.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'attendance.php') ? 'active' : ''; ?>">
                <i class="fas fa-fw fa-clipboard-check"></i> Attendance
            </a>
        </li>
        <li class="nav-item">
            <a href="course_closure.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'course_closure.php') ? 'active' : ''; ?>">
                <i class="fas fa-fw fa-folder-minus"></i> Course Closure
            </a>
        </li>
        <?php endif; ?>
        <li class="nav-item">
            <a href="tasks.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'tasks.php') ? 'active' : ''; ?>">
                <i class="fas fa-fw fa-tasks"></i> Tasks
            </a>
        </li>
        <li class="nav-item">
            <a href="daily_reports.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'daily_reports.php') ? 'active' : ''; ?>">
                <i class="fas fa-fw fa-calendar-day"></i> Daily Reports
            </a>
        </li>        
        
        <li class="nav-item logout-link" style="margin-top: auto; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 0.5rem;">
            <a href="logout.php" class="nav-link" style="color: #ef4444; font-weight: 700;">
                <i class="fas fa-fw fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>

<script>
document.getElementById('sidebarToggle').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('active');
});

// Close sidebar when clicking outside on mobile
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebarToggle');
    if (window.innerWidth <= 992) {
        if (!sidebar.contains(event.target) && !toggle.contains(event.target) && sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
        }
    }
});
</script>

