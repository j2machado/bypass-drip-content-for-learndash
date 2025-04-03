<?php
/**
 * Plugin Name: Bypass Drip Content for LearnDash
 * Description: Adds a bypass drip content option to LearnDash lessons.
 * Version: 1.0.0
 * Author: Obi Juan
 * Author URI: https://obijuan.dev
 * License: GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: bypass-drip-content-ld
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

class Bypass_Drip_Content_LearnDash {
    /**
     * @var null|Bypass_Drip_Content_LearnDash
     */
    private static $instance = null;

    /**
     * @var Bypass_Drip_Content_Admin
     */
    private $admin;

    /**
     * @var int
     */
    private $lesson_id;

    /**
     * @var int
     */
    private $user_id;

    /**
     * @var bool
     */
    private $is_bypass_enabled = false;

    /**
     * @var array
     */
    private $selected_users = array();

    /**
     * @var array
     */
    private $selected_groups = array();

    /**
     * Get singleton instance
     * @return Bypass_Drip_Content_LearnDash
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->define_constants();
        $this->load_dependencies();
        $this->init_hooks();
    }

    private function is_bypass_enabled($lesson_id) {
        // Get bypass settings
        $bypass_enabled = get_post_meta($lesson_id, 'bypass_drip_content_enabled', true);
        if ('on' !== $bypass_enabled) {
            return false;
        }

        $this->is_bypass_enabled = true;

        return true;
    }

    private function get_bypass_users($lesson_id) {
        // Get selected users and groups with proper default values
        $selected_users = get_post_meta($lesson_id, 'bypass_drip_content', true);
        $selected_groups = get_post_meta($lesson_id, 'bypass_drip_content_groups', true);

        // Ensure we have valid JSON data or set empty arrays as defaults
        $selected_users = !empty($selected_users) ? json_decode($selected_users, true) : array();
        $selected_groups = !empty($selected_groups) ? json_decode($selected_groups, true) : array();
        
        // Ensure we always have arrays, even if json_decode fails
        $selected_users = is_array($selected_users) ? $selected_users : array();
        $selected_groups = is_array($selected_groups) ? $selected_groups : array();
        
        $this->selected_users = $selected_users;
        $this->selected_groups = $selected_groups;

        return array($selected_users, $selected_groups);
    }

    /**
     * Check if the user should bypass drip content restrictions
     * 
     * @param int $user_id The user ID to check
     * @return bool True if user should bypass restrictions, false otherwise
     */
    private function should_bypass_restrictions($user_id) {
        // If user is admin, don't modify access
        if (current_user_can('manage_options')) {
            return false;
        }

        $selected_users = $this->selected_users;
        $selected_groups = $this->selected_groups;

        $user_id = (string) $user_id;

      // Check if user is in selected users
        if (in_array($user_id, $selected_users)) {
            return true;
        }

        // Check if user is in any selected groups
        if (!empty($selected_groups)) {
            foreach ($selected_groups as $group_id) {
                if (learndash_is_user_in_group($user_id, $group_id)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Maybe bypass the lesson access timer
     * 
     * @param int $lesson_access_from The timestamp when the lesson becomes available
     * @param int $lesson_id The lesson post ID
     * @param int $user_id The user ID
     * @return int Modified access timestamp
     */
    public function maybe_bypass_lesson_timer($lesson_access_from, $lesson_id, $user_id) {

        $this->is_bypass_enabled($lesson_id);

        $this->get_bypass_users($lesson_id);

        if ($this->is_bypass_enabled && $this->should_bypass_restrictions($user_id)) {
            return 0; // Set to 0 to make the lesson immediately available
        }

        return $lesson_access_from;
    }

    /**
     * Define plugin constants
     */
    private function define_constants() {
        define('BDCLD_VERSION', '1.0.0');
        define('BDCLD_PLUGIN_DIR', plugin_dir_path(__FILE__));
        define('BDCLD_PLUGIN_URL', plugin_dir_url(__FILE__));
    }

    /**
     * Load required dependencies
     */
    private function load_dependencies() {
        require_once BDCLD_PLUGIN_DIR . 'includes/class-bypass-drip-content-admin.php';
        $this->admin = new Bypass_Drip_Content_Admin();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Hook into LearnDash's drip content checks
        add_filter('ld_lesson_access_from__visible_after', array($this, 'maybe_bypass_lesson_timer'), 10, 3);
        add_filter('ld_lesson_access_from__visible_after_specific_date', array($this, 'maybe_bypass_lesson_timer'), 10, 3);
        add_action('plugins_loaded', array($this, 'on_plugins_loaded'));
    }

    /**
     * Runs on plugins loaded
     */
    public function on_plugins_loaded() {
        if (!class_exists('SFWD_LMS')) {
            add_action('admin_notices', array($this, 'learndash_not_active_notice'));
            return;
        }
    }

    /**
     * Display notice if LearnDash is not active
     */
    public function learndash_not_active_notice() {
        $message = sprintf(
            __('Bypass Drip Content for LearnDash requires LearnDash LMS plugin to be installed and active. You can download %s here.', 'bypass-drip-content-ld'),
            '<a href="https://www.learndash.com" target="_blank">LearnDash</a>'
        );
        echo '<div class="error"><p>' . $message . '</p></div>';
    }
}

// Initialize the plugin
function bypass_drip_content_learndash() {
    return Bypass_Drip_Content_LearnDash::get_instance();
}

// Start the plugin
bypass_drip_content_learndash();