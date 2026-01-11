<?php
/**
 * Miraculous Music Theme Functions
 *
 * @package Miraculous_Music
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme Setup
 */
function miraculous_music_setup() {
    // Load text domain for translations
    load_theme_textdomain('miraculous-music', get_template_directory() . '/languages');

    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    add_theme_support('custom-logo');
    add_theme_support('customize-selective-refresh-widgets');

    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'miraculous-music'),
        'sidebar' => __('Sidebar Menu', 'miraculous-music'),
    ));

    // Set content width
    if (!isset($content_width)) {
        $content_width = 1200;
    }
}
add_action('after_setup_theme', 'miraculous_music_setup');

/**
 * Enqueue styles and scripts
 */
function miraculous_music_scripts() {
    $theme_version = '1.0.0';

    // Styles
    wp_enqueue_style('miraculous-fonts', get_template_directory_uri() . '/assets/css/fonts.css', array(), $theme_version);
    wp_enqueue_style('bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css', array(), $theme_version);
    wp_enqueue_style('font-awesome', get_template_directory_uri() . '/assets/css/font-awesome.min.css', array(), $theme_version);
    wp_enqueue_style('swiper', get_template_directory_uri() . '/assets/js/plugins/swiper/css/swiper.min.css', array(), $theme_version);
    wp_enqueue_style('nice-select', get_template_directory_uri() . '/assets/js/plugins/nice_select/nice-select.css', array(), $theme_version);
    wp_enqueue_style('volume-css', get_template_directory_uri() . '/assets/js/plugins/player/volume.css', array(), $theme_version);
    wp_enqueue_style('mCustomScrollbar', get_template_directory_uri() . '/assets/js/plugins/scroll/jquery.mCustomScrollbar.css', array(), $theme_version);
    wp_enqueue_style('miraculous-style', get_template_directory_uri() . '/assets/css/style.css', array(), $theme_version);
    wp_enqueue_style('suno-player-fix', get_template_directory_uri() . '/assets/css/suno-player-fix.css', array('miraculous-style'), $theme_version);
    wp_enqueue_style('auth-modal', get_template_directory_uri() . '/assets/css/auth-modal.css', array('miraculous-style'), $theme_version);
    wp_enqueue_style('profile-fix', get_template_directory_uri() . '/assets/css/profile-fix.css', array('miraculous-style'), $theme_version);
    wp_enqueue_style('policy-pages', get_template_directory_uri() . '/assets/css/policy-pages.css', array('miraculous-style'), $theme_version);

    // Scripts
    wp_enqueue_script('jquery');
    wp_enqueue_script('bootstrap', get_template_directory_uri() . '/assets/js/bootstrap.min.js', array('jquery'), $theme_version, true);
    wp_enqueue_script('swiper', get_template_directory_uri() . '/assets/js/plugins/swiper/js/swiper.min.js', array('jquery'), $theme_version, true);
    // jPlayer scripts - MUST load in correct order: jPlayer core -> playlist -> audio-player
    wp_enqueue_script('jplayer', get_template_directory_uri() . '/assets/js/plugins/player/jquery.jplayer.min.js', array('jquery'), $theme_version, true);
    wp_enqueue_script('jplayer-playlist', get_template_directory_uri() . '/assets/js/plugins/player/jplayer.playlist.min.js', array('jquery', 'jplayer'), $theme_version, true);
    wp_enqueue_script('audio-player', get_template_directory_uri() . '/assets/js/plugins/player/audio-player.js', array('jquery', 'jplayer', 'jplayer-playlist'), $theme_version, true);
    wp_enqueue_script('volume-js', get_template_directory_uri() . '/assets/js/plugins/player/volume.js', array('jquery'), $theme_version, true);
    wp_enqueue_script('nice-select', get_template_directory_uri() . '/assets/js/plugins/nice_select/jquery.nice-select.min.js', array('jquery'), $theme_version, true);
    wp_enqueue_script('mCustomScrollbar', get_template_directory_uri() . '/assets/js/plugins/scroll/jquery.mCustomScrollbar.js', array('jquery'), $theme_version, true);
    wp_enqueue_script('miraculous-custom', get_template_directory_uri() . '/assets/js/custom.js', array('jquery'), $theme_version, true);
    wp_enqueue_script('suno-api-js', get_template_directory_uri() . '/assets/js/suno-api.js', array('jquery', 'miraculous-custom'), $theme_version, true);
    wp_enqueue_script('auth-js', get_template_directory_uri() . '/assets/js/auth.js', array('jquery', 'miraculous-custom'), $theme_version, true);

    // Localize script immediately after enqueue
    wp_localize_script('miraculous-custom', 'miraculousAjax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('miraculous_ajax'),
        'api_url' => miraculous_get_api_url(),
        'home_url' => home_url('/'),
        'theme_url' => get_template_directory_uri(),
    ));
}
add_action('wp_enqueue_scripts', 'miraculous_music_scripts');

/**
 * Register Widget Areas
 */
