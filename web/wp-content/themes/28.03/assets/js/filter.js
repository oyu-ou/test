/**
 * AXIOM — filter.js
 * Category filtering via nav links.
 * Nav: About(all) · Web · Video · Photo · Graphic · Contact
 * "about" and "contact" scroll only — don't filter projects.
 */
(function () {
  'use strict';

  var activeFilter = 'all';
  var navLinks;

  /* Non-filter nav actions */
  var SCROLL_ONLY = ['contact', 'about'];

  /* ── Update active state on nav links ─────────────── */
  function updateNav(filter) {
    if (!navLinks) return;
    navLinks.forEach(function (link) {
      var f = link.getAttribute('data-filter') || '';
      link.classList.toggle('active', f === filter);
    });
  }

  /* ── Filter CLEAN list items ──────────────────────── */
  function filterClean(filter) {
    var items   = document.querySelectorAll('.clean__item');
    var visible = 0;
    items.forEach(function (item) {
      var cat  = item.getAttribute('data-cat') || 'all';
      var show = filter === 'all' || cat === filter;
      item.classList.toggle('hidden', !show);
      if (show) {
        visible++;
        item.style.transitionDelay = (visible * 0.035) + 's';
      }
    });
    var countEl = document.querySelector('.clean__count');
    if (countEl) countEl.textContent = String(visible || items.length).padStart(2,'0') + ' Total';
  }

  /* ── Notify slider of filter change ──────────────── */
  function filterWild(filter) {
    document.dispatchEvent(new CustomEvent('axiom:filter', { detail: { filter: filter } }));
  }

  /* ── Apply filter ─────────────────────────────────── */
  function apply(filter) {
    activeFilter = filter;
    updateNav(filter);
    filterClean(filter);
    filterWild(filter);
  }

  /* ── Smooth scroll ────────────────────────────────── */
  function scrollTo(id) {
    var el = document.getElementById(id);
    if (!el) return;
    var navH = parseInt(getComputedStyle(document.documentElement)
      .getPropertyValue('--nav-h')) || 52;
    window.scrollTo({
      top: el.getBoundingClientRect().top + window.scrollY - navH,
      behavior: 'smooth'
    });
  }

  /* ── Init ─────────────────────────────────────────── */
  function init() {
    navLinks = Array.from(document.querySelectorAll('[data-filter]'));

    navLinks.forEach(function (link) {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        var filter = link.getAttribute('data-filter') || 'all';

        /* Close mobile drawer */
        var drawer = document.querySelector('.nav__drawer');
        var burger = document.querySelector('.nav__burger');
        if (drawer) { drawer.classList.remove('open'); }
        if (burger) { burger.classList.remove('open'); burger.setAttribute('aria-expanded','false'); }

        /* Scroll-only links: about and contact */
        if (filter === 'about') {
          scrollTo('ax-about');
          return;
        }
        if (filter === 'contact') {
          scrollTo('ax-contact');
          return;
        }

        /* In WILD mode — apply filter to slider */
        var mode = document.documentElement.getAttribute('data-mode') || 'clean';
        apply(filter);

        /* In CLEAN mode — also scroll to work section */
        if (mode === 'clean') {
          scrollTo('ax-clean');
        }
      });
    });

    /* Also wire up plain anchor links */
    document.querySelectorAll('a[href^="#ax-"]').forEach(function (a) {
      if (a.hasAttribute('data-filter')) return; /* already handled above */
      a.addEventListener('click', function (e) {
        var id = a.getAttribute('href').slice(1);
        var el = document.getElementById(id);
        if (!el) return;
        e.preventDefault();
        var navH = parseInt(getComputedStyle(document.documentElement)
          .getPropertyValue('--nav-h')) || 52;
        window.scrollTo({
          top: el.getBoundingClientRect().top + window.scrollY - navH,
          behavior: 'smooth'
        });
      });
    });

    apply('all');
  }

  window.AxFilter = { init: init, apply: apply };
})();
