<?php
/**
 * Suno Shortcodes Class
 *
 * Handles all shortcodes for frontend display
 */

if (!defined('ABSPATH')) {
    exit;
}

class Suno_Shortcodes {

    /**
     * Register shortcodes
     */
    public static function register() {
        add_shortcode('suno_generator', array(__CLASS__, 'generator_shortcode'));
        add_shortcode('suno_custom_generator', array(__CLASS__, 'custom_generator_shortcode'));
        add_shortcode('suno_auto_generator', array(__CLASS__, 'auto_generator_shortcode'));
        add_shortcode('suno_lyrics_generator', array(__CLASS__, 'lyrics_generator_shortcode'));
        add_shortcode('suno_song_list', array(__CLASS__, 'song_list_shortcode'));
    }

    /**
     * Basic generator shortcode
     * [suno_generator model="V3_5" show_instrumental="true"]
     */
    public static function generator_shortcode($atts) {
        $atts = shortcode_atts(array(
            'model' => get_option('default_model', 'V3_5'),
            'show_instrumental' => 'true',
            'show_model' => 'true',
        ), $atts);

        $models = Suno_API::get_models();
        $show_instrumental = filter_var($atts['show_instrumental'], FILTER_VALIDATE_BOOLEAN);
        $show_model = filter_var($atts['show_model'], FILTER_VALIDATE_BOOLEAN);

        ob_start();
        ?>
        <div class="suno-generator-wrap" data-type="simple">
            <form class="suno-generator-form">
                <div class="suno-form-group">
                    <label for="suno-prompt"><?php _e('Mô tả bài hát của bạn', 'suno-music-generator'); ?></label>
                    <textarea
                        id="suno-prompt"
                        name="prompt"
                        rows="4"
                        placeholder="<?php esc_attr_e('VD: A catchy pop song about summer love with upbeat melody and happy vibes', 'suno-music-generator'); ?>"
                        required
                    ></textarea>
                    <small class="suno-hint"><?php _e('Mô tả chi tiết bằng tiếng Anh để có kết quả tốt nhất', 'suno-music-generator'); ?></small>
                </div>

                <?php if ($show_instrumental) : ?>
                <div class="suno-form-group suno-checkbox-group">
                    <label>
                        <input type="checkbox" name="instrumental" value="1">
                        <?php _e('Instrumental (không có lời)', 'suno-music-generator'); ?>
                    </label>
                </div>
                <?php endif; ?>

                <?php if ($show_model) : ?>
                <div class="suno-form-group">
                    <label for="suno-model"><?php _e('Model', 'suno-music-generator'); ?></label>
                    <select id="suno-model" name="model">
                        <?php foreach ($models as $key => $label) : ?>
                            <option value="<?php echo esc_attr($key); ?>" <?php selected($atts['model'], $key); ?>>
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php else : ?>
                <input type="hidden" name="model" value="<?php echo esc_attr($atts['model']); ?>">
                <?php endif; ?>

                <div class="suno-form-group">
                    <button type="submit" class="suno-btn suno-btn-primary">
                        <span class="suno-btn-text"><?php _e('Tạo nhạc', 'suno-music-generator'); ?></span>
                        <span class="suno-btn-loading" style="display: none;">
                            <span class="suno-spinner"></span>
                            <?php _e('Đang xử lý...', 'suno-music-generator'); ?>
                        </span>
                    </button>
                </div>
            </form>

            <div class="suno-progress" style="display: none;">
                <div class="suno-progress-bar">
                    <div class="suno-progress-fill"></div>
                </div>
                <p class="suno-progress-text"><?php _e('Đang tạo nhạc...', 'suno-music-generator'); ?></p>
            </div>

            <div class="suno-results" style="display: none;"></div>
            <div class="suno-error" style="display: none;"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Custom generator shortcode
     * [suno_custom_generator]
     */
    public static function custom_generator_shortcode($atts) {
        $atts = shortcode_atts(array(
            'model' => get_option('default_model', 'V3_5'),
        ), $atts);

        $models = Suno_API::get_models();

        ob_start();
        ?>
        <div class="suno-generator-wrap" data-type="custom">
            <form class="suno-generator-form suno-custom-form">
                <div class="suno-form-row">
                    <div class="suno-form-group suno-form-half">
                        <label for="suno-title"><?php _e('Tiêu đề bài hát', 'suno-music-generator'); ?></label>
                        <input
                            type="text"
                            id="suno-title"
                            name="title"
                            placeholder="<?php esc_attr_e('VD: Summer Dreams', 'suno-music-generator'); ?>"
                        >
                    </div>

                    <div class="suno-form-group suno-form-half">
                        <label for="suno-style"><?php _e('Phong cách', 'suno-music-generator'); ?></label>
                        <input
                            type="text"
                            id="suno-style"
                            name="style"
                            placeholder="<?php esc_attr_e('VD: Pop, Ballad, Rock, R&B...', 'suno-music-generator'); ?>"
                        >
                    </div>
                </div>

                <div class="suno-form-group">
                    <label for="suno-lyrics"><?php _e('Lyrics', 'suno-music-generator'); ?></label>
                    <textarea
                        id="suno-lyrics"
                        name="lyrics"
                        rows="10"
                        placeholder="<?php esc_attr_e('[Verse 1]
Walking down the street on a sunny day
Everything feels right in every way

[Chorus]
Summer dreams, they come alive
Under the blue sky, we will thrive', 'suno-music-generator'); ?>"
                        required
                    ></textarea>
                    <small class="suno-hint"><?php _e('Thêm [Verse], [Chorus], [Bridge] để định dạng bài hát', 'suno-music-generator'); ?></small>
                </div>

                <div class="suno-form-row">
                    <div class="suno-form-group suno-form-half">
                        <label for="suno-model-custom"><?php _e('Model', 'suno-music-generator'); ?></label>
                        <select id="suno-model-custom" name="model">
                            <?php foreach ($models as $key => $label) : ?>
                                <option value="<?php echo esc_attr($key); ?>" <?php selected($atts['model'], $key); ?>>
                                    <?php echo esc_html($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="suno-form-group suno-form-half suno-checkbox-group">
                        <label>
                            <input type="checkbox" name="instrumental" value="1">
                            <?php _e('Instrumental', 'suno-music-generator'); ?>
                        </label>
                    </div>
                </div>

                <div class="suno-form-group">
                    <button type="submit" class="suno-btn suno-btn-primary">
                        <span class="suno-btn-text"><?php _e('Tạo nhạc', 'suno-music-generator'); ?></span>
                        <span class="suno-btn-loading" style="display: none;">
                            <span class="suno-spinner"></span>
                            <?php _e('Đang xử lý...', 'suno-music-generator'); ?>
                        </span>
                    </button>
                </div>
            </form>

            <div class="suno-progress" style="display: none;">
                <div class="suno-progress-bar">
                    <div class="suno-progress-fill"></div>
                </div>
                <p class="suno-progress-text"><?php _e('Đang tạo nhạc...', 'suno-music-generator'); ?></p>
            </div>

            <div class="suno-results" style="display: none;"></div>
            <div class="suno-error" style="display: none;"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Auto generator shortcode (with ChatGPT)
     * [suno_auto_generator]
     */
    public static function auto_generator_shortcode($atts) {
        $atts = shortcode_atts(array(
            'model' => get_option('default_model', 'V3_5'),
            'default_language' => 'vietnamese',
        ), $atts);

        $models = Suno_API::get_models();
        $languages = array(
            'vietnamese' => __('Tiếng Việt', 'suno-music-generator'),
            'english' => __('English', 'suno-music-generator'),
            'korean' => __('한국어', 'suno-music-generator'),
            'japanese' => __('日本語', 'suno-music-generator'),
        );

        ob_start();
        ?>
        <div class="suno-generator-wrap" data-type="auto">
            <form class="suno-generator-form suno-auto-form">
                <div class="suno-form-group">
                    <label for="suno-idea"><?php _e('Ý tưởng bài hát', 'suno-music-generator'); ?></label>
                    <textarea
                        id="suno-idea"
                        name="idea"
                        rows="4"
                        placeholder="<?php esc_attr_e('VD: Một bài hát về tình yêu đơn phương, buồn man mác, phong cách ballad', 'suno-music-generator'); ?>"
                        required
                    ></textarea>
                    <small class="suno-hint"><?php _e('AI sẽ tự động tạo lyrics và nhạc từ ý tưởng của bạn', 'suno-music-generator'); ?></small>
                </div>

                <div class="suno-form-row">
                    <div class="suno-form-group suno-form-half">
                        <label for="suno-language"><?php _e('Ngôn ngữ lyrics', 'suno-music-generator'); ?></label>
                        <select id="suno-language" name="language">
                            <?php foreach ($languages as $key => $label) : ?>
                                <option value="<?php echo esc_attr($key); ?>" <?php selected($atts['default_language'], $key); ?>>
                                    <?php echo esc_html($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="suno-form-group suno-form-half">
                        <label for="suno-model-auto"><?php _e('Model', 'suno-music-generator'); ?></label>
                        <select id="suno-model-auto" name="model">
                            <?php foreach ($models as $key => $label) : ?>
                                <option value="<?php echo esc_attr($key); ?>" <?php selected($atts['model'], $key); ?>>
                                    <?php echo esc_html($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="suno-form-group">
                    <button type="submit" class="suno-btn suno-btn-primary">
                        <span class="suno-btn-text"><?php _e('Tạo nhạc tự động', 'suno-music-generator'); ?></span>
                        <span class="suno-btn-loading" style="display: none;">
                            <span class="suno-spinner"></span>
                            <?php _e('AI đang sáng tác...', 'suno-music-generator'); ?>
                        </span>
                    </button>
                </div>
            </form>

            <div class="suno-gpt-result" style="display: none;">
                <h4><?php _e('Nội dung AI tạo:', 'suno-music-generator'); ?></h4>
                <div class="suno-gpt-content"></div>
            </div>

            <div class="suno-progress" style="display: none;">
                <div class="suno-progress-bar">
                    <div class="suno-progress-fill"></div>
                </div>
                <p class="suno-progress-text"><?php _e('Đang tạo nhạc...', 'suno-music-generator'); ?></p>
            </div>

            <div class="suno-results" style="display: none;"></div>
            <div class="suno-error" style="display: none;"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Lyrics generator shortcode
     * [suno_lyrics_generator]
     */
    public static function lyrics_generator_shortcode($atts) {
        ob_start();
        ?>
        <div class="suno-generator-wrap" data-type="lyrics">
            <form class="suno-generator-form suno-lyrics-form">
                <div class="suno-form-group">
                    <label for="suno-lyrics-prompt"><?php _e('Mô tả lyrics bạn muốn tạo', 'suno-music-generator'); ?></label>
                    <textarea
                        id="suno-lyrics-prompt"
                        name="prompt"
                        rows="4"
                        placeholder="<?php esc_attr_e('VD: A love song about missing someone, with emotional verses and a powerful chorus', 'suno-music-generator'); ?>"
                        required
                    ></textarea>
                </div>

                <div class="suno-form-group">
                    <button type="submit" class="suno-btn suno-btn-primary">
                        <span class="suno-btn-text"><?php _e('Tạo Lyrics', 'suno-music-generator'); ?></span>
                        <span class="suno-btn-loading" style="display: none;">
                            <span class="suno-spinner"></span>
                            <?php _e('Đang tạo...', 'suno-music-generator'); ?>
                        </span>
                    </button>
                </div>
            </form>

            <div class="suno-progress" style="display: none;">
                <div class="suno-progress-bar">
                    <div class="suno-progress-fill"></div>
                </div>
                <p class="suno-progress-text"><?php _e('Đang tạo lyrics...', 'suno-music-generator'); ?></p>
            </div>

            <div class="suno-lyrics-result" style="display: none;">
                <h4 class="suno-lyrics-title"></h4>
                <pre class="suno-lyrics-content"></pre>
                <button type="button" class="suno-btn suno-btn-secondary suno-copy-lyrics">
                    <?php _e('Sao chép lyrics', 'suno-music-generator'); ?>
                </button>
            </div>

            <div class="suno-error" style="display: none;"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Song list shortcode
     * [suno_song_list limit="10" columns="2"]
     */
    public static function song_list_shortcode($atts) {
        $atts = shortcode_atts(array(
            'limit' => 10,
            'user' => 'all',
            'columns' => 2,
        ), $atts);

        global $wpdb;
        $table_name = $wpdb->prefix . 'suno_history';
        $limit = intval($atts['limit']);
        $columns = intval($atts['columns']);

        // Build query
        if ($atts['user'] === 'current' && is_user_logged_in()) {
            $user_id = get_current_user_id();
            $query = $wpdb->prepare(
                "SELECT * FROM $table_name WHERE status = 'completed' AND songs IS NOT NULL AND songs != '' AND user_id = %d ORDER BY created_at DESC LIMIT %d",
                $user_id,
                $limit
            );
        } else {
            $query = $wpdb->prepare(
                "SELECT * FROM $table_name WHERE status = 'completed' AND songs IS NOT NULL AND songs != '' ORDER BY created_at DESC LIMIT %d",
                $limit
            );
        }

        $items = $wpdb->get_results($query);

        ob_start();
        ?>
        <div class="suno-song-list-wrap">
            <?php if (empty($items)) : ?>
                <p class="suno-no-songs"><?php _e('Chưa có bài hát nào.', 'suno-music-generator'); ?></p>
            <?php else : ?>
                <div class="suno-song-grid" style="display:grid;grid-template-columns:repeat(<?php echo $columns; ?>,1fr);gap:20px;">
                    <?php foreach ($items as $item) :
                        $songs = json_decode($item->songs, true);
                        if (empty($songs)) continue;
                        foreach ($songs as $song) :
                    ?>
                        <div class="suno-song-card">
                            <?php if (!empty($song['image_url'])) : ?>
                                <div class="suno-cover-wrap"><img src="<?php echo esc_url($song['image_url']); ?>" alt="<?php echo esc_attr($song['title']); ?>" class="suno-cover-img"></div>
                            <?php endif; ?>
                            <div class="suno-song-info-box">
                                <h4 class="suno-song-title-text"><?php echo esc_html($song['title'] ?: 'Untitled'); ?></h4>
                                <?php if (!empty($song['style'])) : ?><p class="suno-style-text"><?php echo esc_html($song['style']); ?></p><?php endif; ?>
                                <?php if (!empty($song['audio_url'])) : ?>
                                    <audio controls preload="metadata" style="width:100%;margin:10px 0;"><source src="<?php echo esc_url($song['audio_url']); ?>" type="audio/mpeg"></audio>
                                <?php endif; ?>
                                <div class="suno-btns">
                                    <?php if (!empty($song['audio_url'])) : ?><a href="<?php echo esc_url($song['audio_url']); ?>" download class="suno-btn suno-btn-secondary" style="padding:8px 12px;font-size:12px;">Tải MP3</a><?php endif; ?>
                                    <?php if (!empty($song['video_url'])) : ?><a href="<?php echo esc_url($song['video_url']); ?>" download class="suno-btn suno-btn-secondary" style="padding:8px 12px;font-size:12px;">Tải Video</a><?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <style>
        .suno-song-card{background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.1);transition:.2s}
        .suno-song-card:hover{transform:translateY(-4px);box-shadow:0 8px 24px rgba(0,0,0,.15)}
        .suno-cover-wrap{position:relative;padding-top:100%;overflow:hidden}
        .suno-cover-img{position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover}
        .suno-song-info-box{padding:16px}
        .suno-song-title-text{margin:0 0 8px;font-size:16px;font-weight:600;color:#1e293b}
        .suno-style-text{margin:0 0 8px;font-size:13px;color:#64748b}
        .suno-btns{display:flex;gap:8px;flex-wrap:wrap}
        .suno-no-songs{text-align:center;padding:40px;color:#64748b}
        @media(max-width:768px){.suno-song-grid{grid-template-columns:1fr!important}}
        </style>
        <?php
        return ob_get_clean();
    }
}
