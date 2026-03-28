/**
 * ou — slider.js
 * WILD MODE: fullscreen touch+drag+keyboard slider.
 * Rebuilds on filter change. Handles all transitions.
 */
(function () {
  'use strict';

  var wrap, track, dotsWrap, counterCur, counterTotal;
  var slides     = [];   /* all .wild__slide elements */
  var visible    = [];   /* indices of currently visible slides */
  var current    = 0;    /* index into visible[] */
  var isAnimating = false;
  var isDragging  = false;
  var dragStartX  = 0;
  var dragOffsetX = 0;
  var autoTimer;
  var AUTO_DELAY = 6000;

  /* ── Build slide ─────────────────────────────────── */
  function buildSlide(data, idx) {
    var slide = document.createElement('div');
    slide.className = 'wild__slide';
    slide.setAttribute('data-cat', data.cat || 'all');
    slide.setAttribute('data-href', data.href || '#');

    /* Ghost category text */
    var ghost = document.createElement('div');
    ghost.className = 'wild__ghost-cat';
    ghost.textContent = data.cat || '';
    slide.appendChild(ghost);

    /* Curtain */
    var curtain = document.createElement('div');
    curtain.className = 'wild__curtain';
    slide.appendChild(curtain);

    /* Media */
    var media = document.createElement('div');
    media.className = 'wild__media';

    if (data.imgSrc) {
      var img = document.createElement('img');
      img.src     = data.imgSrc;
      img.alt     = data.title || '';
      img.loading = idx === 0 ? 'eager' : 'lazy';
      media.appendChild(img);
    } else {
      var ph = document.createElement('div');
      ph.className = 'wild__placeholder';
      if (data.gradient) {
        ph.style.background = data.gradient;
      } else {
        var tones = [
          'linear-gradient(135deg,#0d0d0d 0%,#1a0a00 100%)',
          'linear-gradient(135deg,#080808 0%,#001030 100%)',
          'linear-gradient(135deg,#0a0a0a 0%,#060018 100%)',
          'linear-gradient(135deg,#080808 0%,#0a1a0a 100%)',
          'linear-gradient(135deg,#0c0c0c 0%,#1a0010 100%)',
          'linear-gradient(135deg,#080808 0%,#0a0a18 100%)'
        ];
        ph.style.background = tones[idx % tones.length];
      }
      ph.style.position = 'relative';
      var scanlines = document.createElement('div');
      scanlines.style.cssText = 'position:absolute;inset:0;background-image:repeating-linear-gradient(0deg,transparent,transparent 3px,rgba(255,255,255,0.018) 3px,rgba(255,255,255,0.018) 6px);pointer-events:none;';
      ph.appendChild(scanlines);
      /* Centered project-number watermark */
      var numWatermark = document.createElement('div');
      numWatermark.style.cssText = [
        'position:absolute','inset:0',
        'display:flex','align-items:center','justify-content:center',
        'font-size:clamp(6rem,15vw,16rem)',
        'font-weight:200',
        'letter-spacing:-0.05em',
        'color:rgba(255,255,255,0.04)',
        'line-height:1',
        'user-select:none',
        'pointer-events:none',
        'font-variant-numeric:tabular-nums'
      ].join(';');
      numWatermark.textContent = String(idx + 1).padStart(2, '0');
      ph.appendChild(numWatermark);
      media.appendChild(ph);
    }
    slide.appendChild(media);

    /* Scrim */
    var scrim = document.createElement('div');
    scrim.className = 'wild__scrim';
    slide.appendChild(scrim);

    /* Info */
    var info = document.createElement('div');
    info.className = 'wild__info';

    var infoLeft = document.createElement('div');
    infoLeft.className = 'wild__info-left';

    var num = document.createElement('div');
    num.className = 'wild__num';
    num.textContent = String(idx + 1).padStart(2, '0');

    var titleWrap = document.createElement('div');
    titleWrap.className = 'wild__title';

    /* Split title into word spans for clip animation */
    var words = (data.title || 'Untitled').split(' ');
    words.forEach(function (word) {
      var wordWrap = document.createElement('span');
      wordWrap.className = 'wild__title-word';
      var inner = document.createElement('span');
      inner.className = 'wild__title-inner';
      inner.textContent = word;
      wordWrap.appendChild(inner);
      titleWrap.appendChild(wordWrap);
    });

    var cat = document.createElement('div');
    cat.className = 'wild__cat';
    cat.textContent = data.cat || '';

    var open = document.createElement('a');
    open.className = 'wild__open';
    open.href = data.href || '#';
    open.innerHTML = 'Open Project <span>↗</span>';

    infoLeft.appendChild(num);
    infoLeft.appendChild(titleWrap);
    infoLeft.appendChild(cat);
    infoLeft.appendChild(open);

    var infoRight = document.createElement('div');
    infoRight.className = 'wild__info-right';

    var year = document.createElement('div');
    year.className = 'wild__year';
    year.textContent = data.year || '';

    infoRight.appendChild(year);
    info.appendChild(infoLeft);
    info.appendChild(infoRight);
    slide.appendChild(info);

    return slide;
  }

  /* ── Populate track from PHP data ────────────────── */
  function buildTrack() {
    if (!track) return;
    /* projectData is injected by wp_localize_script in functions.php */
    var data = (window.ouData && window.ouData.projects) ? window.ouData.projects : [];

    if (!data.length) {
      var empty = document.createElement('div');
      empty.className = 'wild__empty';
      empty.innerHTML = '<p class="wild__empty-text">Add projects in WP Admin → Portfolio</p>';
      track.appendChild(empty);
      return;
    }

    data.forEach(function (p, i) {
      var slide = buildSlide(p, i);
      track.appendChild(slide);
    });

    slides = Array.from(track.querySelectorAll('.wild__slide'));
  }

  /* ── Dots ────────────────────────────────────────── */
  function buildDots(count) {
    if (!dotsWrap) return;
    dotsWrap.innerHTML = '';
    for (var i = 0; i < count; i++) {
      var dot = document.createElement('div');
      dot.className = 'wild__dot';
      dot.setAttribute('data-i', i);
      dot.addEventListener('click', (function (idx) {
        return function () { goTo(idx, true); };
      })(i));
      dotsWrap.appendChild(dot);
    }
  }

  function updateDots() {
    if (!dotsWrap) return;
    var dots = dotsWrap.querySelectorAll('.wild__dot');
    dots.forEach(function (d, i) {
      d.classList.toggle('active', i === current);
    });
  }

  /* ── Counter ─────────────────────────────────────── */
  function updateCounter() {
    if (counterCur)   counterCur.textContent   = String(current + 1).padStart(2, '0');
    if (counterTotal) counterTotal.textContent = String(visible.length).padStart(2, '0');
  }

  /* ── Recompute visible set (after filter) ─────────── */
  function applyFilter(filter) {
    visible = [];
    slides.forEach(function (slide, i) {
      var cat = slide.getAttribute('data-cat') || 'all';
      if (filter === 'all' || cat === filter) visible.push(i);
    });

    if (!visible.length) visible = slides.map(function (_, i) { return i; });

    /* Reset to first visible */
    current = 0;
    positionTo(visible[0], false);
    activateSlide(visible[0]);
    buildDots(visible.length);
    updateDots();
    updateCounter();
  }

  /* ── Position track ──────────────────────────────── */
  function positionTo(slideIdx, animate) {
    if (!track) return;
    var offset = -slideIdx * 100;
    track.style.transition = animate ? 'transform 0.9s cubic-bezier(0.16,1,0.3,1)' : 'none';
    track.style.transform  = 'translateX(' + offset + '%)';
  }

  /* ── Activate slide ──────────────────────────────── */
  function activateSlide(idx) {
    slides.forEach(function (s) { s.classList.remove('is-active'); });
    if (slides[idx]) slides[idx].classList.add('is-active');
  }

  /* ── Go to slide ─────────────────────────────────── */
  function goTo(visibleIdx, animate) {
    if (isAnimating && animate) return;
    if (!visible.length) return;

    visibleIdx = ((visibleIdx % visible.length) + visible.length) % visible.length;
    current    = visibleIdx;
    var realIdx = visible[current];

    positionTo(realIdx, animate !== false);
    activateSlide(realIdx);
    updateDots();
    updateCounter();

    if (animate !== false) {
      isAnimating = true;
      setTimeout(function () { isAnimating = false; }, 950);
    }

    resetAuto();
  }

  /* ── Auto-advance ────────────────────────────────── */
  function startAuto() {
    clearInterval(autoTimer);
    autoTimer = setInterval(function () {
      if (document.documentElement.getAttribute('data-mode') === 'wild' &&
          !document.hidden && !isDragging) {
        goTo(current + 1, true);
      }
    }, AUTO_DELAY);
  }

  function resetAuto() { startAuto(); }

  /* ── Keyboard ─────────────────────────────────────── */
  function onKey(e) {
    if (document.documentElement.getAttribute('data-mode') !== 'wild') return;
    if (e.key === 'ArrowRight' || e.key === 'ArrowDown')  goTo(current + 1, true);
    if (e.key === 'ArrowLeft'  || e.key === 'ArrowUp')    goTo(current - 1, true);
  }

  /* ── Touch / drag ────────────────────────────────── */
  function onDragStart(e) {
    if (document.documentElement.getAttribute('data-mode') !== 'wild') return;
    isDragging  = true;
    dragStartX  = (e.touches ? e.touches[0].clientX : e.clientX);
    dragOffsetX = 0;
    wrap.classList.add('is-dragging');
    clearInterval(autoTimer);
    e.preventDefault();
  }

  function onDragMove(e) {
    if (!isDragging) return;
    var x = (e.touches ? e.touches[0].clientX : e.clientX);
    dragOffsetX = x - dragStartX;
    var realIdx  = visible[current];
    var base     = -realIdx * 100;
    var pct      = (dragOffsetX / window.innerWidth) * 100;
    track.style.transition = 'none';
    track.style.transform  = 'translateX(calc(' + base + '% + ' + dragOffsetX + 'px))';
  }

  function onDragEnd(e) {
    if (!isDragging) return;
    isDragging = false;
    wrap.classList.remove('is-dragging');
    var THRESHOLD = window.innerWidth * 0.15;
    if (dragOffsetX < -THRESHOLD) goTo(current + 1, true);
    else if (dragOffsetX > THRESHOLD) goTo(current - 1, true);
    else goTo(current, true); /* snap back */
    startAuto();
  }

  /* ── Init ─────────────────────────────────────────── */
  function init() {
    wrap       = document.getElementById('ou-wild');
    if (!wrap) return;

    track      = wrap.querySelector('.wild__track');
    dotsWrap   = wrap.querySelector('.wild__dots');
    counterCur   = wrap.querySelector('.wild__counter-current');
    counterTotal = wrap.querySelector('.wild__counter-total');

    var btnPrev = wrap.querySelector('.wild__arrow--prev');
    var btnNext = wrap.querySelector('.wild__arrow--next');

    buildTrack();
    applyFilter('all');
    startAuto();

    /* Arrow buttons */
    if (btnPrev) btnPrev.addEventListener('click', function () { goTo(current - 1, true); });
    if (btnNext) btnNext.addEventListener('click', function () { goTo(current + 1, true); });

    /* Keyboard */
    document.addEventListener('keydown', onKey);

    /* Touch */
    wrap.addEventListener('touchstart', onDragStart, { passive: false });
    wrap.addEventListener('touchmove',  onDragMove,  { passive: false });
    wrap.addEventListener('touchend',   onDragEnd);

    /* Mouse drag */
    wrap.addEventListener('mousedown', onDragStart);
    window.addEventListener('mousemove', onDragMove);
    window.addEventListener('mouseup',   onDragEnd);

    /* Filter events from filter.js */
    document.addEventListener('ou:filter', function (e) {
      applyFilter(e.detail.filter);
    });

    /* Pause auto in clean mode */
    document.addEventListener('ou:mode', function (e) {
      if (e.detail.mode === 'wild') startAuto();
      else clearInterval(autoTimer);
    });

    document.addEventListener('visibilitychange', function () {
      if (!document.hidden) startAuto();
      else clearInterval(autoTimer);
    });
  }

  window.AxSlider = { init: init };
})();
