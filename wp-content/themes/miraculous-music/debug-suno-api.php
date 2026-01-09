<?php
/**
 * Debug Suno API
 *
 * Truy cập: /wp-content/themes/miraculous-music/debug-suno-api.php
 *
 * @package Miraculous_Music
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('Access denied. Please login as administrator.');
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Suno API Debug</title>
    <style>
        body {
            font-family: monospace;
            margin: 20px;
            background: #f5f5f5;
        }
        .section {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            border-bottom: 2px solid #ff4865;
            padding-bottom: 10px;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
        .info {
            color: blue;
        }
        pre {
            background: #f9f9f9;
            padding: 15px;
            border-left: 4px solid #ff4865;
            overflow-x: auto;
        }
        button {
            background: #ff4865;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }
        button:hover {
            background: #e63850;
        }
        input[type="text"] {
            padding: 8px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <h1>🎵 Suno API Debug Tool</h1>

    <div class="section">
        <h2>1. API Configuration</h2>
        <?php
        $api_url = get_option('suno_api_url', 'Not set');
        $api_key = get_option('suno_api_key', 'Not set');
        $has_key = !empty($api_key) && $api_key !== 'Not set';
        ?>
        <p><strong>API URL:</strong> <span class="info"><?php echo esc_html($api_url); ?></span></p>
        <p><strong>API Key:</strong>
            <?php if ($has_key) : ?>
                <span class="success">✓ Configured (<?php echo substr($api_key, 0, 10); ?>...)</span>
            <?php else : ?>
                <span class="error">✗ Not configured</span>
            <?php endif; ?>
        </p>
        <?php if (!$has_key) : ?>
            <p class="error">⚠️ Vui lòng cấu hình API key tại: <a href="<?php echo admin_url('admin.php?page=miraculous-suno-api'); ?>">WordPress Admin → Suno API</a></p>
        <?php endif; ?>
    </div>

    <?php if ($has_key) : ?>

    <div class="section">
        <h2>2. Test Get Credits</h2>
        <button onclick="testCredits()">Test Credits API</button>
        <div id="credits-result"></div>
    </div>

    <div class="section">
        <h2>3. Test Get Song by Task ID</h2>
        <form onsubmit="testGetSong(event)">
            <input type="text" id="task-id" placeholder="Enter Task ID" required>
            <button type="submit">Test Get Song</button>
        </form>
        <div id="get-song-result"></div>
    </div>

    <div class="section">
        <h2>4. Test Generate Music</h2>
        <form onsubmit="testGenerate(event)">
            <input type="text" id="prompt" placeholder="e.g., A happy pop song" required>
            <select id="model">
                <option value="V4">V4</option>
                <option value="V4.5">V4.5</option>
                <option value="V5">V5</option>
            </select>
            <button type="submit">Test Generate</button>
        </form>
        <div id="generate-result"></div>
    </div>

    <div class="section">
        <h2>5. Recent Error Logs</h2>
        <button onclick="location.reload()">Refresh Logs</button>
        <pre><?php
        // Read last 50 lines of error log
        $log_file = WP_CONTENT_DIR . '/debug.log';
        if (file_exists($log_file)) {
            $lines = file($log_file);
            $suno_lines = array_filter($lines, function($line) {
                return strpos($line, 'Suno API') !== false;
            });
            echo esc_html(implode('', array_slice($suno_lines, -50)));
        } else {
            echo "Error log not found. Enable WP_DEBUG_LOG in wp-config.php";
        }
        ?></pre>
    </div>

    <?php endif; ?>

    <script>
    function displayResult(elementId, data) {
        var el = document.getElementById(elementId);
        el.innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
    }

    function testCredits() {
        displayResult('credits-result', {status: 'Loading...'});

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                action: 'get_credits',
                nonce: '<?php echo wp_create_nonce('miraculous_ajax'); ?>'
            })
        })
        .then(r => r.json())
        .then(data => displayResult('credits-result', data))
        .catch(e => displayResult('credits-result', {error: e.message}));
    }

    function testGetSong(e) {
        e.preventDefault();
        var taskId = document.getElementById('task-id').value;
        displayResult('get-song-result', {status: 'Loading...'});

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                action: 'get_song',
                nonce: '<?php echo wp_create_nonce('miraculous_ajax'); ?>',
                task_id: taskId
            })
        })
        .then(r => r.json())
        .then(data => displayResult('get-song-result', data))
        .catch(e => displayResult('get-song-result', {error: e.message}));
    }

    function testGenerate(e) {
        e.preventDefault();
        var prompt = document.getElementById('prompt').value;
        var model = document.getElementById('model').value;
        displayResult('generate-result', {status: 'Loading...'});

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                action: 'generate_music',
                nonce: '<?php echo wp_create_nonce('miraculous_ajax'); ?>',
                prompt: prompt,
                model: model
            })
        })
        .then(r => r.json())
        .then(data => displayResult('generate-result', data))
        .catch(e => displayResult('generate-result', {error: e.message}));
    }
    </script>
</body>
</html>
