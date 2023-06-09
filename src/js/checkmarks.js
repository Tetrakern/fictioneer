// =============================================================================
// SETUP
// =============================================================================

var /** @type {Object} */ fcn_checkmarks,
    /** @type {Number} */ fcn_userCheckmarksTimeout;

// Initialize
if (fcn_isLoggedIn) fcn_initializeCheckmarks();

// =============================================================================
// INITIALIZE
// =============================================================================

/**
 * Initialize checkmarks.
 *
 * @since 5.0
 */

function fcn_initializeCheckmarks() {
  fcn_checkmarks = fcn_getCheckmarks();
  fcn_fetchCheckmarksFromDatabase();

  // Listen for clicks on checkmarks
  _$$('button.checkmark').forEach(element => {
    element.addEventListener(
      'click',
      (e) => {
        fcn_clickCheckmark(e.currentTarget)
      }
    );
  });
}

// =============================================================================
// GET CHECKMARKS FROM LOCAL STORAGE
// =============================================================================

/**
 * Get checkmarks from local storage.
 *
 * @description Looks for checkmarks in local storage. If none are found, an
 * empty checkmarks JSON is created and returned.
 *
 * @since 4.0
 * @return {JSON|Boolean} Current checkmarks of the user. False if logged out.
 */

function fcn_getCheckmarks() {
  // Look for checkmarks in local storage
  let c = localStorage.getItem('fcnCheckmarks');

  // Check for valid JSON and create new one if not found
  c = (c && fcn_isValidJSONString(c)) ? JSON.parse(c) : { 'lastLoaded': 0, 'data': {}, 'timestamp': Date.now() };

  // Legacy cleanup
  if (!c.data || c['last-loaded']) c = { 'lastLoaded': 0, 'data': {} };

  // Return checkmarks JSON
  return c;
}

// =============================================================================
// TOGGLE CHECKMARK
// =============================================================================

/**
 * Toggle checkmarks for chapters and stories.
 *
 * @since 4.0
 * @see fcn_getCheckmarks()
 * @see fcn_removeItemOnce()
 * @see fcn_updateCheckmarksView()
 * @param {Number} story - ID of the story.
 * @param {String} type - Either 'story' or 'chapter'.
 * @param {Number=} chapter - ID of the chapter.
 * @param {HTMLElement=} source - Source of the event. Default null.
 * @param {String} [mode=toggle] - Force specific change with 'toggle', 'set', or 'unset'.
 */

function fcn_toggleCheckmark(story, type, chapter = null, source = null, mode = 'toggle') {
  // Prevent error in case something went very wrong
  if (!fcn_checkmarks) return;

  // Check local storage for outside changes
  let currentCheckmarks = fcn_getCheckmarks();

  // Clear cached bookshelf content (if any)
  localStorage.removeItem('fcnBookshelfContent');

  // Initialize if story is not yet tracked
  if (!fcn_checkmarks.data.hasOwnProperty(story)) fcn_checkmarks.data[story] = [];
  if (!currentCheckmarks.data.hasOwnProperty(story)) currentCheckmarks.data[story] = [];

  // Re-synchronize if data has diverged
  if (
    mode === 'toggle' &&
    JSON.stringify(currentCheckmarks.data[story]) !== JSON.stringify(fcn_checkmarks.data[story])
  ) {
    fcn_checkmarks = currentCheckmarks;
    fcn_showNotification(__('Checkmarks re-synchronized.', 'fictioneer'));
    fcn_updateCheckmarksView();
    return;
  }

  fcn_checkmarks = currentCheckmarks;

  // Set by reading progress (if not already)
  if (chapter && type === 'progress' && !fcn_checkmarks.data[story].includes(chapter)) {
    // Add chapter checkmark to JSON
    fcn_checkmarks.data[story].push(chapter);
  }

  // Set/Unset chapter checkmark
  if (chapter && type === 'chapter') {
    if ((fcn_checkmarks.data[story].includes(chapter) || mode === 'unset') && mode !== 'set') {
      // Remove chapter checkmark from JSON
      fcn_removeItemOnce(fcn_checkmarks.data[story], chapter);

      // Update chapter checkmark from HTML
      if (source) {
        source.classList.remove('marked');
        source.ariaChecked = false;
      }

      // Not complete anymore
      fcn_removeItemOnce(fcn_checkmarks.data[story], story);
      let completeCheckmark = _$('button[data-type=story]');

      if (completeCheckmark) {
        completeCheckmark.classList.remove('marked');
        completeCheckmark.ariaChecked = false;
      }
    } else {
      // Add chapter checkmark to JSON
      fcn_checkmarks.data[story].push(chapter);

      // Update chapter checkmark in HTML
      if (source) {
        source.classList.add('marked');
        source.ariaChecked = true;
      }
    }
  }

  // Flip all checkmarks if necessary
  if (type === 'story') {
    // Unset all?
    let unsetAll = (fcn_checkmarks.data[story].includes(story) || mode === 'unset') && mode !== 'set';

    // Always remove all from JSON
    fcn_checkmarks.data[story] = [];

    if (!unsetAll) {
      // (Re-)add checkmarks to JSON based on buttons
      _$$('button.checkmark').forEach(item => {
        fcn_checkmarks.data[story].push(parseInt(item.dataset.id));
      });

      // Make sure story has been added if completed
      if (!fcn_checkmarks.data[story].includes(story)) fcn_checkmarks.data[story].push(story);
    }
  }

  // Make sure IDs are unique
  fcn_checkmarks.data[story] = fcn_checkmarks.data[story].filter(
    (value, index, self) => self.indexOf(value) == index
  );

  // Reset AJAX threshold
  fcn_checkmarks['lastLoaded'] = 0;

  // Update view and local storage
  fcn_updateCheckmarksView();

  // Payload
  let payload = {
    'action': 'fictioneer_ajax_set_checkmark',
    'story_id': story,
    'update': fcn_checkmarks.data[story].join(' ')
  }

  // Clear previous timeout (if still pending)
  clearTimeout(fcn_userCheckmarksTimeout);

  // Update in database; only one request every n seconds
  fcn_userCheckmarksTimeout = setTimeout(() => {
    fcn_ajaxPost(payload)
    .then((response) => {
      // Check for failure
      if (response.data.error) {
        fcn_showNotification(response.data.error, 3, 'warning');
      }
    })
    .catch((error) => {
      if (error.status && error.statusText) {
        fcn_showNotification(`${error.status}: ${error.statusText}`, 5, 'warning');
      }
    });
  }, fictioneer_ajax.post_debounce_rate); // Debounce synchronization
}

