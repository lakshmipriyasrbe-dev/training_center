<?php require_once 'common_file.php'; 
if ($user_role != 'admin') { header("Location: dashboard.php"); exit(); }
$from_page = 'receipt';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt Management - Training Center</title>
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
                Active Receipts
                <button class="btn-add" onclick="ShowPage('receipt', '')">Add New Receipt</button>
            </div>
            <div class="list-controls">
                <div class="entries-control">
                    Show 
                    <select id="receipt_limit" onchange="loadData('receipt', 1, this.value, $('#receipt_search').val())">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    entries
                </div>
                <div class="search-control">
                    <i class="fas fa-search"></i>
                    <input type="text" id="receipt_search" placeholder="Search receipts..." onkeyup="loadData('receipt', 1, $('#receipt_limit').val(), this.value)">
                </div>
            </div>

            <div id="receipt_list">
                <p style="text-align:center; padding: 2rem; color: var(--text-muted);">Loading Receipts...</p>
            </div>
        </div>
    </div>
    <div class="main-content new_content" style="display: none;"></div>

    <script>
        $(document).ready(function() {
            loadData('receipt');
        });
    </script>
    <script src="main/js/script.js"></script>
    <script src="main/js/keyboard_control.js"></script>
</body>
</html>
