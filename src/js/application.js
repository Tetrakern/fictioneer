// =============================================================================
// GLOBAL SETUP (FIRST TO BE EXECUTED)
// =============================================================================

const /** @const {HTMLElement} */ fcn_theSite = _$$$('site');
const /** @const {HTMLElement} */ fcn_theBody = _$('body');
const /** @const {HTMLElement} */ fcn_theRoot = document.documentElement;
const /** @const {HTMLElement} */ fcn_inlineStorage = _$$$('inline-storage').dataset;
const /** @const {Object} */ fcn_urlParams = Object.fromEntries(new URLSearchParams(window.location.search).entries());
const /** @const {Number} */ fcn_pageLoadTimestamp = Date.now();
const /** @const {Number} */ fcn_ajaxLimitThreshold = Date.now() - parseInt(fictioneer_ajax.ttl); // Default: 60 seconds

var /** @type {Boolean} */ fcn_isLoggedIn = fcn_theBody.classList.contains('logged-in');
var /** @type {HTMLElement} */ fcn_chapterList = _$('#story-chapter-list > ul');

// =============================================================================
// CHAPTER INDEX
// =============================================================================

if (fcn_chapterList) {
  fcn_chapterList = fcn_chapterList.cloneNode(true);
  _$$$('story-chapter-list').remove();
  fcn_chapterList.querySelector(`[data-id="${fcn_chapterList.dataset.currentId}"]`)?.classList.add('current-chapter');
}

// =============================================================================
// STARTUP CLEANUP
// =============================================================================

// Remove superfluous data and nodes if not logged in
if (!fcn_isLoggedIn && !fcn_theRoot.dataset.ajaxAuth) {
  fcn_cleanupWebStorage(true);
  fcn_cleanupGuestView();
}

// Remove query args (defined in dynamic-scripts.js)
if (typeof fcn_removeQueryArgs === 'function') {
  fcn_removeQueryArgs();
}

// =============================================================================
// WEB STORAGE CLEANUP
// =============================================================================

/**
 * Clean-up web storage.
 *
 * @since 4.5.0
 * @param {Boolean} keepGuestData - Whether to keep data that does not need the
 *                                  user to be logged in.
 */

function fcn_cleanupWebStorage(keepGuestData = false) {
  const localItems = ['fcnProfileAvatar', 'fcnBookshelfContent'];

  if (!keepGuestData) {
    localItems.push('fcnChapterBookmarks');
  }

  // Remove local data
  localItems.forEach(item => localStorage.removeItem(item));

  // Remove user data (if any)
  const userDataKeys = ['loggedIn', 'follows', 'reminders', 'checkmarks', 'bookmarks', 'fingerprint'];
  const userData = fcn_parseJSON(localStorage.getItem('fcnUserData'));

  if (userData) {
    userDataKeys.forEach(key => userData[key] = false);
    localStorage.setItem('fcnUserData', JSON.stringify(userData));
  }

  // Remove private authentication data
  const auth = fcn_parseJSON(localStorage.getItem('fcnAuth'));

  if (auth?.loggedIn) {
    localStorage.removeItem('fcnAuth');
  }
}

// Admin bar logout link
_$('#wp-admin-bar-logout a')?.addEventListener('click', () => {
  fcn_cleanupWebStorage();
});

/**
 * Clean-up web storage in preparation of login
 *
 * @since 5.7.1
 */

function fcn_prepareLogin() {
  localStorage.removeItem('fcnUserData');
  localStorage.removeItem('fcnAuth');
}

_$$('.subscriber-login, .oauth-login-link, [data-prepare-login]').forEach(element => {
  element.addEventListener('click', () => {
    fcn_prepareLogin();
  });
});

// =============================================================================
// GUEST VIEW CLEANUP
// =============================================================================

/**
 * Cleanup view for non-authenticated guests.
 *
 * @since 5.0.0
 */

function fcn_cleanupGuestView() {
  fcn_isLoggedIn = false;
  fcn_theBody.classList.remove('logged-in', 'is-admin', 'is-moderator', 'is-editor', 'is-author');

  _$$$('fictioneer-ajax-nonce')?.remove();

  _$$('.only-moderators, .only-admins, .only-authors, .only-editors, .chapter-group__list-item-checkmark').forEach(element => {
    element.remove();
  });
}

// =============================================================================
// AUTHENTICATE VIA AJAX
// =============================================================================

// Initialize
document.addEventListener('DOMContentLoaded', () => {
  if (fcn_theRoot.dataset.ajaxAuth) {
    fcn_ajaxAuth();
  }
});

/**
 * Authenticate user via AJAX or from web storage
 *
 * @since 5.0.0
 */

function fcn_ajaxAuth() {
  let eventFired = false;

  // Look for recent authentication in web storage
  let localAuth = fcn_parseJSON(localStorage.getItem('fcnAuth')) ?? false;

  // Clear left over guest authentication
  if (fcn_isLoggedIn && !localAuth?.loggedIn) {
    localStorage.removeItem('fcnAuth');
    localAuth = false;
  }

  // Authenticate from web storage (if set)...
  if (localAuth) {
    fcn_addNonceHTML(localAuth['nonceHtml']);

    // Fire fcnAuthReady event
    const event = new CustomEvent('fcnAuthReady', {
      detail: {
        nonceHtml: localAuth['nonceHtml'],
        nonce: localAuth['nonce'],
        loggedIn: localAuth['loggedIn'],
        isAdmin: localAuth['isAdmin'],
        isModerator: localAuth['isModerator'],
        isAuthor: localAuth['isAuthor'],
        isEditor: localAuth['isEditor']
      },
      bubbles: true,
      cancelable: false
    });

    document.dispatchEvent(event);
    eventFired = true;

    // ... but refresh from server after some time has passed (e.g. 60 seconds)
    if (fcn_ajaxLimitThreshold < localAuth.lastLoaded) {
      return;
    }
  }

  // Request nonce via AJAX
  fcn_ajaxGet({
    'action': 'fictioneer_ajax_get_auth',
    'fcn_fast_ajax': 1
  }).then(response => {
    if (response.success) {
      // Append hidden input with nonce to DOM
      fcn_addNonceHTML(response.data.nonceHtml);

      // Unpack
      const data = {
        'lastLoaded': Date.now(),
        'nonceHtml': response.data.nonceHtml,
        'nonce': response.data.nonce,
        'loggedIn': response.data.loggedIn,
        'isAdmin': response.data.isAdmin,
        'isModerator': response.data.isModerator,
        'isAuthor': response.data.isAuthor,
        'isEditor': response.data.isEditor
      };

      // Fire fcnAuthReady event (if not already done)
      if (!eventFired) {
        const event = new CustomEvent('fcnAuthReady', {
          detail: data,
          bubbles: true,
          cancelable: false
        });

        document.dispatchEvent(event);
      }

      // Remember to avoid too many requests
      localStorage.setItem(
        'fcnAuth',
        JSON.stringify(data)
      );
    } else {
      // If unsuccessful, clear local data
      fcn_cleanupGuestView();

      // Fire fcnAuthFailed event
      const event = new Event('fcnAuthFailed');
      document.dispatchEvent(event);
    }
  })
  .catch(() => {
    // Most likely 403 after likely unsafe logout, clear local data
    localStorage.removeItem('fcnAuth');
    fcn_cleanupGuestView();

    // Fire fcnAuthError event
    const event = new Event('fcnAuthError');
    document.dispatchEvent(event);
  });
}

/**
 * Append hidden input element with nonce to DOM.
 *
 * @since 5.0.0
 * @param {String} nonceHtml - HTML for the hidden input with nonce value.
 */

function fcn_addNonceHTML(nonceHtml) {
  // Remove old (if any)
  _$$$('fictioneer-ajax-nonce')?.remove();

  // Append hidden input with nonce to DOM
  fcn_theBody.appendChild(fcn_html`${nonceHtml}`);
}

