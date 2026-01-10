<?php
/**
 * Template Name: Login Page
 *
 * @package Miraculous_Music
 * @since 1.0.0
 */

// Redirect if already logged in
if (is_user_logged_in()) {
    wp_redirect(home_url('/'));
    exit;
}

get_header();
?>

<div class="ms_content_wrapper padder_top80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="ms_auth_page_wrapper">
                    <div class="ms_auth_header">
                        <h1><?php esc_html_e('Đăng nhập', 'miraculous-music'); ?></h1>
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
                            <a href="<?php echo esc_url(home_url('/quen-mat-khau')); ?>" class="ms_forgot_link">
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
                            <a href="<?php echo esc_url(home_url('/dang-ky')); ?>">
                                <?php esc_html_e('Đăng ký ngay', 'miraculous-music'); ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
