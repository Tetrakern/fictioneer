// =============================================================================
// BOOKMARKS
// =============================================================================

const /** @const {HTMLElement[]} */ fcn_jumpToBookmarkButtons = _$$('.button--bookmark'),
      /** @const {HTMLElement} */ fcn_mobileBookmarkJump = _$$$('mobile-menu-bookmark-jump'),
      /** @const {HTMLElement} */ fcn_mobileBookmarkList = _$('.mobile-menu__bookmark-list'),
      /** @const {HTMLElement} */ fcn_mobileBookmarkTemplate = _$('#mobile-bookmark-template'),
      /** @const {HTMLElement} */ fcn_bookmarksSmallCardBlock = _$('.bookmarks-block'),
      /** @const {HTMLElement} */ fcn_bookmarksSmallCardTemplate = _$('.bookmark-small-card-template');

var /** @type {Object} */ fcn_bookmarks = fcn_getBookmarks(),
    /** @type {Number} */ fcn_userBookmarksTimeout;

// Initialize
fcn_getBookmarksForUser();
fcn_showChapterBookmark();

// =============================================================================
// SAVE BOOKMARKS FOR USER
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

function fcn_saveBookmarksForUser(value) {
  // Do not proceed if not logged in
  if (!fcn_isLoggedIn) return;

  // Reset AJAX threshold
  fcn_bookmarks['lastLoaded'] = 0;

  // Save to local storage
  localStorage.setItem('fcnChapterBookmarks', JSON.stringify(fcn_bookmarks));

  // Payload
  let payload = {
    'action': 'fictioneer_ajax_save_bookmarks',
    'bookmarks': value
  }

  // Clear previous timeout (if still pending)
  clearTimeout(fcn_userBookmarksTimeout);

  // Only one save request every n seconds
  fcn_userBookmarksTimeout = setTimeout(() => {
    fcn_ajaxPost(payload)
    .then((response) => {
      // Check for failure
      if (response.data.error) {
        fcn_showNotification(response.data.error, 3, 'warning');
      }
    })
    .catch((error) => {
      if (error.status && error.statusText) {
        fcn_showNotification(`${error.status}: ${error.statusText}`, 3, 'warning');
      }
    });
  }, fictioneer_ajax.post_debounce_rate); // Debounce synchronization
}

// =============================================================================
// GET BOOKMARKS FOR USER
// =============================================================================

/**
 * Fetch bookmarks JSON from database via AJAX and set it.
 *
 * @description Check whether to pull bookmarks for a logged-in user from the
 * database. This can only happen once every n seconds, as set by the difference
 * between the fcn_ajaxLimitThreshold and 'lastLoaded' node, in order to reduce
 * the number of requests if someone is just browsing.
 *
 * @since 4.0
 * @see fcn_setBookmarks()
 * @see fcn_showChapterBookmark()
 * @see fcn_updateBookmarkCards()
 */

function fcn_getBookmarksForUser() {
  // Do not proceed if not logged in
  if (!fcn_isLoggedIn) return;

  // Only update from server after some time has passed (e.g. 60 seconds)
  if (fcn_ajaxLimitThreshold < fcn_bookmarks['lastLoaded']) {
    if (Object.keys(fcn_bookmarks.data).length > 0) fcn_updateBookmarkCards();
    return;
  }

  // Request
  fcn_ajaxGet({
    'action': 'fictioneer_ajax_get_bookmarks'
  })
  .then((response) => {
    // Check for success
    if (response.success) {
      // Unpack
      let bookmarks = response.data.bookmarks;

      // Validate
      bookmarks = fcn_isValidJSONString(bookmarks) ? bookmarks : '{}';
      bookmarks = JSON.parse(bookmarks);

      if (Object.keys(bookmarks).length > 2) bookmarks = {};

      // Setup
      if (typeof bookmarks === 'object' && bookmarks.data) {
        bookmarks['lastLoaded'] = Date.now();
        fcn_setBookmarks(bookmarks, true);
        fcn_showChapterBookmark();
      }
    }

    // Regardless of success
    fcn_updateBookmarkCards();
  });
}

// =============================================================================
// GET BOOKMARKS
// =============================================================================

/**
 * Get bookmarks JSON from local storage or create new one.
 *
 * @description Looks for bookmarks in local storage, both for guests and logged-in
 * users. If none are found, an empty bookmarks JSON is created, set, and returned.
 *
 * @since 4.0
 * @see fcn_isValidJSONString()
 * @return {Object} The bookmarks JSON.
 */