// =============================================================================
// UPDATE LOGIN STATE
// =============================================================================

// Only if AJAX authentication is active
if (!fcn_isLoggedIn && fcn_theRoot.dataset.ajaxAuth) {
  document.addEventListener('fcnAuthReady', (event) => {
    if (event.detail.loggedIn) {
      fcn_setLoggedInState(event.detail);
    } else {
      fcn_cleanupWebStorage(true);
      fcn_cleanupGuestView();
    }
  });
}

/**
 * Manually set logged-in state and call setup functions.
 *
 * @since 5.0.0
 * @param {Object} state - Fetched/Cached login state.
 */

function fcn_setLoggedInState(state) {
  // Update state and DOM
  fcn_isLoggedIn = state.loggedIn;
  fcn_theBody.classList.add('logged-in');
  fcn_theBody.classList.toggle('is-admin', state.isAdmin);
  fcn_theBody.classList.toggle('is-moderator', state.isModerator);
  fcn_theBody.classList.toggle('is-author', state.isAuthor);
  fcn_theBody.classList.toggle('is-editor', state.isEditor);

  // Cleanup view for users
  const removeSelectors = [];

  if (!state.isAdmin) {
    removeSelectors.push('.only-admins');
  }

  if (!state.isModerator && !state.isAdmin) {
    removeSelectors.push('.only-moderators');
  }

  if (!state.isAuthor && !state.isAdmin) {
    removeSelectors.push('.only-authors');
  }

  if (!state.isModerator && !state.isAdmin) {
    removeSelectors.push('.only-editors');
  }

  if (removeSelectors.length > 0) {
    _$$(removeSelectors.join(', ')).forEach(el => el.remove());
  }

  _$$('label[for="modal-login-toggle"], #modal-login-toggle, #login-modal').forEach(el => el.remove());

  // Initialize local user
  fcn_getProfileImage();
  fcn_fetchUserData();
}

// =============================================================================
// BIND EVENTS TO ANIMATION FRAMES
// =============================================================================

fcn_bindEventToAnimationFrame('scroll', 'scroll.rAF');
fcn_bindEventToAnimationFrame('resize', 'resize.rAF');

// =============================================================================
// HANDLE GLOBAL CLICK EVENTS
// =============================================================================

fcn_theBody.addEventListener('click', e => {
  // --- LAST CLICK ------------------------------------------------------------

  const lastClickTarget = e.target.closest('.toggle-last-clicked');

  if (
    lastClickTarget &&
    (
      !['BUTTON', 'A', 'INPUT', 'SELECT'].includes(e.target.tagName) ||
      e.target.classList.contains('toggle-last-clicked')
    )
  ) {
    fcn_toggleLastClicked?.(lastClickTarget);
    fcn_popupPosition?.();

    e.stopPropagation();
    return;
  }

  // --- PAGINATION JUMP -------------------------------------------------------

  const pageDots = e.target.closest('.page-numbers.dots:not(button)');

  if (pageDots) {
    fcn_jumpPage(pageDots);
    return;
  }

  // --- SPOILERS --------------------------------------------------------------

  const spoilerTarget = e.target.closest('.spoiler');

  if (spoilerTarget) {
    spoilerTarget.classList.toggle('_open');
    return;
  }

  // --- LOGIN CLICKS ----------------------------------------------------------

  if (e.target.closest('.oauth-login-link, .subscriber-login, [data-prepare-login]')) {
    fcn_prepareLogin();
  }

  // --- DATA CLICK HANDLERS ---------------------------------------------------

  const clickTarget = e.target.closest('[data-click]');
  const clickAction = clickTarget?.dataset.click;

  if (!clickAction) {
    return;
  }

  switch (clickAction) {
    case 'copy-to-clipboard':
      // Handle copy input to clipboard
      clickTarget.select();
      fcn_copyToClipboard(clickTarget.value, clickTarget.dataset.message);
      break;
    case 'reset-consent':
      // Handle consent reset
      fcn_deleteCookie('fcn_cookie_consent');
      location.reload();
      break;
    case 'clear-cookies':
      // Handle clear all cookies
      fcn_deleteAllCookies();
      alert(clickTarget.dataset.message);
      break;
    case 'logout':
      // Handle logout cleanup
      fcn_cleanupWebStorage();
      break;
    case 'card-toggle-follow':
      // Handle toggle card Follow
      if (fcn_isLoggedIn) {
        fcn_toggleFollow(clickTarget.dataset.storyId);
      } else {
        _$$$('modal-login-toggle')?.click();
      }
      break;
    case 'card-toggle-reminder':
      // Handle toggle card Reminder
      if (fcn_isLoggedIn) {
        fcn_toggleReminder(clickTarget.dataset.storyId);
      } else {
        _$$$('modal-login-toggle')?.click();
      }
      break;
    case 'card-toggle-checkmarks':
      // Handle toggle card Reminder
      if (fcn_isLoggedIn) {
        fcn_toggleCheckmark(
          parseInt(clickTarget.dataset.storyId),
          clickTarget.dataset.type,
          parseInt(clickTarget.dataset.chapterId),
          null,
          clickTarget.dataset.mode
        );
        fcn_updateCheckmarksView();
      } else {
        _$$$('modal-login-toggle')?.click();
      }
      break;
    case 'toggle-obfuscation':
      // Handle obfuscation
      clickTarget.closest('[data-obfuscation-target]').classList.toggle('_obfuscated');
      break;
  }
});

// =============================================================================
// HANDLE GLOBAL CHECKBOX CHANGE EVENTS
// =============================================================================

fcn_theBody.addEventListener('change', e => {
  const checkbox = e.target.closest('[type="checkbox"]');

  // Abort if not a checkbox
  if (!checkbox) {
    return;
  }

  // Data of interest
  const name = checkbox.name;
  const labels = name ? _$$(`label[for="${name}"]`) : [];
  const checked = checkbox.checked;

  // --- ARIA CHECKED ----------------------------------------------------------

  checkbox.closest('[aria-checked]')?.setAttribute('aria-checked', checked);

  labels.forEach(label => {
    label.closest('[aria-checked]')?.setAttribute('aria-checked', checked);
  });
});

// =============================================================================
// LOAD IFRAMES AFTER CONSENT CLICK
// =============================================================================

/**
 * Load embedded content by setting the src attribute.
 *
 * @since 4.0.0
 * @param {Event} event - The event.
 */

function fcn_loadEmbed(event) {
  event.target.parentNode.querySelectorAll('iframe, script')[0].src = event.target.dataset.src;
  event.target.parentElement.querySelector('.embed-logo')?.remove();
  event.target.remove();
}

// Listen for clicks on embeds
_$$('.iframe-consent, .twitter-consent').forEach(element => {
  element.onclick = (event) => {
    fcn_loadEmbed(event);
  }
});

// =============================================================================
// DETECT SCROLL DIRECTION
// =============================================================================

var /** @const {Number} */ fcn_lastScrollTop = 0;

/**
 * Detect scroll position and set indicator classes.
 *
 * @description Compares the vertical scroll offset to the top of the window with
 * a previously stored value. If the threshold of 5px is exceeded, meant to catch
 * insignificant movements, the scroll direction class will be updated depending
 * on the difference. Updates the stored value afterwards for the next time.
 *
 * @since 4.0.0
 * @since 5.8.1 - Also checks whether the page is scrolled to the top.
 */

