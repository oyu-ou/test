</div><!-- /#page-wrap -->

<!-- Back to top -->
<button class="ax-back-top" aria-label="Back to top">↑</button>

<!-- ═══════════════════════════════════════════════════
     BOTTOM NAVIGATION
     Navigation: About · Web · Video · Photo · Graphic · Contact
     data-filter drives category filtering in both modes.
     href="#ax-…" drives smooth scroll in clean mode.
     ═══════════════════════════════════════════════════ -->
<nav id="ax-nav" role="navigation" aria-label="Primary">
  <div class="nav__inner">

    <ul class="nav__links" role="list">

      <li>
        <a href="#ax-about" class="nav__link" data-filter="all" aria-label="About">
          <span class="nav__link-idx">00</span>About
        </a>
      </li>

      <li>
        <a href="#ax-clean" class="nav__link" data-filter="web" aria-label="Web projects">
          <span class="nav__link-idx">01</span>Web
        </a>
      </li>

      <li>
        <a href="#ax-clean" class="nav__link" data-filter="video" aria-label="Video projects">
          <span class="nav__link-idx">02</span>Video
        </a>
      </li>

      <li>
        <a href="#ax-clean" class="nav__link" data-filter="photo" aria-label="Photo projects">
          <span class="nav__link-idx">03</span>Photo
        </a>
      </li>

      <li>
        <a href="#ax-clean" class="nav__link" data-filter="graphic" aria-label="Graphic projects">
          <span class="nav__link-idx">04</span>Graphic
        </a>
      </li>

      <li>
        <a href="#ax-contact" class="nav__link" data-filter="contact" aria-label="Contact">
          <span class="nav__link-idx">05</span>Contact
        </a>
      </li>

    </ul>

    <!-- Right meta: location -->
    <div class="nav__right t-mono">
      <?php echo esc_html( ax('ou_location','Remote / Global') ); ?>
    </div>

    <!-- Mobile hamburger -->
    <button class="nav__burger" aria-label="Open menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>

  </div>
</nav>

<!-- Mobile drawer -->
<div class="nav__drawer" role="dialog" aria-modal="true" aria-label="Menu">
  <a href="#ax-about"   class="nav__drawer-link" data-filter="all">About</a>
  <a href="#ax-clean"   class="nav__drawer-link" data-filter="web">Web</a>
  <a href="#ax-clean"   class="nav__drawer-link" data-filter="video">Video</a>
  <a href="#ax-clean"   class="nav__drawer-link" data-filter="photo">Photo</a>
  <a href="#ax-clean"   class="nav__drawer-link" data-filter="graphic">Graphic</a>
  <a href="#ax-contact" class="nav__drawer-link" data-filter="contact">Contact</a>
</div>

<?php wp_footer(); ?>
</body>
</html>
