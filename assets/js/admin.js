jQuery(document).ready(function($) {
    // Initialize Select2 on the bypass drip content select field
    $('.bypass-drip-content-select').each(function() {
        const $select = $(this);
        
        // Get any pre-selected options
        const selectedOptions = $select.find('option:selected').map(function() {
            return {
                id: $(this).val(),
                text: $(this).text() || $(this).val()
            };
        }).get();

        // Initialize Select2
        $select.select2({
            width: '100%',
            allowClear: true,
            multiple: true,
            tags: true,
            tokenSeparators: [',', ' '],
            placeholder: $select.data('placeholder') || 'Select or add users',
            closeOnSelect: false,
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
            },
            templateResult: function(data) {
                if (data.loading) return data.text;
                
                var $result = $(
                    '<div class="select2-result-option' + 
                    (data.selected ? ' select2-result-option--selected' : '') + 
                    '">' + data.text + '</div>'
                );
                
                return $result;
            },
            templateSelection: function(data, container) {
                return data.text;
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
