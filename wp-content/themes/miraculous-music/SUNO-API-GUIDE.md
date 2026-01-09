# Hướng dẫn tích hợp Suno API

## Tổng quan

WordPress theme **Miraculous Music** đã được tích hợp đầy đủ với Suno API để:
- ✅ Tạo nhạc AI từ mô tả văn bản
- ✅ Load bài hát từ Task ID
- ✅ Lưu trữ và quản lý music từ Suno API
- ✅ Tự động cache kết quả API
- ✅ Tích hợp với jPlayer audio player

## 1. Cấu hình API

### Bước 1: Lấy API Key

1. Truy cập: https://sunoapi.org/api-key
2. Đăng ký tài khoản (nếu chưa có)
3. Copy API Key của bạn

### Bước 2: Nhập API Key vào WordPress

1. Đăng nhập WordPress Admin
2. Vào **Suno API** (menu bên trái)
3. Nhập thông tin:
   - **API Base URL**: `https://api.sunoapi.org` (mặc định)
   - **API Key**: Paste API key của bạn
4. Click **Save Settings**
5. Click **Test Connection** để kiểm tra

## 2. Sử dụng Template Suno Music Generator

### Tạo Page với Template

1. Vào **Pages → Add New**
2. Đặt tên: "AI Music Generator"
3. Ở **Page Attributes**, chọn **Template**: `Suno Music Generator`
4. Click **Publish**
5. Truy cập page vừa tạo

### Các tính năng trên Page

#### A. Generate Music (Tạo nhạc mới)

1. Nhập mô tả nhạc muốn tạo:
   - VD: "A happy pop song about summer"
   - VD: "Relaxing piano music for studying"

2. Chọn AI Model:
   - **V4**: Model cũ hơn, nhanh
   - **V4.5**: Cải tiến hơn
   - **V4.5 PLUS**: Âm thanh phong phú hơn
   - **V4.5 ALL**: Cấu trúc bài hát tốt hơn
   - **V5**: Model mới nhất (khuyên dùng)

3. Click **Generate Music**

4. Đợi 20-30 giây, hệ thống sẽ tự động:
   - Gửi request tạo nhạc
   - Lưu Task ID
   - Tự động polling để check kết quả
   - Hiển thị thông tin bài hát khi xong

#### B. Load Existing Song (Load bài hát có sẵn)

1. Nhập **Task ID** (từ lần generate trước)
2. Click **Load Song**
3. Bài hát sẽ được load và hiển thị

## 3. Quản lý Music Posts

### Thêm Music từ Admin

1. Vào **Music → Add New**
2. Điền thông tin:
   - **Title**: Tên bài hát
   - **Content**: Mô tả
   - **Featured Image**: Upload ảnh bìa

3. Trong **Suno API Information** meta box:
   - **Task ID**: Nhập task_id từ Suno API
   - Click **Fetch from API** để tự động load data
   - Hoặc nhập thủ công:
     - **Audio URL**: Link file MP3
     - **Cover Image URL**: Link ảnh bìa
     - **AI Model**: Chọn model đã dùng

4. Click **Publish**

### Tự động load từ API

Khi có Task ID, click nút **Fetch from API** sẽ tự động:
- ✅ Load Audio URL
- ✅ Load Cover Image URL
- ✅ Load Title (nếu chưa có)
- ✅ Lưu vào post meta

## 4. API Functions (Dành cho Developer)

### PHP Functions

```php
// Get song by task ID
$result = miraculous_get_song_by_key($task_id);

// Generate new music
$result = miraculous_generate_music('A happy song', 'V5');

// Get API credits
$credits = miraculous_get_credits();

// Make custom API request
$result = miraculous_suno_api_request('/api/v1/endpoint', 'POST', $data);
```

### JavaScript/AJAX

```javascript
// Get song by task ID
jQuery.post(miraculousAjax.ajax_url, {
    action: 'get_song',
    nonce: miraculousAjax.nonce,
    task_id: 'YOUR_TASK_ID'
}, function(response) {
    console.log(response.data);
});

// Generate music
jQuery.post(miraculousAjax.ajax_url, {
    action: 'generate_music',
    nonce: miraculousAjax.nonce,
    prompt: 'A happy song',
    model: 'V5'
}, function(response) {
    console.log(response.data);
});

// Get credits
jQuery.post(miraculousAjax.ajax_url, {
    action: 'get_credits',
    nonce: miraculousAjax.nonce
}, function(response) {
    console.log(response.data);
});
```

### JavaScript Helper (suno-api.js)

