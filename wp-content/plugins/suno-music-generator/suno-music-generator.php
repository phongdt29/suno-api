<?php
/**
 * Plugin Name: Suno Music Generator
 * Plugin URI: https://github.com/your-repo/suno-music-generator
 * Description: Tạo nhạc AI với Suno API - Hỗ trợ tạo nhạc từ prompt, lyrics tùy chỉnh, và tích hợp ChatGPT
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: suno-music-generator
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('SUNO_PLUGIN_VERSION', '1.0.0');
define('SUNO_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('SUNO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SUNO_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Plugin Class
 */
class Suno_Music_Generator {

    /**
     * Instance
     */
    private static $instance = null;

    /**
     * Get instance
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
        $this->load_dependencies();
        $this->init_hooks();
    }

    /**
     * Load dependencies
     */
    private function load_dependencies() {
        require_once SUNO_PLUGIN_PATH . 'includes/class-suno-api.php';
        require_once SUNO_PLUGIN_PATH . 'includes/class-suno-admin.php';
        require_once SUNO_PLUGIN_PATH . 'includes/class-suno-rest-api.php';
        require_once SUNO_PLUGIN_PATH . 'includes/class-suno-shortcodes.php';
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Activation/Deactivation
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        // Init
        add_action('init', array($this, 'init'));
        add_action('plugins_loaded', array($this, 'load_textdomain'));

        // Enqueue scripts
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));

        // Initialize components
        add_action('init', array('Suno_Admin', 'get_instance'));
        add_action('rest_api_init', array('Suno_Rest_API', 'register_routes'));
        add_action('init', array('Suno_Shortcodes', 'register'));

        // Schedule cron handler
        add_action('suno_scheduled_generate', array($this, 'handle_scheduled_generate'));
    }

    /**
     * Handle scheduled music generation
     */
    public function handle_scheduled_generate($schedule_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'suno_schedule';

        // Get schedule data
        $schedule = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $schedule_id
        ));

        if (!$schedule || $schedule->status !== 'pending') {
            return;
        }

        // Update status to processing
        $wpdb->update($table_name, array('status' => 'processing'), array('id' => $schedule_id));

        // Generate music
        $api = new Suno_API();
        $result = $api->generate(array(
            'prompt' => $schedule->full_prompt,
            'model' => $schedule->model,
            'make_instrumental' => (bool) $schedule->instrumental,
            'style' => $schedule->genre,
        ));

        if ($result['success']) {
            // Update status
            $wpdb->update($table_name, array(
                'status' => 'completed',
                'task_id' => $result['data']['taskId'] ?? '',
            ), array('id' => $schedule_id));

            // Handle repeat
            if ($schedule->repeat_type !== 'once') {
                $next_time = $this->calculate_next_schedule($schedule->schedule_time, $schedule->repeat_type);
                if ($next_time) {
                    $wpdb->insert($table_name, array(
                        'genre' => $schedule->genre,
                        'prompt' => $schedule->prompt,
                        'full_prompt' => $schedule->full_prompt,
                        'model' => $schedule->model,
                        'instrumental' => $schedule->instrumental,
                        'schedule_time' => $next_time,
                        'repeat_type' => $schedule->repeat_type,
                        'status' => 'pending',
                        'created_at' => current_time('mysql'),
                    ));
                    wp_schedule_single_event(strtotime($next_time), 'suno_scheduled_generate', array($wpdb->insert_id));
                }
            }
        } else {
            $wpdb->update($table_name, array('status' => 'failed'), array('id' => $schedule_id));
        }
    }

    /**
     * Calculate next schedule time
     */
    private function calculate_next_schedule($current_time, $repeat_type) {
        $timestamp = strtotime($current_time);

        switch ($repeat_type) {
            case 'daily':
                return date('Y-m-d H:i:s', strtotime('+1 day', $timestamp));
            case 'weekly':
                return date('Y-m-d H:i:s', strtotime('+1 week', $timestamp));
            case 'monthly':
                return date('Y-m-d H:i:s', strtotime('+1 month', $timestamp));
            default:
                return null;
        }
    }

    /**
     * Init
     */
    public function init() {
        // Additional initialization
    }

    /**
     * Load text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'suno-music-generator',
            false,
            dirname(SUNO_PLUGIN_BASENAME) . '/languages'
        );
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        wp_enqueue_style(
            'suno-frontend',
            SUNO_PLUGIN_URL . 'assets/css/suno-frontend.css',
            array(),
            SUNO_PLUGIN_VERSION
        );

        wp_enqueue_script(
            'suno-frontend',
            SUNO_PLUGIN_URL . 'assets/js/suno-frontend.js',
            array('jquery'),
            SUNO_PLUGIN_VERSION,
            true
        );

        wp_localize_script('suno-frontend', 'sunoApi', array(
            'restUrl' => rest_url('suno/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
            'i18n' => array(
                'generating' => __('Đang tạo nhạc...', 'suno-music-generator'),
                'processing' => __('Đang xử lý...', 'suno-music-generator'),
                'completed' => __('Hoàn thành!', 'suno-music-generator'),
                'error' => __('Có lỗi xảy ra', 'suno-music-generator'),
                'download' => __('Tải xuống', 'suno-music-generator'),
            )
        ));
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'suno-music') === false) {
            return;
        }

        wp_enqueue_style(
            'suno-admin',
            SUNO_PLUGIN_URL . 'assets/css/suno-admin.css',
            array(),
            SUNO_PLUGIN_VERSION
        );

        wp_enqueue_script(
            'suno-admin',
            SUNO_PLUGIN_URL . 'assets/js/suno-admin.js',
            array('jquery'),
            SUNO_PLUGIN_VERSION,
            true
        );

        wp_localize_script('suno-admin', 'sunoAdmin', array(
            'restUrl' => rest_url('suno/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
        ));
    }

    /**
     * Activate plugin
     */
    public function activate() {
        // Set default options
        $defaults = array(
            'suno_api_key' => '',
            'openai_api_key' => '',
            'default_model' => 'V3_5',
            'enable_history' => true,
        );

        foreach ($defaults as $key => $value) {
            if (get_option($key) === false) {
                add_option($key, $value);
            }
        }

        // Create database table for history
        $this->create_tables();

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Deactivate plugin
     */
    public function deactivate() {
        flush_rewrite_rules();
    }

    /**
     * Create database tables
     */
    private function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // History table
        $table_name = $wpdb->prefix . 'suno_history';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL DEFAULT 0,
            task_id varchar(100) NOT NULL,
            prompt text,
            lyrics text,
            title varchar(255),
            genre varchar(100) COMMENT 'Thể loại nhạc',
            style varchar(255),
            model varchar(20),
            status varchar(50) DEFAULT 'pending',
            songs longtext,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY task_id (task_id),
            KEY user_id (user_id),
            KEY status (status),
            KEY genre (genre)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // Schedule table
        $schedule_table = $wpdb->prefix . 'suno_schedule';
        $sql_schedule = "CREATE TABLE IF NOT EXISTS $schedule_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            genre varchar(100) NOT NULL,
            prompt text,
            full_prompt text,
            model varchar(20) DEFAULT 'V3_5',
            instrumental tinyint(1) DEFAULT 0,
            schedule_time datetime NOT NULL,
            repeat_type varchar(20) DEFAULT 'once',
            status varchar(50) DEFAULT 'pending',
            task_id varchar(100),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY status (status),
            KEY schedule_time (schedule_time)
        ) $charset_collate;";

        dbDelta($sql_schedule);
    }
}

// Initialize plugin
function suno_music_generator() {
    return Suno_Music_Generator::get_instance();
}

// Start the plugin
suno_music_generator();
