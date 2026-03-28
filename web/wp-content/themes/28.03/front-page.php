<?php
/**
 * AXIOM — front-page.php  (v2 — fully filled)
 * Sections: Hero → Marquee → Work List → Stats → About → Marquee → Contact → Footer
 */
get_header();

$hero_video = ax('axiom_video','');
$discs = array_filter([
    ax('axiom_disc1','Web Design'),
    ax('axiom_disc2','Video'),
    ax('axiom_disc3','Photography'),
    ax('axiom_disc4','Graphic Design'),
]);

/* Portfolio query */
$pq = new WP_Query([
    'post_type' => 'portfolio', 'posts_per_page' => -1,
    'orderby' => 'menu_order', 'order' => 'ASC', 'post_status' => 'publish',
]);
$project_count = $pq->found_posts;

/* Dummy demo projects */
$demo_items = [
    ['title'=>'Brand Identity System',   'cat'=>'graphic','year'=>'2024','client'=>'Studio Noma'],
    ['title'=>'Editorial Film Series',   'cat'=>'video',  'year'=>'2024','client'=>'Vogue Italia'],
    ['title'=>'Web Experience Design',   'cat'=>'web',    'year'=>'2023','client'=>'Arket'],
    ['title'=>'Portrait Series Vol. II', 'cat'=>'photo',  'year'=>'2023','client'=>'Self-initiated'],
    ['title'=>'Motion Campaign',         'cat'=>'video',  'year'=>'2023','client'=>'Nike'],
    ['title'=>'E-Commerce Platform',     'cat'=>'web',    'year'=>'2022','client'=>'Jacquemus'],
    ['title'=>'Landscape Photography',   'cat'=>'photo',  'year'=>'2022','client'=>'GEO Magazine'],
    ['title'=>'Type Specimen Poster',    'cat'=>'graphic','year'=>'2022','client'=>'Self-initiated'],
];

$display_count = $project_count ?: count($demo_items);

/* Marquee words */
$marquee = ['Web Design','Video','Photography','Graphic Design','Art Direction',
            'Motion','Branding','Editorial','Digital','Visual'];
?>

<!-- ══════════════════════════════════════════
     HERO
     ══════════════════════════════════════════ -->
<section id="ax-hero">

  <div class="hero__video-bg" aria-hidden="true">
    <?php if ($hero_video): ?>
    <video id="hero-video" autoplay muted loop playsinline preload="auto">
      <source src="<?php echo esc_url($hero_video); ?>" type="video/mp4">
    </video>
    <?php else: ?>
    <div style="position:absolute;inset:0;background:radial-gradient(ellipse 70% 55% at 65% 40%,rgba(255,214,0,0.055) 0%,transparent 65%),radial-gradient(ellipse 45% 70% at 15% 85%,rgba(255,255,255,0.025) 0%,transparent 60%),#000;"></div>
    <?php endif; ?>
  </div>

  <div class="hero__status">
    <div class="hero__edition t-label">
      <?php echo esc_html(ax('axiom_edition','Issue N°001')); ?><br>
      <?php echo date('Y'); ?>
    </div>
    <div class="hero__availability t-label">
      <span class="hero__avail-dot" aria-hidden="true"></span>
      <?php echo esc_html(ax('axiom_available','Available for new projects')); ?>
    </div>
  </div>

  <div class="hero__content">
    <h1 class="hero__name"><?php echo esc_html(ax('axiom_name', get_bloginfo('name'))); ?></h1>
    <div class="hero__disciplines" role="list">
      <?php foreach ($discs as $d): ?>
      <span class="hero__disc" role="listitem"><?php echo esc_html($d); ?></span>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="hero__scroll">
    <div class="hero__scroll-line" aria-hidden="true"></div>
    <span class="hero__scroll-label">Scroll</span>
  </div>

</section>

<!-- ══════════════════════════════════════════
     MARQUEE
     ══════════════════════════════════════════ -->
