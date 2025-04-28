document.addEventListener('DOMContentLoaded', function() {
    // FAQ Toggle
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        
        question.addEventListener('click', () => {
            // Close other open items
            faqItems.forEach(otherItem => {
                if (otherItem !== item && otherItem.classList.contains('active')) {
                    otherItem.classList.remove('active');
                }
            });
            
            // Toggle current item
            item.classList.toggle('active');
        });
    });
    
    // File upload display
    const fileInputs = document.querySelectorAll('.file-input');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const fileLabel = this.nextElementSibling;
            const fileNameSpan = fileLabel.querySelector('span');
            
            if (this.files.length > 0) {
                fileNameSpan.textContent = this.files[0].name;
                fileLabel.classList.add('has-file');
            } else {
                fileNameSpan.textContent = 'Choose a file';
                fileLabel.classList.remove('has-file');
            }
        });
    });
    
    // Radio button dependency for admin notes
    const radioButtons = document.querySelectorAll('input[name="status"]');
    const adminNotes = document.getElementById('admin_notes');
    
    if (radioButtons.length > 0 && adminNotes) {
        radioButtons.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'rejected' || this.value === 'additional_info') {
                    adminNotes.setAttribute('required', 'required');
                    adminNotes.closest('.form-group').classList.add('required-field');
                } else {
                    adminNotes.removeAttribute('required');
                    adminNotes.closest('.form-group').classList.remove('required-field');
                }
            });
        });
    }
    
    // Mobile navigation toggle
    const mobileToggle = document.querySelector('.mobile-toggle');
    const mainNav = document.querySelector('.main-nav');
    
    if (mobileToggle && mainNav) {
        mobileToggle.addEventListener('click', () => {
            mainNav.classList.toggle('active');
            mobileToggle.classList.toggle('active');
        });
    }
    
    // Form validation
    const registrationForm = document.querySelector('form[action="register.php"]');
    
    if (registrationForm) {
        registrationForm.addEventListener('submit', function(event) {
            const requiredFields = registrationForm.querySelectorAll('[required]');
            let hasError = false;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('error');
                    hasError = true;
                } else {
                    field.classList.remove('error');
                }
            });
            
            const emailField = registrationForm.querySelector('#email');
            if (emailField && emailField.value) {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(emailField.value)) {
                    emailField.classList.add('error');
                    hasError = true;
                }
            }
            
            if (hasError) {
                event.preventDefault();
                alert('Please fill in all required fields correctly.');
            }
        });
    }
});