<?php
/**
 * The main template file
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
            $banner_title = get_theme_mod('banner_title', 'Listen Millions of songs for Free!');
            $banner_desc = get_theme_mod('banner_description', 'Nowhere else provides the most listening services than here. Enjoy Your day!');
            $banner_bg = get_theme_mod('banner_background', get_template_directory_uri() . '/assets/images/banner/img.png');
            ?>
            <div class="ms_banner_wrapper">
                <div class="ms_banner_text">
                    <h1><?php echo esc_html($banner_title); ?></h1>
                    <p><?php echo esc_html($banner_desc); ?></p>
                    <div class="ms_banner_btn">
                        <a href="#" class="ms_btn"><?php esc_html_e('listen now', 'miraculous-music'); ?></a>
                        <a href="#" class="ms_btn ms_btn_h"><?php esc_html_e('add to queue', 'miraculous-music'); ?></a>
                    </div>
                </div>
                <div class="ms_banner_img">
                    <img src="<?php echo esc_url($banner_bg); ?>" alt="" class="img-fluid">
                </div>
                <div class="banner_shape_img">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/shape.png" alt="" class="img-fluid">
                </div>
            </div>

            <?php if (have_posts()) : ?>

                <!----Recently Played Songs Section Start---->
                <div class="ms_rcnt_slider">
                    <div class="ms_heading">
                        <h1><?php esc_html_e('recently played', 'miraculous-music'); ?></h1>
                        <span class="veiw_all"><a href="<?php echo esc_url(home_url('/music')); ?>"><?php esc_html_e('view more', 'miraculous-music'); ?></a></span>
                    </div>
                    <div class="swiper-container">
                        <div class="swiper-wrapper">
                            <?php
                            // Query recent music posts
                            $recent_music = new WP_Query(array(
                                'post_type' => 'music',
                                'posts_per_page' => 6,
                                'orderby' => 'date',
                                'order' => 'DESC'
                            ));

                            if ($recent_music->have_posts()) :
                                while ($recent_music->have_posts()) : $recent_music->the_post();
                            ?>
                                <div class="swiper-slide">
                                    <div class="ms_rcnt_box">
                                        <div class="ms_rcnt_box_img">
                                            <?php if (has_post_thumbnail()) : ?>
                                                <a href="<?php the_permalink(); ?>">
                                                    <?php the_post_thumbnail('medium', array('class' => 'img-fluid')); ?>
                                                </a>
                                            <?php else : ?>
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/music/r_music1.jpg" alt="" class="img-fluid">
                                            <?php endif; ?>
                                            <div class="ms_main_overlay">
                                                <div class="ms_box_overlay"></div>
                                                <div class="ms_more_icon">
                                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/svg/more.svg" alt="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ms_rcnt_box_text">
                                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                            <p><?php the_excerpt(); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php
                                endwhile;
                                wp_reset_postdata();
                            endif;
                            ?>
                        </div>
                    </div>
                    <!-- Add Arrows -->
                    <div class="swiper-button-next5 slider_nav_next"></div>
                    <div class="swiper-button-prev5 slider_nav_prev"></div>
                </div>

                <!----Weekly Top 15 Section Start---->
                <div class="ms_weekly_wrapper">
                    <div class="ms_heading">
                        <h1><?php esc_html_e('weekly top 15', 'miraculous-music'); ?></h1>
                        <span class="veiw_all"><a href="#"><?php esc_html_e('view more', 'miraculous-music'); ?></a></span>
                    </div>
                    <div class="ms_weekly_inner">
                        <?php
                        // Query top music
                        $top_music = new WP_Query(array(
                            'post_type' => 'music',
                            'posts_per_page' => 15,
                            'meta_key' => '_music_plays',
                            'orderby' => 'meta_value_num',
                            'order' => 'DESC'
                        ));

                        if ($top_music->have_posts()) :
                            $counter = 1;
                            while ($top_music->have_posts()) : $top_music->the_post();
                        ?>
                            <div class="ms_weekly_box">
                                <div class="weekly_left">
                                    <span class="w_top_no"><?php echo $counter++; ?></span>
                                    <div class="w_top_song">
                                        <div class="w_tp_song_img">
                                            <?php if (has_post_thumbnail()) : ?>
                                                <?php the_post_thumbnail('thumbnail', array('class' => 'img-fluid')); ?>
                                            <?php else : ?>
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/weekly/song1.jpg" alt="" class="img-fluid">
                                            <?php endif; ?>
                                            <div class="ms_song_overlay"></div>
                                            <div class="ms_play_icon">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/svg/play.svg" alt="">
                                            </div>
                                        </div>
                                        <div class="w_tp_song_name">
                                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                            <p><?php echo get_post_meta(get_the_ID(), '_music_artist', true); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="weekly_right">
                                    <span class="w_song_time"><?php echo get_post_meta(get_the_ID(), '_music_duration', true) ?: '3:22'; ?></span>
                                    <span class="ms_more_icon" data-other="1">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/svg/more.svg" alt="">
                                    </span>
                                </div>
                            </div>
                        <?php
                            endwhile;
                            wp_reset_postdata();
                        endif;
                        ?>
                    </div>
                </div>

            <?php else : ?>
                <div class="ms_heading">
                    <h1><?php esc_html_e('No content found', 'miraculous-music'); ?></h1>
                    <p><?php esc_html_e('It seems we can\'t find what you\'re looking for.', 'miraculous-music'); ?></p>
                </div>
            <?php endif; ?>

        </div>
        <!----Main Content Wrapper End---->

<?php get_footer(); ?>
