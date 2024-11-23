/**
 * Validates a CSS string.
 *
 * @since 5.26.0
 * @param {String} css - The CSS to validate.
 * @return {Boolean} True or false.
 */

function fcn_validateCss(css) {
  const openBraces = (css.match(/{/g) || []).length;
  const closeBraces = (css.match(/}/g) || []).length;

  if (openBraces !== closeBraces) {
    return false;
  }

  if (css.includes('<')) {
    return false;
  }

  return true;
}

/**
 * Returns the skins JSON from local storage or a default.
 *
 * @since 5.26.0
 * @return {Object} The skins JSON.
 */

function fcn_getSkins() {
  const fingerprint = fcn_getCookie('fcnLoggedIn');

  if (!fingerprint) {
    return null;
  }

  const _default = { 'data': {}, 'active': null, 'fingerprint': fingerprint };
  const skins = fcn_parseJSON(localStorage.getItem('fcnSkins')) ?? _default;

  if (!skins?.fingerprint || fingerprint !== skins.fingerprint) {
    return _default;
  }

  if (typeof skins.data !== 'object' || Array.isArray(skins.data)) {
    skins.data = {};
  }

  return skins;
}

/**
 * Saves the skins JSON to local storage.
 *
 * @since 5.26.0
 * @param {Object} skins - The skins to store.
 */

function fcn_setSkins(skins) {
  if (
    typeof skins !== 'object' || skins === null ||
    typeof skins.data !== 'object' || Array.isArray(skins.data)
  ) {
    fcn_showNotification(fcn_skinTranslations.invalidJson, 3, 'warning');
    return;
  }

  if (!skins?.fingerprint || fcn_getCookie('fcnLoggedIn') !== skins.fingerprint) {
    fcn_showNotification(fcn_skinTranslations.wrongFingerprint, 3, 'warning');
    return;
  }

  localStorage.setItem('fcnSkins', JSON.stringify(skins));
}

/**
 * Returns info object about a CSS skin.
 *
 * @since 5.26.0
 * @param {String} css - The CSS to analyze.
 * @return {Object} CSS info with name, author, and version.
 */

function fcn_getSkinInfo(css) {
  const nameMatch = css.match(/Name:\s*(.+)/);
  const authorMatch = css.match(/Author:\s*(.+)/);
  const versionMatch = css.match(/Version:\s*(.+)/);

  return {
    name: nameMatch ? fcn_sanitizeHTML(nameMatch[1].trim()) : null,
    author: authorMatch ? fcn_sanitizeHTML(authorMatch[1].trim()) : null,
    version: versionMatch ? fcn_sanitizeHTML(versionMatch[1].trim()) : null
  };
}

/**
 * Toggles the currently selected skin (custom CSS).
 *
 * @since 5.26.0
 * @param {HTMLElement} target - The clicked element.
 */

function fcn_toggleSkin(target) {
  const item = target.closest('[data-css-skin-finder="skin-item"]');
  const skins = fcn_getSkins();

  if (item.classList.contains('active')) {
    _$$('[data-css-skin-finder="skin-item"]').forEach(element => element.classList.remove('active'));
    skins.active = null;
  } else {
    _$$('[data-css-skin-finder="skin-item"]').forEach(element => element.classList.remove('active'));
    item.classList.add('active');
    skins.active = target.dataset.skinId;
  }

  fcn_setSkins(skins);
  fcn_applySkin();
}

/**
 * Delete the skin (custom CSS).
 *
 * @since 5.26.0
 * @param {HTMLElement} target - The clicked element.
 */

function fcn_deleteSkin(target) {
  const item = target.closest('[data-css-skin-finder="skin-item"]');
  const skins = fcn_getSkins();

  if (item.classList.contains('active')) {
    skins.active = null;
  }

  delete skins.data[target.dataset.skinId];
  _$('[data-css-skin-target="file"]').value = '';

  fcn_setSkins(skins);
  fcn_renderSkinList();
  fcn_applySkin();
}

/**
 * Add skin (custom CSS) to document <head>.
 *
 * @since 5.26.0
 */

