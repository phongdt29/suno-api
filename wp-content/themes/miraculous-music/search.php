<?php
/**
 * Search Results Template - Search Suno Music
 *
 * @package Miraculous_Music
 * @since 1.0.0
 */

get_header();

$search_query = get_search_query();
?>

        <!----Main Content Wrapper Start---->
        <div class="ms_content_wrapper padder_top80">

            <!----Search Header---->
            <div class="ms_heading">
                <h1>
                    <?php
                    if ($search_query) {
                        printf(
                            esc_html__('Kết quả tìm kiếm cho: "%s"', 'miraculous-music'),
                            '<span class="ms_color">' . esc_html($search_query) . '</span>'
                        );
                    } else {
                        esc_html_e('Tìm kiếm nhạc', 'miraculous-music');
                    }
                    ?>
                </h1>
            </div>

            <!----Search Form---->
            <div class="ms_search_wrapper" style="margin-bottom: 40px;">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <form role="search" method="get" class="ms_search_form" action="<?php echo esc_url(home_url('/')); ?>">
                                <div class="input-group">
                                    <input type="text"
                                           class="form-control ms_search_input"
                                           name="s"
                                           id="suno-search-input"
                                           placeholder="<?php esc_attr_e('Tìm theo tiêu đề, thể loại hoặc lời bài hát...', 'miraculous-music'); ?>"
                                           value="<?php echo esc_attr($search_query); ?>"
                                           autocomplete="off">
                                    <button type="submit" class="ms_btn">
                                        <i class="fa fa-search"></i> <?php esc_html_e('Tìm kiếm', 'miraculous-music'); ?>
                                    </button>
                                </div>
                                <div class="ms_search_filters" style="margin-top: 15px;">
                                    <label class="ms_filter_label">
                                        <input type="checkbox" name="search_title" value="1" <?php checked(isset($_GET['search_title']) ? $_GET['search_title'] : '1', '1'); ?>>
                                        <?php esc_html_e('Tiêu đề', 'miraculous-music'); ?>
                                    </label>
                                    <label class="ms_filter_label">
                                        <input type="checkbox" name="search_style" value="1" <?php checked(isset($_GET['search_style']), '1'); ?>>
                                        <?php esc_html_e('Thể loại', 'miraculous-music'); ?>
                                    </label>
                                    <label class="ms_filter_label">
                                        <input type="checkbox" name="search_lyrics" value="1" <?php checked(isset($_GET['search_lyrics']), '1'); ?>>
                                        <?php esc_html_e('Lời bài hát', 'miraculous-music'); ?>
                                    </label>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!----Search Results---->
            <div id="search-results-container">
                <?php
                if ($search_query) {
                    // Search in Suno history
                    $search_results = miraculous_search_suno_music($search_query, array(
                        'search_title'  => isset($_GET['search_title']) ? true : (!isset($_GET['search_style']) && !isset($_GET['search_lyrics'])),
                        'search_style'  => isset($_GET['search_style']),
                        'search_lyrics' => isset($_GET['search_lyrics']),
                        'limit'         => 20,
                    ));

                    if (!empty($search_results)) :
                ?>
                    <div class="ms_weekly_wrapper">
                        <div class="ms_heading">
                            <h1><?php printf(esc_html__('Tìm thấy %d kết quả', 'miraculous-music'), count($search_results)); ?></h1>
                        </div>
                        <div class="ms_weekly_inner" id="music-search-results">
                            <?php
                            $counter = 1;
                            foreach ($search_results as $song) :
                                $has_audio = !empty($song['audio_url']);
                            ?>
                                <div class="ms_weekly_box" data-song-id="<?php echo esc_attr($song['id']); ?>">
                                    <div class="weekly_left">
                                        <span class="w_top_no"><?php echo $counter++; ?></span>
                                        <div class="w_top_song">
                                            <div class="w_tp_song_img">
                                                <?php if ($song['image_url']) : ?>
                                                    <img src="<?php echo esc_url($song['image_url']); ?>" alt="<?php echo esc_attr($song['title']); ?>" class="img-fluid">
                                                <?php else : ?>
                                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/weekly/song1.jpg" alt="<?php echo esc_attr($song['title']); ?>" class="img-fluid">
                                                <?php endif; ?>

                                                <?php if ($has_audio || !empty($song['video_url'])) : ?>
                                                    <div class="ms_song_overlay"></div>
                                                    <div class="ms_play_icon play-suno-song"
                                                         data-audio-url="<?php echo esc_url($song['audio_url']); ?>"
                                                         data-video-url="<?php echo esc_url($song['video_url']); ?>"
                                                         data-title="<?php echo esc_attr($song['title']); ?>"
                                                         data-artist="Suno AI"
                                                         data-poster="<?php echo esc_url($song['image_url']); ?>"
                                                         style="cursor: pointer;">
                                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/svg/play.svg" alt="">
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="w_tp_song_name">
                                                <h3><?php echo esc_html($song['title']); ?></h3>
                                                <p><?php echo esc_html($song['style'] ?: 'Suno AI'); ?></p>
                                                <?php if ($song['model']) : ?>
                                                    <small class="text-muted">Model: <?php echo esc_html($song['model']); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="weekly_right">
                                        <?php if ($song['duration']) : ?>
                                            <span class="w_song_time"><?php echo esc_html($song['duration']); ?></span>
                                        <?php endif; ?>

                                        <?php if (!empty($song['views'])) : ?>
                                            <span class="w_song_time">
                                                <i class="fa fa-eye"></i> <?php echo number_format($song['views']); ?>
                                            </span>
                                        <?php endif; ?>

                                        <span class="ms_more_icon" data-other="1">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/svg/more.svg" alt="">
                                        </span>
                                    </div>
                                    <ul class="more_option">
                                        <li><a href="#"><span class="opt_icon"><span class="icon icon_fav"></span></span><?php esc_html_e('Thêm vào yêu thích', 'miraculous-music'); ?></a></li>
                                        <li><a href="#"><span class="opt_icon"><span class="icon icon_queue"></span></span><?php esc_html_e('Thêm vào hàng đợi', 'miraculous-music'); ?></a></li>
                                        <?php if ($has_audio) : ?>
                                        <li><a href="<?php echo esc_url($song['audio_url']); ?>" download><span class="opt_icon"><span class="icon icon_dwn"></span></span><?php esc_html_e('Tải xuống', 'miraculous-music'); ?></a></li>
                                        <?php endif; ?>
                                        <li><a href="#"><span class="opt_icon"><span class="icon icon_share"></span></span><?php esc_html_e('Chia sẻ', 'miraculous-music'); ?></a></li>
                                    </ul>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php
                    else :
                ?>
                    <!----No Results Found---->
                    <div class="ms_no_results" style="text-align: center; padding: 50px 0;">
                        <div class="ms_heading">
                            <h1><?php esc_html_e('Không tìm thấy kết quả', 'miraculous-music'); ?></h1>
                            <p style="color: #888; margin-top: 15px;">
                                <?php printf(esc_html__('Không tìm thấy nhạc cho "%s". Hãy thử từ khóa hoặc bộ lọc khác.', 'miraculous-music'), esc_html($search_query)); ?>
                            </p>
                        </div>
                        <div style="margin-top: 30px;">
                            <a href="<?php echo esc_url(home_url('/suno-music-generator')); ?>" class="ms_btn">
                                <i class="fa fa-magic"></i> <?php esc_html_e('Tạo nhạc mới', 'miraculous-music'); ?>
                            </a>
                            <a href="<?php echo esc_url(home_url('/')); ?>" class="ms_btn" style="margin-left: 10px;">
                                <i class="fa fa-home"></i> <?php esc_html_e('Xem tất cả nhạc', 'miraculous-music'); ?>
                            </a>
                        </div>
                    </div>
                <?php
                    endif;
                } else {
                ?>
                    <!----Popular Searches / Recent Music---->
                    <div class="ms_popular_searches">
                        <div class="ms_heading">
                            <h1><?php esc_html_e('Thể loại phổ biến', 'miraculous-music'); ?></h1>
                        </div>
                        <div class="ms_genres_wrapper" style="margin-bottom: 40px;">
                            <div class="row">
                                <?php
                                $popular_styles = array('Pop', 'Rock', 'Jazz', 'Ballad', 'EDM', 'Hip Hop', 'R&B', 'Country', 'Classical', 'Tết', 'Bolero', 'Acoustic');
                                foreach ($popular_styles as $style) :
                                ?>
                                <div class="col-lg-2 col-md-3 col-6" style="margin-bottom: 15px;">
                                    <a href="<?php echo esc_url(add_query_arg(array('s' => $style, 'search_style' => '1'), home_url('/'))); ?>" class="ms_genres_box" style="display: block; text-align: center; padding: 20px; background: linear-gradient(135deg, #3bc8e7 0%, #14a3c7 100%); border-radius: 10px; color: #fff; text-decoration: none;">
                                        <span style="font-size: 16px; font-weight: 600;"><?php echo esc_html($style); ?></span>
                                    </a>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <?php
                        // Show recent music
                        $recent_music = miraculous_get_recent_music_from_history(8);
                        if (!empty($recent_music)) :
                        ?>
                        <div class="ms_heading">
                            <h1><?php esc_html_e('Nhạc gần đây', 'miraculous-music'); ?></h1>
                        </div>
                        <div class="ms_rcnt_slider">
                            <div class="swiper-container">
                                <div class="swiper-wrapper">
                                    <?php foreach ($recent_music as $song) : ?>
                                        <div class="swiper-slide">
                                            <div class="ms_rcnt_box">
                                                <div class="ms_rcnt_box_img">
                                                    <?php if ($song['image_url']) : ?>
                                                        <img src="<?php echo esc_url($song['image_url']); ?>" alt="<?php echo esc_attr($song['title']); ?>" class="img-fluid">
                                                    <?php else : ?>
                                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/music/r_music1.jpg" alt="<?php echo esc_attr($song['title']); ?>" class="img-fluid">
                                                    <?php endif; ?>
                                                    <div class="ms_main_overlay">
                                                        <div class="ms_box_overlay"></div>
                                                        <?php if ($song['audio_url'] || $song['video_url']) : ?>
                                                            <div class="ms_play_icon play-suno-song"
                                                                 data-audio-url="<?php echo esc_url($song['audio_url']); ?>"
                                                                 data-video-url="<?php echo esc_url($song['video_url']); ?>"
                                                                 data-title="<?php echo esc_attr($song['title']); ?>"
                                                                 data-artist="Suno AI"
                                                                 data-poster="<?php echo esc_url($song['image_url']); ?>"
                                                                 style="cursor: pointer;">
                                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/svg/play.svg" alt="">
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="ms_more_icon">
                                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/svg/more.svg" alt="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="ms_rcnt_box_text">
                                                    <h3><?php echo esc_html($song['title']); ?></h3>
                                                    <p><?php echo esc_html($song['style'] ?: 'Suno AI'); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <!-- Add Arrows -->
                            <div class="swiper-button-next slider_nav_next"></div>
                            <div class="swiper-button-prev slider_nav_prev"></div>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php
                }
                ?>
            </div>

        </div>
        <!----Main Content Wrapper End---->

<style>
.ms_search_form .input-group {
    display: flex;
    gap: 10px;
}
.ms_search_form .ms_search_input {
    flex: 1;
    height: 50px;
    border-radius: 25px;
    padding: 0 25px;
    border: 2px solid #3bc8e7;
    font-size: 16px;
    background: rgba(255,255,255,0.1);
    color: #fff;
}
.ms_search_form .ms_search_input:focus {
    outline: none;
    border-color: #14a3c7;
    box-shadow: 0 0 10px rgba(59, 200, 231, 0.3);
}
.ms_search_form .ms_search_input::placeholder {
    color: #888;
}
.ms_search_form .ms_btn {
    height: 50px;
    padding: 0 30px;
    border-radius: 25px;
}
.ms_search_filters {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
}
.ms_filter_label {
    color: #fff;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
}
.ms_filter_label input[type="checkbox"] {
    accent-color: #3bc8e7;
    width: 16px;
    height: 16px;
}
.ms_genres_box:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 20px rgba(59, 200, 231, 0.4);
    transition: all 0.3s ease;
}
</style>

<?php get_footer(); ?>