function fcn_scrollDirection() {
  // Get current scroll offset
  const root_overflow = window.getComputedStyle(document.documentElement).overflow !== 'hidden';
  const newScrollTop = root_overflow ? (window.scrollY ?? document.documentElement.scrollTop) : (fcn_theBody.scrollTop ?? 1);

  // Scrolled to top?
  fcn_theBody.classList.toggle('scrolled-to-top', newScrollTop === 0);

  // Stop if the mobile menu is open
  if (fcn_theSite.classList.contains('transformed-scroll')) {
    return;
  }

  // Check whether the difference between old and new offset exceeds the threshold
  if (Math.abs(fcn_lastScrollTop - newScrollTop) >= 25) {
    fcn_theBody.classList.toggle('scrolling-down', newScrollTop > fcn_lastScrollTop);
    fcn_theBody.classList.toggle('scrolling-up', !(newScrollTop > fcn_lastScrollTop));
    fcn_lastScrollTop = Math.max(newScrollTop, 0);
  }
}

// Listen for window scrolling
window.addEventListener('scroll.rAF', fcn_throttle(fcn_scrollDirection, 200));

// Initialize once
fcn_scrollDirection();

// =============================================================================
// OBSERVERS
// =============================================================================

/**
 * Create and initialize an IntersectionObserver.
 *
 * @param {String} selector - Selector for the element to be observed.
 * @param {Function} callback - Callback for the IntersectionObserver.
 * @param {Object} options - Options for the IntersectionObserver.
 */
function fcn_observe(selector, callback, options = {}) {
  const element = _$(selector);

  if (!element) {
    return null;
  }

  const observer = new IntersectionObserver(entries => callback(entries[0]), options);
  observer.observe(element);
}

document.addEventListener('DOMContentLoaded', () => {
  const mainObserverCallback = e => {
    fcn_theBody.classList.toggle('is-inside-main', e.intersectionRatio < 1 && e.boundingClientRect.top <= 0);
  };

  const endOfChapterCallback = e => {
    fcn_theBody.classList.toggle('is-end-of-chapter', e.isIntersecting || e.boundingClientRect.top < 0);
  };

  const navStickyCallback = e => {
    _$$$('full-navigation').classList.toggle('is-sticky', e.intersectionRatio < 1);
  };

  // Initialize observers
  fcn_observe('.main-observer', mainObserverCallback, { threshold: [1] });
  fcn_observe('.chapter-end', endOfChapterCallback, { root: null, threshold: 0 });
  fcn_observe('#nav-observer-sticky', navStickyCallback, { threshold: [1] });
});

// =============================================================================
// DRAGGABLE CONTAINER
// =============================================================================

/**
 * Makes an element mouse draggable.
 *
 * @since 4.0.0
 */

function fcn_dragElement(element) {
  const target = element.querySelector('.drag-anchor') ?? element;
  let lastX;
  let lastY;

  target.onmousedown = startDrag;

  function startDrag(e) {
    e.preventDefault();

    lastX = e.clientX;
    lastY = e.clientY;

    target.onmousemove = moveDrag;
    target.onmouseup = stopDrag;
  }

  function moveDrag(e) {
    e.preventDefault();

    element.style.top = `${(element.offsetTop - (lastY - e.clientY))}px`;
    element.style.left = `${(element.offsetLeft - (lastX - e.clientX))}px`;

    lastX = e.clientX;
    lastY = e.clientY;
  }

  function stopDrag() {
    target.onmouseup = null;
    target.onmousemove = null;
  }
}

// Make selected modals draggable
_$$('.modal__header.drag-anchor').forEach(element => {
  fcn_dragElement(element.closest('.modal__wrapper'));
});

// =============================================================================
// NOTIFICATIONS
// =============================================================================

/**
 * Show notification that will vanish after a while.
 *
 * @since 4.0.0
 * @param {String} message - The message to display.
 * @param {Number} [duration=] - Seconds the message will be visible. Default 3.
 * @param {String} [type=] - Type of the message. Default 'base'.
 */

function fcn_showNotification(message, duration = 3, type = 'base') {
  // Setup
  const container = _$('#notifications');
  const node = document.createElement('div');

  // Build notification and set up transition to transparent
  node.innerHTML = message;
  node.classList.add('notifications__message', `_${type}`);
  node.style.opacity = 1;
  node.style.transitionDelay = `${duration}s`;

  // New notification added on top of stack (if any)
  container.prepend(node);

  // Remove element once the notification has vanished
  node.addEventListener('transitionend', e => { container.removeChild(e.target); });
  node.addEventListener('click', e => { container.removeChild(e.currentTarget); });

  // Wait for the element to become visible, otherwise it will never show up
  setTimeout(() => { node.style.opacity = 0; }, 100);
}

// Show OAuth 2.0 registration error notice (if any)
if (fcn_urlParams['failure'] === 'oauth_email_taken') {
  fcn_showNotification(fictioneer_tl.notification.oauthEmailTaken, 5, 'warning');
}

// Show OAuth 2.0 link error notice (if any)
if (fcn_urlParams['failure'] === 'oauth_already_linked') {
  fcn_showNotification(fictioneer_tl.notification.oauthAccountAlreadyLinked, 5, 'warning');
}

// Show new subscriber notice (if any)
if (fcn_urlParams['success'] === 'oauth_new') {
  fcn_showNotification(fictioneer_tl.notification.oauthNew, 10);
}

// Show OAuth 2.0 account merge notice (if any)
if (fcn_urlParams['success']?.includes('oauth_merged_')) {
  fcn_showNotification(fictioneer_tl.notification.oauthAccountLinked, 3, 'success');
}

// Generic messages
if (fcn_urlParams['fictioneer-notice'] && fcn_urlParams['fictioneer-notice'] !== '') {
  let type = fcn_urlParams['failure'] === '1' ? 'warning' : 'base';
  type = fcn_urlParams['success'] === '1' ? 'success' : type;

  fcn_showNotification(fcn_sanitizeHTML(fcn_urlParams['fictioneer-notice']), 3, type);
}

// =============================================================================
// SITE SETTINGS
// =============================================================================

const /** @const {HTMLElement} */ fcn_settingMinimal = _$$$('site-setting-minimal');
const /** @const {HTMLElement} */ fcn_settingChapterProgressBar = _$$$('site-setting-chapter-progress-bar');
const /** @const {HTMLElement} */ fcn_settingHueRotateRange = _$$$('site-setting-hue-rotate-range');
const /** @const {HTMLElement} */ fcn_settingHueRotateText = _$$$('site-setting-hue-rotate-text');
const /** @const {HTMLElement} */ fcn_settingHueRotateReset = _$$$('site-setting-hue-rotate-reset');
const /** @const {HTMLElement[]} */ fcn_settingDarkenRanges = _$$('.setting-darken-range');
const /** @const {HTMLElement[]} */ fcn_settingDarkenTexts = _$$('.setting-darken-text');
const /** @const {HTMLElement[]} */ fcn_settingDarkenResets = _$$('.setting-darken-reset');
const /** @const {HTMLElement[]} */ fcn_settingSaturationRanges = _$$('.setting-saturation-range');
const /** @const {HTMLElement[]} */ fcn_settingSaturationTexts = _$$('.setting-saturation-text');
const /** @const {HTMLElement[]} */ fcn_settingSaturationResets = _$$('.setting-saturation-reset');
const /** @const {HTMLElement[]} */ fcn_settingFontLightnessRanges = _$$('.setting-font-lightness-range');
const /** @const {HTMLElement[]} */ fcn_settingFontLightnessTexts = _$$('.setting-font-lightness-text');
const /** @const {HTMLElement[]} */ fcn_settingFontLightnessResets = _$$('.setting-font-lightness-reset');

const /** @const {String[]} */ fcn_settingEvents = [
  'nav-sticky',
  'background-textures',
  'polygons',
  'covers',
  'text-shadows',
  'minimal',
  'chapter-progress-bar'
];

var /** @var {OBJECT} */ fcn_siteSettings = fcn_getSiteSettings();

// =============================================================================
// SITE SETTINGS: TOGGLE
// =============================================================================

