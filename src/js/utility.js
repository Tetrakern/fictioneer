// =============================================================================
// SHORTHANDS TO GET ELEMENT(S)
// =============================================================================

/**
 * Query one (first) element by selector.
 *
 * @since 4.0
 * @alias document.querySelector
 * @type {function}
 * @const
 * @param {string} selector - The selector.
 * @return {HTMLElement} The queried element.
 */

const _$ = document.querySelector.bind(document);

/**
 * Query all elements by selector.
 *
 * @since 4.0
 * @alias document.querySelectorAll
 * @type {function}
 * @const
 * @param {string} selector - The selector.
 * @return {NodeList} List of queried elements.
 */

const _$$ = document.querySelectorAll.bind(document);

/**
 * Get an element by ID.
 *
 * @since 4.0
 * @alias document.getElementById
 * @type {function}
 * @const
 * @param {string} id - The ID.
 * @return {HTMLElement} The element.
 */

const _$$$ = document.getElementById.bind(document);

// =============================================================================
// FICTIONEER AJAX REQUESTS
// =============================================================================

/**
 * Make an AJAX POST request with the Fetch API.
 *
 * @since 5.0
 * @param {Object} data - The payload, including the action and nonce.
 * @param {String} url - Optional. The AJAX URL if different from the default.
 * @param {Object} headers - Optional. Headers for the request.
 * @return {JSON} The parsed JSON response if successful.
 */

async function fcn_ajaxPost(data = {}, url = null, headers = {}) {
  // Get URL if not provided
  if (!url) url = fictioneer_ajax.ajax_url;

  // Default headers
  final_headers = {
    'Content-Type': 'application/x-www-form-urlencoded',
    'Cache-Control': 'no-cache'
  }

  // Merge in custom headers (if any)
  final_headers = {...final_headers, ...headers};

  // Merge with default nonce
  data = {...{'nonce': fcn_getNonce()}, ...data};

  // Fetch promise
  const response = await fetch(url, {
    method: 'POST',
    credentials: 'same-origin',
    headers: final_headers,
    mode: 'same-origin',
    body: new URLSearchParams(data)
  });

  // Return response
  if (response.ok) {
    return response.json();
  } else {
    return Promise.reject(response);
  }
}

/**
 * Make an AJAX GET request with the Fetch API.
 *
 * @since 5.0
 * @param {Object} data - The payload, including the action and nonce.
 * @param {String} url - Optional. The AJAX URL if different from the default.
 * @param {Object} headers - Optional. Headers for the request.
 * @return {JSON} The parsed JSON response if successful.
 */

async function fcn_ajaxGet(data = {}, url = null, headers = {}) {
  // Build URL
  url = url ? url : fictioneer_ajax.ajax_url;
  data = {...{'nonce': fcn_getNonce()}, ...data};
  url = fcn_buildUrl(data, url);

  // Default headers
  final_headers = {
    'Content-Type': 'application/x-www-form-urlencoded',
    'Cache-Control': 'no-cache'
  }

  // Merge in custom headers (if any)
  final_headers = {...final_headers, ...headers};

  // Fetch promise
  const response = await fetch(url, {
    method: 'GET',
    credentials: 'same-origin',
    headers: final_headers,
    mode: 'same-origin'
  });

  // Return response
  if (response.ok) {
    return response.json();
  } else {
    return Promise.reject(response);
  }
}

// =============================================================================
// EVALUATE AS BOOLEAN
// =============================================================================

/**
 * Evaluate anything as boolean with a fallback value.
 *
 * @since 5.0
 * @param {Any} candidate - The boolean hopeful to be evaluated.
 * @param {Boolean} fallback - Optional. The fallback boolean, default false.
 * @return {Boolean} True or false.
 */

function fcn_evaluateAsBoolean(candidate, fallback = false) {
  // Is the candidate a boolean?
  if (typeof candidate === 'boolean') return candidate;

  // Does the candidate even exist?
  if (typeof candidate === 'undefined') return fallback;

  // Is the candidate a checkbox?
  if (candidate instanceof HTMLInputElement && candidate.getAttribute('type') === 'checkbox') return candidate.checked;

  // Is the candidate an element with (data-) value?
  if (candidate instanceof HTMLElement) {
    if (candidate.hasAttribute('value')) {
      candidate = candidate.value;
    } else if (candidate.hasAttribute('data-value')) {
      candidate = candidate.dataset.value;
    }
  }

  // Truthy/Falsy value?
  let s = String(candidate),
      i = parseInt(candidate);
  if (s === 'true' || s === '1' || i === 1) return true;
  if (s === 'false' || s === '0' || i === 0) return false;

  // Return fallback if nothing sticks
  return fallback;
}

