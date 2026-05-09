function formSubmit(form_name, from_page, to_page, page_name) {
    const form = document.forms[form_name];
    if (!form) {
        console.error("Form not found:", form_name);
        return;
    }

    // Clear previous errors
    const errorSpans = form.querySelectorAll('.error-msg');
    errorSpans.forEach(span => span.innerText = '');
    
    const successDiv = form.querySelector('.success-msg') || document.getElementById('success-msg');
    if (successDiv) {
        successDiv.innerText = '';
        successDiv.classList.add('hidden');
    }

    const formData = new FormData(form);
    if (page_name) {
        formData.append('page_name', page_name);
    }

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn ? submitBtn.innerText : 'Save';
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerText = 'Processing...';
    }

    fetch(from_page, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            if (successDiv) {
                successDiv.innerText = data.message;
                successDiv.classList.remove('hidden');
                successDiv.style.background = 'rgba(16, 185, 129, 0.1)';
                successDiv.style.color = '#10b981';
            }
            
            setTimeout(() => {
                // window.location.href = to_page;
                $('.new_content').hide().html('');
                $('.update_content').show();

                loadData(page_name);
            }, 1000);
        } else {
            if (data.errors && Object.keys(data.errors).length > 0) {
                for (const field in data.errors) {
                    const errorSpan = document.getElementById('error-' + field);
                    if (errorSpan) {
                        errorSpan.innerText = data.errors[field];
                        errorSpan.style.color = '#ef4444';
                    }
                }
            } 
            
            if (data.message) {
                if (successDiv) {
                    successDiv.innerText = data.message;
                    successDiv.classList.remove('hidden');
                    successDiv.style.background = 'rgba(239, 68, 68, 0.1)';
                    successDiv.style.color = '#ef4444';
                } else {
                    alert(data.message);
                }
            }
            
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerText = originalBtnText;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An unexpected error occurred.');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerText = originalBtnText;
        }
    });
}

// Password Visibility Toggle
document.addEventListener('click', function(e) {
    const toggleBtn = e.target.closest('.toggle-password') || (e.target.id === 'toggle-password' ? e.target : null);
    
    if (toggleBtn) {
        const targetId = toggleBtn.getAttribute('data-target') || 'reg-password';
        const passwordInput = document.getElementById(targetId);
        if (!passwordInput) return;

        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Update icon
        const iconName = type === 'password' ? 'eye' : 'eye-off';
        toggleBtn.setAttribute('data-lucide', iconName);
        lucide.createIcons();
    }
});

// Password Requirements Check
function checkPasswordRequirements(password) {
    const requirements = {
        lower: /[a-z]/.test(password),
        upper: /[A-Z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[\W]/.test(password),
        length: password.length >= 8
    };

    for (const req in requirements) {
        const element = document.getElementById('hint-' + req);
        if (element) {
            if (requirements[req]) {
                element.classList.remove('invalid');
                element.classList.add('valid');
                element.querySelector('svg').outerHTML = '<i data-lucide="check-circle-2"></i>';
            } else {
                element.classList.remove('valid');
                element.classList.add('invalid');
                element.querySelector('svg').outerHTML = '<i data-lucide="circle"></i>';
            }
        }
    }
    lucide.createIcons();
}

function ShowPage(page, edit_id = '') {

    $('.update_content').hide();
    $('.new_content').show();

    $.ajax({
        type: 'POST',
        url: page + '_action.php',
        data: {
            ['view_' + page + '_id']: edit_id
        },
        success: function(data) {
            $('.new_content').html(data);
        }
    });
}

// function loadData(page_name) {
//     $.get(page_name + '_action.php?action=list', function(data) {
//         $('#' + page_name + '_list').html(data);
//     });
// }

function deleteRecord(page_name, id) {

    if(confirm('Are you sure you want to delete this record?')) {

        $.post(page_name + '_action.php', {

            action: 'delete',
            id: id

        }, function(response) {

            loadData(page_name);

        });

    }

}

function getCourseDetails(course_id) {

    if(course_id) {

        $.ajax({
            url: 'course_action.php',
            type: 'POST',
            data: {
                action: 'get_details',
                course_id: course_id
            },
            dataType: 'json',

            success: function(response) {

                console.log(response);

                if(response.status === 'success') {

                    $('#course_fee').val(response.data.course_fee);
                    $('#course_duration').val(response.data.course_duration);

                } else {

                    $('#course_fee').val('');
                    $('#course_duration').val('');
                }
            },

            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });

    } else {

        $('#course_fee').val('');
        $('#course_duration').val('');
    }
}