/**
 * Toggle a single site setting.
 *
 * @since 4.0.0
 * @see fcn_applySiteSettings();
 * @param {HTMLElement} target - The clicked toggle.
 * @param {String} key - The setting to set.
 * @param {Boolean} value - Set setting to true or false
 */

function fcn_updateSiteSetting(target, key, value) {
  target.checked = value;
  fcn_siteSettings[key] = value;
  fcn_applySiteSettings(fcn_siteSettings);
}

// Listen for clicks on toggles
fcn_settingEvents.forEach(setting => {
  _$$$(`site-setting-${setting}`)?.addEventListener('change', event => {
    fcn_updateSiteSetting(event.currentTarget, setting, event.currentTarget.checked);
  });
});

// =============================================================================
// SITE SETTINGS: LIGHT MODE
// =============================================================================

/**
 * Toggle light mode.
 *
 * @description Looks for the current light mode state in local storage or falls
 * back to the default set in the HTML root. Flips the state and then updates the
 * view and local storage via fcn_setLightMode().
 *
 * @since 4.3.0
 * @see fcn_setLightMode();
 */

function fcn_toggleLightMode() {
  const current =
    localStorage.getItem('fcnLightmode') ?
      localStorage.getItem('fcnLightmode') == 'true' :
      fcn_theRoot.dataset.modeDefault == 'light';

  fcn_setLightMode(!current);
}

/**
 * Set the light mode state.
 *
 * @since 4.3.0
 * @see fcn_updateThemeColor();
 * @param {Boolean} boolean - Set light mode to true or false.
 * @param {Boolean} [silent=false] - Optional. Whether to not update the theme color meta tag.
 */

function fcn_setLightMode(boolean, silent = false) {
  // Update light mode state
  localStorage.setItem('fcnLightmode', boolean);
  fcn_theRoot.dataset.mode = boolean ? 'light' : 'dark';

  // Update aria-checked attributes
  _$$('.toggle-light-mode').forEach(element => {
    element.closest('[aria-checked]')?.setAttribute('aria-checked', boolean);
  });

  // Update theme color meta tag
  if (!silent) {
    fcn_updateThemeColor();
  }
}

// Initialize (with default from HTML root)
fcn_setLightMode(
  localStorage.getItem('fcnLightmode') ?
    localStorage.getItem('fcnLightmode') == 'true' :
    fcn_theRoot.dataset.modeDefault == 'light',
  true
);

// Listen to clicks on light mode toggles
_$$('.toggle-light-mode').forEach(element => {
  element.onclick = () => fcn_toggleLightMode();
});

// =============================================================================
// SITE SETTINGS: FONT WEIGHT
// =============================================================================

/**
 * Update font weight setting.
 *
 * @since 4.0.0
 */

function fcn_updateFontWeight() {
  // Modified?
  const modified = fcn_siteSettings['font-weight'] != 'default';

  // Find and update all font-weight selects
  _$$('.site-setting-font-weight').forEach(setting => {
    setting.value = fcn_siteSettings['font-weight'];
  });

  // Find and update all font-weight reset buttons
  _$$('.font-weight-reset').forEach(reset => {
    reset.classList.toggle('_modified', modified);
  });
}

/**
 * Reset font weight setting to default.
 *
 * @since 4.3.0
 */

function fcn_resetFontWeight() {
  fcn_siteSettings['font-weight'] = 'default';
  fcn_theRoot.dataset.fontWeight = 'default';
  fcn_applySiteSettings(fcn_siteSettings);
}

// Listen for clicks on font weight reset buttons
_$$('.font-weight-reset').forEach(element => {
  element.addEventListener('click', fcn_resetFontWeight);
});

// Listen for changes of font weight select
_$$('.site-setting-font-weight').forEach(setting => {
  setting.onchange = (e) => {
    fcn_siteSettings['font-weight'] = e.target.value;
    fcn_theRoot.dataset.fontWeight = e.target.value;
    fcn_applySiteSettings(fcn_siteSettings);
    fcn_updateFontWeight();
  }
});

// =============================================================================
// SITE SETTINGS: HUE ROTATE
// =============================================================================

/**
 * Update hue rotate setting.
 *
 * @since 4.3.0
 */

function fcn_updateHueRotate(value) {
  // Evaluate
  value = fcn_clamp(0, 360, value ?? 0);

  // Update associated elements
  fcn_settingHueRotateText.value = value;
  fcn_settingHueRotateRange.value = value;
  fcn_settingHueRotateReset.classList.toggle('_modified', value != 0);

  // Update hue-rotate property
  fcn_theRoot.style.setProperty('--hue-rotate', `(${value}deg + var(--hue-offset))`);

  // Update local storage
  fcn_siteSettings['hue-rotate'] = value;
  fcn_setSiteSettings();

  // Update theme color
  fcn_updateThemeColor();
}

/**
 * Helper to call fcn_updateHueRotate() with input value.
 *
 * @since 4.0.0
 */

function fcn_setHueRotate() {
  fcn_updateHueRotate(this.value);
}

// Listen for clicks on hue rotate reset button
fcn_settingHueRotateReset?.addEventListener('click', () => { fcn_updateHueRotate(0) });

// Listen for hue rotate range input
fcn_settingHueRotateRange?.addEventListener('input', fcn_throttle(fcn_setHueRotate, 1000 / 24));

// Listen for hue rotate text input
fcn_settingHueRotateText?.addEventListener('input', fcn_setHueRotate);

// =============================================================================
// SITE SETTINGS: DARKEN
// =============================================================================

/**
 * Update darken setting.
 *
 * @since 4.0.0
 */

function fcn_updateDarken(value = null) {
  // Evaluate
  value = fcn_clamp(-1, 1, value ?? fcn_siteSettings['darken']);
  value = Math.round((value + Number.EPSILON) * 100) / 100;

  // Update associated elements
  fcn_settingDarkenResets.forEach(element => { element.classList.toggle('_modified', value != 0); });
  fcn_settingDarkenRanges.forEach(element => { element.value = value; });
  fcn_settingDarkenTexts.forEach(element => { element.value = parseInt(value * 100); });

  // Calculate property
  const d = value >= 0 ? 1 + value ** 2 : 1 - value ** 2;

  // Update property in DOM
  fcn_theRoot.style.setProperty('--darken', `(${d} + var(--lightness-offset))`);

  // Update local storage
  fcn_siteSettings['darken'] = value;
  fcn_setSiteSettings();

  // Update theme color
  fcn_updateThemeColor();
}

/**
 * Helper to call fcn_updateDarken() with range input value.
 *
 * @since 4.0.0
 */

function fcn_setDarkenFromRange() {
  fcn_updateDarken(this.value);
}

/**
 * Helper to call fcn_updateDarken() with text input value.
 *
 * @since 4.0.0
 */

function fcn_setDarkenFromText() {
  if (this.value == '-' || this.value == '') {
    return;
  }

  fcn_updateDarken((parseInt(this.value) ?? 0) / 100);
}

// Listen for clicks darken reset buttons
fcn_settingDarkenResets.forEach(element => {
  element.addEventListener('click', () => { fcn_updateDarken(0) });
});

// Listen for darken range inputs
fcn_settingDarkenRanges.forEach(element => {
  element.addEventListener('input', fcn_throttle(fcn_setDarkenFromRange, 1000 / 24));
});

// Listen for darken text inputs
fcn_settingDarkenTexts.forEach(element => {
  element.addEventListener('input', fcn_setDarkenFromText);
});

// =============================================================================
// SITE SETTINGS: SATURATION
// =============================================================================

/**
 * Update saturation setting.
 *
 * @since 4.3.0
 */

