/**
 * AXIOM — scroll.js
 * Loader · scroll reveals · list reveals · parallax ·
 * nav tracking · smooth scroll · hero video · mobile nav · preview image
 */
(function () {
  'use strict';

  /* ── 1. LOADER ───────────────────────────────────── */
  function initLoader() {
    var loader = document.getElementById('axiom-loader');
    if (!loader) return;

    var bar   = loader.querySelector('.loader__bar');
    var numEl = loader.querySelector('.loader__num-inner');

    document.body.style.overflow = 'hidden';

    var start = performance.now(), dur = 1500;

    function step(now) {
      var t    = Math.min((now - start) / dur, 1);
      var ease = 1 - Math.pow(1 - t, 3);
      var pct  = Math.floor(ease * 100);

      if (bar)   bar.style.width = pct + '%';
      if (numEl) numEl.textContent = (pct < 10 ? '0' : '') + pct;

      if (t < 1) { requestAnimationFrame(step); return; }

      setTimeout(function () {
        loader.classList.add('done');
        document.body.style.overflow = '';

        /* Hero home page */
        var name = document.querySelector('.hero__name');
        var disc = document.querySelector('.hero__disciplines');
        var scrl = document.querySelector('.hero__scroll');
        if (name) name.classList.add('in');
        if (disc) setTimeout(function () { disc.classList.add('in'); }, 150);
        if (scrl) scrl.classList.add('in');

        /* Single project hero */
        var spHero = document.getElementById('sp-hero-section');
        if (spHero) spHero.classList.add('in');

      }, 280);
    }
    requestAnimationFrame(step);
  }

  /* ── 2. SCROLL REVEAL ────────────────────────────── */
  function initReveal() {
    var els = document.querySelectorAll('[data-reveal]');
    if (!els.length) return;

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) { e.target.classList.add('is-on'); io.unobserve(e.target); }
      });
    }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });

    els.forEach(function (el) { io.observe(el); });
  }

  /* ── 3. LIST ROW REVEALS ─────────────────────────── */
  function initListReveals() {
    var items = document.querySelectorAll('.clean__item:not(.is-on)');
    if (!items.length) return;

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) { e.target.classList.add('is-on'); io.unobserve(e.target); }
      });
    }, { threshold: 0.04, rootMargin: '0px 0px -20px 0px' });

    items.forEach(function (el) { io.observe(el); });
  }

  /* ── 4. CLIP REVEAL (for headings) ──────────────── */
  function initClipReveal() {
    var wraps = document.querySelectorAll('.clip-wrap');
    if (!wraps.length) return;

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) { e.target.classList.add('is-on'); io.unobserve(e.target); }
      });
    }, { threshold: 0.2 });

    wraps.forEach(function (el) { io.observe(el); });
  }

  /* ── 5. ACTIVE NAV TRACKING ──────────────────────── */
  function initNavTracking() {
    var links    = Array.from(document.querySelectorAll('.nav__link[href^="#"]'));
    var sections = links.map(function (l) {
      return document.querySelector(l.getAttribute('href'));
    }).filter(Boolean);
    if (!sections.length) return;

    function update() {
      var mid = window.scrollY + window.innerHeight * 0.35;
      var cur = null;
      sections.forEach(function (s, i) { if (s && s.offsetTop <= mid) cur = i; });
      links.forEach(function (l, i) { l.classList.toggle('active', i === cur); });
    }

    window.addEventListener('scroll', update, { passive: true });
    update();
  }

  /* ── 6. SMOOTH SCROLL ────────────────────────────── */
  function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(function (a) {
      a.addEventListener('click', function (e) {
        var id = a.getAttribute('href');
        if (id === '#') return;
        var target = document.querySelector(id);
        if (!target) return;
        e.preventDefault();
        var navH = parseInt(getComputedStyle(document.documentElement)
          .getPropertyValue('--nav-h')) || 52;
        window.scrollTo({
          top: target.getBoundingClientRect().top + window.scrollY - navH,
          behavior: 'smooth'
        });
        /* Close drawer */
        var drawer = document.querySelector('.nav__drawer');
        var burger = document.querySelector('.nav__burger');
        if (drawer) drawer.classList.remove('open');
        if (burger) { burger.classList.remove('open'); burger.setAttribute('aria-expanded','false'); }
      });
    });
  }

  /* ── 7. HERO VIDEO ───────────────────────────────── */
  function initHeroVideo() {
    var v = document.getElementById('hero-video');
    if (!v) return;
    function onLoad() { v.classList.add('loaded'); }
    v.addEventListener('canplaythrough', onLoad);
    v.addEventListener('loadeddata', onLoad);
    if (v.readyState >= 3) onLoad();
  }

  /* ── 8. MOBILE NAV ───────────────────────────────── */
  function initMobileNav() {
    var burger = document.querySelector('.nav__burger');
    var drawer = document.querySelector('.nav__drawer');
    if (!burger || !drawer) return;
    burger.addEventListener('click', function () {
      var open = drawer.classList.toggle('open');
      burger.classList.toggle('open', open);
      burger.setAttribute('aria-expanded', open);
    });
  }

  /* ── 9. HOVER PREVIEW (clean list) ──────────────── */
  function initPreview() {
    var preview = document.getElementById('preview-img');
    if (!preview || window.matchMedia('(hover:none)').matches) return;

    var imgEl = preview.querySelector('img');
    var phEl  = preview.querySelector('.preview__placeholder');
    /* Start far off-screen so no flash on init */
    var px = -9999, py = -9999, tx = -9999, ty = -9999;

    /* Lerp loop — smooth follow */
    (function loop() {
      requestAnimationFrame(loop);
      px += (tx - px) * 0.1;
      py += (ty - py) * 0.1;
      preview.style.left = Math.round(px) + 'px';
      preview.style.top  = Math.round(py) + 'px';
    })();

    document.addEventListener('mousemove', function (e) {
      tx = e.clientX + 32;
      ty = e.clientY - (preview.offsetHeight / 2);
    });

    function showPreview(src) {
      if (src && imgEl) {
        imgEl.src = src;
        imgEl.style.display = 'block';
        if (phEl) phEl.style.display = 'none';
      } else {
        if (imgEl) imgEl.style.display = 'none';
        if (phEl) phEl.style.display = 'flex';
      }
      preview.classList.add('visible');
    }

    function hidePreview() {
      preview.classList.remove('visible');
    }

    /* Attach to list items — use event delegation so it works after filter */
    document.addEventListener('mouseover', function (e) {
      var item = e.target.closest('.clean__item');
      if (!item) return;
      showPreview(item.getAttribute('data-img') || '');
    });
    document.addEventListener('mouseout', function (e) {
      var item = e.target.closest('.clean__item');
      if (!item) return;
      var relTarget = e.relatedTarget;
      if (!relTarget || !item.contains(relTarget)) hidePreview();
    });
  }

  /* ── 10. BACK-TO-TOP ─────────────────────────────── */
  function initBackTop() {
    var btn = document.querySelector('.ax-back-top');
    if (!btn) return;
    window.addEventListener('scroll', function () {
      btn.classList.toggle('visible', window.scrollY > 400);
    }, { passive: true });
    btn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* ── RE-INIT REVEALS after mode/filter change ────── */
  document.addEventListener('axiom:mode',   initReveal);
  document.addEventListener('axiom:filter', function () {
    setTimeout(initListReveals, 100);
  });

  /* ── INIT ─────────────────────────────────────────── */
  function init() {
    initLoader();
    initReveal();
    initListReveals();
    initClipReveal();
    initNavTracking();
    initSmoothScroll();
    initHeroVideo();
    initMobileNav();
    initPreview();
    initBackTop();
  }

  window.AxScroll = { init: init };
})();
