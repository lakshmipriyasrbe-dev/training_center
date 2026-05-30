<?php require_once 'common_file.php'; 
$from_page = 'course_enquiry';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Enquiry Management - <?php echo get_company_name(); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content update_content">
        <div class="header">
            <h2>Course Enquiry</h2>
        </div>

        <div class="module-section">
            <div class="section-title">
                Active Enquiries
                <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'course_enquiry', PERMISSION_ADD)): ?>
                    <button class="btn-add" onclick="ShowPage('course_enquiry', '')">Add New Enquiry</button>
                <?php endif; ?>
            </div>

            <div class="list-controls">
                <div class="entries-control">
                    Show 
                    <select id="course_enquiry_limit" onchange="loadData('course_enquiry', 1, this.value, $('#course_enquiry_search').val())">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    entries
                </div>
                <div class="search-control">
                    <i class="fas fa-search"></i>
                    <input type="text" id="course_enquiry_search" placeholder="Search enquiries..." onkeyup="loadData('course_enquiry', 1, $('#course_enquiry_limit').val(), this.value)">
                </div>
            </div>

            <div id="course_enquiry_list">
                <p style="text-align:center; padding: 2rem; color: var(--text-muted);">Loading Enquiries...</p>
            </div>
        </div>
    </div>
    
    <div class="main-content new_content" style="display: none;">
    </div>

    <!-- Conversion Selection Modal -->
    <div id="convertModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div class="modal-content" style="background: white; width: 90%; max-width: 450px; border-radius: 1rem; padding: 2rem; position: relative; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 1.5rem; color: var(--text-dark); font-family: 'Outfit', sans-serif; font-weight: 600;">Convert Course Enquiry</h3>
            <p style="color: var(--text-muted); margin-bottom: 2rem; font-family: 'Outfit', sans-serif;">Please select the type of enrollment to convert this enquiry into:</p>
            <input type="hidden" id="convert_enquiry_id" value="">
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <button onclick="executeConversion('training')" class="btn-add" style="background: #10b981; padding: 0.75rem; font-size: 1rem; width: 100%; border-radius: 0.5rem; border: none; color: white; font-family: 'Outfit', sans-serif; font-weight: 600; cursor: pointer;">Training Enrollment</button>
                <button onclick="executeConversion('internship')" class="btn-add" style="background: #3b82f6; padding: 0.75rem; font-size: 1rem; width: 100%; border-radius: 0.5rem; border: none; color: white; font-family: 'Outfit', sans-serif; font-weight: 600; cursor: pointer;">Internship Enrollment</button>
                <button onclick="closeConvertModal()" class="btn-add" style="background: #ef4444; padding: 0.75rem; font-size: 1rem; width: 100%; border-radius: 0.5rem; border: none; color: white; font-family: 'Outfit', sans-serif; font-weight: 600; cursor: pointer;">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            loadData('course_enquiry');
        });

        function convertEnquiry(id) {
            $('#convert_enquiry_id').val(id);
            $('#convertModal').css('display', 'flex');
        }

        function closeConvertModal() {
            $('#convertModal').hide();
        }

        function executeConversion(type) {
            const id = $('#convert_enquiry_id').val();
            $.ajax({
                type: 'POST',
                url: 'course_enquiry_action.php',
                data: {
                    action: 'set_conversion_session',
                    enquiry_id: id
                },
                success: function(res) {
                    if (type === 'training') {
                        window.location.href = 'enrollment.php';
                    } else if (type === 'internship') {
                        window.location.href = 'enrollment_internship.php';
                    }
                }
            });
        }
    </script>
    <script src="main/js/script.js"></script>
    <script src="main/js/keyboard_control.js"></script>
</body>
</html>
