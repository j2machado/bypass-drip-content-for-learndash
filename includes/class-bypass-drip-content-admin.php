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

    public function add_bypass_drip_field($fields, $metabox_key) {
        // Only add the field to lesson access settings
        if ($metabox_key === 'learndash-lesson-access-settings') {
            $post_id = get_the_ID();
            
            $saved_values = get_post_meta($post_id, 'bypass_drip_content', true);
            $saved_values = !empty($saved_values) ? json_decode($saved_values, true) : array();

            // Get dummy users and merge with saved values to create options
            $dummy_users = $this->get_dummy_users();
            $all_options = array_merge(
                $dummy_users,
                array_combine($saved_values, $saved_values)
            );

            $fields['bypass_drip_content'] = array(
                'name'          => 'bypass_drip_content',
                'label'         => __('Bypass Drip Content', 'bypass-drip-content-ld'),
                'type'          => 'select',
                'class'         => 'bypass-drip-content-select',
                'multiple'      => true,
                'help_text'     => __('Select existing users or type new usernames to add them.', 'bypass-drip-content-ld'),
                'default'       => array(),
                'value'         => $saved_values,
                'options'       => $all_options,
                'attrs'         => array(
                    'data-tags' => 'true',
                    'data-placeholder' => __('Select or add users', 'bypass-drip-content-ld'),
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
                
                // Store as JSON
                update_post_meta($post_id, 'bypass_drip_content', wp_json_encode($bypass_values));
                
                // Update LearnDash settings
                $settings_values['bypass_drip_content'] = $bypass_values;
            }
        }
        return $settings_values;
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_assets($hook) {
        global $post;
        
        // Only enqueue on lesson edit screen
        if ($hook == 'post.php' && $post && $post->post_type === 'sfwd-lessons') {
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
        }
    }
}
