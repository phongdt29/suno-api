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

            // Get form values
            var genre = $form.find('select[name="genre"]').val();
            var prompt = $form.find('textarea[name="prompt"], input[name="prompt"]').val() || '';
            var model = $form.find('select[name="model"]').val() || 'V4.5';
            var makeInstrumental = $form.find('input[name="make_instrumental"]').is(':checked');

            // Get selected moods
            var moods = [];
            $form.find('input[name="mood[]"]:checked').each(function() {
                moods.push($(this).val());
            });

            // Validate genre selection
            if (!genre) {
                alert('Vui lòng chọn thể loại nhạc');
                return;
            }

            // Build full prompt with genre and mood
            var fullPrompt = genre;

            if (moods.length > 0) {
                fullPrompt += ', ' + moods.join(', ');
            }

            if (prompt) {
                fullPrompt += '. ' + prompt;
            }

            // Build style string for database
            var style = genre;
            if (moods.length > 0) {
                style += ', ' + moods.join(', ');
            }

            console.log('Generating music with:', {
                genre: genre,
                moods: moods,
                prompt: prompt,
                fullPrompt: fullPrompt,
                style: style,
                model: model,
                instrumental: makeInstrumental
            });

            $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang tạo nhạc...');

            $.ajax({
                url: miraculousAjax.ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'generate_music',
                    nonce: miraculousAjax.nonce,
                    prompt: fullPrompt,
                    style: style,
                    model: model,
                    make_instrumental: makeInstrumental ? 1 : 0
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
            var songId = $btn.closest('[data-song-id]').data('song-id');

            console.log('Play button clicked:', {
                audioUrl: audioUrl,
                videoUrl: videoUrl,
                title: title,
                artist: artist,
                poster: poster,
                songId: songId
            });

            if (!audioUrl && !videoUrl) {
                console.error('No audio or video URL available');
                alert('Không có URL nhạc hoặc video để phát');
                return;
            }

            // Track view if song ID is available
            if (songId) {
                SunoAPI.trackView(songId);
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
        },

        /**
         * Track song view
         */
        trackView: function(songId) {
            if (!songId) {
                return;
            }

            $.ajax({
                url: miraculousAjax.ajax_url,
                type: 'POST',
                data: {
                    action: 'track_view',
                    nonce: miraculousAjax.nonce,
                    song_id: songId
                },
                success: function(response) {
                    if (response.success) {
                        console.log('View tracked:', response.data);
                    }
                },
                error: function() {
                    console.log('Failed to track view');
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

        // Initialize Live Search
        LiveSearch.init();
    });

    /**
     * Live Search Module
     */
    var LiveSearch = {
        searchTimer: null,
        minChars: 2,
        delay: 300,

        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            var self = this;

            // Input event for live search
            $('#header-search-input').on('input', function() {
                var query = $(this).val().trim();

                clearTimeout(self.searchTimer);

                if (query.length < self.minChars) {
                    self.hideDropdown();
                    return;
                }

                self.searchTimer = setTimeout(function() {
                    self.performSearch(query);
                }, self.delay);
            });

            // Focus event
            $('#header-search-input').on('focus', function() {
                var query = $(this).val().trim();
                if (query.length >= self.minChars) {
                    self.showDropdown();
                }
            });

            // Click outside to close
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.ms_top_search').length) {
                    self.hideDropdown();
                }
            });

            // Play button in search results
            $(document).on('click', '.search-result-play', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var $item = $(this).closest('.search-result-item');
                var audioUrl = $item.data('audio-url');
                var videoUrl = $item.data('video-url');
                var title = $item.data('title');
                var poster = $item.data('poster');

                if (audioUrl || videoUrl) {
                    SunoAPI.addToJPlayer({
                        title: title,
                        audio_url: audioUrl,
                        video_url: videoUrl,
                        image_url: poster
                    });

                    // Play immediately
                    if (typeof window.myPlaylist !== 'undefined') {
                        var lastIndex = window.myPlaylist.playlist.length - 1;
                        window.myPlaylist.play(lastIndex);
                    }
                }

                self.hideDropdown();
            });

            // Click on result item (go to search page)
            $(document).on('click', '.search-result-item', function(e) {
                if ($(e.target).closest('.search-result-play').length) {
                    return;
                }

                var query = $('#header-search-input').val().trim();
                if (query) {
                    window.location.href = miraculousAjax.home_url + '?s=' + encodeURIComponent(query);
                }
            });

            // Enter key to submit search
            $('#header-search-input').on('keypress', function(e) {
                if (e.which === 13) {
                    self.hideDropdown();
                }
            });
        },

        performSearch: function(query) {
            var self = this;

            this.showLoading();

            $.ajax({
                url: miraculousAjax.ajax_url,
                type: 'POST',
                data: {
                    action: 'miraculous_search_music',
                    nonce: miraculousAjax.nonce,
                    query: query
                },
                success: function(response) {
                    if (response.success) {
                        self.displayResults(response.data.results, query);
                    } else {
                        self.showNoResults();
                    }
                },
                error: function() {
                    self.showNoResults();
                }
            });
        },

        displayResults: function(results, query) {
            var $dropdown = $('#live-search-results');
            var $inner = $dropdown.find('.search-results-inner');
            var defaultImg = (typeof miraculousAjax !== 'undefined' && miraculousAjax.theme_url)
                ? miraculousAjax.theme_url + '/assets/images/music/r_music1.jpg'
                : '';
            var playIcon = (typeof miraculousAjax !== 'undefined' && miraculousAjax.theme_url)
                ? miraculousAjax.theme_url + '/assets/images/svg/play.svg'
                : '';

            if (!results || results.length === 0) {
                this.showNoResults();
                return;
            }

            var html = '';

            $.each(results.slice(0, 8), function(index, song) {
                var imgUrl = song.image_url || defaultImg;
                var hasAudio = song.audio_url || song.video_url;

                html += '<div class="search-result-item" ';
                html += 'data-audio-url="' + (song.audio_url || '') + '" ';
                html += 'data-video-url="' + (song.video_url || '') + '" ';
                html += 'data-title="' + (song.title || 'Untitled') + '" ';
                html += 'data-poster="' + imgUrl + '">';

                html += '<div class="search-result-img">';
                html += '<img src="' + imgUrl + '" alt="">';
                html += '</div>';

                html += '<div class="search-result-info">';
                html += '<div class="search-result-title">' + (song.title || 'Untitled') + '</div>';
                html += '<div class="search-result-style">' + (song.style || 'Suno AI') + '</div>';
                html += '</div>';

                if (hasAudio) {
                    html += '<div class="search-result-play">';
                    html += '<img src="' + playIcon + '" alt="Play">';
                    html += '</div>';
                }

                html += '</div>';
            });

            // View all link
            var homeUrl = (typeof miraculousAjax !== 'undefined' && miraculousAjax.home_url)
                ? miraculousAjax.home_url
                : '/';
            html += '<a href="' + homeUrl + '?s=' + encodeURIComponent(query) + '" class="search-view-all">';
            html += 'View all results for "' + query + '"';
            html += '</a>';

            $inner.html(html);
            this.showDropdown();
        },

        showLoading: function() {
            var $dropdown = $('#live-search-results');
            var $inner = $dropdown.find('.search-results-inner');
            $inner.html('<div class="search-loading"><i class="fa fa-spinner fa-spin"></i> Searching...</div>');
            this.showDropdown();
        },

        showNoResults: function() {
            var $dropdown = $('#live-search-results');
            var $inner = $dropdown.find('.search-results-inner');
            $inner.html('<div class="search-no-results">No results found</div>');
            this.showDropdown();
        },

        showDropdown: function() {
            $('#live-search-results').slideDown(200);
        },

        hideDropdown: function() {
            $('#live-search-results').slideUp(200);
        }
    };

    // Expose LiveSearch globally
    window.LiveSearch = LiveSearch;

})(jQuery);
