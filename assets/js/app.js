(function () {
  var root = document.documentElement;
  var btn = document.getElementById('theme-toggle');
  if (!btn) return;

  function label() {
    btn.textContent = root.getAttribute('data-theme') === 'dark' ? 'Light mode' : 'Dark mode';
  }

  label();

  btn.addEventListener('click', function () {
    var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    root.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    label();
  });
})();