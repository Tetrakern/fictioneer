// =============================================================================
// SETUP
// =============================================================================

var /** @type {Object} */ fcn_checkmarks,
    /** @type {Number} */ fcn_userCheckmarksTimeout;

// Initialize
document.addEventListener('fcnUserDataReady', event => {
  fcn_initializeCheckmarks(event);
});

// =============================================================================
// INITIALIZE
// =============================================================================

/**
 * Initialize checkmarks.
 *
 * @since 5.0.0
 * @param {Event} event - The fcnUserDataReady event.
 */

function fcn_initializeCheckmarks(event) {
  // Unpack
  const checkmarks = event.detail.data.checkmarks;

  // Validate
  if (checkmarks === false) {
    return;
  }

  // Fix empty array that should be an object
  if (Array.isArray(checkmarks.data) && checkmarks.data.length === 0) {
    checkmarks.data = {};
  }

  // Set checkmarks instance
  fcn_checkmarks = checkmarks;

  // Update view
  fcn_updateCheckmarksView();

  // Clear cached bookshelf content (if any)
  localStorage.removeItem('fcnBookshelfContent');

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
// TOGGLE CHECKMARK
// =============================================================================

/**
 * Toggle checkmarks for chapters and stories.
 *
 * @since 4.0.0
 * @see fcn_removeItemOnce()
 * @see fcn_updateCheckmarksView()
 * @param {Number} storyId - ID of the story.
 * @param {String} type - Either 'story' or 'chapter'.
 * @param {Number=} chapter - ID of the chapter.
 * @param {HTMLElement=} source - Source of the event. Default null.
 * @param {String} [mode=toggle] - Force specific change with 'toggle', 'set', or 'unset'.
 */

function fcn_toggleCheckmark(storyId, type, chapter = null, source = null, mode = 'toggle') {
  // Get current data
  const currentUserData = fcn_getUserData();

  // Prevent error in case something went very wrong
  if (!fcn_checkmarks || !currentUserData.checkmarks) {
    return;
  }

  // Clear cached bookshelf content (if any)
  localStorage.removeItem('fcnBookshelfContent');

  // Re-synchronize if data has diverged from other tab/window
  if (
    mode === 'toggle' &&
    JSON.stringify(fcn_checkmarks.data[storyId]) !== JSON.stringify(currentUserData.checkmarks.data[storyId])
  ) {
    fcn_checkmarks = currentUserData.checkmarks;
    fcn_showNotification(__('Checkmarks re-synchronized.', 'fictioneer'));
    fcn_updateCheckmarksView();

    return;
  }

  // Initialize if story is not yet tracked
  if (!fcn_checkmarks.data.hasOwnProperty(storyId)) {
    fcn_checkmarks.data[storyId] = [];
  }

  if (!currentUserData.checkmarks.data.hasOwnProperty(storyId)) {
    currentUserData.checkmarks.data[storyId] = [];
  }

  // Set by reading progress (if not already)
  if (chapter && type === 'progress' && !fcn_checkmarks.data[storyId].includes(chapter)) {
    // Add chapter checkmark to JSON
    fcn_checkmarks.data[storyId].push(chapter);
  }

  // Set/Unset chapter checkmark
  if (chapter && type === 'chapter') {
    if ((fcn_checkmarks.data[storyId].includes(chapter) || mode === 'unset') && mode !== 'set') {
      // Remove chapter checkmark from JSON
      fcn_removeItemOnce(fcn_checkmarks.data[storyId], chapter);

      // Update chapter checkmark in HTML
      if (source) {
        source.classList.remove('marked');
        source.setAttribute('aria-checked', false);
      }

      // Not complete anymore
      fcn_removeItemOnce(fcn_checkmarks.data[storyId], storyId);

      const completeCheckmark = _$('button[data-type="story"]');

      if (completeCheckmark) {
        completeCheckmark.classList.remove('marked');
        completeCheckmark.setAttribute('aria-checked', false);
      }
    } else {
      // Add chapter checkmark to JSON
      fcn_checkmarks.data[storyId].push(chapter);

      // Update chapter checkmark in HTML
      if (source) {
        source.classList.add('marked');
        source.setAttribute('aria-checked', true);
      }
    }
  }

  // Flip all checkmarks if necessary
  if (type === 'story') {
    // Unset all?
    const unsetAll = (fcn_checkmarks.data[storyId].includes(storyId) || mode === 'unset') && mode !== 'set';

    // Always remove all from JSON
    fcn_checkmarks.data[storyId] = [];

    if (!unsetAll) {
      // (Re-)add checkmarks to JSON based on buttons
      _$$('button.checkmark').forEach(item => {
        fcn_checkmarks.data[storyId].push(parseInt(item.dataset.id));
      });

      // Make sure story has been added if completed
      if (!fcn_checkmarks.data[storyId].includes(storyId)) {
        fcn_checkmarks.data[storyId].push(storyId);
      }
    }
  }

  // Make sure IDs are unique
  fcn_checkmarks.data[storyId] = fcn_checkmarks.data[storyId].filter(
    (value, index, self) => self.indexOf(value) == index
  );

  // Update local storage
  currentUserData.checkmarks.data[storyId] = fcn_checkmarks.data[storyId];
  currentUserData.lastLoaded = 0;
  fcn_setUserData(currentUserData);

  // Update view
  fcn_updateCheckmarksView();

  // Clear previous timeout (if still pending)
  clearTimeout(fcn_userCheckmarksTimeout);

  // Update in database; only one request every n seconds
  fcn_userCheckmarksTimeout = setTimeout(() => {
    fcn_updateCheckmarks(storyId, fcn_checkmarks.data[storyId]);
  }, fictioneer_ajax.post_debounce_rate); // Debounce synchronization
}

/**
 * Evaluate click on a checkmark.
 *
 * @since 4.0.0
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
// UPDATE CHECKMARKS IN DATABASE
// =============================================================================

/**
 * Update checkmarks in database (POST)
 *
 * @since 5.7.0
 * @param {Number} storyId - ID of the story.
 * @param {Array=} checkmarks - The story's checkmarks.
 */

function fcn_updateCheckmarks(storyId, checkmarks = null) {
  // Prepare update
  checkmarks = checkmarks ? checkmarks : fcn_getUserData().checkmarks.data[storyId];

  // Request
  fcn_ajaxPost({
    'action': 'fictioneer_ajax_set_checkmark',
    'fcn_fast_ajax': 1,
    'story_id': storyId,
    'update': checkmarks.join(' ')
  })
  .then(response => {
    if (response.data.error) {
      fcn_showNotification(response.data.error, 3, 'warning');
    }
  })
  .catch(error => {
    if (error.status && error.statusText) {
      fcn_showNotification(`${error.status}: ${error.statusText}`, 5, 'warning');
    }
  });
}

// =============================================================================
// UPDATE CHECKMARKS IN VIEW
// =============================================================================

/**
 * Update the view with the current checkmarks state.
 *
 * @since 4.0.0
 */

function fcn_updateCheckmarksView() {
  // Get current data
  const currentUserData = fcn_getUserData(),
        checkmarks = currentUserData.checkmarks;

  if (!checkmarks) {
    return;
  }

  // Completed story pages
  const storyId = parseInt(fcn_inlineStorage.storyId);

  if (storyId) {
    const completed = checkmarks.data.hasOwnProperty(storyId) &&
      checkmarks.data[storyId].includes(storyId);

    // Add missing checkmarks
    if (completed) {
      let updated = false;

      _$$('button.checkmark').forEach(item => {
        const checkId = parseInt(item.dataset.id);

        if (!checkmarks.data[storyId].includes(checkId)) {
          checkmarks.data[storyId].push(checkId);
          updated = true;
        }
      });

      if (updated) {
        currentUserData.checkmarks = checkmarks; // Global
        fcn_setUserData(currentUserData);
        fcn_updateCheckmarks(storyId, checkmarks.data[storyId]);
      }
    }

    // Ribbon
    _$$$('ribbon-read')?.classList.toggle('hidden', !completed);
  }

  // Update checkmarks on story pages
  _$$('button.checkmark').forEach(item => {
    const checkStoryId = parseInt(item.dataset.storyId);

    if (!checkmarks.data.hasOwnProperty(checkStoryId)) {
      return;
    }

    const checked = checkmarks.data[checkStoryId].includes(parseInt(item.dataset.id));

    item.classList.toggle('marked', checked);
    item.setAttribute('aria-checked', checked);
  });

  // Update icon and buttons on cards
  _$$('.card').forEach(item => {
    const cardStoryId = parseInt(item.dataset.storyId),
          force = checkmarks.data.hasOwnProperty(cardStoryId) &&
            (
              checkmarks.data[cardStoryId].includes(parseInt(item.dataset.checkId)) ||
              checkmarks.data[cardStoryId].includes(cardStoryId)
            );
    item.classList.toggle('has-checkmark', force);
  });
}
