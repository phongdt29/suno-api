<?php
/**
 * Clear all WordPress cache and transients
 */
require_once 'wp-load.php';

global $wpdb;

echo "=== CLEARING WORDPRESS CACHE ===\n\n";

// Delete all transients
$result = $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%'");
echo "Transients cleared: $result\n";

// Clear object cache if available
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
    echo "Object cache flushed\n";
}

echo "\n✅ Cache cleared successfully!\n";
echo "Now refresh your browser (Ctrl + Shift + R)\n";