function miraculous_music_widgets_init() {
    register_sidebar(array(
        'name'          => __('Sidebar', 'miraculous-music'),
        'id'            => 'sidebar-1',
        'description'   => __('Add widgets here to appear in your sidebar.', 'miraculous-music'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => __('Footer 1', 'miraculous-music'),
        'id'            => 'footer-1',
        'description'   => __('Footer widget area 1', 'miraculous-music'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => __('Footer 2', 'miraculous-music'),
        'id'            => 'footer-2',
        'description'   => __('Footer widget area 2', 'miraculous-music'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => __('Footer 3', 'miraculous-music'),
        'id'            => 'footer-3',
        'description'   => __('Footer widget area 3', 'miraculous-music'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => __('Footer 4', 'miraculous-music'),
        'id'            => 'footer-4',
        'description'   => __('Footer widget area 4', 'miraculous-music'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'miraculous_music_widgets_init');

/**
 * Theme Customizer Settings
 */
function miraculous_music_customize_register($wp_customize) {
    // Banner Section
    $wp_customize->add_section('miraculous_banner', array(
        'title'    => __('Banner Settings', 'miraculous-music'),
        'priority' => 30,
    ));

    $wp_customize->add_setting('banner_title', array(
        'default'           => 'Listen Millions of songs for Free!',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('banner_title', array(
        'label'   => __('Banner Title', 'miraculous-music'),
        'section' => 'miraculous_banner',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('banner_description', array(
        'default'           => 'Nowhere else provides the most listening services than here. Enjoy Your day!',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('banner_description', array(
        'label'   => __('Banner Description', 'miraculous-music'),
        'section' => 'miraculous_banner',
        'type'    => 'textarea',
    ));

    $wp_customize->add_setting('banner_background', array(
        'default'           => get_template_directory_uri() . '/assets/images/banner.png',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'banner_background', array(
        'label'   => __('Banner Image', 'miraculous-music'),
        'section' => 'miraculous_banner',
    )));

    // Advertisement Section
    $wp_customize->add_section('miraculous_advertisement', array(
        'title'    => __('Advertisement Settings', 'miraculous-music'),
        'priority' => 35,
    ));

    // Advertisement 1
    $wp_customize->add_setting('show_adv_banner', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('show_adv_banner', array(
        'label'   => __('Show Advertisement 1', 'miraculous-music'),
        'section' => 'miraculous_advertisement',
        'type'    => 'checkbox',
    ));

    $wp_customize->add_setting('adv_banner_image', array(
        'default'           => get_template_directory_uri() . '/assets/images/adv.jpg',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'adv_banner_image', array(
        'label'   => __('Advertisement 1 Image', 'miraculous-music'),
        'section' => 'miraculous_advertisement',
    )));

    $wp_customize->add_setting('adv_banner_link', array(
        'default'           => '#',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('adv_banner_link', array(
        'label'   => __('Advertisement 1 Link', 'miraculous-music'),
        'section' => 'miraculous_advertisement',
        'type'    => 'url',
    ));

    // Advertisement 2
    $wp_customize->add_setting('show_adv_banner_2', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('show_adv_banner_2', array(
        'label'   => __('Show Advertisement 2', 'miraculous-music'),
        'section' => 'miraculous_advertisement',
        'type'    => 'checkbox',
    ));

    $wp_customize->add_setting('adv_banner_image_2', array(
        'default'           => get_template_directory_uri() . '/assets/images/adv1.jpg',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'adv_banner_image_2', array(
        'label'   => __('Advertisement 2 Image', 'miraculous-music'),
        'section' => 'miraculous_advertisement',
    )));

    $wp_customize->add_setting('adv_banner_link_2', array(
        'default'           => '#',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('adv_banner_link_2', array(
        'label'   => __('Advertisement 2 Link', 'miraculous-music'),
        'section' => 'miraculous_advertisement',
        'type'    => 'url',
    ));
    // SEO Section
    $wp_customize->add_section('miraculous_seo', array(
        'title'    => __('SEO Settings', 'miraculous-music'),
        'priority' => 25,
        'description' => __('Cài đặt SEO cho website. Meta description và keywords sẽ được hiển thị trên trang chủ và các trang không có SEO riêng.', 'miraculous-music'),
    ));

    // Meta Description
    $wp_customize->add_setting('seo_meta_description', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('seo_meta_description', array(
        'label'       => __('Meta Description', 'miraculous-music'),
        'description' => __('Mô tả ngắn gọn về website (150-160 ký tự)', 'miraculous-music'),
        'section'     => 'miraculous_seo',
        'type'        => 'textarea',
    ));

    // Meta Keywords
    $wp_customize->add_setting('seo_meta_keywords', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('seo_meta_keywords', array(
        'label'       => __('Meta Keywords', 'miraculous-music'),
        'description' => __('Từ khóa SEO, cách nhau bởi dấu phẩy', 'miraculous-music'),
        'section'     => 'miraculous_seo',
        'type'        => 'text',
    ));

    // OG Image
    $wp_customize->add_setting('seo_og_image', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'seo_og_image', array(
        'label'       => __('OG Image (Social Share)', 'miraculous-music'),
        'description' => __('Hình ảnh hiển thị khi chia sẻ lên mạng xã hội (1200x630px)', 'miraculous-music'),
        'section'     => 'miraculous_seo',
    )));

    // Google Site Verification
    $wp_customize->add_setting('seo_google_verification', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('seo_google_verification', array(
        'label'       => __('Google Site Verification', 'miraculous-music'),
        'description' => __('Mã xác minh Google Search Console', 'miraculous-music'),
        'section'     => 'miraculous_seo',
        'type'        => 'text',
    ));

    // Bing Site Verification
    $wp_customize->add_setting('seo_bing_verification', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('seo_bing_verification', array(
        'label'       => __('Bing Site Verification', 'miraculous-music'),
        'description' => __('Mã xác minh Bing Webmaster Tools', 'miraculous-music'),
        'section'     => 'miraculous_seo',
        'type'        => 'text',
    ));

    // Facebook App ID
    $wp_customize->add_setting('seo_facebook_app_id', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('seo_facebook_app_id', array(
        'label'       => __('Facebook App ID', 'miraculous-music'),
        'description' => __('ID ứng dụng Facebook (để tracking)', 'miraculous-music'),
        'section'     => 'miraculous_seo',
        'type'        => 'text',
    ));

    // Twitter Handle
    $wp_customize->add_setting('seo_twitter_handle', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('seo_twitter_handle', array(
        'label'       => __('Twitter Handle', 'miraculous-music'),
        'description' => __('Tên tài khoản Twitter (VD: @username)', 'miraculous-music'),
        'section'     => 'miraculous_seo',
        'type'        => 'text',
    ));

    // Robots Meta
    $wp_customize->add_setting('seo_robots_index', array(
        'default'           => 'index',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('seo_robots_index', array(
        'label'   => __('Robots Index', 'miraculous-music'),
        'section' => 'miraculous_seo',
        'type'    => 'select',
        'choices' => array(
            'index'   => __('Index (Cho phép index)', 'miraculous-music'),
            'noindex' => __('NoIndex (Không cho index)', 'miraculous-music'),
        ),
    ));

    $wp_customize->add_setting('seo_robots_follow', array(
        'default'           => 'follow',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('seo_robots_follow', array(
        'label'   => __('Robots Follow', 'miraculous-music'),
        'section' => 'miraculous_seo',
        'type'    => 'select',
        'choices' => array(
            'follow'   => __('Follow (Theo dõi links)', 'miraculous-music'),
            'nofollow' => __('NoFollow (Không theo dõi links)', 'miraculous-music'),
        ),
    ));

    // Analytics Section
    $wp_customize->add_section('miraculous_analytics', array(
        'title'    => __('Analytics & Tracking', 'miraculous-music'),
        'priority' => 26,
        'description' => __('Thêm mã tracking Google Analytics, Facebook Pixel, v.v.', 'miraculous-music'),
    ));

    // Google Analytics ID
    $wp_customize->add_setting('analytics_google_id', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('analytics_google_id', array(
        'label'       => __('Google Analytics ID', 'miraculous-music'),
        'description' => __('VD: G-XXXXXXXXXX hoặc UA-XXXXXXXX-X', 'miraculous-music'),
        'section'     => 'miraculous_analytics',
        'type'        => 'text',
    ));

    // Google Tag Manager ID
    $wp_customize->add_setting('analytics_gtm_id', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('analytics_gtm_id', array(
        'label'       => __('Google Tag Manager ID', 'miraculous-music'),
        'description' => __('VD: GTM-XXXXXXX', 'miraculous-music'),
        'section'     => 'miraculous_analytics',
        'type'        => 'text',
    ));

    // Facebook Pixel ID
    $wp_customize->add_setting('analytics_fb_pixel', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('analytics_fb_pixel', array(
        'label'       => __('Facebook Pixel ID', 'miraculous-music'),
        'description' => __('ID Facebook Pixel để tracking', 'miraculous-music'),
        'section'     => 'miraculous_analytics',
        'type'        => 'text',
    ));

    // Custom Head Scripts
    $wp_customize->add_setting('analytics_head_scripts', array(
        'default'           => '',
        'sanitize_callback' => 'miraculous_sanitize_scripts',
    ));
    $wp_customize->add_control('analytics_head_scripts', array(
        'label'       => __('Custom Scripts (Head)', 'miraculous-music'),
        'description' => __('Thêm mã script vào thẻ &lt;head&gt;', 'miraculous-music'),
        'section'     => 'miraculous_analytics',
        'type'        => 'textarea',
    ));

    // Custom Footer Scripts
    $wp_customize->add_setting('analytics_footer_scripts', array(
        'default'           => '',
        'sanitize_callback' => 'miraculous_sanitize_scripts',
    ));
    $wp_customize->add_control('analytics_footer_scripts', array(
        'label'       => __('Custom Scripts (Footer)', 'miraculous-music'),
        'description' => __('Thêm mã script trước thẻ &lt;/body&gt;', 'miraculous-music'),
        'section'     => 'miraculous_analytics',
        'type'        => 'textarea',
    ));
}
add_action('customize_register', 'miraculous_music_customize_register');

/**
 * Sanitize scripts - allow script tags
 */
function miraculous_sanitize_scripts($input) {
    return $input; // Allow scripts (only admins can access customizer)
}

/**
 * Output SEO Meta Tags
 * Only output if Yoast SEO or other SEO plugins are not active
 */
function miraculous_music_seo_meta_tags() {
    // Skip if Yoast SEO or other popular SEO plugins are active
    if (defined('WPSEO_VERSION') || class_exists('RankMath') || class_exists('JENGA_SEO')) {
        return;
    }

    $meta_description = get_theme_mod('seo_meta_description', '');
    $meta_keywords = get_theme_mod('seo_meta_keywords', '');
    $og_image = get_theme_mod('seo_og_image', '');
    $google_verification = get_theme_mod('seo_google_verification', '');
    $bing_verification = get_theme_mod('seo_bing_verification', '');
    $fb_app_id = get_theme_mod('seo_facebook_app_id', '');
    $twitter_handle = get_theme_mod('seo_twitter_handle', '');
    $robots_index = get_theme_mod('seo_robots_index', 'index');
    $robots_follow = get_theme_mod('seo_robots_follow', 'follow');

    // Default description from site tagline if not set
    if (empty($meta_description)) {
        $meta_description = get_bloginfo('description');
    }

    // For single posts/pages, use excerpt if available
    if (is_singular() && has_excerpt()) {
        $meta_description = wp_strip_all_tags(get_the_excerpt());
    }

    // Robots meta
    echo '<meta name="robots" content="' . esc_attr($robots_index) . ', ' . esc_attr($robots_follow) . '">' . "\n";

    // Canonical URL
    if (is_singular()) {
        echo '<link rel="canonical" href="' . esc_url(get_permalink()) . '">' . "\n";
    } elseif (is_home() || is_front_page()) {
        echo '<link rel="canonical" href="' . esc_url(home_url('/')) . '">' . "\n";
    }

    // Output meta description
    if (!empty($meta_description)) {
        echo '<meta name="description" content="' . esc_attr($meta_description) . '">' . "\n";
    }

    // Output meta keywords
    if (!empty($meta_keywords)) {
        echo '<meta name="keywords" content="' . esc_attr($meta_keywords) . '">' . "\n";
    }

    // Output Google verification
    if (!empty($google_verification)) {
        echo '<meta name="google-site-verification" content="' . esc_attr($google_verification) . '">' . "\n";
    }

    // Output Bing verification
    if (!empty($bing_verification)) {
        echo '<meta name="msvalidate.01" content="' . esc_attr($bing_verification) . '">' . "\n";
    }

    // Open Graph tags
    echo '<meta property="og:locale" content="' . esc_attr(get_locale()) . '">' . "\n";
    echo '<meta property="og:type" content="' . (is_singular() ? 'article' : 'website') . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";

    if (!empty($fb_app_id)) {
        echo '<meta property="fb:app_id" content="' . esc_attr($fb_app_id) . '">' . "\n";
    }

    if (is_singular()) {
        echo '<meta property="og:title" content="' . esc_attr(get_the_title()) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url(get_permalink()) . '">' . "\n";

        if (has_post_thumbnail()) {
            $thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
            if ($thumbnail) {
                echo '<meta property="og:image" content="' . esc_url($thumbnail[0]) . '">' . "\n";
                echo '<meta property="og:image:width" content="' . esc_attr($thumbnail[1]) . '">' . "\n";
                echo '<meta property="og:image:height" content="' . esc_attr($thumbnail[2]) . '">' . "\n";
            }
        } elseif (!empty($og_image)) {
            echo '<meta property="og:image" content="' . esc_url($og_image) . '">' . "\n";
        }
    } else {
        echo '<meta property="og:title" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url(home_url('/')) . '">' . "\n";

        if (!empty($og_image)) {
            echo '<meta property="og:image" content="' . esc_url($og_image) . '">' . "\n";
        }
    }

    if (!empty($meta_description)) {
        echo '<meta property="og:description" content="' . esc_attr($meta_description) . '">' . "\n";
    }

    // Twitter Card
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr(is_singular() ? get_the_title() : get_bloginfo('name')) . '">' . "\n";

    if (!empty($twitter_handle)) {
        echo '<meta name="twitter:site" content="' . esc_attr($twitter_handle) . '">' . "\n";
        echo '<meta name="twitter:creator" content="' . esc_attr($twitter_handle) . '">' . "\n";
    }

    if (!empty($meta_description)) {
        echo '<meta name="twitter:description" content="' . esc_attr($meta_description) . '">' . "\n";
    }
}
add_action('wp_head', 'miraculous_music_seo_meta_tags', 1);

/**
 * Output Analytics Scripts in Head
 */
function miraculous_music_analytics_head() {
    // Skip if in admin or customizer preview
    if (is_admin() || is_customize_preview()) {
        return;
    }

    $ga_id = get_theme_mod('analytics_google_id', '');
    $gtm_id = get_theme_mod('analytics_gtm_id', '');
    $fb_pixel = get_theme_mod('analytics_fb_pixel', '');
    $head_scripts = get_theme_mod('analytics_head_scripts', '');

    // Google Tag Manager (head)
    if (!empty($gtm_id)) {
        echo "<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','" . esc_js($gtm_id) . "');</script>
<!-- End Google Tag Manager -->\n";
    }

    // Google Analytics 4
    if (!empty($ga_id) && strpos($ga_id, 'G-') === 0) {
        echo "<!-- Google Analytics 4 -->
<script async src=\"https://www.googletagmanager.com/gtag/js?id=" . esc_attr($ga_id) . "\"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '" . esc_js($ga_id) . "');
</script>
<!-- End Google Analytics 4 -->\n";
    }
    // Universal Analytics (deprecated but still supported)
    elseif (!empty($ga_id) && strpos($ga_id, 'UA-') === 0) {
        echo "<!-- Universal Analytics -->
<script async src=\"https://www.google-analytics.com/analytics.js\"></script>
<script>
window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};ga.l=+new Date;
ga('create', '" . esc_js($ga_id) . "', 'auto');
ga('send', 'pageview');
</script>
<!-- End Universal Analytics -->\n";
    }

    // Facebook Pixel
    if (!empty($fb_pixel)) {
        echo "<!-- Facebook Pixel -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '" . esc_js($fb_pixel) . "');
fbq('track', 'PageView');
</script>
<noscript><img height=\"1\" width=\"1\" style=\"display:none\"
src=\"https://www.facebook.com/tr?id=" . esc_attr($fb_pixel) . "&ev=PageView&noscript=1\"/></noscript>
<!-- End Facebook Pixel -->\n";
    }

    // Custom head scripts
    if (!empty($head_scripts)) {
        echo $head_scripts . "\n";
    }
}
add_action('wp_head', 'miraculous_music_analytics_head', 2);

/**
 * Output Google Tag Manager noscript in body
 */
function miraculous_music_gtm_body() {
    if (is_admin() || is_customize_preview()) {
        return;
    }

    $gtm_id = get_theme_mod('analytics_gtm_id', '');

    if (!empty($gtm_id)) {
        echo '<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . esc_attr($gtm_id) . '"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->' . "\n";
    }
}
add_action('wp_body_open', 'miraculous_music_gtm_body', 1);

/**
 * Output Custom Footer Scripts
 */
function miraculous_music_footer_scripts() {
    if (is_admin() || is_customize_preview()) {
        return;
    }

    $footer_scripts = get_theme_mod('analytics_footer_scripts', '');

    if (!empty($footer_scripts)) {
        echo $footer_scripts . "\n";
    }
}
add_action('wp_footer', 'miraculous_music_footer_scripts', 999);

/**
 * Register Custom Post Types
 */
function miraculous_music_register_post_types() {
    // Music/Songs Post Type
    register_post_type('music', array(
        'labels' => array(
            'name' => __('Music', 'miraculous-music'),
            'singular_name' => __('Song', 'miraculous-music'),
            'add_new' => __('Add New Song', 'miraculous-music'),
            'add_new_item' => __('Add New Song', 'miraculous-music'),
            'edit_item' => __('Edit Song', 'miraculous-music'),
            'new_item' => __('New Song', 'miraculous-music'),
            'view_item' => __('View Song', 'miraculous-music'),
            'search_items' => __('Search Songs', 'miraculous-music'),
        ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-format-audio',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'comments'),
        'rewrite' => array('slug' => 'music'),
    ));

    // Albums Post Type
    register_post_type('album', array(
        'labels' => array(
            'name' => __('Albums', 'miraculous-music'),
            'singular_name' => __('Album', 'miraculous-music'),
            'add_new' => __('Add New Album', 'miraculous-music'),
        ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-album',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'rewrite' => array('slug' => 'albums'),
    ));

    // Artists Post Type
    register_post_type('artist', array(
        'labels' => array(
            'name' => __('Artists', 'miraculous-music'),
            'singular_name' => __('Artist', 'miraculous-music'),
            'add_new' => __('Add New Artist', 'miraculous-music'),
        ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-admin-users',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'rewrite' => array('slug' => 'artists'),
    ));

    // Playlists Post Type
    register_post_type('playlist', array(
        'labels' => array(
            'name' => __('Playlists', 'miraculous-music'),
            'singular_name' => __('Playlist', 'miraculous-music'),
            'add_new' => __('Add New Playlist', 'miraculous-music'),
        ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-playlist-audio',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'rewrite' => array('slug' => 'playlists'),
    ));
}
add_action('init', 'miraculous_music_register_post_types');

/**
 * Register Taxonomies
 */
function miraculous_music_register_taxonomies() {
    // Genres Taxonomy
    register_taxonomy('genre', array('music', 'album'), array(
        'labels' => array(
            'name' => __('Genres', 'miraculous-music'),
            'singular_name' => __('Genre', 'miraculous-music'),
        ),
        'hierarchical' => true,
        'show_admin_column' => true,
        'rewrite' => array('slug' => 'genre'),
    ));
}
add_action('init', 'miraculous_music_register_taxonomies');

/**
 * Custom excerpt length
 */
function miraculous_music_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'miraculous_music_excerpt_length');

/**
 * Custom excerpt more
 */
function miraculous_music_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'miraculous_music_excerpt_more');

/**
 * Default sidebar menu fallback
 */
function miraculous_music_default_sidebar_menu() {
    ?>
    <ul>
        <li><a href="<?php echo esc_url(home_url('/')); ?>" class="active" title="Discover">
            <span class="nav_icon"><span class="icon icon_discover"></span></span>
            <span class="nav_text">discover</span>
        </a></li>
        <li><a href="<?php echo esc_url(home_url('/albums')); ?>" title="Albums">
            <span class="nav_icon"><span class="icon icon_albums"></span></span>
            <span class="nav_text">albums</span>
        </a></li>
        <li><a href="<?php echo esc_url(home_url('/artists')); ?>" title="Artists">
            <span class="nav_icon"><span class="icon icon_artists"></span></span>
            <span class="nav_text">artists</span>
        </a></li>
        <li><a href="<?php echo esc_url(home_url('/genre')); ?>" title="Genres">
            <span class="nav_icon"><span class="icon icon_genres"></span></span>
            <span class="nav_text">genres</span>
        </a></li>
        <li><a href="<?php echo esc_url(home_url('/music')); ?>" title="Music">
            <span class="nav_icon"><span class="icon icon_music"></span></span>
            <span class="nav_text">music</span>
        </a></li>
    </ul>
    <ul class="nav_downloads">
        <li><a href="<?php echo esc_url(home_url('/playlists')); ?>" title="Playlists">
            <span class="nav_icon"><span class="icon icon_fe_playlist"></span></span>
            <span class="nav_text">playlists</span>
        </a></li>
    </ul>
    <?php
}

/**
 * ========================================
 * SUNO API INTEGRATION
 * ========================================
 */

/**
 * Get Suno API Base URL
 */
function miraculous_get_api_url() {
    return get_option('suno_api_url', 'https://api.sunoapi.org');
}

/**
 * Get Suno API Key
 */
function miraculous_get_api_key() {
    return get_option('suno_api_key', '');
}

/**
 * Mock Suno API responses for development mode
 */
function miraculous_suno_mock_response($endpoint, $method = 'GET', $data = null) {
    error_log('DEV MODE: Mock Suno API Request - ' . $endpoint);

    // Mock response for generate endpoint
    if (strpos($endpoint, '/api/v1/generate') !== false && $method === 'POST') {
        $task_id = 'dev-task-' . uniqid();
        $song_id_1 = 'dev-song-' . uniqid();
        $song_id_2 = 'dev-song-' . uniqid();

        return array(
            'success' => true,
            'data' => array(
                'task_id' => $task_id,
                'status' => 'pending',
                'songs' => array(
                    array(
                        'id' => $song_id_1,
                        'title' => ($data['prompt'] ?? 'Dev Song') . ' - Version 1',
                        'audio_url' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3',
                        'video_url' => '',
                        'image_url' => get_template_directory_uri() . '/assets/images/weekly/song1.jpg',
                        'status' => 'completed',
                        'duration' => 180,
                        'metadata' => array(
                            'prompt' => $data['prompt'] ?? '',
                            'model' => $data['model'] ?? 'V4',
                            'style' => 'Dev Mode Test'
                        )
                    ),
                    array(
                        'id' => $song_id_2,
                        'title' => ($data['prompt'] ?? 'Dev Song') . ' - Version 2',
                        'audio_url' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3',
                        'video_url' => '',
                        'image_url' => get_template_directory_uri() . '/assets/images/weekly/song2.jpg',
                        'status' => 'completed',
                        'duration' => 200,
                        'metadata' => array(
                            'prompt' => $data['prompt'] ?? '',
                            'model' => $data['model'] ?? 'V4',
                            'style' => 'Dev Mode Test'
                        )
                    )
                )
            ),
            'http_code' => 200
        );
    }

    // Mock response for credit endpoint
    if (strpos($endpoint, '/api/v1/generate/credit') !== false) {
        return array(
            'success' => true,
            'data' => array(
                'credits' => 999999,
                'total_credits' => 999999,
                'used_credits' => 0,
                'dev_mode' => true
            ),
            'http_code' => 200
        );
    }

    // Mock response for get task/song by ID
    if (strpos($endpoint, '/api/v1/generate/') !== false && $method === 'GET') {
        return array(
            'success' => true,
            'data' => array(
                'status' => 'completed',
                'songs' => array(
                    array(
                        'id' => 'dev-song-' . uniqid(),
                        'title' => 'Dev Mode Song',
                        'audio_url' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-3.mp3',
                        'video_url' => '',
                        'image_url' => get_template_directory_uri() . '/assets/images/weekly/song3.jpg',
                        'status' => 'completed',
                        'duration' => 190
                    )
                )
            ),
            'http_code' => 200
        );
    }

    // Default mock response
    return array(
        'success' => true,
        'data' => array('dev_mode' => true, 'message' => 'Mock response'),
        'http_code' => 200
    );
}

/**
 * Make request to Suno API
 */
function miraculous_suno_api_request($endpoint, $method = 'GET', $data = null) {
    // Check if dev mode is enabled
    if (defined('SUNO_DEV_MODE') && SUNO_DEV_MODE === true) {
        return miraculous_suno_mock_response($endpoint, $method, $data);
    }

    $api_url = miraculous_get_api_url();
    $api_key = miraculous_get_api_key();

    if (empty($api_key)) {
        return array(
            'success' => false,
            'error' => 'API key not configured',
            'message' => 'Vui lòng cấu hình API key trong WordPress Admin → Suno API'
        );
    }

    $url = $api_url . $endpoint;

    $args = array(
        'method' => $method,
        'timeout' => 30,
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ),
    );

    if ($method === 'POST' && $data) {
        $args['body'] = json_encode($data);
    }

    // Log request for debugging
    error_log('Suno API Request: ' . $url);
    error_log('Suno API Method: ' . $method);
    if ($data) {
        error_log('Suno API Data: ' . json_encode($data));
    }

    $response = wp_remote_request($url, $args);

    if (is_wp_error($response)) {
        error_log('Suno API Error: ' . $response->get_error_message());
        return array(
            'success' => false,
            'error' => $response->get_error_message(),
            'message' => 'Lỗi kết nối API: ' . $response->get_error_message()
        );
    }

    $body = wp_remote_retrieve_body($response);
    $http_code = wp_remote_retrieve_response_code($response);

    // Log response
    error_log('Suno API Response Code: ' . $http_code);
    error_log('Suno API Response Body: ' . substr($body, 0, 500)); // First 500 chars

    $decoded = json_decode($body, true);

    // Check for API errors
    if ($http_code >= 400) {
        return array(
            'success' => false,
            'error' => 'HTTP ' . $http_code,
            'message' => isset($decoded['message']) ? $decoded['message'] : 'API trả về lỗi ' . $http_code,
            'http_code' => $http_code,
            'raw_response' => $body
        );
    }

    // Validate response
    if (json_last_error() !== JSON_ERROR_NONE) {
        return array(
            'success' => false,
            'error' => 'Invalid JSON response',
            'message' => 'Phản hồi API không hợp lệ: ' . json_last_error_msg(),
            'http_code' => $http_code,
            'raw_response' => substr($body, 0, 200)
        );
    }

    return array(
        'success' => true,
        'data' => $decoded,
        'http_code' => $http_code,
    );
}

/**
 * Get song by task ID
 */
function miraculous_get_song_by_key($task_id) {
    // Check cache first
    $cache_key = 'suno_song_' . md5($task_id);
    $cached = get_transient($cache_key);

    if ($cached !== false) {
        return $cached;
    }

    $result = miraculous_suno_api_request('/api/v1/generate/record-info?task_id=' . urlencode($task_id));

    // Cache for 5 minutes
    if (!isset($result['error'])) {
        set_transient($cache_key, $result, 5 * MINUTE_IN_SECONDS);
    }

    return $result;
}

/**
 * Generate music from prompt
 */
function miraculous_generate_music($prompt, $model = 'V4', $options = array()) {
    $data = array_merge(array(
        'prompt' => $prompt,
        'model' => $model,
        'make_instrumental' => false,
    ), $options);

    return miraculous_suno_api_request('/api/v1/generate', 'POST', $data);
}

/**
 * Get user credits
 */
function miraculous_get_credits() {
    $cache_key = 'suno_credits';
    $cached = get_transient($cache_key);

    if ($cached !== false) {
        return $cached;
    }

    $result = miraculous_suno_api_request('/api/v1/generate/credit');

    // Cache for 1 minute
    if (!isset($result['error'])) {
        set_transient($cache_key, $result, MINUTE_IN_SECONDS);
    }

    return $result;
}

/**
 * Get music list from WordPress posts with Suno API data
 */
function miraculous_get_music_list($args = array()) {
    $defaults = array(
        'posts_per_page' => 12,
        'post_type' => 'music',
        'orderby' => 'date',
        'order' => 'DESC',
        'meta_query' => array(),
    );

    $args = wp_parse_args($args, $defaults);

    $query = new WP_Query($args);
    $music_list = array();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $music_list[] = array(
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'excerpt' => get_the_excerpt(),
                'permalink' => get_permalink(),
                'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'medium'),
                'task_id' => get_post_meta(get_the_ID(), '_suno_task_id', true),
                'audio_url' => get_post_meta(get_the_ID(), '_suno_audio_url', true),
                'image_url' => get_post_meta(get_the_ID(), '_suno_image_url', true),
                'artist' => get_post_meta(get_the_ID(), '_music_artist', true) ?: 'Suno AI',
                'duration' => get_post_meta(get_the_ID(), '_music_duration', true),
                'model' => get_post_meta(get_the_ID(), '_suno_model', true),
            );
        }
        wp_reset_postdata();
    }

    return array(
        'music' => $music_list,
        'total' => $query->found_posts,
        'max_pages' => $query->max_num_pages,
    );
}

/**
 * Get recently generated music from Suno API
 */
function miraculous_get_recent_suno_music($limit = 6) {
    return miraculous_get_music_list(array(
        'posts_per_page' => $limit,
        'meta_query' => array(
            array(
                'key' => '_suno_task_id',
                'compare' => 'EXISTS',
            ),
        ),
    ));
}

/**
 * Get music from wp_suno_history table
 *
 * @param array $args Query arguments
 * @return array Music list
 */
function miraculous_get_music_from_history($args = array()) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'suno_history';

    // Default arguments
    $defaults = array(
        'limit' => 12,
        'offset' => 0,
        'status' => 'completed', // Only get completed songs
        'user_id' => 0, // 0 = all users
        'order' => 'DESC',
        'orderby' => 'created_at',
    );

    $args = wp_parse_args($args, $defaults);

    // Build WHERE clause
    $where = array("status = 'completed'");

    if ($args['status'] && $args['status'] !== 'all') {
        $where[] = $wpdb->prepare("status = %s", $args['status']);
    }

    if ($args['user_id'] > 0) {
        $where[] = $wpdb->prepare("user_id = %d", $args['user_id']);
    }

    $where_sql = 'WHERE ' . implode(' AND ', $where);

    // Build ORDER BY clause
    $orderby = sanitize_sql_orderby($args['orderby'] . ' ' . $args['order']);

    // Query
    $sql = $wpdb->prepare(
        "SELECT * FROM $table_name $where_sql ORDER BY $orderby LIMIT %d OFFSET %d",
        $args['limit'],
        $args['offset']
    );

    $results = $wpdb->get_results($sql);

    if (empty($results)) {
        return array();
    }

    // Format results
    $music_list = array();

    foreach ($results as $row) {
        // Decode songs JSON
        $songs = !empty($row->songs) ? json_decode($row->songs, true) : null;

        // If songs array exists, process each song
        if (is_array($songs)) {
            foreach ($songs as $song) {
                $music_list[] = array(
                    'id' => $row->id,
                    'task_id' => $row->task_id,
                    'title' => !empty($song['title']) ? $song['title'] : $row->title,
                    'audio_url' => $song['audio_url'] ?? '',
                    'image_url' => $song['image_url'] ?? '',
                    'video_url' => $song['video_url'] ?? '',
                    'duration' => $song['duration'] ?? '',
                    'prompt' => $row->prompt,
                    'lyrics' => $row->lyrics,
                    'style' => $row->style,
                    'model' => $row->model,
                    'status' => $row->status,
                    'created_at' => $row->created_at,
                    'song_id' => $song['id'] ?? '',
                );
            }
        } else {
            // No songs data, use basic info
            $music_list[] = array(
                'id' => $row->id,
                'task_id' => $row->task_id,
                'title' => $row->title ?: 'Untitled',
                'audio_url' => '',
                'image_url' => '',
                'video_url' => '',
                'duration' => '',
                'prompt' => $row->prompt,
                'lyrics' => $row->lyrics,
                'style' => $row->style,
                'model' => $row->model,
                'status' => $row->status,
                'created_at' => $row->created_at,
                'song_id' => '',
            );
        }
    }

    return $music_list;
}

/**
 * Get recent music from history
 *
 * @param int $limit Number of songs
 * @return array Music list
 */
function miraculous_get_recent_music_from_history($limit = 6) {
    return miraculous_get_music_from_history(array(
        'limit' => $limit,
        'status' => 'completed',
        'orderby' => 'created_at',
        'order' => 'DESC',
    ));
}

/**
 * Get total count from history
 *
 * @param array $args Query arguments
 * @return int Total count
 */
function miraculous_get_history_count($args = array()) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'suno_history';

    $defaults = array(
        'status' => 'completed',
        'user_id' => 0,
    );

    $args = wp_parse_args($args, $defaults);

    // Build WHERE clause
    $where = array();

    if ($args['status'] && $args['status'] !== 'all') {
        $where[] = $wpdb->prepare("status = %s", $args['status']);
    }

    if ($args['user_id'] > 0) {
        $where[] = $wpdb->prepare("user_id = %d", $args['user_id']);
    }

    $where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

    return (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name $where_sql");
}

/**
 * Increment view count for a song
 *
 * @param int $song_id The song history ID
 * @return bool Success status
 */
function miraculous_increment_song_views($song_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'suno_history';

    $result = $wpdb->query($wpdb->prepare(
        "UPDATE $table_name SET views = views + 1 WHERE id = %d",
        $song_id
    ));

    return $result !== false;
}

/**
 * Get top songs by views
 *
 * @param int $limit Number of songs to return
 * @return array Music list ordered by views
 */
function miraculous_get_top_songs_by_views($limit = 10) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'suno_history';

    // Get top songs by views
    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name
        WHERE status = 'completed'
        AND songs IS NOT NULL
        AND songs != ''
        ORDER BY views DESC, created_at DESC
        LIMIT %d",
        $limit
    ));

    if (empty($results)) {
        return array();
    }

    $music_list = array();

    foreach ($results as $row) {
        // Decode songs JSON
        $songs = !empty($row->songs) ? json_decode($row->songs, true) : null;

        // If songs array exists, use first song
        if (is_array($songs) && !empty($songs)) {
            $song = $songs[0]; // Use first song for ranking
            $music_list[] = array(
                'id' => $row->id,
                'task_id' => $row->task_id,
                'title' => !empty($song['title']) ? $song['title'] : $row->title,
                'audio_url' => $song['audio_url'] ?? '',
                'image_url' => $song['image_url'] ?? '',
                'video_url' => $song['video_url'] ?? '',
                'duration' => $song['duration'] ?? '',
                'prompt' => $row->prompt,
                'lyrics' => $row->lyrics,
                'style' => $row->style,
                'model' => $row->model,
                'status' => $row->status,
                'created_at' => $row->created_at,
                'views' => $row->views ?? 0,
                'song_id' => $song['id'] ?? '',
            );
        }
    }

    return $music_list;
}

/**
 * Get music by style/genre from wp_suno_history
 *
 * @param string $style Style/genre to filter by
 * @param int $limit Number of songs to retrieve
 * @return array Music list
 */
function miraculous_get_music_by_style($style, $limit = 6) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'suno_history';

    // Query for songs with matching style
    $query = $wpdb->prepare(
        "SELECT * FROM $table_name
        WHERE status = 'completed'
        AND (style LIKE %s OR title LIKE %s)
        ORDER BY created_at DESC
        LIMIT %d",
        '%' . $wpdb->esc_like($style) . '%',
        '%' . $wpdb->esc_like($style) . '%',
        $limit
    );

    $results = $wpdb->get_results($query, ARRAY_A);

    if (empty($results)) {
        return array();
    }

    // Process results to extract song data
    $music_list = array();
    foreach ($results as $row) {
        // Decode songs JSON
        $songs = json_decode($row['songs'], true);

        if (!empty($songs) && is_array($songs)) {
            foreach ($songs as $song) {
                $music_list[] = array(
                    'id' => $row['id'],
                    'task_id' => $row['task_id'],
                    'title' => !empty($song['title']) ? $song['title'] : $row['title'],
                    'style' => $row['style'],
                    'model' => $row['model'],
                    'audio_url' => $song['audio_url'] ?? '',
                    'video_url' => $song['video_url'] ?? '',
                    'image_url' => $song['image_url'] ?? $song['image_large_url'] ?? '',
                    'duration' => isset($song['metadata']['duration']) ? gmdate('i:s', $song['metadata']['duration']) : '',
                    'created_at' => $row['created_at'],
                );

                if (count($music_list) >= $limit) {
                    break 2;
                }
            }
        }
    }

    return $music_list;
}

/**
 * Search Suno music in history
 *
 * @param string $query Search query
 * @param array $args Search arguments
 * @return array Search results
 */
function miraculous_search_suno_music($query, $args = array()) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'suno_history';

    $defaults = array(
        'search_title'  => true,
        'search_style'  => false,
        'search_lyrics' => false,
        'limit'         => 20,
        'offset'        => 0,
    );

    $args = wp_parse_args($args, $defaults);

    // Sanitize query
    $query = sanitize_text_field($query);
    if (empty($query)) {
        return array();
    }

    // Build WHERE conditions
    $search_conditions = array();
    $search_like = '%' . $wpdb->esc_like($query) . '%';

    if ($args['search_title']) {
        $search_conditions[] = $wpdb->prepare("title LIKE %s", $search_like);
        $search_conditions[] = $wpdb->prepare("songs LIKE %s", $search_like);
    }

    if ($args['search_style']) {
        $search_conditions[] = $wpdb->prepare("style LIKE %s", $search_like);
    }

    if ($args['search_lyrics']) {
        $search_conditions[] = $wpdb->prepare("lyrics LIKE %s", $search_like);
    }

    // If no conditions, search all
    if (empty($search_conditions)) {
        $search_conditions[] = $wpdb->prepare("title LIKE %s", $search_like);
    }

    $where_search = '(' . implode(' OR ', $search_conditions) . ')';

    // Build query
    $sql = $wpdb->prepare(
        "SELECT * FROM $table_name
        WHERE status = 'completed'
        AND songs IS NOT NULL
        AND songs != ''
        AND $where_search
        ORDER BY views DESC, created_at DESC
        LIMIT %d OFFSET %d",
        $args['limit'],
        $args['offset']
    );

    $results = $wpdb->get_results($sql);

    if (empty($results)) {
        return array();
    }

    // Process results
    $music_list = array();

    foreach ($results as $row) {
        $songs = !empty($row->songs) ? json_decode($row->songs, true) : null;

        if (is_array($songs) && !empty($songs)) {
            foreach ($songs as $song) {
                $music_list[] = array(
                    'id'         => $row->id,
                    'task_id'    => $row->task_id,
                    'title'      => !empty($song['title']) ? $song['title'] : $row->title,
                    'audio_url'  => $song['audio_url'] ?? '',
                    'image_url'  => $song['image_url'] ?? $song['image_large_url'] ?? '',
                    'video_url'  => $song['video_url'] ?? '',
                    'duration'   => isset($song['metadata']['duration']) ? gmdate('i:s', $song['metadata']['duration']) : '',
                    'prompt'     => $row->prompt,
                    'lyrics'     => $row->lyrics,
                    'style'      => $row->style,
                    'model'      => $row->model,
                    'status'     => $row->status,
                    'created_at' => $row->created_at,
                    'views'      => $row->views ?? 0,
                    'song_id'    => $song['id'] ?? '',
                );

                if (count($music_list) >= $args['limit']) {
                    break 2;
                }
            }
        }
    }

    return $music_list;
}

/**
 * AJAX handler for live search
 */
function miraculous_ajax_search_music() {
    check_ajax_referer('miraculous_ajax', 'nonce');

    $query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';

    if (strlen($query) < 2) {
        wp_send_json_error(array('message' => 'Query too short'));
    }

    $results = miraculous_search_suno_music($query, array(
        'search_title' => true,
        'search_style' => true,
        'limit'        => 10,
    ));

    if (empty($results)) {
        wp_send_json_success(array('results' => array(), 'message' => 'No results found'));
    }

    wp_send_json_success(array('results' => $results));
}
add_action('wp_ajax_miraculous_search_music', 'miraculous_ajax_search_music');
add_action('wp_ajax_nopriv_miraculous_search_music', 'miraculous_ajax_search_music');

/**
 * Get top played music
 */
function miraculous_get_top_music($limit = 15) {
    return miraculous_get_music_list(array(
        'posts_per_page' => $limit,
        'meta_key' => '_music_plays',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
    ));
}

/**
 * AJAX: Get song by key
 */
function miraculous_ajax_get_song() {
    check_ajax_referer('miraculous_ajax', 'nonce');

    $task_id = isset($_POST['task_id']) ? sanitize_text_field($_POST['task_id']) : '';

    if (empty($task_id)) {
        wp_send_json_error(array('message' => 'Task ID không được để trống'));
        return;
    }

    $result = miraculous_get_song_by_key($task_id);

    // Check if request was successful
    if (isset($result['success']) && $result['success'] === false) {
        wp_send_json_error(array(
            'message' => $result['message'] ?? 'Không thể load bài hát',
            'error' => $result['error'] ?? 'Unknown error',
            'debug' => $result
        ));
        return;
    }

    wp_send_json_success($result);
}
add_action('wp_ajax_get_song', 'miraculous_ajax_get_song');
add_action('wp_ajax_nopriv_get_song', 'miraculous_ajax_get_song');

/**
 * AJAX: Generate music
 */
function miraculous_ajax_generate_music() {
    check_ajax_referer('miraculous_ajax', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Vui lòng đăng nhập để tạo nhạc'));
        return;
    }

    $prompt = isset($_POST['prompt']) ? sanitize_text_field($_POST['prompt']) : '';
    $model = isset($_POST['model']) ? sanitize_text_field($_POST['model']) : 'V4';

    if (empty($prompt)) {
        wp_send_json_error(array('message' => 'Vui lòng nhập mô tả bài hát'));
        return;
    }

    $result = miraculous_generate_music($prompt, $model);

    // Check if request was successful
    if (isset($result['success']) && $result['success'] === false) {
        wp_send_json_error(array(
            'message' => $result['message'] ?? 'Không thể tạo bài hát',
            'error' => $result['error'] ?? 'Unknown error',
            'debug' => $result
        ));
        return;
    }

    // Check if we have task_id in response
    if (isset($result['data']['task_id']) || isset($result['data']['id'])) {
        wp_send_json_success($result);
    } else {
        wp_send_json_error(array(
            'message' => 'API không trả về Task ID',
            'debug' => $result
        ));
    }
}
add_action('wp_ajax_generate_music', 'miraculous_ajax_generate_music');

/**
 * AJAX: Get credits
 */
function miraculous_ajax_get_credits() {
    check_ajax_referer('miraculous_ajax', 'nonce');

    $result = miraculous_get_credits();

    if (isset($result['error'])) {
        wp_send_json_error($result);
    } else {
        wp_send_json_success($result);
    }
}
add_action('wp_ajax_get_credits', 'miraculous_ajax_get_credits');
add_action('wp_ajax_nopriv_get_credits', 'miraculous_ajax_get_credits');

/**
 * AJAX: Load more music
 */
function miraculous_ajax_load_more_music() {
    check_ajax_referer('miraculous_ajax', 'nonce');

    $page = isset($_POST['page']) ? absint($_POST['page']) : 1;
    $offset = ($page - 1) * 12;

    // Get music from history table
    $music_list = miraculous_get_music_from_history(array(
        'limit' => 12,
        'offset' => $offset,
    ));

    if (empty($music_list)) {
        wp_send_json_error(array('message' => 'No more music found'));
    }

    // Build HTML for music items
    ob_start();

    $counter_start = ($page - 1) * 12 + 1;
    $counter = $counter_start;

    foreach ($music_list as $song) :
        $has_audio = !empty($song['audio_url']);
    ?>
        <div class="ms_weekly_box" data-song-id="<?php echo esc_attr($song['id']); ?>">
            <div class="weekly_left">
                <span class="w_top_no"><?php echo $counter++; ?></span>
                <div class="w_top_song">
                    <div class="w_tp_song_img">
                        <?php if ($song['image_url']) : ?>
                            <img src="<?php echo esc_url($song['image_url']); ?>" alt="<?php echo esc_attr($song['title']); ?>" class="img-fluid">
                        <?php else : ?>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/weekly/song1.jpg" alt="<?php echo esc_attr($song['title']); ?>" class="img-fluid">
                        <?php endif; ?>

                        <?php if ($has_audio || !empty($song['video_url'])) : ?>
                            <div class="ms_song_overlay"></div>
                            <div class="ms_play_icon play-suno-song"
                                 data-audio-url="<?php echo esc_url($song['audio_url']); ?>"
                                 data-video-url="<?php echo esc_url($song['video_url']); ?>"
                                 data-title="<?php echo esc_attr($song['title']); ?>"
                                 data-artist="Suno AI"
                                 data-poster="<?php echo esc_url($song['image_url']); ?>"
                                 style="cursor: pointer;">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/svg/play.svg" alt="">
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="w_tp_song_name">
                        <h3><?php echo esc_html($song['title']); ?></h3>
                        <p><?php echo esc_html($song['style'] ?: 'Suno AI'); ?></p>
                        <?php if ($song['task_id']) : ?>
                            <small class="text-muted" style="font-size: 10px;">ID: <?php echo esc_html(substr($song['task_id'], 0, 8)); ?>...</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="weekly_right">
                <?php if ($song['duration']) : ?>
                    <span class="w_song_time"><?php echo esc_html($song['duration']); ?></span>
                <?php endif; ?>

                <?php if ($song['task_id'] && !$has_audio) : ?>
                    <button type="button"
                            class="ms_btn load-song-by-key"
                            data-task-id="<?php echo esc_attr($song['task_id']); ?>"
                            style="font-size: 12px; padding: 5px 10px;">
                        <?php esc_html_e('Load', 'miraculous-music'); ?>
                    </button>
                <?php endif; ?>

                <span class="ms_more_icon" data-other="1">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/svg/more.svg" alt="">
                </span>
            </div>
        </div>
    <?php
    endforeach;

    $html = ob_get_clean();

    wp_send_json_success(array(
        'html' => $html,
        'total' => $music_data['total'],
        'current_page' => $page,
        'max_pages' => $music_data['max_pages'],
    ));
}
add_action('wp_ajax_load_more_music', 'miraculous_ajax_load_more_music');
add_action('wp_ajax_nopriv_load_more_music', 'miraculous_ajax_load_more_music');

/**
 * AJAX: Track song view
 */
function miraculous_ajax_track_view() {
    check_ajax_referer('miraculous_ajax', 'nonce');

    $song_id = isset($_POST['song_id']) ? absint($_POST['song_id']) : 0;

    if ($song_id <= 0) {
        wp_send_json_error(array('message' => 'Invalid song ID'));
        return;
    }

    $result = miraculous_increment_song_views($song_id);

    if ($result) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'suno_history';
        $views = $wpdb->get_var($wpdb->prepare(
            "SELECT views FROM $table_name WHERE id = %d",
            $song_id
        ));

        wp_send_json_success(array(
            'message' => 'View tracked',
            'views' => $views
        ));
    } else {
        wp_send_json_error(array('message' => 'Failed to track view'));
    }
}
add_action('wp_ajax_track_view', 'miraculous_ajax_track_view');
add_action('wp_ajax_nopriv_track_view', 'miraculous_ajax_track_view');

/**
 * Add Meta Box for Suno Music
 */
function miraculous_add_suno_meta_box() {
    add_meta_box(
        'suno_music_meta',
        'Suno API Information',
        'miraculous_suno_meta_box_callback',
        'music',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'miraculous_add_suno_meta_box');

/**
 * Meta Box Callback
 */
function miraculous_suno_meta_box_callback($post) {
    wp_nonce_field('miraculous_save_suno_meta', 'suno_meta_nonce');

    $task_id = get_post_meta($post->ID, '_suno_task_id', true);
    $audio_url = get_post_meta($post->ID, '_suno_audio_url', true);
    $image_url = get_post_meta($post->ID, '_suno_image_url', true);
    $model = get_post_meta($post->ID, '_suno_model', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="suno_task_id">Task ID / Song Key</label></th>
            <td>
                <input type="text" id="suno_task_id" name="suno_task_id" value="<?php echo esc_attr($task_id); ?>" class="regular-text">
                <p class="description">Suno API Task ID to fetch song data</p>
                <?php if ($task_id) : ?>
                    <button type="button" class="button" onclick="loadSunoData('<?php echo esc_js($task_id); ?>')">Fetch from API</button>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th><label for="suno_audio_url">Audio URL</label></th>
            <td>
                <input type="url" id="suno_audio_url" name="suno_audio_url" value="<?php echo esc_attr($audio_url); ?>" class="regular-text">
                <p class="description">Direct link to audio file</p>
            </td>
        </tr>
        <tr>
            <th><label for="suno_image_url">Cover Image URL</label></th>
            <td>
                <input type="url" id="suno_image_url" name="suno_image_url" value="<?php echo esc_attr($image_url); ?>" class="regular-text">
                <p class="description">Direct link to cover image</p>
            </td>
        </tr>
        <tr>
            <th><label for="suno_model">AI Model</label></th>
            <td>
                <select id="suno_model" name="suno_model">
                    <option value="">Select Model</option>
                    <option value="V4" <?php selected($model, 'V4'); ?>>V4</option>
                    <option value="V4.5" <?php selected($model, 'V4.5'); ?>>V4.5</option>
                    <option value="V4.5PLUS" <?php selected($model, 'V4.5PLUS'); ?>>V4.5 PLUS</option>
                    <option value="V4.5ALL" <?php selected($model, 'V4.5ALL'); ?>>V4.5 ALL</option>
                    <option value="V5" <?php selected($model, 'V5'); ?>>V5</option>
                </select>
            </td>
        </tr>
    </table>

    <script>
    function loadSunoData(taskId) {
        if (!taskId) return;

        jQuery.post(ajaxurl, {
            action: 'get_song',
            nonce: '<?php echo wp_create_nonce('miraculous_ajax'); ?>',
            task_id: taskId
        }, function(response) {
            if (response.success && response.data.data) {
                var song = response.data.data;
                if (song.audio_url) jQuery('#suno_audio_url').val(song.audio_url);
                if (song.image_url) jQuery('#suno_image_url').val(song.image_url);
                if (song.title && !jQuery('#title').val()) jQuery('#title').val(song.title);
                alert('Data loaded successfully!');
            } else {
                alert('Failed to load song data');
            }
        });
    }
    </script>
    <?php
}

/**
 * Save Meta Box Data
 */
function miraculous_save_suno_meta($post_id) {
    if (!isset($_POST['suno_meta_nonce']) || !wp_verify_nonce($_POST['suno_meta_nonce'], 'miraculous_save_suno_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['suno_task_id'])) {
        update_post_meta($post_id, '_suno_task_id', sanitize_text_field($_POST['suno_task_id']));
    }

    if (isset($_POST['suno_audio_url'])) {
        update_post_meta($post_id, '_suno_audio_url', esc_url_raw($_POST['suno_audio_url']));
    }

    if (isset($_POST['suno_image_url'])) {
        update_post_meta($post_id, '_suno_image_url', esc_url_raw($_POST['suno_image_url']));
    }

    if (isset($_POST['suno_model'])) {
        update_post_meta($post_id, '_suno_model', sanitize_text_field($_POST['suno_model']));
    }
}
add_action('save_post_music', 'miraculous_save_suno_meta');

/**
 * Add API Settings Page
 */
function miraculous_add_api_settings_page() {
    add_menu_page(
        'Suno API Settings',
        'Suno API',
        'manage_options',
        'miraculous-suno-api',
        'miraculous_api_settings_page',
        'dashicons-admin-generic',
        80
    );
}
add_action('admin_menu', 'miraculous_add_api_settings_page');

/**
 * API Settings Page HTML
 */
function miraculous_api_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Save settings
    if (isset($_POST['miraculous_save_api'])) {
        check_admin_referer('miraculous_api_settings');

        update_option('suno_api_url', sanitize_text_field($_POST['suno_api_url']));
        update_option('suno_api_key', sanitize_text_field($_POST['suno_api_key']));

        echo '<div class="notice notice-success"><p>Settings saved successfully!</p></div>';
    }

    $api_url = get_option('suno_api_url', 'https://api.sunoapi.org');
    $api_key = get_option('suno_api_key', '');
    ?>
    <div class="wrap">
        <h1>Suno API Settings</h1>

        <form method="post" action="">
            <?php wp_nonce_field('miraculous_api_settings'); ?>

            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="suno_api_url">API Base URL</label>
                    </th>
                    <td>
                        <input type="text"
                               id="suno_api_url"
                               name="suno_api_url"
                               value="<?php echo esc_attr($api_url); ?>"
                               class="regular-text"
                               placeholder="https://api.sunoapi.org">
                        <p class="description">Enter the Suno API base URL</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="suno_api_key">API Key</label>
                    </th>
                    <td>
                        <input type="password"
                               id="suno_api_key"
                               name="suno_api_key"
                               value="<?php echo esc_attr($api_key); ?>"
                               class="regular-text"
                               placeholder="Your Suno API Key">
                        <p class="description">Get your API key from <a href="https://sunoapi.org/api-key" target="_blank">https://sunoapi.org/api-key</a></p>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" name="miraculous_save_api" class="button button-primary" value="Save Settings">
            </p>
        </form>

        <hr>

        <h2>Test API Connection</h2>
        <p>
            <button type="button" class="button" onclick="testSunoAPI()">Test Connection</button>
            <span id="api-test-result"></span>
        </p>

        <script>
        function testSunoAPI() {
            var resultEl = document.getElementById('api-test-result');
            resultEl.innerHTML = '<span style="color: blue;">Testing...</span>';

            jQuery.post(ajaxurl, {
                action: 'get_credits',
                nonce: '<?php echo wp_create_nonce('miraculous_ajax'); ?>'
            }, function(response) {
                if (response.success) {
                    resultEl.innerHTML = '<span style="color: green;">✓ Connection successful! Credits: ' + JSON.stringify(response.data) + '</span>';
                } else {
                    resultEl.innerHTML = '<span style="color: red;">✗ Connection failed: ' + (response.data.message || 'Unknown error') + '</span>';
                }
            }).fail(function() {
                resultEl.innerHTML = '<span style="color: red;">✗ Request failed</span>';
            });
        }
        </script>
    </div>
    <?php
}

/**
 * ========================================
 * USER AUTHENTICATION (Login/Register)
 * ========================================
 */

/**
 * AJAX: User Login
 */
function miraculous_ajax_login() {
    // Log request for debugging
    error_log('miraculous_ajax_login called');
    error_log('POST data: ' . print_r($_POST, true));

    // Verify nonce
    if (!check_ajax_referer('miraculous_login', 'login_nonce', false)) {
        error_log('Login nonce verification failed');
        wp_send_json_error(array('message' => 'Phiên làm việc hết hạn. Vui lòng tải lại trang.'));
        return;
    }

    // Get credentials
    $username = isset($_POST['username']) ? sanitize_text_field($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['remember']) && $_POST['remember'] === '1';

    // Validate inputs
    if (empty($username) || empty($password)) {
        wp_send_json_error(array('message' => 'Vui lòng nhập đầy đủ thông tin đăng nhập.'));
        return;
    }

    // Check if username is an email
    if (is_email($username)) {
        $user = get_user_by('email', $username);
        if ($user) {
            $username = $user->user_login;
        }
    }

    // Attempt login
    $creds = array(
        'user_login'    => $username,
        'user_password' => $password,
        'remember'      => $remember,
    );

    $user = wp_signon($creds, is_ssl());

    if (is_wp_error($user)) {
        $error_code = $user->get_error_code();

        switch ($error_code) {
            case 'invalid_username':
            case 'invalid_email':
                $message = 'Tên đăng nhập hoặc email không tồn tại.';
                break;
            case 'incorrect_password':
                $message = 'Mật khẩu không đúng.';
                break;
            default:
                $message = 'Đăng nhập thất bại. Vui lòng thử lại.';
        }

        wp_send_json_error(array('message' => $message));
        return;
    }

    // Login successful
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, $remember);

    wp_send_json_success(array(
        'message' => 'Đăng nhập thành công! Đang chuyển hướng...',
        'redirect' => home_url('/'),
        'user' => array(
            'id' => $user->ID,
            'name' => $user->display_name,
            'email' => $user->user_email,
        )
    ));
}
add_action('wp_ajax_nopriv_miraculous_login', 'miraculous_ajax_login');

/**
 * AJAX: User Registration
 */
function miraculous_ajax_register() {
    // Log request for debugging
    error_log('miraculous_ajax_register called');
    error_log('POST data: ' . print_r($_POST, true));

    // Verify nonce
    if (!check_ajax_referer('miraculous_register', 'register_nonce', false)) {
        error_log('Register nonce verification failed');
        wp_send_json_error(array('message' => 'Phiên làm việc hết hạn. Vui lòng tải lại trang.'));
        return;
    }

    // Check if registration is allowed (can be overridden by theme setting)
    $allow_registration = get_option('users_can_register') || get_theme_mod('allow_frontend_registration', true);
    if (!$allow_registration) {
        wp_send_json_error(array('message' => 'Đăng ký tài khoản hiện đang bị tắt.'));
        return;
    }

    // Get form data
    $firstname        = isset($_POST['firstname']) ? sanitize_text_field($_POST['firstname']) : '';
    $lastname         = isset($_POST['lastname']) ? sanitize_text_field($_POST['lastname']) : '';
    $username         = isset($_POST['username']) ? sanitize_user($_POST['username']) : '';
    $email            = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $password         = isset($_POST['password']) ? $_POST['password'] : '';
    $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
    $terms            = isset($_POST['terms']) && $_POST['terms'] === '1';

    // Validate required fields
    if (empty($username)) {
        wp_send_json_error(array('message' => 'Vui lòng nhập tên đăng nhập.', 'field' => 'username'));
        return;
    }

    if (empty($email)) {
        wp_send_json_error(array('message' => 'Vui lòng nhập địa chỉ email.', 'field' => 'email'));
        return;
    }

    if (!is_email($email)) {
        wp_send_json_error(array('message' => 'Địa chỉ email không hợp lệ.', 'field' => 'email'));
        return;
    }

    if (empty($password)) {
        wp_send_json_error(array('message' => 'Vui lòng nhập mật khẩu.', 'field' => 'password'));
        return;
    }

    if (strlen($password) < 8) {
        wp_send_json_error(array('message' => 'Mật khẩu phải có ít nhất 8 ký tự.', 'field' => 'password'));
        return;
    }

    if ($password !== $password_confirm) {
        wp_send_json_error(array('message' => 'Mật khẩu xác nhận không khớp.', 'field' => 'password_confirm'));
        return;
    }

    if (!$terms) {
        wp_send_json_error(array('message' => 'Vui lòng đồng ý với điều khoản sử dụng.', 'field' => 'terms'));
        return;
    }

    // Check if username exists
    if (username_exists($username)) {
        wp_send_json_error(array('message' => 'Tên đăng nhập này đã được sử dụng.', 'field' => 'username'));
        return;
    }

    // Check if email exists
    if (email_exists($email)) {
        wp_send_json_error(array('message' => 'Email này đã được đăng ký.', 'field' => 'email'));
        return;
    }

    // Validate username
    if (!validate_username($username)) {
        wp_send_json_error(array('message' => 'Tên đăng nhập chứa ký tự không hợp lệ.', 'field' => 'username'));
        return;
    }

    // Create user
    $userdata = array(
        'user_login'    => $username,
        'user_pass'     => $password,
        'user_email'    => $email,
        'first_name'    => $firstname,
        'last_name'     => $lastname,
        'display_name'  => trim($firstname . ' ' . $lastname) ?: $username,
        'role'          => get_option('default_role', 'subscriber'),
    );

    $user_id = wp_insert_user($userdata);

    if (is_wp_error($user_id)) {
        wp_send_json_error(array('message' => 'Không thể tạo tài khoản: ' . $user_id->get_error_message()));
        return;
    }

    // Send welcome email
    wp_new_user_notification($user_id, null, 'both');

    // Auto login after registration
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id, false);

    wp_send_json_success(array(
        'message' => 'Đăng ký thành công! Đang chuyển hướng...',
        'redirect' => home_url('/'),
        'user' => array(
            'id' => $user_id,
            'name' => trim($firstname . ' ' . $lastname) ?: $username,
            'email' => $email,
        )
    ));
}
add_action('wp_ajax_nopriv_miraculous_register', 'miraculous_ajax_register');

/**
 * AJAX: Forgot Password
 */
function miraculous_ajax_forgot_password() {
    // Verify nonce
    if (!check_ajax_referer('miraculous_forgot', 'forgot_nonce', false)) {
        wp_send_json_error(array('message' => 'Phiên làm việc hết hạn. Vui lòng tải lại trang.'));
        return;
    }

    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';

    if (empty($email) || !is_email($email)) {
        wp_send_json_error(array('message' => 'Vui lòng nhập địa chỉ email hợp lệ.'));
        return;
    }

    // Check if user exists
    $user = get_user_by('email', $email);

    if (!$user) {
        // For security, don't reveal if email exists
        wp_send_json_success(array(
            'message' => 'Nếu email tồn tại trong hệ thống, bạn sẽ nhận được link đặt lại mật khẩu.'
        ));
        return;
    }

    // Generate reset key
    $reset_key = get_password_reset_key($user);

    if (is_wp_error($reset_key)) {
        wp_send_json_error(array('message' => 'Không thể tạo link đặt lại mật khẩu. Vui lòng thử lại sau.'));
        return;
    }

    // Build reset link
    $reset_link = network_site_url("wp-login.php?action=rp&key=$reset_key&login=" . rawurlencode($user->user_login), 'login');

    // Send email
    $site_name = get_bloginfo('name');
    $subject = sprintf('[%s] Đặt lại mật khẩu', $site_name);

    $message = sprintf(
        "Xin chào %s,\n\n" .
        "Bạn đã yêu cầu đặt lại mật khẩu cho tài khoản tại %s.\n\n" .
        "Nhấn vào link sau để đặt lại mật khẩu:\n%s\n\n" .
        "Nếu bạn không yêu cầu điều này, vui lòng bỏ qua email này.\n\n" .
        "Link này sẽ hết hạn sau 24 giờ.\n\n" .
        "Trân trọng,\n%s",
        $user->display_name,
        $site_name,
        $reset_link,
        $site_name
    );

    $headers = array('Content-Type: text/plain; charset=UTF-8');

    $sent = wp_mail($email, $subject, $message, $headers);

    if ($sent) {
        wp_send_json_success(array(
            'message' => 'Link đặt lại mật khẩu đã được gửi đến email của bạn.'
        ));
    } else {
        wp_send_json_error(array('message' => 'Không thể gửi email. Vui lòng thử lại sau.'));
    }
}
add_action('wp_ajax_nopriv_miraculous_forgot_password', 'miraculous_ajax_forgot_password');

/**
 * AJAX: Check username availability
 */
function miraculous_ajax_check_username() {
    $username = isset($_POST['username']) ? sanitize_user($_POST['username']) : '';

    if (empty($username)) {
        wp_send_json_error(array('available' => false));
        return;
    }

    $available = !username_exists($username) && validate_username($username);

    wp_send_json_success(array(
        'available' => $available,
        'message' => $available ? 'Tên đăng nhập có thể sử dụng' : 'Tên đăng nhập đã tồn tại hoặc không hợp lệ'
    ));
}
add_action('wp_ajax_nopriv_miraculous_check_username', 'miraculous_ajax_check_username');

/**
 * AJAX: Check email availability
 */
function miraculous_ajax_check_email() {
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';

    if (empty($email) || !is_email($email)) {
        wp_send_json_error(array('available' => false));
        return;
    }

    $available = !email_exists($email);

    wp_send_json_success(array(
        'available' => $available,
        'message' => $available ? 'Email có thể sử dụng' : 'Email này đã được đăng ký'
    ));
}
add_action('wp_ajax_nopriv_miraculous_check_email', 'miraculous_ajax_check_email');

/**
 * AJAX: Logout
 */
function miraculous_ajax_logout() {
    wp_logout();
    wp_send_json_success(array(
        'message' => 'Đăng xuất thành công',
        'redirect' => home_url('/')
    ));
}
add_action('wp_ajax_miraculous_logout', 'miraculous_ajax_logout');
