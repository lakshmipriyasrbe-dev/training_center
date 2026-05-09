<?php require_once 'common_file.php'; 
if ($user_role != 'admin') { header("Location: dashboard.php"); exit(); }
$from_page = 'payment_mode';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Mode - Training Center</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content update_content">
        <div class="header">
            <h2><?php if(!empty($from_page))  echo ucfirst(str_replace('_', ' ', $from_page)); ?></h2>
        </div>

        <div class="module-section">
            <div class="section-title">
                Active Payment Modes
                <button class="btn-add" onclick="Javacript:ShowPage('<?php echo $from_page; ?>', '')">Add New Payment Mode</button>
            </div>
            <div id="<?php echo $from_page; ?>_list">
                <p>Loading payment modes...</p>
            </div>
        </div>
    </div>
    <div class="main-content new_content" style="display: none;"></div>

    <script>
        $(document).ready(function() {
            loadData('payment_mode');
        });

        function loadData(page_name) {
            $.get(page_name + '_action.php?action=list', function(data) {
                $('#' + page_name + '_list').html(data);
            });
        }
    </script>
    <script src="main/js/script.js"></script>
    <script src="main/js/keyboard_control.js"></script>
</body>
</html>
