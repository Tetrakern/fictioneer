// =============================================================================
// SETUP
// =============================================================================

var /** @type {Number} */ fcn_storyCommentPage = 1;

// Initialize
var /** @type {Object} */ fcn_storySettings = fcn_getStorySettings();

// =============================================================================
// LOAD USER SETTINGS
// =============================================================================

/**
 * Get story settings JSON from local storage or create new one.
 *
 * @since 5.0.6
 * @see fcn_parseJSON()
 * @see fcn_defaultStorySettings()
 * @see fcn_setStorySettings();
 * @return {Object} The story settings.
 */

function fcn_getStorySettings() {
  // Get settings from local storage or use defaults
  let settings = fcn_parseJSON(localStorage.getItem('fcnStorySettings')) ?? fcn_defaultStorySettings();

  // Timestamp allows to force resets after script updates (may annoy users)
  if (!settings.hasOwnProperty('timestamp') || settings['timestamp'] < 1674770712849) {
    settings = fcn_defaultStorySettings();
    settings['timestamp'] = Date.now();
  }

  // Update local storage and return
  fcn_setStorySettings(settings);
  return settings;
}

/**
 * Returns default story settings.
 *
 * @since 5.0.6
 * @return {Object} The formatting settings.
 */

function fcn_defaultStorySettings() {
  return {
    'view': 'list',
    'order': 'asc',
    'timestamp': 1674770712849 // Used to force resets on script updates
  };
}

/**
 * Set the story settings object and save to local storage.
 *
 * @since 5.0.6
 * @param {Object} settings - The formatting settings.
 */

function fcn_setStorySettings(settings) {
  // Simple validation
  if (typeof settings !== 'object') {
    return;
  }

  // Keep global updated
  fcn_storySettings = settings;

  // Update local storage
  localStorage.setItem('fcnStorySettings', JSON.stringify(settings));
}

/**
 * Apply story settings
 *
 * @since 5.0.6
 */

function fcn_applyStorySettings() {
  // Simple validation
  if (typeof fcn_storySettings !== 'object') {
    return;
  }

  // List view
  _$$('[data-view]').forEach(element => {
    element.dataset.view = fcn_storySettings.view == 'grid' ? 'grid' : 'list';
  });

  // List order
  _$$('[data-order]').forEach(element => {
    element.dataset.order = fcn_storySettings.order == 'desc' ? 'desc' : 'asc';
  });
}

// Initialize
fcn_applyStorySettings();

// =============================================================================
// ORDER & VIEW
// =============================================================================

// Event listener for to toggle chapter order
_$$$('button-toggle-chapter-order')?.addEventListener(
  'click',
  event => {
    fcn_storySettings['order'] = event.currentTarget.dataset.order === 'asc' ? 'desc' : 'asc';
    fcn_setStorySettings(fcn_storySettings);
    fcn_applyStorySettings();
  }
);

// Event listener for to toggle chapter view
_$$$('button-toggle-chapter-view')?.addEventListener(
  'click',
  event => {
    fcn_storySettings['view'] = event.currentTarget.dataset.view === 'list' ? 'grid' : 'list';
    fcn_setStorySettings(fcn_storySettings);
    fcn_applyStorySettings();
  }
);

// =============================================================================
// CHAPTER FOLDING
// =============================================================================

_$$('.chapter-group__folding-toggle').forEach(element => {
  element.addEventListener(
    'click',
    event => {
      const group = event.currentTarget.closest('.chapter-group[data-folded]');

      if (group) {
        group.dataset.folded = group.dataset.folded == 'true' ? 'false' : 'true';
      }
    }
  );
});

// =============================================================================
// STORY TABS
// =============================================================================

/**
 * Toggles the active story tab.
 *
 * @since 5.4.0
 *
 * @param {HTMLElement} target - The clicked tab element.
 */

function fcn_toggleStoryTab(target) {
  // Clear previous tab
  _$$('.story__article ._current').forEach(item => {
    item.classList.remove('_current');
  });

  // Set new tab
  _$$(`[data-finder="${target.dataset.target}"]`).forEach(element => {
    element.classList.add('_current');
  });

  _$$$('tabs').dataset.current = target.dataset.target;
  target.classList.add('_current');
}

// Listen for clicks on tabs...
_$$('.tabs__item').forEach(element => {
  element.addEventListener(
    'click',
    event => {
      fcn_toggleStoryTab(event.currentTarget);
    }
  );
});

// =============================================================================
// LOAD STORY COMMENTS
// =============================================================================

/**
 * Fetch and insert the HTML for the next batch of comments on the story page.
 *
 * @since 4.0.0
 */

function fcn_loadStoryComments() {
  // Setup
  let errorNote;

  // Prepare view
  _$('.load-more-list-item').remove();
  _$('.comments-loading-placeholder').classList.remove('hidden');

  // REST request
  fcn_ajaxGet(
    {
      'post_id': fcn_inlineStorage.postId,
      'page': fcn_storyCommentPage
    },
    'get_story_comments'
  )
  .then(response => {
    // Check for success
    if (response.success) {
      _$('.fictioneer-comments__list > ul').innerHTML += response.data.html;
      fcn_storyCommentPage++;
    } else if (response.data?.error) {
      errorNote = fcn_buildErrorNotice(response.data.error);
    }
  })
  .catch(error => {
    errorNote = fcn_buildErrorNotice(error);
  })
  .then(() => {
    // Remove progress state
    _$('.comments-loading-placeholder').remove();

    // Add error if any
    if (errorNote) {
      _$('.fictioneer-comments__list > ul').appendChild(errorNote);
    }
  });
}

// Listen for clicks to load more comments...
_$('.comment-section')?.addEventListener('click', event => {
  if (event.target?.classList.contains('load-more-comments-button')) {
    fcn_loadStoryComments();
  }
});

// =============================================================================
// EPUB DOWNLOAD
// =============================================================================

/**
 * Initiates the download of an EPUB file.
 *
 * @since 5.7.2
 * @param {HTMLElement} link - The link that triggered the download.
 * @param {number} times - The number of times the function has been called recursively.
 */

function fcn_startEpubDownload(link, times = 0) {
  if (times > 3) {
    link.classList.remove('ajax-in-progress')
    return;
  }

  fcn_ajaxGet({
    'action': 'fictioneer_ajax_download_epub',
    'story_id': link.dataset.storyId
  })
  .then(response => {
    if (response.success) {
      window.location.href = link.href;
      setTimeout(() => { link.classList.remove('ajax-in-progress') }, 2000);
    } else {
      setTimeout(() => { fcn_startEpubDownload(link, times + 1) }, 2000);
    }
  })
  .catch(error => {
    link.classList.remove('ajax-in-progress');

    if (error.status && error.statusText) {
      fcn_showNotification(`${error.status}: ${error.statusText}`, 5, 'warning');
    }
  });
}

_$$('[data-action="download-epub"]').forEach(element => {
  element.addEventListener('click', event => {
    // Stop link
    event.preventDefault();

    // Abort if...
    if (event.currentTarget.classList.contains('ajax-in-progress')) {
      return;
    }

    // Disable button
    event.currentTarget.classList.add('ajax-in-progress');

    // Request
    fcn_startEpubDownload(event.currentTarget);
  });
});
