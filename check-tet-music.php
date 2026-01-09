<?php
require_once 'wp-load.php';

global $wpdb;
$table = $wpdb->prefix . 'suno_history';

// Check for Tết music
echo "=== KIỂM TRA NHẠC TẾT ===\n\n";

$tet_results = $wpdb->get_results("SELECT id, title, style, status FROM $table WHERE (style LIKE '%Tết%' OR title LIKE '%Tết%') LIMIT 5", ARRAY_A);

if (empty($tet_results)) {
    echo "❌ Không có bài hát nào với style 'Tết' trong database\n\n";

    echo "📋 Danh sách tất cả styles hiện có:\n";
    $styles = $wpdb->get_results("SELECT DISTINCT style FROM $table WHERE style IS NOT NULL AND style != '' LIMIT 20", ARRAY_A);

    if (empty($styles)) {
        echo "Không có style nào trong database\n\n";
    } else {
        foreach ($styles as $s) {
            echo "  - " . $s['style'] . "\n";
        }
    }

    echo "\n📊 Tổng số bài hát: ";
    echo $wpdb->get_var("SELECT COUNT(*) FROM $table");
    echo "\n\n";

    echo "💡 Để có nhạc Tết hiển thị, bạn cần:\n";
    echo "   1. Tạo bài hát với style chứa 'Tết'\n";
    echo "   2. Hoặc title chứa 'Tết'\n";
    echo "   3. Status phải là 'completed'\n\n";

} else {
    echo "✅ Tìm thấy " . count($tet_results) . " bài hát Tết:\n\n";
    foreach ($tet_results as $row) {
        echo "ID: " . $row['id'] . "\n";
        echo "Title: " . $row['title'] . "\n";
        echo "Style: " . $row['style'] . "\n";
        echo "Status: " . $row['status'] . "\n";
        echo "---\n";
    }
}

// Check for Bolero music
echo "\n=== KIỂM TRA NHẠC BOLERO ===\n\n";

$bolero_results = $wpdb->get_results("SELECT id, title, style, status FROM $table WHERE (style LIKE '%Bolero%' OR title LIKE '%Bolero%') LIMIT 5", ARRAY_A);

if (empty($bolero_results)) {
    echo "❌ Không có bài hát nào với style 'Bolero' trong database\n";
} else {
    echo "✅ Tìm thấy " . count($bolero_results) . " bài hát Bolero:\n\n";
    foreach ($bolero_results as $row) {
        echo "ID: " . $row['id'] . "\n";
        echo "Title: " . $row['title'] . "\n";
        echo "Style: " . $row['style'] . "\n";
        echo "Status: " . $row['status'] . "\n";
        echo "---\n";
    }
}
