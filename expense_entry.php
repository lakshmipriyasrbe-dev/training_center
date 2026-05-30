<?php require_once 'common_file.php'; 
$from_page = 'Expense Entry';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Entry Management - <?php echo get_company_name(); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content update_content">
        <div class="header">
            <h2><?php if(!empty($from_page)) echo ucfirst($from_page); ?></h2>
        </div>

        <div class="module-section">
            <div class="section-title">
                Active Expense Entries
                <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'expense_entry', PERMISSION_ADD)): ?>
                    <button class="btn-add" onclick="ShowPage('expense_entry', '')">Add New Expense Entry</button>
                <?php endif; ?>
            </div>
            <div class="list-controls">
                <div class="entries-control">
                    Show 
                    <select id="expense_entry_limit" onchange="loadData('expense_entry', 1, this.value, $('#expense_entry_search').val())">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    entries
                </div>
                <div class="search-control">
                    <i class="fas fa-search"></i>
                    <input type="text" id="expense_entry_search" placeholder="Search expense entry..." onkeyup="loadData('expense_entry', 1, $('#expense_entry_limit').val(), this.value)">
                </div>
            </div>

            <div id="expense_entry_list">
                <p style="text-align:center; padding: 2rem; color: var(--text-muted);">Loading Expense Entry...</p>
            </div>
        </div>
    </div>
    <div class="main-content new_content" style="display: none;"></div>

    <script>
        $(document).ready(function() {
            loadData('expense_entry');
        });
    </script>
    <script src="main/js/script.js"></script>
    <script src="main/js/keyboard_control.js"></script>
</body>
</html>
