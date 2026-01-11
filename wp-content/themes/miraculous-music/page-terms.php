<?php
/**
 * Template Name: Điều khoản sử dụng
 *
 * @package Miraculous_Music
 * @since 1.0.0
 */

get_header();

$site_name = get_bloginfo('name');
$site_url = home_url('/');
$contact_email = get_option('admin_email');
?>

<div class="ms_content_wrapper ms_policy_page padder_top80">
    <div class="ms_policy_wrapper">
        <h1><?php esc_html_e('Điều khoản sử dụng', 'miraculous-music'); ?></h1>

        <div class="ms_policy_box">
            <p class="ms_policy_update">Cập nhật lần cuối: <?php echo date('d/m/Y'); ?></p>

            <div class="ms_policy_content">
                <section class="ms_policy_section">
                    <h2>1. Giới thiệu</h2>
                    <p>Chào mừng bạn đến với <strong><?php echo esc_html($site_name); ?></strong>. Bằng việc truy cập và sử dụng website này, bạn đồng ý tuân thủ và bị ràng buộc bởi các điều khoản và điều kiện sau đây.</p>
                    <p>Vui lòng đọc kỹ các điều khoản này trước khi sử dụng dịch vụ của chúng tôi.</p>
                </section>

                <section class="ms_policy_section">
                    <h2>2. Định nghĩa</h2>
                    <ul>
                        <li><strong>"Website"</strong> là trang web <?php echo esc_html($site_name); ?> tại địa chỉ <?php echo esc_url($site_url); ?></li>
                        <li><strong>"Người dùng"</strong> là bất kỳ cá nhân nào truy cập và sử dụng Website</li>
                        <li><strong>"Nội dung"</strong> bao gồm nhạc, hình ảnh, văn bản và tất cả các tài liệu khác trên Website</li>
                        <li><strong>"Dịch vụ"</strong> là các tính năng và chức năng được cung cấp trên Website</li>
                    </ul>
                </section>

                <section class="ms_policy_section">
                    <h2>3. Điều kiện sử dụng</h2>
                    <h3>3.1. Đăng ký tài khoản</h3>
                    <ul>
                        <li>Bạn phải từ 13 tuổi trở lên để đăng ký tài khoản</li>
                        <li>Thông tin đăng ký phải chính xác và đầy đủ</li>
                        <li>Bạn chịu trách nhiệm bảo mật thông tin tài khoản của mình</li>
                        <li>Không chia sẻ tài khoản với người khác</li>
                    </ul>

                    <h3>3.2. Hành vi được phép</h3>
                    <ul>
                        <li>Nghe nhạc trực tuyến cho mục đích cá nhân</li>
                        <li>Tạo và quản lý playlist cá nhân</li>
                        <li>Chia sẻ nội dung thông qua các tính năng được cung cấp</li>
                        <li>Tương tác với cộng đồng một cách văn minh</li>
                    </ul>

                    <h3>3.3. Hành vi bị cấm</h3>
                    <ul>
                        <li>Sao chép, tải xuống hoặc phân phối nội dung trái phép</li>
                        <li>Sử dụng bot, spider hoặc các công cụ tự động khác</li>
                        <li>Can thiệp vào hoạt động của Website</li>
                        <li>Đăng tải nội dung vi phạm pháp luật hoặc xúc phạm</li>
                        <li>Giả mạo danh tính hoặc thông tin</li>
                    </ul>
                </section>

                <section class="ms_policy_section">
                    <h2>4. Quyền sở hữu trí tuệ</h2>
                    <p>Tất cả nội dung trên Website bao gồm nhưng không giới hạn: nhạc, hình ảnh, logo, thiết kế, văn bản đều thuộc quyền sở hữu của <?php echo esc_html($site_name); ?> hoặc các đối tác được cấp phép.</p>
                    <p>Người dùng không được:</p>
                    <ul>
                        <li>Sao chép, sửa đổi hoặc phân phối nội dung mà không có sự cho phép</li>
                        <li>Sử dụng nội dung cho mục đích thương mại</li>
                        <li>Gỡ bỏ các thông báo bản quyền hoặc nhãn hiệu</li>
                    </ul>
                </section>

                <section class="ms_policy_section">
                    <h2>5. Nội dung người dùng</h2>
                    <p>Khi bạn đăng tải nội dung lên Website:</p>
                    <ul>
                        <li>Bạn giữ quyền sở hữu nội dung của mình</li>
                        <li>Bạn cấp cho chúng tôi quyền sử dụng, hiển thị và phân phối nội dung đó</li>
                        <li>Bạn đảm bảo nội dung không vi phạm quyền của bên thứ ba</li>
                        <li>Chúng tôi có quyền xóa nội dung vi phạm mà không cần thông báo</li>
                    </ul>
                </section>

                <section class="ms_policy_section">
                    <h2>6. Giới hạn trách nhiệm</h2>
                    <p><?php echo esc_html($site_name); ?> không chịu trách nhiệm về:</p>
                    <ul>
                        <li>Sự gián đoạn hoặc lỗi của dịch vụ</li>
                        <li>Mất mát dữ liệu do sự cố kỹ thuật</li>
                        <li>Nội dung do người dùng khác đăng tải</li>
                        <li>Các thiệt hại gián tiếp phát sinh từ việc sử dụng Website</li>
                    </ul>
                </section>

                <section class="ms_policy_section">
                    <h2>7. Chấm dứt</h2>
                    <p>Chúng tôi có quyền:</p>
                    <ul>
                        <li>Tạm ngưng hoặc chấm dứt tài khoản vi phạm điều khoản</li>
                        <li>Từ chối cung cấp dịch vụ cho bất kỳ ai vì bất kỳ lý do gì</li>
                        <li>Thay đổi hoặc ngừng cung cấp dịch vụ mà không cần thông báo trước</li>
                    </ul>
                </section>

                <section class="ms_policy_section">
                    <h2>8. Thay đổi điều khoản</h2>
                    <p>Chúng tôi có quyền thay đổi các điều khoản này bất cứ lúc nào. Các thay đổi sẽ có hiệu lực ngay khi được đăng tải trên Website.</p>
                    <p>Việc tiếp tục sử dụng Website sau khi thay đổi có nghĩa là bạn chấp nhận các điều khoản mới.</p>
                </section>

                <section class="ms_policy_section">
                    <h2>9. Luật áp dụng</h2>
                    <p>Các điều khoản này được điều chỉnh theo pháp luật Việt Nam. Mọi tranh chấp sẽ được giải quyết tại tòa án có thẩm quyền tại Việt Nam.</p>
                </section>

                <section class="ms_policy_section">
                    <h2>10. Liên hệ</h2>
                    <p>Nếu bạn có bất kỳ câu hỏi nào về Điều khoản sử dụng này, vui lòng liên hệ:</p>
                    <ul>
                        <li><strong>Email:</strong> <a href="mailto:<?php echo esc_attr($contact_email); ?>"><?php echo esc_html($contact_email); ?></a></li>
                        <li><strong>Website:</strong> <a href="<?php echo esc_url($site_url); ?>"><?php echo esc_html($site_url); ?></a></li>
                    </ul>
                </section>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
