// alert('okk');

let manage_option = document.querySelectorAll('.manage-option-select');
let couponFormContainer = document.querySelectorAll('.coupon-fields');

manage_option.forEach(function(select) {
    select.addEventListener('change', function() {
        // Check if the selected value is "2" (use string comparison)
        if (select.value === 'Send Email With Discount Coupon Code') {
            // Show the coupon fields
            couponFormContainer.forEach(function(form) {
                form.style.display = 'block';
            });
        } else {
            // Hide the coupon fields
            couponFormContainer.forEach(function(form) {
                form.style.display = 'none';
            });
        }
    });
});
