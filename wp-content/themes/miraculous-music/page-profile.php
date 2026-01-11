<?php
/**
 * Template Name: Profile Page
 *
 * @package Miraculous_Music
 * @since 1.0.0
 */

// Redirect if not logged in
if (!is_user_logged_in()) {
    wp_redirect(home_url('/dang-nhap'));
    exit;
}

$current_user = wp_get_current_user();
$user_id = $current_user->ID;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['profile_nonce'])) {
    if (wp_verify_nonce($_POST['profile_nonce'], 'update_profile')) {
        $errors = array();
        $success = false;

        // Get form data
        $first_name = isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '';
        $last_name = isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '';
        $display_name = isset($_POST['display_name']) ? sanitize_text_field($_POST['display_name']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
        $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

        // Validate email
        if (empty($email)) {
            $errors[] = 'Email không được để trống.';
        } elseif (!is_email($email)) {
            $errors[] = 'Email không hợp lệ.';
        } elseif ($email !== $current_user->user_email && email_exists($email)) {
            $errors[] = 'Email này đã được sử dụng.';
        }

        // Validate password change
        if (!empty($new_password)) {
            if (empty($current_password)) {
                $errors[] = 'Vui lòng nhập mật khẩu hiện tại để đổi mật khẩu.';
            } elseif (!wp_check_password($current_password, $current_user->user_pass, $user_id)) {
                $errors[] = 'Mật khẩu hiện tại không đúng.';
            } elseif (strlen($new_password) < 8) {
                $errors[] = 'Mật khẩu mới phải có ít nhất 8 ký tự.';
            } elseif ($new_password !== $confirm_password) {
                $errors[] = 'Xác nhận mật khẩu không khớp.';
            }
        }

        // Update profile if no errors
        if (empty($errors)) {
            $userdata = array(
                'ID' => $user_id,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'display_name' => $display_name ?: $current_user->user_login,
                'user_email' => $email,
            );

            if (!empty($new_password)) {
                $userdata['user_pass'] = $new_password;
            }

            $result = wp_update_user($userdata);

            if (is_wp_error($result)) {
                $errors[] = $result->get_error_message();
            } else {
                $success = true;
                // Refresh user data
                $current_user = wp_get_current_user();
            }
        }
    }
}

// Handle avatar upload
if (isset($_POST['avatar_nonce']) && wp_verify_nonce($_POST['avatar_nonce'], 'upload_avatar')) {
    if (!empty($_FILES['avatar']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        $attachment_id = media_handle_upload('avatar', 0);

        if (!is_wp_error($attachment_id)) {
            update_user_meta($user_id, 'custom_avatar', $attachment_id);
            $avatar_success = true;
        } else {
            $avatar_error = $attachment_id->get_error_message();
        }
    }
}

// Get user avatar
$custom_avatar_id = get_user_meta($user_id, 'custom_avatar', true);
if ($custom_avatar_id) {
    $avatar_url = wp_get_attachment_image_url($custom_avatar_id, 'thumbnail');
} else {
    $avatar_url = get_avatar_url($user_id, array('size' => 150));
}

get_header();
?>

<div class="ms_content_wrapper ms_profile padder_top80">
    <div class="ms_profile_wrapper">
        <h1><?php esc_html_e('Chỉnh sửa hồ sơ', 'miraculous-music'); ?></h1>

        <?php if (!empty($errors)) : ?>
            <div class="ms_alert alert-error">
                <ul>
                    <?php foreach ($errors as $error) : ?>
                        <li><?php echo esc_html($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)) : ?>
            <div class="ms_alert alert-success">
                <?php esc_html_e('Cập nhật hồ sơ thành công!', 'miraculous-music'); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($avatar_success)) : ?>
            <div class="ms_alert alert-success">
                <?php esc_html_e('Cập nhật ảnh đại diện thành công!', 'miraculous-music'); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($avatar_error)) : ?>
            <div class="ms_alert alert-error">
                <?php echo esc_html($avatar_error); ?>
            </div>
        <?php endif; ?>

        <div class="ms_profile_box">
            <div class="ms_pro_img">
                <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($current_user->display_name); ?>" class="img-fluid">
                <form method="post" enctype="multipart/form-data" class="avatar_form">
                    <?php wp_nonce_field('upload_avatar', 'avatar_nonce'); ?>
                    <div class="pro_img_overlay">
                        <label for="avatar_input">
                            <i class="fa fa-camera"></i>
                        </label>
                        <input type="file" id="avatar_input" name="avatar" accept="image/*" onchange="this.form.submit()">
                    </div>
                </form>
            </div>

            <div class="ms_pro_form">
                <form method="post" id="profile-form">
                    <?php wp_nonce_field('update_profile', 'profile_nonce'); ?>

                    <div class="form-group ms_full">
                        <label><?php esc_html_e('Họ', 'miraculous-music'); ?></label>
                        <input type="text" name="first_name" class="form-control"
                               value="<?php echo esc_attr($current_user->first_name); ?>"
                               placeholder="<?php esc_attr_e('Nhập họ', 'miraculous-music'); ?>">
                    </div>

                    <div class="form-group ms_full">
                        <label><?php esc_html_e('Tên', 'miraculous-music'); ?></label>
                        <input type="text" name="last_name" class="form-control"
                               value="<?php echo esc_attr($current_user->last_name); ?>"
                               placeholder="<?php esc_attr_e('Nhập tên', 'miraculous-music'); ?>">
                    </div>

                    <div class="form-group ms_full">
                        <label><?php esc_html_e('Tên hiển thị', 'miraculous-music'); ?> <span class="required">*</span></label>
                        <input type="text" name="display_name" class="form-control"
                               value="<?php echo esc_attr($current_user->display_name); ?>"
                               placeholder="<?php esc_attr_e('Tên hiển thị công khai', 'miraculous-music'); ?>" required>
                    </div>

                    <div class="form-group ms_full">
                        <label><?php esc_html_e('Email', 'miraculous-music'); ?> <span class="required">*</span></label>
                        <input type="email" name="email" class="form-control"
                               value="<?php echo esc_attr($current_user->user_email); ?>"
                               placeholder="<?php esc_attr_e('Địa chỉ email', 'miraculous-music'); ?>" required>
                    </div>

                    <div class="form-group ms_full">
                        <label><?php esc_html_e('Tên đăng nhập', 'miraculous-music'); ?></label>
                        <input type="text" class="form-control" value="<?php echo esc_attr($current_user->user_login); ?>" disabled>
                        <small class="form-text"><?php esc_html_e('Tên đăng nhập không thể thay đổi.', 'miraculous-music'); ?></small>
                    </div>

                    <hr class="ms_divider">

                    <h3><?php esc_html_e('Đổi mật khẩu', 'miraculous-music'); ?></h3>
                    <p class="form-description"><?php esc_html_e('Để trống nếu không muốn đổi mật khẩu.', 'miraculous-music'); ?></p>

                    <div class="form-group ms_full">
                        <label><?php esc_html_e('Mật khẩu hiện tại', 'miraculous-music'); ?></label>
                        <div class="ms_password_wrapper">
                            <input type="password" name="current_password" id="current_password" class="form-control"
                                   placeholder="<?php esc_attr_e('Nhập mật khẩu hiện tại', 'miraculous-music'); ?>">
                            <span class="ms_toggle_password" data-target="current_password">
                                <i class="fa fa-eye"></i>
                            </span>
                        </div>
                    </div>

                    <div class="form-group ms_full">
                        <label><?php esc_html_e('Mật khẩu mới', 'miraculous-music'); ?></label>
                        <div class="ms_password_wrapper">
                            <input type="password" name="new_password" id="new_password" class="form-control"
                                   placeholder="<?php esc_attr_e('Nhập mật khẩu mới (ít nhất 8 ký tự)', 'miraculous-music'); ?>" minlength="8">
                            <span class="ms_toggle_password" data-target="new_password">
                                <i class="fa fa-eye"></i>
                            </span>
                        </div>
                        <div class="ms_password_strength" id="password-strength"></div>
                    </div>

                    <div class="form-group ms_full">
                        <label><?php esc_html_e('Xác nhận mật khẩu mới', 'miraculous-music'); ?></label>
                        <div class="ms_password_wrapper">
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control"
                                   placeholder="<?php esc_attr_e('Nhập lại mật khẩu mới', 'miraculous-music'); ?>">
                            <span class="ms_toggle_password" data-target="confirm_password">
                                <i class="fa fa-eye"></i>
                            </span>
                        </div>
                    </div>

                    <div class="pro-form-btn form-group ms_full text-center marger_top15">
                        <button type="submit" class="ms_btn" style="min-width: 200px;"><?php esc_html_e('Lưu thay đổi', 'miraculous-music'); ?></button>
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="ms_btn ms_btn_secondary" style="min-width: 200px;"><?php esc_html_e('Hủy', 'miraculous-music'); ?></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Toggle password visibility
    $('.ms_toggle_password').on('click', function() {
        var targetId = $(this).data('target');
        var input = $('#' + targetId);
        var icon = $(this).find('i');

        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Password strength for new password field
    $('#new_password').on('input', function() {
        var password = $(this).val();
        var strengthEl = $('#password-strength');

        if (password.length === 0) {
            strengthEl.html('').removeClass('weak medium strong');
            return;
        }

        var score = 0;
        if (password.length >= 8) score += 1;
        if (password.length >= 12) score += 1;
        if (/[a-z]/.test(password)) score += 1;
        if (/[A-Z]/.test(password)) score += 1;
        if (/[0-9]/.test(password)) score += 1;
        if (/[^a-zA-Z0-9]/.test(password)) score += 1;

        var level, percent, text;
        if (score <= 2) {
            level = 'weak'; percent = 33; text = 'Yếu';
        } else if (score <= 4) {
            level = 'medium'; percent = 66; text = 'Trung bình';
        } else {
            level = 'strong'; percent = 100; text = 'Mạnh';
        }

        var html = '<div class="strength-bar ' + level + '">';
        html += '<span class="strength-fill" style="width: ' + percent + '%"></span>';
        html += '</div>';
        html += '<span class="strength-text">' + text + '</span>';

        strengthEl.html(html).removeClass('weak medium strong').addClass(level);
    });
});
</script>

<?php get_footer(); ?>
