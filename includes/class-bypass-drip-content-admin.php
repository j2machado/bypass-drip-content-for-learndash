<?php
/**
 * Admin functionality for Bypass Drip Content
 */
class Bypass_Drip_Content_Admin {
    /**
     * Constructor
     */
    public function __construct() {
        // Add the custom field to LearnDash lesson settings
        add_filter('learndash_settings_fields', array($this, 'add_bypass_drip_field'), 10, 2);
        
        // Save the custom field value
        add_filter('learndash_metabox_save_fields_learndash-lesson-access-settings', array($this, 'save_bypass_drip_field'), 30, 3);
        
        // Add custom admin scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));

        // Add AJAX handler for user search
        add_action('wp_ajax_search_users_for_bypass', array($this, 'search_users_for_bypass'));
    }

    /**
     * AJAX handler for searching users
     */
    public function search_users_for_bypass() {
        check_ajax_referer('bypass_drip_content_nonce', 'nonce');

        $search = isset($_GET['term']) ? sanitize_text_field($_GET['term']) : '';
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;

        $args = array(
            'search'         => '*' . $search . '*',
            'search_columns' => array('user_login', 'user_email', 'user_nicename', 'display_name'),
            'number'         => 10,
            'offset'         => ($page - 1) * 10,
            'orderby'       => 'display_name',
            'order'         => 'ASC'
        );

        $user_query = new WP_User_Query($args);
        $users = array();

        if (!empty($user_query->get_results())) {
            foreach ($user_query->get_results() as $user) {
                $users[] = array(
                    'id'   => $user->ID,
                    'text' => sprintf('%s (%s)', $user->display_name, $user->user_email)
                );
            }
        }

        $total_users = $user_query->get_total();
        $more = ($page * 10) < $total_users;

        wp_send_json(array(
            'results'    => $users,
            'pagination' => array(
                'more' => $more
            )
        ));
    }

    /**
     * Add bypass drip content field to LearnDash lesson access settings
     */
    /**
     * Get dummy users for testing
     */
    private function get_dummy_users() {
        return array(
            'john.doe' => 'John Doe',
            'jane.smith' => 'Jane Smith',
            'bob.wilson' => 'Bob Wilson',
            'alice.brown' => 'Alice Brown',
            'mike.davis' => 'Mike Davis'
        );
    }

    /**
     * Get dummy groups for testing
     */
    private function get_dummy_groups() {
        return array(
            'beginners' => 'Beginners Group',
            'intermediate' => 'Intermediate Group',
            'advanced' => 'Advanced Group',
            'premium' => 'Premium Members',
            'vip' => 'VIP Members'
        );
    }

    public function add_bypass_drip_field($fields, $metabox_key) {
        // Only add the field to lesson access settings
        if ($metabox_key === 'learndash-lesson-access-settings') {
            $post_id = get_the_ID();
            
            $saved_values = get_post_meta($post_id, 'bypass_drip_content', true);
            $saved_values = !empty($saved_values) ? json_decode($saved_values, true) : array();
            $saved_users_data = $this->get_users_data($saved_values);

            $saved_group_values = get_post_meta($post_id, 'bypass_drip_content_groups', true);
            $saved_group_values = !empty($saved_group_values) ? json_decode($saved_group_values, true) : array();

            // Get dummy users and merge with saved values to create options
            $dummy_users = $this->get_dummy_users();
            $all_options = array_merge(
                $dummy_users,
                array_combine($saved_values, $saved_values)
            );

            // Get dummy groups and merge with saved values to create options
            $dummy_groups = $this->get_dummy_groups();
            $all_group_options = array_merge(
                $dummy_groups,
                array_combine($saved_group_values, $saved_group_values)
            );

            $enable_bypass = get_post_meta($post_id, 'bypass_drip_content_enabled', true);
            
            $fields['bypass_drip_content_enabled'] = array(
                'name'          => 'bypass_drip_content_enabled',
                'label'         => __('Enable Bypass Drip Content', 'bypass-drip-content-ld'),
                'type'          => 'checkbox-switch',
                'class'         => 'bypass-drip-content-toggle',
                'value'         => $enable_bypass,
                'default'       => '',
                'child_section_state' => $enable_bypass === 'on' ? 'open' : 'closed',
                'help_text'     => __('Enable to allow specific users to bypass drip content for this lesson.', 'bypass-drip-content-ld'),
                'options'       => array(
                    'on'  => __('Allow specific users to bypass drip content for this lesson', 'bypass-drip-content-ld'),
                    ''    => '',
                ),
            );

            $fields['bypass_drip_content'] = array(
                'name'          => 'bypass_drip_content',
                'label'         => __('Select Users to Bypass', 'bypass-drip-content-ld'),
                'type'          => 'select',
                'class'         => 'bypass-drip-content-select',
                'multiple'      => true,
                'help_text'     => __('Select users from the list to bypass drip content for this lesson.', 'bypass-drip-content-ld'),
                'default'       => array(),
                'value'         => $saved_values,
                'options'       => $saved_users_data, // Pre-populate with saved users
                'parent_setting' => 'bypass_drip_content_enabled',
                'parent_setting_trigger' => 'on',
                'attrs'         => array(
                    'data-placeholder' => __('Select users', 'bypass-drip-content-ld'),
                    'multiple' => 'multiple'
                )
            );

            $fields['bypass_drip_content_groups'] = array(
                'name'          => 'bypass_drip_content_groups',
                'label'         => __('Select Groups to Bypass', 'bypass-drip-content-ld'),
                'type'          => 'select',
                'class'         => 'bypass-drip-content-select',
                'multiple'      => true,
                'help_text'     => __('Select groups from the list to bypass drip content for this lesson.', 'bypass-drip-content-ld'),
                'default'       => array(),
                'value'         => $saved_group_values,
                'options'       => $all_group_options,
                'parent_setting' => 'bypass_drip_content_enabled',
                'parent_setting_trigger' => 'on',
                'attrs'         => array(
                    'data-placeholder' => __('Select groups', 'bypass-drip-content-ld'),
                    'multiple' => 'multiple'
                )
            );
        }
        return $fields;
    }

