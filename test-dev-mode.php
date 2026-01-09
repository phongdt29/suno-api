<?php
/**
 * Test Development Mode
 */
require_once 'wp-load.php';

echo "=== TESTING SUNO DEV MODE ===\n\n";

// Check constants
echo "1. Configuration:\n";
echo "   SUNO_DEV_MODE: " . (defined('SUNO_DEV_MODE') && SUNO_DEV_MODE ? '✅ ENABLED' : '❌ DISABLED') . "\n";
echo "   SUNO_API_KEY: " . (defined('SUNO_API_KEY') ? SUNO_API_KEY : 'NOT SET') . "\n\n";

// Test get credits
echo "2. Testing Get Credits:\n";
$credits = miraculous_get_credits();
echo "   Result: " . json_encode($credits, JSON_PRETTY_PRINT) . "\n\n";

// Test generate music
echo "3. Testing Generate Music:\n";
$result = miraculous_generate_music('Test song in dev mode', 'V4');
echo "   Success: " . ($result['success'] ? '✅ YES' : '❌ NO') . "\n";
if (isset($result['data']['task_id'])) {
    echo "   Task ID: " . $result['data']['task_id'] . "\n";
    echo "   Songs: " . count($result['data']['songs']) . " songs\n";
    if (!empty($result['data']['songs'][0])) {
        echo "   First song: " . $result['data']['songs'][0]['title'] . "\n";
        echo "   Audio URL: " . $result['data']['songs'][0]['audio_url'] . "\n";
    }
} else {
    echo "   Error: " . ($result['error'] ?? 'Unknown') . "\n";
    echo "   Message: " . ($result['message'] ?? 'No message') . "\n";
}

echo "\n=== TEST COMPLETE ===\n";

if (defined('SUNO_DEV_MODE') && SUNO_DEV_MODE) {
    echo "✅ Dev mode is working!\n";
    echo "You can now generate music without using credits.\n";
} else {
    echo "❌ Dev mode is not enabled!\n";
    echo "Please check wp-config.php\n";
}
