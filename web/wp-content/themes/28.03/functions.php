<?php
/**
 * ou Theme — functions.php
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ═══════════════════════════════════════════════════
   SETUP
   ═══════════════════════════════════════════════════ */
function ou_setup() {
    load_theme_textdomain( 'ou', get_template_directory() . '/languages' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array( 'search-form','comment-form','comment-list','gallery','caption' ) );
    add_theme_support( 'custom-logo', array( 'flex-width' => true, 'flex-height' => true ) );
    add_theme_support( 'responsive-embeds' );

    add_image_size( 'project-hero',   1920, 1080, true );
    add_image_size( 'project-thumb',   800,  600, true );
    add_image_size( 'project-preview', 480,  320, true );

    register_nav_menus( array(
        'primary' => __( 'Primary Nav', 'ou' ),
    ) );
}
add_action( 'after_setup_theme', 'ou_setup' );

/* ═══════════════════════════════════════════════════
   ENQUEUE
   ═══════════════════════════════════════════════════ */
function ou_enqueue() {
    $v   = '1.0.0';
    $uri = get_template_directory_uri();

    /* REPLACE: font import — swap Google Fonts URL or add @font-face */
    wp_enqueue_style( 'ou-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400&display=swap',
        array(), null );

    /* CSS modules — order matters */
    $css = array( 'variables','base','loader','nav','hero','clean','wild','responsive' );
    $dep = array( 'ou-fonts' );
    foreach ( $css as $mod ) {
        wp_enqueue_style( "ou-{$mod}", "{$uri}/assets/css/{$mod}.css", $dep, $v );
        $dep = array( "ou-{$mod}" );
    }
    wp_enqueue_style( 'ou-style', get_stylesheet_uri(), $dep, $v );

    /* JS modules */
    $js = array( 'grain','cursor','theme','mode','filter','slider','scroll','main' );
    foreach ( $js as $mod ) {
        wp_enqueue_script( "ou-{$mod}", "{$uri}/assets/js/{$mod}.js", array(), $v, true );
    }

    /* Pass PHP data to JS — project list for the slider */
    wp_localize_script( 'ou-main', 'ouData', array(
        'projects' => ou_get_projects_data(),
        'siteUrl'  => home_url('/'),
        'ajaxUrl'  => admin_url('admin-ajax.php'),
    ) );
}
add_action( 'wp_enqueue_scripts', 'ou_enqueue' );

/* ═══════════════════════════════════════════════════
   PROJECT DATA FOR JS SLIDER
   ═══════════════════════════════════════════════════ */
function ou_get_projects_data() {
    $q = new WP_Query( array(
        'post_type'      => 'portfolio',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
        'post_status'    => 'publish',
    ) );

    $out = array();

    if ( $q->have_posts() ) {
        while ( $q->have_posts() ) {
            $q->the_post();
            $id    = get_the_ID();
            $terms = get_the_terms( $id, 'project_type' );
            $cat   = $terms ? strtolower( $terms[0]->slug ) : 'all';
            $year  = get_post_meta( $id, 'ou_year', true );
            $thumb = get_the_post_thumbnail_url( $id, 'project-hero' );
            $prev  = get_the_post_thumbnail_url( $id, 'project-preview' );
            $out[] = array(
                'id'       => $id,
                'title'    => get_the_title(),
                'href'     => get_permalink(),
                'cat'      => $cat,
                'year'     => $year ?: date('Y'),
                'imgSrc'   => $thumb ?: '',
                'prevSrc'  => $prev  ?: '',
                'gradient' => '',
            );
        }
        wp_reset_postdata();
        return $out;
    }

    return $out;
}

/* ═══════════════════════════════════════════════════
   CUSTOM POST TYPE: portfolio
   ═══════════════════════════════════════════════════ */
