# Suno API Development Mode & Music Ranking Feature

## Overview
This document describes the development mode setup and music ranking feature for the Miraculous Music WordPress theme with Suno AI integration.

## 1. Development Mode (No Credits Required)

### Purpose
Allows testing music generation without consuming real Suno API credits by using mock responses.

### Configuration

**File: `wp-config.php`** (lines 98-103)
```php
// Suno API Development Mode
// Set to true to use mock data instead of real API calls (no credits required)
define('SUNO_DEV_MODE', true);

// Suno API Key (optional in dev mode)
define('SUNO_API_KEY', 'dev-key-no-credits-required');
```

### How It Works

When `SUNO_DEV_MODE` is enabled:
- All API requests are intercepted and return mock data
- No real API calls are made
- No credits are consumed
- Uses free sample MP3s from SoundHelix for testing

**File: `functions.php`** (lines 295-386)

Mock responses include:
- **Generate endpoint**: Returns 2 sample songs with fake task IDs
- **Credit endpoint**: Returns 999,999 fake credits
- **Get task endpoint**: Returns completed song data

### Sample Audio URLs
Dev mode uses these free audio samples:
- https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3
- https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3
- https://www.soundhelix.com/examples/mp3/SoundHelix-Song-3.mp3

### Switching to Production

To use real Suno API:
```php
// In wp-config.php
define('SUNO_DEV_MODE', false); // or remove this line
define('SUNO_API_KEY', 'your-real-api-key-here');
```

---

## 2. Music Ranking System (Bảng Xếp Hạng)

### Database Changes

Added `views` column to track play counts:

**File: `add-views-column.php`**
```sql
ALTER TABLE wp_suno_history ADD COLUMN views INT(11) DEFAULT 0 AFTER songs
```

### Backend Functions

**File: `functions.php`**

#### Track Views (lines 752-762)
```php
function miraculous_increment_song_views($song_id)
```
Increments view count by 1 when song is played.

#### Get Top Songs (lines 770-819)
```php
function miraculous_get_top_songs_by_views($limit = 10)
```
Returns top songs ordered by views (DESC).

#### AJAX Endpoint (lines 1081-1110)
```php
function miraculous_ajax_track_view()
```
Handles AJAX requests to track views.

### Frontend Integration

#### JavaScript (suno-api.js lines 291-320, 444-466)

**Track views when playing:**
```javascript
// Get song ID from data attribute
var songId = $btn.closest('[data-song-id]').data('song-id');

// Track view via AJAX
if (songId) {
    SunoAPI.trackView(songId);
}
```

**AJAX call:**
```javascript
trackView: function(songId) {
    $.ajax({
        url: miraculousAjax.ajax_url,
        type: 'POST',
        data: {
            action: 'track_view',
            nonce: miraculousAjax.nonce,
            song_id: songId
        }
    });
}
```

#### HTML Template (index.php lines 95-167)

Displays top 10 songs with:
- Gold medal (#1) - `#FFD700`
- Silver medal (#2) - `#C0C0C0`
- Bronze medal (#3) - `#CD7F32`
- Gray for others - `#8f9092`

Shows view count with eye icon:
```php
<i class="fa fa-eye"></i> <?php echo number_format($song['views']); ?> views
```

### Vietnamese Translation

**File: `languages/vi_VN.po`** (lines 260-268)
```
msgid "Bảng Xếp Hạng"
msgstr "Bảng Xếp Hạng"

msgid "views"
msgstr "lượt xem"

msgid "view all"
msgstr "xem tất cả"
```

### Testing

**File: `add-sample-views.php`**

Adds random view counts (10-1000) to all songs for testing:
```bash
php add-sample-views.php
```

This will:
1. Find all completed songs
2. Assign random view counts
3. Display top 10 ranking

---

## File Summary

### New Files Created
1. `add-views-column.php` - Database migration script
2. `add-sample-views.php` - Test data generator
3. `DEVELOPMENT-MODE-README.md` - This documentation

### Modified Files
1. `wp-config.php` - Dev mode configuration
2. `functions.php` - Mock API, view tracking functions
3. `assets/js/suno-api.js` - View tracking JavaScript
4. `index.php` - Ranking section HTML
5. `languages/vi_VN.po` - New translations
6. `languages/vi_VN.mo` - Compiled translations

---

## Usage Instructions

### For Developers

1. **Enable Dev Mode**
   ```php
   // wp-config.php
   define('SUNO_DEV_MODE', true);
   ```

2. **Add Test Data**
   ```bash
   php add-sample-music.php    # Add sample songs
   php add-sample-views.php    # Add view counts
   ```

3. **Visit Homepage**
   - See "Bảng Xếp Hạng" section
   - Click play buttons to increment views
   - Check console for view tracking logs

### For Production

1. **Disable Dev Mode**
   ```php
   define('SUNO_DEV_MODE', false);
   define('SUNO_API_KEY', 'sk-xxxxx');
   ```

2. **Views Auto-Track**
   - Every song play increments views
   - Ranking updates automatically
   - No manual intervention needed

---

## Architecture Notes

### Why Views Column in Database?
- Persistent tracking across sessions
- Fast sorting for ranking queries
- No need for external analytics

### Why AJAX for Tracking?
- Non-blocking user experience
- Works without page reload
- Fails silently if error occurs

### Why Mock at API Layer?
- Single point of interception
- No code changes needed elsewhere
- Easy to toggle on/off

---

## Future Enhancements

Potential improvements:
1. Add trending songs (views in last 7 days)
2. Add genre-specific rankings
3. Add user favorite tracking
4. Add share counts
5. Add download tracking
6. Cache ranking queries

---

## Troubleshooting

**Views not tracking?**
- Check browser console for AJAX errors
- Verify `data-song-id` attribute exists
- Check nonce is valid

**Ranking not showing?**
- Run `php add-sample-views.php`
- Check database has views > 0
- Verify songs have status='completed'

**Dev mode not working?**
- Clear WordPress cache
- Check `SUNO_DEV_MODE` is true
- Look for error logs in debug.log

---

## Credits

- **Theme**: Miraculous Music WordPress Theme
- **API**: Suno AI Music Generation
- **Sample Audio**: SoundHelix (https://www.soundhelix.com)
- **Development**: Created 2025-01-09
