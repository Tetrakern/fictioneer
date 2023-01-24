// =============================================================================
// SETUP
// =============================================================================

const /** @const {HTMLElement} */ fcn_desktopFollowList = _$$$('follow-menu-scroll'),
      /** @const {HTMLElement} */ fcn_mobileFollowList = _$$$('mobile-menu-follows-list'),
      /** @const {HTMLElement} */ fcn_followsMenuItem = _$$$('follow-menu-button');

var /** @type {Number} */ fcn_userFollowsTimeout,
    /** @type {Object} */ fcn_follows;

// Initialize
if (fcn_isLoggedIn) fcn_initializeFollows();

// =============================================================================
// INITIALIZE
// =============================================================================

/**
 * Initialize Follows.
 *
 * @since 5.0
 */

function fcn_initializeFollows() {
  fcn_follows = fcn_getFollows();
  fcn_fetchFollowsFromDatabase();
}

// =============================================================================
// GET FOLLOWS FROM LOCAL STORAGE
// =============================================================================

/**
 * Get Follows from local storage or create new JSON.
 *
 * @since 4.3
 * @see fcn_isValidJSONString()
 */

function fcn_getFollows() {
  // Get JSON string from local storage
  let f = localStorage.getItem('fcnStoryFollows');

  // Parse and return JSON string if valid, otherwise return new JSON
  return (f && fcn_isValidJSONString(f)) ? JSON.parse(f) : { 'lastLoaded': 0, 'data': {}, 'seen': Date.now(), 'new': false };
}

// =============================================================================
// FETCH FOLLOWS FROM DATABASE - AJAX
// =============================================================================

/**
 * Fetch Follows from database via AJAX.
 *
 * @since 4.3
 * @see fcn_updateFollowsView()
 */

function fcn_fetchFollowsFromDatabase() {
  // Only update from server after some time has passed (e.g. 60 seconds)
  if (fcn_ajaxLimitThreshold < fcn_follows['lastLoaded']) {
    fcn_updateFollowsView();
    return;
  }

  // Request
  fcn_ajaxGet({
    'action': 'fictioneer_ajax_get_follows'
  })
  .then((response) => {
    // Check for success
    if (response.success) {
      // Unpack
      let follows = response.data.follows;

      // Validate
      follows = fcn_isValidJSONString(follows) ? follows : '{}';
      follows = JSON.parse(follows);

      // Setup
      if (typeof follows === 'object' && follows.data && Object.keys(follows.data).length > 0) {
        fcn_follows = follows;
      } else {
        fcn_follows = { 'data': {}, 'seen': Date.now(), 'new': false };
      }

      // Remember last pull
      fcn_follows['lastLoaded'] = Date.now();
    }
  })
  .catch(() => {
    // Probably not logged in, clear local data
    localStorage.removeItem('fcnStoryFollows')
    fcn_follows = false;
  })
  .then(() => {
    // Update view regardless of success
    fcn_updateFollowsView();

    // Clear cached bookshelf content (if any)
    localStorage.removeItem('fcnBookshelfContent');
  });
}

// =============================================================================
// UPDATE FOLLOWS VIEW
// =============================================================================

/**
 * Updates the view to reflect the current Follows state and saves the JSON
 * to local storage.
 *
 * @since 4.3
 */

function fcn_updateFollowsView() {
  // Not ready yet
  if (!fcn_follows) return;

  // Update button state
  _$$('.button-follow-story').forEach(element => {
    element.classList.toggle(
      '_followed',
      fcn_follows?.data.hasOwnProperty(element.dataset.storyId)
    );
  });

  // Update icon on cards
  _$$('.card-follow-icon').forEach(item => {
    if (fcn_follows?.data.hasOwnProperty(item.dataset.followId)) {
      item.removeAttribute('hidden');
    } else {
      item.setAttribute('hidden', true);
    }
  });

  // Update local storage
  localStorage.setItem('fcnStoryFollows', JSON.stringify(fcn_follows));

  // Set "new" marker if there are new items
  let isNew = parseInt(fcn_follows['new']) > 0;

  _$$('.mark-follows-read, label[for=mobile-menu-frame-follows], .mobile-menu-button').forEach(element => {
    element.classList.toggle('_new', isNew);
    if (isNew > 0) element.dataset.newCount = fcn_follows['new'];
  });
}

// =============================================================================
// TOGGLE FOLLOW - AJAX
// =============================================================================

