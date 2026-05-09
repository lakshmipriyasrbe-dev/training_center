<?php require_once 'common_file.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Reports - Training Center</title>
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
            <div class="section-title">
                Report History
                <button class="btn-add" onclick="window.location.href='add_report.php'">Submit Today's Report</button>
            </div>
            <div id="reportList">
                <p>Loading reports...</p>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            loadReports();
        });

        function loadReports() {
            $.get('report_action.php?action=list', function(data) {
                $('#reportList').html(data);
            });
        }
    </script>
</body>
</html>
