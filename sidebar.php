<div class="sidebar">
    <div class="logo">TC Management</div>
    <ul class="nav-links">
        <li class="nav-item"><a href="dashboard.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a></li>
        <?php if ($user_role == 'admin'): ?>
        <li class="nav-item"><a href="company.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'company.php') ? 'active' : ''; ?>">Company Details</a></li>
        <li class="nav-item"><a href="payment_mode.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'payment_mode.php') ? 'active' : ''; ?>">Payment Mode</a></li>
        <li class="nav-item"><a href="bank.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'bank.php') ? 'active' : ''; ?>">Bank</a></li>
        <li class="nav-item"><a href="course.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'course.php') ? 'active' : ''; ?>">Course</a></li>
        <li class="nav-item"><a href="enrollment.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'enrollment.php') ? 'active' : ''; ?>">Enrollment</a></li>
       
        <li class="nav-item"><a href="users.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'users.php') ? 'active' : ''; ?>">User Management</a></li>
        <li class="nav-item"><a href="roles.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'roles.php') ? 'active' : ''; ?>">Role Management</a></li>
        <li class="nav-item"><a href="staff.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'staff.php') ? 'active' : ''; ?>">Staff Management</a></li>
        <?php endif; ?>
        <li class="nav-item"><a href="tasks.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'tasks.php') ? 'active' : ''; ?>">Tasks</a></li>
        <li class="nav-item"><a href="daily_reports.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'daily_reports.php') ? 'active' : ''; ?>">Daily Reports</a></li>
        
        <li class="nav-item" style="margin-top: 2rem;">
            <a href="logout.php" class="nav-link" style="color: #ef4444; background: rgba(239, 68, 68, 0.05);">
                Logout
            </a>
        </li>
    </ul>
</div>
