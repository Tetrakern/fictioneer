// =============================================================================
// SETUP
// =============================================================================

const fcn_bookshelfTarget = _$$$('ajax-bookshelf-target');

// Initialize
if (fcn_theRoot.dataset.ajaxNonce) {
  document.addEventListener('fcnAuthReady', () => {
    fcn_updateBookshelfView();
  });
} else {
  fcn_updateBookshelfView();
}

// =============================================================================
// GET CONTENT FROM WEB STORAGE
// =============================================================================

/**
 * Get bookshelf content from web storage or create new JSON.
 *
 * @since 4.3
 * @see fcn_parseJSON()
 */

function fcn_getBookshelfContent() {
  return fcn_parseJSON(localStorage.getItem('fcnBookshelfContent')) ?? { html: {}, count: {} };
}

// =============================================================================
// UPDATE VIEW
// =============================================================================

/**
 * Update the bookshelf view with fetched/cached content.
 *
 * @since 5.0.0
 *
 * @param {string|null} action - The action to perform (default: null).
 * @param {string|null} page - The page to display (default: null).
 * @param {string|null} order - The order of the bookshelf (default: null).
 * @param {boolean} scroll - Whether to scroll to the content (default: false).
 */

function fcn_updateBookshelfView(action = null, page = null, order = null, scroll = false) {
  // Setup
  let fcn_bookshelfStorage = fcn_getBookshelfContent();

  action = action ?? fcn_bookshelfTarget.dataset.action,
  page = page ?? fcn_bookshelfTarget.dataset.page,
  order = order ?? fcn_bookshelfTarget.dataset.order;

  const htmlKey = action + page + order;

  // Storage item valid for 60 seconds
  if (
    ! fcn_bookshelfStorage.hasOwnProperty('timestamp') ||
    fcn_bookshelfStorage['timestamp'] + 60000 < Date.now()
  ) {
    localStorage.removeItem('fcnBookshelfContent');
    fcn_bookshelfStorage = { html: {}, count: {} };
    fcn_fetchBookshelfPart(action, page, order, scroll);
    return;
  }

  // Check if content already cached
  if (
    fcn_bookshelfStorage.hasOwnProperty('html') &&
    fcn_bookshelfStorage['html'].hasOwnProperty(htmlKey)
  ) {
    fcn_bookshelfTarget.innerHTML = fcn_bookshelfStorage['html'][htmlKey];
    fcn_bookshelfTarget.classList.remove('ajax-in-progress');
    fcn_bookshelfTarget.dataset.page = page;
    _$('.item-number').innerHTML = `(${fcn_bookshelfStorage['count'][action]})`;

    if (scroll) {
      _$$$('main').scrollIntoView({ behavior: 'smooth' });
    }
  } else {
    fcn_fetchBookshelfPart(action, page, order, scroll);
  }
}

// =============================================================================
// CHANGE PAGE
// =============================================================================

/**
 * Change current bookshelf page.
 *
 * @since 5.0.0
 *
 * @param {string} page - The page to browse to.
 */

function fcn_browseBookshelfPage(page) {
  // Indicate loading
  fcn_bookshelfTarget.classList.add('ajax-in-progress');

  // Update view
  fcn_updateBookshelfView(null, page, null, true);

  // Update URL
  history.pushState(
    {},
    '',
    fcn_buildUrl({
      tab: fcn_bookshelfTarget.dataset.tab,
      pg: page,
      order: fcn_bookshelfTarget.dataset.order
    }).href
  );
}

// =============================================================================
// REQUEST BOOKSHELF VIA AJAX
// =============================================================================

/**
 * Fetch a part of the bookshelf via AJAX.
 *
 * @param {string} action - The action to perform.
 * @param {string} page - The page to retrieve.
 * @param {string} order - The order of the bookshelf.
 * @param {boolean} [scroll=false] - Whether to scroll to the top after rendering.
 */

function fcn_fetchBookshelfPart(action, page, order, scroll = false) {
  // Setup
  const htmlKey = action + page + order,
        fcn_bookshelfStorage = fcn_getBookshelfContent();

  // Request
  fcn_ajaxGet({
    'action': action,
    'fcn_fast_ajax': 1,
    'page': page,
    'order': order
  })
  .then((response) => {
    // Check for success
    if (response.success) {
      // Temporary remember in web storage
      fcn_bookshelfStorage['timestamp'] = Date.now();
      fcn_bookshelfStorage['html'][htmlKey] = response.data.html;
      fcn_bookshelfStorage['count'][action] = response.data.count;
      localStorage.setItem('fcnBookshelfContent', JSON.stringify(fcn_bookshelfStorage));

      // Render in view
      fcn_bookshelfTarget.innerHTML = response.data.html;
      fcn_bookshelfTarget.dataset.page = page;
      _$('.item-number').innerHTML = `(${response.data.count})`;
    } else {
      fcn_bookshelfTarget.innerHTML = '';
      fcn_bookshelfTarget.appendChild(fcn_buildErrorNotice(response.data.error));
    }
  })
  .catch((error) => {
    // Clear view
    _$('.item-number').innerHTML = '';
    fcn_bookshelfTarget.innerHTML = '';

    // Add error message
    fcn_bookshelfTarget.appendChild(fcn_buildErrorNotice(`${error.status}: ${error.statusText}`));
  })
  .then(() => {
    // Regardless of outcome
    fcn_bookshelfTarget.classList.remove('ajax-in-progress');

    if (scroll) {
      _$$$('main').scrollIntoView({ behavior: 'smooth' });
    }
  });
}

// =============================================================================
// BOOKSHELF EVENT DELEGATES
// =============================================================================

_$('.bookshelf__list')?.addEventListener('click', event => {
  // Handle page number
  const pageButton = event.target.closest('.page-numbers[data-page]');

  if (pageButton) {
    fcn_browseBookshelfPage(pageButton.dataset.page);
    return;
  }
});
