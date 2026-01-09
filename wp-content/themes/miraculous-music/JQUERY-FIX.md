# Fix lỗi: Uncaught TypeError: $ is not a function

## Lỗi gặp phải

```
Uncaught TypeError: $ is not a function
    at audio-player.js?ver=1.0.0:1:1
```

## Nguyên nhân

### 1. WordPress jQuery noConflict Mode

WordPress chạy jQuery trong **noConflict mode** để tránh xung đột với các thư viện khác. Trong mode này:

- ❌ `$` không hoạt động
- ✅ `jQuery` hoạt động

### 2. Thứ tự load script sai

Scripts được load theo thứ tự:
```
❌ SAI:
1. jquery.jplayer.min.js
2. jplayer.playlist.min.js  ← Load trước jPlayer core!
3. audio-player.js

✅ ĐÚNG:
1. jquery.jplayer.min.js     ← Core phải load trước
2. jplayer.playlist.min.js   ← Playlist depend on core
3. audio-player.js            ← Depend on both
```

## Giải pháp đã thực hiện

### 1. Sửa audio-player.js - WordPress jQuery Wrapper

**Trước:**
```javascript
$(function() {
    "use strict";
    if ($('.audio-player').length) {
        // code...
    }
});
```

**Sau:**
```javascript
jQuery(document).ready(function($) {
    "use strict";
    if ($('.audio-player').length) {
        // code...
    }
});
```

**Tại sao?**
- `jQuery(document).ready(function($) {` - Pass `$` as parameter
- Bên trong function, `$` có thể dùng bình thường
- Compatible với WordPress noConflict mode

### 2. Sửa thứ tự load script trong functions.php

**Trước:**
```php
wp_enqueue_script('jplayer-playlist', '...', array('jquery'), ...);
wp_enqueue_script('jplayer', '...', array('jquery'), ...);
wp_enqueue_script('audio-player', '...', array('jquery', 'jplayer'), ...);
```

**Sau:**
```php
// jPlayer core FIRST
wp_enqueue_script('jplayer', '...', array('jquery'), ...);

// Playlist depends on jPlayer
wp_enqueue_script('jplayer-playlist', '...', array('jquery', 'jplayer'), ...);

// Audio player depends on BOTH
wp_enqueue_script('audio-player', '...', array('jquery', 'jplayer', 'jplayer-playlist'), ...);
```

**Dependencies chain:**
```
jquery
  ↓
jplayer (depends on: jquery)
  ↓
jplayer-playlist (depends on: jquery, jplayer)
  ↓
audio-player (depends on: jquery, jplayer, jplayer-playlist)
```

## Files đã sửa

### 1. [audio-player.js](assets/js/plugins/player/audio-player.js:1)

```javascript
// Line 1
jQuery(document).ready(function($) {
    "use strict";
    if ($('.audio-player').length) {
        // ... rest of code
    }
});
```

### 2. [functions.php](functions.php:65-68)

```php
// Lines 65-68
// jPlayer scripts - MUST load in correct order: jPlayer core -> playlist -> audio-player
wp_enqueue_script('jplayer', get_template_directory_uri() . '/assets/js/plugins/player/jquery.jplayer.min.js', array('jquery'), $theme_version, true);
wp_enqueue_script('jplayer-playlist', get_template_directory_uri() . '/assets/js/plugins/player/jplayer.playlist.min.js', array('jquery', 'jplayer'), $theme_version, true);
wp_enqueue_script('audio-player', get_template_directory_uri() . '/assets/js/plugins/player/audio-player.js', array('jquery', 'jplayer', 'jplayer-playlist'), $theme_version, true);
```

## WordPress jQuery Best Practices

### Method 1: jQuery wrapper (Recommended)

```javascript
jQuery(document).ready(function($) {
    // $ works inside this function
    $('.element').click(function() {
        console.log('Clicked!');
    });
});
```

### Method 2: Use jQuery instead of $

```javascript
jQuery(document).ready(function() {
    jQuery('.element').click(function() {
        console.log('Clicked!');
    });
});
```

### Method 3: IIFE wrapper

```javascript
(function($) {
    $(document).ready(function() {
        $('.element').click(function() {
            console.log('Clicked!');
        });
    });
})(jQuery);
```

### ❌ AVOID (Doesn't work in WordPress)

```javascript
$(document).ready(function() {
    // $ is undefined!
});
```

