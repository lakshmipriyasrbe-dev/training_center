<?php require_once 'common_file.php'; 
if ($user_role != 'admin' && !$is_management) { header("Location: dashboard.php"); exit(); }
$from_page = 'company';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company - <?php echo get_company_name(); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            min-height: 42px;
            padding: 4px;
        }
        .logo-preview {
            max-width: 150px;
            max-height: 150px;
            margin-top: 1rem;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content update_content">
        <div class="header">
            <h2><?php if(!empty($from_page))  echo ucfirst(str_replace('_', ' ', $from_page)); ?></h2>
        </div>

        <div class="module-section">
            <div class="section-title">
                Active Companies
                <button class="btn-add" onclick="ShowPage('company', '')">Add New Company</button>
            </div>

            <div class="list-controls">
                <div class="entries-control">
                    Show 
                    <select id="company_limit" onchange="loadData('company', 1, this.value, $('#company_search').val())">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    entries
                </div>
                <div class="search-control">
                    <i class="fas fa-search"></i>
                    <input type="text" id="company_search" placeholder="Search companies..." onkeyup="loadData('company', 1, $('#company_limit').val(), this.value)">
                </div>
            </div>

            <div id="company_list">
                <p style="text-align:center; padding: 2rem; color: var(--text-muted);">Loading Companies...</p>
            </div>
        </div>
    </div>
    <div class="main-content new_content" style="display: none;"></div>

    <script>
        $(document).ready(function() {
            loadData('company');
        });
    </script>
    <script src="main/js/script.js"></script>
    <script src="main/js/keyboard_control.js"></script>
</body>
</html>
