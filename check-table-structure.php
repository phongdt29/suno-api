<?php
require_once 'wp-load.php';

global $wpdb;
$table = $wpdb->prefix . 'suno_history';

echo "=== TABLE STRUCTURE: $table ===\n\n";

$columns = $wpdb->get_results("DESCRIBE $table");

foreach($columns as $col) {
    echo sprintf("%-20s %-20s %-10s %-10s\n",
        $col->Field,
        $col->Type,
        $col->Null,
        $col->Key
    );
}

echo "\n";