    /**
     * Save the bypass drip content field value
     */
    public function save_bypass_drip_field($settings_values, $metabox_key, $settings_screen_id) {
        if ($metabox_key === 'learndash-lesson-access-settings') {
            $post_id = get_the_ID();
            
            if ($post_id) {
                // Save the enabled state
                $enable_bypass = isset($_POST[$metabox_key]['bypass_drip_content_enabled']) ? $_POST[$metabox_key]['bypass_drip_content_enabled'] : '';
                update_post_meta($post_id, 'bypass_drip_content_enabled', $enable_bypass);
                $settings_values['bypass_drip_content_enabled'] = $enable_bypass;

                // Get the raw POST data for our field
                $raw_values = isset($_POST[$metabox_key]['bypass_drip_content']) ? $_POST[$metabox_key]['bypass_drip_content'] : array();
                
                // Ensure we have an array
                if (!is_array($raw_values)) {
                    $raw_values = array($raw_values);
                }
                
                // Clean up the values
                $bypass_values = array_map(function($value) {
                    return sanitize_text_field(trim($value));
                }, $raw_values);
                
                // Remove any empty values
                $bypass_values = array_filter($bypass_values);
                
                // Ensure we have a sequential array
                $bypass_values = array_values($bypass_values);
                
                // Store users as JSON
                update_post_meta($post_id, 'bypass_drip_content', wp_json_encode($bypass_values));
                $settings_values['bypass_drip_content'] = $bypass_values;

                // Handle groups
                $raw_group_values = isset($_POST[$metabox_key]['bypass_drip_content_groups']) ? $_POST[$metabox_key]['bypass_drip_content_groups'] : array();
                
                // Ensure we have an array
                if (!is_array($raw_group_values)) {
                    $raw_group_values = array($raw_group_values);
                }
                
                // Clean up the group values
                $bypass_group_values = array_map(function($value) {
                    return sanitize_text_field(trim($value));
                }, $raw_group_values);
                
                // Remove any empty values
                $bypass_group_values = array_filter($bypass_group_values);
                
                // Ensure we have a sequential array
                $bypass_group_values = array_values($bypass_group_values);
                
                // Store groups as JSON
                update_post_meta($post_id, 'bypass_drip_content_groups', wp_json_encode($bypass_group_values));
                $settings_values['bypass_drip_content_groups'] = $bypass_group_values;
            }
        }
        return $settings_values;
    }

    /**
     * Enqueue admin scripts and styles
     */
    /**
     * Get user data for saved user IDs
     *
     * @param array $user_ids Array of user IDs
     * @return array Array of user data with id and text
     */
    private function get_users_data($user_ids) {
        $users_data = array();
        if (!empty($user_ids)) {
            $users = get_users(array(
                'include' => $user_ids,
                'orderby' => 'display_name',
                'order'   => 'ASC'
            ));

            foreach ($users as $user) {
                $users_data[] = array(
                    'id'   => $user->ID,
                    'text' => sprintf('%s (%s)', $user->display_name, $user->user_email)
                );
            }
        }
        return $users_data;
    }

    public function enqueue_admin_assets($hook) {
        global $post;
        
        // Only enqueue on lesson edit screen
        if ($hook == 'post.php' && $post && $post->post_type === 'sfwd-lessons') {
            // Get saved users for this lesson
            $saved_values = get_post_meta($post->ID, 'bypass_drip_content', true);
            $saved_values = !empty($saved_values) ? json_decode($saved_values, true) : array();
            $saved_users_data = $this->get_users_data($saved_values);
            // Enqueue Select2
            wp_enqueue_style(
                'select2',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
                array(),
                '4.1.0-rc.0'
            );

            wp_enqueue_script(
                'select2',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                array('jquery'),
                '4.1.0-rc.0',
                true
            );

            // Enqueue our custom assets
            wp_enqueue_style(
                'bypass-drip-content-admin',
                BDCLD_PLUGIN_URL . 'assets/css/admin.css',
                array('select2'),
                BDCLD_VERSION
            );

            wp_enqueue_script(
                'bypass-drip-content-admin',
                BDCLD_PLUGIN_URL . 'assets/js/admin.js',
                array('jquery', 'select2'),
                BDCLD_VERSION,
                true
            );

            wp_localize_script('bypass-drip-content-admin', 'bypassDripContent', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce'   => wp_create_nonce('bypass_drip_content_nonce'),
                'savedUsers' => $saved_users_data
            ));
        }
    }
}
