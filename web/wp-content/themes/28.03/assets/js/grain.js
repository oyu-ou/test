/**
 * AXIOM — grain.js
 * Animated 35mm film grain via canvas at 24fps.
 */
(function () {
  'use strict';
  var canvas, ctx, w, h, px, last = 0, FPS = 24, interval = 1000 / FPS;

  function init() {
    canvas = document.createElement('canvas');
    canvas.id = 'grain-canvas';
    canvas.setAttribute('aria-hidden', 'true');
    document.body.appendChild(canvas);
    ctx = canvas.getContext('2d');
    resize();
    requestAnimationFrame(tick);
    window.addEventListener('resize', resize);
  }

  function resize() {
    w = canvas.width  = Math.ceil(window.innerWidth  * 0.6);
    h = canvas.height = Math.ceil(window.innerHeight * 0.6);
    canvas.style.width  = '100vw';
    canvas.style.height = '100vh';
    px = ctx.createImageData(w, h);
  }

  function draw() {
    var d = px.data, len = d.length;
    for (var i = 0; i < len; i += 4) {
      var v = (Math.random() * 255) | 0;
      d[i] = d[i+1] = d[i+2] = v;
      d[i+3] = 55;
    }
    ctx.putImageData(px, 0, 0);
  }

  function tick(now) {
    requestAnimationFrame(tick);
    if (now - last < interval) return;
    last = now - ((now - last) % interval);
    draw();
  }

  window.AxGrain = { init: init };
})();
