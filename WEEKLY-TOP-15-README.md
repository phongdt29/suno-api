# Weekly Top 15 Feature Documentation

## Overview
Weekly Top 15 is a music ranking section that displays the top 15 most viewed songs in a 3-column layout, matching the original HTML theme design.

## Key Features

### 1. Three-Column Layout
- **5 songs per column** (total 15 songs)
- Responsive design using Bootstrap grid:
  - Desktop (lg): 3 columns side by side
  - Mobile (md/sm): Stacked columns
- `padding_right40` class on first 2 columns for spacing

### 2. Ranking Display
- **Two-digit ranking** format: "01", "02", "03", etc.
  - Uses `str_pad($rank, 2, '0', STR_PAD_LEFT)` for zero-padding
- **Medal colors** for top 3:
  - 🥇 #1: Gold `#FFD700`
  - 🥈 #2: Silver `#C0C0C0`
  - 🥉 #3: Bronze `#CD7F32`
  - #4-15: Gray `#8f9092`

### 3. Song Dividers
- `ms_divider` class between songs (matching HTML theme)
- **Not shown** after last song in each column
- Logic: `<?php if ($index < count($column_songs) - 1) : ?>`

### 4. View Count Display
- Shows in `w_song_time` position (replacing duration)
- Format: `<i class="fa fa-eye"></i> 992`
- Uses `number_format()` for thousands separator

### 5. Context Menu (More Options)
- `ul.more_option` dropdown menu
- Options:
  - Add To Favourites
  - Add To Queue
  - Download Now
  - Add To Playlist
  - Share
- All strings translated to Vietnamese

## File Locations

### Modified Files

**1. index.php** (lines 95-188)
```php
<!----Weekly Top 15 (Based on Views)------>
<?php
$top_songs = miraculous_get_top_songs_by_views(15);
// 3-column layout with 5 songs each
// ms_divider between songs
// Medal colors for top 3
?>
```

**2. languages/vi_VN.po** (lines 270-286)
```
msgid "weekly top 15"
msgstr "top 15 tuần"

msgid "Add To Favourites"
msgstr "Thêm vào yêu thích"
...
```

**3. languages/vi_VN.mo**
- Compiled translation file

### Supporting Files

**functions.php** (lines 770-819)
```php
function miraculous_get_top_songs_by_views($limit = 10)
```
- Already created in previous session
- Returns top songs ordered by views DESC

**assets/js/suno-api.js** (lines 444-466)
```javascript
trackView: function(songId)
```
- Tracks views when songs are played

## Layout Structure

```
ms_weekly_wrapper
  └─ ms_weekly_inner
      └─ row
          ├─ col-lg-12 (header)
          │   └─ ms_heading
          │       └─ h1: "weekly top 15"
          │
          ├─ col-lg-4 (column 1: songs 1-5)
          │   ├─ ms_weekly_box (song 1)
          │   ├─ ms_divider
          │   ├─ ms_weekly_box (song 2)
          │   ├─ ms_divider
          │   └─ ...
          │
          ├─ col-lg-4 (column 2: songs 6-10)
          │   └─ ...
          │
          └─ col-lg-4 (column 3: songs 11-15)
              └─ ...
```

## Song Box Structure

```html
<div class="ms_weekly_box" data-song-id="123">
    <div class="weekly_left">
        <span class="w_top_no" style="background: #FFD700;">01</span>
        <div class="w_top_song">
            <div class="w_tp_song_img">
                <img src="..." class="img-fluid">
                <div class="ms_song_overlay"></div>
                <div class="ms_play_icon play-suno-song" ...>
                    <img src="play.svg">
                </div>
            </div>
            <div class="w_tp_song_name">
                <h3><a href="#">Song Title</a></h3>
                <p>Artist/Style</p>
            </div>
        </div>
    </div>
    <div class="weekly_right">
        <span class="w_song_time">
            <i class="fa fa-eye"></i> 992
        </span>
        <span class="ms_more_icon" data-other="1">
            <img src="more.svg">
        </span>
    </div>
    <ul class="more_option">...</ul>
</div>
```

## Column Distribution Logic

```php
// Split 15 songs into 3 columns
$songs_per_column = 5;

for ($col = 0; $col < 3; $col++) :
    $start = $col * $songs_per_column;  // 0, 5, 10
    $column_songs = array_slice($top_songs, $start, $songs_per_column);

    foreach ($column_songs as $index => $song) :
        $current_rank = $start + $index + 1;  // 1-5, 6-10, 11-15
        // Display song
    endforeach;
endfor;
```

## Default Images

When song has no custom image:
```php
$image_num = (($current_rank - 1) % 13) + 1;  // Cycles through 1-13
$image_url = get_template_directory_uri() . "/assets/images/weekly/song{$image_num}.jpg";
```