function ou_register_cpt() {
    register_post_type( 'portfolio', array(
        'labels'         => array(
            'name'               => 'Portfolio',
            'singular_name'      => 'Project',
            'add_new'            => 'Add New Project',
            'add_new_item'       => 'Add New Project',
            'edit_item'          => 'Edit Project',
            'view_item'          => 'View Project',
            'search_items'       => 'Search Projects',
            'not_found'          => 'No projects found',
        ),
        'public'         => true,
        'has_archive'    => true,
        'supports'       => array( 'title','editor','thumbnail','excerpt','custom-fields','page-attributes' ),
        'menu_icon'      => 'dashicons-portfolio',
        'menu_position'  => 5,
        'rewrite'        => array( 'slug' => 'work' ),
        'show_in_rest'   => true,
    ) );

    /* Category taxonomy: web / video / photo / graphic */
    register_taxonomy( 'project_type', 'portfolio', array(
        'label'             => 'Project Type',
        'rewrite'           => array( 'slug' => 'type' ),
        'hierarchical'      => false,
        'show_in_rest'      => true,
        'show_admin_column' => true,
    ) );
}
add_action( 'init', 'ou_register_cpt' );

/* Meta fields */
function ou_register_meta() {
    $fields = array(
        'ou_url'    => 'Live URL',
        'ou_year'   => 'Year',
        'ou_client' => 'Client',
        'ou_role'   => 'Role (comma-separated)',
        'ou_tools'  => 'Tools (comma-separated)',
    );
    foreach ( $fields as $key => $label ) {
        register_post_meta( 'portfolio', $key, array(
            'show_in_rest'  => true,
            'single'        => true,
            'type'          => 'string',
            'auth_callback' => function () { return current_user_can('edit_posts'); },
        ) );
    }
}
add_action( 'init', 'ou_register_meta' );

/* ═══════════════════════════════════════════════════
   CUSTOMIZER
   ═══════════════════════════════════════════════════ */