## Script Load Order - Best Practices

### Rule 1: Core libraries first

```php
wp_enqueue_script('jquery');
wp_enqueue_script('library-core', '...', array('jquery'), ...);
wp_enqueue_script('library-plugin', '...', array('jquery', 'library-core'), ...);
```

### Rule 2: Declare dependencies

```php
wp_enqueue_script('my-script',
    '...',
    array('jquery', 'dependency-1', 'dependency-2'),  // Dependencies
    '1.0.0',  // Version
    true      // In footer
);
```

### Rule 3: Use correct hooks

```php
add_action('wp_enqueue_scripts', 'my_scripts_function');

function my_scripts_function() {
    // Enqueue scripts here
}
```

## Debugging jQuery Issues

### Check if jQuery loaded

```javascript
// In browser console
console.log(typeof jQuery); // "function" = loaded
console.log(typeof $);      // "undefined" = noConflict mode
```

### Check script order

```javascript
// View all loaded scripts
document.querySelectorAll('script[src]').forEach(function(script) {
    console.log(script.src);
});
```

### Test jQuery works

```javascript
jQuery(document).ready(function($) {
    console.log('jQuery loaded!');
    console.log($('.audio-player').length);
});
```

## Common jQuery Errors in WordPress

### Error 1: $ is not a function

**Cause:** Using `$` outside jQuery wrapper

**Fix:**
```javascript
jQuery(document).ready(function($) {
    // Use $ here
});
```

### Error 2: jQuery is not defined

**Cause:** jQuery not loaded or wrong order

**Fix:**
```php
wp_enqueue_script('jquery'); // Add this
wp_enqueue_script('my-script', '...', array('jquery'), ...);
```

### Error 3: Plugin function not defined

**Cause:** Plugin loaded before core library

**Fix:**
```php
// Wrong order
wp_enqueue_script('plugin', '...', array('jquery'), ...);
wp_enqueue_script('core', '...', array('jquery'), ...);

// Correct order
wp_enqueue_script('core', '...', array('jquery'), ...);
wp_enqueue_script('plugin', '...', array('jquery', 'core'), ...);
```

## Testing After Fix

### 1. Clear browser cache

```
Ctrl + Shift + Delete
or
Ctrl + Shift + R (hard reload)
```

### 2. Check console

```javascript
// Should see NO errors
// Check jQuery loaded
console.log(typeof jQuery); // "function"
console.log(typeof window.myPlaylist); // "object"
```

### 3. Test player

```javascript
// Add a test track
jQuery(document).ready(function($) {
    if (window.myPlaylist) {
        console.log('Player ready!');
        window.myPlaylist.add({
            title: "Test",
            artist: "Test",
            mp3: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3"
        }, true);
    }
});
```

### 4. Click play button

Click any `.ms_play_icon.play-suno-song` → Should add to playlist and play

## Correct Script Loading Order

```
1. jQuery (WordPress core)
   ↓
2. Bootstrap
   ↓
3. Swiper
   ↓
4. jPlayer core
   ↓
5. jPlayer playlist
   ↓
6. Audio player init
   ↓
7. Volume control
   ↓
8. Nice select
   ↓
9. mCustomScrollbar
   ↓
10. Custom scripts
   ↓
11. Suno API integration
```

## Prevention - Checklist

When adding new scripts:

- [ ] Wrap code in `jQuery(document).ready(function($) {}`
- [ ] Declare dependencies in `wp_enqueue_script()`
- [ ] Load core libraries before plugins
- [ ] Test in browser console
- [ ] Check for errors in DevTools
- [ ] Clear cache after changes

## Summary

✅ **Fixed jQuery wrapper** - `jQuery(document).ready(function($) {})`
✅ **Fixed script order** - jPlayer core → playlist → audio-player
✅ **Added dependencies** - Each script declares what it needs
✅ **WordPress compatible** - Works with noConflict mode

**Result:**
- ✅ No more `$ is not a function` error
- ✅ jPlayer initializes correctly
- ✅ Player ready to use
- ✅ Click play → music plays!

## References

- [WordPress jQuery](https://developer.wordpress.org/reference/functions/wp_enqueue_script/)
- [jQuery noConflict](https://api.jquery.com/jquery.noconflict/)
- [Script Dependencies](https://developer.wordpress.org/themes/basics/including-css-javascript/)
