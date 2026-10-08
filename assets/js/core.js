/*
 * Keso Invest: core.js (Nivo 0 + 1). Bez biblioteka.
 * Navigacija, otkrivanje pri skrolu (IntersectionObserver), brojači, filter stanova,
 učitavanje Nivoa 2 (motion.js).
 * Nikakvih scroll listenera.
 */
(function () {
  'use strict';

  var doc = document;
  var html = doc.documentElement;
  var cfg = window.KESO || {};
  var i18n = cfg.i18n || {};
  var reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  var $ = function (s, r) { return (r || doc).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || doc).querySelectorAll(s)); };
  var hasIO = 'IntersectionObserver' in window;

  html.classList.add('is-ready');

  /* ---------- Header: čvrst i uži posle prvih piksela (jedan observer) ---------- */
  var header = $('[data-header]');
  var sentinel = $('[data-sentinel]');
  if (header && sentinel && hasIO) {
    new IntersectionObserver(function (entries) {
      header.classList.toggle('is-stuck', !entries[0].isIntersecting);
    }).observe(sentinel);
  } else if (header) {
    header.classList.add('is-stuck');
  }

  /* ---------- Navigacija ---------- */
  var toggle = $('[data-nav-toggle]');
  var nav = $('[data-nav]');
  var desktop = matchMedia('(min-width: 1024px)');
  var lastFocus = null;

  function focusables() {
    return $$('a[href], button:not([disabled])', header).filter(function (el) {
      return el.offsetParent !== null || el === toggle;
    });
  }
  function setNav(open) {
    if (!toggle || !nav) return;
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    var label = $('[data-nav-toggle-label]', toggle);
    if (label) label.textContent = open ? (i18n.close || 'Zatvori meni') : (i18n.open || 'Otvori meni');
    nav.classList.toggle('is-open', open);
    html.classList.toggle('nav-open', open);
    var main = $('#sadrzaj');
    var foot = $('.site-footer');
    [main, foot].forEach(function (el) { if (el) el.inert = open; });
    if (open) {
      lastFocus = doc.activeElement;
      var first = $('a', nav);
      if (first) first.focus();
    } else if (lastFocus && lastFocus.focus) {
      lastFocus.focus();
    }
  }
  if (toggle && nav) {
    toggle.addEventListener('click', function () { setNav(toggle.getAttribute('aria-expanded') !== 'true'); });
    nav.addEventListener('click', function (e) {
      var a = e.target.closest('a');
      if (a && !desktop.matches) setNav(false);
    });
    desktop.addEventListener('change', function (e) { if (e.matches) setNav(false); });
  }
  doc.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      var openSub = $$('.nav__list .is-open');
      if (nav && nav.classList.contains('is-open')) { setNav(false); return; }
      if (openSub.length) {
        openSub.forEach(function (li) {
          li.classList.remove('is-open');
          var b = $('.sub-toggle', li);
          if (b) b.setAttribute('aria-expanded', 'false');
        });
        if (openSub[0]) { var tb = $('.sub-toggle', openSub[0]); if (tb) tb.focus(); }
      }
    } else if (e.key === 'Tab' && nav && nav.classList.contains('is-open')) {
      var f = focusables();
      if (!f.length) return;
      var first = f[0], last = f[f.length - 1];
      if (e.shiftKey && doc.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && doc.activeElement === last) { e.preventDefault(); first.focus(); }
    }
  });
  $$('.sub-toggle').forEach(function (b) {
    b.addEventListener('click', function () {
      var li = b.closest('li');
      var open = !li.classList.contains('is-open');
      li.classList.toggle('is-open', open);
      b.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  });

  /* ---------- Otkrivanje pri skrolu (Nivo 1) ---------- */
  var revealEls = $$('[data-reveal], [data-reveal-img]');
  $$('[data-stagger]').forEach(function (list) {
    $$(':scope > *', list).forEach(function (c, i) { c.style.setProperty('--i', Math.min(i, 8)); revealEls.push(c); });
  });
  function show(el) { el.classList.add('is-in'); }
  if (hasIO && html.classList.contains('anim-ready')) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) { show(en.target); io.unobserve(en.target); }
      });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
    revealEls.forEach(function (el) { io.observe(el); });
    // Element sa fokusom tastature mora biti vidljiv.
    doc.addEventListener('focusin', function (e) {
      var r = e.target.closest('[data-reveal], [data-reveal-img], [data-stagger] > *');
      if (r) show(r);
    });
  } else {
    revealEls.forEach(show);
  }

  /* ---------- Brojači ---------- */
  var counters = $$('[data-count]');
  function fmt(n, suffix) { return String(n) + (suffix || ''); }
  if (counters.length) {
    if (hasIO && !reduce) {
      var cio = new IntersectionObserver(function (entries) {
        entries.forEach(function (en) {
          if (!en.isIntersecting) return;
          cio.unobserve(en.target);
          run(en.target);
        });
      }, { threshold: 0.4 });
      counters.forEach(function (el) {
        var end = parseInt(el.getAttribute('data-count'), 10) || 0;
        var suffix = el.getAttribute('data-suffix') || '';
        // Čitači ekrana dobijaju krajnju vrednost; animirani tekst je sakriven od njih.
        var sr = doc.createElement('span');
        sr.className = 'sr-only';
        sr.textContent = fmt(end, suffix);
        el.parentNode.insertBefore(sr, el.nextSibling);
        el.setAttribute('aria-hidden', 'true');
        if (end > 0) el.textContent = fmt(0, suffix);
        cio.observe(el);
      });
    }
  }
  function run(el) {
    var end = parseInt(el.getAttribute('data-count'), 10) || 0;
    var suffix = el.getAttribute('data-suffix') || '';
    var dur = 1500, t0 = null;
    function step(t) {
      if (t0 === null) t0 = t;
      var p = Math.min(1, (t - t0) / dur);
      var eased = p === 1 ? 1 : 1 - Math.pow(2, -10 * p);
      el.textContent = fmt(Math.round(end * eased), suffix);
      if (p < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  /* ---------- Sprat: filter stanova ---------- */
  var filters = $('[data-filters]');
  if (filters) {
    var rows = $$('.apts__row');
    var count = $('[data-filters-count]', filters);
    var empty = $('[data-filters-empty]');
    var apply = function () {
      var st = ($('input[name="st"]:checked', filters) || {}).value || '';
      var min = parseFloat(String(filters.elements.min.value).replace(',', '.')) || 0;
      var shown = 0;
      rows.forEach(function (r) {
        var area = parseFloat(r.getAttribute('data-area')) || 0;
        var ok = (!st || r.getAttribute('data-status') === st) && area >= min;
        r.hidden = !ok;
        if (ok) shown++;
      });
      if (count) count.textContent = 'Prikazano ' + shown + ' od ' + rows.length;
      if (empty) empty.hidden = shown !== 0;
    };
    filters.hidden = false;
    filters.addEventListener('input', apply);
    filters.addEventListener('change', apply);
    filters.addEventListener('submit', function (e) { e.preventDefault(); });
    apply();
  }

  /* ---------- Nivo 2: učitavanje animacija samo ako je uređaj prošao gate ---------- */
  function fallbackLevel1() {
    html.classList.remove('motion-ok', 'motion-on');
    html.setAttribute('data-motion', 'off');
    $$('[data-reveal-img]').forEach(show);
  }
  if (html.classList.contains('motion-ok') && cfg.motion && cfg.motion.length &&
      $('[data-hero], [data-parallax], [data-process], [data-reveal-img], [data-split]')) {
    html.setAttribute('data-motion', 'loading');
    var left = cfg.motion.length;
    var failed = false;
    cfg.motion.forEach(function (src) {
      var s = doc.createElement('script');
      s.src = src;
      s.async = false; // redosled: gsap, ScrollTrigger, Lenis, motion.js
      s.onload = function () {
        left--;
        if (left === 0 && !failed && window.KesoMotion) window.KesoMotion.init();
      };
      s.onerror = function () { failed = true; fallbackLevel1(); html.setAttribute('data-motion', 'failed'); };
      doc.head.appendChild(s);
    });
  } else {
    html.setAttribute('data-motion', 'off');
  }
  window.KesoCore = { fallback: fallbackLevel1 };
})();
