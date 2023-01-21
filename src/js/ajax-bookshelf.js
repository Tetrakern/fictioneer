// =============================================================================
// SETUP
// =============================================================================

const fcn_bookshelfTarget = _$$$('ajax-bookshelf-target');

// Initialize
if (fcn_theRoot.dataset.ajaxNonce && !_$$$('fictioneer-ajax-nonce')) {
  fcn_theRoot.addEventListener('nonceReady', () => {
    fcn_updateBookshelfView();
  });
} else {
  fcn_updateBookshelfView();
}

// =============================================================================
// GET CONTENT FROM LOCAL STORAGE
// =============================================================================

/**
 * Get bookshelf content from local storage or create new JSON.
 *
 * @since 4.3
 * @see fcn_isValidJSONString()
 */

function fcn_getBookshelfContent() {
  // Get JSON string from local storage
  let c = localStorage.getItem('fcnBookshelfContent');

  // Parse and return JSON string if valid, otherwise return new JSON
  return (c && fcn_isValidJSONString(c)) ? JSON.parse(c) : { html: {}, count: {} };
}

// =============================================================================
// UPDATE VIEW
// =============================================================================

function fcn_updateBookshelfView(action = null, page = null, order = null, update = false) {
  // Setup
  let fcn_bookshelfStorage = fcn_getBookshelfContent();
  action = action ?? fcn_bookshelfTarget.dataset.action,
  page = page ?? fcn_bookshelfTarget.dataset.page,
  order = order ?? fcn_bookshelfTarget.dataset.order;
  let htmlKey = action + page + order;

  // Storage item valid for 60 seconds
  if (
    ! fcn_bookshelfStorage.hasOwnProperty('timestamp') ||
    fcn_bookshelfStorage['timestamp'] + 60000 < Date.now()
  ) {
    localStorage.removeItem('fcnBookshelfContent');
    fcn_bookshelfStorage = { html: {}, count: {} };
    fcn_fetchBookshelfPart(action, page, order, update);
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
    if (update) _$$$('main').scrollIntoView({behavior: 'smooth'});
  } else {
    fcn_fetchBookshelfPart(action, page, order, update);
  }
}

// =============================================================================
// CHANGE PAGE
// =============================================================================

function fcn_browseBookshelfPage(page) {
  // Indicate loading
  fcn_bookshelfTarget.classList.add('ajax-in-progress');

  // Update view
  fcn_updateBookshelfView(null, page, null, true);

  // Update URL
  let url = fcn_buildUrl({
    tab: fcn_bookshelfTarget.dataset.tab,
    pg: page,
    order: fcn_bookshelfTarget.dataset.order
  });

  history.pushState({}, '', url.href);
}

// =============================================================================
// REQUEST BOOKSHELF VIA AJAX
// =============================================================================

function fcn_fetchBookshelfPart(action, page, order, update = false) {
  // Setup
  let fcn_bookshelfStorage = fcn_getBookshelfContent(),
      htmlKey = action + page + order;

  // Request
  fcn_ajaxGet({
    'action': action,
    'page': page,
    'order': order
  })
  .then((response) => {
    // Check for success
    if (response.success) {
      // Temporary remember in session storage
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
    if (update) _$$$('main').scrollIntoView({behavior: 'smooth'});
  });
}
