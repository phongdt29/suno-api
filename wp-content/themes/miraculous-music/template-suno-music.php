<?php
/**
 * Template Name: Suno Music Generator
 *
 * Template for generating and loading music from Suno API
 *
 * @package Miraculous_Music
 * @since 1.0.0
 */

get_header(); ?>

        <!----Main Content Wrapper Start---->
        <div class="ms_content_wrapper padder_top80">

            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ms_heading">
                            <h1><?php esc_html_e('Suno AI Music Generator', 'miraculous-music'); ?></h1>
                            <p><?php esc_html_e('Generate music with AI or load existing songs', 'miraculous-music'); ?></p>
                        </div>

                        <!-- Credits Display -->
                        <div class="suno-credits-box">
                            <div id="suno-credits-display">
                                <button type="button" class="ms_btn" onclick="SunoAPI.getCredits()">
                                    <?php esc_html_e('Check Credits', 'miraculous-music'); ?>
                                </button>
                            </div>
                        </div>

                        <!-- Generate Music Section -->
                        <div class="suno-generator-box" style="background: #f8f9fa; padding: 30px; border-radius: 10px; margin-bottom: 30px;">
                            <h2><?php esc_html_e('Generate New Music', 'miraculous-music'); ?></h2>

                            <form id="generate-music-form">
                                <div class="form-group">
                                    <label for="music-prompt"><?php esc_html_e('Music Description', 'miraculous-music'); ?></label>
                                    <input type="text"
                                           id="music-prompt"
                                           name="prompt"
                                           class="form-control"
                                           placeholder="<?php esc_attr_e('e.g., A happy pop song about summer', 'miraculous-music'); ?>"
                                           required>
                                    <small class="form-text text-muted">
                                        <?php esc_html_e('Describe the type of music you want to generate', 'miraculous-music'); ?>
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="music-model"><?php esc_html_e('AI Model', 'miraculous-music'); ?></label>
                                    <select id="music-model" name="model" class="form-control">
                                        <option value="V4">V4</option>
                                        <option value="V4.5">V4.5</option>
                                        <option value="V4.5PLUS">V4.5 PLUS</option>
                                        <option value="V4.5ALL">V4.5 ALL</option>
                                        <option value="V5">V5 (Latest)</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="generated-task-id"><?php esc_html_e('Generated Task ID', 'miraculous-music'); ?></label>
                                    <input type="text"
                                           id="generated-task-id"
                                           class="form-control"
                                           readonly
                                           placeholder="<?php esc_attr_e('Task ID will appear here after generation', 'miraculous-music'); ?>">
                                </div>

                                <button type="submit" class="ms_btn">
                                    <?php esc_html_e('Generate Music', 'miraculous-music'); ?>
                                </button>
                            </form>
                        </div>

                        <!-- Load Existing Song Section -->
                        <div class="suno-loader-box" style="background: #fff; padding: 30px; border-radius: 10px; margin-bottom: 30px; border: 1px solid #ddd;">
                            <h2><?php esc_html_e('Load Existing Song', 'miraculous-music'); ?></h2>

                            <div class="form-group">
                                <label for="task-id-input"><?php esc_html_e('Task ID / Song Key', 'miraculous-music'); ?></label>
                                <input type="text"
                                       id="task-id-input"
                                       class="form-control"
                                       placeholder="<?php esc_attr_e('Enter Task ID', 'miraculous-music'); ?>">
                            </div>

                            <button type="button"
                                    class="ms_btn load-song-by-key"
                                    data-task-id=""
                                    onclick="this.setAttribute('data-task-id', document.getElementById('task-id-input').value)">
                                <?php esc_html_e('Load Song', 'miraculous-music'); ?>
                            </button>
                        </div>

                        <!-- Song Display Area -->
                        <div id="suno-song-display" style="background: #fff; padding: 30px; border-radius: 10px; margin-bottom: 30px; border: 1px solid #ddd; min-height: 200px;">
                            <h3><?php esc_html_e('Song Information', 'miraculous-music'); ?></h3>
                            <p class="text-muted"><?php esc_html_e('Load or generate a song to see details here', 'miraculous-music'); ?></p>
                        </div>

                        <!-- Recent Generated Songs (from WordPress posts) -->
                        <div class="recent-generated-songs">
                            <h2><?php esc_html_e('Recent Generated Songs', 'miraculous-music'); ?></h2>

                            <?php
                            $recent_songs = new WP_Query(array(
                                'post_type' => 'music',
                                'posts_per_page' => 10,
                                'meta_query' => array(
                                    array(
                                        'key' => '_suno_task_id',
                                        'compare' => 'EXISTS'
                                    )
                                ),
                                'orderby' => 'date',
                                'order' => 'DESC'
                            ));

                            if ($recent_songs->have_posts()) : ?>
                                <div class="ms_weekly_inner">
                                    <?php while ($recent_songs->have_posts()) : $recent_songs->the_post();
                                        $task_id = get_post_meta(get_the_ID(), '_suno_task_id', true);
                                        $audio_url = get_post_meta(get_the_ID(), '_suno_audio_url', true);
                                        $artist = get_post_meta(get_the_ID(), '_music_artist', true);
                                    ?>
                                        <div class="ms_weekly_box">
                                            <div class="weekly_left">
                                                <div class="w_top_song">
                                                    <div class="w_tp_song_img">
                                                        <?php if (has_post_thumbnail()) : ?>
                                                            <?php the_post_thumbnail('thumbnail', array('class' => 'img-fluid')); ?>
                                                        <?php else : ?>
                                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/weekly/song1.jpg" alt="" class="img-fluid">
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="w_tp_song_name">
                                                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                                        <p><?php echo esc_html($artist ?: 'Suno AI'); ?></p>
                                                        <?php if ($task_id) : ?>
                                                            <small class="text-muted">Task ID: <?php echo esc_html($task_id); ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="weekly_right">
                                                <?php if ($task_id) : ?>
                                                    <button type="button"
                                                            class="ms_btn load-song-by-key"
                                                            data-task-id="<?php echo esc_attr($task_id); ?>">
                                                        <?php esc_html_e('Reload', 'miraculous-music'); ?>
                                                    </button>
                                                <?php endif; ?>

                                                <?php if ($audio_url) : ?>
                                                    <button type="button"
                                                            class="ms_btn play-suno-song"
                                                            data-audio-url="<?php echo esc_url($audio_url); ?>"
                                                            data-title="<?php echo esc_attr(get_the_title()); ?>"
                                                            data-artist="<?php echo esc_attr($artist ?: 'Suno AI'); ?>"
                                                            data-poster="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'thumbnail')); ?>">
                                                        <?php esc_html_e('Play', 'miraculous-music'); ?>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else : ?>
                                <p><?php esc_html_e('No generated songs found yet. Start generating!', 'miraculous-music'); ?></p>
                            <?php endif;
                            wp_reset_postdata();
                            ?>
                        </div>

                        <!-- API Documentation -->
                        <div class="suno-api-docs" style="margin-top: 50px; padding: 30px; background: #e9ecef; border-radius: 10px;">
                            <h2><?php esc_html_e('How to Use', 'miraculous-music'); ?></h2>

                            <ol>
                                <li><strong><?php esc_html_e('Configure API:', 'miraculous-music'); ?></strong>
                                    <?php esc_html_e('Go to WordPress Admin → Suno API → Enter your API key', 'miraculous-music'); ?>
                                </li>
                                <li><strong><?php esc_html_e('Generate Music:', 'miraculous-music'); ?></strong>
                                    <?php esc_html_e('Enter a description and click "Generate Music". Wait 20-30 seconds for completion.', 'miraculous-music'); ?>
                                </li>
                                <li><strong><?php esc_html_e('Load Song:', 'miraculous-music'); ?></strong>
                                    <?php esc_html_e('Use the Task ID to load any previously generated song', 'miraculous-music'); ?>
                                </li>
                                <li><strong><?php esc_html_e('Play Music:', 'miraculous-music'); ?></strong>
                                    <?php esc_html_e('Once loaded, the song will be added to your player', 'miraculous-music'); ?>
                                </li>
                            </ol>

                            <p>
                                <strong><?php esc_html_e('API Documentation:', 'miraculous-music'); ?></strong>
                                <a href="https://docs.sunoapi.org/" target="_blank">https://docs.sunoapi.org/</a>
                            </p>
                        </div>

                    </div>
                </div>
            </div>

        </div>
        <!----Main Content Wrapper End---->

<?php get_footer(); ?>