<div class="ax-marquee" aria-hidden="true">
  <div class="ax-marquee__track">
    <?php for ($r = 0; $r < 4; $r++): foreach ($marquee as $w): ?>
    <span class="ax-marquee__item"><?php echo esc_html($w); ?><span class="ax-marquee__sep"></span></span>
    <?php endforeach; endfor; ?>
  </div>
</div>

<!-- ══════════════════════════════════════════
     WORK LIST
     ══════════════════════════════════════════ -->
<section id="ax-clean">

  <div class="clean__header">
    <div>
      <div class="ax-section-label" data-reveal="fade">Selected Work</div>
      <h2 class="clean__title" data-reveal="up">Projects</h2>
    </div>
    <span class="clean__count t-label" data-reveal="up" data-delay="2">
      <?php echo str_pad($display_count, 2, '0', STR_PAD_LEFT); ?> Total
    </span>
  </div>

  <div class="clean__list" role="list">
  <?php if ($pq->have_posts()):
    $i = 0;
    while ($pq->have_posts()): $pq->the_post();
      $pid   = get_the_ID();
      $terms = get_the_terms($pid, 'project_type');
      $cat   = $terms ? strtolower($terms[0]->slug) : 'all';
      $label = $terms ? $terms[0]->name : '';
      $year  = get_post_meta($pid, 'axiom_year', true) ?: date('Y');
      $thumb = get_the_post_thumbnail_url($pid, 'project-preview');
      $num   = str_pad($i + 1, 2, '0', STR_PAD_LEFT);
  ?>
    <a class="clean__item" href="<?php the_permalink(); ?>"
       role="listitem" data-cat="<?php echo esc_attr($cat); ?>"
       data-img="<?php echo esc_attr($thumb ?: ''); ?>">
      <span class="clean__num"><?php echo esc_html($num); ?></span>
      <span class="clean__name"><?php the_title(); ?></span>
      <span class="clean__cat t-label"><?php echo esc_html($label); ?></span>
      <span class="clean__year t-mono"><?php echo esc_html($year); ?></span>
      <span class="clean__arrow" aria-hidden="true">↗</span>
    </a>
  <?php $i++; endwhile; wp_reset_postdata();
  else:
    foreach ($demo_items as $k => $p):
      $num = str_pad($k + 1, 2, '0', STR_PAD_LEFT);
  ?>
    <div class="clean__item" role="listitem"
         data-cat="<?php echo esc_attr($p['cat']); ?>" data-img="">
      <span class="clean__num"><?php echo esc_html($num); ?></span>
      <span class="clean__name"><?php echo esc_html($p['title']); ?></span>
      <span class="clean__cat t-label"><?php echo esc_html(ucfirst($p['cat'])); ?></span>
      <span class="clean__year t-mono"><?php echo esc_html($p['year']); ?></span>
      <span class="clean__arrow" aria-hidden="true">↗</span>
    </div>
  <?php endforeach; endif; ?>
  </div>

</section>

<!-- ══════════════════════════════════════════
     STATS
     ══════════════════════════════════════════ -->
<div class="ax-stats" role="list">
  <?php
  $stats = [
    ['120+','Projects Delivered'],
    ['8','Years Active'],
    ['14','Awards'],
    ['60+','Happy Clients'],
  ];
  foreach ($stats as $i => $s): ?>
  <div class="ax-stat" role="listitem" data-reveal="up" data-delay="<?php echo $i+1; ?>">
    <div class="ax-stat__num"><?php echo esc_html($s[0]); ?></div>
    <div class="ax-stat__label"><?php echo esc_html($s[1]); ?></div>
  </div>
  <?php endforeach; ?>
</div>

<!-- ══════════════════════════════════════════
     ABOUT
     ══════════════════════════════════════════ -->
