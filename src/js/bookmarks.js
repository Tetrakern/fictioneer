// =============================================================================
// BOOKMARKS
// =============================================================================

const /** @const {HTMLElement[]} */ fcn_jumpToBookmarkButtons = _$$('.button--bookmark');
const /** @const {HTMLElement} */ fcn_mobileBookmarkJump = _$$$('mobile-menu-bookmark-jump');
const /** @const {HTMLElement} */ fcn_mobileBookmarkList = _$('.mobile-menu__bookmark-list');
const /** @const {HTMLElement} */ fcn_bookmarksSmallCardBlock = _$('.bookmarks-block');
const /** @const {HTMLElement} */ fcn_bookmarksSmallCardTemplate = _$('.bookmark-small-card-template');

var /** @type {Object} */ fcn_bookmarks;
var /** @type {Number} */ fcn_userBookmarksTimeout;

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
 * @since 4.0.0
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
 * Initialize bookmarks for logged-in users.
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
 * Get bookmarks from local storage.
 *
 * @since 5.7.0
 * @see fcn_parseJSON()
 * @return {Object} The bookmarks JSON.
 */

function fcn_getBookmarks() {
  let bookmarks = fcn_parseJSON(localStorage.getItem('fcnChapterBookmarks')) ?? { 'data': {} };

  // Fix empty array that should be an object
  if (Array.isArray(bookmarks.data) && bookmarks.data.length === 0) {
    bookmarks.data = {};
  }

  // Validate and sanitize
  bookmarks = fcn_fixBookmarks(bookmarks);

  // Last check and return
  return (!bookmarks || Object.keys(bookmarks).length < 1) ? { 'data': {} } : bookmarks;
}

// =============================================================================
// VALIDATE & SANITIZE BOOKMARKS
// =============================================================================

/**
 * Fixes bookmarks to only contain valid data.
 *
 * @since 5.7.4
 * @param {JSON} bookmarks - The bookmarks JSON to be fixed.
 * @return {JSON} The fixed bookmarks JSON.
 */

function fcn_fixBookmarks(bookmarks) {
  const fixedData = {};

  for (const key in bookmarks.data) {
    if (key.startsWith('ch-')) {
      const node = fcn_fixBookmarksNode(bookmarks.data[key]);

      if (node) {
        fixedData[key] = node;
      }
    }
  }

  return { data: fixedData };
}

/**
 * Fixes a bookmark child node.
 *
 * @since 5.7.4
 * @param {JSON} node - The child node to be fixed.
 * @return {JSON} The fixed child node.
 */

