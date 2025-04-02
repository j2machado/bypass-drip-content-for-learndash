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
                    '" data-id="' + data.id + '">' + data.text + '</div>'
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

            // Update selected state for all options in the dropdown
            setTimeout(function() {
                $('.select2-results__options .select2-result-option').each(function() {
                    const $option = $(this);
                    const optionId = $option.data('id');
                    if (values.includes(optionId)) {
                        $option.addClass('select2-result-option--selected');
                    } else {
                        $option.removeClass('select2-result-option--selected');
                    }
                });
            }, 0);
        });

        // Also update on dropdown open to ensure consistency
        $select.on('select2:open', function() {
            const values = $(this).val() || [];
            setTimeout(function() {
                $('.select2-results__options .select2-result-option').each(function() {
                    const $option = $(this);
                    const optionId = $option.data('id');
                    if (values.includes(optionId)) {
                        $option.addClass('select2-result-option--selected');
                    } else {
                        $option.removeClass('select2-result-option--selected');
                    }
                });
            }, 0);
        });

        // Trigger initial state
        $select.trigger('change');
    });
});
