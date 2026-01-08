=== Suno Music Generator ===
Contributors: yourname
Tags: music, ai, suno, generator, audio, songs
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Tạo nhạc AI với Suno - Hỗ trợ tạo nhạc từ prompt, lyrics tùy chỉnh, và tích hợp ChatGPT.

== Description ==

**Suno Music Generator** là plugin WordPress cho phép bạn tạo nhạc AI sử dụng Suno API. Plugin cung cấp giao diện đẹp mắt và dễ sử dụng để tạo nhạc từ mô tả văn bản hoặc lyrics tùy chỉnh.

= Tính năng chính =

* **Tạo nhạc từ mô tả** - Chỉ cần mô tả bài hát bạn muốn, AI sẽ sáng tác cho bạn
* **Tạo nhạc với lyrics tùy chỉnh** - Nhập lyrics của bạn và AI sẽ tạo nhạc phù hợp
* **Tích hợp ChatGPT** - Tự động tạo lyrics từ ý tưởng với ChatGPT
* **Tạo lyrics AI** - Tạo lyrics từ mô tả văn bản
* **Hỗ trợ nhiều model** - Suno V3.5, V4, V4.5
* **Lịch sử tạo nhạc** - Lưu trữ và quản lý các bài hát đã tạo
* **REST API** - API đầy đủ cho developers
* **Shortcodes** - Dễ dàng nhúng vào bất kỳ trang nào

= Shortcodes =

* `[suno_generator]` - Form tạo nhạc cơ bản
* `[suno_custom_generator]` - Form tạo nhạc với lyrics tùy chỉnh
* `[suno_auto_generator]` - Form tạo nhạc tự động với ChatGPT
* `[suno_lyrics_generator]` - Form tạo lyrics AI

= REST API Endpoints =

* `POST /wp-json/suno/v1/generate` - Tạo nhạc từ prompt
* `POST /wp-json/suno/v1/generate-custom` - Tạo nhạc với lyrics
* `GET /wp-json/suno/v1/song/{task_id}` - Lấy trạng thái bài hát
* `GET /wp-json/suno/v1/credits` - Kiểm tra credits
* `POST /wp-json/suno/v1/lyrics` - Tạo lyrics
* `POST /wp-json/suno/v1/extend` - Mở rộng bài hát
* `POST /wp-json/suno/v1/auto-generate` - Tạo nhạc với ChatGPT

== Installation ==

1. Upload thư mục `suno-music-generator` vào `/wp-content/plugins/`
2. Kích hoạt plugin qua menu 'Plugins' trong WordPress
3. Vào **Suno Music > Cài đặt** để nhập API key
4. Sử dụng shortcodes hoặc REST API để tạo nhạc

= Lấy API Key =

1. Truy cập [apibox.erweima.ai](https://apibox.erweima.ai)
2. Đăng ký tài khoản
3. Lấy API key từ dashboard
4. Nhập API key vào plugin

== Frequently Asked Questions ==

= Tôi cần API key từ đâu? =

Bạn cần đăng ký tại [apibox.erweima.ai](https://apibox.erweima.ai) để lấy API key.

= Mất bao lâu để tạo một bài hát? =

Thông thường từ 30 giây đến 2 phút tùy thuộc vào độ phức tạp và model sử dụng.

= Mỗi lần tạo được bao nhiêu bài? =

Mỗi lần tạo sẽ cho ra 2 phiên bản khác nhau của bài hát.

= URL audio có hết hạn không? =

Có, URL audio có thời hạn. Nếu muốn lưu trữ lâu dài, hãy tải về máy.

= Tại sao nên viết prompt bằng tiếng Anh? =

AI được huấn luyện chủ yếu với tiếng Anh nên prompt tiếng Anh sẽ cho kết quả tốt hơn.

== Screenshots ==

1. Dashboard quản trị
2. Trang cài đặt
3. Form tạo nhạc cơ bản
4. Form tạo nhạc với lyrics
5. Kết quả tạo nhạc

== Changelog ==

= 1.0.0 =
* Phiên bản đầu tiên
* Hỗ trợ tạo nhạc từ prompt
* Hỗ trợ tạo nhạc với lyrics tùy chỉnh
* Tích hợp ChatGPT cho auto generate
* Tạo lyrics AI
* Dashboard quản trị
* Lịch sử tạo nhạc
* REST API đầy đủ
* 4 shortcodes

== Upgrade Notice ==

= 1.0.0 =
Phiên bản đầu tiên của plugin.
