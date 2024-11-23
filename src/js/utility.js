// =============================================================================
// SHORTHANDS TO GET ELEMENT(S)
// =============================================================================

/**
 * Query one (first) element by selector.
 *
 * @since 4.0.0
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
 * @since 4.0.0
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
 * @since 4.0.0
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
 * Make a POST request with the Fetch API
 *
 * @since 5.0.0
 * @param {Object} data - The payload, including the action and nonce.
 * @param {String} [url] - Optional. The request URL if different from the default.
 * @param {Object} [headers] - Optional. Headers for the request.
 * @return {Promise} A Promise that resolves to the parsed JSON response if successful.
 */

async function fcn_ajaxPost(data = {}, url = null, headers = {}) {
  // Auto-complete REST request if not a full URL
  if (url && !url.startsWith('http')) {
    url = FcnGlobals.restURL + url;
  }

  // Get URL if not provided
  url = url ? url : FcnGlobals.ajaxURL;

  // Default headers
  let final_headers = {
    'Content-Type': 'application/x-www-form-urlencoded',
    'Cache-Control': 'no-cache'
  };

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
 * Make a GET request with the Fetch API.
 *
 * @since 5.0.0
 * @param {Object} data - The payload, including the action and nonce.
 * @param {String} [url] - Optional. The request URL if different from the default.
 * @param {Object} [headers] - Optional. Headers for the request.
 * @return {JSON} The parsed JSON response if successful.
 */

async function fcn_ajaxGet(data = {}, url = null, headers = {}) {
  // Auto-complete REST request if not a full URL
  if (url && !url.startsWith('http')) {
    url = FcnGlobals.restURL + url;
  }

  // Build URL
  url = url ? url : FcnGlobals.ajaxURL;
  data = {...{'nonce': fcn_getNonce()}, ...data};
  url = fcn_buildUrl(data, url);

  // Default headers
  let final_headers = {
    'Content-Type': 'application/x-www-form-urlencoded',
    'Cache-Control': 'no-cache'
  };

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
// COPY TO CLIPBOARD
// =============================================================================

/**
 * Copy valid to clipboard and optionally show a notice.
 *
 * @since 4.0.0
 * @param {String} text - The text to be copied.
 * @param {String} [message] - Optional notice to show.
 */

function fcn_copyToClipboard(text, message = false) {
  message = message ? message : fictioneer_tl.notification.copiedToClipboard;

  if (navigator.clipboard) {
    navigator.clipboard.writeText(text);

    if (message) {
      fcn_showNotification(message, 2);
    }
  }
}

// =============================================================================
// PARSE JSON
// =============================================================================

/**
 * Parse JSON and account for invalid strings.
 *
 * @since 5.7.0
 * @param {String} str - The string to parse.
 * @return {Object|null} Parsed JSON or null if not a JSON string.
 */

function fcn_parseJSON(str) {
  if (str === null || typeof str === 'undefined' || typeof str !== 'string') {
    return null;
  }

  try {
    return JSON.parse(str);
  } catch (e) {
    return null;
  }
}

// =============================================================================
// REMOVE ITEM FROM ARRAY ONCE
// =============================================================================

/**
 * Remove item from array once.
 *
 * @since 4.0.0
 * @param {Any[]} array - The array from which to remove the item from.
 * @param {Any} value - The value of the item to remove.
 * @return {Any[]} The modified array.
 */

function fcn_removeItemOnce(array, value) {
  var index = array.indexOf(value);

  if (index > -1) {
    array.splice(index, 1);
  }

  return array;
}

// =============================================================================
// CLAMP
// =============================================================================

/**
 * Clamp a number between a minimum and a maximum.
 *
 * @since 4.0.0
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
 * @since 4.0.0
 * @param {String|Boolean} [color=false] - Optional color code.
 */

function fcn_updateThemeColor(color = false) {
  const darken = fcn_siteSettings['darken'] ? fcn_siteSettings['darken'] : 0;
  const saturation = fcn_siteSettings['saturation'] ? fcn_siteSettings['saturation'] : 0;
  const hueRotate = fcn_siteSettings['hue-rotate'] ? fcn_siteSettings['hue-rotate'] : 0;
  const d = darken >= 0 ? 1 + darken ** 2 : 1 - darken ** 2;
  const s = saturation >= 0 ? 1 + saturation ** 2 : 1 - saturation ** 2;

  let themeColor = getComputedStyle(document.documentElement).getPropertyValue('--theme-color-base').trim().split(' ');

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
 * @since 4.0.0
 * @param {HTMLElement} element - Element to get the position for.
 * @return {TopLeftPosition} Top-left position of the element.
 */

function fcn_offset(element) {
  const rect = element.getBoundingClientRect();

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
 * @since 4.1.0
 * @link https://github.com/jashkenas/underscore/blob/master/underscore.js
 * @link https://stackoverflow.com/a/27078401/17140970
 * @param {Function} func - The function to throttle.
 * @param {Number} wait - Throttle threshold.
 * @param {Object{}} options - Optional arguments.
 */

function fcn_throttle(func, wait, options) {
  var context;
  var args;
  var result;
  var timeout = null;
  var previous = 0;

  if (!options) {
    options = {};
  }

  var later = function() {
    previous = options.leading === false ? 0 : Date.now();
    timeout = null;
    result = func.apply(context, args);

    if (!timeout) {
      context = args = null;
    }
  }

  return function() {
    var now = Date.now();

    if (!previous && options.leading === false) {
      previous = now;
    }

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

      if (!timeout) {
        context = args = null;
      }

    } else if (!timeout && options.trailing !== false) {
      timeout = setTimeout(later, remaining);
    }

    return result;
  }
}

// =============================================================================
// BIND EVENT TO ANIMATION FRAME
// =============================================================================

var /** @const {Map} */ fcn_animFrameEvents = new Map();

/**
 * Bind event to animation frame for improved performance.
 *
 * @since 4.1.0
 * @param {String} type - The event type, e.g. 'scroll' or 'resize'.
 * @param {String} name - Name of the bound event.
 * @param {HTMLElement} [obj=window] - Target of the event listener.
 */

function fcn_bindEventToAnimationFrame(type, name, obj = window) {
  var func = function() {
    if (fcn_animFrameEvents.get(name)) {
      return;
    }

    fcn_animFrameEvents.set(name, true);

    requestAnimationFrame(() => {
      obj.dispatchEvent(new CustomEvent(name));
      fcn_animFrameEvents.set(name, false);
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
 * @since 4.0.0
 * @param {HTMLElement} element - The clicked element.
 */

function fcn_toggleLastClicked(element) {
  const set = !element.classList.contains('last-clicked');

  element.classList.toggle('last-clicked', set);
  element.closest('.watch-last-clicked')?.classList.toggle('has-last-clicked', set);

  if (fcn_lastClicked && fcn_lastClicked != element) {
    fcn_removeLastClick(fcn_lastClicked);
  }

  fcn_lastClicked = element;

  document.dispatchEvent(new CustomEvent('fcnLastClicked', { detail: { element } }));
}

/**
 * Removes last-clicked classes from an element and parents.
 *
 * @since 5.4.1
 * @param {HTMLElement} target - The element to clean.
 */

function fcn_removeLastClick(target) {
  target.closest('.watch-last-clicked')?.classList.remove('has-last-clicked');
  target.classList.remove('last-clicked');
  fcn_lastClicked = null;
}

/**
 * Clicks on elements with the 'toggle-last-clicked' class are handled by the
 * global click handler in application.js to avoid rebinding cases.
 */

// Listen for click outside
_$('body').addEventListener(
  'click',
  e => {
    const target = e.target.closest('.toggle-last-clicked');

    if (
      (!['BUTTON', 'A'].includes(e.target.tagName) && target) ||
      e.target.closest('.escape-last-click') !== null
    ) {
      return;
    }

    if (fcn_lastClicked && target != fcn_lastClicked) {
      fcn_removeLastClick(fcn_lastClicked);
    }
  }
);

// Listen for escape key
_$('body').addEventListener(
  'keydown',
  e => {
    if (e.key == 'Escape' && fcn_lastClicked) {
      fcn_removeLastClick(fcn_lastClicked);
      document.activeElement?.blur();
    }
  }
);

// Hover over main menu
_$$$('full-navigation')?.addEventListener('mouseover', () => {
  if (fcn_lastClicked) {
    fcn_removeLastClick(fcn_lastClicked);
    document.activeElement?.blur();
  }
});

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
 * @since 4.0.0
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
// COOKIES
// =============================================================================

/**
 * Delete a cookie.
 *
 * @since 4.7.0
 * @param {String} cname - Name of the cookie to delete.
 */

function fcn_deleteCookie(cname) {
  // Delete cookie by setting expiration date to the past
  document.cookie = cname + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/';
}

/**
 * Delete all cookies.
 *
 * @since 4.7.0
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

/**
 * Set a cookie.
 *
 * @since 4.7.0
 * @param {String} cname - Name of the cookie to set.
 * @param {String} value - Value of the cookie to set.
 * @param {Number} [days=30] - Days the cookie will last.
 */

function fcn_setCookie(cname, value, days = 30) {
  const d = new Date();

  d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));

  const expires = 'expires=' + d.toUTCString();

  document.cookie = cname + '=' + encodeURIComponent(value) + ';' + expires + ';SameSite=Strict;path=/';
}

/**
 * Retrieves the given cookie if available.
 *
 * @since 4.7.0
 * @param {String} cname - Name of the cookie to retrieve.
 * @return {String|Null} The cookie or null if not found.
 */

function fcn_getCookie(cname) {
  const name = cname + '=';
  const cookies = document.cookie.split(';');

  for (var i = 0; i < cookies.length; i++) {
    const c = cookies[i].trim();

    if (c.indexOf(name) == 0) {
      return decodeURIComponent(c.substring(name.length, c.length));
    }
  }

  return null;
}

// =============================================================================
// GET NONCE
// =============================================================================

/**
 * Return the Fictioneer nonce, accounting for dynamic nonces.
 *
 * @since 5.0.0
 * @return {String} The nonce value.
 */

function fcn_getNonce() {
  return _$$$('fictioneer-ajax-nonce')?.value ??
    _$$$('general-fictioneer-nonce')?.value ??
    _$('[name="fictioneer_nonce"]')?.value ?? 0;
}

// =============================================================================
// BUILD URL
// =============================================================================

/**
 * Build new URL with search parameters.
 *
 * @since 5.0.0
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
// ERROR NOTICE
// =============================================================================

/**
 * Build error message notice to add to the DOM.
 *
 * @since 5.0.0
 * @param {String} message - Message of the error notice.
 * @param {String} [id=false] - Optional. ID of the element.
 * @param {Boolean} [sanitize=true] - Optional. Whether to sanitize the HTML.
 * @return {HTMLElement} Error notice to be added.
 */

function fcn_buildErrorNotice(message, id = false, sanitize = true) {
  // Always output on console
  console.error('Error:', message);

  // Setup
  const notice = document.createElement('div');
  let text = message;

  if (id) {
    notice.id = id;
  }

  notice.classList = 'notice _warning';

  // Check if message is error object
  if (typeof message == 'object') {
    text = '';

    if (message.status) {
      text = `${message.status}: `;
    }

    if (message.statusText) {
      text += message.statusText;
    }

    if (!text) {
      text = 'Unknown error.';
    }
  }

  // Build and return
  notice.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i><div>${sanitize ? fcn_sanitizeHTML(text) : text}</div>`;
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
  const temp = document.createElement('div');

  temp.innerText = html instanceof HTMLElement ? html.innerHTML : html;
  temp.textContent = typeof html === 'string' ? html : html.textContent;

  return temp.innerHTML;
}

// =============================================================================
// SCREEN COLLISION DETECTION
// =============================================================================

/**
 * Detects if an element is about to leave the visible screen
 * and returns the collision directions. Threshold: 50px.
 *
 * @since 5.2.5
 * @since 5.18.0 - Add horizontal collisions.
 * @param {HTMLElement} element - The element to check for collision.
 * @returns {Array} - Array of collision directions ('top', 'bottom', 'left', 'right').
 */

function fcn_detectScreenCollision(element) {
  const rect = element.getBoundingClientRect();
  const viewportHeight = window.innerHeight ?? document.documentElement.clientHeight;
  const viewportWidth = window.innerWidth ?? document.documentElement.clientWidth;
  const verticalOffset = (element.closest('.popup-menu-toggle')?.clientHeight ?? 32) + 16;
  const bottomSpacing = viewportHeight - rect.bottom;
  const result = [];

  if (rect.top <= 50 && bottomSpacing > 50 + verticalOffset) {
    result.push('top');
  }

  if (rect.bottom >= viewportHeight - 50 && rect.top > 50 + verticalOffset) {
    result.push('bottom');
  }

  if (rect.left <= 10) {
    result.push('left');
  }

  if (rect.right >= viewportWidth - 10) {
    result.push('right');
  }

  return result;
}

// =============================================================================
// SCROLL TO ELEMENT
// =============================================================================

/**
 * Scroll to a specific target on the page.
 *
 * @since 5.4.0
 *
 * @param {HTMLElement} target - The target element to scroll to.
 * @param {Number} [offset=64] - Optional. Offset from top.
 */

function fcn_scrollTo(target, offset = 64) {
  // Scroll to position - offset
  window.scrollTo({
    top: target.getBoundingClientRect().top + window.scrollY - offset,
    behavior: 'smooth'
  });
}

// =============================================================================
// CONVERT HTML STRING TO DOM ELEMENT
// =============================================================================

/**
 * Creates an HTML element from a template string.
 *
 * @since 5.4.5
 * @param {...string} args - The template strings and placeholders.
 * @return {HTMLElement} The HTML element created from the template string.
 *
 * @example
 * const element = fcn_html`
 *   <div class="my-element">
 *     <h1>Title</h1>
 *     <p>Content</p>
 *   </div>
 * `;
 *
 * document.body.appendChild(element);
 */

function fcn_html(...args) {
  const template = document.createElement('template');
  template.innerHTML = String.raw(...args).trim();

  return template.content.firstChild;
}

// =============================================================================
// CHECK FOR SEARCH CRAWLER
// =============================================================================

/**
 * Checks if the user is a crawler (if JS is enabled)
 *
 * @returns {Boolean} - True or false.
 */

function fcn_isSearchEngineCrawler() {
  const userAgent = navigator.userAgent.toLowerCase();
  const crawlers = [
    'googlebot', // Google
    'bingbot', // Bing
    'slurp', // Yahoo
    'duckduckbot', // DuckDuckGo
    'baiduspider', // Baidu
    'yandexbot', // Yandex
    'sogou', // Sogou
    'exabot', // Exalead
    'facebot', // Facebook
    'ia_archiver' // Alexa Internet
  ];

  return crawlers.some(crawler => userAgent.includes(crawler));
}

// =============================================================================
// PROGRESSIVE ELEMENT STATUS
// =============================================================================

/**
 * Toggles progression state of an element.
 *
 * @since 5.26.0
 * @param {HTMLElement} element - The element.
 * @param {Boolean|null} force - Whether to disable or enable. Defaults to
 *                               the opposite of the current state.
 */

function fcn_toggleInProgress(element, force = null) {
  force = force !== null ? force : !element.disabled;

  if (force) {
    element.dataset.enableWith = element.innerHTML;
    element.innerHTML = element.dataset.disableWith ?? 'Processing';
    element.disabled = true;
    element.classList.add('disabled');
  } else {
    element.innerHTML = element.dataset.enableWith;
    element.disabled = false;
    element.classList.remove('disabled');
  }
}

// =============================================================================
// TEXTAREAS
// =============================================================================

/**
 * Adjust textarea height to fit the value without vertical scroll bar.
 *
 * @since 4.7.0
 * @param {HTMLElement} area - The textarea element to adjust.
 */

function fcn_adjustTextarea(area) {
  if (area) {
    area.style.height = 'auto'; // Reset if lines are removed
    area.style.height = `${area.scrollHeight}px`;
  }
}
