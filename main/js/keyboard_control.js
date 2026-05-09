function allowNumbersOnly(e) {
    const charCode = (e.which) ? e.which : e.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        e.preventDefault();
        return false;
    }
    return true;
}

function allowLettersOnly(e) {
    const charCode = (e.which) ? e.which : e.keyCode;
    // Allow A-Z, a-z, and space
    if ((charCode >= 65 && charCode <= 90) || (charCode >= 97 && charCode <= 122) || charCode == 32) {
        return true;
    }
    e.preventDefault();
    return false;
}

function restrictPasteNumbers(e) {
    const input = e.target;
    // Remove any non-numeric characters
    input.value = input.value.replace(/[^0-9]/g, '');
}

function restrictPasteLetters(e) {
    const input = e.target;
    // Remove anything that isn't a letter or space
    input.value = input.value.replace(/[^a-zA-Z\s]/g, '');
}

// Enter to Next Field Logic
document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        const target = e.target;
        if (target.tagName === 'INPUT' || target.tagName === 'SELECT' || target.tagName === 'TEXTAREA') {
            if (target.type === 'submit' || target.tagName === 'BUTTON') {
                return;
            }
            
            e.preventDefault();
            const form = target.form;
            if (!form) return;

            const index = Array.prototype.indexOf.call(form.elements, target);
            if (index > -1) {
                let next = form.elements[index + 1];
                while (next && (next.readOnly || next.disabled || next.type === 'hidden')) {
                    next = form.elements[Array.prototype.indexOf.call(form.elements, next) + 1];
                }
                if (next) {
                    next.focus();
                } else {
                    // If no next element, try to find the submit button
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) submitBtn.focus();
                }
            }
        }
    }
});
