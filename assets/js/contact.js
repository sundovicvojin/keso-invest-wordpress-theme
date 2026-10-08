/*
 * Keso Invest: contact.js. Kontakt forma preko fetch() na admin-ajax.php.
 * Bez JS-a forma radi kao običan POST na admin-post.php. Bez biblioteka.
 * Nonce i vremenski žig se uzimaju sveži pri učitavanju (stranica može biti keširana).
 */
(function () {
  'use strict';

  var cfg = window.KESO_FORM || {};
  var i18n = cfg.i18n || {};
  if (!window.fetch || !cfg.ajax) return;

  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };

  $$('[data-form]').forEach(function (form) {
    var status = $('[data-form-status]', form);
    var submit = $('[data-form-submit]', form);
    var label = submit.innerHTML;
    var tokenReady = null;

    var setStatus = function (cls, msg) {
      status.textContent = '';
      var p = document.createElement('p');
      p.className = 'notice notice--' + cls;
      p.textContent = msg;
      status.appendChild(p);
    };
    var clearErrors = function () {
      $$('[aria-invalid]', form).forEach(function (f) { f.removeAttribute('aria-invalid'); f.removeAttribute('aria-describedby'); });
      $$('.field__err', form).forEach(function (p) { p.hidden = true; p.textContent = ''; });
    };
    var busy = function (on) {
      submit.disabled = on;
      if (on) { submit.setAttribute('aria-busy', 'true'); submit.textContent = i18n.sending || 'Šaljem…'; }
      else { submit.removeAttribute('aria-busy'); submit.innerHTML = label; }
    };

    // Svež nonce + žig.
    var loadToken = function () {
      tokenReady = fetch(cfg.ajax + '?action=keso_token', { credentials: 'same-origin', cache: 'no-store' })
        .then(function (r) { return r.json(); })
        .then(function (j) {
          if (j && j.success) {
            form.elements.kf_nonce.value = j.data.nonce;
            form.elements.kf_ts.value = j.data.ts;
          }
        })
        .catch(function () {});
      return tokenReady;
    };
    loadToken();

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      clearErrors();
      status.textContent = '';
      busy(true);
      Promise.resolve(tokenReady).then(function () {
        var body = new FormData(form);
        body.set('action', 'keso_contact');
        return fetch(cfg.ajax, { method: 'POST', body: body, credentials: 'same-origin' });
      })
        .then(function (r) { return r.json().catch(function () { return {}; }).then(function (j) { return { ok: r.ok, j: j }; }); })
        .then(function (res) {
          var j = res.j || {};
          busy(false);
          if (res.ok && j.ok) {
            setStatus('ok', j.message);
            form.reset();
            loadToken();
            status.focus();
            return;
          }
          setStatus('err', j.message || i18n.error);
          Object.keys(j.errors || {}).forEach(function (name) {
            var field = form.elements[name];
            var err = $('#' + name + '-err', form);
            if (field) { field.setAttribute('aria-invalid', 'true'); field.setAttribute('aria-describedby', name + '-err'); }
            if (err) { err.textContent = j.errors[name]; err.hidden = false; }
          });
          status.focus();
          if (!j.errors) loadToken();
        })
        .catch(function () {
          busy(false);
          setStatus('err', i18n.error || 'Slanje nije uspelo.');
          status.focus();
        });
    });
  });
})();
