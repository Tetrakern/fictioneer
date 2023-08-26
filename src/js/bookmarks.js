// =============================================================================
// BOOKMARKS
// =============================================================================

const /** @const {HTMLElement[]} */ fcn_jumpToBookmarkButtons = _$$('.button--bookmark'),
      /** @const {HTMLElement} */ fcn_mobileBookmarkJump = _$$$('mobile-menu-bookmark-jump'),
      /** @const {HTMLElement} */ fcn_mobileBookmarkList = _$('.mobile-menu__bookmark-list'),
      /** @const {HTMLElement} */ fcn_mobileBookmarkTemplate = _$('#mobile-bookmark-template'),
      /** @const {HTMLElement} */ fcn_bookmarksSmallCardBlock = _$('.bookmarks-block'),
      /** @const {HTMLElement} */ fcn_bookmarksSmallCardTemplate = _$('.bookmark-small-card-template');

var /** @type {Object} */ fcn_bookmarks,
    /** @type {Number} */ fcn_userBookmarksTimeout;

// Initialize
fcn_initializeLocalBookmarks();

document.addEventListener('fcnUserDataReady', event => {
  fcn_initializeUserBookmarks(event);
});

// =============================================================================
// INITIALIZE
// =============================================================================

/**
 * Initialize local bookmarks.
 *
 * @description Looks for bookmarks in local storage, both for guests and logged-in
 * users. If none are found, an empty bookmarks JSON is created and set.
 *
 * @since 4.0
 * @see fcn_isValidJSONString()
 */

function fcn_initializeLocalBookmarks() {
  // Look for bookmarks in local storage
  fcn_bookmarks = fcn_getBookmarks();

  // Always update bookmarks in storage
  fcn_setBookmarks(fcn_bookmarks, true);

  // Update view
  fcn_updateBookmarksView();
}

/**
 * Initialize Bookmarks for logged-in users.
 *
 * @since 5.7.0
 * @param {Event} event - The fcnUserDataReady event.
 */

function fcn_initializeUserBookmarks(event) {
  // Always update bookmarks in storage
  fcn_setBookmarks(JSON.parse(event.detail.data.bookmarks), true);

  // Update view
  fcn_updateBookmarksView();
}

// =============================================================================
// GET BOOKMARKS
// =============================================================================

/**
 * Initialize bookmarks from local storage.
 *
 * @since 5.7.0
 * @see fcn_isValidJSONString()
 * @return {Object} The bookmarks JSON.
 */

function fcn_getBookmarks() {
  // Look for bookmarks in local storage
  fcn_bookmarks = localStorage.getItem('fcnChapterBookmarks');

  // Check for valid JSON and create new one if not found
  return (fcn_bookmarks && fcn_isValidJSONString(fcn_bookmarks)) ?
    JSON.parse(fcn_bookmarks) : { 'data': {} };
}

// =============================================================================
// SET BOOKMARKS
// =============================================================================

/**
 * Save bookmarks to local storage (and database).
 *
 * @description Saves the bookmarks JSON to local storage, overriding any previous
 * instance. If not set to silent, an update to the database is called as well,
 * clearing the timeout of any previous enqueued update to avoid unnecessary
 * requests. Since the bookmarks are always saved as complete collection, yet
 * unfinished requests can be cancelled without loss of data.
 *
 * @since 4.0
 * @see fcn_saveUserBookmarks()
 * @param {Object} value - The bookmarks JSON to be set.
 * @param {Boolean} [silent=false] - Whether or not to update the database.
 */

function fcn_setBookmarks(value, silent = false) {
  // Make sure this is a JSON object
  if (typeof value !== 'object') {
    return;
  }

  // Keep global updated
  fcn_bookmarks = value;
  localStorage.setItem('fcnChapterBookmarks', JSON.stringify(value));

  // Keep user data updated as well
  if (fcn_isLoggedIn) {
    const currentUserData = fcn_getUserData();

    if (currentUserData) {
      currentUserData.bookmarks = JSON.stringify(value);
      fcn_setUserData(currentUserData);
    }
  }

  // Do not save to database if silent update
  if (silent) {
    return;
  }

  // Update database for user
  fcn_saveUserBookmarks(JSON.stringify(value));
}

// =============================================================================
// UPDATE BOOKMARKS VIEW
// =============================================================================

/**
 * Updates the view with the current Bookmarks state.
 *
 * @since 5.7.0
 */

function fcn_updateBookmarksView() {
  // Abort if bookmarks are not set
  if (!fcn_bookmarks) {
    return;
  }

  // Setup
  const stats = _$('.profile-bookmarks-stats'),
        count = Object.keys(fcn_bookmarks.data).length;

  // Insert bookmarks count on user profile page
  if (stats) {
    stats.innerHTML = stats.innerHTML.replace('%s', count);
  }

  // Bookmark icons
  if (count > 0) {
    _$$('.icon-menu-bookmarks').forEach(element => {
      element.classList.remove('hidden');
    });
  }

  // Render bookmark cards if already logged-in
  fcn_showBookmarkCards();

  // Chapter bookmark
  fcn_showChapterBookmark();
}

