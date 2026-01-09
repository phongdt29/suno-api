/**
 * Suno API Integration JavaScript
 *
 * @package Miraculous_Music
 */

(function($) {
    'use strict';

    var SunoAPI = {
        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
        },

        /**
         * Bind events
         */
        bindEvents: function() {
            // Get song by task ID
            $(document).on('click', '.load-song-by-key', this.loadSongByKey);

            // Generate music
            $(document).on('submit', '#generate-music-form', this.generateMusic);

            // Load song into player
            $(document).on('click', '.play-suno-song', this.playSunoSong);

            // Load more music
            $(document).on('click', '#load-more-music', this.loadMoreMusic);
        },

        /**
         * Load song by task ID
         */
        loadSongByKey: function(e) {
            e.preventDefault();

            var $btn = $(this);
            var taskId = $btn.data('task-id');

            if (!taskId) {
                alert('Vui lòng nhập Task ID');
                return;
            }

            $btn.prop('disabled', true).text('Loading...');

            $.ajax({
                url: miraculousAjax.ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'get_song',
                    nonce: miraculousAjax.nonce,
                    task_id: taskId
                },
                success: function(response) {
                    console.log('Full API Response:', response);

                    if (response.success) {
                        console.log('Song data:', response.data);

                        // Display song info
                        SunoAPI.displaySongInfo(response.data);

                        // Add to playlist if available
                        if (typeof window.myPlaylist !== 'undefined' && response.data.data) {
                            SunoAPI.addToJPlayer(response.data.data);
                        }

                        alert('Bài hát đã được load thành công!');
                    } else {
                        // Show detailed error
                        var errorMsg = 'Lỗi: ' + (response.data && response.data.message ? response.data.message : 'Không thể load bài hát');

                        if (response.data && response.data.debug) {
                            console.error('Debug info:', response.data.debug);
                            errorMsg += '\n\nChi tiết lỗi (xem Console):';
                            errorMsg += '\nError: ' + (response.data.error || 'Unknown');
                        }

                        alert(errorMsg);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX Error:', textStatus, errorThrown);
                    console.error('Response:', jqXHR.responseText);
                    alert('Lỗi kết nối: ' + textStatus + '\nKiểm tra Console để biết chi tiết');
                },
                complete: function() {
                    $btn.prop('disabled', false).text('Load Song');
                }
            });
        },

        /**
         * Generate music from prompt
         */
        generateMusic: function(e) {
            e.preventDefault();

            var $form = $(this);
            var $submitBtn = $form.find('button[type="submit"]');
            var prompt = $form.find('input[name="prompt"]').val();
            var model = $form.find('select[name="model"]').val() || 'V4';

            if (!prompt) {
                alert('Vui lòng nhập mô tả bài hát');
                return;
            }

            $submitBtn.prop('disabled', true).text('Đang tạo nhạc...');

            $.ajax({
                url: miraculousAjax.ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'generate_music',
                    nonce: miraculousAjax.nonce,
                    prompt: prompt,
                    model: model
                },
                success: function(response) {
                    console.log('Generate Response:', response);

                    if (response.success) {
                        var taskId = null;

                        // Try different response formats
                        if (response.data && response.data.data) {
                            taskId = response.data.data.task_id || response.data.data.id;
                        } else if (response.data) {
                            taskId = response.data.task_id || response.data.id;
                        }

                        if (taskId) {
                            alert('Bắt đầu tạo nhạc! Task ID: ' + taskId + '\n\nVui lòng đợi 20-30 giây...');

                            // Save task ID for later retrieval
                            $('#generated-task-id').val(taskId);

                            // Start polling for result
                            setTimeout(function() {
                                SunoAPI.checkGenerationStatus(taskId);
                            }, 20000); // Check after 20 seconds
                        } else {
                            alert('Lỗi: API không trả về Task ID\nKiểm tra Console để biết chi tiết');
                            console.error('Response data:', response.data);
                        }
                    } else {
                        // Show detailed error
                        var errorMsg = 'Lỗi: ' + (response.data && response.data.message ? response.data.message : 'Không thể tạo bài hát');

                        if (response.data && response.data.debug) {
                            console.error('Debug info:', response.data.debug);
                            errorMsg += '\n\nChi tiết lỗi (xem Console):';
                            errorMsg += '\nError: ' + (response.data.error || 'Unknown');
                        }

                        alert(errorMsg);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX Error:', textStatus, errorThrown);
                    console.error('Response:', jqXHR.responseText);
                    alert('Lỗi kết nối: ' + textStatus + '\nKiểm tra Console để biết chi tiết');
                },
                complete: function() {
                    $submitBtn.prop('disabled', false).text('Generate Music');
                }
            });
        },

        /**
         * Check generation status
         */
        checkGenerationStatus: function(taskId) {
            $.ajax({
                url: miraculousAjax.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_song',
                    nonce: miraculousAjax.nonce,
                    task_id: taskId
                },
                success: function(response) {
                    if (response.success && response.data.data) {
                        var songData = response.data.data;

                        if (songData.status === 'completed' || songData.audio_url) {
                            alert('Music generation completed!');
                            SunoAPI.displaySongInfo(response.data);
                            SunoAPI.addToJPlayer(songData);
                        } else if (songData.status === 'processing') {
                            // Check again in 10 seconds
                            setTimeout(function() {
                                SunoAPI.checkGenerationStatus(taskId);
                            }, 10000);
                        }
                    }
                }
            });
        },

        /**
         * Display song information
         */
        displaySongInfo: function(data) {
            var songData = data.data || data;

            var html = '<div class="suno-song-info">';
            html += '<h3>Song Information</h3>';

            if (songData.title) {
                html += '<p><strong>Title:</strong> ' + songData.title + '</p>';
            }

            if (songData.audio_url) {
                html += '<p><strong>Audio URL:</strong> <a href="' + songData.audio_url + '" target="_blank">Download</a></p>';
            }

            if (songData.image_url) {
                html += '<p><img src="' + songData.image_url + '" alt="Cover" style="max-width: 200px;"></p>';
            }

            if (songData.status) {
                html += '<p><strong>Status:</strong> ' + songData.status + '</p>';
            }

            html += '</div>';

            // Display in a designated area or modal
            if ($('#suno-song-display').length) {
                $('#suno-song-display').html(html);
            } else {
                console.log('Song Info:', songData);
            }
        },

        /**
         * Add song to jPlayer playlist
         */
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
                // Detect format from URL or extension
                var videoUrl = songData.video_url.toLowerCase();
                if (videoUrl.endsWith('.mp4') || videoUrl.indexOf('.mp4?') > -1) {
                    track.mp4 = songData.video_url;
                } else if (videoUrl.endsWith('.m4v') || videoUrl.indexOf('.m4v?') > -1) {
                    track.m4v = songData.video_url;
                } else {
                    // Default to mp4
                    track.mp4 = songData.video_url;
                }
            }

            // Add to jPlayer if available
            if (typeof window.myPlaylist !== 'undefined') {
                window.myPlaylist.add(track);
                console.log('Added to playlist:', track);
            } else {
                console.warn('jPlayer not initialized');
            }
        },

        /**
         * Play Suno song directly
         */
        playSunoSong: function(e) {
            e.preventDefault();

            var $btn = $(this);
            var audioUrl = $btn.data('audio-url');
            var videoUrl = $btn.data('video-url');
            var title = $btn.data('title') || 'Untitled';
            var artist = $btn.data('artist') || 'Suno AI';
            var poster = $btn.data('poster') || '';

            console.log('Play button clicked:', {
                audioUrl: audioUrl,
                videoUrl: videoUrl,
                title: title,
                artist: artist,
                poster: poster
            });

            if (!audioUrl && !videoUrl) {
                console.error('No audio or video URL available');
                alert('Không có URL nhạc hoặc video để phát');
                return;
            }

            var track = {
                title: title,
                artist: artist,
                poster: poster,
                image: poster
            };

            // Add audio URL
            if (audioUrl) {
                track.mp3 = audioUrl;
            }

            // Add video URL
            if (videoUrl) {
                var videoUrlLower = videoUrl.toLowerCase();
                if (videoUrlLower.endsWith('.mp4') || videoUrlLower.indexOf('.mp4?') > -1) {
                    track.mp4 = videoUrl;
                } else if (videoUrlLower.endsWith('.m4v') || videoUrlLower.indexOf('.m4v?') > -1) {
                    track.m4v = videoUrl;
                } else {
                    track.mp4 = videoUrl;
                }
            }

            // Add option for more menu
            track.option = '<ul class="more_option"><li><a href="#"><span class="opt_icon" title="Add To Favourites"><span class="icon icon_fav"></span></span></a></li><li><a href="#"><span class="opt_icon" title="Download Now"><span class="icon icon_dwn"></span></span></a></li></ul>';

            console.log('Track to add:', track);

            // Play in jPlayer
            if (typeof window.myPlaylist !== 'undefined' && window.myPlaylist) {
                console.log('Adding to playlist and playing...');

                // Add track to playlist and play immediately
                window.myPlaylist.add(track, true);

                console.log('Track added successfully!');
            } else {
                console.warn('jPlayer not initialized, opening in new tab');
                alert('Player chưa sẵn sàng. Mở nhạc trong tab mới...');
                window.open(audioUrl || videoUrl, '_blank');
            }
        },

        /**
         * Get credits
         */
        getCredits: function() {
            $.ajax({
                url: miraculousAjax.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_credits',
                    nonce: miraculousAjax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        console.log('Credits:', response.data);

                        if ($('#suno-credits-display').length) {
                            $('#suno-credits-display').html('Credits: ' + JSON.stringify(response.data.data));
                        }
                    }
                }
            });
        },

        /**
         * Load more music (pagination)
         */
        loadMoreMusic: function(e) {
            e.preventDefault();

            var $btn = $(this);
            var currentPage = parseInt($btn.data('page'));
            var maxPages = parseInt($btn.data('max-pages'));
            var nextPage = currentPage + 1;

            if (nextPage > maxPages) {
                $btn.text('No more music').prop('disabled', true);
                return;
            }

            $btn.prop('disabled', true).text('Loading...');

            $.ajax({
                url: miraculousAjax.ajax_url,
                type: 'POST',
                data: {
                    action: 'load_more_music',
                    nonce: miraculousAjax.nonce,
                    page: nextPage
                },
                success: function(response) {
                    if (response.success && response.data.html) {
                        // Append new music to list
                        $('#music-list-container').append(response.data.html);

                        // Update page number
                        $btn.data('page', nextPage);

                        // Check if we've reached the end
                        if (nextPage >= maxPages) {
                            $btn.text('No more music').prop('disabled', true);
                        } else {
                            $btn.prop('disabled', false).text('Load More Music');
                        }
                    } else {
                        alert('Failed to load more music');
                        $btn.prop('disabled', false).text('Load More Music');
                    }
                },
                error: function() {
                    alert('Request failed. Please try again.');
                    $btn.prop('disabled', false).text('Load More Music');
                }
            });
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        SunoAPI.init();

        // Expose to global scope
        window.SunoAPI = SunoAPI;

        // Clear queue confirmation
        $(document).on('click', '#confirm-clear-queue', function(e) {
            e.preventDefault();

            if (typeof window.myPlaylist !== 'undefined' && window.myPlaylist) {
                // Remove all tracks from playlist
                window.myPlaylist.remove();

                console.log('Queue cleared successfully');

                // Close modal
                $('#clear_modal').modal('hide');
            } else {
                console.warn('Playlist not available');
            }
        });
    });

})(jQuery);
