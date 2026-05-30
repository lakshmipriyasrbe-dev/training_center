<?php 
require_once 'common_file.php'; 
$from_page = 'student_reports';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Reports Review - Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content update_content">
        <div class="header">
            <h2>Student Daily Reports Evaluation</h2>
            <div class="user-profile">
                <span><?php echo $username; ?></span>
                <div class="avatar"><?php echo substr($username, 0, 1); ?></div>
            </div>
        </div>

        <div class="module-section">
            <div class="section-title">
                Review Student Submissions
            </div>

            <div class="list-controls" style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
                <div style="display:flex; gap:0.5rem; align-items:center;">
                    Show 
                    <select id="student_reports_limit" onchange="loadData('student_reports', 1, this.value, $('#student_reports_search').val(), $('#student_reports_status').val())">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    entries
                    
                    <select id="student_reports_status" onchange="loadData('student_reports', 1, $('#student_reports_limit').val(), $('#student_reports_search').val(), this.value)" style="padding:0.4rem; border: 1.5px solid #e2e8f0; border-radius:6px; font-family:inherit; font-size:0.85rem; color:#475569; margin-left:1rem;">
                        <option value="">All Statuses</option>
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                    </select>
                </div>
                <div class="search-control">
                    <i class="fas fa-search"></i>
                    <input type="text" id="student_reports_search" placeholder="Search ID, task, text..." onkeyup="loadData('student_reports', 1, $('#student_reports_limit').val(), this.value, $('#student_reports_status').val())">
                </div>
            </div>

            <div id="student_reports_list">
                <p style="text-align:center; padding: 2rem; color: var(--text-muted);">Loading Student Submissions...</p>
            </div>
        </div>
    </div>

    <!-- Evaluation Modal -->
    <div id="reviewModal" class="modal">
        <div class="modal-content" style="max-width: 480px;">
            <h3 style="margin-top:0; margin-bottom: 1rem; color: var(--primary);" id="review_student_title">Evaluate Daily Report</h3>
            <p style="font-size:0.8rem; color:var(--text-muted); margin-bottom: 1.5rem; margin-top:0;">Provide evaluation comments and update submission approval status.</p>
            
            <form id="review_form" onsubmit="submitReview(event)">
                <input type="hidden" name="action" value="approve_report">
                <input type="hidden" id="review_report_id" name="id">

                <div class="form-group">
                    <label>Evaluation / Feedback Remarks</label>
                    <textarea name="remarks" id="review_remarks" class="form-input" style="height:100px; resize:none;" placeholder="Excellent work / Need updates on milestone X / etc."></textarea>
                </div>

                <div class="form-group">
                    <label>Status *</label>
                    <select name="status" id="review_status" class="form-input" required>
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                    </select>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:0.5rem; margin-top: 1.5rem;">
                    <button type="button" class="btn-add" style="background:#64748b;" onclick="$('#reviewModal').hide()">Cancel</button>
                    <button type="submit" class="btn-add">Save Evaluation</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            loadData('student_reports');
        });

        function openReviewModal(id, studentId, remarks, status) {
            $('#review_report_id').val(id);
            $('#review_student_title').text('Evaluate Report - ' + studentId);
            $('#review_remarks').val(remarks);
            $('#review_status').val(status);
            $('#reviewModal').css('display', 'flex');
        }

        function submitReview(e) {
            e.preventDefault();
            $.post('student_reports_action.php', $('#review_form').serialize(), function(response) {
                try {
                    let res = JSON.parse(response);
                    if(res.status === 'success') {
                        $('#reviewModal').hide();
                        loadData('student_reports');
                    } else {
                        alert(res.message || 'Error saving review');
                    }
                } catch(err) {
                    alert('Server error occurred.');
                }
            });
        }
    </script>
    <script src="main/js/script.js"></script>
    <script src="main/js/keyboard_control.js"></script>
</body>
</html>
