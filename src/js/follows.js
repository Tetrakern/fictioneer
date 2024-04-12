// =============================================================================
// SETUP
// =============================================================================

const /** @const {HTMLElement} */ fcn_followsMenuItem = _$$$('follow-menu-button');

var /** @type {Number} */ fcn_userFollowsTimeout;
var /** @type {Object} */ fcn_follows;

// Initialize
document.addEventListener('fcnUserDataReady', event => {
  fcn_initializeFollows(event);
});

// =============================================================================
// INITIALIZE
// =============================================================================

/**
 * Initialize Follows.
 *
 * @since 5.0.0
 * @param {Event} event - The fcnUserDataReady event.
 */

function fcn_initializeFollows(event) {
  // Unpack
  const follows = event.detail.data.follows;

  // Validate
  if (follows === false) {
    return;
  }

  // Fix empty array that should be an object
  if (Array.isArray(follows.data) && follows.data.length === 0) {
    follows.data = {};
  }

  // Set follows instance
  fcn_follows = follows;

  // Update view
  fcn_updateFollowsView();

  // Clear cached bookshelf content (if any)
  localStorage.removeItem('fcnBookshelfContent');
}

// =============================================================================
// TOGGLE FOLLOW - AJAX
// =============================================================================

/**
 * Adds or removes a story ID from the Follows JSON, then calls for an update of
 * the view to reflect the changes and makes a save request to the database.
 *
 * @since 4.3.0
 * @see fcn_updateFollowsView()
 * @param {Number} storyId - The ID of the story.
 */

function fcn_toggleFollow(storyId) {
  // Get current data
  const currentUserData = fcn_getUserData();

  // Prevent error in case something went very wrong
  if (!fcn_follows || !currentUserData.follows) {
    return;
  }

  // Clear cached bookshelf content (if any)
  localStorage.removeItem('fcnBookshelfContent');

  // Re-synchronize if data has diverged
  if (JSON.stringify(fcn_follows.data[storyId]) !== JSON.stringify(currentUserData.follows.data[storyId])) {
    fcn_follows = currentUserData.follows;
    fcn_showNotification(fictioneer_tl.notification.followsResynchronized);
    fcn_updateFollowsView();

    return;
  }

  // Set/Unset follow
  if (fcn_follows.data.hasOwnProperty(storyId)) {
    delete fcn_follows.data[storyId];
  } else {
    fcn_follows.data[storyId] = { 'story_id': parseInt(storyId), 'timestamp': Date.now() };
  }

  // Update local storage
  currentUserData.follows.data[storyId] = fcn_follows.data[storyId];
  currentUserData.lastLoaded = 0;
  fcn_setUserData(currentUserData);

  // Update view
  fcn_updateFollowsView();

  // Clear previous timeout (if still pending)
  clearTimeout(fcn_userFollowsTimeout);

  // Update in database; only one request every n seconds
  fcn_userFollowsTimeout = setTimeout(() => {
    fcn_ajaxPost({
      'action': 'fictioneer_ajax_toggle_follow',
      'fcn_fast_ajax': 1,
      'story_id': storyId,
      'set': fcn_follows.data.hasOwnProperty(storyId)
    })
    .then(response => {
      if (response.data.error) {
        fcn_showNotification(response.data.error, 5, 'warning');
      }
    })
    .catch(error => {
      if (error.status === 429) {
        fcn_showNotification(fictioneer_tl.notification.slowDown, 3, 'warning');
      } else if (error.status && error.statusText) {
        fcn_showNotification(`${error.status}: ${error.statusText}`, 5, 'warning');
      }
    });
  }, fictioneer_ajax.post_debounce_rate); // Debounce synchronization
}

// =============================================================================
// UPDATE FOLLOWS VIEW
// =============================================================================

/**
 * Updates the view with the current Follows state.
 *
 * @since 4.3.0
 */

function fcn_updateFollowsView() {
  // Get current data
  const currentUserData = fcn_getUserData();

  if (!fcn_follows || !currentUserData.follows) {
    return;
  }

  // Update button state
  _$$('.button-follow-story').forEach(element => {
    element.classList.toggle(
      '_followed',
      fcn_follows?.data.hasOwnProperty(element.dataset.storyId)
    );
  });

  // Update icon and buttons on cards
  _$$('.card').forEach(element => {
    element.classList.toggle(
      'has-follow',
      fcn_follows?.data.hasOwnProperty(element.dataset.storyId)
    );
  });

  // Set "new" marker if there are new items
  const isNew = parseInt(fcn_follows['new']) > 0;

  _$$('.mark-follows-read, .follows-alert-number, .mobile-menu-button').forEach(element => {
    element.classList.toggle('_new', isNew);

    if (isNew > 0) {
      element.dataset.newCount = fcn_follows['new'];
    }
  });
}

