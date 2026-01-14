<?php
/**
 * Genre taxonomy template - List music posts with pagination
 *
 * @package Miraculous_Music
 */
get_header();

// Get current genre term
$term = get_queried_object();
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$posts_per_page = 12;

// Query songs from custom table wp_suno_history
global $wpdb;
$table = $wpdb->prefix . 'suno_history';
$term_name = $term->name;
$offset = ($paged - 1) * $posts_per_page;

// Get total count for pagination
$total = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$table} WHERE style = %s", $term_name));

// Get songs for current page
$songs = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table} WHERE style = %s ORDER BY created_at DESC LIMIT %d OFFSET %d", $term_name, $posts_per_page, $offset), ARRAY_A);

$max_pages = $total ? ceil($total / $posts_per_page) : 1;
?>

<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="ms_weekly_wrapper ms_free_music">
                <div class="ms_title">
                    <h1><?php echo esc_html($term->name); ?></h1>
                    <?php if (!empty($term->description)) : ?>
                        <p class="term-description"><?php echo wp_kses_post($term->description); ?></p>
                    <?php endif; ?>
                </div>

                <?php if (!empty($songs)) : ?>
                    <div class="ms_weekly_inner" id="music-list-container">
                        <?php foreach ($songs as $song) :
                            $song_id = isset($song['id']) ? $song['id'] : 0;
                            $image = !empty($song['image_url']) ? $song['image_url'] : '';
                            $title = !empty($song['title']) ? $song['title'] : '';
                            $style_name = !empty($song['style']) ? $song['style'] : $term_name;
                            $duration = !empty($song['duration']) ? $song['duration'] : '';
                            $task_id = !empty($song['task_id']) ? $song['task_id'] : '';
                            $audio_url = !empty($song['audio_url']) ? $song['audio_url'] : '';
                            $video_url = !empty($song['video_url']) ? $song['video_url'] : '';
                        ?>
                        <div class="ms_weekly_box" data-song-id="<?php echo esc_attr($song_id); ?>">
                            <div class="w_top_song">
                                <div class="w_tp_song_img">
                                    <?php if ($image) : ?>
                                        <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="img-fluid">
                                    <?php else : ?>
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/weekly/song1.jpg" alt="<?php echo esc_attr($title); ?>" class="img-fluid">
                                    <?php endif; ?>
                                    <div class="ms_song_overlay"></div>
                                    <?php if (!empty($audio_url) || !empty($video_url)) : ?>
                                        <div class="ms_play_icon play-suno-song"
                                             data-audio-url="<?php echo esc_url($audio_url); ?>"
                                             data-video-url="<?php echo esc_url($video_url); ?>"
                                             data-title="<?php echo esc_attr($title); ?>"
                                             data-artist="<?php echo esc_attr(!empty($song['author_name']) ? $song['author_name'] : 'Suno AI'); ?>"
                                             data-poster="<?php echo esc_url($image); ?>">
                                            <span class="icon"></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="w_tp_song_name">
                                    <h3><?php echo esc_html($title); ?></h3>
                                    <p><?php echo esc_html($style_name ?: 'Suno AI'); ?></p>
                                    <?php if ($duration) : ?><span class="w_song_time"><?php echo esc_html($duration); ?></span><?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <nav class="pagination-nav" aria-label="Page navigation">
                        <?php
                        $base = get_pagenum_link(1) . '%_%';
                        $format = (get_option('permalink_structure')) ? 'page/%#%/' : '&paged=%#%';
                        echo paginate_links(array(
                            'base' => $base,
                            'format' => $format,
                            'total' => $max_pages,
                            'current' => $paged,
                            'mid_size' => 2,
                            'prev_text' => '&laquo; ' . esc_html__('Prev', 'miraculous-music'),
                            'next_text' => esc_html__('Next', 'miraculous-music') . ' &raquo;',
                        ));
                        ?>
                    </nav>

                <?php else : ?>
                    <div class="no-music">
                        <h3><?php esc_html_e('Không tìm thấy bài hát trong danh mục này.', 'miraculous-music'); ?></h3>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-4">
            <?php get_sidebar(); ?>
        </div>
    </div>
</div>

<?php get_footer();
