<?php
/**
 * Template Name: Chính sách bảo mật
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
        <h1><?php esc_html_e('Chính sách bảo mật', 'miraculous-music'); ?></h1>

        <div class="ms_policy_box">
            <p class="ms_policy_update">Cập nhật lần cuối: <?php echo date('d/m/Y'); ?></p>

            <div class="ms_policy_content">
                <section class="ms_policy_section">
                    <h2>1. Giới thiệu</h2>
                    <p><strong><?php echo esc_html($site_name); ?></strong> cam kết bảo vệ quyền riêng tư của bạn. Chính sách bảo mật này giải thích cách chúng tôi thu thập, sử dụng và bảo vệ thông tin cá nhân của bạn.</p>
                    <p>Bằng việc sử dụng website của chúng tôi, bạn đồng ý với các điều khoản trong chính sách này.</p>
                </section>

                <section class="ms_policy_section">
                    <h2>2. Thông tin chúng tôi thu thập</h2>

                    <h3>2.1. Thông tin bạn cung cấp</h3>
                    <ul>
                        <li><strong>Thông tin đăng ký:</strong> Tên, email, mật khẩu khi tạo tài khoản</li>
                        <li><strong>Thông tin hồ sơ:</strong> Ảnh đại diện, tên hiển thị, thông tin cá nhân khác</li>
                        <li><strong>Nội dung:</strong> Playlist, bình luận, đánh giá bạn tạo ra</li>
                        <li><strong>Liên hệ:</strong> Thông tin khi bạn liên hệ hỗ trợ</li>
                    </ul>

                    <h3>2.2. Thông tin tự động thu thập</h3>
                    <ul>
                        <li><strong>Dữ liệu sử dụng:</strong> Bài hát nghe, thời gian nghe, tương tác</li>
                        <li><strong>Thông tin thiết bị:</strong> Loại thiết bị, hệ điều hành, trình duyệt</li>
                        <li><strong>Địa chỉ IP:</strong> Để xác định vị trí địa lý chung</li>
                        <li><strong>Cookies:</strong> Để ghi nhớ tùy chọn và cải thiện trải nghiệm</li>
                    </ul>
                </section>

                <section class="ms_policy_section">
                    <h2>3. Mục đích sử dụng thông tin</h2>
                    <p>Chúng tôi sử dụng thông tin của bạn để:</p>
                    <ul>
                        <li>Cung cấp và duy trì dịch vụ</li>
                        <li>Cá nhân hóa trải nghiệm nghe nhạc</li>
                        <li>Đề xuất nội dung phù hợp với sở thích</li>
                        <li>Gửi thông báo về cập nhật và tính năng mới</li>
                        <li>Hỗ trợ khách hàng</li>
                        <li>Phân tích và cải thiện dịch vụ</li>
                        <li>Bảo vệ an ninh và ngăn chặn gian lận</li>
                        <li>Tuân thủ các yêu cầu pháp lý</li>
                    </ul>
                </section>

                <section class="ms_policy_section">
                    <h2>4. Chia sẻ thông tin</h2>
                    <p>Chúng tôi <strong>không bán</strong> thông tin cá nhân của bạn. Chúng tôi có thể chia sẻ thông tin với:</p>

                    <h3>4.1. Đối tác dịch vụ</h3>
                    <ul>
                        <li>Nhà cung cấp hosting và lưu trữ</li>
                        <li>Dịch vụ phân tích (Google Analytics)</li>
                        <li>Dịch vụ email marketing</li>
                        <li>Cổng thanh toán (nếu có)</li>
                    </ul>

                    <h3>4.2. Yêu cầu pháp lý</h3>
                    <p>Chúng tôi có thể tiết lộ thông tin khi:</p>
                    <ul>
                        <li>Được yêu cầu bởi pháp luật</li>
                        <li>Bảo vệ quyền lợi hợp pháp của chúng tôi</li>
                        <li>Ngăn chặn hành vi gian lận hoặc bất hợp pháp</li>
                    </ul>
                </section>

                <section class="ms_policy_section">
                    <h2>5. Bảo mật thông tin</h2>
                    <p>Chúng tôi áp dụng các biện pháp bảo mật để bảo vệ thông tin của bạn:</p>
                    <ul>
                        <li><strong>Mã hóa SSL:</strong> Tất cả dữ liệu truyền tải được mã hóa</li>
                        <li><strong>Mã hóa mật khẩu:</strong> Mật khẩu được hash bằng thuật toán an toàn</li>
                        <li><strong>Kiểm soát truy cập:</strong> Chỉ nhân viên được phép mới có quyền truy cập</li>
                        <li><strong>Sao lưu định kỳ:</strong> Dữ liệu được sao lưu thường xuyên</li>
                        <li><strong>Giám sát bảo mật:</strong> Hệ thống được giám sát 24/7</li>
                    </ul>
                </section>

                <section class="ms_policy_section">
                    <h2>6. Cookies và công nghệ theo dõi</h2>
                    <p>Chúng tôi sử dụng cookies để:</p>
                    <ul>
                        <li>Duy trì phiên đăng nhập của bạn</li>
                        <li>Ghi nhớ tùy chọn cài đặt</li>
                        <li>Phân tích lưu lượng truy cập</li>
                        <li>Cải thiện hiệu suất website</li>
                    </ul>
                    <p>Bạn có thể quản lý cookies thông qua cài đặt trình duyệt. Tuy nhiên, việc tắt cookies có thể ảnh hưởng đến một số tính năng.</p>
                </section>

                <section class="ms_policy_section">
                    <h2>7. Quyền của bạn</h2>
                    <p>Bạn có các quyền sau đối với thông tin cá nhân:</p>
                    <ul>
                        <li><strong>Quyền truy cập:</strong> Xem thông tin chúng tôi lưu trữ về bạn</li>
                        <li><strong>Quyền chỉnh sửa:</strong> Cập nhật thông tin không chính xác</li>
                        <li><strong>Quyền xóa:</strong> Yêu cầu xóa tài khoản và dữ liệu</li>
                        <li><strong>Quyền hạn chế:</strong> Hạn chế cách chúng tôi sử dụng dữ liệu</li>
                        <li><strong>Quyền phản đối:</strong> Từ chối nhận email marketing</li>
                        <li><strong>Quyền di chuyển:</strong> Nhận bản sao dữ liệu của bạn</li>
                    </ul>
                    <p>Để thực hiện các quyền này, vui lòng liên hệ: <a href="mailto:<?php echo esc_attr($contact_email); ?>"><?php echo esc_html($contact_email); ?></a></p>
                </section>

                <section class="ms_policy_section">
                    <h2>8. Lưu trữ dữ liệu</h2>
                    <ul>
                        <li>Dữ liệu tài khoản được lưu trữ cho đến khi bạn xóa tài khoản</li>
                        <li>Dữ liệu sử dụng được lưu trữ trong 2 năm</li>
                        <li>Dữ liệu thanh toán được lưu trữ theo quy định pháp luật</li>
                        <li>Sau khi xóa tài khoản, dữ liệu sẽ bị xóa trong vòng 30 ngày</li>
                    </ul>
                </section>

                <section class="ms_policy_section">
                    <h2>9. Trẻ em</h2>
                    <p>Website của chúng tôi không dành cho trẻ em dưới 13 tuổi. Chúng tôi không cố ý thu thập thông tin từ trẻ em. Nếu bạn là phụ huynh và phát hiện con mình đã cung cấp thông tin cho chúng tôi, vui lòng liên hệ để xóa thông tin đó.</p>
                </section>

                <section class="ms_policy_section">
                    <h2>10. Liên kết bên thứ ba</h2>
                    <p>Website có thể chứa liên kết đến các trang web khác. Chúng tôi không chịu trách nhiệm về chính sách bảo mật của các trang web đó. Vui lòng đọc chính sách bảo mật của họ trước khi cung cấp thông tin.</p>
                </section>

                <section class="ms_policy_section">
                    <h2>11. Thay đổi chính sách</h2>
                    <p>Chúng tôi có thể cập nhật chính sách này theo thời gian. Các thay đổi quan trọng sẽ được thông báo qua:</p>
                    <ul>
                        <li>Email đến địa chỉ đăng ký của bạn</li>
                        <li>Thông báo trên website</li>
                    </ul>
                    <p>Việc tiếp tục sử dụng dịch vụ sau khi thay đổi có nghĩa là bạn chấp nhận chính sách mới.</p>
                </section>

                <section class="ms_policy_section">
                    <h2>12. Liên hệ</h2>
                    <p>Nếu bạn có câu hỏi về Chính sách bảo mật này, vui lòng liên hệ:</p>
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
