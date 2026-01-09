# Hướng dẫn Test Player

## Kiểm tra khi click vào nút play

### 1. Mở Browser Console (F12)

Khi click vào nút play `.ms_play_icon.play-suno-song`, bạn sẽ thấy các log sau:

```javascript
Play button clicked: {
    audioUrl: "https://...",
    videoUrl: "https://...",
    title: "Tên bài hát",
    artist: "Suno AI",
    poster: "https://..."
}

Track to add: {
    title: "...",
    artist: "...",
    mp3: "...",
    mp4: "...",
    poster: "...",
    image: "...",
    option: "..."
}

Adding to playlist and playing...
Track added successfully!
```

### 2. Kiểm tra jPlayer có sẵn sàng không

```javascript
// Trong console
console.log(typeof window.myPlaylist);
// Kết quả mong đợi: "object"

console.log(window.myPlaylist);
// Kết quả mong đợi: Object {playlist: Array, current: 0, ...}
```

### 3. Test thủ công add bài hát

```javascript
// Test thêm bài hát vào playlist
window.myPlaylist.add({
    title: "Test Song",
    artist: "Test Artist",
    mp3: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3",
    poster: "https://via.placeholder.com/300"
}, true); // true = play immediately
```

### 4. Các vấn đề thường gặp

#### Lỗi: "jPlayer not initialized"

**Nguyên nhân:** Player chưa được load

**Giải pháp:**
```javascript
// Check xem class .audio-player có tồn tại không
console.log($('.audio-player').length); // Phải > 0

// Đợi DOM load xong
$(document).ready(function() {
    console.log('jPlayer ready:', typeof window.myPlaylist);
});
```

#### Lỗi: Không có log trong console

**Nguyên nhân:** Event không được bind

**Giải pháp:**
```javascript
// Check event đã bind chưa
console.log($._data($('.play-suno-song')[0], 'events'));

// Re-bind manually
$(document).on('click', '.play-suno-song', function(e) {
    e.preventDefault();
    console.log('Click detected!');
});
```

#### Lỗi: Click không phản hồi

**Nguyên nhân:** Element có thể bị overlay che

**Kiểm tra:**
```javascript
// Check z-index
$('.ms_play_icon').css('z-index');
$('.ms_main_overlay').css('z-index');

// Check pointer-events
$('.ms_play_icon').css('pointer-events');
```

## Cấu trúc HTML cần có

```html
<div class="ms_play_icon play-suno-song"
     data-audio-url="URL_AUDIO"
     data-video-url="URL_VIDEO"
     data-title="Tên bài hát"
     data-artist="Suno AI"
     data-poster="URL_IMAGE"
     style="cursor: pointer;">
    <img src="assets/images/svg/play.svg" alt="">
</div>
```

## Script Load Order

Thứ tự load script phải đúng:

1. jQuery
2. jPlayer core (`jquery.jplayer.min.js`)
3. jPlayer playlist (`jplayer.playlist.min.js`)
4. Audio player init (`audio-player.js`)
5. Suno API (`suno-api.js`)

## Debug Steps

### Bước 1: Kiểm tra jQuery
```javascript
console.log(typeof jQuery); // "function"
console.log(jQuery.fn.jquery); // Version: "3.x.x"
```

### Bước 2: Kiểm tra jPlayer loaded
```javascript
console.log(typeof $.jPlayer); // "function"
console.log(typeof jPlayerPlaylist); // "function"
```

### Bước 3: Kiểm tra player initialized
```javascript
console.log(typeof window.myPlaylist); // "object"
console.log(window.myPlaylist.playlist.length); // Số bài trong playlist
```

### Bước 4: Kiểm tra event binding
```javascript
// Test click event
$('.play-suno-song').first().trigger('click');
// Xem console có log không
```

### Bước 5: Manual test
```javascript
// Thêm bài test
window.myPlaylist.add({
    title: "Test",
    artist: "Artist",
    mp3: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3",
    image: "https://via.placeholder.com/300"
}, true);
```

## Giải pháp nhanh

Nếu vẫn không chạy, thử code này trong console:

```javascript
// Force bind event
$(document).off('click', '.play-suno-song');
$(document).on('click', '.play-suno-song', function(e) {
    e.preventDefault();

    var $btn = $(this);
    var track = {
        title: $btn.data('title') || 'Untitled',
        artist: $btn.data('artist') || 'Suno AI',
        mp3: $btn.data('audio-url'),
        mp4: $btn.data('video-url'),
        poster: $btn.data('poster'),
        image: $btn.data('poster')
    };

    console.log('Track:', track);

    if (window.myPlaylist) {
        window.myPlaylist.add(track, true);
        console.log('Added and playing!');
    } else {
        console.error('Playlist not found!');
    }
});

console.log('Event re-bound successfully!');
```

## Kiểm tra Network

Mở DevTools → Network tab:

1. Click play button
2. Xem có request tải MP3/MP4 không
3. Check status code (200 = OK)
4. Check Content-Type (audio/mpeg hoặc video/mp4)

## CSS Conflicts

Đôi khi CSS có thể block click:

```javascript
// Remove pointer-events none
$('.ms_play_icon').css('pointer-events', 'auto');

// Ensure proper z-index
$('.ms_play_icon').css('z-index', '100');

// Check display
$('.ms_play_icon').css('display'); // Không được "none"
```

## Thông tin thêm

- Player được init trong: `assets/js/plugins/player/audio-player.js`
- Event binding trong: `assets/js/suno-api.js`
- HTML template: `index.php` và `functions.php`

## Success Indicators

Khi hoạt động đúng:

✅ Console log hiện đầy đủ thông tin
✅ Bài hát xuất hiện trong playlist (bottom player)
✅ Audio/Video bắt đầu phát
✅ Player UI update (title, artist, progress bar)
✅ Play button chuyển thành pause button

## Nếu cần giúp

Gửi cho tôi:
1. Console logs (tất cả errors)
2. Network tab (các request failed)
3. HTML của play button (inspect element)
4. Browser version
