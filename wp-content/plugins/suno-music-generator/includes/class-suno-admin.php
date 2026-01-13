<?php
/**
 * Suno Admin Class
 *
 * Handles admin settings and dashboard
 */

if (!defined('ABSPATH')) {
    exit;
}

class Suno_Admin {

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
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_filter('plugin_action_links_' . SUNO_PLUGIN_BASENAME, array($this, 'add_action_links'));
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Suno Music Generator', 'suno-music-generator'),
            __('Suno Music', 'suno-music-generator'),
            'manage_options',
            'suno-music-generator',
            array($this, 'render_dashboard'),
            'dashicons-format-audio',
            30
        );

        add_submenu_page(
            'suno-music-generator',
            __('Dashboard', 'suno-music-generator'),
            __('Dashboard', 'suno-music-generator'),
            'manage_options',
            'suno-music-generator',
            array($this, 'render_dashboard')
        );

        add_submenu_page(
            'suno-music-generator',
            __('Cài đặt', 'suno-music-generator'),
            __('Cài đặt', 'suno-music-generator'),
            'manage_options',
            'suno-music-settings',
            array($this, 'render_settings')
        );

        add_submenu_page(
            'suno-music-generator',
            __('Lịch sử', 'suno-music-generator'),
            __('Lịch sử', 'suno-music-generator'),
            'manage_options',
            'suno-music-history',
            array($this, 'render_history')
        );

        add_submenu_page(
            'suno-music-generator',
            __('Lên lịch tạo nhạc', 'suno-music-generator'),
            __('Lên lịch', 'suno-music-generator'),
            'manage_options',
            'suno-music-schedule',
            array($this, 'render_schedule')
        );

        add_submenu_page(
            'suno-music-generator',
            __('Hướng dẫn', 'suno-music-generator'),
            __('Hướng dẫn', 'suno-music-generator'),
            'manage_options',
            'suno-music-guide',
            array($this, 'render_guide')
        );
    }

    /**
     * Get music genres
     */
    public static function get_genres() {
        return array(
            'vietnamese' => array(
                'label' => __('Nhạc Việt Nam', 'suno-music-generator'),
                'options' => array(
                    'Nhạc Trẻ' => 'Nhạc Trẻ',
                    'Nhạc Bolero' => 'Nhạc Bolero',
                    'Nhạc Trữ Tình' => 'Nhạc Trữ Tình',
                    'Nhạc Tết' => 'Nhạc Tết',
                    'Nhạc Quê Hương' => 'Nhạc Quê Hương',
                    'Rap Việt' => 'Rap Việt',
                    'V-Pop' => 'V-Pop',
                ),
            ),
            'international' => array(
                'label' => __('Nhạc Quốc Tế', 'suno-music-generator'),
                'options' => array(
                    'Pop' => 'Pop',
                    'Rock' => 'Rock',
                    'R&B' => 'R&B / Soul',
                    'Hip Hop' => 'Hip Hop',
                    'EDM' => 'EDM / Electronic',
                    'Jazz' => 'Jazz',
                    'Classical' => 'Classical',
                    'K-Pop' => 'K-Pop',
                    'Latin' => 'Latin',
                ),
            ),
            'mood' => array(
                'label' => __('Nhạc Theo Tâm Trạng', 'suno-music-generator'),
                'options' => array(
                    'Nhạc Buồn' => 'Nhạc Buồn',
                    'Nhạc Vui' => 'Nhạc Vui / Sôi Động',
                    'Nhạc Thư Giãn' => 'Nhạc Thư Giãn / Chill',
                    'Nhạc Lãng Mạn' => 'Nhạc Lãng Mạn',
                ),
            ),
            'instrumental' => array(
                'label' => __('Nhạc Không Lời', 'suno-music-generator'),
                'options' => array(
                    'Instrumental' => 'Instrumental',
                    'Piano' => 'Piano',
                    'Lo-Fi' => 'Lo-Fi',
                    'Ambient' => 'Ambient',
                ),
            ),
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        // API Settings
        register_setting('suno_settings', 'suno_api_key', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        register_setting('suno_settings', 'ai_provider', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'gemini',
        ));

        register_setting('suno_settings', 'gemini_api_key', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        register_setting('suno_settings', 'openai_api_key', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        register_setting('suno_settings', 'default_model', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'V3_5',
        ));

        register_setting('suno_settings', 'enable_history', array(
            'type' => 'boolean',
            'default' => true,
        ));

        // API Section
        add_settings_section(
            'suno_api_section',
            __('Cài đặt API', 'suno-music-generator'),
            array($this, 'api_section_callback'),
            'suno_settings'
        );

        add_settings_field(
            'suno_api_key',
            __('Suno API Key', 'suno-music-generator'),
            array($this, 'api_key_field_callback'),
            'suno_settings',
            'suno_api_section'
        );

        add_settings_field(
            'ai_provider',
            __('AI Provider (Auto Generate)', 'suno-music-generator'),
            array($this, 'ai_provider_field_callback'),
            'suno_settings',
            'suno_api_section'
        );

        add_settings_field(
            'gemini_api_key',
            __('Google Gemini API Key', 'suno-music-generator'),
            array($this, 'gemini_key_field_callback'),
            'suno_settings',
            'suno_api_section'
        );

        add_settings_field(
            'openai_api_key',
            __('OpenAI API Key', 'suno-music-generator'),
            array($this, 'openai_key_field_callback'),
            'suno_settings',
            'suno_api_section'
        );

        // General Section
        add_settings_section(
            'suno_general_section',
            __('Cài đặt chung', 'suno-music-generator'),
            array($this, 'general_section_callback'),
            'suno_settings'
        );

        add_settings_field(
            'default_model',
            __('Model mặc định', 'suno-music-generator'),
            array($this, 'model_field_callback'),
            'suno_settings',
            'suno_general_section'
        );

        add_settings_field(
            'enable_history',
            __('Lưu lịch sử', 'suno-music-generator'),
            array($this, 'history_field_callback'),
            'suno_settings',
            'suno_general_section'
        );
    }

    /**
     * API section callback
     */
    public function api_section_callback() {
        echo '<p>' . __('Nhập API key để sử dụng Suno Music Generator.', 'suno-music-generator') . '</p>';
    }

    /**
     * General section callback
     */
    public function general_section_callback() {
        echo '<p>' . __('Cấu hình các tùy chọn chung.', 'suno-music-generator') . '</p>';
    }

    /**
     * API key field callback
     */
    public function api_key_field_callback() {
        $value = get_option('suno_api_key', '');
        ?>
        <input type="password"
               id="suno_api_key"
               name="suno_api_key"
               value="<?php echo esc_attr($value); ?>"
               class="regular-text"
               autocomplete="off">
        <button type="button" class="button" onclick="togglePasswordVisibility('suno_api_key')">
            <?php _e('Hiện/Ẩn', 'suno-music-generator'); ?>
        </button>
        <p class="description">
            <?php _e('Lấy API key tại: <a href="https://apibox.erweima.ai" target="_blank">apibox.erweima.ai</a>', 'suno-music-generator'); ?>
        </p>
        <?php
    }

    /**
     * AI Provider field callback
     */
    public function ai_provider_field_callback() {
        $value = get_option('ai_provider', 'gemini');
        ?>
        <select id="ai_provider" name="ai_provider">
            <option value="gemini" <?php selected($value, 'gemini'); ?>>
                Google Gemini (Miễn phí)
            </option>
            <option value="openai" <?php selected($value, 'openai'); ?>>
                OpenAI ChatGPT (Trả phí)
            </option>
        </select>
        <p class="description">
            <?php _e('Chọn AI provider cho tính năng Auto Generate (tạo lyrics tự động).', 'suno-music-generator'); ?>
        </p>
        <?php
    }

    /**
     * Gemini key field callback
     */
    public function gemini_key_field_callback() {
        $value = get_option('gemini_api_key', '');
        $provider = get_option('ai_provider', 'gemini');
        ?>
        <input type="password"
               id="gemini_api_key"
               name="gemini_api_key"
               value="<?php echo esc_attr($value); ?>"
               class="regular-text"
               autocomplete="off">
        <button type="button" class="button" onclick="togglePasswordVisibility('gemini_api_key')">
            <?php _e('Hiện/Ẩn', 'suno-music-generator'); ?>
        </button>
        <?php if ($provider === 'gemini') : ?>
            <span class="dashicons dashicons-yes-alt" style="color: green; margin-left: 5px;" title="Đang sử dụng"></span>
        <?php endif; ?>
        <p class="description">
            <?php _e('<strong>MIỄN PHÍ!</strong> Lấy API key tại: <a href="https://aistudio.google.com/app/apikey" target="_blank">Google AI Studio</a>', 'suno-music-generator'); ?>
        </p>
        <?php
    }

    /**
     * OpenAI key field callback
     */
    public function openai_key_field_callback() {
        $value = get_option('openai_api_key', '');
        $provider = get_option('ai_provider', 'gemini');
        ?>
        <input type="password"
               id="openai_api_key"
               name="openai_api_key"
               value="<?php echo esc_attr($value); ?>"
               class="regular-text"
               autocomplete="off">
        <button type="button" class="button" onclick="togglePasswordVisibility('openai_api_key')">
            <?php _e('Hiện/Ẩn', 'suno-music-generator'); ?>
        </button>
        <?php if ($provider === 'openai') : ?>
            <span class="dashicons dashicons-yes-alt" style="color: green; margin-left: 5px;" title="Đang sử dụng"></span>
        <?php endif; ?>
        <p class="description">
            <?php _e('Trả phí. Lấy tại: <a href="https://platform.openai.com/api-keys" target="_blank">OpenAI Platform</a>', 'suno-music-generator'); ?>
        </p>
        <?php
    }

    /**
     * Model field callback
     */
    public function model_field_callback() {
        $value = get_option('default_model', 'V3_5');
        $models = Suno_API::get_models();
        ?>
        <select id="default_model" name="default_model">
            <?php foreach ($models as $key => $label) : ?>
                <option value="<?php echo esc_attr($key); ?>" <?php selected($value, $key); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description">
            <?php _e('Chọn model mặc định khi tạo nhạc.', 'suno-music-generator'); ?>
        </p>
        <?php
    }

    /**
     * History field callback
     */
    public function history_field_callback() {
        $value = get_option('enable_history', true);
        ?>
        <label>
            <input type="checkbox"
                   id="enable_history"
                   name="enable_history"
                   value="1"
                   <?php checked($value, true); ?>>
            <?php _e('Lưu lịch sử các bài hát đã tạo', 'suno-music-generator'); ?>
        </label>
        <?php
    }

    /**
     * Add action links
     */
    public function add_action_links($links) {
        $settings_link = '<a href="' . admin_url('admin.php?page=suno-music-settings') . '">' . __('Cài đặt', 'suno-music-generator') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * Render dashboard
     */
    public function render_dashboard() {
        $api = new Suno_API();
        $credits = $api->get_credits();
        ?>
        <div class="wrap suno-admin-wrap">
            <h1><?php _e('Suno Music Generator', 'suno-music-generator'); ?></h1>

            <div class="suno-dashboard">
                <!-- Credits Card -->
                <div class="suno-card">
                    <h2><?php _e('Credits', 'suno-music-generator'); ?></h2>
                    <?php if ($credits['success']) : ?>
                        <div class="suno-credits">
                            <span class="credits-number"><?php echo esc_html($credits['data']['credits']); ?></span>
                            <span class="credits-label"><?php _e('credits còn lại', 'suno-music-generator'); ?></span>
                        </div>
                    <?php else : ?>
                        <p class="error"><?php echo esc_html($credits['message']); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Quick Generate Card -->
                <div class="suno-card suno-card-large">
                    <h2><?php _e('Tạo nhạc nhanh', 'suno-music-generator'); ?></h2>
                    <form id="suno-quick-generate">
                        <div class="suno-form-row">
                            <div class="suno-form-col">
                                <p>
                                    <label for="quick-genre"><strong><?php _e('Thể loại nhạc:', 'suno-music-generator'); ?></strong> <span style="color:red;">*</span></label>
                                    <select id="quick-genre" name="genre" class="regular-text" required style="width: 100%;">
                                        <option value=""><?php _e('-- Chọn thể loại --', 'suno-music-generator'); ?></option>
                                        <?php foreach (self::get_genres() as $group_key => $group) : ?>
                                            <optgroup label="<?php echo esc_attr($group['label']); ?>">
                                                <?php foreach ($group['options'] as $value => $label) : ?>
                                                    <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        <?php endforeach; ?>
                                    </select>
                                </p>
                            </div>
                            <div class="suno-form-col">
                                <p>
                                    <label for="quick-model"><strong><?php _e('AI Model:', 'suno-music-generator'); ?></strong></label>
                                    <select id="quick-model" name="model" class="regular-text" style="width: 100%;">
                                        <?php foreach (Suno_API::get_models() as $key => $label) : ?>
                                            <option value="<?php echo esc_attr($key); ?>" <?php selected(get_option('default_model', 'V3_5'), $key); ?>>
                                                <?php echo esc_html($label); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </p>
                            </div>
                        </div>

                        <p>
                            <label for="quick-prompt"><strong><?php _e('Mô tả chi tiết:', 'suno-music-generator'); ?></strong></label>
                            <textarea id="quick-prompt" name="prompt" rows="3" class="large-text" placeholder="<?php esc_attr_e('VD: Một bài hát về mùa xuân, tình yêu đầu đời, giai điệu nhẹ nhàng...', 'suno-music-generator'); ?>"></textarea>
                            <span class="description"><?php _e('Mô tả thêm về nội dung, cảm xúc, hoặc chủ đề bài hát', 'suno-music-generator'); ?></span>
                        </p>

                        <p>
                            <label>
                                <input type="checkbox" name="instrumental" value="1">
                                <?php _e('Không lời (Instrumental)', 'suno-music-generator'); ?>
                            </label>
                        </p>

                        <p>
                            <button type="submit" class="button button-primary button-large">
                                <span class="dashicons dashicons-format-audio" style="vertical-align: middle;"></span>
                                <?php _e('Tạo Nhạc AI', 'suno-music-generator'); ?>
                            </button>
                        </p>
                    </form>
                    <div id="suno-quick-result" class="suno-result"></div>
                </div>

                <!-- Shortcodes Card -->
                <div class="suno-card">
                    <h2><?php _e('Shortcodes', 'suno-music-generator'); ?></h2>
                    <table class="form-table">
                        <tr>
                            <td><code>[suno_generator]</code></td>
                            <td><?php _e('Form tạo nhạc cơ bản', 'suno-music-generator'); ?></td>
                        </tr>
                        <tr>
                            <td><code>[suno_custom_generator]</code></td>
                            <td><?php _e('Form tạo nhạc với lyrics tùy chỉnh', 'suno-music-generator'); ?></td>
                        </tr>
                        <tr>
                            <td><code>[suno_auto_generator]</code></td>
                            <td><?php _e('Form tạo nhạc tự động với ChatGPT', 'suno-music-generator'); ?></td>
                        </tr>
                        <tr>
                            <td><code>[suno_lyrics_generator]</code></td>
                            <td><?php _e('Form tạo lyrics AI', 'suno-music-generator'); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <script>
        function togglePasswordVisibility(fieldId) {
            var field = document.getElementById(fieldId);
            field.type = field.type === 'password' ? 'text' : 'password';
        }
        </script>
        <?php
    }

    /**
     * Render settings
     */
    public function render_settings() {
        ?>
        <div class="wrap suno-admin-wrap">
            <h1><?php _e('Cài đặt Suno Music Generator', 'suno-music-generator'); ?></h1>

            <form method="post" action="options.php">
                <?php
                settings_fields('suno_settings');
                do_settings_sections('suno_settings');
                submit_button();
                ?>
            </form>

            <div class="suno-card" style="margin-top: 20px;">
                <h2><?php _e('Kiểm tra kết nối', 'suno-music-generator'); ?></h2>
                <button type="button" id="test-connection" class="button">
                    <?php _e('Test API Connection', 'suno-music-generator'); ?>
                </button>
                <div id="test-result"></div>
            </div>
        </div>

        <script>
        function togglePasswordVisibility(fieldId) {
            var field = document.getElementById(fieldId);
            field.type = field.type === 'password' ? 'text' : 'password';
        }

        jQuery(document).ready(function($) {
            $('#test-connection').on('click', function() {
                var $btn = $(this);
                var $result = $('#test-result');

                $btn.prop('disabled', true);
                $result.html('<p>Đang kiểm tra...</p>');

                $.ajax({
                    url: sunoAdmin.restUrl + 'credits',
                    method: 'GET',
                    headers: {
                        'X-WP-Nonce': sunoAdmin.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            $result.html('<p class="success" style="color: green;">✓ Kết nối thành công! Credits: ' + response.data.credits + '</p>');
                        } else {
                            $result.html('<p class="error" style="color: red;">✗ Lỗi: ' + response.message + '</p>');
                        }
                    },
                    error: function(xhr) {
                        $result.html('<p class="error" style="color: red;">✗ Lỗi kết nối</p>');
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                    }
                });
            });
        });
        </script>
        <?php
    }

    /**
     * Render history
     */
    public function render_history() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'suno_history';

        $page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $per_page = 20;
        $offset = ($page - 1) * $per_page;

        $total = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        $items = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT %d OFFSET %d",
                $per_page,
                $offset
            )
        );

        $total_pages = ceil($total / $per_page);
        ?>
        <div class="wrap suno-admin-wrap">
            <h1><?php _e('Lịch sử tạo nhạc', 'suno-music-generator'); ?></h1>

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('ID', 'suno-music-generator'); ?></th>
                        <th><?php _e('Task ID', 'suno-music-generator'); ?></th>
                        <th><?php _e('Tiêu đề', 'suno-music-generator'); ?></th>
                        <th><?php _e('Model', 'suno-music-generator'); ?></th>
                        <th><?php _e('Trạng thái', 'suno-music-generator'); ?></th>
                        <th><?php _e('Ngày tạo', 'suno-music-generator'); ?></th>
                        <th><?php _e('Thao tác', 'suno-music-generator'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)) : ?>
                        <tr>
                            <td colspan="7"><?php _e('Chưa có lịch sử.', 'suno-music-generator'); ?></td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($items as $item) : ?>
                            <tr>
                                <td><?php echo esc_html($item->id); ?></td>
                                <td><code><?php echo esc_html($item->task_id); ?></code></td>
                                <td><?php echo esc_html($item->title ?: '-'); ?></td>
                                <td><?php echo esc_html($item->model); ?></td>
                                <td>
                                    <span class="status-<?php echo esc_attr($item->status); ?>">
                                        <?php echo esc_html($item->status); ?>
                                    </span>
                                </td>
                                <td><?php echo esc_html($item->created_at); ?></td>
                                <td>
                                    <button type="button" class="button button-small view-songs" data-id="<?php echo esc_attr($item->id); ?>">
                                        <?php _e('Xem', 'suno-music-generator'); ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php if ($total_pages > 1) : ?>
                <div class="tablenav bottom">
                    <div class="tablenav-pages">
                        <?php
                        echo paginate_links(array(
                            'base' => add_query_arg('paged', '%#%'),
                            'format' => '',
                            'prev_text' => '&laquo;',
                            'next_text' => '&raquo;',
                            'total' => $total_pages,
                            'current' => $page,
                        ));
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Render guide
     */
    public function render_guide() {
        ?>
        <div class="wrap suno-admin-wrap">
            <h1><?php _e('Hướng dẫn sử dụng', 'suno-music-generator'); ?></h1>

            <div class="suno-card">
                <h2><?php _e('1. Cấu hình API', 'suno-music-generator'); ?></h2>
                <p><?php _e('Đầu tiên, bạn cần lấy API key từ <a href="https://apibox.erweima.ai" target="_blank">apibox.erweima.ai</a> và nhập vào trang Cài đặt.', 'suno-music-generator'); ?></p>
            </div>

            <div class="suno-card">
                <h2><?php _e('2. Sử dụng Shortcodes', 'suno-music-generator'); ?></h2>

                <h3><code>[suno_generator]</code></h3>
                <p><?php _e('Form tạo nhạc cơ bản. Tham số:', 'suno-music-generator'); ?></p>
                <ul>
                    <li><code>model</code> - Model mặc định (V3_5, V4, V4_5)</li>
                    <li><code>show_instrumental</code> - Hiện tùy chọn instrumental (true/false)</li>
                </ul>
                <p><?php _e('Ví dụ:', 'suno-music-generator'); ?> <code>[suno_generator model="V4" show_instrumental="true"]</code></p>

                <h3><code>[suno_custom_generator]</code></h3>
                <p><?php _e('Form tạo nhạc với lyrics tùy chỉnh. Cho phép người dùng nhập lyrics, tiêu đề, và style.', 'suno-music-generator'); ?></p>

                <h3><code>[suno_auto_generator]</code></h3>
                <p><?php _e('Form tạo nhạc tự động với AI (Google Gemini miễn phí hoặc OpenAI). AI sẽ tự động tạo lyrics từ ý tưởng của bạn.', 'suno-music-generator'); ?></p>

                <h3><code>[suno_lyrics_generator]</code></h3>
                <p><?php _e('Form tạo lyrics AI. Chỉ tạo lyrics, không tạo nhạc.', 'suno-music-generator'); ?></p>
            </div>

            <div class="suno-card">
                <h2><?php _e('3. REST API', 'suno-music-generator'); ?></h2>
                <p><?php _e('Plugin cung cấp REST API endpoints tại <code>/wp-json/suno/v1/</code>:', 'suno-music-generator'); ?></p>
                <table class="form-table">
                    <tr>
                        <td><code>POST /generate</code></td>
                        <td><?php _e('Tạo nhạc từ prompt', 'suno-music-generator'); ?></td>
                    </tr>
                    <tr>
                        <td><code>POST /generate-custom</code></td>
                        <td><?php _e('Tạo nhạc với lyrics tùy chỉnh', 'suno-music-generator'); ?></td>
                    </tr>
                    <tr>
                        <td><code>GET /song/{task_id}</code></td>
                        <td><?php _e('Lấy trạng thái và kết quả bài hát', 'suno-music-generator'); ?></td>
                    </tr>
                    <tr>
                        <td><code>GET /credits</code></td>
                        <td><?php _e('Lấy số credits còn lại', 'suno-music-generator'); ?></td>
                    </tr>
                    <tr>
                        <td><code>POST /lyrics</code></td>
                        <td><?php _e('Tạo lyrics từ prompt', 'suno-music-generator'); ?></td>
                    </tr>
                    <tr>
                        <td><code>POST /extend</code></td>
                        <td><?php _e('Mở rộng bài hát đã có', 'suno-music-generator'); ?></td>
                    </tr>
                </table>
            </div>

            <div class="suno-card">
                <h2><?php _e('4. Mẹo sử dụng', 'suno-music-generator'); ?></h2>
                <ul>
                    <li><?php _e('Viết prompt bằng tiếng Anh để có kết quả tốt nhất', 'suno-music-generator'); ?></li>
                    <li><?php _e('Mỗi lần tạo sẽ cho ra 2 phiên bản khác nhau', 'suno-music-generator'); ?></li>
                    <li><?php _e('Thời gian xử lý thường từ 30 giây đến 2 phút', 'suno-music-generator'); ?></li>
                    <li><?php _e('URL audio có thời hạn, hãy tải về nếu muốn lưu trữ lâu dài', 'suno-music-generator'); ?></li>
                </ul>
            </div>
        </div>
        <?php
    }
}
