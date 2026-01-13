-- =====================================================
-- Suno Music Generator - Create Schedule Table
-- Run this SQL in phpMyAdmin or MySQL CLI
-- =====================================================

-- Create suno_schedule table
CREATE TABLE IF NOT EXISTS `wp_suno_schedule` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `genre` varchar(100) NOT NULL COMMENT 'Thể loại nhạc',
    `prompt` text COMMENT 'Mô tả chi tiết',
    `full_prompt` text COMMENT 'Prompt đầy đủ (genre + prompt)',
    `model` varchar(20) DEFAULT 'V3_5' COMMENT 'AI Model (V3_5, V4, V4_5, V5)',
    `instrumental` tinyint(1) DEFAULT 0 COMMENT '1 = không lời, 0 = có lời',
    `schedule_time` datetime NOT NULL COMMENT 'Thời gian tạo nhạc',
    `repeat_type` varchar(20) DEFAULT 'once' COMMENT 'once, daily, weekly, monthly',
    `status` varchar(50) DEFAULT 'pending' COMMENT 'pending, processing, completed, failed',
    `task_id` varchar(100) DEFAULT NULL COMMENT 'Task ID từ Suno API sau khi tạo',
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `status` (`status`),
    KEY `schedule_time` (`schedule_time`),
    KEY `genre` (`genre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Sample data (optional - uncomment to insert)
-- =====================================================

-- INSERT INTO `wp_suno_schedule` (`genre`, `prompt`, `full_prompt`, `model`, `instrumental`, `schedule_time`, `repeat_type`, `status`) VALUES
-- ('Nhạc Tết', 'Bài hát chúc mừng năm mới vui vẻ', 'Nhạc Tết. Bài hát chúc mừng năm mới vui vẻ', 'V4', 0, '2025-01-15 08:00:00', 'once', 'pending'),
-- ('Nhạc Bolero', 'Nhạc buồn về tình yêu xa cách', 'Nhạc Bolero. Nhạc buồn về tình yêu xa cách', 'V4', 0, '2025-01-16 10:00:00', 'daily', 'pending'),
-- ('Lo-Fi', 'Nhạc học bài thư giãn', 'Lo-Fi. Nhạc học bài thư giãn', 'V4', 1, '2025-01-17 14:00:00', 'weekly', 'pending');

-- =====================================================
-- Useful queries
-- =====================================================

-- View all scheduled items
-- SELECT * FROM wp_suno_schedule ORDER BY schedule_time ASC;

-- View pending schedules
-- SELECT * FROM wp_suno_schedule WHERE status = 'pending' ORDER BY schedule_time ASC;

-- View completed schedules
-- SELECT * FROM wp_suno_schedule WHERE status = 'completed' ORDER BY updated_at DESC;

-- Delete old completed schedules (older than 30 days)
-- DELETE FROM wp_suno_schedule WHERE status = 'completed' AND updated_at < DATE_SUB(NOW(), INTERVAL 30 DAY);

-- Count by status
-- SELECT status, COUNT(*) as count FROM wp_suno_schedule GROUP BY status;

-- Count by genre
-- SELECT genre, COUNT(*) as count FROM wp_suno_schedule GROUP BY genre ORDER BY count DESC;