/**
 * Adds or removes a story ID from the Follows JSON, then calls for an update of
 * the view to reflect the changes and makes a save request to the database.
 *
 * @since 4.3
 * @see fcn_getFollows()
 * @see fcn_updateFollowsView()
 * @param {Number} storyId - The ID of the story.
 */

function fcn_toggleFollow(storyId) {
  // Check local storage for outside changes
  let currentFollows = fcn_getFollows();

  // Clear cached bookshelf content (if any)
  localStorage.removeItem('fcnBookshelfContent');

  // Re-synchronize if data has diverged
  if (JSON.stringify(fcn_follows.data[storyId]) !== JSON.stringify(currentFollows.data[storyId])) {
    fcn_follows = currentFollows;
    fcn_showNotification(__('Follows re-synchronized.', 'fictioneer'));
    fcn_updateFollowsView();
    return;
  }

  // Refresh object
  fcn_follows = currentFollows;

  // Add/Remove from current JSON
  if (fcn_follows.data.hasOwnProperty(storyId)) {
    delete fcn_follows.data[storyId];
  } else {
    fcn_follows.data[storyId] = { 'timestamp': Date.now() };
  }

  // Reset AJAX threshold
  fcn_follows['lastLoaded'] = 0;

  // Update view and local storage
  fcn_updateFollowsView();

  // Payload
  let payload = {
    'action': 'fictioneer_ajax_toggle_follow',
    'story_id': storyId,
    'set': fcn_follows.data.hasOwnProperty(storyId)
  }

  // Clear previous timeout (if still pending)
  clearTimeout(fcn_userFollowsTimeout);

  // Update in database; only one request every n seconds
  fcn_userFollowsTimeout = setTimeout(() => {
    fcn_ajaxPost(payload)
    .then((response) => {
      // Check for failure
      if (response.data.error) {
        fcn_showNotification(response.data.error, 5, 'warning');
      }
    })
    .catch((error) => {
      if (error.status && error.statusText) {
        fcn_showNotification(`${error.status}: ${error.statusText}`, 5, 'warning');
      }
    });
  }, fictioneer_ajax.post_debounce_rate); // Debounce synchronization
}

// =============================================================================
// SET FOLLOWS HTML - AJAX
// =============================================================================

/**
 * Fetch and insert HTML for Follows list via AJAX.
 *
 * @since 4.3
 */

function fcn_setupFollowsHTML() {
  // Abort if already loaded
  if (fcn_followsMenuItem.classList.contains('_loaded')) return;

  // Payload
  let payload = {
    'action': 'fictioneer_ajax_get_follows_notifications'
  }

  // Request
  fcn_ajaxGet(payload)
  .then((response) => {
    // Any Follows HTML retrieved?
    if (response.data.html) {
      fcn_desktopFollowList.innerHTML = response.data.html;
      fcn_mobileFollowList.innerHTML = response.data.html;
    }
  })
  .catch((error) => {
    // Show server error
    if (error.status && error.statusText) {
      fcn_showNotification(`${error.status}: ${error.statusText}`, 5, 'warning');
    }

    // Remove broken lists from view
    fcn_desktopFollowList.remove();
    fcn_mobileFollowList.remove();
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
 * @since 4.3
 */

function fcn_markFollowsRead() {
  // Check if there are new items loaded
  if (
    !fcn_followsMenuItem.classList.contains('_new') ||
    !fcn_followsMenuItem.classList.contains('_loaded')
  ) return;

  // Remove the 'new' markers
  _$$('.mark-follows-read, [for=mobile-menu-frame-follows], .follow-item, .mobile-menu-button').forEach(element => {
    element.classList.remove('_new');
  });

  // Update local storage
  fcn_follows['new'] = 0;
  fcn_follows['lastLoaded'] = 0;
  localStorage.setItem('fcnStoryFollows', JSON.stringify(fcn_follows));

  // Request
  fcn_ajaxPost({
    'action': 'fictioneer_ajax_mark_follows_read'
  })
  .catch((error) => {
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

// Listen for click on the Follows mobile menu item to load Follows
_$('label[for=mobile-menu-frame-follows]')?.addEventListener('click', () => {
  fcn_setupFollowsHTML();
}, { once: true });

// Listen for clicks on any Follow buttons
_$$('.button-follow-story').forEach(element => {
  element.addEventListener('click', (e) => {
    fcn_toggleFollow(e.currentTarget.dataset.storyId);
  });
});

// Listen for clicks on mark read buttons
_$$('.mark-follows-read').forEach(element => {
  element.addEventListener('click', () => {
    fcn_markFollowsRead();
  });
});