function fcn_updateSaturation(value = null) {
  // Evaluate
  value = fcn_clamp(-1, 1, value ?? fcn_siteSettings['saturation']);

  // Update associated elements
  fcn_settingSaturationResets.forEach(element => { element.classList.toggle('_modified', value != 0); });
  fcn_settingSaturationRanges.forEach(element => { element.value = value; });
  fcn_settingSaturationTexts.forEach(element => { element.value = parseInt(value * 100); });

  // Calculate property
  const s = value >= 0 ? 1 + value ** 2 : 1 - value ** 2;

  // Update property in DOM
  fcn_theRoot.style.setProperty('--saturation', `(${s} + var(--saturation-offset))`);

  // Update local storage
  fcn_siteSettings['saturation'] = value;
  fcn_setSiteSettings();

  // Update theme color
  fcn_updateThemeColor();
}

/**
 * Helper to call fcn_updateSaturation() with range input value.
 *
 * @since 4.0.0
 */

function fcn_setSaturationFromRange() {
  fcn_updateSaturation(this.value);
}

/**
 * Helper to call fcn_updateSaturation() with text input value.
 *
 * @since 4.0.0
 */

function fcn_setSaturationFromText() {
  if (this.value == '-' || this.value == '') {
    return;
  }

  fcn_updateSaturation((parseInt(this.value) ?? 0) / 100);
}

// Listen for clicks saturation reset buttons
fcn_settingSaturationResets.forEach(element => {
  element.addEventListener('click', () => { fcn_updateSaturation(0) });
});

// Listen for darken range inputs
fcn_settingSaturationRanges.forEach(element => {
  element.addEventListener('input', fcn_throttle(fcn_setSaturationFromRange, 1000 / 24));
});

// Listen for darken text inputs
fcn_settingSaturationTexts.forEach(element => {
  element.addEventListener('input', fcn_setSaturationFromText);
});

// =============================================================================
// SITE SETTINGS: FONT LIGHTNESS
// =============================================================================

/**
 * Update font lightness setting.
 *
 * @since 5.12.2
 */

function fcn_updateFontLightness(value = null) {
  // Evaluate
  value = fcn_clamp(-1, 1, value ?? fcn_siteSettings['font-lightness'] ?? 1);

  // Update associated elements
  fcn_settingFontLightnessResets.forEach(element => { element.classList.toggle('_modified', value != 0); });
  fcn_settingFontLightnessRanges.forEach(element => { element.value = value; });
  fcn_settingFontLightnessTexts.forEach(element => { element.value = parseInt(value * 100); });

  // Calculate property
  const l = value >= 0 ? 1 + value ** 2 : 1 - value ** 2;

  // Update property in DOM
  fcn_theRoot.style.setProperty('--font-lightness', `(${l} + var(--font-lightness-offset))`);

  // Update local storage
  fcn_siteSettings['font-lightness'] = value;
  fcn_setSiteSettings();

  // Update theme color
  fcn_updateThemeColor();
}

/**
 * Helper to call fcn_updateFontLightness() with range input value.
 *
 * @since 5.12.2
 */

function fcn_setFontLightnessFromRange() {
  fcn_updateFontLightness(this.value);
}

/**
 * Helper to call fcn_updateFontLightness() with text input value.
 *
 * @since 5.12.2
 */

function fcn_setFontLightnessFromText() {
  if (this.value == '-' || this.value == '') {
    return;
  }

  fcn_updateFontLightness((parseInt(this.value) ?? 0) / 100);
}

// Listen for clicks saturation reset buttons
fcn_settingFontLightnessResets.forEach(element => {
  element.addEventListener('click', () => { fcn_updateFontLightness(0) });
});

// Listen for darken range inputs
fcn_settingFontLightnessRanges.forEach(element => {
  element.addEventListener('input', fcn_throttle(fcn_setFontLightnessFromRange, 1000 / 24));
});

// Listen for darken text inputs
fcn_settingFontLightnessTexts.forEach(element => {
  element.addEventListener('input', fcn_setFontLightnessFromText);
});

// =============================================================================
// SITE SETTINGS: GET/SET/APPLY
// > also done in <head> to avoid flickering
// =============================================================================

/**
 * Returns default site settings.
 *
 * @since 4.0.0
 * @return {Object} The site settings.
 */

function fcn_defaultSiteSettings() {
  return {
    'nav-sticky': true,
    'background-textures': true,
    'polygons': true,
    'covers': true,
    'text-shadows': false,
    'minimal': false,
    'chapter-progress-bar': true,
    'site-theme': 'default',
    'font-weight': 'default',
    'darken': 0,
    'saturation': 0,
    'font-saturation': 0,
    'font-lightness': 0,
    'hue-rotate': 0
  };
}

/**
 * Get site settings JSON from web storage or create new one.
 *
 * @since 4.0.0
 * @see fcn_parseJSON()
 * @see fcn_defaultSiteSettings()
 * @return {Object} The site settings.
 */

function fcn_getSiteSettings() {
  // Get settings from web storage or use defaults
  const s = fcn_parseJSON(localStorage.getItem('fcnSiteSettings')) ?? fcn_defaultSiteSettings();

  // Update web storage and return
  fcn_setSiteSettings(s);

  return s;
}

/**
 * Set the site settings object and save to local storage.
 *
 * @since 4.0.0
 * @param {Object=} value - The site settings.
 */

function fcn_setSiteSettings(value = null) {
  // Get settings object
  value = value ? value : fcn_siteSettings;

  // Simple validation
  if (typeof value !== 'object') {
    return;
  }

  // Keep global updated
  fcn_siteSettings = value;

  // Update local storage
  localStorage.setItem('fcnSiteSettings', JSON.stringify(value));
}

/**
 * Apply site settings to the site.
 *
 * @since 4.0.0
 * @param {Object} value - The site settings.
 */

function fcn_applySiteSettings(value) {
  // Evaluate
  value = typeof value !== 'object' ? fcn_defaultSiteSettings() : value;

  // Loop all settings...
  Object.entries(value).forEach((setting) => {
    // Update checkboxes
    const control = _$$$(`site-setting-${setting[0]}`);

    if (control) {
      control.checked = setting[1];
    }

    // Update styles and classes
    switch (setting[0]) {
      case 'minimal':
        fcn_theRoot.classList.toggle('minimal', setting[1]);
        break;
      case 'darken':
        fcn_updateDarken();
        break;
      case 'saturation':
        fcn_updateSaturation();
        break;
      case 'font-saturation':
        break;
      case 'font-lightness':
        fcn_updateFontLightness();
        break;
      case 'hue-rotate':
        fcn_updateHueRotate(setting[1]);
        break;
      case 'font-weight':
        fcn_updateFontWeight();
        break;
      default:
        fcn_theRoot.classList.toggle(`no-${setting[0]}`, !setting[1]);
    }
  });

  // Update local storage
  fcn_setSiteSettings(value);
}

// Initialize
fcn_applySiteSettings(fcn_siteSettings);

// =============================================================================
// SITE SETTINGS: THEME
// =============================================================================

function fcn_updateSiteTheme(theme) {
  // Update root and settings
  fcn_siteSettings['site-theme'] = theme;
  fcn_theRoot.dataset.theme = theme;

  // Update reset button
  _$$$('site-setting-theme-reset').classList.toggle('_modified', theme != 'default');

  // Apply
  fcn_applySiteSettings(fcn_siteSettings);
  fcn_updateThemeColor();
}

_$$('.site-setting-site-theme').forEach(element => {
  // Initialize site theme select elements
  element.value = fcn_siteSettings.hasOwnProperty('site-theme') ? fcn_siteSettings['site-theme'] : 'default';

  // Modified?
  _$$$('site-setting-theme-reset').classList.toggle('_modified', element.value != 'default');

  // Listen for site theme changes
  element.addEventListener('change', event => { fcn_updateSiteTheme(event.target.value) });
});

function fcn_resetSiteTheme() {
  fcn_updateSiteTheme('default');
  _$$('.site-setting-site-theme').forEach(element => { element.value = 'default'; });
}