function ou_customizer( $wp_customize ) {

    /* ── Identity ──────────────────────────────────── */
    $wp_customize->add_section( 'ou_id', array( 'title' => 'Site Identity', 'priority' => 20 ) );
    _ou_text( $wp_customize, 'ou_name',      'ou_id', 'Your Name / Studio',          get_bloginfo('name') );
    _ou_text( $wp_customize, 'ou_tagline',   'ou_id', 'One-line tagline',             '' );
    _ou_text( $wp_customize, 'ou_available', 'ou_id', 'Availability text',           'Available for new projects' );
    _ou_text( $wp_customize, 'ou_edition',   'ou_id', 'Edition label (e.g. Issue N°003)', 'Issue N°001' );
    _ou_text( $wp_customize, 'ou_location',  'ou_id', 'Location',                    'Remote / Global' );

    /* ── Hero ──────────────────────────────────────── */
    $wp_customize->add_section( 'ou_hero', array( 'title' => 'Hero Section', 'priority' => 30 ) );
    /* REPLACE: video — upload mp4 to Media, paste URL here */
    $wp_customize->add_setting( 'ou_video', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
    $wp_customize->add_control( 'ou_video', array(
        'label'       => 'Hero Video URL (.mp4) — REPLACE: video',
        'description' => 'Upload mp4 to Media Library, paste URL here.',
        'section'     => 'ou_hero', 'type' => 'url',
    ) );
    _ou_text( $wp_customize, 'ou_disc1', 'ou_hero', 'Discipline 1', 'Web Design' );
    _ou_text( $wp_customize, 'ou_disc2', 'ou_hero', 'Discipline 2', 'Video' );
    _ou_text( $wp_customize, 'ou_disc3', 'ou_hero', 'Discipline 3', 'Photography' );
    _ou_text( $wp_customize, 'ou_disc4', 'ou_hero', 'Discipline 4', 'Graphic Design' );

    /* ── About ─────────────────────────────────────── */
    $wp_customize->add_section( 'ou_about', array( 'title' => 'About Section', 'priority' => 40 ) );
    _ou_text(     $wp_customize, 'ou_about_h1',   'ou_about', 'Heading line 1', 'Making things' );
    _ou_text(     $wp_customize, 'ou_about_h2',   'ou_about', 'Heading line 2', 'that matter.' );
    _ou_textarea( $wp_customize, 'ou_about_text', 'ou_about', 'Body text',
        'I design and build digital experiences for brands with something to say. Based globally, working globally.' );
    _ou_text( $wp_customize, 'ou_tag1', 'ou_about', 'Tag 1', 'Web Design' );
    _ou_text( $wp_customize, 'ou_tag2', 'ou_about', 'Tag 2', 'Video' );
    _ou_text( $wp_customize, 'ou_tag3', 'ou_about', 'Tag 3', 'Photography' );
    _ou_text( $wp_customize, 'ou_tag4', 'ou_about', 'Tag 4', 'Graphic Design' );
    _ou_text( $wp_customize, 'ou_tag5', 'ou_about', 'Tag 5', 'Art Direction' );

    /* ── Stats ────────────────────────────────────────── */
    $wp_customize->add_section( 'ou_stats', array( 'title' => 'Stats Strip', 'priority' => 48 ) );
    _ou_text( $wp_customize, 'ou_stat1_num', 'ou_stats', 'Stat 1 Number', '120+' );
    _ou_text( $wp_customize, 'ou_stat1_lbl', 'ou_stats', 'Stat 1 Label',  'Projects Delivered' );
    _ou_text( $wp_customize, 'ou_stat2_num', 'ou_stats', 'Stat 2 Number', '8' );
    _ou_text( $wp_customize, 'ou_stat2_lbl', 'ou_stats', 'Stat 2 Label',  'Years Active' );
    _ou_text( $wp_customize, 'ou_stat3_num', 'ou_stats', 'Stat 3 Number', '14' );
    _ou_text( $wp_customize, 'ou_stat3_lbl', 'ou_stats', 'Stat 3 Label',  'Awards' );
    _ou_text( $wp_customize, 'ou_stat4_num', 'ou_stats', 'Stat 4 Number', '60+' );
    _ou_text( $wp_customize, 'ou_stat4_lbl', 'ou_stats', 'Stat 4 Label',  'Happy Clients' );

    /* ── Contact ───────────────────────────────────── */
    $wp_customize->add_section( 'ou_contact', array( 'title' => 'Contact', 'priority' => 50 ) );
    _ou_text( $wp_customize, 'ou_contact_h',   'ou_contact', 'Heading',       "Let's work." );
    _ou_textarea( $wp_customize, 'ou_contact_t','ou_contact', 'Subtext',
        'Available for select projects. Get in touch.' );
    _ou_text( $wp_customize, 'ou_email',        'ou_contact', 'Email',         'hello@yoursite.com' );
    _ou_text( $wp_customize, 'ou_instagram',    'ou_contact', 'Instagram URL', '' );
    _ou_text( $wp_customize, 'ou_behance',      'ou_contact', 'Behance URL',   '' );
    _ou_text( $wp_customize, 'ou_linkedin',     'ou_contact', 'LinkedIn URL',  '' );
    _ou_text( $wp_customize, 'ou_vimeo',        'ou_contact', 'Vimeo URL',     '' );
}
add_action( 'customize_register', 'ou_customizer' );

function _ou_text( $c, $id, $sec, $label, $default = '' ) {
    $c->add_setting( $id, array( 'default' => $default, 'sanitize_callback' => 'sanitize_text_field' ) );
    $c->add_control( $id, array( 'label' => $label, 'section' => $sec, 'type' => 'text' ) );
}

function _ou_textarea( $c, $id, $sec, $label, $default = '' ) {
    $c->add_setting( $id, array( 'default' => $default, 'sanitize_callback' => 'sanitize_textarea_field' ) );
    $c->add_control( $id, array( 'label' => $label, 'section' => $sec, 'type' => 'textarea' ) );
}

/* ── Helper ────────────────────────────────────────── */
function ou( $key, $fallback = '' ) {
    return get_theme_mod( $key, $fallback ) ?: $fallback;
}

/* ═══════════════════════════════════════════════════
   BODY CLASS
   ═══════════════════════════════════════════════════ */
function ou_body_class( $classes ) {
    $classes[] = 'ou-theme';
    return $classes;
}
add_filter( 'body_class', 'ou_body_class' );

/* ═══════════════════════════════════════════════════
   EXCERPT
   ═══════════════════════════════════════════════════ */
add_filter( 'excerpt_length', function () { return 18; }, 999 );
add_filter( 'excerpt_more',   function () { return ''; } );

/* ═══════════════════════════════════════════════════
   CLEAN UP HEAD
   ═══════════════════════════════════════════════════ */
remove_action( 'wp_head',             'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles',     'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles',  'print_emoji_styles' );
add_filter( 'wp_lazy_loading_enabled', '__return_true' );