// =============================================================================
// COPY TO CLIPBOARD
// =============================================================================

/**
 * Copy valid to clipboard and optionally show a notice.
 *
 * @since 4.0
 * @param {String} text - The text to be copied.
 * @param {String} [message] - Optional notice to show.
 */

function fcn_copyToClipboard(text, message = false) {
  message = message ? message : __('Copied to clipboard!', 'fictioneer');

  if (navigator.clipboard) {
    navigator.clipboard.writeText(text);
    if (message) fcn_showNotification(message, 2);
  }
}

// =============================================================================
// IS JSON VALID?
// =============================================================================

/**
 * Is a string a valid JSON?
 *
 * @since 4.0
 * @param {String} str - The string to test.
 * @return {Boolean} True if valid, false of not.
 */

function fcn_isValidJSONString(str) {
  try {
    JSON.parse(str);
  } catch (e) {
    return false;
  }
  return true;
}

// =============================================================================
// REMOVE ITEM FROM ARRAY ONCE
// =============================================================================

/**
 * Remove item from array once.
 *
 * @since 4.0
 * @param {Any[]} array - The array from which to remove the item from.
 * @param {Any} value - The value of the item to remove.
 * @return {Any[]} The modified array.
 */

function fcn_removeItemOnce(array, value) {
  var index = array.indexOf(value);
  if (index > -1) array.splice(index, 1);
  return array;
}

// =============================================================================
// CLAMP
// =============================================================================

/**
 * Clamp a number between a minimum and a maximum.
 *
 * @since 4.0
 * @param {Number} min - The minimum.
 * @param {Number} max - The maximum.
 * @param {Number} val - The value to clamp.
 */

function fcn_clamp(min, max, val) {
  return Math.min(Math.max(val, min), max);
}

// =============================================================================
// UPDATE THEME COLOR META TAG
// =============================================================================

/**
 * Update theme color meta tag.
 *
 * @since 4.0
 * @param {String|Boolean} [color=false] - Optional color code.
 */

function fcn_updateThemeColor(color = false) {
  let themeColor = fcn_cssVars.getPropertyValue('--theme-color-base').trim().split(' '),
      darken = fcn_siteSettings['darken'] ? fcn_siteSettings['darken'] : 0,
      saturation = fcn_siteSettings['saturation'] ? fcn_siteSettings['saturation'] : 0,
      hueRotate = fcn_siteSettings['hue-rotate'] ? fcn_siteSettings['hue-rotate'] : 0,
      d = darken >= 0 ? 1 + Math.pow(darken, 2) : 1 - Math.pow(darken, 2);
      s = saturation >= 0 ? 1 + Math.pow(saturation, 2) : 1 - Math.pow(saturation, 2);

  themeColor = `hsl(${(parseInt(themeColor[0]) + hueRotate) % 360}deg ${(parseInt(themeColor[1]) * s).toFixed(2)}% ${(parseInt(themeColor[2]) * d).toFixed(2)}%)`;

  _$('meta[name=theme-color]').setAttribute('content', color || themeColor);
}

// =============================================================================
// GET ELEMENT OFFSET TO DOCUMENT
// =============================================================================

/**
 * @typedef {Object} TopLeftPosition
 * @property {number} left - The X Coordinate.
 * @property {number} top - The Y Coordinate.
 * @inner
 */

/**
 * Returns the top-left position of an element relative to the window.
 *
 * @since 4.0
 * @param {HTMLElement} element - Element to get the position for.
 * @return {TopLeftPosition} Top-left position of the element.
 */

function fcn_offset(element) {
  let rect = element.getBoundingClientRect();

  return {
    top: rect.top + window.scrollY,
    left: rect.left + window.scrollX
  };
}

// =============================================================================
// THROTTLE FUNCTION
// =============================================================================

