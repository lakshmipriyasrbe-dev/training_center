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
        } else if (data.status === 'success_with_payment') {
            if (successDiv) {
                successDiv.innerText = data.message;
                successDiv.classList.remove('hidden');
                successDiv.style.background = 'rgba(59, 130, 246, 0.1)';
                successDiv.style.color = '#3b82f6';
            }
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerText = originalBtnText;
            }
            if (typeof openPaymentModal === 'function') {
                openPaymentModal(data.enrollment_id, data.paid_amount, data.course_type, data.student_id);
            }
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

// Global Password Visibility & Requirements Validator
function initPasswordFields() {
    const passwordInputs = document.querySelectorAll('input[type="password"]:not(.pass-initialized)');
    
    passwordInputs.forEach(function(input) {
        input.classList.add('pass-initialized');
        
        // 1. Wrap the input in a relative positioned container for precise icon placement
        const wrapper = document.createElement('div');
        wrapper.className = 'password-input-wrapper';
        wrapper.style.position = 'relative';
        wrapper.style.display = 'block';
        wrapper.style.width = '100%';
        
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);
        
        input.style.paddingRight = '40px';
        
        // 2. Append modern SVG Eye Toggle Button
        const toggleBtn = document.createElement('button');
        toggleBtn.type = 'button';
        toggleBtn.className = 'password-toggle-btn';
        toggleBtn.style.position = 'absolute';
        toggleBtn.style.right = '12px';
        toggleBtn.style.top = '50%';
        toggleBtn.style.transform = 'translateY(-50%)';
        toggleBtn.style.background = 'none';
        toggleBtn.style.border = 'none';
        toggleBtn.style.cursor = 'pointer';
        toggleBtn.style.padding = '0';
        toggleBtn.style.display = 'flex';
        toggleBtn.style.alignItems = 'center';
        toggleBtn.style.justifyContent = 'center';
        toggleBtn.style.color = '#64748b';
        toggleBtn.style.zIndex = '10';
        
        const eyeSVG = `
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
        `;
        
        const eyeOffSVG = `
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye-off">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
            </svg>
        `;
        
        toggleBtn.innerHTML = eyeSVG;
        wrapper.appendChild(toggleBtn);
        
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const currentType = input.getAttribute('type');
            if (currentType === 'password') {
                input.setAttribute('type', 'text');
                toggleBtn.innerHTML = eyeOffSVG;
            } else {
                input.setAttribute('type', 'password');
                toggleBtn.innerHTML = eyeSVG;
            }
        });
        
        // 3. Skip requirements checklist for Login forms
        const isLoginForm = input.closest('#loginForm') !== null;
        if (isLoginForm) {
            return;
        }
        
        // 4. Create and append the Password Requirements Checklist dropdown
        const checklist = document.createElement('div');
        checklist.className = 'password-checklist-container';
        checklist.style.marginTop = '0.5rem';
        checklist.style.display = 'none';
        checklist.style.fontSize = '0.75rem';
        checklist.style.border = '1px solid #e2e8f0';
        checklist.style.borderRadius = '0.5rem';
        checklist.style.padding = '0.75rem';
        checklist.style.background = '#f8fafc';
        checklist.style.boxShadow = '0 1px 2px 0 rgba(0, 0, 0, 0.05)';
        
        checklist.innerHTML = `
            <div style="font-weight: 600; color: #475569; margin-bottom: 0.4rem;">Password Requirements:</div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.35rem 0.75rem;">
                <div class="req-item req-capital" style="color: #ef4444; display: flex; align-items: center; gap: 0.25rem;">✗ 1 Capital Letter</div>
                <div class="req-item req-number" style="color: #ef4444; display: flex; align-items: center; gap: 0.25rem;">✗ 1 Number</div>
                <div class="req-item req-special" style="color: #ef4444; display: flex; align-items: center; gap: 0.25rem;">✗ 1 Special Char</div>
                <div class="req-item req-length" style="color: #ef4444; display: flex; align-items: center; gap: 0.25rem;">✗ Min 8 Characters</div>
            </div>
        `;
        
        // Insert checklist directly after the wrapper
        wrapper.parentNode.insertBefore(checklist, wrapper.nextSibling);
        
        const updateChecklist = function() {
            const val = input.value;
            const hasCapital = /[A-Z]/.test(val);
            const hasNumber = /[0-9]/.test(val);
            const hasSpecial = /[\W]/.test(val);
            const hasLength = val.length >= 8;
            
            const capItem = checklist.querySelector('.req-capital');
            const numItem = checklist.querySelector('.req-number');
            const specItem = checklist.querySelector('.req-special');
            const lenItem = checklist.querySelector('.req-length');
            
            const setItemState = (item, isValid, label) => {
                if (isValid) {
                    item.style.color = '#10b981';
                    item.style.fontWeight = '600';
                    item.innerHTML = `✓ ${label}`;
                } else {
                    item.style.color = '#ef4444';
                    item.style.fontWeight = 'normal';
                    item.innerHTML = `✗ ${label}`;
                }
            };
            
            setItemState(capItem, hasCapital, '1 Capital Letter');
            setItemState(numItem, hasNumber, '1 Number');
            setItemState(specItem, hasSpecial, '1 Special Char');
            setItemState(lenItem, hasLength, 'Min 8 Characters');
            
            return hasCapital && hasNumber && hasSpecial && hasLength;
        };
        
        // Real-time input listener
        input.addEventListener('input', function() {
            if (input.value === '') {
                checklist.style.display = 'none';
            } else {
                checklist.style.display = 'block';
                updateChecklist();
            }
        });
        
        input.addEventListener('focus', function() {
            if (input.value !== '') {
                checklist.style.display = 'block';
                updateChecklist();
            }
        });
        
        // Hide checklist on blur if field is empty
        input.addEventListener('blur', function() {
            if (input.value === '') {
                checklist.style.display = 'none';
            }
        });
        
        // 5. Enforce requirements on form submission
        const form = input.closest('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                // If it is the login form, we don't block
                if (form.id === 'loginForm') return;
                
                // If password is not required and is empty, skip validation
                if (!input.hasAttribute('required') && input.value === '') {
                    return;
                }
                
                const isValid = updateChecklist();
                if (!isValid) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Show requirement checklist
                    checklist.style.display = 'block';
                    
                    // Restore submit button state
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerText = submitBtn.getAttribute('data-original-text') || submitBtn.innerText || 'Save';
                    }
                    
                    // Highlight the input border red briefly
                    input.style.borderColor = '#ef4444';
                    setTimeout(() => {
                        input.style.borderColor = '';
                    }, 2000);
                    
                    alert('Password does not meet the safety requirements. Please check the requirements below the password field.');
                }
            });
        }
    });
}

// Auto-run on load
$(document).ready(function() {
    initPasswordFields();
    
    // MutationObserver to automatically handle AJAX loaded password inputs (e.g. Staff forms)
    const observer = new MutationObserver(function(mutations) {
        let hasPassword = false;
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        if (node.matches('input[type="password"]') || node.querySelector('input[type="password"]')) {
                            hasPassword = true;
                        }
                    }
                });
            }
        });
        if (hasPassword) {
            initPasswordFields();
        }
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});

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

function loadData(page_name, page = 1, limit = 10, search = '', status_filter = '') {
    const listDiv = $('#' + page_name + '_list');
    // Keep loading state minimal but visible
    listDiv.css('opacity', '0.6');

    $.ajax({
        url: page_name + '_action.php',
        type: 'POST',
        data: {
            action: 'list',
            page: page,
            limit: limit,
            search: search,
            status_filter: status_filter
        },
        success: function(data) {
            listDiv.html(data).css('opacity', '1');
        },
        error: function() {
            listDiv.html('<div style="text-align:center; color:red; padding:2rem;">Failed to load data.</div>').css('opacity', '1');
        }
    });
}

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
