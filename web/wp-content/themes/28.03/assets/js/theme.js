/**
 * AXIOM — theme.js
 * Dark ↔ Light toggle. Persists in localStorage.
 * Applied before body paint (in <head>) to avoid FOUC.
 */
(function () {
  'use strict';
  var KEY = 'axiom-theme';
  var html = document.documentElement;
  var btn;

  function apply(t) {
    html.setAttribute('data-theme', t);
    localStorage.setItem(KEY, t);
    if (btn) {
      btn.textContent = t === 'dark' ? 'Light' : 'Dark';
      btn.setAttribute('aria-pressed', t === 'light');
    }
  }

  function toggle() {
    apply(html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
  }

  function init() {
    btn = document.getElementById('toggle-theme');
    if (btn) btn.addEventListener('click', toggle);
    var saved = localStorage.getItem(KEY) ||
      (window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
    apply(saved);
  }

  /* Apply immediately (before render) */
  (function () {
    var s = localStorage.getItem(KEY);
    if (s) html.setAttribute('data-theme', s);
  })();

  window.AxTheme = { init: init };
})();
