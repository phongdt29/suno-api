# Quick Test Suno API

## Test ngay trong Browser Console

Mở Browser Console (F12) và chạy các lệnh sau:

### 1. Test AJAX Configuration

```javascript
// Kiểm tra config có load không
console.log('AJAX URL:', miraculousAjax.ajax_url);
console.log('Nonce:', miraculousAjax.nonce);
console.log('API URL:', miraculousAjax.api_url);
```

### 2. Test Get Credits (Manual)

```javascript
jQuery.post(miraculousAjax.ajax_url, {
    action: 'get_credits',
    nonce: miraculousAjax.nonce
}, function(response) {
    console.log('Credits Response:', response);
    if (response.success) {
        console.log('✓ Success:', response.data);
    } else {
        console.error('✗ Error:', response.data);
    }
}).fail(function(jqXHR) {
    console.error('✗ AJAX Failed:', jqXHR.responseText);
});
```

### 3. Test Get Song (Manual)

```javascript
// Thay YOUR_TASK_ID bằng task ID thực
var testTaskId = 'YOUR_TASK_ID';

jQuery.post(miraculousAjax.ajax_url, {
    action: 'get_song',
    nonce: miraculousAjax.nonce,
    task_id: testTaskId
}, function(response) {
    console.log('Song Response:', response);
    if (response.success) {
        console.log('✓ Success:', response.data);
    } else {
        console.error('✗ Error:', response.data);
        if (response.data.debug) {
            console.error('Debug Info:', response.data.debug);
        }
    }
}).fail(function(jqXHR) {
    console.error('✗ AJAX Failed:', jqXHR.responseText);
});
```

### 4. Test Generate Music (Manual)

```javascript
jQuery.post(miraculousAjax.ajax_url, {
    action: 'generate_music',
    nonce: miraculousAjax.nonce,
    prompt: 'A happy pop song about summer',
    model: 'V5'
}, function(response) {
    console.log('Generate Response:', response);
    if (response.success) {
        console.log('✓ Success:', response.data);
        var taskId = response.data.data ? (response.data.data.task_id || response.data.data.id) : null;
        if (taskId) {
            console.log('Task ID:', taskId);
        }
    } else {
        console.error('✗ Error:', response.data);
        if (response.data.debug) {
            console.error('Debug Info:', response.data.debug);
        }
    }
}).fail(function(jqXHR) {
    console.error('✗ AJAX Failed:', jqXHR.responseText);
});
```

### 5. Test Direct API Call (PHP Backend)

```javascript
// Test trực tiếp WordPress backend
fetch(miraculousAjax.ajax_url + '?action=get_credits&nonce=' + miraculousAjax.nonce)
    .then(r => r.text())
    .then(text => {
        console.log('Raw Response:', text);
        try {
            var json = JSON.parse(text);
            console.log('Parsed JSON:', json);
        } catch(e) {
            console.error('Not valid JSON!', e);
        }
    });
```

## Xem Error Logs Real-time

### Trong Terminal (nếu có quyền SSH):

```bash
# Tail error log
tail -f wp-content/debug.log | grep "Suno API"

# Hoặc xem 50 dòng cuối
tail -50 wp-content/debug.log | grep "Suno API"
```

### Trong WordPress Admin:

1. Install plugin: WP Error Log Viewer
2. Hoặc dùng debug tool: `/wp-content/themes/miraculous-music/debug-suno-api.php`

## Common Errors & Solutions

### Error: "undefined miraculousAjax"

**Nguyên nhân:** JavaScript chưa được enqueue đúng

**Giải pháp:**
```javascript
// Check script loaded
console.log('Scripts loaded:', document.querySelectorAll('script[src*="suno-api"]'));

// Hard reload: Ctrl+Shift+R
```

### Error: "Phản hồi API không hợp lệ"

**Nguyên nhân:** API trả về không phải JSON

**Debug:**
```javascript
// Xem raw response
jQuery.post(miraculousAjax.ajax_url, {
    action: 'get_credits',
    nonce: miraculousAjax.nonce
}, null, 'text').done(function(text) {
    console.log('Raw text:', text);
    console.log('Is JSON?', text[0] === '{');
});
```

### Error: "Không thể tạo bài hát"

**Nguyên nhân:** API key sai hoặc hết credits

**Debug:**
```javascript
// Test API key
jQuery.post(miraculousAjax.ajax_url, {
    action: 'get_credits',
    nonce: miraculousAjax.nonce
}, function(response) {
    if (response.success) {
        console.log('✓ API Key OK - Credits:', response.data);
    } else {
        console.error('✗ API Key Problem:', response.data.message);
    }
});
```

## Check API Endpoint từ PHP

Tạo file test: `test-api.php` trong theme root:

```php
<?php
require_once('../../../wp-load.php');

$api_url = get_option('suno_api_url');
$api_key = get_option('suno_api_key');

echo "<pre>";
echo "API URL: " . $api_url . "\n";
echo "API Key: " . substr($api_key, 0, 10) . "...\n\n";

// Test Credits
$ch = curl_init($api_url . '/api/v1/generate/credit');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer ' . $api_key,
    'Accept: application/json'
));
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
echo "Response: " . $response . "\n";
echo "</pre>";
?>
```

Truy cập: `http://localhost/suno-api/wp-content/themes/miraculous-music/test-api.php`

## Expected Outputs

### Success - Get Credits:
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

### Success - Generate:
```json
{
  "success": true,
  "data": {
    "task_id": "abc123",
    "status": "processing"
  },
  "http_code": 200
}
```

### Error - API Key Invalid:
```json
{
  "success": false,
  "message": "API trả về lỗi 401",
  "error": "HTTP 401",
  "http_code": 401
}
```

## Network Tab Debug

1. Mở DevTools → Network tab
2. Filter: XHR
3. Thực hiện action (generate/load)
4. Click vào request
5. Xem:
   - Headers (có Authorization header không?)
   - Payload (data gửi đi)
   - Response (dữ liệu trả về)

## Last Resort - Enable Full Debug

Trong `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', true);
define('SCRIPT_DEBUG', true);
```

Reload trang và lỗi sẽ hiển thị trực tiếp.
