<?php require_once 'common_file.php'; 
$from_page = 'enrollment_internship';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internship Enrollment Management - <?php echo get_company_name(); ?></title>
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
                Active Internship Enrollments
                <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'enrollment_internship', PERMISSION_ADD)): ?>
                    <button class="btn-add" onclick="ShowPage('enrollment_internship', '')">Add New Enrollment</button>
                <?php endif; ?>
            </div>

            <div class="list-controls">
                <div class="entries-control">
                    Show 
                    <select id="enrollment_internship_limit" onchange="loadData('enrollment_internship', 1, this.value, $('#enrollment_internship_search').val())">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    entries
                </div>
                <div class="search-control">
                    <i class="fas fa-search"></i>
                    <input type="text" id="enrollment_internship_search" placeholder="Search enrollments..." onkeyup="loadData('enrollment_internship', 1, $('#enrollment_internship_limit').val(), this.value)">
                </div>
            </div>

            <div id="enrollment_internship_list">
                <p style="text-align:center; padding: 2rem; color: var(--text-muted);">Loading Enrollments...</p>
            </div>
        </div>
    </div>
    <div class="main-content new_content" style="display: none;">
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div class="modal-content" style="background: white; width: 90%; max-width: 800px; border-radius: 1rem; max-height: 90vh; overflow-y: auto; padding: 2rem; position: relative;">
            <div id="paymentModalBody">
                <p style="text-align:center;">Loading Payment Screen...</p>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            <?php if (isset($_SESSION['from_enquiry'])): ?>
                ShowPage('enrollment_internship', '');
            <?php else: ?>
                loadData('enrollment_internship');
            <?php endif; ?>
        });

        function openPaymentModal(enrollment_id, paid_amount, course_type, student_id) {
            $('#paymentModal').css('display', 'flex');
            $('#paymentModalBody').html('<p style="text-align:center; padding: 2rem;">Loading Payment Screen...</p>');
            
            $.ajax({
                url: 'receipt_action.php',
                type: 'POST',
                data: {
                    action: 'get_payment_modal',
                    enrollment_id: enrollment_id,
                    paid_amount: paid_amount,
                    course_type: course_type,
                    student_id: student_id
                },
                success: function(res) {
                    $('#paymentModalBody').html(res);
                }
            });
        }

        function closePaymentModal() {
            $('#paymentModal').hide();
            $('.new_content').hide().html('');
            $('.update_content').show();
            loadData('enrollment_internship');
        }
    </script>
    <script src="main/js/script.js"></script>
    <script src="main/js/keyboard_control.js"></script>
</body>
</html>
