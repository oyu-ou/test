<?php
/**
 * ou — single.php (v2)
 * Full cinematic single-project layout.
 * Every section has dummy content fallbacks so the page
 * never looks empty even before real data is entered.
 */
get_header();
while ( have_posts() ) : the_post();

$is_port  = ( get_post_type() === 'portfolio' );
$pid      = get_the_ID();
$url      = get_post_meta( $pid, 'ou_url',    true );
$year     = get_post_meta( $pid, 'ou_year',   true ) ?: date('Y');
$client   = get_post_meta( $pid, 'ou_client', true ) ?: 'Confidential';
$role     = get_post_meta( $pid, 'ou_role',   true ) ?: 'Design, Direction';
$tools    = get_post_meta( $pid, 'ou_tools',  true ) ?: 'Figma, CSS, After Effects';
$terms    = get_the_terms( $pid, 'project_type' );
$cat      = $terms ? $terms[0]->name : 'Design';
$cat_slug = $terms ? strtolower($terms[0]->slug) : 'graphic';

/* Adjacent navigation */
$prev_p = get_adjacent_post( false, '', true,  'project_type' );
$next_p = get_adjacent_post( false, '', false, 'project_type' );

/* Dummy body paragraphs — replaced when editor content exists */
$dummy_para1 = 'This project began with a single question: how do you communicate restraint in a world of excess? Working closely with the client, we developed a visual language that speaks in silences — where negative space carries as much weight as what is present.';
$dummy_para2 = 'The research phase uncovered a core tension between the brand\'s heritage and its ambitions. Rather than resolving this tension, we leaned into it — letting the contradiction become the concept. The result is a body of work that feels simultaneously archival and forward.';
$dummy_para3 = 'Every decision was made with the end-user in mind. Typography was treated as image. Layout as rhythm. Color as emotion. The final deliverables span digital and print, unified by a single, unwavering point of view.';

/* Dummy gallery gradients */
$gallery_gradients = [
    'linear-gradient(160deg,#0f0f0f 0%,#1a1200 100%)',
    'linear-gradient(200deg,#080808 0%,#001525 100%)',
    'linear-gradient(140deg,#0c0c0c 0%,#180010 100%)',
    'linear-gradient(170deg,#0a0a0a 0%,#0a1800 100%)',
];

/* Has real content? */
$has_content  = trim( strip_tags( get_the_content() ) ) !== '';
$has_thumb    = has_post_thumbnail();

?>
<?php if ( $is_port ) : ?>
<!-- ════════════════════════════════════════
     PORTFOLIO SINGLE PAGE
     ════════════════════════════════════════ -->

