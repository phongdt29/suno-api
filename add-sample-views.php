<?php
/**
 * Add sample view counts to songs for testing ranking feature
 */
require_once 'wp-load.php';

global $wpdb;
$table = $wpdb->prefix . 'suno_history';

echo "=== ADDING SAMPLE VIEW COUNTS ===\n\n";

// Get all completed songs
$songs = $wpdb->get_results("SELECT id, title FROM $table WHERE status = 'completed' ORDER BY created_at DESC");

if (empty($songs)) {
    echo "❌ No songs found in database\n";
    exit;
}

echo "Found " . count($songs) . " songs. Adding random view counts...\n\n";

// Add random views to each song (1-1000 views)
foreach ($songs as $song) {
    $random_views = rand(10, 1000);

    $result = $wpdb->update(
        $table,
        array('views' => $random_views),
        array('id' => $song->id),
        array('%d'),
        array('%d')
    );

    if ($result !== false) {
        echo "✅ " . $song->title . " → " . number_format($random_views) . " views\n";
    } else {
        echo "❌ Failed to update: " . $song->title . "\n";
    }
}

echo "\n=== TOP 10 SONGS BY VIEWS ===\n\n";

$top_songs = $wpdb->get_results("SELECT id, title, views FROM $table WHERE status = 'completed' ORDER BY views DESC LIMIT 10");

$rank = 1;
foreach ($top_songs as $song) {
    $medal = '';
    if ($rank == 1) $medal = '🥇';
    elseif ($rank == 2) $medal = '🥈';
    elseif ($rank == 3) $medal = '🥉';

    echo sprintf("%s #%d. %s - %s views\n",
        $medal,
        $rank,
        $song->title,
        number_format($song->views)
    );

    $rank++;
}

echo "\n=== COMPLETE ===\n";
echo "Visit the homepage to see the ranking section!\n\n";
