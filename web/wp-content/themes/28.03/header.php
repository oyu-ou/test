<!DOCTYPE html>
<html <?php language_attributes(); ?> data-theme="dark" data-mode="clean">
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#000000">
<?php wp_head(); ?>
<!-- Apply saved theme + mode immediately to avoid FOUC -->
<script>
(function(){
  var t=localStorage.getItem('ou-theme');
  var m=localStorage.getItem('ou-mode');
  if(t) document.documentElement.setAttribute('data-theme',t);
  if(m) document.documentElement.setAttribute('data-mode',m);
})();
</script>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- LOADER -->
<div id="loader" role="status" aria-label="Loading">
  <span class="loader__site-name t-label"><?php echo esc_html( ou('ou_name', get_bloginfo('name')) ); ?></span>
  
  <div class="loader__bar-wrap"><div class="loader__bar"></div></div>
  
  <div class="loader__num"><span class="loader__num-inner">00</span></div>
</div>

<!-- TOP BAR -->
<header id="ou-header">
  <a href="<?php echo esc_url( home_url('/') ); ?>" class="topbar__logo" aria-label="<?php echo esc_attr( ou('ou_name', get_bloginfo('name')) ); ?>">
      <?php if ( has_custom_logo() ) :
        the_custom_logo();
      else : ?>
    <?php endif; ?>
  </a>

  <div class="topbar__right">
    <!-- Dark / Light toggle -->
    <button id="toggle-theme" class="ou-toggle" aria-label="Toggle colour scheme" aria-pressed="false">
      Light
    </button>

    <!-- CLEAN / WILD mode toggle -->
    <button id="toggle-mode" class="ou-toggle" aria-label="Toggle portfolio mode" aria-pressed="false">
      Wild ↗
    </button>
  </div>

</header>

<!-- WILD MODE -->
<div id="ou-slider" aria-label="Portfolio slider" role="region">

  <!-- Counter -->
  <div class="wild__counter">
    <span class="wild__counter-current">01</span>
    <span class="wild__counter-sep">/</span>
    <span class="wild__counter-total">00</span>
  </div>

  <!-- Progress dots -->
  <div class="wild__dots" role="tablist" aria-label="Slide navigation"></div>

  <!-- Slide track — populated by slider.js from ouData.projects -->
  <div class="wild__track"></div>

  <!-- Nav arrows -->
  <div class="wild__arrows">
    <button class="wild__arrow wild__arrow--prev" aria-label="Previous project">←</button>
    <button class="wild__arrow wild__arrow--next" aria-label="Next project">→</button>
  </div>

</div>

<!-- HOVER PREVIEW (clean list mode) -->
<div id="ou-preview" aria-hidden="true">
  <img src="" alt="" loading="lazy">
  <div class="preview__placeholder"></div>
</div>

<!-- Back to top -->
<button class="ou-back-top" aria-label="Back to top">↑</button>

<!-- PAGE WRAPPER -->
<div id="page-wrap">