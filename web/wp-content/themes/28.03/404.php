<?php
/**
 * ou — 404.php
 * Animated not-found page. Big 404 ghost number + editorial layout.
 */
get_header(); ?>
<main class="e404">

  <!-- Giant ghost 404 -->
  <div class="e404__ghost" aria-hidden="true">404</div>

  <!-- Content -->
  <div class="e404__content">

    <div class="e404__label">Error 404</div>

    <h1 class="e404__heading">
      Page not<br>found.
    </h1>

    <p class="e404__text">
      The page you were looking for doesn't exist,
      was moved, or never existed in the first place.
      It happens.
    </p>

    <div class="e404__actions">
      <a href="<?php echo esc_url(home_url('/')); ?>" class="e404__btn-primary">
        Back home <span aria-hidden="true">→</span>
      </a>
      <a href="<?php echo esc_url(home_url('/') . '#work'); ?>" class="e404__btn-ghost">
        View work
      </a>
    </div>

  </div>

  <!-- Suggested navigation -->
  <div class="e404__suggestions" aria-label="Site sections">
    <p class="e404__suggestions-label">Quick links</p>
    <a href="<?php echo esc_url(home_url('/')); ?>#work"   class="e404__sugg-link">Work <span aria-hidden="true">↗</span></a>
    <a href="<?php echo esc_url(home_url('/')); ?>#about"   class="e404__sugg-link">About <span aria-hidden="true">↗</span></a>
    <a href="<?php echo esc_url(home_url('/')); ?>#contact" class="e404__sugg-link">Contact <span aria-hidden="true">↗</span></a>
  </div>

</main>

<?php get_footer(); ?>
