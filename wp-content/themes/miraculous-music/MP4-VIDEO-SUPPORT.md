# MP4 Video Support in Playlist

## Overview

The theme's jPlayer playlist now supports MP4 video files alongside audio files. This allows Suno AI-generated videos to be played directly in the player.

## What Changed

### 1. jPlayer Configuration ([audio-player.js](assets/js/plugins/player/audio-player.js:103))

Updated the `supplied` formats to include video:

```javascript
supplied: "m4v, mp4, m4a, oga, mp3",
solution: "html, flash"
```

**Supported formats:**
- `mp3` - MP3 audio files
- `oga` - OGG audio files
- `m4a` - M4A audio files
- `mp4` - MP4 video files
- `m4v` - M4V video files

### 2. Global Playlist Access ([audio-player.js](assets/js/plugins/player/audio-player.js:116))

Exposed the playlist to window scope:

```javascript
// Expose myPlaylist to window scope for Suno API integration
window.myPlaylist = myPlaylist;
```

This allows other scripts (like `suno-api.js`) to add tracks dynamically.

### 3. Suno API Integration ([suno-api.js](assets/js/suno-api.js:247-286))

Updated `addToJPlayer()` function to handle both audio and video:

```javascript
addToJPlayer: function(songData) {
    if (!songData.audio_url && !songData.video_url) {
        console.warn('No audio or video URL available');
        return;
    }

    var track = {
        title: songData.title || 'Untitled',
        artist: songData.artist || 'Suno AI',
        poster: songData.image_url || '',
        image: songData.image_url || ''
    };

    // Add audio URL (mp3)
    if (songData.audio_url) {
        track.mp3 = songData.audio_url;
    }

    // Add video URL (mp4/m4v)
    if (songData.video_url) {
        var videoUrl = songData.video_url.toLowerCase();
        if (videoUrl.endsWith('.mp4') || videoUrl.indexOf('.mp4?') > -1) {
            track.mp4 = songData.video_url;
        } else if (videoUrl.endsWith('.m4v') || videoUrl.indexOf('.m4v?') > -1) {
            track.m4v = songData.video_url;
        } else {
            track.mp4 = songData.video_url; // Default to mp4
        }
    }

    window.myPlaylist.add(track);
}
```

### 4. Play Button Integration ([suno-api.js](assets/js/suno-api.js:291-337))

Updated `playSunoSong()` function to accept video URL from data attributes:

```javascript
playSunoSong: function(e) {
    var audioUrl = $btn.data('audio-url');
    var videoUrl = $btn.data('video-url');

    // Creates track with both audio and video URLs
    // Plays immediately when clicked
}
```

### 5. HTML Templates

Updated templates to include video URL data attribute:

**index.php** - Both slider and list sections:
```php
<?php if ($song['audio_url'] || $song['video_url']) : ?>
    <div class="ms_play_icon play-suno-song"
         data-audio-url="<?php echo esc_url($song['audio_url']); ?>"
         data-video-url="<?php echo esc_url($song['video_url']); ?>"
         data-title="<?php echo esc_attr($song['title']); ?>"
         data-artist="Suno AI"
         data-poster="<?php echo esc_url($song['image_url']); ?>"
         style="cursor: pointer;">
        <img src="..." alt="">
    </div>
<?php endif; ?>
```

**functions.php** - AJAX load more:
```php
<?php if ($has_audio || !empty($song['video_url'])) : ?>
    <div class="ms_play_icon play-suno-song"
         data-audio-url="<?php echo esc_url($song['audio_url']); ?>"
         data-video-url="<?php echo esc_url($song['video_url']); ?>"
         ...>
    </div>
<?php endif; ?>
```

## How It Works

### Track Object Structure

When adding a track to jPlayer, you can now include both audio and video:

```javascript
var track = {
    title: "Song Title",
    artist: "Artist Name",
    mp3: "https://example.com/song.mp3",     // Audio file
    mp4: "https://example.com/video.mp4",    // Video file
    m4v: "https://example.com/video.m4v",    // Alternative video format
    poster: "https://example.com/cover.jpg", // Cover image
    image: "https://example.com/cover.jpg"   // Same as poster
};

window.myPlaylist.add(track);
```

### Format Priority

jPlayer will automatically choose the best format based on browser support:

1. **MP4/M4V** - Played if available and browser supports HTML5 video
2. **MP3/OGA** - Falls back to audio if video not available
3. **Flash** - Legacy fallback if HTML5 not supported

### Auto-Detection

The system automatically detects video format from URL:

```javascript
// URL ending with .mp4 → sets track.mp4
// URL ending with .m4v → sets track.m4v
// URL with .mp4? (query params) → sets track.mp4
// Unknown format → defaults to track.mp4
```

## Usage Examples

### Example 1: Add Audio + Video Track

```javascript
window.myPlaylist.add({
    title: "My Suno Song",
    artist: "Suno AI",
    mp3: "https://cdn.suno.ai/song.mp3",
    mp4: "https://cdn.suno.ai/video.mp4",
    poster: "https://cdn.suno.ai/cover.jpg"
});
```

