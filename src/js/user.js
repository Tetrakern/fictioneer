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
  if (!avatar || !fcn_isValidUrl(avatar)) {
    return;
  }

  // Save in local storage for later
  if (save) {
    localStorage.setItem('fcnProfileAvatar', avatar);
  }

  // Replace user icon with avatar
  _$$('a.subscriber-profile')?.forEach(element => {
    fcn_replaceProfileImage(element, avatar);
  });

  // Use opportunity to fix broken login state
  if (fcn_getUserData().loggedIn === false) {
    fcn_prepareLogin();
  }
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
  if (!fcn_isValidUrl(avatar)) {
    avatar = false;
  }

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

document.addEventListener('DOMContentLoaded', () => {
  if (fcn_isLoggedIn && !fcn_theRoot.dataset.ajaxAuth) {
    fcn_getProfileImage();
  }
});

// =============================================================================
// FETCH RELEVANT USER DATA
// =============================================================================

/**
 * Get user data from web storage or initialize if missing.
 *
 * @since 5.7.0
 * @return {Object} The user data JSON.
 */

function fcn_getUserData() {
  // Get JSON string from local storage
  const data = fcn_parseJSON(localStorage.getItem('fcnUserData'));

  // Parse and return JSON string if valid, otherwise return new JSON
  return data ??
    {
      'lastLoaded': 0,
      'timestamp': 0,
      'loggedIn': 'pending',
      'follows': false,
      'reminders': false,
      'checkmarks': false,
      'bookmarks': {},
      'fingerprint': false
    };
}

/**
 * Update user data in web storage.
 *
 * @since 5.7.0
 * @param {Object} data - The user data JSON.
 */

function fcn_setUserData(data) {
  localStorage.setItem('fcnUserData', JSON.stringify(data));
}

/**
 * Fetch user data via AJAX or from web storage, then fire events.
 *
 * @since 5.7.0
 */

function fcn_fetchUserData() {
  let currentUserData = fcn_getUserData();

  // Fix broken login state
  if (fcn_isLoggedIn && currentUserData.loggedIn === false) {
    fcn_prepareLogin(); // Remove outdated data from web storage
    currentUserData = fcn_getUserData();
  }

  // Only update from server after some time has passed (e.g. 60 seconds)
  if (fcn_ajaxLimitThreshold < currentUserData['lastLoaded'] || currentUserData.loggedIn === false) {

    if (currentUserData.loggedIn) {
      // User logged-in
      const eventReady = new CustomEvent('fcnUserDataReady', {
        detail: { data: currentUserData, time: new Date() },
        bubbles: false,
        cancelable: true
      });

      document.dispatchEvent(eventReady);
    } else {
      // User logged-out
      const eventFailure = new CustomEvent('fcnUserDataFailed', {
        detail: { response: currentUserData, time: new Date() },
        bubbles: true,
        cancelable: false
      });

      document.dispatchEvent(eventFailure);
    }

    return;
  }

  // Request
  fcn_ajaxGet({
    'action': 'fictioneer_ajax_get_user_data',
    'fcn_fast_ajax': 1
  })
  .then(response => {
    if (response.success) {
      let updatedUserData = fcn_getUserData();

      // Update local user data
      updatedUserData = response.data;
      updatedUserData['lastLoaded'] = Date.now();
      fcn_setUserData(updatedUserData);

      // Prepare custom event
      const eventReady = new CustomEvent('fcnUserDataReady', {
        detail: { data: response.data, time: new Date() },
        bubbles: true,
        cancelable: false
      });

      // Fire event
      document.dispatchEvent(eventReady);
    } else {
      const updatedUserData = fcn_getUserData();

      // Update guest data
      updatedUserData['lastLoaded'] = Date.now();
      updatedUserData['loggedIn'] = false;
      fcn_setUserData(updatedUserData);

      // Prepare custom event
      const eventFailure = new CustomEvent('fcnUserDataFailed', {
        detail: { response: response, time: new Date() },
        bubbles: true,
        cancelable: false
      });

      // Fire event
      document.dispatchEvent(eventFailure);
    }
  })
  .catch(error => {
    // Something went extremely wrong; clear local storage
    localStorage.removeItem('fcnUserData');

    // Prepare custom event
    const eventError = new CustomEvent('fcnUserDataError', {
      detail: { error: error, time: new Date() },
      bubbles: true,
      cancelable: false
    });

    // Fire event
    document.dispatchEvent(eventError);
  });
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
  if (fcn_isLoggedIn && !fcn_theRoot.dataset.ajaxAuth) {
    fcn_fetchUserData();
  }
});