function fcn_fixBookmarksNode(node) {
  const fixedNode = {};
  const structure = {
    'paragraph-id': '',
    'progress': 0.0,
    'date': '',
    'color': '',
    'chapter': '',
    'link': '',
    'thumb': '',
    'image': '',
    'story': '',
    'content': ''
  };

  // Check structure and only leave allowed nodes
  for (const key in structure) {
    if (typeof node[key] === typeof structure[key]) {
      fixedNode[key] = node[key];
    } else {
      return null;
    }
  }

  // Check date
  const date = new Date(fixedNode['date']);

  if (!date || Object.prototype.toString.call(date) !== "[object Date]" || isNaN(date)) {
    fixedNode['date'] = (new Date()).toISOString(); // This happens anyway when stringified
  }

  // Check progress
  if (typeof fixedNode['progress'] !== 'number' || fixedNode['progress'] < 0) {
    fixedNode['progress'] = 0.0;
  }

  // Done
  return fixedNode;
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
 * @since 4.0.0
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
  fcn_saveUserBookmarks(value);
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
  if (!fcn_bookmarks || !fcn_bookmarks.data) {
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
 * @since 4.0.0
 * @param {JSON} bookmarks - The bookmarks JSON to be saved.
 */

function fcn_saveUserBookmarks(bookmarks) {
  // Do not proceed if not logged in
  if (!fcn_isLoggedIn) {
    return;
  }

  // Clear previous timeout (if still pending)
  clearTimeout(fcn_userBookmarksTimeout);

  // Validate and sanitize
  bookmarks = fcn_fixBookmarks(bookmarks);

  // Only one save request every n seconds
  fcn_userBookmarksTimeout = setTimeout(() => {
    fcn_ajaxPost({
      'action': 'fictioneer_ajax_save_bookmarks',
      'fcn_fast_ajax': 1,
      'bookmarks': JSON.stringify(bookmarks)
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
 * @since 4.0.0
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
  const chapter = _$('.chapter__article');
  const currentBookmark = _$('.current-bookmark');

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
    if (Object.keys(fcn_bookmarks.data).length >= 50) {
      // Remove oldest
      fcn_removeBookmark(Object.keys(fcn_bookmarks.data)[0]);
    }

    // Setup
    const p = _$(`[data-paragraph-id="${id}"]`);
    const fcn_chapterBookmarkData = _$$$('chapter-bookmark-data').dataset;

    // Add data node (chapter-id: {}) to bookmarks JSON
    fcn_bookmarks.data[chapter.id] = {
      'paragraph-id': id,
      'progress': (fcn_offset(p).top - fcn_offset(p.parentElement).top) * 100 / p.parentElement.clientHeight,
      'date': (new Date()).toISOString(), // This happens anyway when stringified
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
 * @since 4.0.0
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
  const id = fcn_bookmarks.data[chapter.id]['paragraph-id'];
  const p = _$(`[data-paragraph-id="${id}"]`);
  const color = fcn_bookmarks.data[chapter.id]['color'] ?? 'none';

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
 * @since 4.0.0
 * @since 5.9.4 - Refactored with DocumentFragment.
 * @see fcn_bookmarkDeleteHandler()
 */

function fcn_setMobileMenuBookmarks() {
  // Clear target container of previous entries
  fcn_mobileBookmarkList.innerHTML = '';

  const bookmarks = Object.entries(fcn_bookmarks.data);
  const template = _$('#mobile-bookmark-template');

  if (bookmarks.length > 0) {
    // Use fragment to collect nodes
    const fragment = document.createDocumentFragment();

    // Append bookmarks to fragment
    bookmarks.forEach(([id, { color, progress, link, chapter, 'paragraph-id': paragraphId }]) => {
      const clone = template.content.cloneNode(true);
      const bookmarkElement = clone.querySelector('.mobile-menu__bookmark');

      bookmarkElement.classList.add(`bookmark-${id}`);
      bookmarkElement.dataset.color = color;
      clone.querySelector('.mobile-menu__bookmark-progress > div > div').style.width = `${progress.toFixed(1)}%`;
      clone.querySelector('.mobile-menu__bookmark a').href = `${link}#paragraph-${paragraphId}`;
      clone.querySelector('.mobile-menu__bookmark a span').innerText = chapter;
      clone.querySelector('.mobile-menu-bookmark-delete-button').setAttribute('data-bookmark-id', id);

      fragment.appendChild(clone);
    });

    // Append fragment to DOM
    fcn_mobileBookmarkList.appendChild(fragment);

    // Register events for delete buttons
    fcn_bookmarkDeleteHandler(_$$('.mobile-menu-bookmark-delete-button'));
  } else {
    // No bookmarks found!
    const node = document.createElement('li');

    // Create text node with notice
    node.classList.add('no-bookmarks');
    node.textContent = fcn_mobileBookmarkList.dataset.empty;

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
 * @since 4.0.0
 * @since 5.9.4 - Refactored with DocumentFragment.
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

  // Make elements visible (if any)
  fcn_bookmarksSmallCardBlock.classList.remove('hidden');
  _$('.bookmarks-block__no-bookmarks')?.remove();
  _$$('.show-if-bookmarks').forEach(element => element.classList.remove('hidden'));

  // Max number of rendered bookmarks (-1 for all)
  let count = parseInt(fcn_bookmarksSmallCardBlock.dataset.count);

  // Use fragment to collect nodes
  const fragment = document.createDocumentFragment();

  // Sort bookmarks by date, newest to oldest
  const sorted = Object.entries(fcn_bookmarks.data).sort((a, b) => new Date(b[1].date) - new Date(a[1].date));

  // Append bookmarks to fragment (if any)
  sorted.forEach(([id, { color, progress, link, chapter, 'paragraph-id': paragraphId, date, image, thumb, content }]) => {
    // Limit rendered bookmarks
    if (count == 0) {
      return;
    }

    count--;

    // Clone template and get data from JSON
    const clone = fcn_bookmarksSmallCardTemplate.content.cloneNode(true);
    const formattedDate = new Date(date).toLocaleDateString(
      navigator.language ?? 'en-US',
      { year: '2-digit', month: 'short', day: 'numeric' }
    );

    if (image) {
      clone.querySelector('.bookmark-card__image').href = image;
      clone.querySelector('.bookmark-card__image img').src = thumb;
    } else {
      clone.querySelector('.bookmark-card__image').remove();
    }

    clone.querySelector('.bookmark-card__excerpt').innerHTML += content;
    clone.querySelector('.bookmark-card').classList.add(`bookmark-${id}`);
    clone.querySelector('.bookmark-card').dataset.color = color;
    clone.querySelector('.bookmark-card__title > a').href = `${link}#paragraph-${paragraphId}`;
    clone.querySelector('.bookmark-card__title > a').innerText = chapter;
    clone.querySelector('.bookmark-card__percentage').innerText = `${progress.toFixed(1)} %`;
    clone.querySelector('.bookmark-card__progress').style.width = `${progress.toFixed(1)}%`;
    clone.querySelector('time').innerText = formattedDate;
    clone.querySelector('.button-delete-bookmark').setAttribute('data-bookmark-id', id);

    fragment.appendChild(clone);
  });

  // Append fragment to DOM
  fcn_bookmarksSmallCardBlock.querySelector('ul').appendChild(fragment);

  // Register events for delete buttons
  fcn_bookmarkDeleteHandler(_$$('.button-delete-bookmark'));
}

// =============================================================================
// BOOKMARK DELETION
// =============================================================================

/**
 * Register event handler for bookmark delete button(s).
 *
 * @since 4.0.0
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
 * @since 4.0.0
 * @param {Number} id - ID of the bookmark (chapter ID).
 */

function fcn_removeBookmark(id) {
  // Setup
  const chapter = _$('.chapter__article');
  const currentBookmark = _$('.current-bookmark');

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
