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