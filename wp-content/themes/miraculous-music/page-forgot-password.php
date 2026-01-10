<?php
/**
 * Template Name: Forgot Password Page
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
                        <h1><?php esc_html_e('Quên mật khẩu?', 'miraculous-music'); ?></h1>
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
                            <a href="<?php echo esc_url(home_url('/dang-nhap')); ?>">
                                <?php esc_html_e('Đăng nhập', 'miraculous-music'); ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
