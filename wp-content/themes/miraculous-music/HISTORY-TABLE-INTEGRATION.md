# wp_suno_history Table Integration

## Overview

The homepage now retrieves music directly from the `wp_suno_history` database table instead of WordPress posts. This provides better performance and direct access to Suno API generated songs.

## Table Structure

```sql
CREATE TABLE wp_suno_history (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    user_id bigint(20) NOT NULL DEFAULT 0,
    task_id varchar(100) NOT NULL,
    prompt text,
    lyrics text,
    title varchar(255),
    style varchar(255),
    model varchar(20),
    status varchar(50) DEFAULT 'pending',
    songs longtext,  -- JSON array of song objects
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY task_id (task_id),
    KEY user_id (user_id),
    KEY status (status)
)
```

## New Functions Added

### 1. miraculous_get_music_from_history()

Retrieves music from the history table with flexible filtering options.

```php
$music = miraculous_get_music_from_history(array(
    'limit' => 12,           // Number of songs to retrieve
    'offset' => 0,           // Starting position
    'status' => 'completed', // Filter by status (completed, pending, failed, all)
    'user_id' => 0,          // Filter by user (0 = all users)
    'order' => 'DESC',       // Sort order
    'orderby' => 'created_at' // Sort field
));
```

**Returns:** Array of songs with the following structure:
```php
array(
    'id' => 123,                    // History record ID
    'task_id' => 'abc-123',         // Suno API task ID
    'title' => 'Song Title',
    'audio_url' => 'https://...',
    'image_url' => 'https://...',
    'video_url' => 'https://...',
    'duration' => '3:22',
    'prompt' => 'A happy pop song',
    'lyrics' => 'Full lyrics...',
    'style' => 'Pop',
    'model' => 'V5',
    'status' => 'completed',
    'created_at' => '2024-01-09 10:30:00',
    'song_id' => 'song-xyz'         // Individual song ID from Suno
)
```

### 2. miraculous_get_recent_music_from_history()

Quick function to get recent completed songs.

```php
$recent = miraculous_get_recent_music_from_history(6);
```

### 3. miraculous_get_history_count()

Get total count of records in history table.

```php
$total = miraculous_get_history_count(array(
    'status' => 'completed',
    'user_id' => 0
));
```

## Updated Files

### index.php

The homepage now uses:
- `miraculous_get_recent_music_from_history(6)` - For "Recently Generated" slider
- `miraculous_get_music_from_history(array('limit' => 12))` - For "All Music" list
- `miraculous_get_history_count()` - For pagination calculation

### functions.php

Added three new functions:
- Line 496-590: `miraculous_get_music_from_history()`
- Line 598-605: `miraculous_get_recent_music_from_history()`
- Line 613-638: `miraculous_get_history_count()`

Updated AJAX handler:
- Line 745-831: `miraculous_ajax_load_more_music()` now queries history table

## How Songs are Stored

The `songs` column stores JSON data from Suno API. Each history record can contain multiple songs (Suno API typically returns 2 songs per generation):

```json
[
    {
        "id": "song-123",
        "title": "Summer Vibes",
        "audio_url": "https://cdn.suno.ai/...",
        "image_url": "https://cdn.suno.ai/...",
        "video_url": "https://cdn.suno.ai/...",
        "duration": "3:22"
    },
    {
        "id": "song-124",
        "title": "Summer Vibes (Version 2)",
        "audio_url": "https://cdn.suno.ai/...",
        "image_url": "https://cdn.suno.ai/...",
        "video_url": "https://cdn.suno.ai/...",
        "duration": "3:18"
    }
]
```

The `miraculous_get_music_from_history()` function automatically expands this array, so each song becomes a separate item in the returned list.

## Benefits

1. **Performance**: Direct database queries are faster than WordPress post queries
2. **Flexibility**: Easy filtering by status, user, date, model, etc.
3. **Rich Metadata**: Access to prompt, lyrics, style, model information
4. **Real-time Updates**: Songs are automatically saved to history when generated
5. **No Post Creation**: Doesn't clutter WordPress posts table

## Usage Examples

### Get all completed songs
```php
$songs = miraculous_get_music_from_history(array(
    'limit' => 100,
    'status' => 'completed'
));
```

### Get songs by specific user
```php
$user_songs = miraculous_get_music_from_history(array(
    'limit' => 20,
    'user_id' => get_current_user_id()
));
```

### Get songs by model
```php
global $wpdb;
$v5_songs = $wpdb->get_results(
    "SELECT * FROM {$wpdb->prefix}suno_history
     WHERE status = 'completed' AND model = 'V5'
     ORDER BY created_at DESC
     LIMIT 10"
);
```

### Pagination Example
```php
$page = 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

$songs = miraculous_get_music_from_history(array(
    'limit' => $per_page,
    'offset' => $offset
));

$total = miraculous_get_history_count();
$max_pages = ceil($total / $per_page);
```

## Testing

To verify the integration is working:

1. Generate music using the Suno API plugin
2. Check the homepage - new songs should appear in both sections
3. Click play buttons to test audio playback
4. Use "Load More" button to test pagination
5. Check browser console for any JavaScript errors

## Troubleshooting

### No music showing on homepage

Check if table has data:
```sql
SELECT * FROM wp_suno_history WHERE status = 'completed' LIMIT 10;
```

### Enable debugging
```php
// In wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

### Check error logs
```bash
tail -f wp-content/debug.log | grep "suno"
```

## Future Enhancements

Possible additions:
- Search functionality by title, style, or lyrics
- Favorite/like system
- User playlists from history
- Export history to CSV
- Advanced filters (by date range, model, style)
- Statistics dashboard (most popular models, styles, etc.)
