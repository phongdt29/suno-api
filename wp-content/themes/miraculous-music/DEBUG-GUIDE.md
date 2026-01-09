# Hướng dẫn Debug Suno API

## Lỗi bạn gặp phải

```json
{
  "success": false,
  "message": "Phản hồi API không hợp lệ"
}
```

```json
{
  "message": "Không thể tạo bài hát",
  "success": false
}
```

## Nguyên nhân có thể

1. **API Key không đúng** - API key hết hạn hoặc không valid
2. **API Response format khác** - Suno API trả về format khác với expected
3. **Network/CORS issues** - Server block requests
4. **API Endpoint thay đổi** - API endpoint đã update
5. **Credits hết** - Không còn credits để tạo nhạc

## Cách Debug

### Bước 1: Enable WordPress Debug Log

Mở file `wp-config.php` và thêm:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### Bước 2: Sử dụng Debug Tool

Truy cập:
```
http://localhost/suno-api/wp-content/themes/miraculous-music/debug-suno-api.php
```

**Debug Tool sẽ:**
- ✅ Kiểm tra API configuration
- ✅ Test Credits API
- ✅ Test Get Song API
- ✅ Test Generate Music API
- ✅ Hiển thị error logs

### Bước 3: Xem Error Logs

Logs được lưu tại: `wp-content/debug.log`

Tìm các dòng có chứa:
```
Suno API Request:
Suno API Response Code:
Suno API Response Body:
Suno API Error:
```

### Bước 4: Kiểm tra API Response

**Expected Response từ Suno API:**

#### 1. Get Credits
```json
{
  "success": true,
  "data": {
    "credits": 100,
    "total_credits": 500
  },
  "http_code": 200
}
```

#### 2. Generate Music
```json
{
  "success": true,
  "data": {
    "task_id": "abc123xyz",
    "status": "processing",
    "message": "Song generation started"
  },
  "http_code": 200
}
```

#### 3. Get Song
```json
{
  "success": true,
  "data": {
    "task_id": "abc123xyz",
    "status": "completed",
    "title": "Song Title",
    "audio_url": "https://...",
    "image_url": "https://...",
    "duration": "3:22"
  },
  "http_code": 200
}
```

## Sửa lỗi thường gặp

### Lỗi 1: "API key not configured"

**Giải pháp:**
1. Vào WordPress Admin → Suno API
2. Nhập API Key
3. Click "Test Connection"

### Lỗi 2: "Phản hồi API không hợp lệ"

**Nguyên nhân:** API trả về không phải JSON hoặc format khác

**Debug:**
```
1. Xem debug.log
2. Tìm dòng "Suno API Response Body:"
3. Copy response và parse JSON online
```

**Giải pháp:**
- Kiểm tra API endpoint có đúng không
- API có thể đã thay đổi format response
- Liên hệ support@sunoapi.org

### Lỗi 3: HTTP 401 Unauthorized

**Nguyên nhân:** API key không đúng

**Giải pháp:**
1. Lấy API key mới từ https://sunoapi.org/api-key
2. Cập nhật trong WordPress Admin
3. Test lại

### Lỗi 4: HTTP 403 Forbidden

**Nguyên nhân:** Không có quyền hoặc credits hết

**Giải pháp:**
1. Kiểm tra credits: Click "Check Credits"
2. Nạp thêm credits nếu cần
3. Verify API key permissions

### Lỗi 5: HTTP 429 Too Many Requests

**Nguyên nhân:** Quá nhiều requests

**Giải pháp:**
- Đợi vài phút rồi thử lại
- Cache được enable mặc định (5 phút)

### Lỗi 6: "Không thể tạo bài hát"

**Nguyên nhân:** Generate API failed

**Debug steps:**
1. Check error logs
2. Verify prompt không rỗng
3. Verify model (V4, V4.5, V5)
4. Check credits còn lại

## Manual Test với cURL

### Test Get Credits
```bash
curl -X GET "https://api.sunoapi.org/api/v1/generate/credit" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Accept: application/json"
```

### Test Generate Music
```bash
curl -X POST "https://api.sunoapi.org/api/v1/generate" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"prompt":"A happy pop song","model":"V5"}'
```

### Test Get Song
```bash
curl -X GET "https://api.sunoapi.org/api/v1/generate/record-info?task_id=YOUR_TASK_ID" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Accept: application/json"
```

## Advanced Debug

### Enable Full Request/Response Logging

Trong `functions.php`, tìm function `miraculous_suno_api_request()`:

```php
// Thêm vào sau dòng 341
error_log('Full Response Headers: ' . print_r(wp_remote_retrieve_headers($response), true));
error_log('Full Response Body: ' . $body);
```

### Check WordPress HTTP API

```php
// Thêm vào wp-config.php để test
define('WP_HTTP_BLOCK_EXTERNAL', false);
```

## Contact Support

Nếu vẫn lỗi sau khi debug:

1. **Suno API Support:**
   - Email: support@sunoapi.org
   - Docs: https://docs.sunoapi.org/

2. **Theme Support:**
   - GitHub Issues: https://github.com/phongdt29/suno-api/issues
   - Attach error logs và response body

## Checklist

- [ ] API Key đã cấu hình
- [ ] Test Connection thành công
- [ ] Debug logs enabled (WP_DEBUG_LOG)
- [ ] Check error logs (wp-content/debug.log)
- [ ] Test với debug-suno-api.php
- [ ] Verify API response format
- [ ] Check credits còn lại
- [ ] Test với cURL manually
- [ ] Liên hệ support nếu cần

## Quick Fix

Nếu muốn fix nhanh, thử:

```php
// Clear cache
delete_transient('suno_credits');
delete_transient('suno_song_' . md5($task_id));

// Hoặc clear tất cả transients
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_suno_%'");
```

Restart PHP/Apache và test lại.
