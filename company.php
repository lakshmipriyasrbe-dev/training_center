<?php require_once 'common_file.php'; 
if ($user_role != 'admin') { header("Location: dashboard.php"); exit(); }

// Fetch existing company details
$company = $bf->getTableRecords($GLOBALS['company_table'], 'deleted', 0);
$comp = !empty($company) ? $company[0] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Details - Training Center</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <h2>Company Configuration</h2>
        </div>

        <div class="module-section" style="max-width: 800px;">
            <div class="section-title">
                Current Details
                <button class="btn-add" onclick="window.location.href='edit_company.php'"><?php echo $comp ? 'Edit Details' : 'Set Details'; ?></button>
            </div>
            
            <?php if ($comp): ?>
            <div style="display: grid; grid-template-columns: 200px 1fr; gap: 1.5rem; margin-top: 2rem;">
                <div style="font-weight: 600; color: var(--text-muted);">Company Name:</div>
                <div style="font-size: 1.1rem;"><?php echo $comp['company_name']; ?></div>
                
                <div style="font-weight: 600; color: var(--text-muted);">Company Email:</div>
                <div><?php echo $comp['company_email']; ?></div>
                
                <div style="font-weight: 600; color: var(--text-muted);">Company Mobile:</div>
                <div><?php echo $comp['company_mobile']; ?></div>
                
                <div style="font-weight: 600; color: var(--text-muted);">Company Address:</div>
                <div style="line-height: 1.6;"><?php echo nl2br($comp['company_address']); ?></div>
            </div>
            <?php else: ?>
            <p style="color: var(--text-muted); margin-top: 2rem;">No company details set yet. Please click the button to add them.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
