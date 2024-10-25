// To be minified and added to the head

(function() {
  const name = 'fcnLoggedIn=';
  const cookies = document.cookie.split(';');

  let fingerprint = null;

  for (var i = 0; i < cookies.length; i++) {
    const c = cookies[i].trim();

    if (c.indexOf(name) == 0) {
      fingerprint = decodeURIComponent(c.substring(name.length, c.length));
      break;
    }
  }

  if (!fingerprint) {
    return;
  }

  const skins = JSON.parse(localStorage.getItem('fcnSkins')) ?? { data: {}, active: null, fingerprint: fingerprint };

  if (skins?.data?.[skins.active]?.css && fingerprint === skins?.fingerprint) {
    const styleTag = document.createElement('style');

    styleTag.textContent = skins.data[skins.active].css;
    styleTag.id = 'fictioneer-active-custom-skin';

    document.querySelector('head').appendChild(styleTag);
  }
})();
