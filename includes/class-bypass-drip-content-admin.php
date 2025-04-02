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
    public function add_bypass_drip_field($fields, $metabox_key) {
        // Only add the field to lesson access settings
        if ($metabox_key === 'learndash-lesson-access-settings') {
            $post_id = get_the_ID();
            
            $fields['bypass_drip_content'] = array(
                'name'          => 'bypass_drip_content',
                'label'         => __('Bypass Drip Content', 'bypass-drip-content-ld'),
                'type'          => 'text',
                'class'         => 'bypass-drip-content-checkbox',
                'help_text'     => __('Type the name of users allowed to bypass the drip content.', 'bypass-drip-content-ld'),
                'default'       => '',
                'value'         => get_post_meta($post_id, 'bypass_drip_content', true),
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
                $bypass_value = isset($_POST[$metabox_key]['bypass_drip_content']) ? 
                    sanitize_text_field($_POST[$metabox_key]['bypass_drip_content']) : '';
                
                update_post_meta($post_id, 'bypass_drip_content', $bypass_value);
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
            wp_enqueue_style(
                'bypass-drip-content-admin',
                BDCLD_PLUGIN_URL . 'assets/css/admin.css',
                array(),
                BDCLD_VERSION
            );

            wp_enqueue_script(
                'bypass-drip-content-admin',
                BDCLD_PLUGIN_URL . 'assets/js/admin.js',
                array('jquery'),
                BDCLD_VERSION,
                true
            );
        }
    }
}
