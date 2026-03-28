/**
 * ou — mode.js
 * CLEAN ↔ WILD toggle.
 * Persists in localStorage. Dispatches 'ou:mode' for other modules.
 */
(function () {
  'use strict';
  var KEY  = 'ou-mode';
  var body = document.documentElement;
  var btn;

  function apply(mode, animate) {
    var prev = body.getAttribute('data-mode');
    if (prev === mode) return;

    body.setAttribute('data-mode', mode);
    localStorage.setItem(KEY, mode);

    if (btn) {
      btn.textContent = mode === 'clean' ? 'Wild ↗' : 'Clean ↙';
      btn.classList.toggle('is-on', mode === 'wild');
    }

    if (animate) {
      /* Brief crossfade on document.body (not html element) */
      document.body.style.opacity = '0.88';
      document.body.style.transition = 'opacity 0.28s';
      setTimeout(function () {
        document.body.style.opacity = '';
        document.body.style.transition = '';
      }, 320);
    }

    document.dispatchEvent(new CustomEvent('ou:mode', { detail: { mode: mode } }));
  }

  function toggle() {
    var cur = body.getAttribute('data-mode') || 'clean';
    apply(cur === 'clean' ? 'wild' : 'clean', true);
  }

  function init() {
    btn = document.getElementById('toggle-mode');
    if (btn) btn.addEventListener('click', toggle);

    var saved = localStorage.getItem(KEY) || 'clean';
    apply(saved, false);
  }

  window.OuMode = { init: init, apply: apply };
})();