```javascript
// Load song by key
$('.load-song-by-key').data('task-id', 'YOUR_TASK_ID').click();

// Play song directly
SunoAPI.playSunoSong({
    audioUrl: 'https://...',
    title: 'Song Title',
    artist: 'Artist Name'
});

// Check credits
SunoAPI.getCredits();
```

## 5. Workflow tạo nhạc hoàn chỉnh

### Workflow 1: Tạo và Publish

1. Vào page **AI Music Generator**
2. Generate nhạc với prompt
3. Đợi nhạc được tạo xong (20-30s)
4. Copy Task ID
5. Vào **Music → Add New**
6. Paste Task ID vào meta box
7. Click **Fetch from API**
8. Điền thêm thông tin (nếu cần)
9. **Publish**

### Workflow 2: Load từ Task ID có sẵn

1. Vào **Music → Add New**
2. Nhập Task ID vào **Suno API Information**
3. Click **Fetch from API**
4. Kiểm tra thông tin đã load
5. **Publish**

## 6. Caching & Performance

### Cache tự động

- **Song data**: Cache 5 phút
- **Credits info**: Cache 1 phút
- Cache key format: `suno_song_{md5(task_id)}`

### Clear cache

```php
// Clear specific song cache
delete_transient('suno_song_' . md5($task_id));

// Clear credits cache
delete_transient('suno_credits');
```

## 7. Troubleshooting

### Lỗi: "API key not configured"

**Giải pháp:**
- Vào **Suno API** settings
- Nhập API key
- Save và test connection

### Lỗi: "Connection failed"

**Giải pháp:**
1. Kiểm tra API key đúng chưa
2. Kiểm tra API Base URL: `https://api.sunoapi.org`
3. Test bằng nút **Test Connection**
4. Kiểm tra server có block requests ra ngoài không

### Nhạc không được tạo

**Giải pháp:**
1. Check credits còn lại (click **Check Credits**)
2. Đợi lâu hơn (có thể mất 30-60s)
3. Check Task ID có đúng không
4. Thử load lại sau vài phút

### AJAX không hoạt động

**Giải pháp:**
1. Kiểm tra JavaScript console (F12) xem có lỗi không
2. Verify file `suno-api.js` đã được load
3. Check `miraculousAjax` variable có tồn tại không:
   ```javascript
   console.log(miraculousAjax);
   ```

## 8. API Endpoints

### Available Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/v1/generate` | POST | Generate new music |
| `/api/v1/generate/record-info` | GET | Get song by task_id |
| `/api/v1/generate/credit` | GET | Get remaining credits |
| `/api/v1/lyrics` | POST | Generate lyrics |
| `/api/v1/lyrics/record-info` | GET | Get lyrics by task_id |

### Response Format

```json
{
    "data": {
        "task_id": "abc123",
        "status": "completed",
        "title": "Song Title",
        "artist": "Artist Name",
        "audio_url": "https://...",
        "image_url": "https://...",
        "duration": "3:22"
    },
    "http_code": 200
}
```

## 9. Custom Filters & Hooks

### Filters

```php
// Modify API URL
add_filter('miraculous_api_url', function($url) {
    return 'https://custom-api.com';
});

// Modify cache duration
add_filter('miraculous_song_cache_time', function($seconds) {
    return 10 * MINUTE_IN_SECONDS; // 10 minutes
});
```

### Actions

```php
// After song loaded successfully
add_action('miraculous_song_loaded', function($song_data, $task_id) {
    // Do something
}, 10, 2);

// Before API request
add_action('miraculous_before_api_request', function($endpoint, $method) {
    // Log request
}, 10, 2);
```

## 10. Security

### Nonces

Tất cả AJAX requests đều được bảo vệ bằng WordPress nonce:
- Check: `check_ajax_referer('miraculous_ajax', 'nonce')`
- Create: `wp_create_nonce('miraculous_ajax')`

### Permissions

- **Generate Music**: Yêu cầu user đăng nhập
- **Get Song**: Public (có nonce)
- **Get Credits**: Public (có nonce)
- **API Settings**: Chỉ admin (`manage_options`)

## 11. Tài liệu API đầy đủ

- **Official Docs**: https://docs.sunoapi.org/
- **Get API Key**: https://sunoapi.org/api-key
- **Support**: support@sunoapi.org

## 12. Demo & Examples

Xem thêm ví dụ tại:
- Template file: `template-suno-music.php`
- JavaScript: `assets/js/suno-api.js`
- Functions: `functions.php` (dòng 266+)
