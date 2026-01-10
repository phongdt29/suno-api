/**
 * Authentication Module - Login, Register, Forgot Password
 *
 * @package Miraculous_Music
 * @since 1.0.0
 */

(function($) {
    'use strict';

    window.MiraculousAuth = {
        /**
         * Initialize
         */
        init: function() {
            var self = this;
            console.log('Auth: Initializing...');

            self.bindEvents();
            self.initPasswordToggle();
            self.initPasswordStrength();
            self.initModals();

            console.log('Auth: Initialized successfully');
        },

        /**
         * Initialize modals
         */
        initModals: function() {
            var self = this;

            console.log('Auth: Setting up modals...');

            // Helper function to open modal
            function openModal(modalId) {
                console.log('Auth: Opening modal', modalId);

                // Close any open modals first
                $('.ms_auth_modal').removeClass('show');

                // Open target modal
                var $modal = $(modalId);
                if ($modal.length) {
                    $modal.addClass('show');
                    $('body').addClass('modal-open');
                    console.log('Auth: Modal opened successfully');
                } else {
                    console.error('Auth: Modal not found:', modalId);
                }
            }

            // Helper function to close modal
            function closeModal() {
                console.log('Auth: Closing modals');
                $('.ms_auth_modal').removeClass('show');
                $('body').removeClass('modal-open');
            }

            // Open any modal via data-bs-target attribute
            $(document).on('click', '.ms_open_modal, [data-bs-target="#myModal1"], [data-bs-target="#myModal"], [data-bs-target="#forgotModal"]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var target = $(this).attr('data-bs-target');
                console.log('Auth: Click detected, target:', target);
                if (target) {
                    openModal(target);
                }
                return false;
            });

            // Close modal - click on close button
            $(document).on('click', '.ms_modal_close, [data-bs-dismiss="modal"]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeModal();
                return false;
            });

            // Close modal - click on modal background (not content)
            $(document).on('click', '.ms_auth_modal', function(e) {
                if ($(e.target).hasClass('ms_auth_modal')) {
                    closeModal();
                }
            });

            // Prevent modal content click from closing
            $(document).on('click', '.ms_auth_modal .modal-dialog', function(e) {
                e.stopPropagation();
            });

            // Close on escape
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeModal();
                }
            });

            // Switch between modals
            $(document).on('click', '.ms_auth_footer a[data-bs-target]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var target = $(this).data('bs-target');
                openModal(target);
                return false;
            });

            // Also handle forgot link
            $(document).on('click', '.ms_forgot_link[data-bs-target]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var target = $(this).data('bs-target');
                openModal(target);
                return false;
            });

            console.log('Auth: Modals setup complete. Found', $('.ms_auth_modal').length, 'modals');
        },

        /**
         * Bind all events
         */
        bindEvents: function() {
            var self = this;

            // Login form
            $(document).on('submit', '#ms-login-form', function(e) {
                self.handleLogin.call(this, e);
            });

            // Register form
            $(document).on('submit', '#ms-register-form', function(e) {
                self.handleRegister.call(this, e);
            });

            // Forgot password form
            $(document).on('submit', '#ms-forgot-form', function(e) {
                self.handleForgotPassword.call(this, e);
            });

            // Username availability check
            $(document).on('blur', '#reg_username', function() {
                self.checkUsername.call(this);
            });

            // Email availability check
            $(document).on('blur', '#reg_email', function() {
                self.checkEmail.call(this);
            });

            // Clear error on input
            $(document).on('input', '.ms_form_input', function() {
                $(this).removeClass('is-invalid');
            });
        },

        /**
         * Initialize password toggle visibility
         */
        initPasswordToggle: function() {
            $(document).on('click', '.ms_toggle_password', function() {
                var target = $(this).data('target');
                var input = $('#' + target);
                var icon = $(this).find('i');

                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });
        },

        /**
         * Initialize password strength indicator
         */
        initPasswordStrength: function() {
            var self = this;
            $(document).on('input', '#reg_password', function() {
                var password = $(this).val();
                var strength = self.calculatePasswordStrength(password);
                var strengthEl = $('#password-strength');

                if (password.length === 0) {
                    strengthEl.html('').removeClass('weak medium strong');
                    return;
                }

                var html = '<div class="strength-bar ' + strength.level + '">';
                html += '<span class="strength-fill" style="width: ' + strength.percent + '%"></span>';
                html += '</div>';
                html += '<span class="strength-text">' + strength.text + '</span>';

                strengthEl.html(html).removeClass('weak medium strong').addClass(strength.level);
            });
        },

        /**
         * Calculate password strength
         */
        calculatePasswordStrength: function(password) {
            var score = 0;

            if (password.length >= 8) score += 1;
            if (password.length >= 12) score += 1;
            if (/[a-z]/.test(password)) score += 1;
            if (/[A-Z]/.test(password)) score += 1;
            if (/[0-9]/.test(password)) score += 1;
            if (/[^a-zA-Z0-9]/.test(password)) score += 1;

            if (score <= 2) {
                return { level: 'weak', percent: 33, text: 'Yếu' };
            } else if (score <= 4) {
                return { level: 'medium', percent: 66, text: 'Trung bình' };
            } else {
                return { level: 'strong', percent: 100, text: 'Mạnh' };
            }
        },

        /**
         * Handle login form submission
         */
        handleLogin: function(e) {
            e.preventDefault();
            console.log('Auth: Login form submitted');

            var $form = $(this);
            var $btn = $form.find('button[type="submit"]');
            var $message = $('#login-message');
            var self = window.MiraculousAuth;

            // Disable button
            self.setLoading($btn, true);
            $message.html('').hide();

            // Collect form data
            var formData = {
                action: 'miraculous_login',
                username: $form.find('#login_username').val(),
                password: $form.find('#login_password').val(),
                remember: $form.find('input[name="remember"]').is(':checked') ? '1' : '0',
                login_nonce: $form.find('input[name="login_nonce"]').val()
            };

            console.log('Auth: Login data', {
                action: formData.action,
                username: formData.username,
                remember: formData.remember,
                hasNonce: !!formData.login_nonce
            });

            // Make AJAX request
            $.ajax({
                url: miraculousAjax.ajax_url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    console.log('Auth: Login response', response);
                    if (response.success) {
                        self.showMessage($message, response.data.message, 'success');
                        setTimeout(function() {
                            window.location.href = response.data.redirect || miraculousAjax.home_url;
                        }, 1000);
                    } else {
                        self.showMessage($message, response.data.message, 'error');
                        self.setLoading($btn, false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Auth: Login error', status, error);
                    self.showMessage($message, 'Lỗi kết nối. Vui lòng thử lại.', 'error');
                    self.setLoading($btn, false);
                }
            });
        },

        /**
         * Handle register form submission
         */
        handleRegister: function(e) {
            e.preventDefault();
            console.log('Auth: Register form submitted');

            var $form = $(this);
            var $btn = $form.find('button[type="submit"]');
            var $message = $('#register-message');
            var self = window.MiraculousAuth;

            // Validate passwords match
            var password = $form.find('#reg_password').val();
            var passwordConfirm = $form.find('#reg_password_confirm').val();

            if (password !== passwordConfirm) {
                self.showMessage($message, 'Mật khẩu xác nhận không khớp.', 'error');
                $form.find('#reg_password_confirm').addClass('is-invalid');
                return;
            }

            // Disable button
            self.setLoading($btn, true);
            $message.html('').hide();

            // Collect form data
            var formData = {
                action: 'miraculous_register',
                firstname: $form.find('#reg_firstname').val(),
                lastname: $form.find('#reg_lastname').val(),
                username: $form.find('#reg_username').val(),
                email: $form.find('#reg_email').val(),
                password: password,
                password_confirm: passwordConfirm,
                terms: $form.find('input[name="terms"]').is(':checked') ? '1' : '0',
                register_nonce: $form.find('input[name="register_nonce"]').val()
            };

            console.log('Auth: Register data', {
                action: formData.action,
                username: formData.username,
                email: formData.email,
                hasNonce: !!formData.register_nonce
            });

            // Make AJAX request
            $.ajax({
                url: miraculousAjax.ajax_url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    console.log('Auth: Register response', response);
                    if (response.success) {
                        self.showMessage($message, response.data.message, 'success');
                        setTimeout(function() {
                            window.location.href = response.data.redirect || miraculousAjax.home_url;
                        }, 1000);
                    } else {
                        self.showMessage($message, response.data.message, 'error');
                        if (response.data.field) {
                            $form.find('#reg_' + response.data.field).addClass('is-invalid');
                        }
                        self.setLoading($btn, false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Auth: Register error', status, error);
                    self.showMessage($message, 'Lỗi kết nối. Vui lòng thử lại.', 'error');
                    self.setLoading($btn, false);
                }
            });
        },

        /**
         * Handle forgot password form submission
         */
        handleForgotPassword: function(e) {
            e.preventDefault();
            console.log('Auth: Forgot form submitted');

            var $form = $(this);
            var $btn = $form.find('button[type="submit"]');
            var $message = $('#forgot-message');
            var self = window.MiraculousAuth;

            // Disable button
            self.setLoading($btn, true);
            $message.html('').hide();

            // Collect form data
            var formData = {
                action: 'miraculous_forgot_password',
                email: $form.find('#forgot_email').val(),
                forgot_nonce: $form.find('input[name="forgot_nonce"]').val()
            };

            // Make AJAX request
            $.ajax({
                url: miraculousAjax.ajax_url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    console.log('Auth: Forgot response', response);
                    if (response.success) {
                        self.showMessage($message, response.data.message, 'success');
                        $form.find('#forgot_email').val('');
                    } else {
                        self.showMessage($message, response.data.message, 'error');
                    }
                    self.setLoading($btn, false);
                },
                error: function(xhr, status, error) {
                    console.error('Auth: Forgot error', status, error);
                    self.showMessage($message, 'Lỗi kết nối. Vui lòng thử lại.', 'error');
                    self.setLoading($btn, false);
                }
            });
        },

        /**
         * Check username availability
         */
        checkUsername: function() {
            var $input = $(this);
            var username = $input.val();

            if (username.length < 3) return;

            $.ajax({
                url: miraculousAjax.ajax_url,
                type: 'POST',
                data: {
                    action: 'miraculous_check_username',
                    username: username
                },
                success: function(response) {
                    if (response.success && response.data.available) {
                        $input.removeClass('is-invalid').addClass('is-valid');
                    } else {
                        $input.removeClass('is-valid').addClass('is-invalid');
                    }
                }
            });
        },

        /**
         * Check email availability
         */
        checkEmail: function() {
            var $input = $(this);
            var email = $input.val();
            var self = window.MiraculousAuth;

            if (!self.isValidEmail(email)) return;

            $.ajax({
                url: miraculousAjax.ajax_url,
                type: 'POST',
                data: {
                    action: 'miraculous_check_email',
                    email: email
                },
                success: function(response) {
                    if (response.success && response.data.available) {
                        $input.removeClass('is-invalid').addClass('is-valid');
                    } else {
                        $input.removeClass('is-valid').addClass('is-invalid');
                    }
                }
            });
        },

        /**
         * Validate email format
         */
        isValidEmail: function(email) {
            var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        },

        /**
         * Show form message
         */
        showMessage: function($element, message, type) {
            var className = type === 'success' ? 'alert-success' : 'alert-error';
            $element
                .html('<div class="ms_alert ' + className + '">' + message + '</div>')
                .show();
        },

        /**
         * Set button loading state
         */
        setLoading: function($btn, loading) {
            if (loading) {
                $btn.prop('disabled', true);
                $btn.find('.btn-text').hide();
                $btn.find('.btn-loading').show();
            } else {
                $btn.prop('disabled', false);
                $btn.find('.btn-text').show();
                $btn.find('.btn-loading').hide();
            }
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        // Check if miraculousAjax is defined
        if (typeof miraculousAjax === 'undefined') {
            console.error('Auth: miraculousAjax is not defined. AJAX will not work.');
            return;
        }
        window.MiraculousAuth.init();
    });

})(jQuery);
