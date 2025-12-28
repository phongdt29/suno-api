# Suno API PHP Wrapper

API PHP để tương tác với Suno AI Music Generator thông qua apibox.erweima.ai

## Cài đặt

1. Copy thư mục `suno-api` vào htdocs
2. Truy cập: `http://localhost/suno-api/`

## Cấu hình

API Key mặc định đã được cấu hình sẵn. Nếu muốn thay đổi, tạo file `config.php`:

```php
<?php
return [
    'api_key' => 'YOUR_API_KEY_HERE',
];
```

## API Endpoints

### 1. Xem Credits
Kiểm tra số credits còn lại.

```
GET /suno-api/?action=credits
```

**Response:**
```json
{
    "success": true,
    "data": {
        "credits": 100
    }
}
```

---

### 2. Tạo bài hát (Generate)
Tạo bài hát từ prompt mô tả.

```
GET/POST /suno-api/?action=generate
```

**Parameters:**
| Param | Type | Required | Mô tả |
|-------|------|----------|-------|
| `prompt` | string | Yes | Mô tả bài hát (VD: "A happy pop song about love") |
| `instrumental` | boolean | No | `true` = không có lời, chỉ nhạc |
| `model` | string | No | Model: `V3_5`, `V4`, `V4_5` (mặc định: V3_5) |

**Ví dụ:**
```
GET /suno-api/?action=generate&prompt=A romantic ballad about summer love&model=V4
```

**Response:**
```json
{
    "success": true,
    "data": {
        "taskId": "xxx-xxx-xxx",
        "status": "processing"
    }
}
```

---

### 3. Tạo bài hát Custom (với Lyrics)
Tạo bài hát với lyrics và style tùy chỉnh.

```
POST /suno-api/?action=generate_custom
```

**Parameters:**
| Param | Type | Required | Mô tả |
|-------|------|----------|-------|
| `title` | string | No | Tên bài hát |
| `lyrics` | string | Yes | Lời bài hát |
| `style` | string | No | Phong cách (VD: "pop rock, energetic") |
| `instrumental` | boolean | No | `true` = không có lời |
| `model` | string | No | Model: `V3_5`, `V4`, `V4_5` |

**Ví dụ (cURL):**
```bash
curl -X POST "http://localhost/suno-api/?action=generate_custom" \
  -d "title=My Love Song" \
  -d "lyrics=[Verse]\nYou are my sunshine\nMy only sunshine\n\n[Chorus]\nYou make me happy\nWhen skies are gray" \
  -d "style=pop ballad, emotional"
```

---

### 4. Lấy thông tin bài hát
Kiểm tra trạng thái và lấy URL bài hát đã tạo.

```
GET /suno-api/?action=get_song&task_id=YOUR_TASK_ID
```

**Parameters:**
| Param | Type | Required | Mô tả |
|-------|------|----------|-------|
| `task_id` | string | Yes | Task ID từ response generate |

**Response (đang xử lý):**
```json
{
    "success": true,
    "data": {
        "status": "processing"
    }
}
```

**Response (hoàn thành):**
```json
{
    "success": true,
    "data": {
        "status": "completed",
        "songs": [
            {
                "id": "audio_id_1",
                "title": "My Song",
                "audio_url": "https://...",
                "image_url": "https://...",
                "duration": 180
            }
        ]
    }
}
```

---

### 5. Extend/Continue bài hát
Kéo dài hoặc tiếp tục bài hát hiện có.

```
POST /suno-api/?action=extend
```

**Parameters:**
| Param | Type | Required | Mô tả |
|-------|------|----------|-------|
| `audio_id` | string | Yes | ID của bài hát cần extend |
| `prompt` | string | No | Lyrics tiếp theo |
| `style` | string | No | Phong cách |
| `continue_at` | int | No | Thời điểm tiếp tục (giây) |
| `model` | string | No | Model sử dụng |

---

### 6. Tạo Lyrics
Tạo lời bài hát từ prompt.

```
POST /suno-api/?action=lyrics
```

**Parameters:**
| Param | Type | Required | Mô tả |
|-------|------|----------|-------|
| `prompt` | string | Yes | Mô tả nội dung lyrics |

**Ví dụ:**
```
GET /suno-api/?action=lyrics&prompt=A song about coding at night
```

---

### 7. Lấy Lyrics đã tạo

```
GET /suno-api/?action=get_lyrics&task_id=YOUR_TASK_ID
```

---

### 8. Upload Audio

```
POST /suno-api/?action=upload
```

**Parameters:**
| Param | Type | Required | Mô tả |
|-------|------|----------|-------|
| `audio_url` | string | Yes | URL của file audio |

---

## Ví dụ sử dụng với JavaScript

```javascript
// Tạo bài hát
async function generateSong(prompt) {
    const response = await fetch('/suno-api/?action=generate&prompt=' + encodeURIComponent(prompt));
    const data = await response.json();

    if (data.success) {
        const taskId = data.data.taskId;
        // Polling để lấy kết quả
        return await pollForResult(taskId);
    }
    return null;
}

// Polling kiểm tra kết quả
async function pollForResult(taskId) {
    while (true) {
        const response = await fetch('/suno-api/?action=get_song&task_id=' + taskId);
        const data = await response.json();

        if (data.data.status === 'completed') {
            return data.data.songs;
        }

        // Đợi 5 giây rồi kiểm tra lại
        await new Promise(resolve => setTimeout(resolve, 5000));
    }
}

// Sử dụng
generateSong('A happy pop song about friendship').then(songs => {
    console.log('Generated songs:', songs);
});
```

## Ví dụ sử dụng với PHP

```php
<?php
// Tạo bài hát
$response = file_get_contents('http://localhost/suno-api/?action=generate&prompt=' . urlencode('A rock song about freedom'));
$data = json_decode($response, true);

if ($data['success']) {
    $taskId = $data['data']['taskId'];

    // Polling
    do {
        sleep(5);
        $result = file_get_contents('http://localhost/suno-api/?action=get_song&task_id=' . $taskId);
        $resultData = json_decode($result, true);
    } while ($resultData['data']['status'] !== 'completed');

    // Lấy URL bài hát
    print_r($resultData['data']['songs']);
}
```

## Models

| Model | Mô tả |
|-------|-------|
| `V3_5` | Model mặc định, cân bằng chất lượng và tốc độ |
| `V4` | Model mới hơn, chất lượng cao hơn |
| `V4_5` | Model mới nhất |

## Lưu ý

- Mỗi lần generate sẽ tạo ra 2 bài hát
- Thời gian xử lý thường từ 30 giây đến 2 phút
- Kiểm tra credits trước khi generate để đảm bảo đủ quota
- Audio URL có thời hạn, nên download về nếu cần lưu trữ lâu dài