/**
 * Evaluate click on a checkmark.
 *
 * @since 4.0
 * @see fcn_toggleCheckmark()
 * @param {HTMLElement} source - Source of the event.
 */

function fcn_clickCheckmark(source) {
  fcn_toggleCheckmark(
    parseInt(source.dataset.storyId),
    source.dataset.type,
    parseInt(source.dataset.id),
    source
  );
}

// =============================================================================
// UPDATE CHECKMARKS VIEW
// =============================================================================

/**
 * Update the view with the current checkmarks state.
 *
 * @since 4.0
 */

function fcn_updateCheckmarksView() {
  // Not ready yet
  if (!fcn_checkmarks) return;

  // Setup
  let storyId = parseInt(fcn_inlineStorage.storyId),
      included = fcn_checkmarks.data.hasOwnProperty(storyId),
      completed = included && fcn_checkmarks.data[storyId].includes(storyId);

  // Initialize if story is not yet tracked
  if (!included) fcn_checkmarks.data[storyId] = [];

  // Completed story?
  if (completed) {
    // Add all checkmarks (if missing)
    _$$('button.checkmark').forEach(item => {
      let checkId = parseInt(item.dataset.id);
      if (!fcn_checkmarks.data[storyId].includes(checkId)) fcn_checkmarks.data[storyId].push(checkId);
    });
  }

  // Add ribbon to thumbnail if complete and present
  _$$$('ribbon-read')?.classList.toggle('hidden', !completed);

  // Update checkmarks on story pages
  _$$('button.checkmark')?.forEach(item => {
    let checkStoryId = parseInt(item.dataset.storyId);
    if (!fcn_checkmarks.data.hasOwnProperty(checkStoryId)) return;
    let checked = fcn_checkmarks.data[checkStoryId].includes(parseInt(item.dataset.id));
    item.classList.toggle('marked', checked);
    item.ariaChecked = checked;
  });

  // Update icon and buttons on cards
  _$$('.card')?.forEach(item => {
    let cardStoryId = parseInt(item.dataset.storyId),
        force = fcn_checkmarks.data.hasOwnProperty(cardStoryId) &&
                (
                  fcn_checkmarks.data[cardStoryId].includes(parseInt(item.dataset.checkId)) ||
                  fcn_checkmarks.data[cardStoryId].includes(cardStoryId)
                );
    item.classList.toggle('has-checkmark', force);
  });

  // Update local storage
  localStorage.setItem('fcnCheckmarks', JSON.stringify(fcn_checkmarks));
}

// =============================================================================
// FETCH CHECKMARKS FROM DATABASE - AJAX
// =============================================================================

/**
 * Retrieve an user's checkmarks from the database.
 *
 * @since 4.0
 */

function fcn_fetchCheckmarksFromDatabase() {
  // Do not proceed if...
  if (!fcn_isLoggedIn) return;

  // Only update from server after some time has passed (e.g. 60 seconds)
  if (fcn_ajaxLimitThreshold < fcn_checkmarks['lastLoaded']) {
    fcn_updateCheckmarksView();
    return;
  }

  // Request
  fcn_ajaxGet({
    'action': 'fictioneer_ajax_get_checkmarks'
  })
  .then((response) => {
    // Check for success
    if (response.success) {
      // Unpack
      let checkmarks = response.data.checkmarks;

      // Validate
      checkmarks = fcn_isValidJSONString(checkmarks) ? checkmarks : '{}';
      checkmarks = JSON.parse(checkmarks);

      // Setup
      if (typeof checkmarks === 'object' && checkmarks.data && Object.keys(checkmarks.data).length > 0) {
        fcn_checkmarks = checkmarks;
      } else {
        fcn_checkmarks = { 'data': {}, 'timestamp': Date.now() };
      }

      // Remember last pull
      fcn_checkmarks['lastLoaded'] = Date.now();
    }
  })
  .catch(() => {
    // Probably not logged in, clear local data
    localStorage.removeItem('fcnCheckmarks')
    fcn_checkmarks = false;
  })
  .then(() => {
    // Update view regardless of success
    fcn_updateCheckmarksView();

    // Clear cached bookshelf content (if any)
    localStorage.removeItem('fcnBookshelfContent');
  });
}
