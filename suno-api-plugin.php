<?php
/**
 * Plugin Name: Suno API + ChatGPT Integration
 * Description: Tích hợp Suno AI Music Generator + ChatGPT vào WordPress
 * Version: 2.0.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) {
    exit;
}

class SunoAPI_Plugin {
    private $baseUrl = 'https://apibox.erweima.ai';
    private $apiKey;
    private $openaiKey;

    public function __construct() {
        $this->apiKey = get_option('suno_api_key', 'd0f8edfa4b6f1adace734102152f3bfb');
        $this->openaiKey = get_option('openai_api_key', '');

        // Admin menu
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);

        // REST API endpoints
        add_action('rest_api_init', [$this, 'register_rest_routes']);

        // Shortcodes
        add_shortcode('suno_generator', [$this, 'generator_shortcode']);
        add_shortcode('suno_auto_generator', [$this, 'auto_generator_shortcode']);

        // Enqueue scripts
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    /**
     * Gửi request đến Suno API
     */
    private function request($endpoint, $method = 'GET', $data = null) {
        $url = $this->baseUrl . $endpoint;

        $args = [
            'method' => $method,
            'timeout' => 30,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ],
        ];

        if ($data && $method === 'POST') {
            $args['body'] = json_encode($data);
        }

        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            return ['error' => $response->get_error_message(), 'code' => 0];
        }

        return [
            'data' => json_decode(wp_remote_retrieve_body($response), true),
            'code' => wp_remote_retrieve_response_code($response)
        ];
    }

    /**
     * Tạo bài hát
     */
    public function generateSong($prompt, $instrumental = false, $model = 'V3_5') {
        $data = [
            'prompt' => $prompt,
            'customMode' => false,
            'instrumental' => $instrumental,
            'model' => $model
        ];
        return $this->request('/api/v1/generate', 'POST', $data);
    }

    /**
     * Tạo bài hát custom
     */
    public function generateCustomSong($title, $lyrics, $style, $instrumental = false, $model = 'V3_5') {
        $data = [
            'prompt' => $lyrics,
            'style' => $style,
            'title' => $title,
            'customMode' => true,
            'instrumental' => $instrumental,
            'model' => $model
        ];
        return $this->request('/api/v1/generate', 'POST', $data);
    }

    /**
     * Lấy thông tin bài hát
     */
    public function getSongByTaskId($taskId) {
        return $this->request('/api/v1/generate/record-info?taskId=' . $taskId);
    }

    /**
     * Lấy credits
     */
    public function getCredits() {
        return $this->request('/api/v1/generate/quota');
    }

    /**
     * Tạo lyrics
     */
    public function generateLyrics($prompt) {
        return $this->request('/api/v1/lyrics', 'POST', ['prompt' => $prompt]);
    }

    /**
     * Gọi ChatGPT API
     */
    private function chatGPT($messages, $model = 'gpt-4o-mini') {
        if (empty($this->openaiKey)) {
            return ['error' => 'OpenAI API key not configured', 'code' => 0];
        }

        $response = wp_remote_post('https://api.openai.com/v1/chat/completions', [
            'timeout' => 60,
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->openaiKey,
            ],
            'body' => json_encode([
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.8,
            ]),
        ]);

        if (is_wp_error($response)) {
            return ['error' => $response->get_error_message(), 'code' => 0];
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        return [
            'data' => $body,
            'code' => wp_remote_retrieve_response_code($response)
        ];
    }

    /**
     * Tạo prompt cho Suno từ ý tưởng
     */
    public function generateSunoPrompt($idea, $language = 'vi') {
        $systemPrompt = "Bạn là chuyên gia sáng tác nhạc. Nhiệm vụ của bạn là tạo prompt cho Suno AI để tạo bài hát.

Khi nhận được ý tưởng, hãy trả về JSON với format:
{
    \"title\": \"Tên bài hát\",
    \"style\": \"Thể loại nhạc (VD: pop ballad, rock, EDM, Vietnamese pop)\",
    \"prompt\": \"Mô tả ngắn gọn về bài hát bằng tiếng Anh cho Suno\",
    \"lyrics\": \"Lời bài hát đầy đủ với [Verse], [Chorus], [Bridge]\"
}

Lưu ý:
- Lyrics nên có cấu trúc rõ ràng với các section
- Style phải phù hợp với nội dung
- Prompt cho Suno nên bằng tiếng Anh
- Nếu yêu cầu tiếng Việt, lyrics viết bằng tiếng Việt";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => "Tạo bài hát với ý tưởng: " . $idea . "\nNgôn ngữ lyrics: " . $language]
        ];

        return $this->chatGPT($messages);
    }

    /**
     * Auto generate: ChatGPT tạo lyrics -> Suno tạo nhạc
     */
    public function autoGenerate($idea, $language = 'vi', $model = 'V3_5') {
        // Bước 1: ChatGPT tạo prompt và lyrics
        $gptResult = $this->generateSunoPrompt($idea, $language);

        if (isset($gptResult['error']) || $gptResult['code'] !== 200) {
            return ['success' => false, 'step' => 'chatgpt', 'error' => $gptResult];
        }

        $content = $gptResult['data']['choices'][0]['message']['content'] ?? '';

        // Parse JSON từ response
        preg_match('/\{[\s\S]*\}/', $content, $matches);
        if (empty($matches)) {
            return ['success' => false, 'step' => 'parse', 'error' => 'Cannot parse ChatGPT response', 'raw' => $content];
        }

        $songData = json_decode($matches[0], true);
        if (!$songData) {
            return ['success' => false, 'step' => 'parse', 'error' => 'Invalid JSON', 'raw' => $matches[0]];
        }

        // Bước 2: Gửi đến Suno
        $sunoResult = $this->generateCustomSong(
            $songData['title'] ?? 'Untitled',
            $songData['lyrics'] ?? '',
            $songData['style'] ?? 'pop',
            false,
            $model
        );

        return [
            'success' => $sunoResult['code'] === 200,
            'chatgpt' => $songData,
            'suno' => $sunoResult
        ];
    }

    /**
     * Admin Menu
     */
    public function add_admin_menu() {
        add_menu_page(
            'Suno API',
            'Suno API',
            'manage_options',
            'suno-api',
            [$this, 'admin_page'],
            'dashicons-format-audio',
            30
        );
    }

    /**
     * Register Settings
     */
    public function register_settings() {
        register_setting('suno_api_settings', 'suno_api_key');
        register_setting('suno_api_settings', 'openai_api_key');
    }

    /**
     * Admin Page
     */
    public function admin_page() {
        $credits = $this->getCredits();
        ?>
        <div class="wrap">
            <h1>Suno API + ChatGPT Settings</h1>

            <form method="post" action="options.php">
                <?php settings_fields('suno_api_settings'); ?>
                <table class="form-table">
                    <tr>
                        <th>Suno API Key</th>
                        <td>
                            <input type="text" name="suno_api_key" value="<?php echo esc_attr(get_option('suno_api_key')); ?>" class="regular-text">
                            <p class="description">API key từ apibox.erweima.ai</p>
                        </td>
                    </tr>
                    <tr>
                        <th>OpenAI API Key</th>
                        <td>
                            <input type="password" name="openai_api_key" value="<?php echo esc_attr(get_option('openai_api_key')); ?>" class="regular-text">
                            <p class="description">API key từ platform.openai.com</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>

            <hr>
            <h2>API Status</h2>
            <?php if ($credits['code'] === 200): ?>
                <p><strong>Suno Credits:</strong> <?php echo esc_html(json_encode($credits['data'])); ?></p>
            <?php else: ?>
                <p style="color:red;">Không thể kết nối Suno API</p>
            <?php endif; ?>
            <p><strong>OpenAI:</strong> <?php echo get_option('openai_api_key') ? '✅ Đã cấu hình' : '❌ Chưa cấu hình'; ?></p>

            <hr>
            <h2>🤖 Auto Generate (ChatGPT + Suno)</h2>
            <p>Chỉ cần nhập ý tưởng, ChatGPT sẽ tự động viết lyrics và gửi đến Suno tạo nhạc.</p>
            <form method="post" id="auto-generate-form">
                <table class="form-table">
                    <tr>
                        <th>Ý tưởng bài hát</th>
                        <td><textarea name="idea" rows="3" class="large-text" placeholder="VD: Bài hát về tình yêu đầu đời của tuổi học trò"></textarea></td>
                    </tr>
                    <tr>
                        <th>Ngôn ngữ lyrics</th>
                        <td>
                            <select name="language">
                                <option value="vi">Tiếng Việt</option>
                                <option value="en">English</option>
                                <option value="ko">한국어</option>
                                <option value="ja">日本語</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Suno Model</th>
                        <td>
                            <select name="model">
                                <option value="V3_5">V3.5</option>
                                <option value="V4">V4</option>
                                <option value="V4_5">V4.5</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <button type="button" class="button button-primary button-hero" onclick="autoGenerate()">🎵 Tự động tạo bài hát</button>
            </form>
            <div id="auto-result" style="margin-top:20px;"></div>

            <hr>
            <h2>Manual Generate (Suno only)</h2>
            <form method="post" id="suno-test-form">
                <table class="form-table">
                    <tr>
                        <th>Prompt</th>
                        <td><textarea name="prompt" rows="3" class="large-text" placeholder="A happy pop song about love"></textarea></td>
                    </tr>
                    <tr>
                        <th>Model</th>
                        <td>
                            <select name="model">
                                <option value="V3_5">V3.5</option>
                                <option value="V4">V4</option>
                                <option value="V4_5">V4.5</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <button type="button" class="button button-primary" onclick="testGenerate()">Generate Song</button>
            </form>
            <div id="suno-result" style="margin-top:20px;"></div>

            <hr>
            <h2>Shortcodes</h2>
            <table class="widefat">
                <tr><td><code>[suno_generator]</code></td><td>Form tạo nhạc thủ công</td></tr>
                <tr><td><code>[suno_auto_generator]</code></td><td>Form tự động với ChatGPT</td></tr>
            </table>
        </div>

        <script>
        async function autoGenerate() {
            const form = document.getElementById('auto-generate-form');
            const idea = form.querySelector('[name="idea"]').value;
            const language = form.querySelector('[name="language"]').value;
            const model = form.querySelector('[name="model"]').value;
            const resultDiv = document.getElementById('auto-result');

            if (!idea) {
                alert('Vui lòng nhập ý tưởng bài hát');
                return;
            }

            resultDiv.innerHTML = '<p>⏳ Bước 1: ChatGPT đang viết lyrics...</p>';

            try {
                const response = await fetch('<?php echo rest_url('suno/v1/auto-generate'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                    },
                    body: JSON.stringify({ idea, language, model })
                });
                const data = await response.json();

                if (data.success) {
                    let html = '<div style="background:#e7f5e7;padding:15px;border-radius:5px;">';
                    html += '<h3>✅ Đã gửi đến Suno!</h3>';
                    html += '<p><strong>Tiêu đề:</strong> ' + (data.chatgpt?.title || 'N/A') + '</p>';
                    html += '<p><strong>Style:</strong> ' + (data.chatgpt?.style || 'N/A') + '</p>';
                    html += '<p><strong>Task ID:</strong> ' + (data.suno?.data?.taskId || 'N/A') + '</p>';
                    html += '<h4>Lyrics:</h4><pre style="background:#fff;padding:10px;white-space:pre-wrap;">' + (data.chatgpt?.lyrics || '') + '</pre>';
                    html += '</div>';
                    resultDiv.innerHTML = html;

                    if (data.suno?.data?.taskId) {
                        pollAutoResult(data.suno.data.taskId);
                    }
                } else {
                    resultDiv.innerHTML = '<div style="background:#fdd;padding:15px;"><h3>❌ Lỗi</h3><pre>' + JSON.stringify(data, null, 2) + '</pre></div>';
                }
            } catch (error) {
                resultDiv.innerHTML = '<p style="color:red;">Lỗi: ' + error.message + '</p>';
            }
        }

        async function pollAutoResult(taskId) {
            const resultDiv = document.getElementById('auto-result');
            let attempts = 0;

            const check = async () => {
                attempts++;
                try {
                    const res = await fetch('<?php echo rest_url('suno/v1/song/'); ?>' + taskId);
                    const result = await res.json();

                    if (result.data && result.data.status === 'completed') {
                        let html = resultDiv.innerHTML;
                        html += '<div style="background:#e7f5e7;padding:15px;margin-top:10px;border-radius:5px;">';
                        html += '<h3>🎵 Bài hát đã tạo xong!</h3>';
                        if (result.data.songs) {
                            result.data.songs.forEach(song => {
                                html += '<div style="margin:10px 0;padding:10px;background:#fff;border-radius:5px;">';
                                html += '<p><strong>' + (song.title || 'Untitled') + '</strong></p>';
                                html += '<audio controls src="' + song.audio_url + '" style="width:100%;"></audio>';
                                html += '</div>';
                            });
                        }
                        html += '</div>';
                        resultDiv.innerHTML = html;
                    } else if (attempts < 30) {
                        resultDiv.querySelector('h3').textContent = '⏳ Suno đang tạo nhạc... (' + (attempts * 5) + 's)';
                        setTimeout(check, 5000);
                    }
                } catch (err) {}
            };

            setTimeout(check, 5000);
        }

        async function testGenerate() {
            const form = document.getElementById('suno-test-form');
            const prompt = form.querySelector('[name="prompt"]').value;
            const model = form.querySelector('[name="model"]').value;
            const resultDiv = document.getElementById('suno-result');

            if (!prompt) {
                alert('Vui lòng nhập prompt');
                return;
            }

            resultDiv.innerHTML = '<p>Đang tạo bài hát...</p>';

            try {
                const response = await fetch('<?php echo rest_url('suno/v1/generate'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                    },
                    body: JSON.stringify({ prompt, model })
                });
                const data = await response.json();
                resultDiv.innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
            } catch (error) {
                resultDiv.innerHTML = '<p style="color:red;">Lỗi: ' + error.message + '</p>';
            }
        }
        </script>
        <?php
    }

    /**
     * Register REST Routes
     */
    public function register_rest_routes() {
        register_rest_route('suno/v1', '/generate', [
            'methods' => 'POST',
            'callback' => [$this, 'rest_generate'],
            'permission_callback' => '__return_true'
        ]);

        register_rest_route('suno/v1', '/generate-custom', [
            'methods' => 'POST',
            'callback' => [$this, 'rest_generate_custom'],
            'permission_callback' => '__return_true'
        ]);

        register_rest_route('suno/v1', '/song/(?P<task_id>[a-zA-Z0-9-]+)', [
            'methods' => 'GET',
            'callback' => [$this, 'rest_get_song'],
            'permission_callback' => '__return_true'
        ]);

        register_rest_route('suno/v1', '/credits', [
            'methods' => 'GET',
            'callback' => [$this, 'rest_credits'],
            'permission_callback' => '__return_true'
        ]);

        register_rest_route('suno/v1', '/lyrics', [
            'methods' => 'POST',
            'callback' => [$this, 'rest_lyrics'],
            'permission_callback' => '__return_true'
        ]);

        register_rest_route('suno/v1', '/auto-generate', [
            'methods' => 'POST',
            'callback' => [$this, 'rest_auto_generate'],
            'permission_callback' => '__return_true'
        ]);

        register_rest_route('suno/v1', '/chatgpt-prompt', [
            'methods' => 'POST',
            'callback' => [$this, 'rest_chatgpt_prompt'],
            'permission_callback' => '__return_true'
        ]);
    }

    public function rest_generate($request) {
        $prompt = $request->get_param('prompt');
        $instrumental = $request->get_param('instrumental') ?? false;
        $model = $request->get_param('model') ?? 'V3_5';

        if (empty($prompt)) {
            return new WP_Error('missing_prompt', 'Prompt is required', ['status' => 400]);
        }

        $result = $this->generateSong($prompt, $instrumental, $model);
        return rest_ensure_response($result);
    }

    public function rest_generate_custom($request) {
        $title = $request->get_param('title') ?? '';
        $lyrics = $request->get_param('lyrics');
        $style = $request->get_param('style') ?? '';
        $instrumental = $request->get_param('instrumental') ?? false;
        $model = $request->get_param('model') ?? 'V3_5';

        if (empty($lyrics)) {
            return new WP_Error('missing_lyrics', 'Lyrics is required', ['status' => 400]);
        }

        $result = $this->generateCustomSong($title, $lyrics, $style, $instrumental, $model);
        return rest_ensure_response($result);
    }

    public function rest_get_song($request) {
        $taskId = $request->get_param('task_id');
        $result = $this->getSongByTaskId($taskId);
        return rest_ensure_response($result);
    }

    public function rest_credits($request) {
        $result = $this->getCredits();
        return rest_ensure_response($result);
    }

    public function rest_lyrics($request) {
        $prompt = $request->get_param('prompt');
        if (empty($prompt)) {
            return new WP_Error('missing_prompt', 'Prompt is required', ['status' => 400]);
        }
        $result = $this->generateLyrics($prompt);
        return rest_ensure_response($result);
    }

    public function rest_auto_generate($request) {
        $idea = $request->get_param('idea');
        $language = $request->get_param('language') ?? 'vi';
        $model = $request->get_param('model') ?? 'V3_5';

        if (empty($idea)) {
            return new WP_Error('missing_idea', 'Idea is required', ['status' => 400]);
        }

        $result = $this->autoGenerate($idea, $language, $model);
        return rest_ensure_response($result);
    }

    public function rest_chatgpt_prompt($request) {
        $idea = $request->get_param('idea');
        $language = $request->get_param('language') ?? 'vi';

        if (empty($idea)) {
            return new WP_Error('missing_idea', 'Idea is required', ['status' => 400]);
        }

        $result = $this->generateSunoPrompt($idea, $language);
        return rest_ensure_response($result);
    }

    /**
     * Enqueue Scripts
     */
    public function enqueue_scripts() {
        wp_enqueue_style('suno-api-style', plugin_dir_url(__FILE__) . 'suno-style.css', [], '1.0.0');
    }

    /**
     * Shortcode
     */
    public function generator_shortcode($atts) {
        ob_start();
        ?>
        <div class="suno-generator">
            <h3>Tạo nhạc với Suno AI</h3>
            <form id="suno-form">
                <div class="suno-field">
                    <label>Mô tả bài hát:</label>
                    <textarea name="prompt" rows="3" placeholder="VD: A romantic ballad about summer love"></textarea>
                </div>
                <div class="suno-field">
                    <label>Model:</label>
                    <select name="model">
                        <option value="V3_5">V3.5</option>
                        <option value="V4">V4</option>
                        <option value="V4_5">V4.5</option>
                    </select>
                </div>
                <div class="suno-field">
                    <label>
                        <input type="checkbox" name="instrumental"> Chỉ nhạc (không lời)
                    </label>
                </div>
                <button type="submit" class="suno-btn">Tạo bài hát</button>
            </form>
            <div id="suno-output"></div>
        </div>

        <script>
        document.getElementById('suno-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = e.target;
            const output = document.getElementById('suno-output');

            const data = {
                prompt: form.querySelector('[name="prompt"]').value,
                model: form.querySelector('[name="model"]').value,
                instrumental: form.querySelector('[name="instrumental"]').checked
            };

            if (!data.prompt) {
                alert('Vui lòng nhập mô tả bài hát');
                return;
            }

            output.innerHTML = '<p>Đang tạo bài hát...</p>';

            try {
                const res = await fetch('<?php echo rest_url('suno/v1/generate'); ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await res.json();

                if (result.data && result.data.taskId) {
                    output.innerHTML = '<p>Task ID: ' + result.data.taskId + '</p><p>Đang xử lý... (kiểm tra lại sau 1-2 phút)</p>';
                    pollResult(result.data.taskId);
                } else {
                    output.innerHTML = '<pre>' + JSON.stringify(result, null, 2) + '</pre>';
                }
            } catch (err) {
                output.innerHTML = '<p style="color:red;">Lỗi: ' + err.message + '</p>';
            }
        });

        async function pollResult(taskId) {
            const output = document.getElementById('suno-output');
            let attempts = 0;
            const maxAttempts = 24; // 2 phút

            const check = async () => {
                attempts++;
                try {
                    const res = await fetch('<?php echo rest_url('suno/v1/song/'); ?>' + taskId);
                    const result = await res.json();

                    if (result.data && result.data.status === 'completed') {
                        let html = '<h4>Bài hát đã tạo xong!</h4>';
                        if (result.data.songs) {
                            result.data.songs.forEach(song => {
                                html += `
                                    <div class="suno-song">
                                        <p><strong>${song.title || 'Untitled'}</strong></p>
                                        <audio controls src="${song.audio_url}"></audio>
                                    </div>
                                `;
                            });
                        }
                        output.innerHTML = html;
                    } else if (attempts < maxAttempts) {
                        output.innerHTML = '<p>Đang xử lý... (' + attempts * 5 + 's)</p>';
                        setTimeout(check, 5000);
                    } else {
                        output.innerHTML = '<p>Timeout. Task ID: ' + taskId + '</p>';
                    }
                } catch (err) {
                    output.innerHTML = '<p style="color:red;">Lỗi: ' + err.message + '</p>';
                }
            };

            setTimeout(check, 5000);
        }
        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * Auto Generator Shortcode (ChatGPT + Suno)
     */
    public function auto_generator_shortcode($atts) {
        ob_start();
        ?>
        <div class="suno-generator suno-auto">
            <h3>Tạo nhạc AI tự động</h3>
            <p class="suno-desc">Chỉ cần nhập ý tưởng, AI sẽ tự động viết lời và tạo nhạc cho bạn!</p>
            <form id="suno-auto-form">
                <div class="suno-field">
                    <label>Ý tưởng bài hát:</label>
                    <textarea name="idea" rows="3" placeholder="VD: Bài hát về tình yêu đầu đời của tuổi học trò, thể loại ballad nhẹ nhàng"></textarea>
                </div>
                <div class="suno-row">
                    <div class="suno-field suno-half">
                        <label>Ngôn ngữ:</label>
                        <select name="language">
                            <option value="vi">Tiếng Việt</option>
                            <option value="en">English</option>
                            <option value="ko">한국어</option>
                            <option value="ja">日本語</option>
                        </select>
                    </div>
                    <div class="suno-field suno-half">
                        <label>Model:</label>
                        <select name="model">
                            <option value="V3_5">V3.5</option>
                            <option value="V4">V4</option>
                            <option value="V4_5">V4.5</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="suno-btn suno-btn-auto">Tự động tạo bài hát</button>
            </form>
            <div id="suno-auto-output"></div>
        </div>

        <script>
        document.getElementById('suno-auto-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = e.target;
            const output = document.getElementById('suno-auto-output');

            const data = {
                idea: form.querySelector('[name="idea"]').value,
                language: form.querySelector('[name="language"]').value,
                model: form.querySelector('[name="model"]').value
            };

            if (!data.idea) {
                alert('Vui lòng nhập ý tưởng bài hát');
                return;
            }

            output.innerHTML = '<div class="suno-loading"><p>Bước 1: AI đang viết lời bài hát...</p></div>';

            try {
                const res = await fetch('<?php echo rest_url('suno/v1/auto-generate'); ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await res.json();

                if (result.success) {
                    let html = '<div class="suno-success">';
                    html += '<h4>Đã gửi đến Suno!</h4>';
                    html += '<p><strong>Tiêu đề:</strong> ' + (result.chatgpt?.title || '') + '</p>';
                    html += '<p><strong>Phong cách:</strong> ' + (result.chatgpt?.style || '') + '</p>';
                    html += '<details><summary>Xem lời bài hát</summary><pre class="suno-lyrics">' + (result.chatgpt?.lyrics || '') + '</pre></details>';
                    html += '<p class="suno-status">Đang tạo nhạc...</p>';
                    html += '</div>';
                    output.innerHTML = html;

                    if (result.suno?.data?.taskId) {
                        pollAutoResult2(result.suno.data.taskId);
                    }
                } else {
                    output.innerHTML = '<div class="suno-error"><h4>Có lỗi xảy ra</h4><pre>' + JSON.stringify(result, null, 2) + '</pre></div>';
                }
            } catch (err) {
                output.innerHTML = '<div class="suno-error"><p>Lỗi: ' + err.message + '</p></div>';
            }
        });

        async function pollAutoResult2(taskId) {
            const output = document.getElementById('suno-auto-output');
            let attempts = 0;

            const check = async () => {
                attempts++;
                try {
                    const res = await fetch('<?php echo rest_url('suno/v1/song/'); ?>' + taskId);
                    const result = await res.json();

                    const statusEl = output.querySelector('.suno-status');

                    if (result.data && result.data.status === 'completed') {
                        let html = '<div class="suno-songs"><h4>Bài hát đã sẵn sàng!</h4>';
                        if (result.data.songs) {
                            result.data.songs.forEach(song => {
                                html += '<div class="suno-song">';
                                html += '<p><strong>' + (song.title || 'Untitled') + '</strong></p>';
                                if (song.image_url) {
                                    html += '<img src="' + song.image_url + '" alt="Cover" class="suno-cover">';
                                }
                                html += '<audio controls src="' + song.audio_url + '"></audio>';
                                html += '<a href="' + song.audio_url + '" download class="suno-download">Tải xuống</a>';
                                html += '</div>';
                            });
                        }
                        html += '</div>';
                        if (statusEl) statusEl.outerHTML = html;
                    } else if (attempts < 30) {
                        if (statusEl) statusEl.textContent = 'Đang tạo nhạc... (' + (attempts * 5) + 's)';
                        setTimeout(check, 5000);
                    } else {
                        if (statusEl) statusEl.textContent = 'Timeout - Vui lòng thử lại sau. Task ID: ' + taskId;
                    }
                } catch (err) {}
            };

            setTimeout(check, 5000);
        }
        </script>
        <?php
        return ob_get_clean();
    }
}

// Initialize
new SunoAPI_Plugin();
