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

<style>
/* ── Single project page styles ────────────────────── */
.sp { padding-bottom: calc(var(--nav-h) + 40px); }

/* ── Fullscreen hero ─────────────────────────────── */
.sp-hero {
  position: relative;
  width: 100%;
  height: 100svh;
  min-height: 560px;
  overflow: hidden;
  display: flex;
  align-items: flex-end;
}

.sp-hero__bg {
  position: absolute;
  inset: 0;
  z-index: 0;
}

.sp-hero__bg img {
  width: 100%; height: 100%;
  object-fit: cover;
}

/* Gradient fallback when no image */
.sp-hero__bg--gradient {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg,#080808 0%,#0f0800 40%,#1a1000 100%);
}

/* Animated grid lines overlay (decorative) */
.sp-hero__grid {
  position: absolute;
  inset: 0;
  z-index: 1;
  background-image:
    linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
  background-size: 80px 80px;
  mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 40%, transparent 100%);
  pointer-events: none;
}

.sp-hero__scrim {
  position: absolute;
  inset: 0;
  z-index: 2;
  background: linear-gradient(
    to top,
    rgba(0,0,0,0.92) 0%,
    rgba(0,0,0,0.3)  50%,
    rgba(0,0,0,0.1)  100%
  );
}

[data-theme="light"] .sp-hero__scrim {
  background: linear-gradient(
    to top,
    rgba(255,255,255,0.94) 0%,
    rgba(255,255,255,0.2)  50%,
    transparent 100%
  );
}

.sp-hero__info {
  position: relative;
  z-index: 3;
  width: 100%;
  padding: var(--pad-x);
  padding-bottom: calc(var(--nav-h) + 56px);
  display: grid;
  grid-template-columns: 1fr auto;
  align-items: flex-end;
  gap: 40px;
}

.sp-hero__cat {
  font-family: var(--font-mono);
  font-size: var(--sz-xs);
  letter-spacing: var(--ls-widest);
  text-transform: uppercase;
  color: var(--accent);
  margin-bottom: 16px;

  opacity: 0;
  transform: translateX(-20px);
  transition: opacity 0.7s var(--ease-out) 0.4s, transform 0.7s var(--ease-out) 0.4s;
}

.sp-hero__title {
  font-size: clamp(2.5rem, 6vw, 7rem);
  font-weight: var(--w-thin);
  letter-spacing: var(--ls-tight);
  line-height: var(--lh-snug);
  max-width: 14ch;

  opacity: 0;
  transform: translateY(30px);
  transition: opacity 0.9s var(--ease-out) 0.5s, transform 0.9s var(--ease-out) 0.5s;
}

.sp-hero__right {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 12px;
  padding-bottom: 6px;
  opacity: 0;
  transition: opacity 0.7s var(--ease-out) 0.7s;
}

.sp-hero__year {
  font-family: var(--font-mono);
  font-size: var(--sz-xs);
  letter-spacing: var(--ls-wide);
  color: var(--fg-mid);
}

.sp-hero__open {
  font-family: var(--font-mono);
  font-size: var(--sz-xs);
  letter-spacing: var(--ls-widest);
  text-transform: uppercase;
  color: var(--fg);
  display: inline-flex;
  align-items: center;
  gap: 8px;
  border-bottom: 1px solid var(--fg-mid);
  padding-bottom: 2px;
  transition: gap 0.3s var(--ease-out), color 0.2s;
}

.sp-hero__open:hover { gap: 16px; color: var(--accent); border-color: var(--accent); }

/* Hero in state — triggered by JS after loader */
.sp-hero.in .sp-hero__cat   { opacity: 1; transform: none; }
.sp-hero.in .sp-hero__title { opacity: 1; transform: none; }
.sp-hero.in .sp-hero__right { opacity: 1; }

/* ── Meta strip ──────────────────────────────────── */
.sp-meta {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  border-bottom: 1px solid var(--border);
}

.sp-meta__item {
  padding: 36px var(--pad-x);
  border-right: 1px solid var(--border);
}

.sp-meta__item:last-child { border-right: none; }

.sp-meta__label {
  font-family: var(--font-mono);
  font-size: var(--sz-xs);
  letter-spacing: var(--ls-widest);
  text-transform: uppercase;
  color: var(--fg-mid);
  margin-bottom: 10px;
}

.sp-meta__value {
  font-size: var(--sz-md);
  font-weight: var(--w-light);
  line-height: 1.3;
}

/* ── Body content ────────────────────────────────── */
.sp-body {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: clamp(40px,8vw,120px);
  padding: clamp(60px,10vw,120px) var(--pad-x);
  align-items: start;
  border-bottom: 1px solid var(--border);
}

.sp-body__text p {
  font-size: var(--sz-md);
  line-height: var(--lh-loose);
  color: var(--fg-mid);
  margin-bottom: 24px;
}

.sp-body__text p:last-child { margin-bottom: 0; }

.sp-body__sticky {
  position: sticky;
  top: calc(var(--nav-h) + 40px);
  overflow: hidden;
}

.sp-body__sticky img {
  width: 100%;
  aspect-ratio: 4/3;
  object-fit: cover;
  display: block;
  transition: transform 0.8s var(--ease-out);
}

.sp-body__sticky:hover img { transform: scale(1.03); }

