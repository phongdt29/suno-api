<?php
/**
 * Add more sample songs to reach 15 songs for Weekly Top 15
 */
require_once 'wp-load.php';

global $wpdb;
$table = $wpdb->prefix . 'suno_history';

echo "=== ADDING MORE SAMPLE SONGS ===\n\n";

// Check current count
$current_count = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE status = 'completed'");
echo "Current songs: $current_count\n\n";

// Sample song styles and titles
$sample_songs = array(
    array('title' => 'Mùa Thu Lá Bay', 'style' => 'Nhạc Trữ Tình', 'views' => 856),
    array('title' => 'Đêm Nhớ Người Yêu', 'style' => 'Bolero', 'views' => 734),
    array('title' => 'Xuân Sang', 'style' => 'Nhạc Tết', 'views' => 689),
    array('title' => 'Chiều Thu Hoang', 'style' => 'Nhạc Trữ Tình', 'views' => 612),
    array('title' => 'Tình Khúc Buồn', 'style' => 'Bolero', 'views' => 543),
    array('title' => 'Hoa Nở Về Đêm', 'style' => 'Pop Ballad', 'views' => 487),
    array('title' => 'Dấu Chân Kỷ Niệm', 'style' => 'Nhạc Trữ Tình', 'views' => 421),
    array('title' => 'Mưa Rơi Lặng Thầm', 'style' => 'Pop Ballad', 'views' => 376),
    array('title' => 'Về Đâu Mái Tóc Người Thương', 'style' => 'Bolero', 'views' => 298),
    array('title' => 'Đường Xa Ướt Mưa', 'style' => 'Nhạc Trữ Tình', 'views' => 234),
    array('title' => 'Nắng Chiều', 'style' => 'Pop Ballad', 'views' => 187),
    array('title' => 'Giấc Mơ Thơ Ngây', 'style' => 'Nhạc Tết', 'views' => 145),
);

echo "Adding songs...\n\n";

foreach ($sample_songs as $index => $song) {
    $task_id = 'sample-' . uniqid();
    $song_id = 'song-' . uniqid();

    // Use theme images
    $image_num = ($index % 13) + 1;
    $image_url = get_template_directory_uri() . "/assets/images/weekly/song{$image_num}.jpg";

    // Create songs JSON
    $songs_data = array(
        array(
            'id' => $song_id,
            'title' => $song['title'],
            'audio_url' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-' . (($index % 16) + 1) . '.mp3',
            'video_url' => '',
            'image_url' => $image_url,
            'status' => 'completed',
            'duration' => rand(180, 300),
            'metadata' => array(
                'style' => $song['style']
            )
        )
    );

    $result = $wpdb->insert(
        $table,
        array(
            'user_id' => 1,
            'task_id' => $task_id,
            'prompt' => 'Tạo bài ' . $song['style'],
            'lyrics' => '',
            'title' => $song['title'],
            'style' => $song['style'],
            'model' => 'suno-v3.5',
            'status' => 'completed',
            'songs' => json_encode($songs_data),
            'views' => $song['views'],
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ),
        array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s')
    );

    if ($result) {
        echo "✅ Added: {$song['title']} ({$song['style']}) - {$song['views']} views\n";
    } else {
        echo "❌ Failed: {$song['title']}\n";
    }
}

echo "\n=== UPDATED TOP 15 ===\n\n";

$top_15 = $wpdb->get_results("SELECT title, style, views FROM $table WHERE status = 'completed' ORDER BY views DESC LIMIT 15");

$rank = 1;
foreach ($top_15 as $song) {
    $medal = '';
    if ($rank == 1) $medal = '🥇';
    elseif ($rank == 2) $medal = '🥈';
    elseif ($rank == 3) $medal = '🥉';

    echo sprintf("%s #%02d. %-30s %-20s %s views\n",
        $medal,
        $rank,
        $song->title,
        "({$song->style})",
        number_format($song->views)
    );

    $rank++;
}

echo "\n=== COMPLETE ===\n";
echo "Total songs: " . $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE status = 'completed'") . "\n";
echo "Visit homepage to see Weekly Top 15 section!\n\n";