### Example 2: Add Video-Only Track

```javascript
window.myPlaylist.add({
    title: "Video Song",
    artist: "Suno AI",
    mp4: "https://cdn.suno.ai/video.mp4",
    poster: "https://cdn.suno.ai/cover.jpg"
});
```

### Example 3: Add Audio-Only Track (Legacy)

```javascript
window.myPlaylist.add({
    title: "Audio Song",
    artist: "Suno AI",
    mp3: "https://cdn.suno.ai/song.mp3",
    poster: "https://cdn.suno.ai/cover.jpg"
});
```

## Data Flow

### From Database to Player

1. **Database** - `wp_suno_history.songs` column stores JSON:
   ```json
   [{
       "audio_url": "https://...",
       "video_url": "https://...",
       "image_url": "https://..."
   }]
   ```

2. **PHP Function** - `miraculous_get_music_from_history()` extracts:
   ```php
   array(
       'audio_url' => '...',
       'video_url' => '...',
       'image_url' => '...'
   )
   ```

3. **HTML Template** - Renders data attributes:
   ```html
   <div data-audio-url="..." data-video-url="...">
   ```

4. **JavaScript** - Reads data attributes and creates track:
   ```javascript
   {mp3: '...', mp4: '...'}
   ```

5. **jPlayer** - Plays the track with best available format

## Browser Compatibility

### MP4 Video Support

| Browser | MP4 Support | Notes |
|---------|-------------|-------|
| Chrome | ✅ Yes | Full support |
| Firefox | ✅ Yes | Full support |
| Safari | ✅ Yes | Native support |
| Edge | ✅ Yes | Full support |
| IE 11 | ⚠️ Partial | Requires Flash fallback |
| Mobile Safari | ✅ Yes | iOS native support |
| Chrome Mobile | ✅ Yes | Android support |

### Fallback Behavior

1. **HTML5 Video** - Primary method (all modern browsers)
2. **Flash Player** - Legacy fallback for old browsers
3. **Direct Link** - Opens in new tab if player fails

## Testing

### Test Video Playback

1. **Check jPlayer Initialization:**
   ```javascript
   // In browser console
   console.log(typeof window.myPlaylist); // Should be "object"
   ```

2. **Add Test Track:**
   ```javascript
   window.myPlaylist.add({
       title: "Test Video",
       artist: "Test",
       mp4: "YOUR_VIDEO_URL.mp4",
       poster: "YOUR_IMAGE_URL.jpg"
   });
   ```

3. **Check Playlist:**
   ```javascript
   console.log(window.myPlaylist.playlist);
   ```

4. **Play Track:**
   ```javascript
   window.myPlaylist.play(0); // Play first track
   ```

### Debug Video Issues

If videos aren't playing:

1. **Check browser console** for errors
2. **Verify video URL** is accessible
3. **Test video format** - MP4 H.264 works best
4. **Check CORS** - video must allow cross-origin access
5. **Try direct URL** - paste video URL in browser

### Common Issues

**Issue:** Video loads but doesn't play
- **Solution:** Check video codec (H.264 recommended)
- **Solution:** Ensure HTTPS for video URLs

**Issue:** Audio plays but video doesn't show
- **Solution:** Verify `mp4` or `m4v` property is set
- **Solution:** Check if player container supports video

**Issue:** Nothing happens when clicking play
- **Solution:** Check if `window.myPlaylist` exists
- **Solution:** Verify data attributes are set correctly

## File Formats

### Recommended Formats

**Video:**
- MP4 with H.264 video codec
- AAC audio codec
- Maximum 1080p resolution
- 30fps recommended

**Audio:**
- MP3 at 128-320 kbps
- 44.1kHz or 48kHz sample rate

### Suno API Formats

Suno API typically provides:
- **audio_url**: MP3 format
- **video_url**: MP4 format (H.264/AAC)
- **image_url**: JPG/PNG cover art

All formats are optimized for web playback.

## Performance Considerations

### Bandwidth

- Video files are larger than audio (typically 10-50MB)
- Consider lazy loading videos
- Preload only current track

### Caching

- Browser caches video/audio files automatically
- CDN recommended for better performance
- Set proper cache headers on server

### Mobile

- Mobile browsers handle video well
- iOS requires user interaction to play
- Android supports autoplay (with muted video)

## Future Enhancements

Possible improvements:

- **Video Visualization** - Show video in player UI
- **Quality Selection** - Allow HD/SD video switching
- **Subtitles** - Support .vtt subtitle files
- **Live Streaming** - Support HLS/DASH formats
- **Picture-in-Picture** - Allow PiP mode
- **Download Button** - Add download option for videos
- **Fullscreen Mode** - Dedicated video player view

## Summary

The playlist now fully supports MP4 video files from Suno API:

✅ MP4/M4V video format support
✅ Automatic format detection
✅ Audio + Video in single track
✅ Browser compatibility layer
✅ Fallback to audio if video unavailable
✅ Data from `wp_suno_history` table
✅ Play buttons include video URLs
✅ Works with all existing features

Videos will play seamlessly alongside audio tracks in the jPlayer playlist!