function fcn_applySkin() {
  const fingerprint = fcn_getCookie('fcnLoggedIn');

  // Ensure user is logged-in and not inside admin panel
  if (!fingerprint || _$('body.wp-admin')) {
    return;
  }

  // Get skins from local storage
  const skins = fcn_getSkins();

  // Cleanup old style tag (if any)
  _$$$('fictioneer-active-custom-skin')?.remove();

  // Check fingerprint
  if (skins?.fingerprint !== fingerprint) {
    return;
  }

  // Check if skins data is valid and an active skin is set
  if (skins?.data?.[skins.active]?.css) {
    const styleTag = document.createElement('style');

    styleTag.textContent = skins.data[skins.active].css;
    styleTag.id = 'fictioneer-active-custom-skin';

    _$('head').appendChild(styleTag);
  }
}

/**
 * Renders list of selectable skins.
 *
 * @since 5.26.0
 */

function fcn_renderSkinList() {
  const container = _$('[data-css-skin-target="list"]');
  const fingerprint = fcn_getCookie('fcnLoggedIn');

  // Ensure the theme login check is passed
  if (!fingerprint || !container) {
    return;
  }

  // Get skins from local storage
  const skins = fcn_getSkins();

  // Clear previous content
  container.innerHTML = '';

  // Check fingerprint
  if (skins?.fingerprint !== fingerprint) {
    _$('[data-css-skin-target="form"]').style.display = '';
    return;
  }

  // Ensure skins data exists and has entries
  if (skins?.data && Object.keys(skins.data).length > 0) {
    // Loop through the skins and render them
    Object.entries(skins.data).forEach(([key, skin]) => {
      const template = _$('[data-css-skin-target="template"]').content.cloneNode(true);

      // Active skin?
      if (skins.active === key) {
        template.querySelector('[data-css-skin-finder="skin-item"]').classList.add('active');
      }

      // Fill template with skin data
      template.querySelector('[data-action="click->css-skin#toggle"]').dataset.skinId = key;
      template.querySelector('[data-action="click->css-skin#delete"]').dataset.skinId = key;
      template.querySelector('[data-css-skin-finder="name"]').innerText = skin.name;
      template.querySelector('[data-css-skin-finder="version"]').innerText = skin.version;
      template.querySelector('[data-css-skin-finder="author"]').innerText = skin.author;

      // Append the template to the container
      container.appendChild(template);
    });

    // Add click events
    _$$('[data-action="click->css-skin#toggle"]').forEach(button => {
      button.addEventListener('click', event => fcn_toggleSkin(event.currentTarget));
    });

    _$$('[data-action="click->css-skin#delete"]').forEach(button => {
      button.addEventListener('click', event => fcn_deleteSkin(event.currentTarget));
    });
  }

  if (Object.keys(skins.data).length > 2) {
    _$('[data-css-skin-target="form"]').style.display = 'none';
  } else {
    _$('[data-css-skin-target="form"]').style.display = '';
  }
}

// Initialize
fcn_renderSkinList();

// Upload
_$('[data-css-skin-target="file"]')?.addEventListener('input', event => {
  event.preventDefault();

  const input = event.currentTarget;
  const file = input.files[0];
  const skins = fcn_getSkins();
  const fingerprint = fcn_getCookie('fcnLoggedIn');

  console.log(input);

  if (Object.keys(skins.data).length > 2) {
    fcn_showNotification(fcn_skinTranslations.tooManySkins, 3, 'warning');
    return;
  }

  if (skins?.fingerprint !== fingerprint) {
    fcn_showNotification(fcn_skinTranslations.wrongFingerprint, 3, 'warning');
    return;
  }

  if (!file) {
    return;
  }

  if (file.size > 200000) {
    fcn_showNotification(fcn_skinTranslations.fileTooLarge, 3, 'warning');
    return;
  }

  if (file.type !== 'text/css') {
    fcn_showNotification(fcn_skinTranslations.wrongFileType, 3, 'warning');
    return;
  }

  const reader = new FileReader();

  reader.onload = event => {
    const css = event.target.result;
    const info = fcn_getSkinInfo(css);
    const skins = fcn_getSkins();

    if (!fcn_validateCss(css)) {
      fcn_showNotification(fcn_skinTranslations.invalidCss, 5, 'warning');
      return;
    }

    if (!info.name) {
      fcn_showNotification(fcn_skinTranslations.missingMetaData, 3, 'warning');
      return;
    }

    const key = btoa(info.name);

    skins.data[key] = {
      name: info.name,
      version: info.version,
      author: info.author,
      css: css
    };

    fcn_setSkins(skins);
    fcn_renderSkinList();
  }

  reader.onerror = () => {
    console.error(reader.error);
    fcn_showNotification(reader.error, 3, 'warning');
    return;
  }

  reader.readAsText(file);
});

