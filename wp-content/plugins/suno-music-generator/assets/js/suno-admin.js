/**
 * Suno Music Generator - Admin JavaScript
 */

(function($) {
    'use strict';

    const POLL_INTERVAL = 5000;
    const MAX_POLL_ATTEMPTS = 60;

    /**
     * Quick Generate Handler
     */
    function initQuickGenerate() {
        const $form = $('#suno-quick-generate');
        const $result = $('#suno-quick-result');
        let pollAttempts = 0;

        $form.on('submit', function(e) {
            e.preventDefault();

            const prompt = $form.find('[name="prompt"]').val();
            if (!prompt) {
                alert('Vui lòng nhập mô tả bài hát');
                return;
            }

            const data = {
                prompt: prompt,
                instrumental: $form.find('[name="instrumental"]').is(':checked'),
                model: $form.find('[name="model"]').val()
            };

            $form.find('button').prop('disabled', true).text('Đang xử lý...');
            $result.html('<p class="loading"><span class="suno-loading"></span> Đang tạo nhạc...</p>').show();

            $.ajax({
                url: sunoAdmin.restUrl + 'generate',
                method: 'POST',
                headers: { 'X-WP-Nonce': sunoAdmin.nonce },
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: function(response) {
                    if (response.success) {
                        pollAttempts = 0;
                        pollForResult(response.data.taskId);
                    } else {
                        showError(response.message);
                        resetForm();
                    }
                },
                error: function() {
                    showError('Lỗi kết nối');
                    resetForm();
                }
            });
        });

        function pollForResult(taskId) {
            pollAttempts++;

            if (pollAttempts > MAX_POLL_ATTEMPTS) {
                showError('Quá thời gian chờ');
                resetForm();
                return;
            }

            $.ajax({
                url: sunoAdmin.restUrl + 'song/' + taskId,
                method: 'GET',
                headers: { 'X-WP-Nonce': sunoAdmin.nonce },
                success: function(response) {
                    if (response.success) {
                        const status = response.data.status;

                        if (status === 'completed') {
                            showResults(response.data.songs);
                            resetForm();
                        } else if (status === 'failed') {
                            showError(response.data.error || 'Tạo nhạc thất bại');
                            resetForm();
                        } else {
                            $result.html('<p class="loading"><span class="suno-loading"></span> Đang xử lý... (' + pollAttempts * 5 + 's)</p>');
                            setTimeout(() => pollForResult(taskId), POLL_INTERVAL);
                        }
                    } else {
                        showError(response.message);
                        resetForm();
                    }
                },
                error: function() {
                    setTimeout(() => pollForResult(taskId), POLL_INTERVAL);
                }
            });
        }

        function showResults(songs) {
            if (!songs || songs.length === 0) {
                showError('Không có kết quả');
                return;
            }

            let html = '<p style="color: green; font-weight: bold;">✓ Hoàn thành!</p>';

            songs.forEach(function(song, index) {
                html += `
                    <div class="song-item">
                        ${song.image_url ? `<img src="${escapeHtml(song.image_url)}" class="song-cover" alt="">` : ''}
                        <div class="song-info">
                            <div class="song-title">${escapeHtml(song.title) || 'Song ' + (index + 1)}</div>
                            ${song.style ? `<div class="song-style">${escapeHtml(song.style)}</div>` : ''}
                            <audio controls style="width: 100%;">
                                <source src="${escapeHtml(song.audio_url)}" type="audio/mpeg">
                            </audio>
                        </div>
                    </div>
                `;
            });

            $result.html(html);
        }

        function showError(message) {
            $result.html('<p style="color: red;">✗ ' + escapeHtml(message) + '</p>');
        }

        function resetForm() {
            $form.find('button').prop('disabled', false).text('Tạo nhạc');
            pollAttempts = 0;
        }

        function escapeHtml(text) {
            if (!text) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text).replace(/[&<>"']/g, m => map[m]);
        }
    }

    /**
     * View Songs Modal Handler
     */
    function initViewSongs() {
        $(document).on('click', '.view-songs', function() {
            const id = $(this).data('id');
            // TODO: Implement modal to view songs
            alert('Xem chi tiết ID: ' + id);
        });
    }

    /**
     * Initialize
     */
    $(document).ready(function() {
        initQuickGenerate();
        initViewSongs();
    });

})(jQuery);
