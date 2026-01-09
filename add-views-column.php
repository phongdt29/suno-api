<?php
/**
 * Add views column to suno_history table
 */
require_once 'wp-load.php';

global $wpdb;
$table = $wpdb->prefix . 'suno_history';

echo "=== ADDING VIEWS COLUMN ===\n\n";

// Add views column if it doesn't exist
$result = $wpdb->query("ALTER TABLE $table ADD COLUMN views INT(11) DEFAULT 0 AFTER songs");

if ($result !== false) {
    echo "✅ Views column added successfully!\n";
} else {
    echo "❌ Error adding views column: " . $wpdb->last_error . "\n";
    echo "Note: If column already exists, this is expected.\n";
}

// Verify column was added
echo "\n=== VERIFYING TABLE STRUCTURE ===\n\n";
$columns = $wpdb->get_results("DESCRIBE $table");

foreach($columns as $col) {
    if ($col->Field === 'views') {
        echo "✅ FOUND: " . $col->Field . " - " . $col->Type . "\n";
    } else {
        echo "   " . $col->Field . " - " . $col->Type . "\n";
    }
}

echo "\n=== COMPLETE ===\n";