<div class="sp">

  <!-- ── HERO ────────────────────────────── -->
  <div class="sp-hero" id="sp-hero-section">

    <!-- Background -->
    <div class="sp-hero__bg">
      <?php if ( $has_thumb ) :
        the_post_thumbnail( 'project-hero', [ 'alt' => get_the_title() ] );
      else : ?>
        <div class="sp-hero__bg--gradient"></div>
      <?php endif; ?>
    </div>

    <!-- Decorative grid lines -->
    <div class="sp-hero__grid" aria-hidden="true"></div>

    <!-- Scrim -->
    <div class="sp-hero__scrim" aria-hidden="true"></div>

    <!-- Info -->
    <div class="sp-hero__info">
      <div>
        <div class="sp-hero__cat"><?php echo esc_html( $cat ); ?></div>
        <h1 class="sp-hero__title"><?php the_title(); ?></h1>
      </div>
      <div class="sp-hero__right">
        <span class="sp-hero__year"><?php echo esc_html( $year ); ?></span>
        <?php if ( $url ) : ?>
        <a href="<?php echo esc_url( $url ); ?>" class="sp-hero__open"
           target="_blank" rel="noopener noreferrer">
          View Live <span aria-hidden="true">↗</span>
        </a>
        <?php endif; ?>
      </div>
    </div>

  </div>

  <!-- ── BACK LINK ────────────────────────── -->
  <a href="<?php echo esc_url( home_url('/') ); ?>#work" class="sp-back">
    <span aria-hidden="true">←</span> All Projects
  </a>

  <!-- ── META STRIP ───────────────────────── -->
  <div class="sp-meta" data-reveal="up">
    <?php
    $meta_fields = [
      'Type'   => $cat,
      'Year'   => $year,
      'Client' => $client,
      'Role'   => $role,
      'Tools'  => $tools,
    ];
    $di = 1;
    foreach ( $meta_fields as $label => $val ) :
      if ( !$val ) continue;
    ?>
    <div class="sp-meta__item" data-reveal="up" data-delay="<?php echo $di; ?>">
      <div class="sp-meta__label"><?php echo esc_html( $label ); ?></div>
      <div class="sp-meta__value"><?php echo esc_html( $val ); ?></div>
    </div>
    <?php $di++; endforeach; ?>
  </div>

  <!-- ── BODY CONTENT ─────────────────────── -->
  <div class="sp-body">

    <div class="sp-body__text" data-reveal="up">
      <?php if ( $has_content ) :
        the_content();
      else : /* Dummy paragraphs */ ?>
        <p><?php echo esc_html( $dummy_para1 ); ?></p>
        <p><?php echo esc_html( $dummy_para2 ); ?></p>
        <p><?php echo esc_html( $dummy_para3 ); ?></p>
      <?php endif; ?>
    </div>

    <div class="sp-body__sticky" data-reveal="up" data-delay="2">
      <?php
      /* Use second gallery image or post thumbnail or placeholder */
      $attach = get_posts([
        'post_type'      => 'attachment',
        'post_parent'    => $pid,
        'post_mime_type' => 'image',
        'posts_per_page' => 2,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
      ]);
      if ( !empty( $attach[1] ) ) :
        echo wp_get_attachment_image( $attach[1]->ID, 'project-thumb', false, ['alt' => ''] );
      elseif ( $has_thumb ) :
        the_post_thumbnail('project-thumb', ['alt' => '']);
      else : ?>
        <div class="sp-img-placeholder"
             style="background:linear-gradient(160deg,#0f0f0f,#1a0800);aspect-ratio:4/3;">
          <span style="font-family:var(--font-mono);font-size:var(--sz-xs);letter-spacing:var(--ls-widest);text-transform:uppercase;color:rgba(255,255,255,0.15);">
            Add Project Images
          </span>
        </div>
      <?php endif; ?>
    </div>

  </div>

  <!-- ── GALLERY GRID ──────────────────────── -->
  <div class="sp-gallery">
    <?php
    /* Pull all attached images for gallery */
    $gallery_imgs = get_posts([
      'post_type'      => 'attachment',
      'post_parent'    => $pid,
      'post_mime_type' => 'image',
      'posts_per_page' => 4,
      'orderby'        => 'menu_order',
      'order'          => 'ASC',
    ]);

    if ( count( $gallery_imgs ) >= 2 ) :
      foreach ( $gallery_imgs as $gi ) :
    ?>
    <div class="sp-gallery__item" data-reveal="scale" data-delay="<?php echo ($gi % 4) + 1; ?>">
      <?php echo wp_get_attachment_image( $gi->ID, 'project-hero', false, ['alt' => ''] ); ?>
    </div>
    <?php endforeach;
    else :
      /* Dummy gradient gallery */
      foreach ( $gallery_gradients as $idx => $grad ) :
    ?>
    <div class="sp-gallery__item" data-reveal="scale" data-delay="<?php echo $idx + 1; ?>">
      <div class="sp-gallery__placeholder" style="background:<?php echo esc_attr($grad); ?>">
        <span class="sp-gallery__label">Add Image <?php echo $idx + 1; ?></span>
      </div>
    </div>
    <?php endforeach; endif; ?>
  </div>

  <!-- ── PREV / NEXT NAV ───────────────────── -->
  <nav class="sp-nav" aria-label="Project navigation">

    <?php if ( $prev_p ) : ?>
    <a href="<?php echo esc_url( get_permalink($prev_p) ); ?>" class="sp-nav__link">
      <span class="sp-nav__dir">← Previous</span>
      <span class="sp-nav__title"><?php echo esc_html( get_the_title($prev_p) ); ?></span>
    </a>
    <?php else : ?>
    <div class="sp-nav__link sp-nav__link--empty">
      <span class="sp-nav__dir">← Previous</span>
      <span class="sp-nav__title">—</span>
    </div>
    <?php endif; ?>

    <?php if ( $next_p ) : ?>
    <a href="<?php echo esc_url( get_permalink($next_p) ); ?>" class="sp-nav__link">
      <span class="sp-nav__dir">Next →</span>
      <span class="sp-nav__title"><?php echo esc_html( get_the_title($next_p) ); ?></span>
    </a>
    <?php else : ?>
    <div class="sp-nav__link sp-nav__link--empty">
      <span class="sp-nav__dir">Next →</span>
      <span class="sp-nav__title">—</span>
    </div>
    <?php endif; ?>

  </nav>

</div><!-- /.sp -->

<!-- Trigger hero animation after loader finishes -->
<script>
(function(){
  function trigger(){
    var h = document.getElementById('sp-hero-section');
    if(h) setTimeout(function(){ h.classList.add('in'); }, 200);
  }
  var loader = document.getElementById('loader');
  if(loader){
    loader.addEventListener('transitionend', trigger, {once:true});
    setTimeout(trigger, 2200); /* fallback */
  } else {
    trigger();
  }
})();
</script>

<?php else : ?>
<!-- ════════════════════════════════════════
     REGULAR BLOG POST
     ════════════════════════════════════════ -->
<article class="blog-single">
  <header class="blog-single__header" data-reveal="up">
    <div class="blog-single__meta">
      <span><?php echo get_the_date(); ?></span>
      <span><?php the_category(', '); ?></span>
    </div>
    <h1 class="blog-single__title"><?php the_title(); ?></h1>
  </header>

  <?php if ( has_post_thumbnail() ) : ?>
  <div class="blog-single__hero" data-reveal="scale" data-delay="1">
    <?php the_post_thumbnail('project-hero'); ?>
  </div>
  <?php endif; ?>

  <div class="blog-single__content" data-reveal="up" data-delay="2">
    <?php the_content(); ?>
  </div>
</article>

<?php endif; ?>

<?php endwhile; get_footer(); ?>
