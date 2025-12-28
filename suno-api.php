<?php
/**
 * Suno API - Get Songs
 * API để lấy bài hát từ Suno
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Xử lý preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

class SunoAPI {
    private $baseUrl = 'https://apibox.erweima.ai';
    private $apiKey;

    public function __construct($apiKey = '') {
        $this->apiKey = $apiKey;
    }

    /**
     * Thiết lập API Key
     */
    public function setApiKey($apiKey) {
        $this->apiKey = $apiKey;
    }

    /**
     * Gửi request đến Suno API
     */
    private function request($endpoint, $method = 'GET', $data = null) {
        $url = $this->baseUrl . $endpoint;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $this->apiKey,
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['error' => $error, 'code' => 0];
        }

        return [
            'data' => json_decode($response, true),
            'code' => $httpCode
        ];
    }

    /**
     * Tạo bài hát mới (generate)
     */
    public function generateSong($prompt, $customMode = false, $instrumental = false, $model = 'V3_5') {
        $data = [
            'prompt' => $prompt,
            'customMode' => $customMode,
            'instrumental' => $instrumental,
            'model' => $model
        ];
        return $this->request('/api/v1/generate', 'POST', $data);
    }

    /**
     * Tạo bài hát với lyrics tùy chỉnh (Custom Mode)
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
     * Lấy thông tin bài hát theo taskId
     */
    public function getSongByTaskId($taskId) {
        return $this->request('/api/v1/generate/record-info?taskId=' . $taskId);
    }

    /**
     * Lấy thông tin credits còn lại
     */
    public function getCredits() {
        return $this->request('/api/v1/generate/quota');
    }

    /**
     * Extend/Continue bài hát
     */
    public function extendSong($audioId, $prompt = '', $style = '', $continueAt = 0, $model = 'V3_5') {
        $data = [
            'audioId' => $audioId,
            'prompt' => $prompt,
            'style' => $style,
            'continueAt' => $continueAt,
            'model' => $model
        ];
        return $this->request('/api/v1/generate/extend', 'POST', $data);
    }

    /**
     * Tạo lyrics từ prompt
     */
    public function generateLyrics($prompt) {
        $data = [
            'prompt' => $prompt
        ];
        return $this->request('/api/v1/lyrics', 'POST', $data);
    }

    /**
     * Lấy lyrics đã tạo theo taskId
     */
    public function getLyricsByTaskId($taskId) {
        return $this->request('/api/v1/lyrics/record-info?taskId=' . $taskId);
    }

    /**
     * Upload audio để tạo nhạc
     */
    public function uploadAudio($audioUrl) {
        $data = [
            'audioUrl' => $audioUrl
        ];
        return $this->request('/api/v1/uploads/audio', 'POST', $data);
    }
}

// ==================== XỬ LÝ REQUEST ====================

// API Key mặc định
$defaultApiKey = 'd0f8edfa4b6f1adace734102152f3bfb';

// Lấy API key từ config hoặc request
$config = [];
if (file_exists(__DIR__ . '/config.php')) {
    $config = include __DIR__ . '/config.php';
}

$apiKey = $_GET['api_key'] ?? $_POST['api_key'] ?? $config['api_key'] ?? $defaultApiKey;
$action = $_GET['action'] ?? $_POST['action'] ?? 'credits';

$suno = new SunoAPI($apiKey);

$response = ['success' => false, 'message' => '', 'data' => null];

