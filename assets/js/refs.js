/*
 * Keso Invest: pregled referenci (početna). Vanilla, bez biblioteka.
 * Hover / fokus / tap na .ref-item menja caption i sliku u okviru .refs__preview.
 * Stavka nosi data-image (URL, može biti prazan) i data-caption. Bez slike ostaje placeholder.
 * Bez navigacije; fade je čist CSS (opacity), isključuje se kroz prefers-reduced-motion.
 */
(function () {
  'use strict';
  var root = document.querySelector('[data-refs]');
  if (!root) return;
  var items = Array.prototype.slice.call(root.querySelectorAll('.ref-item'));
  var frame = root.querySelector('[data-refs-preview] .media');
  var cap = root.querySelector('[data-refs-cap]');
  if (!items.length || !frame || !cap) return;

  var imgs = {}; // url -> <img>, pravi se tek pri prvom prikazu
  var current = null;

  function show(item) {
    if (!item || item === current) return;
    if (current) current.classList.remove('is-active');
    item.classList.add('is-active');
    current = item;
    cap.textContent = item.getAttribute('data-caption') || '';

    var src = item.getAttribute('data-image') || '';
    Object.keys(imgs).forEach(function (k) { imgs[k].classList.toggle('is-on', k === src); });
    if (src && !imgs[src]) {
      var img = document.createElement('img');
      img.className = 'media__img';
      img.alt = '';
      img.decoding = 'async';
      img.addEventListener('load', function () { if (current && current.getAttribute('data-image') === src) img.classList.add('is-on'); });
      img.src = src;
      frame.appendChild(img);
      imgs[src] = img;
    }
  }

  items.forEach(function (item) {
    item.addEventListener('mouseenter', function () { show(item); });
    item.addEventListener('focus', function () { show(item); });
    item.addEventListener('click', function () { show(item); }); // touch: tap
  });
  show(items[0]);
})();
