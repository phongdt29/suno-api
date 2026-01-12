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
                        <div class="suno-generator-box" style="background: #1d2025; padding: 30px; border-radius: 10px; margin-bottom: 30px;">
                            <h2 style="color: #fff; margin-bottom: 25px;"><?php esc_html_e('Generate New Music', 'miraculous-music'); ?></h2>

                            <form id="generate-music-form">
                                <!-- Music Category/Genre Selection -->
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label for="music-genre" style="color: #fff; margin-bottom: 10px; display: block;">
                                        <?php esc_html_e('Thể Loại Nhạc', 'miraculous-music'); ?> <span style="color: #14b8a6;">*</span>
                                    </label>
                                    <select id="music-genre" name="genre" class="form-control" style="background: #2a2e35; color: #fff; border: 1px solid #3d4148; padding: 12px; border-radius: 5px;" required>
                                        <option value=""><?php esc_html_e('-- Chọn thể loại --', 'miraculous-music'); ?></option>

                                        <optgroup label="<?php esc_attr_e('Nhạc Việt Nam', 'miraculous-music'); ?>">
                                            <option value="Nhạc Trẻ">Nhạc Trẻ</option>
                                            <option value="Nhạc Bolero">Nhạc Bolero</option>
                                            <option value="Nhạc Trữ Tình">Nhạc Trữ Tình</option>
                                            <option value="Nhạc Tết">Nhạc Tết</option>
                                            <option value="Nhạc Quê Hương">Nhạc Quê Hương</option>
                                            <option value="Nhạc Cách Mạng">Nhạc Cách Mạng</option>
                                            <option value="Nhạc Thiếu Nhi">Nhạc Thiếu Nhi</option>
                                            <option value="Rap Việt">Rap Việt</option>
                                            <option value="V-Pop">V-Pop</option>
                                        </optgroup>

                                        <optgroup label="<?php esc_attr_e('Nhạc Quốc Tế', 'miraculous-music'); ?>">
                                            <option value="Pop">Pop</option>
                                            <option value="Rock">Rock</option>
                                            <option value="R&B">R&B / Soul</option>
                                            <option value="Hip Hop">Hip Hop</option>
                                            <option value="EDM">EDM / Electronic</option>
                                            <option value="Jazz">Jazz</option>
                                            <option value="Blues">Blues</option>
                                            <option value="Country">Country</option>
                                            <option value="Classical">Classical</option>
                                            <option value="Reggae">Reggae</option>
                                            <option value="Metal">Metal</option>
                                            <option value="Indie">Indie</option>
                                            <option value="K-Pop">K-Pop</option>
                                            <option value="J-Pop">J-Pop</option>
                                            <option value="Latin">Latin</option>
                                        </optgroup>

                                        <optgroup label="<?php esc_attr_e('Nhạc Theo Tâm Trạng', 'miraculous-music'); ?>">
                                            <option value="Nhạc Buồn">Nhạc Buồn</option>
                                            <option value="Nhạc Vui">Nhạc Vui / Sôi Động</option>
                                            <option value="Nhạc Thư Giãn">Nhạc Thư Giãn / Chill</option>
                                            <option value="Nhạc Lãng Mạn">Nhạc Lãng Mạn</option>
                                            <option value="Nhạc Tình Yêu">Nhạc Tình Yêu</option>
                                            <option value="Nhạc Chia Tay">Nhạc Chia Tay</option>
                                        </optgroup>

                                        <optgroup label="<?php esc_attr_e('Nhạc Theo Mục Đích', 'miraculous-music'); ?>">
                                            <option value="Nhạc Tập Gym">Nhạc Tập Gym / Workout</option>
                                            <option value="Nhạc Ngủ">Nhạc Ngủ / Sleep</option>
                                            <option value="Nhạc Học Bài">Nhạc Học Bài / Study</option>
                                            <option value="Nhạc Café">Nhạc Café</option>
                                            <option value="Nhạc Thiền">Nhạc Thiền / Meditation</option>
                                            <option value="Nhạc Tiệc">Nhạc Tiệc / Party</option>
                                            <option value="Nhạc Đám Cưới">Nhạc Đám Cưới</option>
                                        </optgroup>

                                        <optgroup label="<?php esc_attr_e('Nhạc Không Lời', 'miraculous-music'); ?>">
                                            <option value="Instrumental">Instrumental</option>
                                            <option value="Piano">Piano</option>
                                            <option value="Guitar Acoustic">Guitar Acoustic</option>
                                            <option value="Lo-Fi">Lo-Fi</option>
                                            <option value="Ambient">Ambient</option>
                                            <option value="Orchestra">Orchestra</option>
                                        </optgroup>
                                    </select>
                                </div>

                                <!-- Sub-style/Mood Selection -->
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label for="music-mood" style="color: #fff; margin-bottom: 10px; display: block;">
                                        <?php esc_html_e('Phong Cách / Mood', 'miraculous-music'); ?>
                                    </label>
                                    <div class="mood-tags" style="display: flex; flex-wrap: wrap; gap: 10px;">
                                        <?php
                                        $moods = array(
                                            'upbeat' => 'Sôi Động',
                                            'slow' => 'Chậm Rãi',
                                            'emotional' => 'Xúc Động',
                                            'energetic' => 'Năng Lượng',
                                            'calm' => 'Bình Yên',
                                            'dark' => 'U Tối',
                                            'bright' => 'Tươi Sáng',
                                            'nostalgic' => 'Hoài Niệm',
                                            'romantic' => 'Lãng Mạn',
                                            'powerful' => 'Mạnh Mẽ',
                                        );
                                        foreach ($moods as $value => $label) :
                                        ?>
                                            <label class="mood-tag" style="display: inline-flex; align-items: center; background: #2a2e35; padding: 8px 15px; border-radius: 20px; cursor: pointer; transition: all 0.3s;">
                                                <input type="checkbox" name="mood[]" value="<?php echo esc_attr($value); ?>" style="display: none;">
                                                <span style="color: #aaa;"><?php echo esc_html($label); ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <!-- Music Description -->
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label for="music-prompt" style="color: #fff; margin-bottom: 10px; display: block;">
                                        <?php esc_html_e('Mô Tả Chi Tiết', 'miraculous-music'); ?>
                                    </label>
                                    <textarea id="music-prompt"
                                           name="prompt"
                                           class="form-control"
                                           rows="3"
                                           style="background: #2a2e35; color: #fff; border: 1px solid #3d4148; padding: 12px; border-radius: 5px; resize: vertical;"
                                           placeholder="<?php esc_attr_e('Ví dụ: Một bài hát về mùa xuân, tình yêu đầu đời, giai điệu nhẹ nhàng...', 'miraculous-music'); ?>"></textarea>
                                    <small style="color: #888; margin-top: 5px; display: block;">
                                        <?php esc_html_e('Mô tả thêm về nội dung, cảm xúc, hoặc chủ đề bài hát', 'miraculous-music'); ?>
                                    </small>
                                </div>

                                <div class="row">
                                    <!-- AI Model -->
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 20px;">
                                            <label for="music-model" style="color: #fff; margin-bottom: 10px; display: block;">
                                                <?php esc_html_e('AI Model', 'miraculous-music'); ?>
                                            </label>
                                            <select id="music-model" name="model" class="form-control" style="background: #2a2e35; color: #fff; border: 1px solid #3d4148; padding: 12px; border-radius: 5px;">
                                                <option value="V4">V4 (Stable)</option>
                                                <option value="V4.5" selected>V4.5 (Recommended)</option>
                                                <option value="V4.5PLUS">V4.5 PLUS</option>
                                                <option value="V5">V5 (Latest)</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Instrumental Option -->
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 20px;">
                                            <label style="color: #fff; margin-bottom: 10px; display: block;">
                                                <?php esc_html_e('Tùy Chọn', 'miraculous-music'); ?>
                                            </label>
                                            <label class="instrumental-toggle" style="display: flex; align-items: center; background: #2a2e35; padding: 12px 15px; border-radius: 5px; cursor: pointer;">
                                                <input type="checkbox" id="make-instrumental" name="make_instrumental" style="margin-right: 10px;">
                                                <span style="color: #fff;"><?php esc_html_e('Không lời (Instrumental)', 'miraculous-music'); ?></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Generated Task ID -->
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label for="generated-task-id" style="color: #fff; margin-bottom: 10px; display: block;">
                                        <?php esc_html_e('Task ID (sau khi tạo)', 'miraculous-music'); ?>
                                    </label>
                                    <input type="text"
                                           id="generated-task-id"
                                           class="form-control"
                                           readonly
                                           style="background: #2a2e35; color: #14b8a6; border: 1px solid #3d4148; padding: 12px; border-radius: 5px;"
                                           placeholder="<?php esc_attr_e('Task ID sẽ hiển thị ở đây sau khi tạo nhạc', 'miraculous-music'); ?>">
                                </div>

                                <button type="submit" class="ms_btn" style="background: linear-gradient(135deg, #14b8a6, #0d9488); border: none; padding: 15px 40px; font-size: 16px; border-radius: 25px; width: 100%;">
                                    <i class="fa fa-music" style="margin-right: 10px;"></i>
                                    <?php esc_html_e('Tạo Nhạc AI', 'miraculous-music'); ?>
                                </button>
                            </form>
                        </div>

                        <style>
                            .mood-tag:hover,
                            .mood-tag:has(input:checked) {
                                background: linear-gradient(135deg, #14b8a6, #0d9488) !important;
                            }
                            .mood-tag:has(input:checked) span,
                            .mood-tag:hover span {
                                color: #fff !important;
                            }
                            #generate-music-form .form-control:focus {
                                border-color: #14b8a6;
                                box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.2);
                                outline: none;
                            }
                            .instrumental-toggle:hover {
                                background: #3d4148 !important;
                            }
                        </style>

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
