<?php
/**
 * ou — 404.php
 * Animated not-found page. Big 404 ghost number + editorial layout.
 */
get_header(); ?>

<style>
.e404 {
  min-height: 100svh;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  padding: var(--pad-x);
  padding-top: calc(90px + var(--nav-h));
  padding-bottom: calc(var(--nav-h) + 64px);
  position: relative;
  overflow: hidden;
}

/* Giant ghost number */
.e404__ghost {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -55%);
  font-size: clamp(18rem, 40vw, 38rem);
  font-weight: var(--w-thin);
  letter-spacing: var(--ls-tight);
  color: var(--fg-lower);
  pointer-events: none;
  user-select: none;
  line-height: 1;
  white-space: nowrap;

  /* Fade-in on load */
  opacity: 0;
  animation: ghostIn 1.4s var(--ease-out) 0.6s forwards;
}

@keyframes ghostIn {
  from { opacity: 0; transform: translate(-50%, -45%); }
  to   { opacity: 1; transform: translate(-50%, -55%); }
}

/* Content */
.e404__content {
  position: relative;
  z-index: 2;
  max-width: 680px;
}

.e404__label {
  font-family: var(--font-mono);
  font-size: var(--sz-xs);
  letter-spacing: var(--ls-widest);
  text-transform: uppercase;
  color: var(--accent);
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 12px;
  opacity: 0;
  transform: translateX(-20px);
  animation: slideIn 0.7s var(--ease-out) 0.9s forwards;
}

.e404__label::before {
  content: '';
  display: block;
  width: 20px; height: 1px;
  background: var(--accent);
}

.e404__heading {
  font-size: var(--sz-3xl);
  font-weight: var(--w-thin);
  letter-spacing: var(--ls-tight);
  line-height: var(--lh-snug);
  margin-bottom: 32px;
  opacity: 0;
  transform: translateY(24px);
  animation: slideUp 0.9s var(--ease-out) 1.0s forwards;
}

.e404__text {
  font-size: var(--sz-base);
  line-height: var(--lh-loose);
  color: var(--fg-mid);
  margin-bottom: 48px;
  max-width: 40ch;
  opacity: 0;
  transform: translateY(16px);
  animation: slideUp 0.8s var(--ease-out) 1.1s forwards;
}

.e404__actions {
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
  opacity: 0;
  transform: translateY(12px);
  animation: slideUp 0.7s var(--ease-out) 1.2s forwards;
}

.e404__btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  font-family: var(--font-mono);
  font-size: var(--sz-xs);
  letter-spacing: var(--ls-widest);
  text-transform: uppercase;
  padding: 12px 24px;
  background: var(--fg);
  color: var(--bg);
  border: 1px solid var(--fg);
  transition: background var(--t-fast), color var(--t-fast), gap var(--t-base) var(--ease-out);
}

.e404__btn-primary:hover {
  background: var(--accent);
  border-color: var(--accent);
  color: var(--bg);
  gap: 18px;
}

.e404__btn-ghost {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  font-family: var(--font-mono);
  font-size: var(--sz-xs);
  letter-spacing: var(--ls-widest);
  text-transform: uppercase;
  padding: 12px 24px;
  background: transparent;
  color: var(--fg-mid);
  border: 1px solid var(--border);
  transition: border-color var(--t-fast), color var(--t-fast);
}

.e404__btn-ghost:hover {
  border-color: var(--fg);
  color: var(--fg);
}

/* Suggested links */
.e404__suggestions {
  position: absolute;
  bottom: calc(var(--nav-h) + 48px);
  right: var(--pad-x);
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 8px;
  opacity: 0;
  animation: fadeIn 0.8s var(--ease-out) 1.4s forwards;
}

.e404__suggestions-label {
  font-family: var(--font-mono);
  font-size: var(--sz-xs);
  letter-spacing: var(--ls-widest);
  text-transform: uppercase;
  color: var(--fg-low);
  margin-bottom: 12px;
}

.e404__sugg-link {
  font-size: var(--sz-md);
  font-weight: var(--w-thin);
  letter-spacing: var(--ls-tight);
  color: var(--fg-mid);
  display: flex;
  align-items: center;
  gap: 8px;
  transition: color var(--t-fast), gap var(--t-base) var(--ease-out);
}

.e404__sugg-link:hover {
  color: var(--accent);
  gap: 14px;
}

@keyframes slideIn {
  from { opacity: 0; transform: translateX(-20px); }
  to   { opacity: 1; transform: translateX(0); }
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(20px); }
  to   { opacity: 1; transform: translateY(0); }
}

@keyframes fadeIn {
  from { opacity: 0; }
  to   { opacity: 1; }
}

@media (max-width: 600px) {
  .e404__ghost { font-size: clamp(10rem, 40vw, 18rem); }
  .e404__suggestions { display: none; }
}
</style>

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
