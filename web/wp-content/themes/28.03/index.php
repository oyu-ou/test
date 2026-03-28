<?php
/**
 * ou — index.php
 * Archive, blog, search, and category fallback.
 * Matches the clean editorial aesthetic of the homepage list.
 */
get_header(); ?>

<style>
.arch {
  padding-top: calc(90px + var(--nav-h));
  padding-bottom: calc(var(--nav-h) + 80px);
}

.arch__header {
  padding: 0 var(--pad-x) 48px;
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 24px;
  flex-wrap: wrap;
}

.arch__label {
  font-family: var(--font-mono);
  font-size: var(--sz-xs);
  letter-spacing: var(--ls-widest);
  text-transform: uppercase;
  color: var(--accent);
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  gap: 12px;
}

.arch__label::before {
  content: '';
  display: block;
  width: 20px; height: 1px;
  background: var(--accent);
}

.arch__title {
  font-size: var(--sz-3xl);
  font-weight: var(--w-thin);
  letter-spacing: var(--ls-tight);
  line-height: var(--lh-snug);
}

.arch__count {
  font-family: var(--font-mono);
  font-size: var(--sz-xs);
  letter-spacing: var(--ls-widest);
  text-transform: uppercase;
  color: var(--fg-mid);
  padding-bottom: 4px;
}

/* Post row */
.arch__item {
  display: grid;
  grid-template-columns: 48px 1fr auto auto;
  align-items: center;
  gap: 0 28px;
  padding: 28px var(--pad-x);
  border-bottom: 1px solid var(--border);
  text-decoration: none;
  color: var(--fg);
  transition: background var(--t-fast), padding-left var(--t-base) var(--ease-out);

  opacity: 0;
  transform: translateY(10px);
  transition: opacity var(--t-slow) var(--ease-out),
              transform var(--t-slow) var(--ease-out),
              background var(--t-fast);
}

.arch__item.is-on { opacity: 1; transform: none; }
.arch__item:hover  { background: var(--fg-lower); }

.arch__item-num {
  font-family: var(--font-mono);
  font-size: var(--sz-xs);
  letter-spacing: var(--ls-wide);
  color: var(--fg-mid);
  font-variant-numeric: tabular-nums;
}

.arch__item-title {
  font-size: clamp(1rem, 2vw, 1.5rem);
  font-weight: var(--w-thin);
  letter-spacing: var(--ls-tight);
  transition: color var(--t-fast);
}

.arch__item:hover .arch__item-title { color: var(--accent); }

.arch__item-cat {
  font-family: var(--font-mono);
  font-size: var(--sz-xs);
  letter-spacing: var(--ls-wider);
  text-transform: uppercase;
  color: var(--fg-mid);
  white-space: nowrap;
}

.arch__item-date {
  font-family: var(--font-mono);
  font-size: var(--sz-xs);
  letter-spacing: var(--ls-wide);
  color: var(--fg-low);
  white-space: nowrap;
}

/* Arrow */
.arch__item::after {
  content: '↗';
  font-size: var(--sz-md);
  color: var(--accent);
  opacity: 0;
  transition: opacity var(--t-fast);
  position: absolute;
  right: var(--pad-x);
}

.arch__item { position: relative; }
.arch__item:hover::after { opacity: 1; }

/* Pagination */
.arch__pagination {
  padding: 40px var(--pad-x);
  display: flex;
  gap: 8px;
  align-items: center;
}

.arch__page-link {
  font-family: var(--font-mono);
  font-size: var(--sz-xs);
  letter-spacing: var(--ls-wider);
  text-transform: uppercase;
  padding: 8px 16px;
  border: 1px solid var(--border);
  color: var(--fg-mid);
  transition: border-color var(--t-fast), color var(--t-fast), background var(--t-fast);
}

.arch__page-link:hover,
.arch__page-link.current {
  border-color: var(--fg);
  color: var(--fg);
  background: var(--fg);
  color: var(--bg);
}

/* Empty state */
.arch__empty {
  padding: 80px var(--pad-x);
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.arch__empty-num {
  font-size: var(--sz-hero);
  font-weight: var(--w-thin);
  letter-spacing: var(--ls-tight);
  color: var(--fg-lower);
  line-height: 1;
}

.arch__empty-msg {
  font-size: var(--sz-xl);
  font-weight: var(--w-thin);
  letter-spacing: var(--ls-tight);
  color: var(--fg-mid);
}

.arch__empty-back {
  font-family: var(--font-mono);
  font-size: var(--sz-xs);
  letter-spacing: var(--ls-widest);
  text-transform: uppercase;
  color: var(--fg);
  display: inline-flex;
  align-items: center;
  gap: 10px;
  border-bottom: 1px solid var(--fg);
  padding-bottom: 2px;
  width: fit-content;
  transition: gap var(--t-base) var(--ease-out), color var(--t-fast);
}
.arch__empty-back:hover { gap: 18px; color: var(--accent); border-color: var(--accent); }

@media (max-width: 600px) {
  .arch__item { grid-template-columns: 36px 1fr; flex-wrap: wrap; }
  .arch__item-cat, .arch__item-date { display: none; }
}
</style>

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
  if (window.AxScroll) AxScroll.init();
  var items = document.querySelectorAll('.arch__item');
  var io = new IntersectionObserver(function(entries){
    entries.forEach(function(e){ if(e.isIntersecting){ e.target.classList.add('is-on'); io.unobserve(e.target); } });
  }, { threshold:0.04, rootMargin:'0px 0px -20px 0px' });
  items.forEach(function(el){ io.observe(el); });
});
</script>

<?php get_footer(); ?>
