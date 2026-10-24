// Small progressive enhancements. The site still works (navigation, filters via Enter, links) without JS.
(function () {
  // ----- Hamburger menu (visible on mobile only, see CSS) -----
  var burger = document.getElementById('burger');
  var nav = document.getElementById('nav');
  function setMenu(open) {
    nav.classList.toggle('open', open);
    burger.setAttribute('aria-expanded', open);
    burger.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
  }
  if (burger && nav) {
    burger.addEventListener('click', function () { setMenu(!nav.classList.contains('open')); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setMenu(false); });
  }

  // ----- Catalog filters: submit the form when a select changes -----
  document.querySelectorAll('[data-autosubmit]').forEach(function (el) {
    el.addEventListener('change', function () { el.form.submit(); });
  });

  // ----- Flower gallery: thumbnails switch the big image -----
  var thumbs = document.querySelectorAll('#gthumbs button');
  var slides = document.querySelectorAll('#gmain .slide');
  thumbs.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var i = Number(btn.dataset.i);
      slides.forEach(function (s, idx) { s.hidden = idx !== i; });
      thumbs.forEach(function (b) { b.classList.toggle('on', b === btn); });
    });
  });

  // ----- Confirm before destructive actions: <form data-confirm="Delete X?"> -----
  document.querySelectorAll('form[data-confirm]').forEach(function (f) {
    f.addEventListener('submit', function (e) { if (!confirm(f.dataset.confirm)) e.preventDefault(); });
  });
})();

// ----- Confirm before submitting forms that have data-confirm -----
document.querySelectorAll('form[data-confirm]').forEach(function (form) {
  form.addEventListener('submit', function (e) {
    if (!confirm(form.dataset.confirm)) e.preventDefault();
  });
});