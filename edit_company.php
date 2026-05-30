<?php require_once 'common_file.php'; 
if ($user_role != 'admin' && !$is_management) { header("Location: dashboard.php"); exit(); }

// Fetch existing company details
$company = $bf->getTableRecords($GLOBALS['company_table'], 'deleted', 0);
$comp = !empty($company) ? $company[0] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Company - <?php echo get_company_name(); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <h2><?php echo $comp ? 'Update Company Details' : 'Set Company Details'; ?></h2>
        </div>
        
        <div class="module-section" style="max-width: 600px;">
            <form id="companyForm">
                <input type="hidden" name="id" value="<?php echo $comp['id'] ?? ''; ?>">
                <div class="form-group">
                    <label>Company Name</label>
                    <input type="text" name="company_name" value="<?php echo $comp['company_name'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Company Email</label>
                    <input type="email" name="company_email" value="<?php echo $comp['company_email'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Company Mobile</label>
                    <input type="text" name="company_mobile" value="<?php echo $comp['company_mobile'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Company Address</label>
                    <textarea name="company_address" rows="5"><?php echo $comp['company_address'] ?? ''; ?></textarea>
                </div>
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn-add" style="flex: 1;">Save Details</button>
                    <button type="button" class="btn-add" style="flex: 1; background: #64748b;" onclick="window.location.href='company.php'">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $('#companyForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: 'company_action.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.trim() == 'Success') {
                        window.location.href = 'company.php';
                    } else {
                        alert(response);
                    }
                }
            });
        });
    </script>
</body>
</html>