/**
 * Throttle event for improved performance.
 *
 * @since 4.1
 * @link https://github.com/jashkenas/underscore/blob/master/underscore.js
 * @link https://stackoverflow.com/a/27078401/17140970
 * @param {Function} func - The function to throttle.
 * @param {Number} wait - Throttle threshold.
 * @param {Object{}} options - Optional arguments.
 */

function fcn_throttle(func, wait, options) {
  var context,
      args,
      result,
      timeout = null,
      previous = 0;

  if (!options) options = {};

  var later = function() {
    previous = options.leading === false ? 0 : Date.now();
    timeout = null;
    result = func.apply(context, args);
    if (!timeout) context = args = null;
  }

  return function() {
    var now = Date.now();
    if (!previous && options.leading === false) previous = now;
    var remaining = wait - (now - previous);
    context = this;
    args = arguments;
    if (remaining <= 0 || remaining > wait) {
      if (timeout) {
        clearTimeout(timeout);
        timeout = null;
      }
      previous = now;
      result = func.apply(context, args);
      if (!timeout) context = args = null;
    } else if (!timeout && options.trailing !== false) {
      timeout = setTimeout(later, remaining);
    }
    return result;
  }
}

// =============================================================================
// BIND EVENT TO ANIMATION FRAME
// =============================================================================

/**
 * Bind event to animation frame for improved performance.
 *
 * @since 4.1
 * @param {String} type - The event type, e.g. 'scroll' or 'resize'.
 * @param {String} name - Name of the bound event.
 * @param {HTMLElement} [obj=window] - Target of the event listener.
 */

function fcn_bindEventToAnimationFrame(type, name, obj = window) {
  var running = false;

  var func = function() {
    if (running) return;

    running = true;

    requestAnimationFrame(() => {
      obj.dispatchEvent(new CustomEvent(name));
      running = false;
    });
  };

  obj.addEventListener(type, func);
}

// =============================================================================
// TOGGLE LAST CLICKED
// =============================================================================

var /** @const {HTMLElement} */ fcn_lastClicked;

/**
 * Toggle the 'last-clicked' class on an element.
 *
 * @since 4.0
 * @param {HTMLElement} element - The clicked element.
 */

function fcn_toggleLastClicked(element) {
  element.classList.toggle('last-clicked', !element.classList.contains('last-clicked'));

  if (fcn_lastClicked && fcn_lastClicked != element) {
    fcn_lastClicked.classList.remove('last-clicked');
  }

  fcn_lastClicked = element;
}

// Listen for clicks on elements with the 'toggle-last-clicked' class
_$$('.toggle-last-clicked').forEach(element => {
  element.addEventListener(
    'click',
    e => {
      fcn_toggleLastClicked(e.currentTarget);
      e.stopPropagation();
    }
  );
});

// Listen for click on the <body>
_$('body').addEventListener(
  'click',
  e => {
    if (
      e.currentTarget.classList.contains('escape-last-click') ||
      e.target.closest('.escape-last-click') !== null
    ) return;

    if (fcn_lastClicked && e.currentTarget != fcn_lastClicked) {
      fcn_lastClicked.classList.remove('last-clicked');
      fcn_lastClicked = null;
    }
  }
);

// Listen for escape key
_$('body').addEventListener(
  'keydown',
  e => {
    if (e.keyCode == 27 && fcn_lastClicked) {
      fcn_lastClicked.classList.remove('last-clicked');
      fcn_lastClicked = null;
      document.activeElement?.blur();
    }
  }
);

// =============================================================================
// REMOVE PARAGRAPH TOOLS BUTTONS FROM TEXT SELECTION
// =============================================================================

/**
 * Clean text selection from paragraph tools buttons.
 *
 * @description Probably the worst regex operation you have ever seen! This
 * needs to make sure to not accidentally remove content since, while not
 * common, the button labels may occur in the chapter text intentionally.
 *
 * @since 4.0
 * @param {String} selection - The text selection to be cleaned.
 * @return {String} The cleaned text selection.
 */

