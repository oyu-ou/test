<?php
/**
 * AXIOM Theme — functions.php
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ═══════════════════════════════════════════════════
   SETUP
   ═══════════════════════════════════════════════════ */
function axiom_setup() {
    load_theme_textdomain( 'axiom', get_template_directory() . '/languages' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array( 'search-form','comment-form','comment-list','gallery','caption' ) );
    add_theme_support( 'custom-logo', array( 'flex-width' => true, 'flex-height' => true ) );
    add_theme_support( 'responsive-embeds' );

    add_image_size( 'project-hero',   1920, 1080, true );
    add_image_size( 'project-thumb',   800,  600, true );
    add_image_size( 'project-preview', 480,  320, true );

    register_nav_menus( array(
        'primary' => __( 'Primary Nav', 'axiom' ),
    ) );
}
add_action( 'after_setup_theme', 'axiom_setup' );

/* ═══════════════════════════════════════════════════
   ENQUEUE
   ═══════════════════════════════════════════════════ */
function axiom_enqueue() {
    $v   = '1.0.0';
    $uri = get_template_directory_uri();

    /* REPLACE: font import — swap Google Fonts URL or add @font-face */
    wp_enqueue_style( 'axiom-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400&display=swap',
        array(), null );

    /* CSS modules — order matters */
    $css = array( 'variables','base','loader','nav','hero','clean','wild','responsive' );
    $dep = array( 'axiom-fonts' );
    foreach ( $css as $mod ) {
        wp_enqueue_style( "axiom-{$mod}", "{$uri}/assets/css/{$mod}.css", $dep, $v );
        $dep = array( "axiom-{$mod}" );
    }
    wp_enqueue_style( 'axiom-style', get_stylesheet_uri(), $dep, $v );

    /* JS modules */
    $js = array( 'grain','cursor','theme','mode','filter','slider','scroll','main' );
    foreach ( $js as $mod ) {
        wp_enqueue_script( "axiom-{$mod}", "{$uri}/assets/js/{$mod}.js", array(), $v, true );
    }

    /* Pass PHP data to JS — project list for the slider */
    wp_localize_script( 'axiom-main', 'axiomData', array(
        'projects' => axiom_get_projects_data(),
        'siteUrl'  => home_url('/'),
        'ajaxUrl'  => admin_url('admin-ajax.php'),
    ) );
}
add_action( 'wp_enqueue_scripts', 'axiom_enqueue' );

/* ═══════════════════════════════════════════════════
   PROJECT DATA FOR JS SLIDER
   ═══════════════════════════════════════════════════ */
function axiom_get_projects_data() {
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
            $year  = get_post_meta( $id, 'axiom_year', true );
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

    /* Dummy fallback — shown until real projects are added */
    $dummy = array(
        array( 'title' => 'Brand Identity System',   'cat' => 'graphic', 'year' => '2024', 'gradient' => 'linear-gradient(135deg,#0d0d0d 0%,#1a0a00 50%,#2a1200 100%)' ),
        array( 'title' => 'Editorial Film Series',   'cat' => 'video',   'year' => '2024', 'gradient' => 'linear-gradient(135deg,#080808 0%,#001020 50%,#002040 100%)' ),
        array( 'title' => 'Web Experience Design',   'cat' => 'web',     'year' => '2023', 'gradient' => 'linear-gradient(135deg,#0a0a0a 0%,#060018 50%,#100030 100%)' ),
        array( 'title' => 'Portrait Series Vol. II', 'cat' => 'photo',   'year' => '2023', 'gradient' => 'linear-gradient(135deg,#080808 0%,#0a1a0a 50%,#102010 100%)' ),
        array( 'title' => 'Motion Campaign',         'cat' => 'video',   'year' => '2023', 'gradient' => 'linear-gradient(135deg,#0c0c0c 0%,#1a0010 50%,#280020 100%)' ),
        array( 'title' => 'E-Commerce Platform',     'cat' => 'web',     'year' => '2022', 'gradient' => 'linear-gradient(135deg,#080808 0%,#0a0a18 50%,#141428 100%)' ),
        array( 'title' => 'Landscape Photography',   'cat' => 'photo',   'year' => '2022', 'gradient' => 'linear-gradient(135deg,#0a0a0a 0%,#0f1408 50%,#182008 100%)' ),
        array( 'title' => 'Type Specimen Poster',    'cat' => 'graphic', 'year' => '2022', 'gradient' => 'linear-gradient(135deg,#0d0d0d 0%,#181008 50%,#201808 100%)' ),
    );
    foreach ( $dummy as $p ) {
        $out[] = array(
            'id'       => 0,
            'title'    => $p['title'],
            'href'     => '#',
            'cat'      => $p['cat'],
            'year'     => $p['year'],
            'imgSrc'   => '',
            'prevSrc'  => '',
            'gradient' => $p['gradient'],
        );
    }
    return $out;
}

