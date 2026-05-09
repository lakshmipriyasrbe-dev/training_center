<?php require_once 'common_file.php'; 
if ($user_role != 'admin') { header("Location: dashboard.php"); exit(); }
$from_page = 'staff';

// echo $bf->encode_decode('decrypt', 'Qy9DUkxUbmd3SldjYm9OY1ZTNmRlV0ZHS0JuR3MwbkN0SmF2cEZZQjJJZz0=');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management - Training Center</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include Select2 CSS and JS for multiselect if not already included in style.css or here -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Custom styles for Select2 to match the theme if necessary */
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            min-height: 42px;
            padding: 4px;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content update_content">
        <div class="header">
            <h2><?php if(!empty($from_page))  echo ucfirst($from_page); ?></h2>
        </div>

        <div class="module-section">
            <div class="section-title">
                Active Staff
                <button class="btn-add" onclick="Javacript:ShowPage('<?php echo $from_page; ?>', '')">Add New Staff</button>
            </div>
            <div id="<?php echo $from_page; ?>_list">
                <p>Loading Staff...</p>
            </div>
        </div>
    </div>
    <div class="main-content new_content" style="display: none;">
    </div>

    <script>
        $(document).ready(function() {
            loadData('staff');
        });

        function loadData(page_name) {
            $.get(page_name + '_action.php?action=list', function(data) {
                $('#' + page_name + '_list').html(data);
            });
        }

        function deleteRecord(page_name, id) {
            if (confirm('Are you sure you want to delete this ' + page_name + '?')) {
                $.post(page_name + '_action.php', { action: 'delete', id: id }, function() {
                    loadData(page_name);
                });
            }
        }
        
    </script>
    <script src="main/js/script.js"></script>
    <script src="main/js/keyboard_control.js"></script>
</body>
</html>
