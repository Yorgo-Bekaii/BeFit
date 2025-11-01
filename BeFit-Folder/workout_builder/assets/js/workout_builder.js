// Form validation and enhancements
document.addEventListener('DOMContentLoaded', function() {
    // Equipment selection logic
    const equipmentCheckboxes = document.querySelectorAll('input[name="equipment[]"]');
    const noneCheckbox = document.querySelector('input[name="equipment[]"][value="none"]');
    
    if (noneCheckbox) {
        noneCheckbox.addEventListener('change', function() {
            if (this.checked) {
                equipmentCheckboxes.forEach(cb => {
                    if (cb !== noneCheckbox) cb.checked = false;
                });
            }
        });
        
        equipmentCheckboxes.forEach(cb => {
            if (cb !== noneCheckbox) {
                cb.addEventListener('change', function() {
                    if (this.checked) {
                        noneCheckbox.checked = false;
                    }
                });
            }
        });
    }
    
    // Form submission enhancements
    const workoutForm = document.getElementById('workoutForm');
    if (workoutForm) {
        workoutForm.addEventListener('submit', function(e) {
            // Additional client-side validation can go here
            const weight = parseFloat(document.getElementById('weight').value);
            const height = parseFloat(document.getElementById('height').value);
            
            if (weight < 30 || weight > 200) {
                alert('Please enter a valid weight between 30kg and 200kg');
                e.preventDefault();
                return;
            }
            
            if (height < 100 || height > 250) {
                alert('Please enter a valid height between 100cm and 250cm');
                e.preventDefault();
                return;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="loader"></span> Generating Your Plan...';
            }
        });
    }
    
    // Print button functionality
    const printButton = document.getElementById('printPlan');
    if (printButton) {
        printButton.addEventListener('click', function() {
            window.print();
        });
    }
    
    // Error message display
    if (document.querySelector('.error-message')) {
        setTimeout(() => {
            document.querySelector('.error-message').style.display = 'none';
        }, 5000);
    }
});