// Listen for click on site theme reset button
_$$$('site-setting-theme-reset')?.addEventListener('click', fcn_resetSiteTheme);

// Initialize
fcn_updateThemeColor();

// =============================================================================
// PAGE NUMBER JUMP
// =============================================================================

/**
 * Prompt for and jump to page number
 *
 * @since 5.4.0
 *
 * @param {Number} source - Page jump element.
 */

function fcn_jumpPage(source) {
  if (fcn_theRoot.dataset.disablePageJump) {
    return;
  }

  const input = parseInt(window.prompt(fictioneer_tl.notification.enterPageNumber));

  if (input > 0) {
    const url = source.nextElementSibling.getAttribute('href'); // Guaranteed to always have the query parameter
    const pageParams = ['page=', 'paged=', 'comment-page-', 'pg='];

    for (const param of pageParams) {
      if (url.includes(param)) {
        window.location.href = url.replace(new RegExp(`${param}\\d+`), param + input);
        return;
      }
    }

    window.location.href = url.replace(/page\/\d+/, `page/${input}`);
  }
}

// =============================================================================
// WATCH DOM MUTATION TO UPDATE VIEW AND REBIND EVENTS
// =============================================================================

const /** @const {MutationObserver} */ fcn_cardListMutationObserver = new MutationObserver(mutations => {
  mutations.forEach(mutation => {
    if (mutation.addedNodes.length > 0) {
      // Update view
      fcn_updateFollowsView?.();
      fcn_updateCheckmarksView?.();
      fcn_updateRemindersView?.();
    }
  });
});

// Watch for added nodes in card lists
_$$('.card-list:not(._no-mutation-observer)')
  .forEach(card => fcn_cardListMutationObserver.observe(card, { childList: true, subtree: true }));

// =============================================================================
// COLLAPSE/EXPAND CHAPTER GROUPS
// =============================================================================

_$$('.chapter-group__name').forEach(element => {
  element.addEventListener('click', event => {
    const group = event.currentTarget.closest('.chapter-group');
    const list = group.querySelector('.chapter-group__list');
    const state = !group.classList.contains('_closed');

    // Apply will-change: transform
    _$('.main__background')?.classList.add('will-change');

    // Base for transition
    list.style.height = `${list.scrollHeight}px`;

    // Use requestAnimationFrame for next paint to ensure transition occurs
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        group.classList.toggle('_closed', state);
        list.style.height = state ? '0' : `${list.scrollHeight}px`;
      });
    });
  });
});

_$$('.chapter-group__list').forEach(list => {
  list.addEventListener('transitionend', event => {
    // Remove inline height once transition is done
    list.style.height = '';

    // Adjust tabindex for accessibility
    list.querySelectorAll('a, button, label, input:not([hidden])').forEach(element => {
      element.tabIndex = list.parentElement.classList.contains('_closed') ? '-1' : '0';
    });

    // Remove will-change: transform
    _$('.main__background')?.classList.remove('will-change');
  });
});

// =============================================================================
// REVEAL IMAGE ON CLICK ON CONSENT BUTTON
// =============================================================================

/**
 * Reveal image on click.
 *
 * @since 5.0.0
 * @param {HTMLElement} button - The clicked reveal button.
 */

function fcn_revealCommentImage(button) {
  const img = button.parentElement.querySelector('img');
  img.src = img.dataset.src;
  button.remove();
}

_$('.fictioneer-comments')?.addEventListener('click', event => {
  if (event.target?.classList.contains('consent-button')) {
    fcn_revealCommentImage(event.target);
  }
});

// =============================================================================
// CONTACT FORM SHORTCODE
// =============================================================================

/**
 * Submit contact form via AJAX.
 *
 * @since 5.0.0
 * @param {HTMLElement} button - The submit button in the form.
 */

function fcn_contactFormSubmit(button) {
  // Setup
  const form = button.closest('form');
  const formData = new FormData(form);
  const payload = {'action': 'fictioneer_ajax_submit_contact_form'};

  // Form valid?
  if (!form.reportValidity()) {
    return;
  }

  // Disable after click regardless of outcome
  button.disabled = true;
  button.innerHTML = button.dataset.disabled;

  // Delay trap (cannot be done server-side because of caching)
  if (Date.now() < fcn_pageLoadTimestamp + 3000) {
    return;
  }

  // Prepare payload
  for (const [key, value] of formData) {
    payload[key] = value;
  }

  // Set ajax-in-progress
  form.classList.add('ajax-in-progress');

  // Request
  fcn_ajaxPost(payload)
  .then(response => {
    if (response.success) {
      // Success
      form.querySelector('textarea').value = '';
      button.innerHTML = button.dataset.done;
      fcn_showNotification(response.data.success, 3, 'success');
    } else if (response.data.error) {
      // Failure
      button.disabled = false;
      button.innerHTML = button.dataset.enabled;
      fcn_showNotification(response.data.error, 5, 'warning');
    }
  })
  .catch(error => {
    if (error.status && error.statusText) {
      fcn_showNotification(`${error.status}: ${error.statusText}`, 5, 'warning');
      button.disabled = false;
      button.innerHTML = button.dataset.enabled;
    }
  })
  .then(() => {
    // Regardless of outcome
    form.classList.remove('ajax-in-progress');
  });
}

_$$('.fcn-contact-form').forEach(element => {
  element.querySelector('.fcn-contact-form__submit').addEventListener(
    'click',
    event => {
      fcn_contactFormSubmit(event.currentTarget);
    }
  );
});

// =============================================================================
// MODALS
// =============================================================================

/*
 * Set focus onto modal when opened.
 */

fcn_theBody.querySelectorAll('.modal-toggle').forEach(element => {
  element.addEventListener(
    'change',
    event => {
      // Set current tabIndex into modal container
      if (event.currentTarget.checked) {
        const modalElement = event.currentTarget.nextElementSibling.querySelector('[tabindex="0"]');
        modalElement?.focus();
        modalElement?.blur();
      } else if (fcn_theBody.classList.contains('user-is-tabbing')) {
        fcn_theSite.querySelector(`label[for="${event.currentTarget.id}"]`)?.focus();
      }
    }
  );
});

// Open dialog modal
_$$('[data-click-action*="open-dialog-modal"]').forEach(element => {
  element.addEventListener('click', event => {
    document.querySelector(event.currentTarget.dataset.clickTarget).showModal();
  });
});

// Close dialog modal
_$$('[data-click-action*="close-dialog-modal"]').forEach(element => {
  element.addEventListener('click', event => {
    event.preventDefault();
    event.target.closest('dialog').close();
  });
});

// Close dialog modal on click outside
_$$('dialog').forEach(element => {
  element.addEventListener('mousedown', event => {
    if (event.target.tagName.toLowerCase() === 'dialog') {
      event.preventDefault();
      event.target.close();
    }
  });
});

// =============================================================================
// KEYBOARD INPUTS
// =============================================================================

/*
 * Make elements accessible with keyboard.
 */

fcn_theBody.addEventListener(
  'keydown',
  e => {
    let tabFocus = document.activeElement.closest('[tabindex="0"]:not(a, input, button, select)');

    if (['BUTTON', 'A', 'INPUT', 'SELECT'].includes(document.activeElement.tagName)) {
      tabFocus = null;
    }

    // When pressing space or enter on a focused element
    if (tabFocus) {
      if (e.keyCode == 32 || e.keyCode == 13) {
        e.preventDefault();
        tabFocus.click();
      }
    }

    // Escape
    if (e.keyCode == 27) {
      // Uncheck all modal control checkboxes (should only be one)
      _$$('.modal-toggle:checked').forEach(element => {
        element.checked = false;
        element.dispatchEvent(new Event('change'));
      });

      // Close lightbox
      const lightbox = _$('.lightbox.show');

      if (lightbox) {
        lightbox.querySelector('.lightbox__close').click();
        return;
      }

      // Close paragraph tools
      const paragraphToolsClose = _$('.selected-paragraph #button-close-paragraph-tools');

      if (paragraphToolsClose) {
        paragraphToolsClose.click();
        return;
      }

      // Pause/Close TTS
      const tts = _$('#tts-interface:not(.hidden)');

      if (tts) {
        if (tts.classList.contains('playing')) {
          const pause = _$$$('button-tts-pause');
          pause?.click();
          pause?.focus();
          pause?.blur();
        } else {
          _$$$('button-tts-stop').click();
        }

        return;
      }
    }
  }
);

