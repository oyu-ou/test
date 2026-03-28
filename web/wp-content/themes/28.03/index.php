<?php
/**
 * ou — index.php
 * Archive, blog, search, and category fallback.
 * Matches the clean editorial aesthetic of the homepage list.
 */
get_header(); ?>
<main class="arch">

  <!-- Header -->
  <div class="arch__header">
    <div data-reveal="up">
      <div class="arch__label">
        <?php
        if      ( is_search()   ) echo 'Search';
        elseif  ( is_category() ) echo 'Category';
        elseif  ( is_tag()      ) echo 'Tag';
        elseif  ( is_archive()  ) echo 'Archive';
        else                      echo 'Journal';
        ?>
      </div>
      <h1 class="arch__title">
        <?php
        if      ( is_search()   ) echo 'Results for &ldquo;' . esc_html(get_search_query()) . '&rdquo;';
        elseif  ( is_category() ) single_cat_title();
        elseif  ( is_tag()      ) single_tag_title();
        elseif  ( is_archive()  ) the_archive_title();
        else                      bloginfo('name');
        ?>
      </h1>
    </div>
    <?php if ( have_posts() ) : global $wp_query; ?>
    <span class="arch__count" data-reveal="up" data-delay="2">
      <?php echo str_pad( $wp_query->found_posts, 2, '0', STR_PAD_LEFT ); ?> Posts
    </span>
    <?php endif; ?>
  </div>

  <?php if ( have_posts() ) :
    $i = 0;
    while ( have_posts() ) : the_post();
      $num  = str_pad($i + 1, 2, '0', STR_PAD_LEFT);
      $cats = get_the_category();
      $cat  = $cats ? $cats[0]->name : '';
  ?>
  <a class="arch__item" href="<?php the_permalink(); ?>">
    <span class="arch__item-num"><?php echo esc_html($num); ?></span>
    <span class="arch__item-title"><?php the_title(); ?></span>
    <span class="arch__item-cat"><?php echo esc_html($cat); ?></span>
    <span class="arch__item-date"><?php echo get_the_date('Y'); ?></span>
  </a>
  <?php $i++; endwhile; ?>

  <!-- Pagination -->
  <?php
  $pagination = paginate_links([
    'type'      => 'array',
    'prev_text' => '← Prev',
    'next_text' => 'Next →',
  ]);
  if ($pagination) : ?>
  <div class="arch__pagination">
    <?php foreach ($pagination as $link) :
      // Add class to current page span
      echo str_replace(['<a ', '<span '], ['<a class="arch__page-link" ', '<span class="arch__page-link current" '], $link);
    endforeach; ?>
  </div>
  <?php endif; ?>

  <?php else : ?>

  <!-- Empty state -->
  <div class="arch__empty" data-reveal="up">
    <div class="arch__empty-num">∅</div>
    <p class="arch__empty-msg">
      <?php echo is_search() ? 'Nothing matched that search.' : 'Nothing here yet.'; ?>
    </p>
    <a href="<?php echo esc_url(home_url('/')); ?>" class="arch__empty-back">
      <span aria-hidden="true">←</span> Back home
    </a>
  </div>

  <?php endif; ?>

</main>

<!-- Re-init reveals for this page -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  if (window.OuScroll) OuScroll.init();
  var items = document.querySelectorAll('.arch__item');
  var io = new IntersectionObserver(function(entries){
    entries.forEach(function(e){ if(e.isIntersecting){ e.target.classList.add('is-on'); io.unobserve(e.target); } });
  }, { threshold:0.04, rootMargin:'0px 0px -20px 0px' });
  items.forEach(function(el){ io.observe(el); });
});
</script>

<?php get_footer(); ?>
