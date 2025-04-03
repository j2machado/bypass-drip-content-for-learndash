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
            optionsCount: $select.find('option').length,
            savedUsers: bypassDripContent.savedUsers
        });
        
        // Initialize Select2 with AJAX for users dropdown
        const isUserSelect = $select.attr('name').includes('bypass_drip_content]') && !$select.attr('name').includes('groups');

        // Pre-populate options with saved users
        if (isUserSelect && bypassDripContent.savedUsers) {
            bypassDripContent.savedUsers.forEach(function(user) {
                if (!$select.find(`option[value="${user.id}"]`).length) {
                    const option = new Option(user.text, user.id, true, true);
                    $select.append(option);
                }
            });
        }

        const baseConfig = {
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
        };

        const ajaxConfig = isUserSelect ? {
            ajax: {
                url: bypassDripContent.ajaxurl,
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        action: 'search_users_for_bypass',
                        nonce: bypassDripContent.nonce,
                        term: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: data.pagination
                    };
                },
                cache: true
            },
            minimumInputLength: 2,
            initSelection: function(element, callback) {
                if (bypassDripContent.savedUsers) {
                    callback(bypassDripContent.savedUsers);
                }
            }
        } : {};

        // Merge configurations based on select type
        $select.select2({ ...baseConfig, ...ajaxConfig });

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

            // Log value changes for debugging
            console.log('Select2 value changed:', {
                id: $(this).attr('id'),
                newValue: values,
                hasPlaceholder: $(this).data('placeholder') !== undefined,
                placeholderText: $(this).data('placeholder')
            });
        });

        // Add debugging events
        $select.on('select2:open', function() {
            const values = $(this).val() || [];
            console.log('Select2 opened:', {
                id: $(this).attr('id'),
                value: values,
                hasPlaceholder: $(this).data('placeholder') !== undefined,
                placeholderText: $(this).data('placeholder')
            });

            // Update selected state in dropdown
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

        // Log post-initialization state
        setTimeout(() => {
            console.log('Select2 post-initialization state:', {
                id: $select.attr('id'),
                data: $select.select2('data'),
                value: $select.val(),
                hasPlaceholder: $select.data('placeholder') !== undefined,
                placeholderText: $select.data('placeholder')
            });
        }, 100);

        // Trigger initial state
        $select.trigger('change');
    });
});