<section id="ax-about">
  <div class="about__grid">

    <div>
      <div class="ax-section-label" data-reveal="fade">About</div>
      <h2 class="about__heading" data-reveal="up">
        <?php echo esc_html(ax('axiom_about_h1','Making things')); ?><br>
        <?php echo esc_html(ax('axiom_about_h2','that matter.')); ?>
      </h2>
    </div>

    <div class="about__body">
      <p class="about__text" data-reveal="up" data-delay="1">
        <?php echo esc_html(ax('axiom_about_text',
          'I design and build digital experiences for brands with something to say. ' .
          'From motion to identity, web to editorial — the work is always rooted in intention.'
        )); ?>
      </p>

      <div class="about__tags" data-reveal="up" data-delay="2">
        <?php for ($t = 1; $t <= 5; $t++):
          $tag = ax('axiom_tag'.$t,'');
          if ($tag): ?>
        <span class="about__tag t-label"><?php echo esc_html($tag); ?></span>
        <?php endif; endfor;
        /* Fallback tags if customizer is empty */
        if (!ax('axiom_tag1','')): ?>
        <span class="about__tag t-label">Web Design</span>
        <span class="about__tag t-label">Video</span>
        <span class="about__tag t-label">Photography</span>
        <span class="about__tag t-label">Graphic Design</span>
        <span class="about__tag t-label">Art Direction</span>
        <?php endif; ?>
      </div>

      <a href="#ax-contact" class="about__cta" data-reveal="up" data-delay="3">
        Start a conversation <span aria-hidden="true">→</span>
      </a>
    </div>

  </div>
</section>

<!-- ══════════════════════════════════════════
     SECOND MARQUEE (reversed)
     ══════════════════════════════════════════ -->
<div class="ax-marquee" aria-hidden="true">
  <div class="ax-marquee__track" style="animation-direction:reverse;animation-duration:36s;">
    <?php for ($r = 0; $r < 4; $r++): foreach (array_reverse($marquee) as $w): ?>
    <span class="ax-marquee__item" style="color:var(--fg-low);"><?php echo esc_html($w); ?><span class="ax-marquee__sep" style="background:var(--fg-low)"></span></span>
    <?php endforeach; endfor; ?>
  </div>
</div>

<!-- ══════════════════════════════════════════
     CONTACT
     ══════════════════════════════════════════ -->
<section id="ax-contact">

  <div class="contact__inner">
    <div class="ax-section-label" data-reveal="fade">Contact</div>

    <h2 class="contact__big" data-reveal="up">
      <?php echo esc_html(ax('axiom_contact_h',"Let's work.")); ?>
    </h2>

    <div class="contact__grid">

      <p class="contact__text" data-reveal="up" data-delay="1">
        <?php echo esc_html(ax('axiom_contact_t',
          'Available for select projects worldwide. ' .
          'Get in touch to start a conversation about what we can build together.'
        )); ?>
      </p>

      <div class="contact__links" data-reveal="up" data-delay="2">
        <?php $email = ax('axiom_email','hello@yoursite.com'); ?>
        <a href="mailto:<?php echo esc_attr($email); ?>" class="contact__link">
          <?php echo esc_html($email); ?> <span aria-hidden="true">↗</span>
        </a>
        <?php
        $socials = [
          'axiom_instagram'=>'Instagram','axiom_behance'=>'Behance',
          'axiom_linkedin'=>'LinkedIn','axiom_vimeo'=>'Vimeo',
        ];
        foreach ($socials as $key => $label):
          $url = ax($key,'');
          if ($url): ?>
        <a href="<?php echo esc_url($url); ?>" class="contact__link" target="_blank" rel="noopener">
          <?php echo esc_html($label); ?> <span aria-hidden="true">↗</span>
        </a>
        <?php endif; endforeach; ?>
      </div>

    </div>
  </div>

  <div class="ax-footer">
    <span class="ax-footer__copy">
      &copy; <?php echo date('Y'); ?>
      <?php echo esc_html(ax('axiom_name', get_bloginfo('name'))); ?>.
      All rights reserved.
    </span>
    <span class="ax-footer__loc">
      <?php echo esc_html(ax('axiom_location','Remote / Global')); ?>
    </span>
  </div>

</section>

<?php get_footer(); ?>