// =============================================================================
// KEYWORD INPUTS
// =============================================================================

/*
 * Let's do this properly for once.
 */

class FCN_KeywordInput {
  constructor(input) {
    this.input = input;
    this.operator = input.closest('.keyword-input').querySelector('.keyword-input__operator input');
    this.inputWrapper = input.closest('.keyword-input__input-wrapper');
    this.block = input.closest('.keyword-input');
    this.form = this.block.closest('.search-form');
    this.collection = this.block.querySelector('.keyword-input__collection');
    this.suggestionList = this.block.querySelector('.keyword-input__suggestion-list');
    this.tabSuggestion = this.block.querySelector('.keyword-input__tab-suggestion');
    this.allowText = this.form.querySelector('.allow-list')?.innerText ?? '{}';
    this.allowList = JSON.parse(this.allowText);
    this.hints = this.block.querySelector('.keyword-input__hints');
    this.noHint = this.block.querySelector('.keyword-input__no-suggestions');
    this.keywords = this.collection.value.length > 0 ? this.collection.value.split(',') : [];
    this.bindEvents();
    this.resize();
    this.filterSuggestions();
  }

  resize() {
    const size = this.tabSuggestion.innerText.length > 0 ?
      this.tabSuggestion.innerText.length : this.input.value.length;

    this.input.style.width = size * 0.88 + 2 + 'ch'; // Good enough
  }

  reset() {
    // Clear keywords
    this.keywords = [];

    // Clear nodes
    this.block.querySelectorAll('.node').forEach(element => { element.remove() });

    // Reset operators
    this.block.querySelectorAll('.keyword-input__operator > input').forEach(element => { element.checked = false });

    // Clear input
    this.input.value = '';

    // Update hidden collection input
    this.updateCollection();

    // Filter suggestions
    this.filterSuggestions();

    // Resize input
    this.resize();
  }

