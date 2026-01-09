<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="MobileOptimized" content="320">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

    <!----Main Wrapper Start---->
    <div class="ms_main_wrapper">
        <!---Side Menu Start--->
        <div class="ms_sidemenu_wrapper">
            <div class="ms_nav_close">
                <i class="fa fa-angle-right" aria-hidden="true"></i>
            </div>
            <div class="ms_sidemenu_inner">
                <div class="ms_logo_inner">
                    <div class="ms_logo">
                        <?php if (has_custom_logo()) : ?>
                            <?php the_custom_logo(); ?>
                        <?php else : ?>
                            <a href="<?php echo esc_url(home_url('/')); ?>">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png" alt="<?php bloginfo('name'); ?>" class="img-fluid"/>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="ms_logo_open">
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/open_logo.png" alt="<?php bloginfo('name'); ?>" class="img-fluid"/>
                        </a>
                    </div>
                </div>
                <div class="ms_nav_wrapper">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'sidebar',
                        'container' => false,
                        'menu_class' => '',
                        'fallback_cb' => 'miraculous_music_default_sidebar_menu',
                    ));
                    ?>
                </div>
            </div>
        </div>
		<!---Header--->
		<div class="ms_header">
			<div class="ms_top_left">
				<div class="ms_top_search">
					<form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
						<input type="text" class="form-control" name="s" placeholder="<?php esc_attr_e('Search Music Here..', 'miraculous-music'); ?>" value="<?php echo get_search_query(); ?>">
						<span class="search_icon">
							<img src="<?php echo get_template_directory_uri(); ?>/assets/images/svg/search.svg" alt="">
						</span>
					</form>
				</div>
				<div class="ms_top_trend">
					<span><a href="#" class="ms_color"><?php esc_html_e('Trending Songs :', 'miraculous-music'); ?></a></span>
					<span class="top_marquee">
						<a href="#">
							<?php
							// Get trending songs or display default text
							$trending_text = get_theme_mod('trending_songs_text', 'Dream your moments, Until I Met You, Gimme Some Courage, Dark Alley (+8 More)');
							echo esc_html($trending_text);
							?>
						</a>
					</span>
				</div>
			</div>
			<div class="ms_top_right">
				<?php if (is_active_sidebar('header-widgets')) : ?>
					<?php dynamic_sidebar('header-widgets'); ?>
				<?php else : ?>
					<div class="ms_top_lang">
						<span data-bs-toggle="modal" data-bs-target="#lang_modal"><?php esc_html_e('languages', 'miraculous-music'); ?> <img src="<?php echo get_template_directory_uri(); ?>/assets/images/svg/lang.svg" alt=""></span>
					</div>
					<div class="ms_top_btn">
						<?php if (is_user_logged_in()) : ?>
							<a href="<?php echo wp_logout_url(home_url()); ?>" class="ms_btn reg_btn"><span><?php esc_html_e('Logout', 'miraculous-music'); ?></span></a>
							<a href="<?php echo get_edit_user_link(); ?>" class="ms_btn"><?php esc_html_e('Profile', 'miraculous-music'); ?></a>
						<?php else : ?>
							<a href="<?php echo wp_registration_url(); ?>" class="ms_btn reg_btn" data-bs-toggle="modal" data-bs-target="#myModal"><span><?php esc_html_e('register', 'miraculous-music'); ?></span></a>
							<a href="<?php echo wp_login_url(); ?>" class="ms_btn" data-bs-toggle="modal" data-bs-target="#myModal1"><?php esc_html_e('login', 'miraculous-music'); ?></a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
