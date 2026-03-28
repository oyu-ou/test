/**
 * ou — main.js
 * Entry point. Boots all modules after DOM ready.
 *
 * Load order (functions.php):
 *   grain → cursor → theme → mode → filter → slider → scroll → main
 */
(function () {
  'use strict';

  function init() {
    if (window.AxGrain)  AxGrain.init();
    if (window.AxCursor) AxCursor.init();
    if (window.AxTheme)  AxTheme.init();
    if (window.AxMode)   AxMode.init();
    if (window.AxSlider) AxSlider.init();
    if (window.AxFilter) AxFilter.init();
    if (window.AxScroll) AxScroll.init();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
