/*
 * Keso Invest: interaktivna mapa (Leaflet). Inicijalizuje se tek kad sekcija uđe u viewport.
 * Bez JS-a ostaju placeholder i link "Otvori u Google mapama".
 */
(function () {
  'use strict';

  /* Pločice, stil i attribution na jednom mestu. Ključ dolazi iz data-maptiler-key (KESO_MAPTILER_KEY u wp-config.php). */
  var TILES = {
    styles: ['dataviz-light', 'basic-v2-light'], /* prvi koji ne vrati 404 */
    maptiler: 'https://api.maptiler.com/maps/{style}/256/{z}/{x}/{y}{r}.png?key={key}',
    attribution: '&copy; <a href="https://www.maptiler.com/copyright/" target="_blank" rel="noopener">MapTiler</a> &copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap contributors</a>',
    osm: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
    osmAttribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap contributors</a>',
    tileSize: 256,
    maxZoom: 19
  };

  var doc = document;
  var nodes = doc.querySelectorAll('[data-map]');
  if (!nodes.length) return;

  var PIN = '<svg class="keso-pin__svg" width="40" height="52" viewBox="0 0 40 52" aria-hidden="true" focusable="false">' +
    '<path d="M20 51C20 51 3 32.5 3 19.5a17 17 0 0 1 34 0C37 32.5 20 51 20 51Z" fill="currentColor"/>' +
    '<circle cx="20" cy="19.5" r="9" fill="#fff"/>' +
    '<text x="20" y="24" text-anchor="middle" font-family="inherit" font-size="12" font-weight="700" fill="currentColor">K</text></svg>';

  function init(el) {
    if (el.__kesoMap || !window.L) return;
    el.__kesoMap = true;
    var L = window.L;
    var canvas = el.querySelector('[data-map-canvas]');
    var lat = parseFloat(el.getAttribute('data-lat'));
    var lng = parseFloat(el.getAttribute('data-lng'));
    var zoom = parseInt(el.getAttribute('data-zoom'), 10) || 16;
    var title = el.getAttribute('data-title') || '';
    if (!canvas || isNaN(lat) || isNaN(lng)) return;
    var pos = L.latLng(lat, lng);
    var touch = matchMedia('(pointer:coarse)').matches || 'ontouchstart' in window;

    var map = L.map(canvas, {
      center: pos,
      zoom: zoom,
      zoomControl: false,
      scrollWheelZoom: false,
      dragging: !touch,
      tap: false,
      keyboard: true,
      zoomSnap: 1,
      zoomAnimation: !el.classList.contains('is-lite'),
      attributionControl: false
    });

    var key = (el.getAttribute('data-maptiler-key') || '').trim();
    var retina = window.devicePixelRatio > 1;
    var addTiles = function (style) {
      var url, attr;
      if (key && style) {
        url = TILES.maptiler.replace('{style}', style).replace('{key}', encodeURIComponent(key)).replace('{r}', retina ? '@2x' : '');
        attr = TILES.attribution;
      } else {
        url = TILES.osm;
        attr = TILES.osmAttribution;
        el.classList.add('is-osm');
      }
      L.tileLayer(url, { attribution: attr, tileSize: TILES.tileSize, maxZoom: TILES.maxZoom }).addTo(map);
    };
    if (key && window.fetch) {
      /* Probni zahtev: ako stil vrati 404, ide sledeći; ako nijedan ne radi (npr. loš ključ), OSM. */
      var probe = function (i) {
        if (i >= TILES.styles.length) { addTiles(''); return; }
        fetch('https://api.maptiler.com/maps/' + TILES.styles[i] + '/256/0/0/0.png?key=' + encodeURIComponent(key))
          .then(function (r) { if (r.ok) addTiles(TILES.styles[i]); else probe(i + 1); })
          .catch(function () { addTiles(TILES.styles[i]); });
      };
      probe(0);
    } else {
      addTiles(key ? TILES.styles[0] : '');
    }
    L.control.attribution({ position: 'bottomright', prefix: false }).addTo(map);

    /* Marker: vrh pina (donja sredina SVG-a) stoji na koordinati. */
    var icon = L.divIcon({
      className: 'keso-pin',
      html: '<span class="keso-pin__pulse"></span>' + PIN,
      iconSize: [40, 52],
      iconAnchor: [20, 51]
    });
    var marker = L.marker(pos, { icon: icon, keyboard: false, interactive: false }).addTo(map);
    var mel = marker.getElement();
    if (mel) {
      mel.setAttribute('role', 'img');
      mel.setAttribute('aria-label', title);
    }

    /* Sopstvene +/- kontrole gore desno. */
    var Zoom = L.Control.extend({
      options: { position: 'topright' },
      onAdd: function () {
        var box = L.DomUtil.create('div', 'map-zoom');
        var mk = function (txt, label, fn) {
          var b = L.DomUtil.create('button', 'map-zoom__btn', box);
          b.type = 'button';
          b.textContent = txt;
          b.setAttribute('aria-label', label);
          L.DomEvent.disableClickPropagation(b);
          L.DomEvent.on(b, 'click', fn);
        };
        mk('+', 'Uvećaj mapu', function () { map.zoomIn(); });
        mk('−', 'Umanji mapu', function () { map.zoomOut(); });
        return box;
      }
    });
    map.addControl(new Zoom());

    /* Centar pomeren tako da pin ne bude ispod kartice dole levo (samo široki ekrani). */
    var recenter = function () {
      var s = map.getSize();
      var shift = s.x >= 720 ? L.point(-s.x * 0.12, s.y * 0.1) : L.point(0, 0);
      var c = map.unproject(map.project(pos, map.getZoom()).add(shift), map.getZoom());
      map.setView(c, map.getZoom(), { animate: false });
    };
    recenter();
    var rt;
    window.addEventListener('resize', function () { clearTimeout(rt); rt = setTimeout(function () { map.invalidateSize(); recenter(); }, 200); });

    /* Pristupačnost kontejnera. */
    var c = map.getContainer();
    c.setAttribute('aria-label', title + '. Strelice pomeraju mapu, plus i minus menjaju zumiranje.');

    /* Točkić tek posle klika/fokusa; gasi se kad se izađe iz mape. */
    var wheelOn = function () { map.scrollWheelZoom.enable(); };
    var wheelOff = function () { map.scrollWheelZoom.disable(); };
    map.on('click focus', wheelOn);
    c.addEventListener('focus', wheelOn);
    c.addEventListener('blur', wheelOff);
    c.addEventListener('mouseleave', wheelOff);

    /* Touch: jedan prst skroluje stranicu; dva prsta zumiraju/pomeraju; prvi tap uključuje pomeranje jednim prstom. */
    if (touch) {
      var hint = doc.createElement('div');
      hint.className = 'map__hint';
      hint.setAttribute('aria-hidden', 'true');
      hint.textContent = el.getAttribute('data-hint') || '';
      el.appendChild(hint);
      var ht;
      c.addEventListener('touchmove', function (e) {
        if (e.touches.length === 1 && !map.dragging.enabled()) {
          hint.classList.add('is-on');
          clearTimeout(ht);
          ht = setTimeout(function () { hint.classList.remove('is-on'); }, 1600);
        }
      }, { passive: true });
      map.on('click', function () { map.dragging.enable(); });
    }

    /* Prikaz: placeholder (mreža) → fade na mapu. */
    map.whenReady(function () {
      requestAnimationFrame(function () { el.classList.add('is-ready'); });
    });
    /* Ako pločice ne stignu (offline), placeholder + link ostaju vidljivi ispod; mapa se ipak otkriva kad stigne prva pločica. */
  }

  function boot(el) {
    if (window.L) { init(el); return; }
    window.addEventListener('load', function () { init(el); });
  }

  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) { io.unobserve(en.target); boot(en.target); }
      });
    }, { rootMargin: '200px 0px' });
    nodes.forEach(function (n) { io.observe(n); });
  } else {
    nodes.forEach(boot);
  }
})();
