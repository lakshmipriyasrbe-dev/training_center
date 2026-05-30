<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* New Sidebar Grouping Styles */
    .sidebar {
        box-sizing: border-box;
        overflow-y: auto;
    }

    .sidebar::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
    }

    .sidebar::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar *, .sidebar *::before, .sidebar *::after {
        box-sizing: border-box;
    }

    .sidebar .nav-links {
        margin-top: 1rem;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    
    .sidebar .nav-item {
        width: 100%;
    }

    .sidebar .menu-toggle {
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .sidebar .submenu {
        list-style: none;
        padding: 0;
        margin: 0;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease-in-out, opacity 0.3s ease-in-out;
        background: rgba(0, 0, 0, 0.05);
        border-radius: 0.5rem;
        opacity: 0;
    }

    .sidebar .submenu.show {
        max-height: 500px; /* Reduced from 1000px for better performance */
        opacity: 1;
        margin-bottom: 5px;
    }

    .sidebar .submenu .nav-link {
        padding-left: 3rem;
        font-size: 0.85rem;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.7);
    }

    .sidebar .submenu .nav-link:hover {
        color: #fff;
        background: rgba(255, 255, 255, 0.05);
    }

    .sidebar .submenu .nav-link.active {
        color: #fff;
        background: rgba(255, 255, 255, 0.1);
    }

    .sidebar .arrow {
        font-size: 0.75rem;
        transition: transform 0.3s ease;
        opacity: 0.6;
    }

    .sidebar .menu-toggle.open .arrow {
        transform: rotate(90deg);
    }

    .sidebar .menu-label {
        flex-grow: 1;
        display: flex;
        align-items: center;
    }

    /* Active parent highlight */
    .sidebar .menu-toggle.parent-active {
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
    }
</style>

<button class="mobile-toggle" id="sidebarToggle">
    <i class="fas fa-bars"></i>
</button>

<div class="sidebar" id="sidebar">
    <div class="logo">
         <img src="main/images/logo.png" alt="Logo">
    </div>
    
    <?php 
    $current_page = basename($_SERVER['PHP_SELF']); 
    
    // Group definitions for active states
    $students_pages = ['enrollment.php', 'enrollment_internship.php', 'course_enquiry.php'];
    $payroll_pages = ['attendance.php', 'payroll.php'];
    $payments_pages = ['receipt.php', 'payment_mode.php', 'bank.php'];
    $course_pages = ['course.php', 'course_closure.php', 'tasks.php', 'event.php'];
    $creation_pages = ['company.php', 'users.php', 'roles.php', 'staff.php'];
    $reports_pages = ['report_enrollment.php', 'report_payroll.php', 'report_payments.php', 'report_attendance.php', 'report_placement.php'];
    $expenses_page = ['expense_category.php', 'expense_entry.php'];
    ?>

    <ul class="nav-links">
        <?php 
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'director') {
            $is_dash_active = ($current_page === 'director_dashboard.php') ? 'active' : '';
            $is_profile_active = ($current_page === 'director_profile.php') ? 'active' : '';
            ?>
            <li class="nav-item">
                <a href="director_dashboard.php" class="nav-link <?php echo $is_dash_active; ?>">
                    <i class="fas fa-fw fa-tachometer-alt"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="director_profile.php" class="nav-link <?php echo $is_profile_active; ?>">
                    <i class="fas fa-fw fa-user-cog"></i> <span>Profile Settings</span>
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
            // Mobile Sidebar Toggle
            document.getElementById('sidebarToggle')?.addEventListener('click', function() {
                document.getElementById('sidebar').classList.toggle('active');
            });
            </script>
            <?php
            return;
        }

        require_once 'permission_helper.php';
        $sidebar_company_id = $_SESSION['company_id'] ?? '';
        $sidebar_role_id = $_SESSION['role_id'] ?? 0;
        
        // Ensure modules array is available
        if (!isset($modules)) {
            global $modules;
        }

        $urlMap = [
            'dashboard' => 'dashboard.php',
            'company' => 'company.php',
            'roles' => 'role.php',
            'users' => 'users.php',
            'staff' => 'staff.php',
            'course' => 'course.php',
            'course_closure' => 'course_closure.php',
            'tasks' => (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'student') ? 'student_tasks.php' : 'tasks.php',
            'event' => 'event.php',
            'enrollment' => 'enrollment.php',
            'enrollment_internship' => 'enrollment_internship.php',
            'course_enquiry' => 'course_enquiry.php',
            'attendance' => 'attendance.php',
            'payroll' => 'payroll.php',
            'receipt' => 'receipt.php',
            'payment_mode' => 'payment_mode.php',
            'bank' => 'bank.php',
            'expense_category' => 'expense_category.php',
            'expense_entry' => 'expense_entry.php',
            'daily_reports' => 'daily_reports.php',
            'report_enrollment' => 'report_enrollment.php',
            'report_payroll' => 'report_payroll.php',
            'report_payments' => 'report_payments.php',
            'report_attendance' => 'report_attendance.php',
            'report_placement' => 'report_placement.php',
            'student_attendance' => 'student_attendance.php',
            'student_tasks' => 'student_tasks.php',
            'student_reports' => 'student_reports.php',
            'daily_report' => 'daily_report.php',
            'student_profile' => 'student_profile.php'
        ];

        // Defined Menu Structure matching exact HTML Categories
        $menu_structure = [
            [
                'title' => 'Dashboard',
                'icon' => 'fas fa-fw fa-tachometer-alt',
                'pages' => ['dashboard'],
                'dropdown' => false
            ],
            [
                'title' => 'Creation',
                'icon' => 'fas fa-fw fa-users-cog',
                'pages' => ['company', 'roles', 'users', 'staff'],
                'dropdown' => true,
                'menu_id' => 'creation-menu'
            ],
            [
                'title' => 'Management',
                'icon' => 'fas fa-fw fa-tasks',
                'pages' => ['course', 'course_closure', 'tasks', 'event'],
                'dropdown' => true,
                'menu_id' => 'management-menu'
            ],
            [
                'title' => 'Students',
                'icon' => 'fas fa-fw fa-user-graduate',
                'pages' => ['enrollment', 'enrollment_internship', 'course_enquiry'],
                'dropdown' => true,
                'menu_id' => 'students-menu'
            ],
            [
                'title' => 'Payroll',
                'icon' => 'fas fa-fw fa-money-check-alt',
                'pages' => ['attendance', 'payroll'],
                'dropdown' => true,
                'menu_id' => 'payroll-menu'
            ],
            [
                'title' => 'Payments',
                'icon' => 'fas fa-fw fa-file-invoice-dollar',
                'pages' => ['payment_mode', 'bank', 'receipt'],
                'dropdown' => true,
                'menu_id' => 'payments-menu'
            ],
            [
                'title' => 'Expense',
                'icon' => 'fas fa-fw fa-wallet',
                'pages' => ['expense_category', 'expense_entry'],
                'dropdown' => true,
                'menu_id' => 'expense-menu'
            ],
            [
                'title' => 'Daily Reports',
                'icon' => 'fas fa-fw fa-calendar-day',
                'pages' => ['daily_reports'],
                'dropdown' => false
            ],
            [
                'title' => 'Reports',
                'icon' => 'fas fa-fw fa-chart-line',
                'pages' => ['report_enrollment', 'report_payroll', 'report_payments', 'report_attendance', 'report_placement'],
                'dropdown' => true,
                'menu_id' => 'reports-menu'
            ],
            [
                'title' => 'Student Attendance',
                'icon' => 'fas fa-fw fa-user-check',
                'pages' => ['student_attendance'],
                'dropdown' => false
            ],
            [
                'title' => 'Student Tasks',
                'icon' => 'fas fa-fw fa-tasks',
                'pages' => ['student_tasks'],
                'dropdown' => false
            ],
            [
                'title' => 'Student Reports',
                'icon' => 'fas fa-fw fa-file-invoice',
                'pages' => ['student_reports'],
                'dropdown' => false
            ],
            [
                'title' => 'Daily Report',
                'icon' => 'fas fa-fw fa-calendar-day',
                'pages' => ['daily_report'],
                'dropdown' => false
            ],
            [
                'title' => 'My Profile',
                'icon' => 'fas fa-fw fa-user-cog',
                'pages' => ['student_profile'],
                'dropdown' => false
            ]
        ];

        foreach ($menu_structure as $item) {
            // Find permitted pages in this block
            $allowed_pages = [];
            foreach ($item['pages'] as $page_key) {
                if (checkPermission($sidebar_company_id, $sidebar_role_id, $page_key, PERMISSION_VIEW)) {
                    $allowed_pages[] = $page_key;
                }
            }

            // Skip rendering if no pages are allowed
            if (empty($allowed_pages)) {
                continue;
            }

            if (!$item['dropdown']) {
                // Render Direct single links
                $page_key = $allowed_pages[0];
                $url = $urlMap[$page_key] ?? ($page_key . '.php');
                $isActive = ($current_page == $url) ? 'active' : '';
                ?>
                <li class="nav-item">
                    <a href="<?php echo $url; ?>" class="nav-link <?php echo $isActive; ?>">
                        <i class="<?php echo $item['icon']; ?>"></i> <span><?php echo $modules[$page_key] ?? $item['title']; ?></span>
                    </a>
                </li>
                <?php
            } else {
                // Render Dropdowns with children
                $isParentActive = false;
                foreach ($allowed_pages as $page_key) {
                    $url = $urlMap[$page_key] ?? ($page_key . '.php');
                    if ($current_page == $url) {
                        $isParentActive = true;
                        break;
                    }
                }
                ?>
                <li class="nav-item">
                    <a href="javascript:void(0)" class="nav-link menu-toggle <?php echo $isParentActive ? 'parent-active open' : ''; ?>" data-target="<?php echo $item['menu_id']; ?>">
                        <span class="menu-label"><i class="<?php echo $item['icon']; ?>"></i> <?php echo $item['title']; ?></span>
                        <i class="fas fa-chevron-right arrow"></i>
                    </a>
                    <ul class="submenu <?php echo $isParentActive ? 'show' : ''; ?>" id="<?php echo $item['menu_id']; ?>">
                        <?php foreach ($allowed_pages as $page_key): 
                            $url = $urlMap[$page_key] ?? ($page_key . '.php');
                            $isActive = ($current_page == $url) ? 'active' : '';
                            ?>
                            <li>
                                <a href="<?php echo $url; ?>" class="nav-link <?php echo $isActive; ?>">
                                    <?php echo $modules[$page_key] ?? $page_key; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <?php
            }
        }
        ?>
        
        <li class="nav-item logout-link" style="margin-top: auto; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 0.5rem;">
            <a href="logout.php" class="nav-link" style="color: #ef4444; font-weight: 700;">
                <i class="fas fa-fw fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>

<script>
// Mobile Sidebar Toggle
document.getElementById('sidebarToggle').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('active');
});

// Menu Toggle (Accordion)
document.querySelectorAll('.menu-toggle').forEach(toggle => {
    toggle.addEventListener('click', function(e) {
        const targetId = this.getAttribute('data-target');
        const submenu = document.getElementById(targetId);
        
        // Close other submenus
        document.querySelectorAll('.submenu').forEach(sub => {
            if (sub.id !== targetId) {
                sub.classList.remove('show');
                sub.previousElementSibling.classList.remove('open');
            }
        });
        
        // Toggle current
        submenu.classList.toggle('show');
        this.classList.toggle('open');
    });
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

