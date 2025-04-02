jQuery(document).ready(function($) {
    // Add any necessary JavaScript functionality for the admin interface
    $('.bypass-drip-content-checkbox').on('change', function() {
        // Optional: Add visual feedback when the checkbox is toggled
        if ($(this).is(':checked')) {
            $(this).closest('.sfwd_input').addClass('bypass-enabled');
        } else {
            $(this).closest('.sfwd_input').removeClass('bypass-enabled');
        }
    });
});
