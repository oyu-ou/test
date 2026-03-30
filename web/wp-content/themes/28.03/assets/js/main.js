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
    if (window.OuGrain)  OuGrain.init();
    if (window.OuCursor) OuCursor.init();
    if (window.OuTheme)  OuTheme.init();
    if (window.OuMode)   OuMode.init();
    if (window.OuSlider) OuSlider.init();
    if (window.OuFilter) OuFilter.init();
    if (window.OuScroll) OuScroll.init();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
  
  document.addEventListener("DOMContentLoaded", function () {
    const video = document.getElementById("hero-video");
    const content = document.querySelector(".hero__content");

    if (video && content) {
      video.addEventListener("ended", function () {
        // Fade out video
        video.style.opacity = "0";
        setTimeout(() => {
          video.style.display = "none";
        }, 600);

        // Fade & slide in content
        content.style.opacity = "1";
        content.style.transform = "translateY(0)";
      });
    }
  });

})();
