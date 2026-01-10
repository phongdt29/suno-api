        <!---Footer Start--->
        <div class="ms_footer_wrapper">
            <div class="ms_footer_logo">
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/open_logo.png" alt="<?php bloginfo('name'); ?>">
                </a>
            </div>
            <div class="ms_footer_inner">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <?php if (is_active_sidebar('footer-1')) : ?>
                            <?php dynamic_sidebar('footer-1'); ?>
                        <?php else : ?>
                            <div class="footer_box">
                                <h1 class="footer_title"><?php bloginfo('name'); ?></h1>
                                <p><?php bloginfo('description'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <?php if (is_active_sidebar('footer-2')) : ?>
                            <?php dynamic_sidebar('footer-2'); ?>
                        <?php else : ?>
                            <div class="footer_box footer_app">
                                <h1 class="footer_title"><?php esc_html_e('Tải ứng dụng', 'miraculous-music'); ?></h1>
                                <p><?php esc_html_e('Nghe nhạc mọi lúc mọi nơi với ứng dụng của chúng tôi. Tải ngay!', 'miraculous-music'); ?></p>
                                <a href="#" class="foo_app_btn"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/google_play.jpg" alt="" class="img-fluid"></a>
                                <a href="#" class="foo_app_btn"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/app_store.jpg" alt="" class="img-fluid"></a>
                                <a href="#" class="foo_app_btn"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/windows.jpg" alt="" class="img-fluid"></a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <?php if (is_active_sidebar('footer-3')) : ?>
                            <?php dynamic_sidebar('footer-3'); ?>
                        <?php else : ?>
                            <div class="footer_box footer_subscribe">
                                <h1 class="footer_title"><?php esc_html_e('Đăng ký nhận tin', 'miraculous-music'); ?></h1>
                                <p><?php esc_html_e('Đăng ký nhận bản tin để cập nhật thông tin và ưu đãi mới nhất.', 'miraculous-music'); ?></p>
                                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                                    <input type="hidden" name="action" value="newsletter_subscribe">
                                    <?php wp_nonce_field('newsletter_subscribe', 'newsletter_nonce'); ?>
                                    <div class="form-group">
                                        <input type="text" name="subscriber_name" class="form-control" placeholder="<?php esc_attr_e('Nhập tên của bạn', 'miraculous-music'); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="email" name="subscriber_email" class="form-control" placeholder="<?php esc_attr_e('Nhập email của bạn', 'miraculous-music'); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="ms_btn"><?php esc_html_e('Đăng ký', 'miraculous-music'); ?></button>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <?php if (is_active_sidebar('footer-4')) : ?>
                            <?php dynamic_sidebar('footer-4'); ?>
                        <?php else : ?>
                            <div class="footer_box footer_contacts">
                                <h1 class="footer_title"><?php esc_html_e('Liên hệ', 'miraculous-music'); ?></h1>
                                <ul class="foo_con_info">
                                    <li>
                                        <div class="foo_con_icon">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/svg/phone.svg" alt="">
                                        </div>
                                        <div class="foo_con_data">
                                            <span class="con-title"><?php esc_html_e('Điện thoại :', 'miraculous-music'); ?></span>
                                            <span><?php echo get_theme_mod('contact_phone', '(+1) 202-555-0176'); ?></span>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="foo_con_icon">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/svg/message.svg" alt="">
                                        </div>
                                        <div class="foo_con_data">
                                            <span class="con-title"><?php esc_html_e('Email :', 'miraculous-music'); ?></span>
                                            <span><a href="mailto:<?php echo antispambot(get_bloginfo('admin_email')); ?>"><?php echo antispambot(get_bloginfo('admin_email')); ?></a></span>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="foo_con_icon">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/svg/add.svg" alt="">
                                        </div>
                                        <div class="foo_con_data">
                                            <span class="con-title"><?php esc_html_e('Địa chỉ :', 'miraculous-music'); ?></span>
                                            <span><?php echo get_theme_mod('contact_address', '598 Old House Drive, London'); ?></span>
                                        </div>
                                    </li>
                                </ul>
                                <div class="foo_sharing">
                                    <div class="share_title"><?php esc_html_e('Theo dõi :', 'miraculous-music'); ?></div>
                                    <ul>
                                        <?php if ($fb = get_theme_mod('social_facebook')) : ?>
                                            <li><a href="<?php echo esc_url($fb); ?>" target="_blank"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                                        <?php endif; ?>
                                        <?php if ($li = get_theme_mod('social_linkedin')) : ?>
                                            <li><a href="<?php echo esc_url($li); ?>" target="_blank"><i class="fa fa-linkedin" aria-hidden="true"></i></a></li>
                                        <?php endif; ?>
                                        <?php if ($tw = get_theme_mod('social_twitter')) : ?>
                                            <li><a href="<?php echo esc_url($tw); ?>" target="_blank"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                                        <?php endif; ?>
                                        <?php if ($gp = get_theme_mod('social_google')) : ?>
                                            <li><a href="<?php echo esc_url($gp); ?>" target="_blank"><i class="fa fa-google-plus" aria-hidden="true"></i></a></li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="ms_copyright">
                <div class="footer_border"></div>
                <p>&copy; <?php echo date('Y'); ?> <a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>. <?php esc_html_e('Bảo lưu mọi quyền.', 'miraculous-music'); ?></p>
            </div>
        </div>

        <!----Audio Player---->
        <?php get_template_part('template-parts/player'); ?>

        <!----Auth Modals---->
        <?php get_template_part('template-parts/auth-modals'); ?>

	</div>
	<!----Main Wrapper End---->

<?php wp_footer(); ?>
</body>
</html>
