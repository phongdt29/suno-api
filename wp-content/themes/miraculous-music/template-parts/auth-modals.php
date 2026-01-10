<?php
/**
 * Auth Modals - Login and Register Modal Templates
 *
 * @package Miraculous_Music
 * @since 1.0.0
 */

if (is_user_logged_in()) {
    return;
}
?>

<!-- Login Modal -->
<div class="ms_auth_modal" id="myModal1" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <button type="button" class="btn-close ms_modal_close" data-bs-dismiss="modal" aria-label="Close">
                <i class="fa fa-times"></i>
            </button>
            <div class="modal-body">
                <div class="ms_auth_wrapper">
                    <div class="ms_auth_header">
                        <h2><?php esc_html_e('Đăng nhập', 'miraculous-music'); ?></h2>
                        <p><?php esc_html_e('Chào mừng bạn quay trở lại!', 'miraculous-music'); ?></p>
                    </div>

                    <form id="ms-login-form" class="ms_auth_form">
                        <?php wp_nonce_field('miraculous_login', 'login_nonce'); ?>

                        <div class="ms_form_group">
                            <label for="login_username"><?php esc_html_e('Tên đăng nhập hoặc Email', 'miraculous-music'); ?></label>
                            <input type="text" id="login_username" name="username" class="ms_form_input" placeholder="<?php esc_attr_e('Nhập tên đăng nhập hoặc email', 'miraculous-music'); ?>" required>
                        </div>

                        <div class="ms_form_group">
                            <label for="login_password"><?php esc_html_e('Mật khẩu', 'miraculous-music'); ?></label>
                            <div class="ms_password_wrapper">
                                <input type="password" id="login_password" name="password" class="ms_form_input" placeholder="<?php esc_attr_e('Nhập mật khẩu', 'miraculous-music'); ?>" required>
                                <span class="ms_toggle_password" data-target="login_password">
                                    <i class="fa fa-eye"></i>
                                </span>
                            </div>
                        </div>

                        <div class="ms_form_options">
                            <label class="ms_checkbox">
                                <input type="checkbox" name="remember" value="1">
                                <span class="checkmark"></span>
                                <?php esc_html_e('Ghi nhớ đăng nhập', 'miraculous-music'); ?>
                            </label>
                            <a href="#" class="ms_forgot_link" data-bs-toggle="modal" data-bs-target="#forgotModal" data-bs-dismiss="modal">
                                <?php esc_html_e('Quên mật khẩu?', 'miraculous-music'); ?>
                            </a>
                        </div>

                        <div class="ms_form_message" id="login-message"></div>

                        <button type="submit" class="ms_btn ms_auth_btn">
                            <span class="btn-text"><?php esc_html_e('Đăng nhập', 'miraculous-music'); ?></span>
                            <span class="btn-loading" style="display: none;">
                                <i class="fa fa-spinner fa-spin"></i> <?php esc_html_e('Đang xử lý...', 'miraculous-music'); ?>
                            </span>
                        </button>
                    </form>

                    <div class="ms_auth_divider">
                        <span><?php esc_html_e('hoặc', 'miraculous-music'); ?></span>
                    </div>

                    <div class="ms_social_login">
                        <?php if (function_exists('wsl_render_auth_widget')) : ?>
                            <?php wsl_render_auth_widget(); ?>
                        <?php else : ?>
                            <button type="button" class="ms_social_btn ms_google_btn" disabled>
                                <i class="fa fa-google"></i> <?php esc_html_e('Đăng nhập với Google', 'miraculous-music'); ?>
                            </button>
                            <button type="button" class="ms_social_btn ms_facebook_btn" disabled>
                                <i class="fa fa-facebook"></i> <?php esc_html_e('Đăng nhập với Facebook', 'miraculous-music'); ?>
                            </button>
                        <?php endif; ?>
                    </div>

                    <div class="ms_auth_footer">
                        <p><?php esc_html_e('Chưa có tài khoản?', 'miraculous-music'); ?>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#myModal" data-bs-dismiss="modal">
                                <?php esc_html_e('Đăng ký ngay', 'miraculous-music'); ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Register Modal -->