try {
    switch ($action) {
        case 'generate':
            // Tạo bài hát mới
            $prompt = $_POST['prompt'] ?? $_GET['prompt'] ?? '';
            if (empty($prompt)) {
                $response['message'] = 'Prompt is required';
                break;
            }
            $instrumental = filter_var($_POST['instrumental'] ?? $_GET['instrumental'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $model = $_POST['model'] ?? $_GET['model'] ?? 'V3_5';
            $result = $suno->generateSong($prompt, false, $instrumental, $model);
            if ($result['code'] === 200) {
                $response['success'] = true;
                $response['data'] = $result['data'];
            } else {
                $response['message'] = 'Failed to generate song';
                $response['error'] = $result;
            }
            break;

        case 'generate_custom':
            // Tạo bài hát với lyrics tùy chỉnh
            $title = $_POST['title'] ?? $_GET['title'] ?? '';
            $lyrics = $_POST['lyrics'] ?? $_GET['lyrics'] ?? '';
            $style = $_POST['style'] ?? $_GET['style'] ?? '';
            if (empty($lyrics)) {
                $response['message'] = 'Lyrics is required';
                break;
            }
            $instrumental = filter_var($_POST['instrumental'] ?? $_GET['instrumental'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $model = $_POST['model'] ?? $_GET['model'] ?? 'V3_5';
            $result = $suno->generateCustomSong($title, $lyrics, $style, $instrumental, $model);
            if ($result['code'] === 200) {
                $response['success'] = true;
                $response['data'] = $result['data'];
            } else {
                $response['message'] = 'Failed to generate custom song';
                $response['error'] = $result;
            }
            break;

        case 'get_song':
            // Lấy thông tin bài hát theo taskId
            $taskId = $_GET['task_id'] ?? $_POST['task_id'] ?? '';
            if (empty($taskId)) {
                $response['message'] = 'Task ID is required';
                break;
            }
            $result = $suno->getSongByTaskId($taskId);
            if ($result['code'] === 200) {
                $response['success'] = true;
                $response['data'] = $result['data'];
            } else {
                $response['message'] = 'Failed to get song';
                $response['error'] = $result;
            }
            break;

        case 'extend':
            // Extend/Continue bài hát
            $audioId = $_POST['audio_id'] ?? $_GET['audio_id'] ?? '';
            if (empty($audioId)) {
                $response['message'] = 'Audio ID is required';
                break;
            }
            $prompt = $_POST['prompt'] ?? $_GET['prompt'] ?? '';
            $style = $_POST['style'] ?? $_GET['style'] ?? '';
            $continueAt = intval($_POST['continue_at'] ?? $_GET['continue_at'] ?? 0);
            $model = $_POST['model'] ?? $_GET['model'] ?? 'V3_5';
            $result = $suno->extendSong($audioId, $prompt, $style, $continueAt, $model);
            if ($result['code'] === 200) {
                $response['success'] = true;
                $response['data'] = $result['data'];
            } else {
                $response['message'] = 'Failed to extend song';
                $response['error'] = $result;
            }
            break;

        case 'lyrics':
            // Tạo lyrics
            $prompt = $_POST['prompt'] ?? $_GET['prompt'] ?? '';
            if (empty($prompt)) {
                $response['message'] = 'Prompt is required';
                break;
            }
            $result = $suno->generateLyrics($prompt);
            if ($result['code'] === 200) {
                $response['success'] = true;
                $response['data'] = $result['data'];
            } else {
                $response['message'] = 'Failed to generate lyrics';
                $response['error'] = $result;
            }
            break;

        case 'get_lyrics':
            // Lấy lyrics đã tạo
            $taskId = $_GET['task_id'] ?? $_POST['task_id'] ?? '';
            if (empty($taskId)) {
                $response['message'] = 'Task ID is required';
                break;
            }
            $result = $suno->getLyricsByTaskId($taskId);
            if ($result['code'] === 200) {
                $response['success'] = true;
                $response['data'] = $result['data'];
            } else {
                $response['message'] = 'Failed to get lyrics';
                $response['error'] = $result;
            }
            break;

        case 'credits':
            // Lấy thông tin credits
            $result = $suno->getCredits();
            if ($result['code'] === 200) {
                $response['success'] = true;
                $response['data'] = $result['data'];
            } else {
                $response['message'] = 'Failed to get credits';
                $response['error'] = $result;
            }
            break;

        case 'upload':
            // Upload audio
            $audioUrl = $_POST['audio_url'] ?? $_GET['audio_url'] ?? '';
            if (empty($audioUrl)) {
                $response['message'] = 'Audio URL is required';
                break;
            }
            $result = $suno->uploadAudio($audioUrl);
            if ($result['code'] === 200) {
                $response['success'] = true;
                $response['data'] = $result['data'];
            } else {
                $response['message'] = 'Failed to upload audio';
                $response['error'] = $result;
            }
            break;

        default:
            $response['message'] = 'Invalid action. Available actions: generate, generate_custom, get_song, extend, lyrics, get_lyrics, credits, upload';
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