function fcn_cleanTextSelectionFromButtons(selection) {
  // Mark buttons based on line break pattern
  selection = selection.replace(/[\r\n]{2,}/g, '__$__')

  // Delete different possible button sets
  selection = selection.replace(new RegExp('(__Bookmark|__Quote|__Link)', 'g'), '');
  selection = selection.replace(new RegExp('(__Bookmark|__Quote|__TTS|__Link)', 'g'), '');
  selection = selection.replace(new RegExp('(__Bookmark|__Quote|__Suggestion|__TTS|__Link)', 'g'), '');
  selection = selection.replace(new RegExp('(__Bookmark|__Quote|__Suggestion|__Link)', 'g'), '');
  selection = selection.replace(/[__$]{1,}/g, '\n\n').replace(/^[\r\n]+|[\r\n]+$/g, '');

  // Return cleaned string
  return selection;
}

// =============================================================================
// DELETE COOKIES
// =============================================================================

/**
 * Delete a cookie.
 *
 * @since 4.7
 * @param {String} cname - Name of the cookie to delete.
 */

function fcn_deleteCookie(cname) {
  // Delete cookie by setting expiration date to the past
  document.cookie = cname + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/';
}

/**
 * Delete all cookies.
 *
 * @since 4.7
 * @link https://stackoverflow.com/questions/179355/clearing-all-cookies-with-javascript
 */

function fcn_deleteAllCookies() {
  // Clear local storage as well
  localStorage.clear();

  // Delete cookies by setting expiration date to the past
  document.cookie.split(';').forEach(
    (c) => {
      document.cookie = c.replace(/^ +/, '').replace(/=.*/, '=;expires=' + new Date().toUTCString() + ';path=/');
    }
  );
}

// =============================================================================
// SET COOKIE
// =============================================================================

/**
 * Set a cookie.
 *
 * @since 4.7
 * @param {String} cname - Name of the cookie to set.
 * @param {String} value - Value of the cookie to set.
 * @param {Number} [days=30] - Days the cookie will last.
 */

function fcn_setCookie(cname, value, days = 30) {
  let d = new Date();
  d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));

  let expires = 'expires=' + d.toUTCString();

  document.cookie = cname + '=' + encodeURIComponent(value) + ';' + expires + ';SameSite=Strict;path=/';
}

// =============================================================================
// GET COOKIE
// =============================================================================

/**
 * Retrieves the given cookie if available.
 *
 * @since 4.7
 * @param {String} cname - Name of the cookie to retrieve.
 * @return {String|Null} The cookie or null if not found.
 */

function fcn_getCookie(cname) {
  let name = cname + '=',
      cookies = document.cookie.split(';');

  for (var i = 0; i < cookies.length; i++) {
    let c = cookies[i].trim();

    if (c.indexOf(name) == 0) {
      return decodeURIComponent(c.substring(name.length, c.length));
    }
  }

  return null;
}

// =============================================================================
// CHECK IF URL IS VALID (SIMPLE)
// =============================================================================

/**
 * Very simple check whether an URL is valid.
 *
 * @since 4.7
 * @return {Boolean} True of the URL is valid, false otherwise.
 */

function fcn_isValidUrl(url) {
  if (!url) return false;
  return(url.match(/^(https?:\/\/)/) != null);
}

// =============================================================================
// GET NONCE
// =============================================================================

// This nonce is always included in the template file but may not be correct
// if caching is active. Some caching plugins can localize nonces if you add
// them to a special list. Otherwise use nonce deferment in the options.
var fcn_defaultNonce = _$$$('fictioneer-nonce')?.value ?? 0;

/**
 * Return the Fictioneer nonce, accounting for dynamic nonces.
 *
 * @since 5.0
 * @return {String} The fictioneer_nonce value.
 */

function fcn_getNonce() {
  return _$$$('fictioneer-ajax-nonce')?.value ?? fcn_defaultNonce;
}

// =============================================================================
// MATCH FINGERPRINT
// =============================================================================

/**
 * Match a fingerprint with the local user fingerprint if set.
 *
 * @since 5.0
 * @param {String} fingerprint - The fingerprint to match with.
 * @return {Boolean} True or false
 */

function fcn_matchFingerprint(fingerprint) {
  if (typeof fcn_fingerprint === 'undefined') return false;
  return fcn_fingerprint.fingerprint === fingerprint;
}

// =============================================================================
// BUILD URL
// =============================================================================

