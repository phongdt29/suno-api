<?php
/**
 * Add sample Tet and Bolero music to database for testing
 */
require_once 'wp-load.php';

global $wpdb;
$table = $wpdb->prefix . 'suno_history';

// Sample Tết music data
$tet_samples = array(
    array(
        'task_id' => 'tet-sample-' . uniqid(),
        'prompt' => 'Tạo bài nhạc Tết vui vẻ',
        'lyrics' => '',
        'title' => 'Xuân Về Rộn Ràng',
        'style' => 'Nhạc Tết, Pop Vietnamese',
        'model' => 'suno-v3.5',
        'status' => 'completed',
        'songs' => json_encode(array(
            array(
                'id' => 'sample-tet-1',
                'title' => 'Xuân Về Rộn Ràng',
                'audio_url' => 'https://cdn1.suno.ai/sample.mp3',
                'video_url' => '',
                'image_url' => get_template_directory_uri() . '/assets/images/weekly/song1.jpg',
                'metadata' => array('duration' => 180)
            )
        ))
    ),
    array(
        'task_id' => 'tet-sample-' . uniqid(),
        'prompt' => 'Nhạc Tết truyền thống',
        'lyrics' => '',
        'title' => 'Mùa Xuân Đến Rồi',
        'style' => 'Nhạc Tết Truyền Thống',
        'model' => 'suno-v3.5',
        'status' => 'completed',
        'songs' => json_encode(array(
            array(
                'id' => 'sample-tet-2',
                'title' => 'Mùa Xuân Đến Rồi',
                'audio_url' => 'https://cdn1.suno.ai/sample2.mp3',
                'video_url' => '',
                'image_url' => get_template_directory_uri() . '/assets/images/weekly/song2.jpg',
                'metadata' => array('duration' => 200)
            )
        ))
    )
);

// Sample Bolero music data
$bolero_samples = array(
    array(
        'task_id' => 'bolero-sample-' . uniqid(),
        'prompt' => 'Tạo bài nhạc Bolero buồn',
        'lyrics' => '',
        'title' => 'Tình Ca Du Dương',
        'style' => 'Bolero, Vietnamese Classic',
        'model' => 'suno-v3.5',
        'status' => 'completed',
        'songs' => json_encode(array(
            array(
                'id' => 'sample-bolero-1',
                'title' => 'Tình Ca Du Dương',
                'audio_url' => 'https://cdn1.suno.ai/sample-bolero.mp3',
                'video_url' => '',
                'image_url' => get_template_directory_uri() . '/assets/images/weekly/song3.jpg',
                'metadata' => array('duration' => 240)
            )
        ))
    ),
    array(
        'task_id' => 'bolero-sample-' . uniqid(),
        'prompt' => 'Nhạc Bolero tình yêu',
        'lyrics' => '',
        'title' => 'Đêm Buồn Tỉnh Lẻ',
        'style' => 'Bolero Tình Yêu',
        'model' => 'suno-v3.5',
        'status' => 'completed',
        'songs' => json_encode(array(
            array(
                'id' => 'sample-bolero-2',
                'title' => 'Đêm Buồn Tỉnh Lẻ',
                'audio_url' => 'https://cdn1.suno.ai/sample-bolero2.mp3',
                'video_url' => '',
                'image_url' => get_template_directory_uri() . '/assets/images/weekly/song4.jpg',
                'metadata' => array('duration' => 220)
            )
        ))
    )
);

echo "=== THÊM NHẠC MẪU VÀO DATABASE ===\n\n";

// Insert Tết samples
echo "Đang thêm nhạc Tết...\n";
foreach ($tet_samples as $sample) {
    $result = $wpdb->insert(
        $table,
        array(
            'user_id' => 1,
            'task_id' => $sample['task_id'],
            'prompt' => $sample['prompt'],
            'lyrics' => $sample['lyrics'],
            'title' => $sample['title'],
            'style' => $sample['style'],
            'model' => $sample['model'],
            'status' => $sample['status'],
            'songs' => $sample['songs'],
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ),
        array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
    );

    if ($result) {
        echo "  ✅ Đã thêm: {$sample['title']}\n";
    } else {
        echo "  ❌ Lỗi thêm: {$sample['title']}\n";
    }
}

// Insert Bolero samples
echo "\nĐang thêm nhạc Bolero...\n";
foreach ($bolero_samples as $sample) {
    $result = $wpdb->insert(
        $table,
        array(
            'user_id' => 1,
            'task_id' => $sample['task_id'],
            'prompt' => $sample['prompt'],
            'lyrics' => $sample['lyrics'],
            'title' => $sample['title'],
            'style' => $sample['style'],
            'model' => $sample['model'],
            'status' => $sample['status'],
            'songs' => $sample['songs'],
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ),
        array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
    );

    if ($result) {
        echo "  ✅ Đã thêm: {$sample['title']}\n";
    } else {
        echo "  ❌ Lỗi thêm: {$sample['title']}\n";
    }
}

echo "\n=== HOÀN THÀNH ===\n\n";
echo "Bây giờ bạn có thể:\n";
echo "1. Truy cập trang chủ để xem section 'Nhạc Tết' và 'Nhạc Bolero'\n";
echo "2. Hoặc chạy: php check-tet-music.php để kiểm tra\n\n";