// =============================================================================
// SAVE USER BOOKMARKS
// =============================================================================

/**
 * Save bookmarks JSON to the database via AJAX.
 *
 * @description Saves the bookmarks JSON to the database if an user is logged in,
 * otherwise the function aborts to avoid unnecessary requests. This can only
 * happen once every n seconds, as set by the timeout interval, to avoid further
 * unnecessary requests in case someone triggers multiple updates.
 *
 * @since 4.0
 * @param {String} value - The stringified bookmarks JSON to be saved.
 */

function fcn_saveUserBookmarks(value) {
  // Do not proceed if not logged in
  if (!fcn_isLoggedIn) {
    return;
  }

  // Clear previous timeout (if still pending)
  clearTimeout(fcn_userBookmarksTimeout);

  // Only one save request every n seconds
  fcn_userBookmarksTimeout = setTimeout(() => {
    fcn_ajaxPost({
      'action': 'fictioneer_ajax_save_bookmarks',
      'fcn_fast_ajax': 1,
      'bookmarks': value
    })
    .then(response => {
      // Check for failure
      if (response.data.error) {
        fcn_showNotification(response.data.error, 3, 'warning');
      }
    })
    .catch(error => {
      if (error.status && error.statusText) {
        fcn_showNotification(`${error.status}: ${error.statusText}`, 3, 'warning');
      }
    });
  }, fictioneer_ajax.post_debounce_rate); // Debounce synchronization
}

// =============================================================================
// TOGGLE BOOKMARKS
// =============================================================================

/**
 * Toggle bookmark on chapter pages.
 *
 * @description Toggles a chapter bookmark, either saving all relevant data in
 * the bookmarks JSON or removing them. Updates any available buttons, lists,
 * and calls to persist the updated JSON.
 *
 * @since 4.0
 * @see fcn_getBookmarks()
 * @see fcn_removeBookmark()
 * @see fcn_offset()
 * @see fcn_setMobileMenuBookmarks()
 * @see fcn_setBookmarks()
 * @param {Number} id - The ID of the chapter.
 * @param {String} [color=none] - The color of the bookmark.
 */

function fcn_toggleBookmark(id, color = 'none') {
  // Synchronize with local storage again
  fcn_bookmarks = fcn_getBookmarks();

  // Get article node with chapter data
  const chapter = _$('.chapter__article'),
        currentBookmark = _$('.current-bookmark');

  // Check whether an article has been found or abort
  if (!chapter) {
    return;
  }

  // Look for existing bookmark...
  const b = fcn_bookmarks.data[chapter.id];

  // Add, update, or remove bookmark...
  if (b && b['paragraph-id'] == id && currentBookmark) {
    if (color != 'none' && color != b['color']) {
      // --- Update bookmark color ---------------------------------------------
      _$('.current-bookmark').dataset.bookmarkColor = color;
      b['color'] = color;
    } else {
      // --- Remove bookmark ---------------------------------------------------
      fcn_removeBookmark(chapter.id);
    }
  } else {
    // --- Add new bookmark ----------------------------------------------------

    // Maximum of 50 bookmarks
    if ( Object.keys(fcn_bookmarks.data).length >= 50 ) {
      // Remove oldest
      fcn_removeBookmark(Object.keys(fcn_bookmarks.data)[0]);
    }

    // Setup
    const p = _$(`[data-paragraph-id="${id}"]`),
          fcn_chapterBookmarkData = _$$$('chapter-bookmark-data').dataset;

    // Add data node (chapter-id: {}) to bookmarks JSON
    fcn_bookmarks.data[chapter.id] = {
      'paragraph-id': id,
      'progress': (fcn_offset(p).top - fcn_offset(p.parentElement).top) * 100 / p.parentElement.clientHeight,
      'date': new Date(),
      'color': color,
      'chapter': fcn_chapterBookmarkData.title.trim(),
      'link': fcn_chapterBookmarkData.link,
      'thumb': fcn_chapterBookmarkData.thumb,
      'image': fcn_chapterBookmarkData.image,
      'story': fcn_chapterBookmarkData.storyTitle.trim(),
      'content': p.querySelector('span').innerHTML.substring(0, 128) + '…'
    };

    // Reveal jump buttons
    fcn_jumpToBookmarkButtons.forEach(element => {
      element.classList.remove('hidden');
    });

    fcn_mobileBookmarkJump?.removeAttribute('hidden');

    // Remove current-bookmark class from previous paragraph (if any)
    currentBookmark?.classList.remove('current-bookmark');

    // Add new current-bookmark class to paragraph
    p.classList.add('current-bookmark');
    p.setAttribute('data-bookmark-color', color);
  }

  // Reset HTML in mobile menu
  fcn_setMobileMenuBookmarks();

  // Save bookmarks
  fcn_setBookmarks(fcn_bookmarks);
}

