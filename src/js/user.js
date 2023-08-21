// =============================================================================
// SETUP
// =============================================================================

// Initialize
fcn_getProfileImage();

// =============================================================================
// REPLACE PROFILE IMAGE IN ELEMENTS
// =============================================================================

/**
 * Append image with avatar to target element.
 *
 * @since 4.0
 */

function fcn_replaceProfileImage(target, avatar) {
  // Setup
  const old = target.querySelector('.user-icon');

  // Add avatar to view
  if (old) {
    const img = document.createElement('img');

    img.classList.add('user-profile-image');
    img.src = avatar;
    old.remove();
    target.appendChild(img);
  }
}

// =============================================================================
// GET/SET USER AVATAR URL
// =============================================================================

/**
 * Save avatar image in local storage and add it to the document.
 *
 * @since 4.0
 * @see fcn_isValidUrl(url)
 * @see fcn_replaceProfileImage(element, avatar)
 *
 * @param {String} avatar - Avatar URL.
 * @param {Boolean} [save=] - Whether to save in local storage. Default true.
 */

function fcn_setProfileImage(avatar, save = true) {
  // Check if URL is valid
  if (!avatar || !fcn_isValidUrl(avatar)) return;

  // Save in local storage for later
  if (save) localStorage.setItem('fcnProfileAvatar', avatar);

  // replace user icon with avatar
  _$$('a.subscriber-profile')?.forEach(element => {
    fcn_replaceProfileImage(element, avatar);
  });
}

/**
 * Get avatar from local storage or pull it from the server.
 *
 * @since 4.0
 * @see fcn_isValidUrl(url)
 * @see fcn_setProfileImage(avatar)
 * @see fcn_getUserAvatar()
 */

function fcn_getProfileImage() {
  // Look for cached image URL
  let avatar = localStorage.getItem('fcnProfileAvatar');

  // If image URL found but user not logged in, remove it from local storage
  if (!fcn_isLoggedIn) {
    localStorage.removeItem('fcnProfileAvatar');
    return;
  }

  // Check if URL is valid
  if (!fcn_isValidUrl(avatar)) avatar = false;

  if (avatar) {
    // Set profile image if URL has been found
    fcn_setProfileImage(avatar);
  } else {
    // Pull avatar from server
    fcn_getUserAvatar();
  }
}

/**
 * Fetch avatar from server via AJAX.
 *
 * @since 4.0
 * @see fcn_setProfileImage(avatar)
 */

function fcn_getUserAvatar() {
  // Request
  fcn_ajaxGet({
    'action': 'fictioneer_ajax_get_avatar',
    'fcn_fast_ajax': 1
  })
  .then(response => {
    // Check for success
    if (response.success) {
      // Set avatar
      fcn_setProfileImage(response.data.url);
    }
  })
  .catch(() => {
    // You could use a default avatar here
    if (fcn_theRoot.dataset.defaultAvatar) {
      fcn_setProfileImage(fcn_theRoot.dataset.defaultAvatar, false);
    }
  });
}

// =============================================================================
// GET FINGERPRINT
// =============================================================================

var /** @type {Object} */ fcn_fingerprint;

// Initialize
if (fcn_isLoggedIn) fcn_initializeFingerprint();

/**
 * Initialize fingerprint.
 *
 * @since 5.0
 */

function fcn_initializeFingerprint() {
  fcn_fingerprint = fcn_getFingerprint();
  fcn_fetchFingerprint();
}

/**
 * Get fingerprint from local storage or create new JSON.
 *
 * @since 5.0
 * @see fcn_isValidJSONString()
 */

function fcn_getFingerprint() {
  // Get JSON string from local storage
  const f = localStorage.getItem('fcnFingerprint');

  // Parse and return JSON string if valid, otherwise return new JSON
  return (f && fcn_isValidJSONString(f)) ? JSON.parse(f) : { 'lastLoaded': 0, 'fingerprint': false };
}

/**
 * Fetch user fingerprint from database.
 *
 * @description Depending on how aggressive you cache the site, it becomes
 * difficult to show user-specific content. Functions like comment editing
 * may not work at all. Fetching the unique fingerprint periodically and
 * saving it in local storage can get around that. However, you must never
 * take the fingerprint as trustworthy as it is easily copied.
 *
 * @since 5.0
 * @see fcn_isValidJSONString()
 */

function fcn_fetchFingerprint() {
  // Only update from server after some time has passed (e.g. 60 seconds)
  if (fcn_ajaxLimitThreshold < fcn_fingerprint['lastLoaded']) {
    return;
  }

  // Request
  fcn_ajaxGet({
    'action': 'fictioneer_ajax_get_fingerprint',
    'fcn_fast_ajax': 1
  })
  .then((response) => {
    // Fingerprint successfully received?
    if (response.success) {
      // Unpack
      const fingerprint = response.data.fingerprint;

      // Setup
      fcn_fingerprint = { 'fingerprint': fingerprint };

      // Remember last fetch
      fcn_fingerprint['lastLoaded'] = Date.now();

      // Update local storage
      localStorage.setItem('fcnFingerprint', JSON.stringify(fcn_fingerprint));

      // Reveal comment edit buttons
      if (typeof fcn_revealEditButton === 'function') {
        fcn_revealEditButton();
      }

      // Reveal comment delete buttons
      if (typeof fcn_revealDeleteButton === 'function') {
        fcn_revealDeleteButton();
      }
    } else {
      // Something went wrong, possibly logged-out; clear local storage
      localStorage.removeItem('fcnFingerprint');
      fcn_fingerprint = false;
    }
  })
  .catch(() => {
    // Something went extremely wrong; clear local storage
    localStorage.removeItem('fcnFingerprint');
    fcn_fingerprint = false;
  });
}