/**
 * AJAX: Uploads the skins to the database.
 *
 * @since 5.26.0
 * @param {HTMLElement} trigger - The event trigger element.
 */

function fcn_uploadSkins(trigger) {
  // Ensure the theme login check is passed
  if (!FcnUtils.loggedIn() || trigger.classList.contains('disabled')) {
    return;
  }

  // Get skins from local storage
  const skins = fcn_getSkins();

  // Toggle button progress
  fcn_toggleInProgress(trigger);
  _$('[data-css-skin-target="action-status-message"]').classList.add('invisible');

  // Request
  fcn_ajaxPost({
    'action': 'fictioneer_ajax_save_skins',
    'fcn_fast_ajax': 1,
    'skins': JSON.stringify(skins)
  })
  .then(response => {
    if (response.success) {
      fcn_showNotification(response.data.message, 3, 'success');
      fcn_toggleSkinNotice(_$('[data-css-skin-target="action-status-message"]'));
    } else {
      fcn_showNotification(
        response.data.failure ?? response.data.error ?? fictioneer_tl.notification.error,
        3,
        'warning'
      );

      // Make sure the actual error (if any) is printed to the console too
      if (response.data.error || response.data.failure) {
        console.error('Error:', response.data.error ?? response.data.failure);
      }
    }
  })
  .catch(error => {
    if (error.status && error.statusText) {
      fcn_showNotification(`${error.status}: ${error.statusText}`, 3, 'warning');
    }

    console.error(error);
  })
  .then(() => {
    _$('[data-css-skin-target="file"]').value = '';
    fcn_toggleInProgress(trigger);
  });
}

_$('[data-action="click->css-skin#upload"]')?.addEventListener('click', event => {
  fcn_uploadSkins(event.currentTarget);
});

/**
 * AJAX: Downloads the skins from the database.
 *
 * @since 5.26.0
 * @param {HTMLElement} trigger - The event trigger element.
 */

function fcn_downloadSkins(trigger) {
  // Ensure the theme login check is passed
  if (!FcnUtils.loggedIn() || trigger.classList.contains('disabled')) {
    return;
  }

  // Toggle button progress
  fcn_toggleInProgress(trigger);
  _$('[data-css-skin-target="action-status-message"]').classList.add('invisible');

  // Request
  fcn_ajaxPost({
    'action': 'fictioneer_ajax_get_skins',
    'fcn_fast_ajax': 1
  })
  .then(response => {
    if (response.success) {
      fcn_showNotification(response.data.message, 3, 'success');
      fcn_toggleSkinNotice(_$('[data-css-skin-target="action-status-message"]'));
      fcn_setSkins(fcn_parseJSON(response.data.skins));
      fcn_renderSkinList();
      fcn_applySkin();
    } else {
      fcn_showNotification(
        response.data.failure ?? response.data.error ?? fictioneer_tl.notification.error,
        3,
        'warning'
      );

      // Make sure the actual error (if any) is printed to the console too
      if (response.data.error || response.data.failure) {
        console.error('Error:', response.data.error ?? response.data.failure);
      }
    }
  })
  .catch(error => {
    if (error.status && error.statusText) {
      fcn_showNotification(`${error.status}: ${error.statusText}`, 3, 'warning');
    }

    console.error(error);
  })
  .then(() => {
    _$('[data-css-skin-target="file"]').value = '';
    fcn_toggleInProgress(trigger);
  });
}

_$('[data-action="click->css-skin#download"]')?.addEventListener('click', event => {
  fcn_downloadSkins(event.currentTarget);
});

/**
 * Toggles notice for 3 seconds.
 *
 * @since 5.26.0
 * @param {HTMLElement} element - The targeted element.
 */

function fcn_toggleSkinNotice(element) {
  element.classList.remove('invisible');

  setTimeout(() => {
    element.classList.add('invisible');
  }, 3000);
}