// =============================================================================
// SHOW CHAPTER BOOKMARK
// =============================================================================

/**
 * Shows the bookmark on a chapter page.
 *
 * @description If the current page is a chapter and has a bookmark, the
 * 'current-bookmark' class is added to the bookmarked paragraph to show
 * the colored bookmark line and the jump buttons are revealed.
 *
 * @since 4.0
 */

function fcn_showChapterBookmark() {
  // Cleanup (in case of error)
  _$('.current-bookmark')?.classList.remove('current-bookmark');

  // Get current chapter node
  const chapter = _$('.chapter__article');

  // Abort if not a chapter or no bookmark set for chapter
  if (!chapter || !fcn_bookmarks.data[chapter.id]) {
    return;
  }

  // Collect necessary data to show bookmark in chapter
  const id = fcn_bookmarks.data[chapter.id]['paragraph-id'],
        p = _$(`[data-paragraph-id="${id}"]`),
        color = fcn_bookmarks.data[chapter.id]['color'] ?? 'none';

  // If bookmarked paragraph has been found...
  if (id && p) {
    // Reveal bookmark jump buttons in chapter
    fcn_jumpToBookmarkButtons.forEach(element => {
      element.classList.remove('hidden');
    });

    // Reveal bookmark jump button in mobile menu
    fcn_mobileBookmarkJump?.removeAttribute('hidden');

    // Add bookmark line and color
    p.classList.add('current-bookmark');
    p.setAttribute('data-bookmark-color', color);
  }
}

// =============================================================================
// SET MOBILE MENU BOOKMARKS
// =============================================================================

/**
 * Set up bookmarks frame in mobile menu.
 *
 * @description Clears any previous HTML and rebuilds the bookmarks list for the
 * mobile menu (usually when a new bookmark is added or the menu panel opened for
 * the first time per page load).
 *
 * @since 4.0
 * @see fcn_bookmarkDeleteHandler()
 */

function fcn_setMobileMenuBookmarks() {
  // Clear target container of previous entries
  fcn_mobileBookmarkList.innerHTML = '';

  // Check for bookmarks...
  if (Object.keys(fcn_bookmarks.data).length > 0) {
    // Bookmarks found! Loop all!
    Object.entries(fcn_bookmarks.data).forEach((b) => {
      // Clone template node
      const clone = fcn_mobileBookmarkTemplate.content.cloneNode(true);

      // Fill cloned node with data
      clone.querySelector('.mobile-menu__bookmark').classList.add(`bookmark-${b[0]}`);
      clone.querySelector('.mobile-menu__bookmark').dataset.color = b[1]['color'];
      clone.querySelector('.mobile-menu__bookmark-progress > div > div').style.width = `${b[1].progress.toFixed(1)}%`;
      clone.querySelector('.mobile-menu__bookmark a').href = `${b[1].link}#paragraph-${b[1]['paragraph-id']}`;
      clone.querySelector('.mobile-menu__bookmark a span').innerText = b[1].chapter;
      clone.querySelector('.mobile-menu-bookmark-delete-button').setAttribute('data-bookmark-id', `${b[0]}`);

      // Append cloned node
      fcn_mobileBookmarkList.appendChild(clone);
    });

    // Register events for delete buttons
    fcn_bookmarkDeleteHandler(_$$('.mobile-menu-bookmark-delete-button'));
  } else {
    // No bookmarks found!
    const node = document.createElement('li');

    // Create text node with notice
    node.classList.add('no-bookmarks');
    node.appendChild(document.createTextNode(fcn_mobileBookmarkList.dataset.empty));

    // Append node
    fcn_mobileBookmarkList.appendChild(node);
  }
}

// =============================================================================
// BOOKMARK CARDS
// =============================================================================

/**
 * Renders bookmark cards within the bookmarks block if provided.
 *
 * @description If the current page has a bookmarks card block and template
 * (via shortcode), bookmarks are rendered as cards up to the count specified
 * in the block as data-attribute (or all for -1).
 *
 * @since 4.0
 * @see fcn_bookmarkDeleteHandler()
 */

