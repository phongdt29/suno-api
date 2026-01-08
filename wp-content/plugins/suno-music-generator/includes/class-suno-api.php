<?php
/**
 * Suno API Class
 *
 * Handles all communication with the Suno API backend
 */

if (!defined('ABSPATH')) {
    exit;
}

class Suno_API {

    /**
     * API Base URL
     */
    private $base_url = 'https://apibox.erweima.ai';

    /**
     * API Key
     */
    private $api_key;

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
    public function __construct($api_key = null) {
        $this->api_key = $api_key ?: get_option('suno_api_key', 'd0f8edfa4b6f1adace734102152f3bfb');
    }

    /**
     * Set API Key
     */
    public function set_api_key($api_key) {
        $this->api_key = $api_key;
    }

    /**
     * Make API request
     */
    private function request($endpoint, $method = 'GET', $data = null) {
        if (empty($this->api_key)) {
            return array(
                'success' => false,
                'message' => __('API key chưa được cấu hình', 'suno-music-generator'),
            );
        }

        $url = $this->base_url . $endpoint;

        $args = array(
            'method' => $method,
            'timeout' => 60,
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->api_key,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ),
        );

        if ($data && in_array($method, array('POST', 'PUT', 'PATCH'))) {
            $args['body'] = wp_json_encode($data);
        }

        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => $response->get_error_message(),
            );
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return array(
                'success' => false,
                'message' => __('Phản hồi API không hợp lệ', 'suno-music-generator'),
            );
        }

        return $data;
    }

    /**
     * Get available credits
     */
    public function get_credits() {
        $response = $this->request('/api/v1/generate/quota');

        if (isset($response['code']) && $response['code'] == 200) {
            return array(
                'success' => true,
                'data' => array(
                    'credits' => $response['data']['credits'] ?? 0,
                ),
            );
        }

        return array(
            'success' => false,
            'message' => $response['msg'] ?? __('Không thể lấy thông tin credits', 'suno-music-generator'),
        );
    }

    /**
     * Generate song (simple mode)
     */
    public function generate($prompt, $options = array()) {
        $defaults = array(
            'instrumental' => false,
            'model' => get_option('default_model', 'V3_5'),
            'callbackUrl' => home_url('/wp-json/suno/v1/callback'),
        );

        $options = wp_parse_args($options, $defaults);

        $data = array(
            'prompt' => sanitize_text_field($prompt),
            'customMode' => false,
            'instrumental' => (bool) $options['instrumental'],
            'model' => sanitize_text_field($options['model']),
        );

        if (!empty($options['callbackUrl'])) {
            $data['callbackUrl'] = esc_url_raw($options['callbackUrl']);
        }

        $response = $this->request('/api/v1/generate', 'POST', $data);

        // Debug log
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Suno API generate response: ' . print_r($response, true));
        }

        if (isset($response['code']) && $response['code'] == 200) {
            return array(
                'success' => true,
                'data' => array(
                    'taskId' => $response['data']['taskId'] ?? '',
                ),
            );
        }

        return array(
            'success' => false,
            'message' => $response['msg'] ?? __('Không thể tạo bài hát', 'suno-music-generator'),
            'debug' => $response,
        );
    }

    /**
     * Generate custom song (with lyrics)
     */
    public function generate_custom($lyrics, $options = array()) {
        $defaults = array(
            'title' => '',
            'style' => '',
            'instrumental' => false,
            'model' => get_option('default_model', 'V3_5'),
            'callbackUrl' => home_url('/wp-json/suno/v1/callback'),
        );

        $options = wp_parse_args($options, $defaults);

        $data = array(
            'prompt' => sanitize_textarea_field($lyrics),
            'customMode' => true,
            'instrumental' => (bool) $options['instrumental'],
            'model' => sanitize_text_field($options['model']),
        );

        if (!empty($options['title'])) {
            $data['title'] = sanitize_text_field($options['title']);
        }

        if (!empty($options['style'])) {
            $data['style'] = sanitize_text_field($options['style']);
        }

        if (!empty($options['callbackUrl'])) {
            $data['callbackUrl'] = esc_url_raw($options['callbackUrl']);
        }

        $response = $this->request('/api/v1/generate', 'POST', $data);

        if (isset($response['code']) && $response['code'] == 200) {
            return array(
                'success' => true,
                'data' => array(
                    'taskId' => $response['data']['taskId'] ?? '',
                ),
            );
        }

        return array(
            'success' => false,
            'message' => $response['msg'] ?? __('Không thể tạo bài hát', 'suno-music-generator'),
        );
    }

    /**
     * Get song status and results
     */
    public function get_song($task_id) {
        $response = $this->request('/api/v1/generate/record-info?taskId=' . urlencode($task_id));

        // Debug log
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Suno API get_song response: ' . print_r($response, true));
        }

        if (isset($response['code']) && $response['code'] == 200) {
            $data = $response['data'] ?? array();
            $status = $this->map_status($data['status'] ?? 'PENDING');

            $result = array(
                'success' => true,
                'data' => array(
                    'status' => $status,
                    'taskId' => $task_id,
                ),
            );

            // If completed, include songs
            if ($status === 'completed' && !empty($data['response']['sunoData'])) {
                $songs = array();
                foreach ($data['response']['sunoData'] as $song) {
                    $songs[] = array(
                        'id' => $song['id'] ?? '',
                        'title' => $song['title'] ?? '',
                        'audio_url' => $song['audioUrl'] ?? $song['audio_url'] ?? '',
                        'image_url' => $song['imageUrl'] ?? $song['image_url'] ?? '',
                        'video_url' => $song['videoUrl'] ?? $song['video_url'] ?? '',
                        'duration' => $song['duration'] ?? 0,
                        'style' => $song['style'] ?? '',
                        'lyrics' => $song['prompt'] ?? '',
                    );
                }
                $result['data']['songs'] = $songs;
            }

            // If failed, include error
            if ($status === 'failed') {
                $result['data']['error'] = $data['response']['errorMessage'] ?? __('Tạo bài hát thất bại', 'suno-music-generator');
            }

            return $result;
        }

        return array(
            'success' => false,
            'message' => $response['msg'] ?? __('Không thể lấy thông tin bài hát', 'suno-music-generator'),
        );
    }

    /**
     * Extend song
     */
    public function extend($audio_id, $options = array()) {
        $defaults = array(
            'prompt' => '',
            'style' => '',
            'continue_at' => 0,
            'model' => get_option('default_model', 'V3_5'),
        );

        $options = wp_parse_args($options, $defaults);

        $data = array(
            'audioId' => sanitize_text_field($audio_id),
            'model' => sanitize_text_field($options['model']),
        );

        if (!empty($options['prompt'])) {
            $data['prompt'] = sanitize_textarea_field($options['prompt']);
        }

        if (!empty($options['style'])) {
            $data['style'] = sanitize_text_field($options['style']);
        }

        if ($options['continue_at'] > 0) {
            $data['continueAt'] = (int) $options['continue_at'];
        }

        $response = $this->request('/api/v1/generate/extend', 'POST', $data);

        if (isset($response['code']) && $response['code'] == 200) {
            return array(
                'success' => true,
                'data' => array(
                    'taskId' => $response['data']['taskId'] ?? '',
                ),
            );
        }

        return array(
            'success' => false,
            'message' => $response['msg'] ?? __('Không thể mở rộng bài hát', 'suno-music-generator'),
        );
    }

    /**
     * Generate lyrics
     */
    public function generate_lyrics($prompt) {
        $data = array(
            'prompt' => sanitize_text_field($prompt),
        );

        $response = $this->request('/api/v1/lyrics', 'POST', $data);

        if (isset($response['code']) && $response['code'] == 200) {
            return array(
                'success' => true,
                'data' => array(
                    'taskId' => $response['data']['taskId'] ?? '',
                ),
            );
        }

        return array(
            'success' => false,
            'message' => $response['msg'] ?? __('Không thể tạo lyrics', 'suno-music-generator'),
        );
    }

    /**
     * Get generated lyrics
     */
    public function get_lyrics($task_id) {
        $response = $this->request('/api/v1/lyrics/record-info?taskId=' . urlencode($task_id));

        if (isset($response['code']) && $response['code'] == 200) {
            $data = $response['data'] ?? array();
            $status = $this->map_status($data['status'] ?? 'PENDING');

            $result = array(
                'success' => true,
                'data' => array(
                    'status' => $status,
                    'taskId' => $task_id,
                ),
            );

            if ($status === 'completed' && !empty($data['response'])) {
                $result['data']['title'] = $data['response']['title'] ?? '';
                $result['data']['lyrics'] = $data['response']['text'] ?? '';
            }

            return $result;
        }

        return array(
            'success' => false,
            'message' => $response['msg'] ?? __('Không thể lấy lyrics', 'suno-music-generator'),
        );
    }

    /**
     * Upload audio
     */
    public function upload_audio($audio_url) {
        $data = array(
            'audioUrl' => esc_url_raw($audio_url),
        );

        $response = $this->request('/api/v1/uploads/audio', 'POST', $data);

        if (isset($response['code']) && $response['code'] == 200) {
            return array(
                'success' => true,
                'data' => $response['data'] ?? array(),
            );
        }

        return array(
            'success' => false,
            'message' => $response['msg'] ?? __('Không thể upload audio', 'suno-music-generator'),
        );
    }

    /**
     * Map API status to standard status
     */
    private function map_status($status) {
        $status_map = array(
            'PENDING' => 'pending',
            'TEXT_SUCCESS' => 'processing',
            'FIRST_SUCCESS' => 'processing',
            'SUCCESS' => 'completed',
            'CREATE_TASK_FAILED' => 'failed',
            'GENERATE_AUDIO_FAILED' => 'failed',
            'CALLBACK_EXCEPTION' => 'failed',
            'SENSITIVE_WORD_ERROR' => 'failed',
        );

        return $status_map[strtoupper($status)] ?? 'processing';
    }

    /**
     * Get available models
     */
    public static function get_models() {
        return array(
            'V3_5' => 'Suno V3.5 (Mặc định)',
            'V4' => 'Suno V4',
            'V4_5' => 'Suno V4.5 (Mới nhất)',
        );
    }
}
