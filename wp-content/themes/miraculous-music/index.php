<?php
/**
 * The main template file - Homepage with Suno API Music
 *
 * @package Miraculous_Music
 * @since 1.0.0
 */

get_header(); ?>

        <!----Main Content Wrapper Start---->
        <div class="ms_content_wrapper padder_top80">
            <!---Banner Start--->
            <?php
            // Get banner settings from customizer
            $banner_title = get_theme_mod('banner_title', 'Nghe hàng triệu bài hát miễn phí!');
            $banner_desc = get_theme_mod('banner_description', 'Không nơi nào cung cấp dịch vụ nghe nhạc tốt hơn ở đây. Chúc bạn một ngày vui vẻ!');
            $banner_bg = get_theme_mod('banner_background', get_template_directory_uri() . '/assets/images/banner.png');
            ?>
            <div class="ms-banner">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="ms_banner_img">
                                <img src="<?php echo esc_url($banner_bg); ?>" alt="" class="img-fluid">
                            </div>
                            <div class="ms_banner_text">
                                <h1><?php echo esc_html($banner_title); ?></h1>
                                <p><?php echo esc_html($banner_desc); ?></p>
                                <div class="ms_banner_btn">
                                    <a href="#music-list" class="ms_btn"><?php esc_html_e('Nghe ngay', 'miraculous-music'); ?></a>
                                    <a href="<?php echo esc_url(home_url('/suno-music-generator')); ?>" class="ms_btn"><?php esc_html_e('Tạo nhạc AI', 'miraculous-music'); ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!----Recently Generated from Suno API---->
            <?php
            // Get recent music from wp_suno_history table
            $recent_suno_history = miraculous_get_recent_music_from_history(6);
            if (!empty($recent_suno_history)) :
            ?>
                
                <div class="ms_rcnt_slider">
                    <div class="ms_heading">
                        <h1><?php esc_html_e('Nhạc mới tạo từ Suno AI', 'miraculous-music'); ?></h1>
                        <span class="veiw_all"><a href="<?php echo esc_url(home_url('/music')); ?>"><?php esc_html_e('xem thêm', 'miraculous-music'); ?></a></span>
                    </div>
                    <div class="swiper-container">
                        <div class="swiper-wrapper">
                            <?php foreach ($recent_suno_history as $song) : ?>
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
                                            <?php if ($song['model']) : ?>
                                                <small class="text-muted">Model: <?php echo esc_html($song['model']); ?></small>
                                            <?php endif; ?>
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

            <!----Weekly Top 15 (Based on Views)------>
            <?php
            $top_songs = miraculous_get_top_songs_by_views(15);
            if (!empty($top_songs)) :
            ?>
                <div class="ms_weekly_wrapper">
                    <div class="ms_weekly_inner">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="ms_heading">
                                    <h1><?php esc_html_e('Top 15 tuần', 'miraculous-music'); ?></h1>
                                </div>
                            </div>

                            <?php
                            // Split songs into 3 columns (5 songs each)
                            $songs_per_column = 5;
                            $total_songs = count($top_songs);

                            for ($col = 0; $col < 3; $col++) :
                                $start = $col * $songs_per_column;
                                $column_songs = array_slice($top_songs, $start, $songs_per_column);

                                if (empty($column_songs)) continue;
                            ?>
                                <div class="col-lg-4 col-md-12 <?php echo ($col < 2) ? 'padding_right40' : ''; ?>">
                                    <?php
                                    foreach ($column_songs as $index => $song) :
                                        $has_audio = !empty($song['audio_url']);
                                        $current_rank = $start + $index + 1;
                                    ?>
                                        <div class="ms_weekly_box" data-song-id="<?php echo esc_attr($song['id']); ?>">
                                            <div class="weekly_left">
                                                <span class="w_top_no" style="background: <?php
                                                    if ($current_rank == 1) echo '#FFD700'; // Gold
                                                    elseif ($current_rank == 2) echo '#C0C0C0'; // Silver
                                                    elseif ($current_rank == 3) echo '#CD7F32'; // Bronze
                                                    else echo '#8f9092';
                                                ?>">
                                                    <?php echo str_pad($current_rank, 2, '0', STR_PAD_LEFT); ?>
                                                </span>
                                                <div class="w_top_song">
                                                    <div class="w_tp_song_img">
                                                        <?php if ($song['image_url']) : ?>
                                                            <img src="<?php echo esc_url($song['image_url']); ?>" alt="<?php echo esc_attr($song['title']); ?>" class="img-fluid">
                                                        <?php else : ?>
                                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/weekly/song<?php echo (($current_rank - 1) % 13) + 1; ?>.jpg" alt="<?php echo esc_attr($song['title']); ?>" class="img-fluid">
                                                        <?php endif; ?>

                                                        <div class="ms_song_overlay"></div>
                                                        <?php if ($has_audio || !empty($song['video_url'])) : ?>
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
                                                        <h3><a href="#"><?php echo esc_html($song['title']); ?></a></h3>
                                                        <p><?php echo esc_html($song['style'] ?: 'Suno AI'); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="weekly_right">
                                                <span class="w_song_time">
                                                    <i class="fa fa-eye"></i> <?php echo number_format($song['views']); ?>
                                                </span>
                                                <span class="ms_more_icon" data-other="1">
                                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/svg/more.svg" alt="">
                                                </span>
                                            </div>
                                            <ul class="more_option">
                                                <li><a href="#"><span class="opt_icon"><span class="icon icon_fav"></span></span><?php esc_html_e('Thêm vào yêu thích', 'miraculous-music'); ?></a></li>
                                                <li><a href="#"><span class="opt_icon"><span class="icon icon_queue"></span></span><?php esc_html_e('Thêm vào hàng đợi', 'miraculous-music'); ?></a></li>
                                                <li><a href="#"><span class="opt_icon"><span class="icon icon_dwn"></span></span><?php esc_html_e('Tải xuống', 'miraculous-music'); ?></a></li>
                                                <li><a href="#"><span class="opt_icon"><span class="icon icon_playlst"></span></span><?php esc_html_e('Thêm vào Playlist', 'miraculous-music'); ?></a></li>
                                                <li><a href="#"><span class="opt_icon"><span class="icon icon_share"></span></span><?php esc_html_e('Chia sẻ', 'miraculous-music'); ?></a></li>
                                            </ul>
                                        </div>
                                        <?php if ($index < count($column_songs) - 1) : ?>
                                            <div class="ms_divider"></div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!----Advertisement Section---->
            <?php
            $adv_image = get_theme_mod('adv_banner_image', get_template_directory_uri() . '/assets/images/adv.jpg');
            $adv_link = get_theme_mod('adv_banner_link', '#');
            $show_adv = get_theme_mod('show_adv_banner', true);
            if ($show_adv && $adv_image) :
            ?>
            <div class="ms_advr_wrapper">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <a href="<?php echo esc_url($adv_link); ?>" target="_blank">
                                <img src="<?php echo esc_url($adv_image); ?>" alt="<?php esc_attr_e('Advertisement', 'miraculous-music'); ?>" class="img-fluid">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!----Nhạc Tết Section---->
            <?php
            $nhac_tet = miraculous_get_music_by_style('Tết', 6);
            if (!empty($nhac_tet)) :
            ?>
                <div class="ms_rcnt_slider">
                    <div class="ms_heading">
                        <h1><?php esc_html_e('Nhạc Tết', 'miraculous-music'); ?></h1>
                        <span class="veiw_all"><a href="?genre=tet"><?php esc_html_e('xem thêm', 'miraculous-music'); ?></a></span>
                    </div>
                    <div class="swiper-container">
                        <div class="swiper-wrapper">
                            <?php foreach ($nhac_tet as $song) : ?>
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
                                            <p><?php echo esc_html($song['style'] ?: 'Nhạc Tết'); ?></p>
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

            <!----Nhạc Bolero Section---->
            <?php
            $nhac_bolero = miraculous_get_music_by_style('Bolero', 6);
            if (!empty($nhac_bolero)) :
            ?>
                <div class="ms_rcnt_slider">
                    <div class="ms_heading">
                        <h1><?php esc_html_e('Nhạc Bolero', 'miraculous-music'); ?></h1>
                        <span class="veiw_all"><a href="?genre=bolero"><?php esc_html_e('xem thêm', 'miraculous-music'); ?></a></span>
                    </div>
                    <div class="swiper-container">
                        <div class="swiper-wrapper">
                            <?php foreach ($nhac_bolero as $song) : ?>
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
                                            <p><?php echo esc_html($song['style'] ?: 'Nhạc Bolero'); ?></p>
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

            <!----Advertisement Section 2---->
            <?php
            $adv2_image = get_theme_mod('adv_banner_image_2', get_template_directory_uri() . '/assets/images/adv1.jpg');
            $adv2_link = get_theme_mod('adv_banner_link_2', '#');
            $show_adv2 = get_theme_mod('show_adv_banner_2', true);
            if ($show_adv2 && $adv2_image) :
            ?>
            <div class="ms_advr_wrapper ms_advr2">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <a href="<?php echo esc_url($adv2_link); ?>" target="_blank">
                                <img src="<?php echo esc_url($adv2_image); ?>" alt="<?php esc_attr_e('Advertisement', 'miraculous-music'); ?>" class="img-fluid">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!----All Music from Suno API---->
            <?php
            // Get all music from wp_suno_history table
            $all_music_history = miraculous_get_music_from_history(array('limit' => 12));
            $total_count = miraculous_get_history_count();
            $max_pages = ceil($total_count / 12);

            if (!empty($all_music_history)) :
            ?>
                <div id="music-list" class="ms_weekly_wrapper">
                    <div class="ms_heading">
                        <h1><?php esc_html_e('Tất cả bài hát', 'miraculous-music'); ?></h1>
                        <span class="veiw_all">
                            <a href="<?php echo esc_url(home_url('/music')); ?>"><?php esc_html_e('xem tất cả', 'miraculous-music'); ?></a>
                        </span>
                    </div>
                    <div class="ms_weekly_inner" id="music-list-container">
                        <?php
                        $counter = 1;
                        foreach ($all_music_history as $song) :
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
                                            <?php if ($song['task_id']) : ?>
                                                <small class="text-muted" style="font-size: 10px;">ID: <?php echo esc_html(substr($song['task_id'], 0, 8)); ?>...</small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="weekly_right">
                                    <?php if ($song['duration']) : ?>
                                        <span class="w_song_time"><?php echo esc_html($song['duration']); ?></span>
                                    <?php endif; ?>

                                    <?php if ($song['task_id'] && !$has_audio) : ?>
                                        <button type="button"
                                                class="ms_btn load-song-by-key"
                                                data-task-id="<?php echo esc_attr($song['task_id']); ?>"
                                                style="font-size: 12px; padding: 5px 10px;">
                                            <?php esc_html_e('Tải', 'miraculous-music'); ?>
                                        </button>
                                    <?php endif; ?>

                                    <span class="ms_more_icon" data-other="1">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/svg/more.svg" alt="">
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($max_pages > 1) : ?>
                        <div class="load-more-wrapper">
                            <button type="button"
                                    id="load-more-music"
                                    class="ms_btn"
                                    data-page="1"
                                    data-max-pages="<?php echo esc_attr($max_pages); ?>">
                                <?php esc_html_e('Tải thêm nhạc', 'miraculous-music'); ?>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else : ?>
                <!----No Music Found---->
                <div class="ms_heading" style="text-align: center; padding: 50px 0;">
                    <h1><?php esc_html_e('Không tìm thấy nhạc', 'miraculous-music'); ?></h1>
                    <p><?php esc_html_e('Bắt đầu tạo nhạc với Suno AI!', 'miraculous-music'); ?></p>
                    <a href="<?php echo esc_url(home_url('/suno-music-generator')); ?>" class="ms_btn">
                        <?php esc_html_e('Tạo nhạc', 'miraculous-music'); ?>
                    </a>
                </div>
            <?php endif; ?>

        </div>
        <!----Main Content Wrapper End---->

<?php get_footer(); ?>
