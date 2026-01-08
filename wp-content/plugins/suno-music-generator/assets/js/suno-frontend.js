/**
 * Suno Music Generator - Frontend JavaScript
 */

(function($) {
    'use strict';

    // Poll interval in milliseconds
    const POLL_INTERVAL = 5000;
    const MAX_POLL_ATTEMPTS = 60; // 5 minutes max

    /**
     * Suno Generator Class
     */
    class SunoGenerator {
        constructor($wrap) {
            this.$wrap = $wrap;
            this.$form = $wrap.find('.suno-generator-form');
            this.$progress = $wrap.find('.suno-progress');
            this.$results = $wrap.find('.suno-results');
            this.$error = $wrap.find('.suno-error');
            this.$gptResult = $wrap.find('.suno-gpt-result');
            this.$lyricsResult = $wrap.find('.suno-lyrics-result');
            this.type = $wrap.data('type');
            this.pollAttempts = 0;

            this.init();
        }

        init() {
            this.$form.on('submit', (e) => this.handleSubmit(e));
            this.$wrap.on('click', '.suno-copy-lyrics', (e) => this.copyLyrics(e));
        }

        handleSubmit(e) {
            e.preventDefault();

            const formData = this.getFormData();

            if (!this.validateForm(formData)) {
                return;
            }

            this.setLoading(true);
            this.hideMessages();

            switch (this.type) {
                case 'simple':
                    this.generateSimple(formData);
                    break;
                case 'custom':
                    this.generateCustom(formData);
                    break;
                case 'auto':
                    this.generateAuto(formData);
                    break;
                case 'lyrics':
                    this.generateLyrics(formData);
                    break;
            }
        }

        getFormData() {
            const data = {};
            this.$form.serializeArray().forEach(item => {
                data[item.name] = item.value;
            });

            // Handle checkbox
            if (this.$form.find('input[name="instrumental"]').length) {
                data.instrumental = this.$form.find('input[name="instrumental"]').is(':checked');
            }

            return data;
        }

        validateForm(data) {
            if (this.type === 'simple' && !data.prompt) {
                this.showError(sunoApi.i18n.error, 'Vui lòng nhập mô tả bài hát');
                return false;
            }

            if (this.type === 'custom' && !data.lyrics) {
                this.showError(sunoApi.i18n.error, 'Vui lòng nhập lyrics');
                return false;
            }

            if (this.type === 'auto' && !data.idea) {
                this.showError(sunoApi.i18n.error, 'Vui lòng nhập ý tưởng');
                return false;
            }

            if (this.type === 'lyrics' && !data.prompt) {
                this.showError(sunoApi.i18n.error, 'Vui lòng nhập mô tả lyrics');
                return false;
            }

            return true;
        }

        generateSimple(data) {
            this.apiRequest('generate', 'POST', data)
                .then(response => {
                    if (response.success) {
                        this.showProgress();
                        this.pollForResult(response.data.taskId);
                    } else {
                        this.showError(sunoApi.i18n.error, response.message);
                        this.setLoading(false);
                    }
                })
                .catch(error => {
                    this.showError(sunoApi.i18n.error, error.message);
                    this.setLoading(false);
                });
        }

        generateCustom(data) {
            this.apiRequest('generate-custom', 'POST', data)
                .then(response => {
                    if (response.success) {
                        this.showProgress();
                        this.pollForResult(response.data.taskId);
                    } else {
                        this.showError(sunoApi.i18n.error, response.message);
                        this.setLoading(false);
                    }
                })
                .catch(error => {
                    this.showError(sunoApi.i18n.error, error.message);
                    this.setLoading(false);
                });
        }

        generateAuto(data) {
            this.apiRequest('auto-generate', 'POST', data)
                .then(response => {
                    if (response.success) {
                        // Show GPT result
                        if (response.data.gpt_content) {
                            this.showGptResult(response.data.gpt_content);
                        }
                        this.showProgress();
                        this.pollForResult(response.data.taskId);
                    } else {
                        this.showError(sunoApi.i18n.error, response.message);
                        this.setLoading(false);
                    }
                })
                .catch(error => {
                    this.showError(sunoApi.i18n.error, error.message);
                    this.setLoading(false);
                });
        }

        generateLyrics(data) {
            this.apiRequest('lyrics', 'POST', data)
                .then(response => {
                    if (response.success) {
                        this.showProgress();
                        this.pollForLyrics(response.data.taskId);
                    } else {
                        this.showError(sunoApi.i18n.error, response.message);
                        this.setLoading(false);
                    }
                })
                .catch(error => {
                    this.showError(sunoApi.i18n.error, error.message);
                    this.setLoading(false);
                });
        }

        pollForResult(taskId) {
            this.pollAttempts++;

            if (this.pollAttempts > MAX_POLL_ATTEMPTS) {
                this.showError(sunoApi.i18n.error, 'Quá thời gian chờ. Vui lòng thử lại.');
                this.setLoading(false);
                this.hideProgress();
                return;
            }

            this.apiRequest(`song/${taskId}`, 'GET')
                .then(response => {
                    if (response.success) {
                        const status = response.data.status;

                        if (status === 'completed') {
                            this.showResults(response.data.songs);
                            this.setLoading(false);
                            this.hideProgress();
                            this.pollAttempts = 0;
                        } else if (status === 'failed') {
                            this.showError(sunoApi.i18n.error, response.data.error || 'Tạo nhạc thất bại');
                            this.setLoading(false);
                            this.hideProgress();
                            this.pollAttempts = 0;
                        } else {
                            // Continue polling
                            setTimeout(() => this.pollForResult(taskId), POLL_INTERVAL);
                        }
                    } else {
                        this.showError(sunoApi.i18n.error, response.message);
                        this.setLoading(false);
                        this.hideProgress();
                    }
                })
                .catch(error => {
                    // Retry on network error
                    setTimeout(() => this.pollForResult(taskId), POLL_INTERVAL);
                });
        }

        pollForLyrics(taskId) {
            this.pollAttempts++;

            if (this.pollAttempts > MAX_POLL_ATTEMPTS) {
                this.showError(sunoApi.i18n.error, 'Quá thời gian chờ. Vui lòng thử lại.');
                this.setLoading(false);
                this.hideProgress();
                return;
            }

            this.apiRequest(`lyrics/${taskId}`, 'GET')
                .then(response => {
                    if (response.success) {
                        const status = response.data.status;

                        if (status === 'completed') {
                            this.showLyricsResult(response.data);
                            this.setLoading(false);
                            this.hideProgress();
                            this.pollAttempts = 0;
                        } else if (status === 'failed') {
                            this.showError(sunoApi.i18n.error, 'Tạo lyrics thất bại');
                            this.setLoading(false);
                            this.hideProgress();
                            this.pollAttempts = 0;
                        } else {
                            // Continue polling
                            setTimeout(() => this.pollForLyrics(taskId), POLL_INTERVAL);
                        }
                    } else {
                        this.showError(sunoApi.i18n.error, response.message);
                        this.setLoading(false);
                        this.hideProgress();
                    }
                })
                .catch(error => {
                    // Retry on network error
                    setTimeout(() => this.pollForLyrics(taskId), POLL_INTERVAL);
                });
        }

        apiRequest(endpoint, method, data = null) {
            const options = {
                url: sunoApi.restUrl + endpoint,
                method: method,
                headers: {
                    'X-WP-Nonce': sunoApi.nonce
                }
            };

            if (data && method === 'POST') {
                options.contentType = 'application/json';
                options.data = JSON.stringify(data);
            }

            return $.ajax(options);
        }

        setLoading(isLoading) {
            const $btn = this.$form.find('button[type="submit"]');
            const $text = $btn.find('.suno-btn-text');
            const $loading = $btn.find('.suno-btn-loading');

            if (isLoading) {
                $btn.prop('disabled', true);
                $text.hide();
                $loading.show();
            } else {
                $btn.prop('disabled', false);
                $text.show();
                $loading.hide();
            }
        }

        showProgress() {
            this.$progress.show();
        }

        hideProgress() {
            this.$progress.hide();
        }

        hideMessages() {
            this.$error.hide();
            this.$results.hide();
            this.$gptResult.hide();
            this.$lyricsResult.hide();
        }

        showError(title, message) {
            this.$error.html(`
                <p class="suno-error-title">${this.escapeHtml(title)}</p>
                <p class="suno-error-message">${this.escapeHtml(message)}</p>
            `).show();
        }

        showResults(songs) {
            if (!songs || songs.length === 0) {
                this.showError(sunoApi.i18n.error, 'Không có kết quả');
                return;
            }

            let html = `
                <h3 class="suno-results-title">
                    <span class="suno-success-badge">${sunoApi.i18n.completed}</span>
                </h3>
                <div class="suno-song-list">
            `;

            songs.forEach((song, index) => {
                html += `
                    <div class="suno-song-item">
                        ${song.image_url ? `<img src="${this.escapeHtml(song.image_url)}" alt="${this.escapeHtml(song.title)}" class="suno-song-cover">` : ''}
                        <div class="suno-song-info">
                            <h4 class="suno-song-title">${this.escapeHtml(song.title) || 'Song ' + (index + 1)}</h4>
                            ${song.style ? `<p class="suno-song-style">${this.escapeHtml(song.style)}</p>` : ''}
                            <div class="suno-audio-player">
                                <audio controls preload="metadata">
                                    <source src="${this.escapeHtml(song.audio_url)}" type="audio/mpeg">
                                </audio>
                            </div>
                            <div class="suno-song-actions">
                                <a href="${this.escapeHtml(song.audio_url)}" download class="suno-btn suno-btn-secondary">
                                    ${sunoApi.i18n.download} MP3
                                </a>
                                ${song.video_url ? `<a href="${this.escapeHtml(song.video_url)}" download class="suno-btn suno-btn-secondary">${sunoApi.i18n.download} Video</a>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });

            html += '</div>';
            this.$results.html(html).show();
        }

        showGptResult(content) {
            let html = `
                <p><strong>Tiêu đề:</strong> ${this.escapeHtml(content.title)}</p>
                <p><strong>Phong cách:</strong> ${this.escapeHtml(content.style)}</p>
                <p><strong>Lyrics:</strong></p>
                <div class="suno-gpt-lyrics">${this.escapeHtml(content.lyrics)}</div>
            `;
            this.$gptResult.find('.suno-gpt-content').html(html);
            this.$gptResult.show();
        }

        showLyricsResult(data) {
            this.$lyricsResult.find('.suno-lyrics-title').text(data.title || 'Generated Lyrics');
            this.$lyricsResult.find('.suno-lyrics-content').text(data.lyrics);
            this.$lyricsResult.show();
        }

        copyLyrics(e) {
            e.preventDefault();
            const lyrics = this.$lyricsResult.find('.suno-lyrics-content').text();

            if (navigator.clipboard) {
                navigator.clipboard.writeText(lyrics).then(() => {
                    const $btn = $(e.target);
                    const originalText = $btn.text();
                    $btn.text('Đã sao chép!');
                    setTimeout(() => $btn.text(originalText), 2000);
                });
            } else {
                // Fallback for older browsers
                const textarea = document.createElement('textarea');
                textarea.value = lyrics;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);

                const $btn = $(e.target);
                const originalText = $btn.text();
                $btn.text('Đã sao chép!');
                setTimeout(() => $btn.text(originalText), 2000);
            }
        }

        escapeHtml(text) {
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
     * Initialize all generators on page
     */
    $(document).ready(function() {
        $('.suno-generator-wrap').each(function() {
            new SunoGenerator($(this));
        });
    });

})(jQuery);
