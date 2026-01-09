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

    // Scripts
    wp_enqueue_script('jquery');
    wp_enqueue_script('bootstrap', get_template_directory_uri() . '/assets/js/bootstrap.min.js', array('jquery'), $theme_version, true);
    wp_enqueue_script('swiper', get_template_directory_uri() . '/assets/js/plugins/swiper/js/swiper.min.js', array('jquery'), $theme_version, true);
    wp_enqueue_script('jplayer-playlist', get_template_directory_uri() . '/assets/js/plugins/player/jplayer.playlist.min.js', array('jquery'), $theme_version, true);
    wp_enqueue_script('jplayer', get_template_directory_uri() . '/assets/js/plugins/player/jquery.jplayer.min.js', array('jquery'), $theme_version, true);
    wp_enqueue_script('audio-player', get_template_directory_uri() . '/assets/js/plugins/player/audio-player.js', array('jquery', 'jplayer'), $theme_version, true);
    wp_enqueue_script('volume-js', get_template_directory_uri() . '/assets/js/plugins/player/volume.js', array('jquery'), $theme_version, true);
    wp_enqueue_script('nice-select', get_template_directory_uri() . '/assets/js/plugins/nice_select/jquery.nice-select.min.js', array('jquery'), $theme_version, true);
    wp_enqueue_script('mCustomScrollbar', get_template_directory_uri() . '/assets/js/plugins/scroll/jquery.mCustomScrollbar.js', array('jquery'), $theme_version, true);
    wp_enqueue_script('miraculous-custom', get_template_directory_uri() . '/assets/js/custom.js', array('jquery'), $theme_version, true);
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