function fcn_showBookmarkCards() {
  // Check whether bookmark cards need to be rendered
  if (
    !fcn_bookmarks ||
    !fcn_bookmarksSmallCardBlock ||
    !fcn_bookmarksSmallCardTemplate ||
    Object.keys(fcn_bookmarks.data).length < 1 ||
    _$('.bookmark-card')
  ) {
    return;
  }

  // Make block visible
  fcn_bookmarksSmallCardBlock.classList.remove('hidden');
  _$('.bookmarks-block__no-bookmarks')?.remove();

  // Make related elements (if any) visible
  _$$('.show-if-bookmarks').forEach(element => {
    element.classList.remove('hidden');
  });

  // Max number of rendered bookmarks (-1 for all)
  let count = fcn_bookmarksSmallCardBlock.dataset.count;

  // Loop bookmarks
  Object.entries(fcn_bookmarks.data).forEach(b => {
    // Limit rendered bookmarks
    if (count > -1 && count-- < 1) {
      return;
    }

    // Clone template and get data from JSON
    const clone = fcn_bookmarksSmallCardTemplate.content.cloneNode(true),
          date = new Date(b[1].date);

    // Only render an image tag if an src URL has been provided
    if (b[1].image !== '') {
      clone.querySelector('.bookmark-card__image').href = b[1].image;
      clone.querySelector('.bookmark-card__image img').src = `${b[1].thumb}`;
    } else {
      clone.querySelector('.bookmark-card__image').remove();
    }

    // Insert bookmark data into cloned node...
    clone.querySelector('.bookmark-card__excerpt').innerHTML += b[1].content;
    clone.querySelector('.bookmark-card').classList.add(`bookmark-${b[0]}`);
    clone.querySelector('.bookmark-card').dataset.color = b[1]['color'];
    clone.querySelector('.bookmark-card__title > a').href = `${b[1].link}#paragraph-${b[1]['paragraph-id']}`;
    clone.querySelector('.bookmark-card__title > a').innerText = b[1].chapter;
    clone.querySelector('.bookmark-card__percentage').innerText = `${b[1].progress.toFixed(1)} %`;
    clone.querySelector('.bookmark-card__progress').style.width = `${b[1].progress.toFixed(1)}%`;
    clone.querySelector('time').innerText = `${date.toLocaleDateString('en-US', { year: '2-digit', month: 'short', day: 'numeric' })}`;
    clone.querySelector('.button-delete-bookmark').setAttribute('data-bookmark-id', `${b[0]}`);

    // Append cloned node to bookmarks card block
    fcn_bookmarksSmallCardBlock.querySelector('ul').appendChild(clone);
  });

  // Register event handlers for deletion buttons
  fcn_bookmarkDeleteHandler(_$$('.button-delete-bookmark'));
}

// =============================================================================
// BOOKMARK DELETION
// =============================================================================

/**
 * Register event handler for bookmark delete button(s).
 *
 * @since 4.0
 * @see fcn_removeBookmark()
 * @see fcn_setBookmarks()
 * @param {HTMLElement|HTMLElement[]} targets - One or more delete buttons.
 */

function fcn_bookmarkDeleteHandler(targets) {
  // Make sure to have an iterable collection for convenience
  (typeof targets === 'object' ? targets : [targets]).forEach((item) => {
    // Listen for click on delete button per item
    item.addEventListener('click', (e) => {
      // Remove bookmark
      fcn_removeBookmark(e.currentTarget.dataset.bookmarkId);

      // Update bookmarks
      fcn_setBookmarks(fcn_bookmarks);

      // Hide bookmarks block if emptied
      if (Object.keys(fcn_bookmarks.data).length < 1) {
        _$('.bookmarks-block')?.classList.add('hidden');
        _$$('.show-if-bookmarks').forEach(element => {
          element.classList.add('hidden');
        });
      }
    });
  });
}

/**
 * Remove bookmark from JSON and view.
 *
 * @since 4.0
 * @param {Number} id - ID of the bookmark (chapter ID).
 */

function fcn_removeBookmark(id) {
  // Setup
  const chapter = _$('.chapter__article'),
        currentBookmark = _$('.current-bookmark');

  // Remove bookmark from JSON
  delete fcn_bookmarks.data[id];

  // If on bookmark's chapter page...
  if (chapter && chapter.id == id) {
    // Hide jump buttons
    fcn_jumpToBookmarkButtons.forEach(element => {
      element.classList.add('hidden');
    });

    fcn_mobileBookmarkJump?.setAttribute('hidden', true);

    // Remove current-bookmark class from paragraph
    if (currentBookmark) {
      currentBookmark.classList.remove('current-bookmark');
      currentBookmark.removeAttribute('data-bookmark-color');
    }
  }

  // Remove any other bookmark instances (e.g. mobile menu)
  _$$(`.bookmark-${id}`)?.forEach(element => {
    element.remove();
  });
}

// =============================================================================
// BOOKMARKS EVENT LISTENERS
// =============================================================================

fcn_jumpToBookmarkButtons.forEach(button => {
  button.addEventListener(
    'click',
    () => {
      fcn_scrollTo(_$(`[data-paragraph-id="${fcn_bookmarks.data[_$('article').id]['paragraph-id']}"]`));
    }
  );
});