<div class="ms_auth_modal" id="myModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <button type="button" class="btn-close ms_modal_close" data-bs-dismiss="modal" aria-label="Close">
                <i class="fa fa-times"></i>
            </button>
            <div class="modal-body">
                <div class="ms_auth_wrapper">
                    <div class="ms_auth_header">
                        <h2><?php esc_html_e('Đăng ký', 'miraculous-music'); ?></h2>
                        <p><?php esc_html_e('Tạo tài khoản để trải nghiệm đầy đủ!', 'miraculous-music'); ?></p>
                    </div>

                    <form id="ms-register-form" class="ms_auth_form">
                        <?php wp_nonce_field('miraculous_register', 'register_nonce'); ?>

                        <div class="ms_form_row">
                            <div class="ms_form_group ms_half">
                                <label for="reg_firstname"><?php esc_html_e('Họ', 'miraculous-music'); ?></label>
                                <input type="text" id="reg_firstname" name="firstname" class="ms_form_input" placeholder="<?php esc_attr_e('Nhập họ', 'miraculous-music'); ?>">
                            </div>
                            <div class="ms_form_group ms_half">
                                <label for="reg_lastname"><?php esc_html_e('Tên', 'miraculous-music'); ?></label>
                                <input type="text" id="reg_lastname" name="lastname" class="ms_form_input" placeholder="<?php esc_attr_e('Nhập tên', 'miraculous-music'); ?>">
                            </div>
                        </div>

                        <div class="ms_form_group">
                            <label for="reg_username"><?php esc_html_e('Tên đăng nhập', 'miraculous-music'); ?> <span class="required">*</span></label>
                            <input type="text" id="reg_username" name="username" class="ms_form_input" placeholder="<?php esc_attr_e('Chọn tên đăng nhập', 'miraculous-music'); ?>" required>
                        </div>

                        <div class="ms_form_group">
                            <label for="reg_email"><?php esc_html_e('Email', 'miraculous-music'); ?> <span class="required">*</span></label>
                            <input type="email" id="reg_email" name="email" class="ms_form_input" placeholder="<?php esc_attr_e('Nhập địa chỉ email', 'miraculous-music'); ?>" required>
                        </div>

                        <div class="ms_form_group">
                            <label for="reg_password"><?php esc_html_e('Mật khẩu', 'miraculous-music'); ?> <span class="required">*</span></label>
                            <div class="ms_password_wrapper">
                                <input type="password" id="reg_password" name="password" class="ms_form_input" placeholder="<?php esc_attr_e('Tạo mật khẩu (ít nhất 8 ký tự)', 'miraculous-music'); ?>" required minlength="8">
                                <span class="ms_toggle_password" data-target="reg_password">
                                    <i class="fa fa-eye"></i>
                                </span>
                            </div>
                            <div class="ms_password_strength" id="password-strength"></div>
                        </div>

                        <div class="ms_form_group">
                            <label for="reg_password_confirm"><?php esc_html_e('Xác nhận mật khẩu', 'miraculous-music'); ?> <span class="required">*</span></label>
                            <div class="ms_password_wrapper">
                                <input type="password" id="reg_password_confirm" name="password_confirm" class="ms_form_input" placeholder="<?php esc_attr_e('Nhập lại mật khẩu', 'miraculous-music'); ?>" required>
                                <span class="ms_toggle_password" data-target="reg_password_confirm">
                                    <i class="fa fa-eye"></i>
                                </span>
                            </div>
                        </div>

                        <div class="ms_form_group">
                            <label class="ms_checkbox">
                                <input type="checkbox" name="terms" value="1" required>
                                <span class="checkmark"></span>
                                <?php printf(
                                    esc_html__('Tôi đồng ý với %sĐiều khoản sử dụng%s và %sChính sách bảo mật%s', 'miraculous-music'),
                                    '<a href="' . esc_url(get_privacy_policy_url()) . '" target="_blank">',
                                    '</a>',
                                    '<a href="' . esc_url(get_privacy_policy_url()) . '" target="_blank">',
                                    '</a>'
                                ); ?>
                            </label>
                        </div>

                        <div class="ms_form_message" id="register-message"></div>

                        <button type="submit" class="ms_btn ms_auth_btn">
                            <span class="btn-text"><?php esc_html_e('Đăng ký', 'miraculous-music'); ?></span>
                            <span class="btn-loading" style="display: none;">
                                <i class="fa fa-spinner fa-spin"></i> <?php esc_html_e('Đang xử lý...', 'miraculous-music'); ?>
                            </span>
                        </button>
                    </form>

                    <div class="ms_auth_divider">
                        <span><?php esc_html_e('hoặc', 'miraculous-music'); ?></span>
                    </div>

                    <div class="ms_social_login">
                        <?php if (function_exists('wsl_render_auth_widget')) : ?>
                            <?php wsl_render_auth_widget(); ?>
                        <?php else : ?>
                            <button type="button" class="ms_social_btn ms_google_btn" disabled>
                                <i class="fa fa-google"></i> <?php esc_html_e('Đăng ký với Google', 'miraculous-music'); ?>
                            </button>
                            <button type="button" class="ms_social_btn ms_facebook_btn" disabled>
                                <i class="fa fa-facebook"></i> <?php esc_html_e('Đăng ký với Facebook', 'miraculous-music'); ?>
                            </button>
                        <?php endif; ?>
                    </div>

                    <div class="ms_auth_footer">
                        <p><?php esc_html_e('Đã có tài khoản?', 'miraculous-music'); ?>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#myModal1" data-bs-dismiss="modal">
                                <?php esc_html_e('Đăng nhập', 'miraculous-music'); ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Forgot Password Modal -->