// =============================================================================
// SET FOLLOWS HTML - AJAX
// =============================================================================

/**
 * Fetch and insert HTML for Follows list via AJAX.
 *
 * @since 4.3.0
 */

function fcn_setupFollowsHTML() {
  // Abort if already loaded
  if (fcn_followsMenuItem.classList.contains('_loaded')) {
    return;
  }

  // Request
  fcn_ajaxGet({
    'action': 'fictioneer_ajax_get_follows_notifications',
    'fcn_fast_ajax': 1
  })
  .then(response => {
    // Any Follows HTML retrieved?
    if (response.data.html) {
      const menuTarget = _$$$('follow-menu-scroll');

      if (menuTarget) {
        menuTarget.innerHTML = response.data.html;
      }

      const mobileMenuTarget = _$$$('mobile-menu-follows-list');

      if (mobileMenuTarget) {
        mobileMenuTarget.innerHTML = response.data.html;
      }

      // Use opportunity to fix broken login state
      if (fcn_getUserData().loggedIn === false) {
        fcn_prepareLogin();
        fcn_fetchUserData();
      }
    }
  })
  .catch(error => {
    // Show server error
    if (error.status === 429) {
      fcn_showNotification(fictioneer_tl.notification.slowDown, 3, 'warning');
    } else if (error.status && error.statusText) {
      fcn_showNotification(`${error.status}: ${error.statusText}`, 5, 'warning');
    }

    // Remove broken lists from view
    _$$$('follow-menu-scroll')?.remove();
    _$$$('mobile-menu-follows-list')?.remove();
  })
  .then(() => {
    // Regardless of success
    fcn_followsMenuItem.classList.add('_loaded');
  });
}

// =============================================================================
// MARK FOLLOWS NOTIFICATIONS AS READ - AJAX
// =============================================================================

/**
 * Mark all Follows as read via Ajax.
 *
 * @since 4.3.0
 */

function fcn_markFollowsRead() {
  // Check if there are new items loaded
  if (
    !fcn_followsMenuItem.classList.contains('_new') ||
    !fcn_followsMenuItem.classList.contains('_loaded')
  ) {
    return;
  }

  // Remove the 'new' markers
  _$$('.mark-follows-read, .follows-alert-number, .follow-item, .mobile-menu-button').forEach(element => {
    element.classList.remove('_new');
  });

  // Update local storage
  const currentUserData = fcn_getUserData();
  currentUserData.new = 0;
  currentUserData.lastLoaded = 0;
  fcn_setUserData(currentUserData);

  // Request
  fcn_ajaxPost({
    'action': 'fictioneer_ajax_mark_follows_read',
    'fcn_fast_ajax': 1
  })
  .catch(error => {
    if (error.status && error.statusText) {
      fcn_showNotification(`${error.status}: ${error.statusText}`, 5, 'warning');
    }
  });
}

// =============================================================================
// EVENT LISTENERS
// =============================================================================

// Listen for hover over the Follows navigation item to load Follows
fcn_followsMenuItem?.addEventListener('mouseover', () => {
  fcn_setupFollowsHTML();
}, { once: true });

// Listen to focus on the Follows navigation item to load Follows
fcn_followsMenuItem?.addEventListener('focus', () => {
  fcn_setupFollowsHTML();
}, { once: true });

// Append Follows when Follows mobile menu frame is opened, once
_$('.mobile-menu__frame-button[data-frame-target="follows"]')?.addEventListener('click', () => {
  fcn_setupFollowsHTML();
}, { once: true });

// Listen for clicks on any Follow buttons
_$$('.button-follow-story').forEach(element => {
  element.addEventListener('click', event => {
    fcn_toggleFollow(event.currentTarget.dataset.storyId);
  });
});

// Listen for clicks on mark read buttons
_$$('.mark-follows-read').forEach(element => {
  element.addEventListener('click', () => {
    fcn_markFollowsRead();
  });
});
