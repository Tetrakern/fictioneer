// =============================================================================
// UNSET OAUTH CONNECTION
// =============================================================================

/**
 * Unset OAuth connection.
 *
 * @description Requests the deletion of an OAuth connection via AJAX, which
 * must be confirmed in a prompt with a localized confirmation string (DELETE
 * per default). In case this function is called maliciously, the request is
 * validated server-side as well.
 *
 * @since 4.5.0
 * @param {HTMLElement} button - The clicked button.
 */

function fcn_unsetOauth(button) {
  const confirm = prompt(button.dataset.warning);

  if (!confirm || confirm.toLowerCase() != button.dataset.confirm.toLowerCase()) {
    return;
  }

  const connection = _$$$(`oauth-${button.dataset.channel}`);

  // Mark as in-progress
  connection.classList.add('ajax-in-progress');

  // Request
  fcn_ajaxPost(payload = {
    'action': 'fictioneer_ajax_unset_my_oauth',
    'nonce': button.dataset.nonce,
    'channel': button.dataset.channel,
    'id': button.dataset.id
  })
  .then(response => {
    if (response.success) {
      // Successfully unset
      connection.classList.remove('_connected');
      connection.classList.add('_disconnected');
      connection.querySelector('button').remove();
      fcn_showNotification(connection.dataset.unset);
    } else {
      // Failed to unset
      connection.style.background = 'var(--notice-warning-background)';

      fcn_showNotification(
        response.data.failure ?? response.data.error ?? fictioneer_tl.notification.error,
        5,
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
      connection.style.background = 'var(--notice-warning-background)';
      fcn_showNotification(`${error.status}: ${error.statusText}`, 5, 'warning');
    }

    console.error(error);
  })
  .then(() => {
    // Regardless of outcome
    connection.classList.remove('ajax-in-progress');
  });
}

// Listen to click on unset buttons
_$$('.button-unset-oauth').forEach(element => {
  element.addEventListener(
    'click',
    e => {
      fcn_unsetOauth(e.currentTarget);
    }
  );
});

// =============================================================================
// DELETE MY ACCOUNT
// =============================================================================

/**
 * Delete an user's account on request.
 *
 * @description Requests the deletion of an account via AJAX, which must be
 * confirmed in a prompt with a localized confirmation string (DELETE per
 * default). Roles above subscribers cannot delete their account this way.
 * In case this function is called maliciously, the request is validated
 * server-side as well.
 *
 * @since 4.5.0
 * @param {HTMLElement} button - The clicked button.
 */

function fcn_deleteMyAccount(button) {
  if (_$$$('button-delete-my-account').hasAttribute('disabled')) {
    return;
  }

  const confirm = prompt(button.dataset.warning);

  if (!confirm || confirm.toLowerCase() != button.dataset.confirm.toLowerCase()) {
    return;
  }

  // Disable button
  _$$$('button-delete-my-account').setAttribute('disabled', true);

  // Request
  fcn_ajaxPost({
    'action': 'fictioneer_ajax_delete_my_account',
    'nonce': button.dataset.nonce,
    'id': button.dataset.id
  })
  .then(response => {
    if (response.success) {
      // Successfully deleted
      localStorage.removeItem('fcnAuth');
      localStorage.removeItem('fcnProfileAvatar');
      location.reload();
    } else {
      // Could not be deleted
      _$$$('button-delete-my-account').innerHTML = response.data.button;

      fcn_showNotification(
        response.data.failure ?? response.data.error ?? fictioneer_tl.notification.error,
        5,
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
      fcn_showNotification(`${error.status}: ${error.statusText}`, 5, 'warning');
      _$$$('button-delete-my-account').innerHTML = response.data.button;
    }

    console.error(error);
  });
}

// Listen for click on delete profile button
_$$$('button-delete-my-account')?.addEventListener(
  'click',
  e => {
    fcn_deleteMyAccount(e.currentTarget);
  }
);

// =============================================================================
// CLEAR DATA
// =============================================================================

const /** @const {DOMStringMap} */ fcn_profileDataTranslations = _$$$('profile-data-translations')?.dataset;

/**
 * Prompt with string submission before deletion of user data.
 *
 * @since 5.0.0
 * @param {HTMLElement} button - The clicked button.
 * @return {Boolean} True or false.
 */

function fcn_dataDeletionPrompt(button) {
  const confirm = prompt(button.dataset.warning);

  if (!confirm || confirm.toLowerCase() != button.dataset.confirm.toLowerCase()) {
    return false;
  }

  return true;
}

/**
 * AJAX request to clear specific data.
 *
 * @since 4.5.0
 * @param {HTMLElement} button - The clicked button.
 * @param {String} action - The action to perform.
 */

function fcn_clearData(button, action) {
  // Update view
  const card = button.closest('.card');

  // Clear web storage
  localStorage.removeItem('fcnBookshelfContent');

  // Indicator
  card.classList.add('ajax-in-progress');
  button.remove();

  // Request
  fcn_ajaxPost({
    'action': action,
    'fcn_fast_ajax': 1,
    'nonce': button.dataset.nonce
  })
  .then(response => {
    // Check for success
    if (response.success) {
      card.querySelector('.card__content').innerHTML = response.data.success;
    } else {
      fcn_showNotification(
        response.data.failure ?? response.data.error ?? fictioneer_tl.notification.error,
        10,
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
      fcn_showNotification(`${error.status}: ${error.statusText}`, 10, 'warning');
    }

    console.error(error);
  })
  .then(() => {
    card.classList.remove('ajax-in-progress');
  });
}

// =============================================================================
// BUTTON: CLEAR COMMENTS
// =============================================================================

// Listen for click on clear comments button
_$('.button-clear-comments')?.addEventListener(
  'click',
  e => {
    // Confirm clear request using localized string
    if (!fcn_dataDeletionPrompt(e.currentTarget)) {
      return;
    }

    // Clear data
    fcn_clearData(e.currentTarget, 'fictioneer_ajax_clear_my_comments');
  }
);

// =============================================================================
// BUTTON: CLEAR COMMENT SUBSCRIPTIONS
// =============================================================================

// Listen for click on clear comment subscriptions button
_$('.button-clear-comment-subscriptions')?.addEventListener(
  'click',
  e => {
    // Confirm clear request using localized string
    if (!fcn_dataDeletionPrompt(e.currentTarget)) {
      return;
    }

    // Clear data
    fcn_clearData(e.currentTarget, 'fictioneer_ajax_clear_my_comment_subscriptions');
  }
);

// =============================================================================
// BUTTON: CLEAR CHECKMARKS
// =============================================================================

// Listen for click on clear checkmarks button
_$('.button-clear-checkmarks')?.addEventListener(
  'click',
  e => {
    // Confirm clear request using localized string
    if (!fcn_dataDeletionPrompt(e.currentTarget)) {
      return;
    }

    // Update local storage and view
    const currentUserData = fcn_getUserData();
    currentUserData.checkmarks = { 'data': {}, 'updated': Date.now() };
    fcn_setUserData(currentUserData);

    // Update views
    fcn_updateCheckmarksView();

    // Clear data
    fcn_clearData(e.currentTarget, 'fictioneer_ajax_clear_my_checkmarks', true);
  }
);

// =============================================================================
// BUTTON: CLEAR REMINDERS
// =============================================================================

// Listen for click on clear reminders button
_$('.button-clear-reminders')?.addEventListener(
  'click',
  e => {
    // Confirm clear request using localized string
    if (!fcn_dataDeletionPrompt(e.currentTarget)) {
      return;
    }

    // Update local storage and view
    const currentUserData = fcn_getUserData();
    currentUserData.reminders = { 'data': {} };
    fcn_setUserData(currentUserData);

    // Update view
    fcn_updateRemindersView();

    // Clear data
    fcn_clearData(e.currentTarget, 'fictioneer_ajax_clear_my_reminders', true);
  }
);

// =============================================================================
// BUTTON: CLEAR FOLLOWS
// =============================================================================

// Listen for click on clear follows button
_$('.button-clear-follows')?.addEventListener(
  'click',
  e => {
    // Confirm clear request using localized string
    if (!fcn_dataDeletionPrompt(e.currentTarget)) {
      return;
    }

    // Update local storage and view
    const currentUserData = fcn_getUserData();
    currentUserData.follows = { 'data': {} };
    fcn_setUserData(currentUserData);

    // Update view
    fcn_updateFollowsView();

    // Clear data
    fcn_clearData(e.currentTarget, 'fictioneer_ajax_clear_my_follows', true);
  }
);

// =============================================================================
// BUTTON: CLEAR BOOKMARKS
// =============================================================================

// Listen for click on clear bookmarks button
_$('.button-clear-bookmarks')?.addEventListener(
  'click',
  e => {
    // Confirm clear request using localized string
    if (!fcn_dataDeletionPrompt(e.currentTarget)) {
      return;
    }

    // Remove bookmarks
    const currentUserData = fcn_getUserData();
    currentUserData.bookmarks = '{}';
    fcn_setUserData(currentUserData);
    fcn_bookmarks.data = {};

    // Update view
    e.currentTarget.closest('.card').querySelector('.card__content').innerHTML = fcn_profileDataTranslations.clearedSuccess;

    // Update local storage and database
    fcn_setBookmarks(fcn_bookmarks);
  }
);

// =============================================================================
// CSS SKINS
// =============================================================================

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
  const item = target.closest('.custom-skin');
  const skins = fcn_getSkins();

  if (item.classList.contains('active')) {
    _$$('.custom-skin').forEach(element => element.classList.remove('active'));
    skins.active = null;
  } else {
    _$$('.custom-skin').forEach(element => element.classList.remove('active'));
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
  const item = target.closest('.custom-skin');
  const skins = fcn_getSkins();

  if (item.classList.contains('active')) {
    skins.active = null;
  }

  delete skins.data[target.dataset.skinId];
  _$('#css-upload-form > input').value = '';

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

  // Ensure the theme login check is passed
  if (!fingerprint) {
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
  const container = _$('.custom-skin-list');
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
    _$$$('css-upload-form').style.display = '';
    return;
  }

  // Ensure skins data exists and has entries
  if (skins?.data && Object.keys(skins.data).length > 0) {
    // Loop through the skins and render them
    Object.entries(skins.data).forEach(([key, skin]) => {
      const template = _$$$('template-custom-skin').content.cloneNode(true);

      // Active skin?
      if (skins.active === key) {
        template.querySelector('.custom-skin').classList.add('active');
      }

      // Fill template with skin data
      template.querySelector('[data-click-action="skin-toggle"]').dataset.skinId = key;
      template.querySelector('[data-click-action="skin-delete"]').dataset.skinId = key;
      template.querySelector('[data-finder="skin-name"]').innerText = skin.name;
      template.querySelector('[data-finder="skin-version"]').innerText = skin.version;
      template.querySelector('[data-finder="skin-author"]').innerText = skin.author;

      // Append the template to the container
      container.appendChild(template);
    });

    // Add click events
    _$$('[data-click-action="skin-toggle"]').forEach(button => {
      button.addEventListener('click', event => fcn_toggleSkin(event.currentTarget));
    });

    _$$('[data-click-action="skin-delete"]').forEach(button => {
      button.addEventListener('click', event => fcn_deleteSkin(event.currentTarget));
    });
  }

  if (Object.keys(skins.data).length > 2) {
    _$$$('css-upload-form').style.display = 'none';
  } else {
    _$$$('css-upload-form').style.display = '';
  }
}

// Initialize
fcn_renderSkinList();

// Upload
_$('#css-upload-form > input')?.addEventListener('input', event => {
  event.preventDefault();

  const input = _$$$('css-file');
  const file = input.files[0];
  const skins = fcn_getSkins();
  const fingerprint = fcn_getCookie('fcnLoggedIn');

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
  if (!fcn_isUserLoggedIn() || trigger.classList.contains('disabled')) {
    return;
  }

  // Get skins from local storage
  const skins = fcn_getSkins();

  // Toggle button progress
  fcn_toggleInProgress(trigger);

  // Request
  fcn_ajaxPost({
    'action': 'fictioneer_ajax_save_skins',
    'fcn_fast_ajax': 1,
    'skins': JSON.stringify(skins)
  })
  .then(response => {
    if (response.success) {
      fcn_showNotification(response.data.message, 3, 'success');
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
    fcn_toggleInProgress(trigger);
  });
}

_$('[data-click-action="skins-sync-up"]')?.addEventListener('click', event => {
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
  if (!fcn_isUserLoggedIn() || trigger.classList.contains('disabled')) {
    return;
  }

  // Toggle button progress
  fcn_toggleInProgress(trigger);

  // Request
  fcn_ajaxPost({
    'action': 'fictioneer_ajax_get_skins',
    'fcn_fast_ajax': 1
  })
  .then(response => {
    if (response.success) {
      fcn_showNotification(response.data.message, 3, 'success');
      fcn_setSkins(JSON.parse(response.data.skins));
      fcn_renderSkinList();
      fcn_applySkin()
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
    _$('#css-upload-form > input').value = '';
    fcn_toggleInProgress(trigger);
  });
}

_$('[data-click-action="skins-sync-down"]')?.addEventListener('click', event => {
  fcn_downloadSkins(event.currentTarget);
});