<div class="ms_auth_modal" id="forgotModal" tabindex="-1" aria-labelledby="forgotModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <button type="button" class="btn-close ms_modal_close" data-bs-dismiss="modal" aria-label="Close">
                <i class="fa fa-times"></i>
            </button>
            <div class="modal-body">
                <div class="ms_auth_wrapper">
                    <div class="ms_auth_header">
                        <h2><?php esc_html_e('Quên mật khẩu?', 'miraculous-music'); ?></h2>
                        <p><?php esc_html_e('Nhập email để nhận link đặt lại mật khẩu', 'miraculous-music'); ?></p>
                    </div>

                    <form id="ms-forgot-form" class="ms_auth_form">
                        <?php wp_nonce_field('miraculous_forgot', 'forgot_nonce'); ?>

                        <div class="ms_form_group">
                            <label for="forgot_email"><?php esc_html_e('Email', 'miraculous-music'); ?></label>
                            <input type="email" id="forgot_email" name="email" class="ms_form_input" placeholder="<?php esc_attr_e('Nhập địa chỉ email đã đăng ký', 'miraculous-music'); ?>" required>
                        </div>

                        <div class="ms_form_message" id="forgot-message"></div>

                        <button type="submit" class="ms_btn ms_auth_btn">
                            <span class="btn-text"><?php esc_html_e('Gửi link đặt lại', 'miraculous-music'); ?></span>
                            <span class="btn-loading" style="display: none;">
                                <i class="fa fa-spinner fa-spin"></i> <?php esc_html_e('Đang xử lý...', 'miraculous-music'); ?>
                            </span>
                        </button>
                    </form>

                    <div class="ms_auth_footer">
                        <p><?php esc_html_e('Nhớ mật khẩu rồi?', 'miraculous-music'); ?>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#myModal1" data-bs-dismiss="modal">
                                <?php esc_html_e('Đăng nhập', 'miraculous-music'); ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
