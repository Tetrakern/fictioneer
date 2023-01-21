// =============================================================================
// SETUP
// =============================================================================

var /** @type {Number} */ fcn_userRemindersTimeout,
    /** @type {Object} */ fcn_reminders;

// Initialize
if (fcn_isLoggedIn) fcn_initializeReminders();

// =============================================================================
// INITIALIZE
// =============================================================================

/**
 * Initialize Reminders.
 *
 * @since 5.0
 */

function fcn_initializeReminders() {
  fcn_reminders = fcn_getReminders();
  fcn_fetchRemindersFromDatabase();
}

// =============================================================================
// GET REMINDERS FROM LOCAL STORAGE
// =============================================================================

/**
 * Get Reminders from local storage or create new JSON.
 *
 * @since 5.0
 * @see fcn_isValidJSONString()
 */

function fcn_getReminders() {
  // Get JSON string from local storage
  let r = localStorage.getItem('fcnStoryReminders');

  // Parse and return JSON string if valid, otherwise return new JSON
  return (r && fcn_isValidJSONString(r)) ? JSON.parse(r) : { 'lastLoaded': 0, 'data': {} };
}

// =============================================================================
// FETCH REMINDERS FROM DATABASE - AJAX
// =============================================================================

/**
 * Fetch Reminders from database via AJAX.
 *
 * @since 5.0
 * @see fcn_updateRemindersView()
 */

function fcn_fetchRemindersFromDatabase() {
  // Only update from server after some time has passed (e.g. 60 seconds)
  if (fcn_ajaxLimitThreshold < fcn_reminders['lastLoaded']) {
    fcn_updateRemindersView();
    return;
  }

  // Request
  fcn_ajaxGet({
    'action': 'fictioneer_ajax_get_reminders'
  })
  .then((response) => {
    // Check for success
    if (response.success) {
      // Unpack
      let reminders = response.data.reminders;

      // Validate
      reminders = fcn_isValidJSONString(reminders) ? reminders : '{}';
      reminders = JSON.parse(reminders);

      // Setup
      if (typeof reminders === 'object' && reminders.data && Object.keys(reminders.data).length > 0) {
        fcn_reminders = reminders;
      } else {
        fcn_reminders = { 'data': {} };
      }

      // Remember last pull
      fcn_reminders['lastLoaded'] = Date.now();
    }
  })
  .catch(() => {
    // Probably not logged in, clear local data
    localStorage.removeItem('fcnStoryReminders')
    fcn_reminders = false;
  })
  .then(() => {
    // Update view regardless of success
    fcn_updateRemindersView();

    // Clear cached bookshelf content (if any)
    localStorage.removeItem('fcnBookshelfContent');
  });
}

// =============================================================================
// TOGGLE REMINDER - AJAX
// =============================================================================

/**
 * Adds or removes a story ID from the Reminders JSON, then calls for an update
 * of the view to reflect the changes and makes a save request to the database.
 *
 * @since 5.0
 * @see fcn_getReminders()
 * @see fcn_updateRemindersView()
 * @param {Number} storyId - The ID of the story.
 */

function fcn_toggleReminder(storyId) {
  // Check local storage for outside changes
  let currentReminders = fcn_getReminders();

  // Clear cached bookshelf content (if any)
  localStorage.removeItem('fcnBookshelfContent');

  // Re-synchronize if data has diverged
  if (JSON.stringify(fcn_reminders.data[storyId]) !== JSON.stringify(currentReminders.data[storyId])) {
    fcn_reminders = currentReminders;
    fcn_showNotification(__('Reminders re-synchronized.', 'fictioneer'));
    fcn_updateRemindersView();
    return;
  }

  // Refresh object
  fcn_reminders = currentReminders;

  // Add/Remove from current JSON
  if (fcn_reminders.data.hasOwnProperty(storyId)) {
    delete fcn_reminders.data[storyId];
  } else {
    fcn_reminders.data[storyId] = { 'timestamp': Date.now() };
  }

  // Reset AJAX threshold
  fcn_reminders['lastLoaded'] = 0;

  // Update view and local storage
  fcn_updateRemindersView();

  // Payload
  let payload = {
    'action': 'fictioneer_ajax_toggle_reminder',
    'story_id': storyId,
    'set': fcn_reminders.data.hasOwnProperty(storyId)
  }

  // Clear previous timeout (if still pending)
  clearTimeout(fcn_userRemindersTimeout);

  // Update in database; only one request every n seconds
  fcn_userRemindersTimeout = setTimeout(() => {
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
// UPDATE REMINDERS VIEW
// =============================================================================

/**
 * Updates the view to reflect the current Reminders state and saves the JSON
 * to local storage.
 *
 * @since 5.0
 */

function fcn_updateRemindersView() {
  // Not ready yet
  if (!fcn_reminders) return;

  // Update button state
  _$$('.button-read-later').forEach(element => {
    element.classList.toggle(
      '_remembered',
      fcn_reminders.data.hasOwnProperty(element.dataset.storyId)
    );
  });

  // Update icon on cards
  _$$('.card-reminder-icon').forEach(item => {
    if (fcn_reminders.data.hasOwnProperty(item.dataset.storyId)) {
      item.removeAttribute('hidden');
    } else {
      item.setAttribute('hidden', true);
    }
  });

  // Update local storage
  localStorage.setItem('fcnStoryReminders', JSON.stringify(fcn_reminders));
}

// =============================================================================
// EVENT LISTENERS
// =============================================================================

// Listen for clicks on any Read Later buttons
_$$('.button-read-later').forEach(element => {
  element.addEventListener('click', (e) => {
    fcn_toggleReminder(e.currentTarget.dataset.storyId);
  });
});
