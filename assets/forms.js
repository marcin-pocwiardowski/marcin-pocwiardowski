document.addEventListener('submit', function (e) {
  var form = e.target;
  if (!form.matches('form[data-form]')) return;
  e.preventDefault();

  var status = form.querySelector('.form-status');
  var button = form.querySelector('button[type="submit"]');
  var data = new FormData(form);
  data.set('formularz', form.dataset.form);

  if (button) { button.disabled = true; }
  if (status) { status.hidden = false; status.textContent = 'Wysyłam…'; status.className = 'form-status'; }

  fetch('/formularz.php', { method: 'POST', body: data })
    .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, body: j }; }); })
    .then(function (res) {
      if (res.body && res.body.ok) {
        form.reset();
        if (status) { status.textContent = 'Dziękuję, zgłoszenie wysłane!'; status.className = 'form-status form-status-ok'; }
      } else {
        var msg = (res.body && res.body.error) || 'Coś poszło nie tak.';
        if (status) { status.textContent = msg; status.className = 'form-status form-status-error'; }
      }
    })
    .catch(function () {
      if (status) {
        status.textContent = 'Nie udało się wysłać. Napisz bezpośrednio na marcin@techne.pl.';
        status.className = 'form-status form-status-error';
      }
    })
    .finally(function () {
      if (button) { button.disabled = false; }
    });
});