/* ═══════════════════════════════════════════════════
   CUSTOM POST TYPE: portfolio
   ═══════════════════════════════════════════════════ */
function axiom_register_cpt() {
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
add_action( 'init', 'axiom_register_cpt' );

/* Meta fields */
function axiom_register_meta() {
    $fields = array(
        'axiom_url'    => 'Live URL',
        'axiom_year'   => 'Year',
        'axiom_client' => 'Client',
        'axiom_role'   => 'Role (comma-separated)',
        'axiom_tools'  => 'Tools (comma-separated)',
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
add_action( 'init', 'axiom_register_meta' );

/* ═══════════════════════════════════════════════════
   CUSTOMIZER
   ═══════════════════════════════════════════════════ */
function axiom_customizer( $wp_customize ) {

    /* ── Identity ──────────────────────────────────── */
    $wp_customize->add_section( 'axiom_id', array( 'title' => 'Site Identity', 'priority' => 20 ) );
    _ax_text( $wp_customize, 'axiom_name',      'axiom_id', 'Your Name / Studio',          get_bloginfo('name') );
    _ax_text( $wp_customize, 'axiom_tagline',   'axiom_id', 'One-line tagline',             '' );
    _ax_text( $wp_customize, 'axiom_available', 'axiom_id', 'Availability text',           'Available for new projects' );
    _ax_text( $wp_customize, 'axiom_edition',   'axiom_id', 'Edition label (e.g. Issue N°003)', 'Issue N°001' );
    _ax_text( $wp_customize, 'axiom_location',  'axiom_id', 'Location',                    'Remote / Global' );

    /* ── Hero ──────────────────────────────────────── */
    $wp_customize->add_section( 'axiom_hero', array( 'title' => 'Hero Section', 'priority' => 30 ) );
    /* REPLACE: video — upload mp4 to Media, paste URL here */
    $wp_customize->add_setting( 'axiom_video', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
    $wp_customize->add_control( 'axiom_video', array(
        'label'       => 'Hero Video URL (.mp4) — REPLACE: video',
        'description' => 'Upload mp4 to Media Library, paste URL here.',
        'section'     => 'axiom_hero', 'type' => 'url',
    ) );
    _ax_text( $wp_customize, 'axiom_disc1', 'axiom_hero', 'Discipline 1', 'Web Design' );
    _ax_text( $wp_customize, 'axiom_disc2', 'axiom_hero', 'Discipline 2', 'Video' );
    _ax_text( $wp_customize, 'axiom_disc3', 'axiom_hero', 'Discipline 3', 'Photography' );
    _ax_text( $wp_customize, 'axiom_disc4', 'axiom_hero', 'Discipline 4', 'Graphic Design' );

    /* ── About ─────────────────────────────────────── */
    $wp_customize->add_section( 'axiom_about', array( 'title' => 'About Section', 'priority' => 40 ) );
    _ax_text(     $wp_customize, 'axiom_about_h1',   'axiom_about', 'Heading line 1', 'Making things' );
    _ax_text(     $wp_customize, 'axiom_about_h2',   'axiom_about', 'Heading line 2', 'that matter.' );
    _ax_textarea( $wp_customize, 'axiom_about_text', 'axiom_about', 'Body text',
        'I design and build digital experiences for brands with something to say. Based globally, working globally.' );
    _ax_text( $wp_customize, 'axiom_tag1', 'axiom_about', 'Tag 1', 'Web Design' );
    _ax_text( $wp_customize, 'axiom_tag2', 'axiom_about', 'Tag 2', 'Video' );
    _ax_text( $wp_customize, 'axiom_tag3', 'axiom_about', 'Tag 3', 'Photography' );
    _ax_text( $wp_customize, 'axiom_tag4', 'axiom_about', 'Tag 4', 'Graphic Design' );
    _ax_text( $wp_customize, 'axiom_tag5', 'axiom_about', 'Tag 5', 'Art Direction' );

    /* ── Stats ────────────────────────────────────────── */
    $wp_customize->add_section( 'axiom_stats', array( 'title' => 'Stats Strip', 'priority' => 48 ) );
    _ax_text( $wp_customize, 'axiom_stat1_num', 'axiom_stats', 'Stat 1 Number', '120+' );
    _ax_text( $wp_customize, 'axiom_stat1_lbl', 'axiom_stats', 'Stat 1 Label',  'Projects Delivered' );
    _ax_text( $wp_customize, 'axiom_stat2_num', 'axiom_stats', 'Stat 2 Number', '8' );
    _ax_text( $wp_customize, 'axiom_stat2_lbl', 'axiom_stats', 'Stat 2 Label',  'Years Active' );
    _ax_text( $wp_customize, 'axiom_stat3_num', 'axiom_stats', 'Stat 3 Number', '14' );
    _ax_text( $wp_customize, 'axiom_stat3_lbl', 'axiom_stats', 'Stat 3 Label',  'Awards' );
    _ax_text( $wp_customize, 'axiom_stat4_num', 'axiom_stats', 'Stat 4 Number', '60+' );
    _ax_text( $wp_customize, 'axiom_stat4_lbl', 'axiom_stats', 'Stat 4 Label',  'Happy Clients' );

    /* ── Contact ───────────────────────────────────── */
    $wp_customize->add_section( 'axiom_contact', array( 'title' => 'Contact', 'priority' => 50 ) );
    _ax_text( $wp_customize, 'axiom_contact_h',   'axiom_contact', 'Heading',       "Let's work." );
    _ax_textarea( $wp_customize, 'axiom_contact_t','axiom_contact', 'Subtext',
        'Available for select projects. Get in touch.' );
    _ax_text( $wp_customize, 'axiom_email',        'axiom_contact', 'Email',         'hello@yoursite.com' );
    _ax_text( $wp_customize, 'axiom_instagram',    'axiom_contact', 'Instagram URL', '' );
    _ax_text( $wp_customize, 'axiom_behance',      'axiom_contact', 'Behance URL',   '' );
    _ax_text( $wp_customize, 'axiom_linkedin',     'axiom_contact', 'LinkedIn URL',  '' );
    _ax_text( $wp_customize, 'axiom_vimeo',        'axiom_contact', 'Vimeo URL',     '' );
}
add_action( 'customize_register', 'axiom_customizer' );

function _ax_text( $c, $id, $sec, $label, $default = '' ) {
    $c->add_setting( $id, array( 'default' => $default, 'sanitize_callback' => 'sanitize_text_field' ) );
    $c->add_control( $id, array( 'label' => $label, 'section' => $sec, 'type' => 'text' ) );
}

function _ax_textarea( $c, $id, $sec, $label, $default = '' ) {
    $c->add_setting( $id, array( 'default' => $default, 'sanitize_callback' => 'sanitize_textarea_field' ) );
    $c->add_control( $id, array( 'label' => $label, 'section' => $sec, 'type' => 'textarea' ) );
}

/* ── Helper ────────────────────────────────────────── */
function ax( $key, $fallback = '' ) {
    return get_theme_mod( $key, $fallback ) ?: $fallback;
}

/* ═══════════════════════════════════════════════════
   BODY CLASS
   ═══════════════════════════════════════════════════ */
function axiom_body_class( $classes ) {
    $classes[] = 'axiom-theme';
    return $classes;
}
add_filter( 'body_class', 'axiom_body_class' );

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
