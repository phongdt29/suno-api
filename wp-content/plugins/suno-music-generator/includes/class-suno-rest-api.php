<?php
/**
 * Suno REST API Class
 *
 * Handles all REST API endpoints
 */

if (!defined('ABSPATH')) {
    exit;
}

class Suno_Rest_API {

    /**
     * Namespace
     */
    const NAMESPACE = 'suno/v1';

    /**
     * Register routes
     */
    public static function register_routes() {
        // Get credits
        register_rest_route(self::NAMESPACE, '/credits', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_credits'),
            'permission_callback' => array(__CLASS__, 'check_permission'),
        ));

        // Generate song (simple mode)
        register_rest_route(self::NAMESPACE, '/generate', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'generate'),
            'permission_callback' => array(__CLASS__, 'check_permission'),
            'args' => array(
                'prompt' => array(
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'instrumental' => array(
                    'required' => false,
                    'type' => 'boolean',
                    'default' => false,
                ),
                'model' => array(
                    'required' => false,
                    'type' => 'string',
                    'default' => '',
                ),
            ),
        ));

        // Generate custom song (with lyrics)
        register_rest_route(self::NAMESPACE, '/generate-custom', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'generate_custom'),
            'permission_callback' => array(__CLASS__, 'check_permission'),
            'args' => array(
                'lyrics' => array(
                    'required' => true,
                    'type' => 'string',
                ),
                'title' => array(
                    'required' => false,
                    'type' => 'string',
                ),
                'style' => array(
                    'required' => false,
                    'type' => 'string',
                ),
                'instrumental' => array(
                    'required' => false,
                    'type' => 'boolean',
                    'default' => false,
                ),
                'model' => array(
                    'required' => false,
                    'type' => 'string',
                ),
            ),
        ));

        // Get song status
        register_rest_route(self::NAMESPACE, '/song/(?P<task_id>[a-zA-Z0-9-]+)', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_song'),
            'permission_callback' => array(__CLASS__, 'check_permission'),
            'args' => array(
                'task_id' => array(
                    'required' => true,
                    'type' => 'string',
                ),
            ),
        ));

        // Extend song
        register_rest_route(self::NAMESPACE, '/extend', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'extend'),
            'permission_callback' => array(__CLASS__, 'check_permission'),
            'args' => array(
                'audio_id' => array(
                    'required' => true,
                    'type' => 'string',
                ),
                'prompt' => array(
                    'required' => false,
                    'type' => 'string',
                ),
                'style' => array(
                    'required' => false,
                    'type' => 'string',
                ),
                'continue_at' => array(
                    'required' => false,
                    'type' => 'integer',
                ),
                'model' => array(
                    'required' => false,
                    'type' => 'string',
                ),
            ),
        ));

        // Generate lyrics
        register_rest_route(self::NAMESPACE, '/lyrics', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'generate_lyrics'),
            'permission_callback' => array(__CLASS__, 'check_permission'),
            'args' => array(
                'prompt' => array(
                    'required' => true,
                    'type' => 'string',
                ),
            ),
        ));

        // Get lyrics
        register_rest_route(self::NAMESPACE, '/lyrics/(?P<task_id>[a-zA-Z0-9-]+)', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_lyrics'),
            'permission_callback' => array(__CLASS__, 'check_permission'),
            'args' => array(
                'task_id' => array(
                    'required' => true,
                    'type' => 'string',
                ),
            ),
        ));

        // Auto generate with ChatGPT
        register_rest_route(self::NAMESPACE, '/auto-generate', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'auto_generate'),
            'permission_callback' => array(__CLASS__, 'check_permission'),
            'args' => array(
                'idea' => array(
                    'required' => true,
                    'type' => 'string',
                ),
                'language' => array(
                    'required' => false,
                    'type' => 'string',
                    'default' => 'vietnamese',
                ),
                'model' => array(
                    'required' => false,
                    'type' => 'string',
                ),
            ),
        ));

        // Upload audio
        register_rest_route(self::NAMESPACE, '/upload', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'upload_audio'),
            'permission_callback' => array(__CLASS__, 'check_admin_permission'),
            'args' => array(
                'audio_url' => array(
                    'required' => true,
                    'type' => 'string',
                ),
            ),
        ));

        // Get history
        register_rest_route(self::NAMESPACE, '/history', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_history'),
            'permission_callback' => array(__CLASS__, 'check_permission'),
            'args' => array(
                'page' => array(
                    'required' => false,
                    'type' => 'integer',
                    'default' => 1,
                ),
                'per_page' => array(
                    'required' => false,
                    'type' => 'integer',
                    'default' => 10,
                ),
            ),
        ));
    }

    /**
     * Check permission
     */
    public static function check_permission() {
        // Allow all logged in users, or customize as needed
        return true;
    }

    /**
     * Check admin permission
     */
    public static function check_admin_permission() {
        return current_user_can('manage_options');
    }

    /**
     * Get API instance
     */
    private static function get_api() {
        return new Suno_API();
    }

    /**
     * Get credits
     */
    public static function get_credits($request) {
        $api = self::get_api();
        $result = $api->get_credits();

        if ($result['success']) {
            return new WP_REST_Response($result, 200);
        }

        return new WP_REST_Response($result, 400);
    }

    /**
     * Generate song
     */
    public static function generate($request) {
        $api = self::get_api();

        $prompt = $request->get_param('prompt');
        $options = array(
            'instrumental' => $request->get_param('instrumental'),
            'model' => $request->get_param('model') ?: get_option('default_model', 'V3_5'),
        );

        $result = $api->generate($prompt, $options);

        if ($result['success']) {
            // Save to history
            self::save_history(array(
                'task_id' => $result['data']['taskId'],
                'prompt' => $prompt,
                'model' => $options['model'],
                'status' => 'pending',
            ));

            return new WP_REST_Response($result, 200);
        }

        return new WP_REST_Response($result, 400);
    }

    /**
     * Generate custom song
     */
    public static function generate_custom($request) {
        $api = self::get_api();

        $lyrics = $request->get_param('lyrics');
        $options = array(
            'title' => $request->get_param('title'),
            'style' => $request->get_param('style'),
            'instrumental' => $request->get_param('instrumental'),
            'model' => $request->get_param('model') ?: get_option('default_model', 'V3_5'),
        );

        $result = $api->generate_custom($lyrics, $options);

        if ($result['success']) {
            // Save to history
            self::save_history(array(
                'task_id' => $result['data']['taskId'],
                'lyrics' => $lyrics,
                'title' => $options['title'],
                'style' => $options['style'],
                'model' => $options['model'],
                'status' => 'pending',
            ));

            return new WP_REST_Response($result, 200);
        }

        return new WP_REST_Response($result, 400);
    }

    /**
     * Get song status
     */
    public static function get_song($request) {
        $api = self::get_api();
        $task_id = $request->get_param('task_id');

        $result = $api->get_song($task_id);

        if ($result['success']) {
            // Update history
            if ($result['data']['status'] === 'completed' || $result['data']['status'] === 'failed') {
                self::update_history($task_id, array(
                    'status' => $result['data']['status'],
                    'songs' => isset($result['data']['songs']) ? wp_json_encode($result['data']['songs']) : null,
                ));
            }

            return new WP_REST_Response($result, 200);
        }

        return new WP_REST_Response($result, 400);
    }

    /**
     * Extend song
     */
    public static function extend($request) {
        $api = self::get_api();

        $audio_id = $request->get_param('audio_id');
        $options = array(
            'prompt' => $request->get_param('prompt'),
            'style' => $request->get_param('style'),
            'continue_at' => $request->get_param('continue_at'),
            'model' => $request->get_param('model') ?: get_option('default_model', 'V3_5'),
        );

        $result = $api->extend($audio_id, $options);

        if ($result['success']) {
            return new WP_REST_Response($result, 200);
        }

        return new WP_REST_Response($result, 400);
    }

    /**
     * Generate lyrics
     */
    public static function generate_lyrics($request) {
        $api = self::get_api();
        $prompt = $request->get_param('prompt');

        $result = $api->generate_lyrics($prompt);

        if ($result['success']) {
            return new WP_REST_Response($result, 200);
        }

        return new WP_REST_Response($result, 400);
    }

    /**
     * Get lyrics
     */
    public static function get_lyrics($request) {
        $api = self::get_api();
        $task_id = $request->get_param('task_id');

        $result = $api->get_lyrics($task_id);

        if ($result['success']) {
            return new WP_REST_Response($result, 200);
        }

        return new WP_REST_Response($result, 400);
    }

    /**
     * Auto generate with ChatGPT
     */
    public static function auto_generate($request) {
        $openai_key = get_option('openai_api_key', '');

        if (empty($openai_key)) {
            return new WP_REST_Response(array(
                'success' => false,
                'message' => __('OpenAI API key chưa được cấu hình', 'suno-music-generator'),
            ), 400);
        }

        $idea = $request->get_param('idea');
        $language = $request->get_param('language');

        // Generate song content with ChatGPT
        $gpt_result = self::call_chatgpt($idea, $language, $openai_key);

        if (!$gpt_result['success']) {
            return new WP_REST_Response($gpt_result, 400);
        }

        // Generate song with Suno
        $api = self::get_api();
        $options = array(
            'title' => $gpt_result['data']['title'],
            'style' => $gpt_result['data']['style'],
            'model' => $request->get_param('model') ?: get_option('default_model', 'V3_5'),
        );

        $result = $api->generate_custom($gpt_result['data']['lyrics'], $options);

        if ($result['success']) {
            $result['data']['gpt_content'] = $gpt_result['data'];

            // Save to history
            self::save_history(array(
                'task_id' => $result['data']['taskId'],
                'prompt' => $idea,
                'lyrics' => $gpt_result['data']['lyrics'],
                'title' => $gpt_result['data']['title'],
                'style' => $gpt_result['data']['style'],
                'model' => $options['model'],
                'status' => 'pending',
            ));

            return new WP_REST_Response($result, 200);
        }

        return new WP_REST_Response($result, 400);
    }

    /**
     * Call ChatGPT API
     */
    private static function call_chatgpt($idea, $language, $api_key) {
        $language_map = array(
            'vietnamese' => 'tiếng Việt',
            'english' => 'English',
            'korean' => '한국어',
            'japanese' => '日本語',
        );

        $lang_name = $language_map[$language] ?? 'English';

        $system_prompt = "Bạn là một nhạc sĩ chuyên nghiệp. Khi nhận được một ý tưởng, hãy tạo ra:
1. Tiêu đề bài hát (title)
2. Phong cách âm nhạc (style) - ví dụ: Pop, Rock, Ballad, R&B, Hip-hop, Jazz...
3. Lời bài hát (lyrics) với đầy đủ các phần: Verse, Chorus, Bridge...

Hãy viết lời bằng {$lang_name}.

Trả về JSON với format:
{
    \"title\": \"Tiêu đề\",
    \"style\": \"Phong cách\",
    \"lyrics\": \"Lời bài hát đầy đủ\"
}";

        $response = wp_remote_post('https://api.openai.com/v1/chat/completions', array(
            'timeout' => 60,
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
            ),
            'body' => wp_json_encode(array(
                'model' => 'gpt-3.5-turbo',
                'messages' => array(
                    array('role' => 'system', 'content' => $system_prompt),
                    array('role' => 'user', 'content' => $idea),
                ),
                'temperature' => 0.8,
                'max_tokens' => 2000,
            )),
        ));

        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => $response->get_error_message(),
            );
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        $http_code = wp_remote_retrieve_response_code($response);

        // Log for debugging
        error_log('OpenAI Response Code: ' . $http_code);
        error_log('OpenAI Response Body: ' . $body);

        if (!isset($data['choices'][0]['message']['content'])) {
            $error_msg = __('Không thể tạo nội dung từ ChatGPT', 'suno-music-generator');

            if (isset($data['error']['message'])) {
                $error_msg = 'OpenAI Error: ' . $data['error']['message'];
            } elseif ($http_code !== 200) {
                $error_msg = 'OpenAI HTTP Error: ' . $http_code . ' - ' . $body;
            }

            return array(
                'success' => false,
                'message' => $error_msg,
            );
        }

        $content = $data['choices'][0]['message']['content'];

        // Parse JSON from response
        preg_match('/\{[\s\S]*\}/', $content, $matches);
        if (empty($matches)) {
            return array(
                'success' => false,
                'message' => __('Không thể parse kết quả từ ChatGPT', 'suno-music-generator'),
            );
        }

        $song_data = json_decode($matches[0], true);

        if (!$song_data || !isset($song_data['lyrics'])) {
            return array(
                'success' => false,
                'message' => __('Dữ liệu từ ChatGPT không hợp lệ', 'suno-music-generator'),
            );
        }

        return array(
            'success' => true,
            'data' => array(
                'title' => $song_data['title'] ?? '',
                'style' => $song_data['style'] ?? '',
                'lyrics' => $song_data['lyrics'],
            ),
        );
    }

    /**
     * Upload audio
     */
    public static function upload_audio($request) {
        $api = self::get_api();
        $audio_url = $request->get_param('audio_url');

        $result = $api->upload_audio($audio_url);

        if ($result['success']) {
            return new WP_REST_Response($result, 200);
        }

        return new WP_REST_Response($result, 400);
    }

    /**
     * Get history
     */
    public static function get_history($request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'suno_history';

        $page = $request->get_param('page');
        $per_page = $request->get_param('per_page');
        $offset = ($page - 1) * $per_page;

        $user_id = get_current_user_id();

        $where = '';
        if ($user_id > 0 && !current_user_can('manage_options')) {
            $where = $wpdb->prepare(' WHERE user_id = %d', $user_id);
        }

        $total = $wpdb->get_var("SELECT COUNT(*) FROM $table_name $where");
        $items = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table_name $where ORDER BY created_at DESC LIMIT %d OFFSET %d",
                $per_page,
                $offset
            )
        );

        // Decode songs JSON
        foreach ($items as &$item) {
            if ($item->songs) {
                $item->songs = json_decode($item->songs, true);
            }
        }

        return new WP_REST_Response(array(
            'success' => true,
            'data' => array(
                'items' => $items,
                'total' => (int) $total,
                'page' => $page,
                'per_page' => $per_page,
                'total_pages' => ceil($total / $per_page),
            ),
        ), 200);
    }

    /**
     * Save to history
     */
    private static function save_history($data) {
        if (!get_option('enable_history', true)) {
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'suno_history';

        $wpdb->insert($table_name, array(
            'user_id' => get_current_user_id(),
            'task_id' => $data['task_id'],
            'prompt' => $data['prompt'] ?? null,
            'lyrics' => $data['lyrics'] ?? null,
            'title' => $data['title'] ?? null,
            'style' => $data['style'] ?? null,
            'model' => $data['model'] ?? null,
            'status' => $data['status'],
        ));
    }

    /**
     * Update history
     */
    private static function update_history($task_id, $data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'suno_history';

        $wpdb->update(
            $table_name,
            $data,
            array('task_id' => $task_id)
        );
    }
}