/* Placeholder for sticky image */
.sp-img-placeholder {
  width: 100%;
  aspect-ratio: 4/3;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* ── Gallery row ─────────────────────────────────── */
.sp-gallery {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: var(--gap);
}

.sp-gallery__item {
  position: relative;
  overflow: hidden;
}

.sp-gallery__item img {
  width: 100%; height: 100%;
  object-fit: cover;
  aspect-ratio: 4/3;
  display: block;
  transition: transform 0.8s var(--ease-out);
}

.sp-gallery__item:hover img { transform: scale(1.04); }

/* Placeholder gradient gallery item */
.sp-gallery__placeholder {
  width: 100%;
  aspect-ratio: 4/3;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
}

.sp-gallery__placeholder::after {
  content: '';
  position: absolute;
  inset: 0;
  background-image: repeating-linear-gradient(
    0deg, transparent, transparent 3px,
    rgba(255,255,255,0.012) 3px, rgba(255,255,255,0.012) 6px
  );
}

.sp-gallery__label {
  font-family: var(--font-mono);
  font-size: var(--sz-xs);
  letter-spacing: var(--ls-widest);
  text-transform: uppercase;
  color: rgba(255,255,255,0.2);
  position: relative;
  z-index: 1;
}

/* ── Project nav (prev/next) ─────────────────────── */
.sp-nav {
  display: grid;
  grid-template-columns: 1fr 1fr;
  border-top: 1px solid var(--border);
}

.sp-nav__link {
  display: flex;
  flex-direction: column;
  gap: 14px;
  padding: clamp(32px,6vw,60px) var(--pad-x);
  border-right: 1px solid var(--border);
  transition: background var(--t-fast);
  position: relative;
  overflow: hidden;
}

.sp-nav__link:last-child { border-right: none; align-items: flex-end; }
.sp-nav__link:hover { background: var(--fg-lower); }

.sp-nav__link--empty {
  opacity: 0.18;
  pointer-events: none;
}

.sp-nav__dir {
  font-family: var(--font-mono);
  font-size: var(--sz-xs);
  letter-spacing: var(--ls-widest);
  text-transform: uppercase;
  color: var(--fg-mid);
}

.sp-nav__title {
  font-size: clamp(1.1rem, 2.5vw, 1.8rem);
  font-weight: var(--w-thin);
  letter-spacing: var(--ls-tight);
  transition: color var(--t-fast);
}

.sp-nav__link:hover .sp-nav__title { color: var(--accent); }

/* ── Back to all link ────────────────────────────── */
.sp-back {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  font-family: var(--font-mono);
  font-size: var(--sz-xs);
  letter-spacing: var(--ls-widest);
  text-transform: uppercase;
  color: var(--fg-mid);
  border-bottom: 1px solid var(--border);
  padding-bottom: 2px;
  margin: 40px var(--pad-x);
  transition: color var(--t-fast), border-color var(--t-fast), gap 0.3s var(--ease-out);
}

.sp-back:hover { color: var(--fg); border-color: var(--fg); gap: 18px; }

/* ── Responsive ──────────────────────────────────── */
@media (max-width: 900px) {
  .sp-hero__info  { grid-template-columns: 1fr; }
  .sp-hero__right { align-items: flex-start; }
  .sp-body        { grid-template-columns: 1fr; }
  .sp-body__sticky { position: static; }
  .sp-meta        { grid-template-columns: repeat(2, 1fr); }
  .sp-meta__item  { border-right: none; border-bottom: 1px solid var(--border); }
}

@media (max-width: 600px) {
  .sp-gallery { grid-template-columns: 1fr; }
  .sp-nav     { grid-template-columns: 1fr; }
  .sp-nav__link { border-right: none; border-bottom: 1px solid var(--border); }
  .sp-nav__link:last-child { align-items: flex-start; }
}
</style>

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

<style>
.blog-single {
  padding: calc(90px + var(--nav-h)) var(--pad-x) calc(var(--nav-h) + 80px);
  max-width: 840px;
}

.blog-single__header { margin-bottom: 64px; }

.blog-single__meta {
  font-family: var(--font-mono);
  font-size: var(--sz-xs);
  letter-spacing: var(--ls-widest);
  text-transform: uppercase;
  color: var(--fg-mid);
  margin-bottom: 24px;
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
}

.blog-single__title {
  font-size: var(--sz-3xl);
  font-weight: var(--w-thin);
  letter-spacing: var(--ls-tight);
  line-height: var(--lh-snug);
}

.blog-single__hero {
  margin-bottom: 60px;
  overflow: hidden;
}

.blog-single__hero img { width: 100%; display: block; }

.blog-single__content {
  font-size: var(--sz-md);
  line-height: var(--lh-loose);
  color: var(--fg-mid);
}

.blog-single__content p  { margin-bottom: 24px; }
.blog-single__content h2 { font-size: var(--sz-xl); font-weight: var(--w-thin); letter-spacing: var(--ls-tight); color: var(--fg); margin: 48px 0 20px; }
.blog-single__content h3 { font-size: var(--sz-lg); font-weight: var(--w-light); color: var(--fg); margin: 36px 0 16px; }
.blog-single__content a  { color: var(--accent); border-bottom: 1px solid var(--accent-dim); }
</style>

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
