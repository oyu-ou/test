/**
 * AXIOM — cursor.js
 * Two-element cursor: dot (exact follow) + ring (lerp lag).
 * Fully theme-aware — visible on both dark and light backgrounds.
 * Uses event delegation so dynamically added elements work too.
 */
(function () {
  'use strict';
  if (window.matchMedia('(hover: none)').matches) return;

  var wrap, dot, ring;
  var mx = 0, my = 0, rx = 0, ry = 0;

  /* ── Build DOM ──────────────────────────────────── */
  function build() {
    wrap = document.createElement('div');
    wrap.className = 'ax-cursor';
    wrap.setAttribute('aria-hidden','true');
    dot  = document.createElement('div'); dot.className  = 'ax-cursor__dot';
    ring = document.createElement('div'); ring.className = 'ax-cursor__ring';
    wrap.appendChild(dot);
    wrap.appendChild(ring);
    document.body.appendChild(wrap);
  }

  /* ── Track mouse position ───────────────────────── */
  document.addEventListener('mousemove', function (e) {
    mx = e.clientX; my = e.clientY;
    /* Dot follows exactly */
    dot.style.left = mx + 'px';
    dot.style.top  = my + 'px';
  });

  /* ── Lerp ring animation ────────────────────────── */
  function loop() {
    requestAnimationFrame(loop);
    rx += (mx - rx) * 0.1;
    ry += (my - ry) * 0.1;
    ring.style.left = rx + 'px';
    ring.style.top  = ry + 'px';
  }

  /* ── Click feedback ─────────────────────────────── */
  document.addEventListener('mousedown', function () { wrap && wrap.classList.add('clicking'); });
  document.addEventListener('mouseup',   function () { wrap && wrap.classList.remove('clicking'); });

  /* ── State detection (event delegation) ─────────── */
  var LINK_SEL = [
    'a', 'button', 'label', '[role="button"]',
    '.nav__link', '.nav__drawer-link', '.ax-toggle',
    '.clean__item', '.wild__arrow', '.wild__dot',
    '.wild__open', '.about__cta', '.contact__link',
    '.sp-nav__link', '.sp-back', '.ax-back-top',
    '.filter-btn', '.topbar__logo',
  ].join(',');

  var TEXT_SEL = 'input, textarea, [contenteditable]';

  document.addEventListener('mouseover', function (e) {
    if (e.target.closest && e.target.closest(TEXT_SEL)) {
      document.body.classList.add('cur-text');
      document.body.classList.remove('cur-link');
    } else if (e.target.closest && e.target.closest(LINK_SEL)) {
      document.body.classList.add('cur-link');
      document.body.classList.remove('cur-text');
    }
  });

  document.addEventListener('mouseout', function (e) {
    var t = e.relatedTarget;
    if (!t || (!t.closest(LINK_SEL) && !t.closest(TEXT_SEL))) {
      document.body.classList.remove('cur-link', 'cur-text');
    }
  });

  /* ── Init ────────────────────────────────────────── */
  function init() {
    build();
    loop();
  }

  window.AxCursor = { init: init };
})();