  encode(text) {
    return encodeURIComponent(text.toLowerCase()).replace(/'/g, '%27');
  }

  filterSuggestions() {
    const value = this.input.value.toLowerCase();
    let count = 0;
    let match = '';

    // Show suggestions if input is not empty...
    if (value == '') {
      this.suggestionList.querySelectorAll('.keyword-button').forEach(element => { element.hidden = true });
    } else {
      this.suggestionList.querySelectorAll('.keyword-button').forEach(element => {
        // Get suggestion name
        const suggestion = element.innerText.toLowerCase();

        // Does suggestion contain input value?
        if (suggestion.includes(value) && this.keywords.indexOf(suggestion) < 0) {
          element.hidden = false; // Show
          count++; // Count of suggestions
          if (match == '' && suggestion.startsWith(value)) match = suggestion; // Tab suggestion
        } else {
          element.hidden = true; // Hide
        }
      });
    }

    // Filter examples
    this.hints.querySelectorAll('.keyword-button').forEach(element => {
      if (this.keywords.indexOf(element.value.toLowerCase()) > -1) {
        element.hidden = true; // Hide
      } else {
        element.hidden = false; // Show
      }
    });

    // Show tab suggestion (if any)
    this.tabSuggestion.innerHTML = match;

    // Show or hide hint
    this.hints.hidden = !(value == '');

    // No matching suggestion
    this.noHint.hidden = !(value != '' && count < 1);
  }

  addNode(text = null) {
    // Get and prepare value
    const name = text ?? this.input.value.replace(',', '');
    const value = this.allowList[this.encode(name)];

    // Only allowed value and no duplicates
    if (!value || this.keywords.indexOf(value) > -1) {
      return;
    }

    // Add to keywords
    this.keywords.push(value);

    // Create node
    const node = document.createElement('div');

    node.innerHTML = `<span class="node-name">${name}</span><span class="node-delete"><i class="fa-solid fa-xmark"></i></span>`;
    node.classList.add('node');
    node.dataset.value = value;

    // Insert before input
    this.inputWrapper.parentNode.insertBefore(node, this.inputWrapper);

    // Clear input
    this.input.value = '';

    // Update hidden collection input
    this.updateCollection();

    // Filter suggestions
    this.filterSuggestions();

    // Resize input
    this.resize();
  }

  removeNodeByValue(value) {
    // Remove from DOM
    this.block.querySelector(`[data-value="${value}"]`)?.remove();

    // Remove from keywords
    this.keywords = this.keywords.filter(item => item != value);

    // Update hidden collection input
    this.updateCollection();

    // Filter suggestions
    this.filterSuggestions();

    // Resize input
    this.resize();
  }

  updateCollection() {
    // Update internal keywords
    this.collection.value = this.keywords.join(',');

    // Empty?
    this.block.classList.toggle('_empty', this.collection.value === '');

    // Reset current query info
    this.form.querySelector('.search-form__current').innerHTML = '';
  }

  bindEvents() {
    // Disabled in favor of global handler
    // if (this.operator) {
    //   this.operator.addEventListener(
    //     'change',
    //     e => {
    //       e.currentTarget.closest('label')?.setAttribute('aria-checked', e.currentTarget.checked);
    //     }
    //   );
    // }

    // Adjust width on input
    this.input.addEventListener(
      'input',
      event => {
        // Check for comma, which indicates end of input
        if (event.currentTarget.value.includes(',')) {
          this.addNode();
        }

        // Empty?
        this.block.classList.toggle('_empty', event.currentTarget.value === '');

        // Filter suggestions
        this.filterSuggestions();

        // Resize width
        this.resize();
      }
    );

    // Listen for key input
    this.input.addEventListener(
      'keydown',
      event => {
        // Enter/Tab
        if (event.keyCode == 9 || event.keyCode == 13) {
          if (this.tabSuggestion.innerText != '') {
            event.preventDefault(); // Prevent tab navigation and submit
            this.input.value = this.tabSuggestion.innerText;
            this.addNode();
          }
        }

        // Escape
        if (event.keyCode == 27) {
          this.input.value = '';
          this.tabSuggestion.innerHTML = '';
          document.activeElement.blur();
        }

        // Backspace
        if (event.keyCode == 8) {
          if (this.input.value == '' && this.keywords.length > 0) {
            this.removeNodeByValue(this.keywords.slice(-1));
          }
        }
      }
    );

    // When leaving the input
    this.input.addEventListener(
      'blur',
      () => {
        // Get and prepare value
        const value = this.allowList[this.encode(this.input.value)];

        // Stoppable to account for blur through button click
        if (value) {
          this.blurTimeout = setTimeout(() => { this.addNode() }, 150)
        } else {
          this.blurTimeout = setTimeout(() => {
            // Reset input
            this.input.value = '';
            this.tabSuggestion.innerHTML = '';

            // Filter suggestions
            this.filterSuggestions();

            // Resize width
            this.resize();
          }, 150)
        }
      }
    );

    // Click anywhere in form...
    this.block.addEventListener(
      'click',
      event => {
        // Delete node
        if (event.target.closest('.node-delete')) {
          event.preventDefault(); // Do not focus input
          this.removeNodeByValue(event.target.closest('.node').dataset.value);
        }
      }
    );

    // Suggestion buttons
    this.block.querySelectorAll('.keyword-button').forEach(element => {
      element.addEventListener(
        'click',
        event => {
          clearTimeout(this.blurTimeout); // Stop blur event
          this.addNode(event.currentTarget.innerText);
        }
      );
    });
  }
}

/**
 * Reset search form.
 *
 * @since 5.9.4
 * @param {HTMLElement} button - The reset button clicked.
 * @param {HTMLElement} form - The form to be reset.
 * @param {FCN_KeywordInput[]} keywordInputs - Array of keyword input objects.
 */

function fcn_resetSearchForm(button, form, keywordInputs) {
  // Reset keyword inputs
  keywordInputs.forEach(input => input.reset());

  // Reset selects and inputs (even after form submit)
  form.querySelectorAll('input, select').forEach(element => {
    element.value = element.dataset.default ?? element.value;
  });

  // Reset current query info
  form.querySelector('.search-form__current').innerHTML = '';

  // Notification
  fcn_showNotification(button.dataset.reset, 2);
}

// Initialize
_$$('.search-form').forEach(form => {
  // Skip if simple form
  if (form.classList.contains('_simple')) {
    return;
  }

  // Initialize each keyword input in form
  const keywordInputs = [];
  form.querySelectorAll('.keyword-input__input').forEach(element => { keywordInputs.push(new FCN_KeywordInput(element)) });

  // Remove allow list
  form.querySelector('.allow-list')?.remove();

  // Listen for form changes...
  form.addEventListener(
    'change',
    event => {
      // Empty current query info when form changes
      if (
        !event.target.classList.contains('search-form__advanced-control') &&
        !event.target.classList.contains('search-form__string')
      ) {
        form.querySelector('.search-form__current').innerHTML = '';
      }
    }
  );

  // Reset form button
  form.querySelectorAll('.reset').forEach(button => {
    button.addEventListener(
      'click', () => { fcn_resetSearchForm(button, form, keywordInputs); }
    );
  });
});

_$$('.search-form__advanced-toggle').forEach(element => {
  element.addEventListener(
    'click',
    event => {
      const toggle = event.currentTarget.closest('form');
      toggle.dataset.advanced = toggle.dataset.advanced == 'true' ? 'false' : 'true';
    }
  );
});

// =============================================================================
// DETECT KEYBOARD USER (UNNECESSARILY COMPLICATED EDITION)
// =============================================================================

/**
 * Listen for tab key to add 'user-is-tabbing' class.
 *
 * @since 5.0.4
 * @link https://medium.com/hackernoon/removing-that-ugly-focus-ring-and-keeping-it-too-6c8727fefcd2
 * @param {Event} e - The event.
 */

function fcn_handleTabInput(e) {
  if (e.keyCode == 9) {
    fcn_theBody.classList.add('user-is-tabbing');

    window.removeEventListener('keydown', fcn_handleTabInput);
    window.addEventListener('mousedown', fcn_handleMouseInput);
  }
}

/**
 * Listen for mouse click to remove 'user-is-tabbing' class.
 *
 * @since 5.0.4
 * @link https://medium.com/hackernoon/removing-that-ugly-focus-ring-and-keeping-it-too-6c8727fefcd2
 */

function fcn_handleMouseInput() {
  fcn_theBody.classList.remove('user-is-tabbing');

  window.removeEventListener('mousedown', fcn_handleMouseInput);
  window.addEventListener('keydown', fcn_handleTabInput);
}

// Initialize
window.addEventListener('keydown', fcn_handleTabInput);

// =============================================================================
// POPUP MENUS
// =============================================================================

/**
 * Positions the popup menus based on screen collision detection.
 *
 * @since 5.2.5
 */

function fcn_popupPosition() {
  // Look for technically open popup menus...
  _$$('.popup-menu-toggle.last-clicked .popup-menu:not(._fixed-position)').forEach(element => {
    // Visible?
    if (window.getComputedStyle(element).display === 'none') {
      return;
    }

    // Collision with screen borders?
    const collision = fcn_detectScreenCollision(element);

    // No collisions
    if (collision && collision.length === 0) {
      return;
    }

    // Top/Bottom?
    if (collision.includes('top')) {
      element.classList.remove('_top');
      element.classList.add('_bottom');
    } else if(collision.includes('bottom')) {
      element.classList.remove('_bottom');
      element.classList.add('_top');
    }
  });
}

// Initialize
window.addEventListener('scroll.rAF', fcn_throttle(fcn_popupPosition, 250));

// =============================================================================
// MODALS
// =============================================================================

// Toggle modals
_$$('.modal-toggle').forEach(element => {
  element.addEventListener('change', (event) => {
    const target = _$$$(event.currentTarget.dataset.target);

    // Toggle class
    target.classList.toggle('_open', event.currentTarget.checked);
    target.hidden = !event.currentTarget.checked;

    // Set focus inside modal
    const close = target.querySelector('.close');
    close?.focus();
    close?.blur();
  });
});

// =============================================================================
// SCROLL TO ANCHORS
// =============================================================================

fcn_theBody.addEventListener('click', event => {
  // Setup
  const trigger = event.target.closest('[href]');
  const href = trigger?.getAttribute('href');

  // Phew...
  if (!trigger || !trigger.tagName === 'A' || !href.startsWith('#') || href.length < 2 || href === '#respond') {
    return;
  }

  // More setup
  const target = href.replace('#', '');
  const targetElement = _$$(`[name="${target}"], #${target}`)[0];
  const storyComment = trigger.closest('.comment._story-comment');

  // Story comments need their anchors fixed
  if (storyComment) {
    window.location.href = storyComment.querySelector('.fictioneer-comment__link').href + href;
  }

  if (targetElement) {
    event.preventDefault();
    targetElement.scrollIntoView({ behavior: 'smooth', block: trigger.dataset?.block ?? 'start' });
  }
});

// =============================================================================
// MARK CURRENTLY ACTIVE MENU ITEM
// =============================================================================

/**
 * Mark currently active menu item
 *
 * @since 5.5.0
 */

function fcn_markCurrentMenuItem() {
  _$$(`.menu-item > [data-nav-object-id="${fcn_theBody.dataset.postId}"]`).forEach(element => {
    element.setAttribute('aria-current', 'page');
    element.closest('.menu-item').classList.add('current-menu-item');
  });
}

// Initialize
fcn_markCurrentMenuItem();

// =============================================================================
// AGE CONFIRMATION
// =============================================================================

if (_$$$('age-confirmation-modal') && localStorage.getItem('fcnAgeConfirmation') !== '1' && !fcn_isSearchEngineCrawler()) {
  // Delay to avoid impacting web vitals
  setTimeout(() => {
    fcn_showAgeConfirmationModal();
  }, 4000);
} else {
  _$$$('age-confirmation-modal')?.remove();
}

/**
 * Show age confirmation modal if required.
 *
 * @since 5.9.0
 */

function fcn_showAgeConfirmationModal() {
  // Adult story or chapter?
  const rating = _$('.story__article, .chapter__article')?.dataset.ageRating;

  if (!fcn_theRoot.dataset.ageConfirmation && rating && rating !== 'adult') {
    _$$$('age-confirmation-modal')?.remove();
    return;
  }

  // Setup
  const leave = _$$$('age-confirmation-leave');

  // Disable site and show modal
  fcn_theRoot.classList.add('age-modal-open');
  _$$$('age-confirmation-modal').classList.add('_open');

  // Confirm button
  _$$$('age-confirmation-confirm')?.addEventListener('click', event => {
    fcn_theRoot.classList.remove('age-modal-open');
    event.currentTarget.closest('.modal').remove();
    localStorage.setItem('fcnAgeConfirmation', '1');
  });

  // Leave button
  leave?.addEventListener('click', () => {
    window.location.href = leave.dataset.redirect ?? 'https://search.brave.com/';
    localStorage.removeItem('fcnAgeConfirmation');
  });
}
