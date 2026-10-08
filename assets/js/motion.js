/*
 * Keso Invest: motion.js (Nivo 2, samo laptop/desktop koji prođe gate u core.js / <head>).
 * Zavisi od GSAP, ScrollTrigger i Lenis (self-hosted, učitani pre ovog fajla).
 *
 * Svaki pokret ima funkciju:
 *  - naslovi se otkrivaju po linijama (maska): vode pogled na hijerarhiju;
 *  - hero: sporo skaliranje i parallax fotografije: dubina i fokus na naslov;
 *  - otkrivanje slika maskom: redosled čitanja sadržaja;
 *  - tanke linije se iscrtavaju: strukturiraju brojke, vrednosti i reference;
 *  - jedna pinovana sekcija (Kako gradimo): koraci se otkrivaju redom, kao u stvarnom procesu.
 * Animira se samo transform/opacity/clip-path. Čuvar performansi spušta sajt na Nivo 1.
 */
(function () {
  'use strict';

  var doc = document;
  var html = doc.documentElement;
  var $$ = function (s, r) { return Array.prototype.slice.call((r || doc).querySelectorAll(s)); };
  var $ = function (s, r) { return (r || doc).querySelector(s); };

  var state = { ctx: null, lenis: null, tick: null, dead: false, mm: null, splits: [] };
  var EASE_OUT = 'power3.out';

  /* ---------- Deljenje naslova na linije (maska, ne seckanje slova) ---------- */
  function esc(s) { return s.replace(/&/g, '&amp;').replace(/</g, '&lt;'); }
  function splitLines(el) {
    if (!el.__orig) el.__orig = el.textContent.replace(/\s+/g, ' ').trim();
    var words = el.__orig.split(' ');
    el.innerHTML = words.map(function (w) { return '<span class="w">' + esc(w) + '</span>'; }).join(' ');
    var lines = [], top = null;
    $$('.w', el).forEach(function (w) {
      var t = w.offsetTop;
      if (top === null || Math.abs(t - top) > 4) { lines.push([]); top = t; }
      lines[lines.length - 1].push(w.textContent);
    });
    el.innerHTML = lines.map(function (l) {
      return '<span class="split-line"><span class="split-line__in">' + esc(l.join(' ')) + '</span></span>';
    }).join('');
    el.setAttribute('aria-label', el.__orig);
    $$('.split-line', el).forEach(function (l) { l.setAttribute('aria-hidden', 'true'); });
    return $$('.split-line__in', el);
  }
  function unsplit(el) {
    if (el.__orig) { el.textContent = el.__orig; el.removeAttribute('aria-label'); }
  }

  /* ---------- Čuvar performansi ---------- */
  function guard(onSlow) {
    function sample(label, cb) {
      var frames = 0, sum = 0, last = performance.now();
      (function loop(now) {
        if (state.dead) return;
        var d = now - last; last = now;
        if (d < 500) { frames++; if (frames > 5) sum += d; } // prvih par kadrova i pauze (skriven tab) se ne računaju
        if (frames < 45) { requestAnimationFrame(loop); return; }
        var avg = sum / (frames - 5);
        html.setAttribute('data-motion-fps-' + label, avg.toFixed(1));
        cb(avg);
      })(last);
    }
    sample('idle', function (avg) { if (avg > 24) onSlow(avg); });
    // Drugi uzorak: tokom prvog skrola.
    var started = false;
    function onScroll() {
      if (started || state.dead) return;
      started = true;
      removeEventListener('wheel', onScroll);
      removeEventListener('touchmove', onScroll);
      removeEventListener('keydown', onScroll);
      sample('scroll', function (avg) { if (avg > 24) onSlow(avg); });
    }
    addEventListener('wheel', onScroll, { passive: true });
    addEventListener('touchmove', onScroll, { passive: true });
    addEventListener('keydown', onScroll);
  }

  /* ---------- Pad na Nivo 1 ---------- */
  function degrade(reason) {
    if (state.dead) return;
    state.dead = true;
    try { ScrollTrigger.killAll(); } catch (e) {}
    try { if (state.ctx) state.ctx.revert(); } catch (e) {}
    try { if (state.mm) state.mm.revert(); } catch (e) {}
    try { if (state.lenis) state.lenis.destroy(); } catch (e) {}
    try { if (state.tick) gsap.ticker.remove(state.tick); } catch (e) {}
    state.splits.forEach(unsplit);
    html.classList.remove('motion-on', 'motion-ok', 'lenis', 'lenis-smooth');
    $$('[data-split]').forEach(function (el) { el.classList.add('is-split'); });
    $$('[data-reveal-img], [data-hero-media], [data-parallax] *').forEach(function (el) { el.style.clipPath = ''; el.style.transform = ''; });
    $$('[data-reveal-img]').forEach(function (el) { el.classList.add('is-in', 'is-revealed'); });
    html.setAttribute('data-motion', 'degraded');
    html.setAttribute('data-motion-reason', reason || '');
  }

  /* ---------- Inicijalizacija ---------- */
  function init() {
    if (state.dead || html.getAttribute('data-motion') === 'on') return;
    if (!window.gsap || !window.ScrollTrigger) { window.KesoCore && window.KesoCore.fallback(); return; }
    gsap.registerPlugin(ScrollTrigger);
    ScrollTrigger.config({ ignoreMobileResize: true });

    var fontsReady = (doc.fonts && doc.fonts.ready) ? doc.fonts.ready : Promise.resolve();
    fontsReady.then(start).catch(start);
  }

  function start() {
    if (state.dead) return;

    // Blag smooth scroll (Lenis) usklađen sa ScrollTrigger-om.
    if (window.Lenis) {
      state.lenis = new Lenis({ duration: 1.05, smoothWheel: true });
      state.lenis.on('scroll', ScrollTrigger.update);
      state.tick = function (t) { state.lenis.raf(t * 1000); };
      gsap.ticker.add(state.tick);
      gsap.ticker.lagSmoothing(0);
      doc.addEventListener('click', function (e) {
        var a = e.target.closest('a[href*="#"]');
        if (!a || !state.lenis) return;
        var url = new URL(a.href, location.href);
        if (url.pathname !== location.pathname || !url.hash) return;
        var target = $(url.hash);
        if (!target) return;
        e.preventDefault();
        state.lenis.scrollTo(target, { offset: -80 });
        history.replaceState(null, '', url.hash);
      });
    }

    html.classList.add('motion-on');
    html.setAttribute('data-motion', 'on');

    state.ctx = gsap.context(function () {
      /* Hero: naslov po linijama, ken burns, parallax. */
      $$('[data-hero]').forEach(function (hero) {
        var title = $('[data-split]', hero);
        var tl = gsap.timeline({ defaults: { ease: EASE_OUT } });
        if (title) {
          state.splits.push(title);
          var lines = splitLines(title);
          gsap.set(lines, { yPercent: 110 });
          title.classList.add('is-split');
          tl.to(lines, { yPercent: 0, duration: 1.15, stagger: 0.12 }, 0.05);
        }
        var rest = $$('.hero__sub, .hero__cta > *, .hero__facts > div', hero);
        gsap.set(rest, { opacity: 0, y: 18 });
        tl.to(rest, { opacity: 1, y: 0, duration: 0.9, stagger: 0.09 }, 0.55);

        var media = $('[data-hero-media]', hero);
        if (media) {
          gsap.fromTo(media, { scale: 1.12 }, { scale: 1, duration: 2.6, ease: 'power2.out' });
          var inner = $('.media__img, .media__ph', media);
          if (inner) {
            gsap.to(inner, { yPercent: 8, ease: 'none', scrollTrigger: { trigger: hero, start: 'top top', end: 'bottom top', scrub: true } });
          }
        }
      });

      /* Ostali naslovi (sekcije i naslovi stranica): maska po linijama, jednom. */
      $$('[data-split]').forEach(function (el) {
        if (el.closest('[data-hero]')) return;
        state.splits.push(el);
        var lines = splitLines(el);
        gsap.set(lines, { yPercent: 110 });
        el.classList.add('is-split');
        gsap.to(lines, {
          yPercent: 0, duration: 1, ease: EASE_OUT, stagger: 0.1,
          scrollTrigger: { trigger: el, start: 'top 88%', once: true }
        });
      });

      /* Fotografije: otkrivanje maskom (jednom) i parallax ±7%. */
      $$('[data-reveal-img]').forEach(function (el) {
        gsap.fromTo(el, { clipPath: 'inset(0 0 100% 0)' }, {
          clipPath: 'inset(0 0 0% 0)', duration: 1.3, ease: 'power3.inOut',
          scrollTrigger: { trigger: el, start: 'top 85%', once: true },
          onComplete: function () { el.classList.add('is-revealed'); el.style.clipPath = ''; }
        });
      });
      $$('[data-parallax]').forEach(function (el) {
        var inner = $('.media__img', el); // označeno mesto bez fotografije ne pomera se
        if (!inner) return;
        gsap.fromTo(inner, { yPercent: -7, scale: 1.16 }, {
          yPercent: 7, scale: 1.16, ease: 'none',
          scrollTrigger: { trigger: el, start: 'top bottom', end: 'bottom top', scrub: true }
        });
      });

      /* Tanke linije se iscrtavaju. */
      var lineSel = '.stats__item, .values__item, .refs__row, .timeline__item, .process__step, .floor-cards li';
      var lineEls = $$(lineSel);
      if (lineEls.length) {
        gsap.set(lineEls, { '--line': 0 });
        ScrollTrigger.batch(lineEls, {
          start: 'top 92%', once: true,
          onEnter: function (batch) { gsap.to(batch, { '--line': 1, duration: 1.2, ease: 'power2.out', stagger: 0.08 }); }
        });
      }
    }, doc.body);

    /* Pinovana sekcija "Kako gradimo": samo kad ima dovoljno prostora. */
    state.mm = gsap.matchMedia();
    state.mm.add('(min-width: 1024px) and (min-height: 680px)', function () {
      var sec = $('[data-process]');
      if (!sec) return;
      var steps = $$('[data-step]', sec);
      var bar = $('[data-process-bar]', sec);
      html.classList.add('process-pinned');
      var setActive = function (i) { steps.forEach(function (s, k) { s.classList.toggle('is-active', k === i); }); };
      setActive(0);
      ScrollTrigger.create({
        trigger: sec, start: 'top top',
        end: function () { return '+=' + Math.round(innerHeight * 0.55 * steps.length); },
        pin: true, anticipatePin: 1, invalidateOnRefresh: true,
        onUpdate: function (self) {
          var i = Math.min(steps.length - 1, Math.floor(self.progress * steps.length));
          setActive(i);
          if (bar) bar.parentNode.style.setProperty('--p', self.progress.toFixed(3));
        }
      });
      return function () { html.classList.remove('process-pinned'); steps.forEach(function (s) { s.classList.remove('is-active'); }); };
    });

    /* Re-deljenje naslova pri promeni širine. */
    var lastW = innerWidth, to = null;
    addEventListener('resize', function () {
      clearTimeout(to);
      to = setTimeout(function () {
        if (state.dead || Math.abs(innerWidth - lastW) < 40) return;
        lastW = innerWidth;
        state.splits.forEach(function (el) {
          if (!el.isConnected) return;
          var shown = true;
          var lines = splitLines(el);
          gsap.set(lines, { yPercent: shown ? 0 : 110 });
        });
        ScrollTrigger.refresh();
      }, 280);
    });

    // Promena uslova (uži prozor, uključeno smanjenje animacija): vrati se na Nivo 1.
    matchMedia('(min-width: 1024px)').addEventListener('change', function (e) { if (!e.matches) degrade('viewport'); });
    matchMedia('(prefers-reduced-motion: reduce)').addEventListener('change', function (e) { if (e.matches) degrade('reduced-motion'); });

    guard(function (avg) { degrade('slow-frames:' + avg.toFixed(1)); });
  }

  window.KesoMotion = { init: init, degrade: degrade };
})();
