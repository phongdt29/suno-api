# Hướng dẫn cài đặt WordPress Theme - Miraculous Music

## Bước 1: Cài đặt WordPress

1. Đảm bảo bạn đã cài đặt WordPress tại `c:\xampp8\htdocs\suno-api\`
2. Nếu chưa có, tải WordPress từ https://wordpress.org/download/
3. Giải nén và copy các file WordPress vào thư mục `c:\xampp8\htdocs\suno-api\`

## Bước 2: Tạo Database

1. Mở phpMyAdmin: http://localhost/phpmyadmin
2. Tạo database mới tên `suno_music`
3. Collation: `utf8mb4_unicode_ci`

## Bước 3: Cài đặt WordPress

1. Truy cập: http://localhost/suno-api/
2. Chọn ngôn ngữ và click "Continue"
3. Điền thông tin database:
   - Database Name: `suno_music`
   - Username: `root`
   - Password: (để trống nếu dùng XAMPP mặc định)
   - Database Host: `localhost`
   - Table Prefix: `wp_`
4. Click "Submit" và "Run the installation"
5. Điền thông tin site:
   - Site Title: `Miraculous Music`
   - Username: (tên đăng nhập admin của bạn)
   - Password: (mật khẩu mạnh)
   - Your Email: (email của bạn)
6. Click "Install WordPress"

## Bước 4: Kích hoạt Theme

1. Đăng nhập WordPress Admin: http://localhost/suno-api/wp-admin
2. Vào **Appearance → Themes**
3. Tìm theme **Miraculous Music**
4. Click **Activate**

## Bước 5: Cấu hình Theme

### 5.1 Tạo Menu

1. Vào **Appearance → Menus**
2. Tạo menu mới:
   - Menu Name: `Sidebar Menu`
   - Click "Create Menu"
3. Thêm các trang/links vào menu
4. Chọn location: **Sidebar Menu**
5. Click "Save Menu"

### 5.2 Cấu hình Customizer

1. Vào **Appearance → Customize**
2. Cấu hình các mục:
   - **Site Identity**: Upload logo
   - **Banner Settings**:
     - Banner Title
     - Banner Description
     - Banner Background Image
   - **Contact Information**:
     - Phone Number
     - Email Address
     - Physical Address
   - **Social Media Links**:
     - Facebook URL
     - Twitter URL
     - LinkedIn URL
     - Google+ URL

### 5.3 Thiết lập Widgets

1. Vào **Appearance → Widgets**
2. Kéo widgets vào các vùng:
   - **Sidebar** - Sidebar chính
   - **Footer 1-4** - 4 cột footer

## Bước 6: Thêm Nội dung Mẫu

### 6.1 Tạo Music Posts

1. Vào **Music → Add New**
2. Điền thông tin:
   - Title: Tên bài hát
   - Content: Mô tả bài hát
   - Featured Image: Ảnh bìa
   - Custom Fields:
     - `_music_artist`: Tên ca sĩ
     - `_music_duration`: Thời lượng (VD: 3:22)
     - `_music_plays`: Số lượt phát
3. Publish

### 6.2 Tạo Albums

1. Vào **Albums → Add New**
2. Điền thông tin và publish

### 6.3 Tạo Artists

1. Vào **Artists → Add New**
2. Điền thông tin và publish

### 6.4 Tạo Playlists

1. Vào **Playlists → Add New**
2. Điền thông tin và publish

### 6.5 Tạo Genres

1. Vào **Music → Genres**
2. Thêm các thể loại: Pop, Rock, Jazz, Classical, Hip Hop, etc.

## Bước 7: Thiết lập Permalinks

1. Vào **Settings → Permalinks**
2. Chọn **Post name**
3. Click "Save Changes"

## Bước 8: Test Theme

1. Truy cập trang chủ: http://localhost/suno-api/
2. Kiểm tra:
   - ✅ Header hiển thị đúng
   - ✅ Sidebar menu hoạt động
   - ✅ Sliders chạy mượt
   - ✅ Music player hiển thị
   - ✅ Footer hiển thị đầy đủ
   - ✅ Responsive trên mobile

## Cấu trúc Theme Files

```
miraculous-music/
├── assets/
│   ├── css/              # Stylesheets
│   ├── js/               # JavaScript files
│   ├── images/           # Images
│   └── fonts/            # Fonts
├── header.php            # Header template
├── footer.php            # Footer template
├── index.php             # Main homepage
├── single.php            # Single post template
├── page.php              # Page template
├── sidebar.php           # Sidebar
├── functions.php         # Theme functions
├── style.css             # Main stylesheet (required by WP)
├── screenshot.png        # Theme preview
└── README.md             # Documentation
```

## Custom Post Types

Theme đã tự động tạo các post types:

- **Music** (`/music/`) - Bài hát
- **Album** (`/albums/`) - Albums
- **Artist** (`/artists/`) - Ca sĩ
- **Playlist** (`/playlists/`) - Playlists

## Taxonomies

- **Genre** - Thể loại nhạc (áp dụng cho Music và Album)

## Troubleshooting

### Lỗi: Không thấy theme trong danh sách

**Giải pháp:**
- Kiểm tra theme đã được copy đúng vào `wp-content/themes/`
- File `style.css` phải có header đúng format

### Lỗi: CSS/JS không load

**Giải pháp:**
- Vào **Settings → Permalinks** và click "Save Changes"
- Xóa cache trình duyệt (Ctrl + F5)

### Lỗi: Sliders không hoạt động

**Giải pháp:**
- Kiểm tra jQuery đã được load
- Kiểm tra file `custom.js` đã được enqueue

### Lỗi: Menu không hiển thị

**Giải pháp:**
- Tạo menu mới tại **Appearance → Menus**
- Assign menu vào location "Sidebar Menu"

## Plugins Đề xuất

- **Advanced Custom Fields** - Quản lý custom fields cho music
- **Contact Form 7** - Form liên hệ
- **Yoast SEO** - SEO optimization
- **WP Super Cache** - Caching

## Hỗ trợ

Nếu gặp vấn đề, vui lòng tạo issue tại:
https://github.com/phongdt29/suno-api/issues

## License

GPL v2 or later
