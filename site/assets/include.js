function loadPartial(id, url) {
  var el = document.getElementById(id);
  if (!el) return;
  fetch(url)
    .then(function (r) { return r.text(); })
    .then(function (html) { el.innerHTML = html; })
    .catch(function (err) { console.error('Nie udało się wczytać ' + url, err); });
}

loadPartial('nav-placeholder', '/partials/nav.html');
loadPartial('banner-placeholder', '/partials/banner.html');
loadPartial('footer-placeholder', '/partials/footer.html');
loadPartial('events-placeholder', '/partials/events.html');
