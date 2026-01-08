# Hướng Dẫn Sử Dụng Plugin Suno Music Generator

Plugin WordPress cho phép tạo nhạc AI sử dụng Suno API.

---

## Mục Lục

1. [Cài Đặt](#1-cài-đặt)
2. [Cấu Hình API Keys](#2-cấu-hình-api-keys)
3. [Sử Dụng Shortcodes](#3-sử-dụng-shortcodes)
4. [REST API](#4-rest-api)
5. [Xử Lý Lỗi Thường Gặp](#5-xử-lý-lỗi-thường-gặp)

---

## 1. Cài Đặt

### Bước 1: Upload Plugin
- Copy thư mục `suno-music-generator` vào `/wp-content/plugins/`

### Bước 2: Kích Hoạt
- Vào **WordPress Admin > Plugins > Installed Plugins**
- Tìm **Suno Music Generator** và nhấn **Activate**

### Bước 3: Cấu Hình
- Vào **Suno Music > Cài đặt**
- Nhập API keys (xem phần 2)

---

## 2. Cấu Hình API Keys

### Suno API Key (Bắt buộc)
1. Truy cập: https://apibox.erweima.ai
2. Đăng ký tài khoản
3. Lấy API key từ Dashboard
4. Nhập vào **Suno Music > Cài đặt > Suno API Key**

> **Lưu ý:** Plugin đã có sẵn API key mặc định để test

### OpenAI API Key (Tùy chọn)
Chỉ cần nếu muốn sử dụng tính năng **Auto Generate** (tự động tạo lyrics với ChatGPT)

1. Truy cập: https://platform.openai.com/api-keys
2. Tạo API key mới
3. Nhập vào **Suno Music > Cài đặt > OpenAI API Key**

---

## 3. Sử Dụng Shortcodes

### 3.1. Form Tạo Nhạc Cơ Bản
```
[suno_generator]
```

**Tham số:**
| Tham số | Giá trị | Mặc định | Mô tả |
|---------|---------|----------|-------|
| `model` | V3_5, V4, V4_5 | V3_5 | Model Suno sử dụng |
| `show_instrumental` | true/false | true | Hiện tùy chọn instrumental |
| `show_model` | true/false | true | Hiện dropdown chọn model |

**Ví dụ:**
```
[suno_generator model="V4" show_instrumental="false"]
```

---

### 3.2. Form Tạo Nhạc Với Lyrics Tùy Chỉnh
```
[suno_custom_generator]
```

Cho phép người dùng nhập:
- Tiêu đề bài hát
- Phong cách (Pop, Rock, Ballad...)
- Lyrics đầy đủ

**Tham số:**
| Tham số | Giá trị | Mặc định |
|---------|---------|----------|
| `model` | V3_5, V4, V4_5 | V3_5 |

---

### 3.3. Form Tạo Nhạc Tự Động (ChatGPT + Suno)
```
[suno_auto_generator]
```

> **Yêu cầu:** Phải cấu hình OpenAI API Key

AI sẽ tự động:
1. Tạo tiêu đề bài hát
2. Tạo phong cách phù hợp
3. Viết lyrics đầy đủ
4. Tạo nhạc với Suno

**Tham số:**
| Tham số | Giá trị | Mặc định |
|---------|---------|----------|
| `model` | V3_5, V4, V4_5 | V3_5 |
| `default_language` | vietnamese, english, korean, japanese | vietnamese |

**Ví dụ:**
```
[suno_auto_generator default_language="english"]
```

---

### 3.4. Form Tạo Lyrics AI
```
[suno_lyrics_generator]
```

Chỉ tạo lyrics từ mô tả, không tạo nhạc.

---

### 3.5. Danh Sách Bài Hát Đã Tạo
```
[suno_song_list]
```

Hiển thị danh sách bài hát đã tạo thành công.

**Tham số:**
| Tham số | Giá trị | Mặc định | Mô tả |
|---------|---------|----------|-------|
| `limit` | Số nguyên | 10 | Số bài hát hiển thị |
| `columns` | 1, 2, 3, 4 | 2 | Số cột hiển thị |
| `user` | all, current | all | Hiển thị tất cả hoặc chỉ user hiện tại |

**Ví dụ:**
```
[suno_song_list limit="20" columns="3"]
[suno_song_list user="current" limit="5" columns="1"]
```

---

## 4. REST API

Base URL: `/wp-json/suno/v1/`

### 4.1. Kiểm Tra Credits
```
GET /wp-json/suno/v1/credits
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

### 4.2. Tạo Nhạc Từ Prompt
```
POST /wp-json/suno/v1/generate
Content-Type: application/json

{
    "prompt": "A happy pop song about summer",
    "instrumental": false,
    "model": "V3_5"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "taskId": "abc123..."
    }
}
```

---

### 4.3. Tạo Nhạc Với Lyrics
```
POST /wp-json/suno/v1/generate-custom
Content-Type: application/json

{
    "lyrics": "[Verse]\nWalking down...\n\n[Chorus]\nSummer dreams...",
    "title": "Summer Dreams",
    "style": "Pop, upbeat",
    "model": "V4"
}
```

---

### 4.4. Lấy Trạng Thái Bài Hát
```
GET /wp-json/suno/v1/song/{task_id}
```

**Response (đang xử lý):**
```json
{
    "success": true,
    "data": {
        "status": "processing",
        "taskId": "abc123..."
    }
}
```

**Response (hoàn thành):**
```json
{
    "success": true,
    "data": {
        "status": "completed",
        "taskId": "abc123...",
        "songs": [
            {
                "id": "song_id",
                "title": "Summer Dreams",
                "audio_url": "https://...",
                "image_url": "https://...",
                "video_url": "https://...",
                "duration": 180,
                "style": "Pop"
            }
        ]
    }
}
```

---

### 4.5. Tạo Nhạc Tự Động (ChatGPT)
```
POST /wp-json/suno/v1/auto-generate
Content-Type: application/json

{
    "idea": "Một bài hát về tình yêu mùa hè",
    "language": "vietnamese",
    "model": "V3_5"
}
```

---

### 4.6. Tạo Lyrics
```
POST /wp-json/suno/v1/lyrics
Content-Type: application/json

{
    "prompt": "A love song about missing someone"
}
```

---

### 4.7. Lấy Lyrics Đã Tạo
```
GET /wp-json/suno/v1/lyrics/{task_id}
```

---

### 4.8. Mở Rộng Bài Hát
```
POST /wp-json/suno/v1/extend
Content-Type: application/json

{
    "audio_id": "existing_song_id",
    "prompt": "New lyrics for continuation",
    "style": "Same style",
    "continue_at": 60,
    "model": "V3_5"
}
```

---

### 4.9. Lấy Lịch Sử
```
GET /wp-json/suno/v1/history?page=1&per_page=10
```

---

## 5. Xử Lý Lỗi Thường Gặp

### Lỗi: "API key chưa được cấu hình"
**Nguyên nhân:** Chưa nhập Suno API key
**Giải pháp:** Vào **Suno Music > Cài đặt** và nhập API key

---

### Lỗi: "OpenAI API key chưa được cấu hình"
**Nguyên nhân:** Dùng shortcode `[suno_auto_generator]` nhưng chưa có OpenAI key
**Giải pháp:**
- Nhập OpenAI API key trong cài đặt, HOẶC
- Dùng shortcode `[suno_generator]` hoặc `[suno_custom_generator]` thay thế

---

### Lỗi: "Không thể tạo nội dung từ ChatGPT"
**Nguyên nhân:**
- OpenAI API key không hợp lệ
- Hết credit OpenAI
- Lỗi kết nối

**Giải pháp:** Kiểm tra API key và credit tại https://platform.openai.com

---

### Lỗi: "Không thể lấy thông tin bài hát"
**Nguyên nhân:**
- Task ID không hợp lệ
- Bài hát đang xử lý
- Lỗi kết nối API

**Giải pháp:** Đợi và thử lại sau vài giây

---

### Lỗi: "Please enter callBackUrl"
**Nguyên nhân:** API yêu cầu callback URL
**Giải pháp:** Plugin đã tự động xử lý, nếu vẫn gặp lỗi hãy kiểm tra URL WordPress

---

### Shortcode không hiển thị / Lỗi trắng trang
**Nguyên nhân:**
- Plugin chưa kích hoạt
- Lỗi PHP trong plugin
- Xung đột với plugin/theme khác

**Giải pháp:**
1. Kiểm tra plugin đã kích hoạt
2. Bật WP_DEBUG trong wp-config.php để xem lỗi
3. Tắt các plugin khác để kiểm tra xung đột

---

## Lưu Ý Quan Trọng

1. **Mỗi lần tạo nhạc sẽ cho ra 2 phiên bản** khác nhau của bài hát

2. **Thời gian xử lý:** 30 giây - 2 phút tùy model

3. **URL audio có thời hạn:** Tải về nếu muốn lưu trữ lâu dài

4. **Viết prompt bằng tiếng Anh** để có kết quả tốt nhất

5. **Kiểm tra credits** trước khi tạo nhạc

6. **Models:**
   - `V3_5`: Mặc định, cân bằng chất lượng/tốc độ
   - `V4`: Chất lượng cao hơn
   - `V4_5`: Mới nhất, chất lượng tốt nhất

---

## Hỗ Trợ

Nếu gặp vấn đề, vui lòng:
1. Kiểm tra mục "Xử Lý Lỗi Thường Gặp" ở trên
2. Bật WP_DEBUG để xem log lỗi
3. Kiểm tra Console trình duyệt (F12) để xem lỗi JavaScript
