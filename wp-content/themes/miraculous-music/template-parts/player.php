<?php
/**
 * Audio Player Template
 *
 * jPlayer audio player at the bottom of the page
 *
 * @package Miraculous_Music
 */
?>
<div class="ms_player_wrapper">
			<div class="ms_player_close">
				<i class="fa fa-angle-up" aria-hidden="true"></i>
			</div>
            <div class="player_mid">
            <div class="audio-player">
                <div id="jquery_jplayer_1" class="jp-jplayer"></div>
                <div id="jp_container_1" class="jp-audio" role="application" aria-label="media player">
                    <div class="player_left">
                        <div class="ms_play_song">
                            <div class="play_song_name">
                                <a href="javascript:void(0);" id="playlist-text">
                                    <div class="jp-now-playing flex-item">
                                        <div class="jp-track-name"></div>
                                        <div class="jp-artist-name"></div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="play_song_options">
                            <ul>
                                <li><a href="#"><span class="song_optn_icon"><i class="ms_icon icon_download"></i></span><?php esc_html_e('download now', 'miraculous-music'); ?></a></li>
                                <li><a href="#"><span class="song_optn_icon"><i class="ms_icon icon_fav"></i></span><?php esc_html_e('Add To Favourites', 'miraculous-music'); ?></a></li>
                                <li><a href="#"><span class="song_optn_icon"><i class="ms_icon icon_playlist"></i></span><?php esc_html_e('Add To Playlist', 'miraculous-music'); ?></a></li>
                                <li><a href="#"><span class="song_optn_icon"><i class="ms_icon icon_share"></i></span><?php esc_html_e('Share', 'miraculous-music'); ?></a></li>
                            </ul>
                        </div>
                        <span class="play-left-arrow"><i class="fa fa-angle-right" aria-hidden="true"></i></span>
                    </div>

                    <!----Right Queue---->
                    <div class="jp_queue_wrapper">
                        <span class="que_text" id="myPlaylistQueue"><i class="fa fa-angle-up" aria-hidden="true"></i> <?php esc_html_e('queue', 'miraculous-music'); ?></span>
                        <div id="playlist-wrap" class="jp-playlist">
                            <div class="jp_queue_cls"><i class="fa fa-times" aria-hidden="true"></i></div>
                            <h2><?php esc_html_e('queue', 'miraculous-music'); ?></h2>
                            <div class="jp_queue_list_inner">
                                <ul>
                                    <li>&nbsp;</li>
                                </ul>
                            </div>
                            <div class="jp_queue_btn">
                                <a href="javascript:;" class="ms_clear" data-toggle="modal" data-target="#clear_modal"><?php esc_html_e('clear', 'miraculous-music'); ?></a>
                                <a href="javascript:;" class="ms_save" data-toggle="modal" data-target="#save_modal"><?php esc_html_e('save', 'miraculous-music'); ?></a>
                            </div>
                        </div>
                    </div>

                    <div class="jp-type-playlist">
                        <div class="jp-gui jp-interface flex-wrap">
                            <div class="jp-controls flex-item">
                                <button class="jp-previous" tabindex="0">
                                    <i class="ms_play_control"></i>
                                </button>
                                <button class="jp-play" tabindex="0">
                                    <i class="ms_play_control"></i>
                                </button>
                                <button class="jp-next" tabindex="0">
                                    <i class="ms_play_control"></i>
                                </button>
                            </div>

                            <div class="jp-progress-container flex-item">
                                <div class="jp-time-holder">
                                    <span class="jp-current-time" role="timer" aria-label="time">&nbsp;</span>
                                    <span class="jp-duration" role="timer" aria-label="duration">&nbsp;</span>
                                </div>
                                <div class="jp-progress">
                                    <div class="jp-seek-bar">
                                        <div class="jp-play-bar">
                                            <div class="bullet"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="jp-volume-controls flex-item">
                                <div class="widget knob-container">
                                    <div class="knob-wrapper-outer">
                                        <div class="knob-wrapper">
                                            <div class="knob-mask">
                                                <div class="knob d3"><span></span></div>
                                                <div class="handle"></div>
                                                <div class="round">
                                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/svg/volume.svg" alt="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="jp-toggles flex-item">
                                <button class="jp-shuffle" tabindex="0" title="<?php esc_attr_e('Shuffle', 'miraculous-music'); ?>">
                                    <i class="ms_play_control"></i>
                                </button>
                                <button class="jp-repeat" tabindex="0" title="<?php esc_attr_e('Repeat', 'miraculous-music'); ?>">
                                    <i class="ms_play_control"></i>
                                </button>
                            </div>

                            <div class="jp_quality_optn custom_select">
                                <select>
                                    <option><?php esc_html_e('quality', 'miraculous-music'); ?></option>
                                    <option value="1">HD</option>
                                    <option value="2"><?php esc_html_e('High', 'miraculous-music'); ?></option>
                                    <option value="3"><?php esc_html_e('Medium', 'miraculous-music'); ?></option>
                                    <option value="4"><?php esc_html_e('Low', 'miraculous-music'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
 </div>

<!-- Song Display Area for Suno API -->
<div id="suno-song-display" style="display: none;"></div>

<!-- Queue Clear Modal -->
<div class="ms_clear_modal">
    <div id="clear_modal" class="modal centered-modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa_icon form_close"></i>
                </button>
                <div class="modal-body">
                    <h1><?php esc_html_e('Are you sure you want to clear your queue?', 'miraculous-music'); ?></h1>
                    <div class="clr_modal_btn">
                        <a href="#" id="confirm-clear-queue"><?php esc_html_e('clear all', 'miraculous-music'); ?></a>
                        <a href="#" data-dismiss="modal"><?php esc_html_e('cancel', 'miraculous-music'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Queue Save Modal -->
<div class="ms_save_modal">
    <div id="save_modal" class="modal centered-modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa_icon form_close"></i>
                </button>
                <div class="modal-body">
                    <h1><?php esc_html_e('Log in to start sharing your music!', 'miraculous-music'); ?></h1>
                    <div class="save_modal_btn">
                        <a href="#"><i class="fa fa-google-plus-square" aria-hidden="true"></i> <?php esc_html_e('continue with google', 'miraculous-music'); ?></a>
                        <a href="#"><i class="fa fa-facebook-square" aria-hidden="true"></i> <?php esc_html_e('continue with facebook', 'miraculous-music'); ?></a>
                    </div>
                    <h5><?php esc_html_e('or', 'miraculous-music'); ?></h5>
                    <form>
                        <input type="text" placeholder="<?php esc_attr_e('Enter your email', 'miraculous-music'); ?>">
                        <input type="password" placeholder="<?php esc_attr_e('Password', 'miraculous-music'); ?>">
                        <button type="submit" class="ms_btn"><?php esc_html_e('Submit', 'miraculous-music'); ?></button>
                    </form>
                    <div class="save_modal_bottom">
                        <span><?php esc_html_e('By continuing, you agree to our', 'miraculous-music'); ?> <a href="#"><?php esc_html_e('Terms', 'miraculous-music'); ?></a> <?php esc_html_e('and', 'miraculous-music'); ?> <a href="#"><?php esc_html_e('Privacy Policy', 'miraculous-music'); ?></a>.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