## Testing

### Add Sample Data
```bash
# Add initial sample songs
php add-sample-music.php

# Add views to songs
php add-sample-views.php

# Add more songs to reach 15
php add-more-sample-songs.php
```

### Check Results
```bash
php -r "require 'wp-load.php'; global \$wpdb; \$table = \$wpdb->prefix . 'suno_history'; \$count = \$wpdb->get_var('SELECT COUNT(*) FROM ' . \$table . ' WHERE status=\"completed\"'); echo \"Total songs: \$count\n\";"
```

## Database Query

The section uses this query from `miraculous_get_top_songs_by_views()`:

```sql
SELECT * FROM wp_suno_history
WHERE status = 'completed'
  AND songs IS NOT NULL
  AND songs != ''
ORDER BY views DESC, created_at DESC
LIMIT 15
```

## Vietnamese Translations

| English | Vietnamese |
|---------|-----------|
| weekly top 15 | top 15 tuần |
| Add To Favourites | Thêm vào yêu thích |
| Add To Queue | Thêm vào hàng chờ |
| Download Now | Tải xuống ngay |
| Add To Playlist | Thêm vào playlist |
| Share | Chia sẻ |

## Styling Notes

### From HTML Theme
- Uses existing theme CSS classes
- No custom CSS needed
- `ms_divider` creates gray horizontal line between songs
- `padding_right40` adds spacing between columns
- Hover effects work automatically via theme CSS

### Medal Colors
Applied via inline styles on `w_top_no`:
```php
style="background: <?php
    if ($current_rank == 1) echo '#FFD700';      // Gold
    elseif ($current_rank == 2) echo '#C0C0C0';  // Silver
    elseif ($current_rank == 3) echo '#CD7F32';  // Bronze
    else echo '#8f9092';                         // Gray
?>"
```

## How Views are Tracked

1. **User plays song** → Click `.play-suno-song`
2. **JavaScript detects** → Gets `data-song-id` from parent
3. **AJAX call** → `trackView(songId)` function
4. **Backend updates** → `miraculous_increment_song_views()`
5. **Database** → `UPDATE views = views + 1`
6. **Next page load** → Updated ranking displayed

## Differences from Original "Bảng Xếp Hạng"

| Feature | Old (Bảng Xếp Hạng) | New (Weekly Top 15) |
|---------|---------------------|---------------------|
| Layout | Single column list | 3-column grid |
| Song count | 10 songs | 15 songs |
| Dividers | No dividers | ms_divider between songs |
| Time display | Duration (5:10) | View count (992) |
| Ranking format | Single digit (1) | Two digits (01) |
| More menu | No | Yes (5 options) |
| View count | In song name | In time position |

## Integration with Existing Features

### Works With
- ✅ View tracking system
- ✅ Song playback (jPlayer)
- ✅ Queue management
- ✅ Translation system
- ✅ Responsive design

### Does Not Conflict With
- ✅ Nhạc Tết section
- ✅ Nhạc Bolero section
- ✅ All Music section
- ✅ Recently Generated section

## Future Enhancements

Possible improvements:
1. **Weekly filtering** - Only count views from last 7 days
2. **Animated transitions** - When rankings change
3. **Genre-specific top 15** - Separate rankings per genre
4. **Daily/Monthly variants** - Different time periods
5. **Trending indicator** - Up/down arrows for rank changes
6. **Play count** - Separate from view count
7. **Caching** - Cache ranking query for performance

## Troubleshooting

**Section not showing?**
- Run `php add-more-sample-songs.php`
- Check database has 15+ songs with views > 0
- Verify `status = 'completed'`

**Layout broken?**
- Check Bootstrap grid classes
- Verify `padding_right40` class exists in theme CSS
- Check responsive breakpoints

**Images not loading?**
- Verify images exist: `assets/images/weekly/song1.jpg` to `song13.jpg`
- Check `get_template_directory_uri()` returns correct path

**Dividers missing?**
- Check `ms_divider` class exists in theme CSS
- Verify conditional logic: `if ($index < count($column_songs) - 1)`

**Translations not working?**
- Run `php wp-content/themes/miraculous-music/languages/compile-translations.php`
- Check `vi_VN.mo` file exists
- Verify `WPLANG` is set to 'vi' in wp-config.php

---

## Summary

The Weekly Top 15 section successfully replicates the HTML theme's ranking design:
- ✅ 3-column layout with 5 songs each
- ✅ Two-digit ranking with medal colors
- ✅ ms_divider between songs
- ✅ View count display
- ✅ Context menu with 5 options
- ✅ Fully responsive
- ✅ Vietnamese translation
- ✅ Automatic view tracking

Visit homepage to see it in action!
