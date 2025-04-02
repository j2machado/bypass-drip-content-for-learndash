jQuery(document).ready(function($) {
    // Initialize Select2 on the bypass drip content select field
    $('.bypass-drip-content-select').each(function() {
        const $select = $(this);
        
        $select.select2({
            width: '100%',
            allowClear: true,
            tags: true,
            tokenSeparators: [',', ' '],
            placeholder: $select.data('placeholder') || 'Select or add users',
            createTag: function(params) {
                const term = $.trim(params.term);
                
                if (term === '') {
                    return null;
                }
                
                return {
                    id: term,
                    text: term,
                    newTag: true
                };
            }
        });

        // Add custom styling when values change
        $select.on('change', function() {
            const $container = $(this).closest('.sfwd_input');
            const values = $(this).val() || [];
            
            if (values.length > 0) {
                $container.addClass('has-bypass-users');
            } else {
                $container.removeClass('has-bypass-users');
            }
        });

        // Trigger initial state
        $select.trigger('change');
    });
});