/**
 * Build new URL with search parameters.
 *
 * @since 5.0
 * @param {Object} [params=] - Optional. Search params to add.
 * @param {String|null} [url=] - Optional. The base URL string.
 * @return {URL} The built URL.
 */

function fcn_buildUrl(params = {}, url = null) {
  // Setup
  url = url ? new URL(url) : new URL(
    window.location.protocol + '//' + window.location.host + window.location.pathname
  );

  // Append parameters
  if (params) {
    Object.keys(params).forEach(key => {
      url.searchParams.append(key, params[key]);
    });
  }

  return url;
}

// =============================================================================
// GET ERROR NOTICE
// =============================================================================

/**
 * Build error message notice to add to the DOM.
 *
 * @since 5.0
 * @param {String} message - Message of the error notice.
 * @param {String} id - Optional. ID of the element.
 * @return {HTMLElement} Error notice to be added.
 */

function fcn_buildErrorNotice(message, id = false) {
  // Setup
  let notice = document.createElement('div'),
      text = message;

  if (id) notice.id = id;

  notice.classList = 'notice _warning';

  // Check if message is error object
  if (typeof message == 'object') {
    text = '';
    if (message.status) text = `${message.status}: `;
    if (message.statusText) text += message.statusText;
    if (!text) text = 'Unknown error.';
  }

  // Build and return
  notice.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i><div>${fcn_sanitizeHTML(text)}</div>`;
  return notice;
}

// =============================================================================
// SANITIZE HTML
// =============================================================================

/**
 * Returns a sanitized HTML string.
 *
 * @description Converts special characters such as brackets to HTML entities,
 * rendering any cross-site scripting attempts ineffective.
 *
 * @since 5.0.5
 * @param {String|HTMLElement} html - The HTML (element) to sanitize.
 * @return {String} Sanitized HTML string.
 */

function fcn_sanitizeHTML(html) {
  let temp = document.createElement('div');
  temp.innerText = html instanceof HTMLElement ? html.innerHTML : html;
  return temp.innerHTML;
}

// =============================================================================
// RESIZE INPUTS
// =============================================================================

/**
 * Resize input width based on value.
 *
 * @since 5.0
 * @param {HTMLElement} input - The input element.
 * @param {Number|null} [size=] - Optional size in characters.
 */

function fcn_resizeInput(input, size = null) {
  size = size ? size : input.value.length;
  input.style.width = size * 0.88 + 2 + 'ch'; // Good enough
}

// =============================================================================
// ARIA CHECKED
// =============================================================================

/*
 * Update ariaChecked when the state changes.
 */

function fcn_ariaCheckedUpdate(source) {
  let target = source.closest('[role="checkbox"][aria-checked]');

  if (target) {
    let checked = fcn_evaluateAsBoolean(source);
    target.ariaChecked = checked;
  }
}

// =============================================================================
// SCROLL ANCHOR INTO VIEW
// =============================================================================

/*
 * Scroll smoothly to anchor.
 *
 * @since 5.2.0
 * @param {HTMLElement} source - Clicked element.
 *
 * @return false
 */

function fcn_scrollToAnchor(source) {
  _$(`[name="${source.getAttribute('href').replace('#', '')}"]`).scrollIntoView({ behavior: 'smooth', block: 'center' });
  return false; // Prevent default link behavior
}

// =============================================================================
// SCREEN COLLISION DETECTION
// =============================================================================

/**
 * Detects if an element is about to leave the visible screen and returns the collision directions.
 *
 * @since 5.2.5
 * @param {HTMLElement} element - The element to check for collision.
 * @returns {Array} - Array of collision directions ('top', 'bottom', 'left', 'right').
 */

function fcn_detectScreenCollision(element) {
  let rect = element.getBoundingClientRect(),
      viewportHeight = window.innerHeight ?? document.documentElement.clientHeight,
      viewportWidth = window.innerWidth ?? document.documentElement.clientWidth,
      threshold = 50,
      result = [];

  if (rect.top <= threshold) result.push('top');
  if (rect.bottom >= viewportHeight - threshold) result.push('bottom');
  if (rect.left <= threshold) result.push('left');
  if (rect.right >= viewportWidth - threshold) result.push('right');

  return result;
}