function fcn_getBookmarks() {
  // Look for bookmarks in local storage
  let b = localStorage.getItem('fcnChapterBookmarks');

  // Check for valid JSON and create new one if not found
  b = (b && fcn_isValidJSONString(b)) ? JSON.parse(b) : { 'lastLoaded': 0, 'data': {} };

  // Always update bookmarks in storage
  fcn_setBookmarks(b, true);

  // Render bookmark cards if no AJAX request following
  if (!fcn_isLoggedIn) fcn_updateBookmarkCards();

  // Insert bookmarks count on user profile page
  let stats = _$('.profile-bookmarks-stats');
  if (stats) stats.innerHTML = stats.innerHTML.replace('%s', Object.keys(b.data).length);

  if (Object.keys(b.data).length > 0) {
    _$$('.icon-menu-bookmarks').forEach(element => {
      element.classList.remove('hidden');
    });
  }

  // Return bookmarks JSON
  return b;
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
 * @see fcn_saveBookmarksForUser()
 * @param {Object} value - The bookmarks JSON to be set.
 * @param {Boolean} [silent=false] - Whether or not to update the database.
 */

function fcn_setBookmarks(value, silent = false) {
  // Make sure this is a JSON object
  if (typeof value !== 'object') return;

  // Keep global updated
  fcn_bookmarks = value;

  // Save to local storage
  localStorage.setItem('fcnChapterBookmarks', JSON.stringify(value));

  // Do not save to database if silent update
  if (silent) return;

  // Update database for user; cancel any enqueued update via the timeout
  window.clearTimeout(fcn_userBookmarksTimeout);
  fcn_saveBookmarksForUser(JSON.stringify(value));
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
  let chapter = _$('.chapter__article'),
      currentBookmark = _$('.current-bookmark');

  // Check whether an article has been found or abort
  if (!chapter) return;

  // Look for existing bookmark...
  let b = fcn_bookmarks.data[chapter.id];

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
    let p = _$(`[data-paragraph-id="${id}"]`),
        offset = fcn_offset(p),
        parentOffset = fcn_offset(p.parentElement),
        progress = (offset.top - parentOffset.top) * 100 / p.parentElement.clientHeight,
        fcn_chapterBookmarkData = _$$$('chapter-bookmark-data').dataset;

    // Add data node (chapter-id: {}) to bookmarks JSON
    fcn_bookmarks.data[chapter.id] = {
      'paragraph-id': id,
      'progress': progress,
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
  let chapter = _$('.chapter__article');

  // Abort if not a chapter or no bookmark set for chapter
  if (!chapter || !fcn_bookmarks.data[chapter.id]) return;

  // Collect necessary data to show bookmark in chapter
  let id = fcn_bookmarks.data[chapter.id]['paragraph-id'],
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
      let clone = fcn_mobileBookmarkTemplate.content.cloneNode(true);

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
    let node = document.createElement('li');

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

function fcn_updateBookmarkCards() {
  // Check whether there are bookmark cards to render
  if (
    !fcn_bookmarks ||
    !fcn_bookmarksSmallCardBlock ||
    !fcn_bookmarksSmallCardTemplate ||
    Object.keys(fcn_bookmarks.data).length < 1
  ) return;

  // Make block visible
  fcn_bookmarksSmallCardBlock.classList.remove('hidden');
  _$('.bookmarks-block__no-bookmarks')?.remove();

  // Make related elements (if any) visible
  _$$('.show-if-bookmarks').forEach(element => {
    element.classList.remove('hidden');
  });

  // Max number of rendered bookmarks (-1 for all)
  let count = _$('.bookmarks-block').dataset.count;

  // Loop bookmarks
  Object.entries(fcn_bookmarks.data).forEach(b => {
    // Limit rendered bookmarks
    if (count > -1 && count-- < 1) return;

    // Clone template and get data from JSON
    let clone = fcn_bookmarksSmallCardTemplate.content.cloneNode(true),
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
      // Setup
      let id = e.currentTarget.dataset.bookmarkId;

      // Remove bookmark
      fcn_removeBookmark(id);

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
  let chapter = _$('.chapter__article'),
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
    e => {
      let target = _$(`[data-paragraph-id="${fcn_bookmarks.data[_$('article').id]['paragraph-id']}"]`),
          position = target.getBoundingClientRect().top,
          offset = position + window.pageYOffset - 64;

      window.scrollTo({ top: offset, behavior: 'smooth' });
    }
  );
});
