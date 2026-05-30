<?php require_once 'common_file.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Reports - <?php echo get_company_name(); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <h2>Daily Activity Reports</h2>
        </div>

        <div class="module-section">

            <?php 
            if($user_role != 'admin' && !$is_management) {    ?>
                <div class="section-title">
                    Report History
                    <button class="btn-add" onclick="window.location.href='add_report.php'">Submit Today's Report</button>
                </div>
            <?php } ?>
            
            <div class="list-controls">
                <div class="entries-control">
                    Show 
                    <select id="report_limit" onchange="loadData('report', 1, this.value, $('#report_search').val())">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    entries
                </div>
                <div class="search-control">
                    <i class="fas fa-search"></i>
                    <input type="text" id="report_search" placeholder="Search reports..." onkeyup="loadData('report', 1, $('#report_limit').val(), this.value)">
                </div>
            </div>

            <div id="report_list">
                <p style="text-align:center; padding: 2rem; color: var(--text-muted);">Loading Reports...</p>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            loadData('report');
        });
    </script>
    <script src="main/js/script.js"></script>
</body>
</html>
