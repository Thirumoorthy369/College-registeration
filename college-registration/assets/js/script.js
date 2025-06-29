document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const registrationForm = document.getElementById('registrationForm');
    if (registrationForm) {
        // Get max age from data attribute
        const maxAge = parseInt(registrationForm.dataset.maxAge) || 30; // Default to 30 if not set
        
        registrationForm.addEventListener('submit', function(e) {
            // Validate age
            const dobInput = document.getElementById('dob');
            if (dobInput.value) {
                const dob = new Date(dobInput.value);
                const today = new Date();
                let age = today.getFullYear() - dob.getFullYear();
                
                // Adjust age if birthday hasn't occurred yet this year
                const monthDiff = today.getMonth() - dob.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                    age--;
                }
                
                if (age > maxAge) {
                    e.preventDefault();
                    alert(`Sorry, registration is only allowed for students aged ${maxAge} or younger.`);
                    dobInput.focus();
                    return;
                }
            }

            // Basic form validation
            const requiredFields = registrationForm.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    }
    
    // Date picker initialization for dob field
    const dobField = document.getElementById('dob');
    if (dobField) {
        // Set max date to today
        dobField.max = new Date().toISOString().split('T')[0];
        
        // Validate date on change
        dobField.addEventListener('change', function() {
            if (this.value) {
                const dob = new Date(this.value);
                const today = new Date();
                let age = today.getFullYear() - dob.getFullYear();
                
                // Adjust age if birthday hasn't occurred yet this year
                const monthDiff = today.getMonth() - dob.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                    age--;
                }
                
                const ageFeedback = document.getElementById('ageFeedback');
                if (ageFeedback) {
                    ageFeedback.textContent = `Age: ${age} years`;
                    
                    // Get max age from form data attribute or use default
                    const maxAge = parseInt(document.getElementById('registrationForm')?.dataset.maxAge) || 30;
                    
                    if (age > maxAge) {
                        ageFeedback.classList.add('text-danger');
                        ageFeedback.textContent += ` (Maximum allowed age is ${maxAge})`;
                    } else {
                        ageFeedback.classList.remove('text-danger');
                    }
                }
            }
        });
    }
    
    // Dynamic year of study and passing out year calculation
    const yearOfStudy = document.getElementById('year_of_study');
    const passingYear = document.getElementById('passing_year');
    
    if (yearOfStudy && passingYear) {
        yearOfStudy.addEventListener('change', function() {
            const currentYear = new Date().getFullYear();
            const yearsLeft = 4 - parseInt(this.value); // Assuming 4-year degree
            passingYear.value = currentYear + yearsLeft;
            
            // Validate passing year
            if (passingYear.value < currentYear) {
                passingYear.classList.add('is-invalid');
            } else {
                passingYear.classList.remove('is-invalid');
            }
        });
        
        // Initialize passing year on page load if year of study is already selected
        if (yearOfStudy.value) {
            yearOfStudy.dispatchEvent(new Event('change'));
        }
    }
    
    // Real-time validation for email format
    const emailField = document.getElementById('email');
    if (emailField) {
        emailField.addEventListener('input', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(this.value)) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    }
    
    // Real-time validation for phone number
    const phoneField = document.getElementById('phone');
    if (phoneField) {
        phoneField.addEventListener('input', function() {
            const phoneRegex = /^[0-9]{10,15}$/;
            if (!phoneRegex.test(this.value)) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    }
});