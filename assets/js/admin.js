jQuery(document).ready(function($) {
    console.log('Starting Select2 initialization...');
    
    // Initialize Select2 on the bypass drip content select field
    $('.bypass-drip-content-select').each(function() {
        const $select = $(this);
        
        console.log('Select element initial state:', {
            id: $select.attr('id'),
            name: $select.attr('name'),
            hasPlaceholder: $select.data('placeholder') !== undefined,
            placeholder: $select.data('placeholder'),
            currentValue: $select.val(),
            hasEmptyOption: $select.find('option[value=""]').length > 0,
            optionsCount: $select.find('option').length
        });
        
        // Initialize Select2
        $select.select2({
            width: '100%',
            allowClear: true,
            multiple: true,
            placeholder: $select.data('placeholder') || 'Select or add users',
            closeOnSelect: false,
            createTag: function() {
                // Prevent tag creation
                return null;
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
            const $selection = $(this).next('.select2-container').find('.select2-selection');
            
            if (values.length > 0) {
                $container.addClass('has-bypass-users');
                $selection.addClass('select2-selection--multiple--has-items');
            } else {
                $container.removeClass('has-bypass-users');
                $selection.removeClass('select2-selection--multiple--has-items');
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

        // Add debugging events
        $select.on('select2:open', function() {
            console.log('Select2 opened:', {
                id: $select.attr('id'),
                value: $select.val(),
                hasPlaceholder: $select.next('.select2-container').find('.select2-selection__placeholder').length > 0,
                placeholderText: $select.next('.select2-container').find('.select2-selection__placeholder').text()
            });
        });

        $select.on('change', function() {
            console.log('Select2 value changed:', {
                id: $select.attr('id'),
                newValue: $select.val(),
                hasPlaceholder: $select.next('.select2-container').find('.select2-selection__placeholder').length > 0,
                placeholderText: $select.next('.select2-container').find('.select2-selection__placeholder').text()
            });
        });

        // Log Select2 state after initialization
        setTimeout(() => {
            console.log('Select2 post-initialization state:', {
                id: $select.attr('id'),
                data: $select.select2('data'),
                value: $select.val(),
                hasPlaceholder: $select.next('.select2-container').find('.select2-selection__placeholder').length > 0,
                placeholderText: $select.next('.select2-container').find('.select2-selection__placeholder').text(),
                containerHtml: $select.next('.select2-container').html()
            });
        }, 100);

        // Add debugging events
        $select.on('select2:open', function() {
            console.log('Select2 opened:', {
                id: $select.attr('id'),
                value: $select.val(),
                hasPlaceholder: $select.next('.select2-container').find('.select2-selection__placeholder').length > 0,
                placeholderText: $select.next('.select2-container').find('.select2-selection__placeholder').text()
            });
        });

        $select.on('change', function() {
            console.log('Select2 value changed:', {
                id: $select.attr('id'),
                newValue: $select.val(),
                hasPlaceholder: $select.next('.select2-container').find('.select2-selection__placeholder').length > 0,
                placeholderText: $select.next('.select2-container').find('.select2-selection__placeholder').text()
            });
        });

        // Log Select2 state after initialization
        setTimeout(() => {
            console.log('Select2 post-initialization state:', {
                id: $select.attr('id'),
                data: $select.select2('data'),
                value: $select.val(),
                hasPlaceholder: $select.next('.select2-container').find('.select2-selection__placeholder').length > 0,
                placeholderText: $select.next('.select2-container').find('.select2-selection__placeholder').text(),
                containerHtml: $select.next('.select2-container').html()
            });
        }, 100);

        // Trigger initial state
        $select.trigger('change');
    });
});
