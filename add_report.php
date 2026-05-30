<?php 
require_once 'common_file.php'; 
if ($user_role === 'admin' || $is_management) {
    header("Location: daily_reports.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Report - <?php echo get_company_name(); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <h2>Submit Daily Report</h2>
        </div>

        <div class="module-section" style="max-width: 600px;">
            <form id="reportForm">
                <div class="form-group">
                    <label>Report Date</label>
                    <input type="date" name="report_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label>Activity Details</label>
                    <textarea name="activity_details" rows="8" placeholder="Describe what you worked on today..." required></textarea>
                </div>
                <div class="form-group">
                    <label>Hours Spent</label>
                    <input type="number" name="hours_spent" step="0.5" min="0" max="24" placeholder="e.g. 8" required>
                </div>
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn-add" style="flex: 1;">Submit Report</button>
                    <button type="button" class="btn-add" style="flex: 1; background: #64748b;" onclick="window.location.href='daily_reports.php'">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $('#reportForm').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: 'report_action.php',
                type: 'POST',
                data: $(this).serialize() + '&action=add',
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        window.location.href = 'daily_reports.php';
                    } else {
                        alert(res.message || 'Error submitting report');
                    }
                }
            });
        });
    </script>
</body>
</html>