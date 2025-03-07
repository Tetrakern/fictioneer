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
// UTILITY OBJECT
// =============================================================================

const FcnUtils = {
  userDataDefaults: {
    'lastLoaded': 0,
    'timestamp': 0,
    'loggedIn': 'pending',
    'follows': false,
    'reminders': false,
    'checkmarks': false,
    'bookmarks': null,
    'likes': null,
    'fingerprint': false,
    'nonceHtml': '',
    'nonce': '',
    'isAdmin': false,
    'isModerator': false,
    'isAuthor': false,
    'isEditor': false
  },

  /**
   * Returns or prepares locally cached user data.
   *
   * @since 5.27.0
   * @return {Object} The user data.
   */

  userData() {
    const data = FcnUtils.parseJSON(localStorage.getItem('fcnUserData')) || {};
    return { ...FcnUtils.userDataDefaults, ...data };
  },

  /**
   * Returns an array or converts object values into an array.
   *
   * @since 5.28.0
   * @param {Array|Object} data - Array or object.
   * @return {Array} The array.
   */

  ensureArray(data) {
    return Array.isArray(data) ? data : Object.values(data);
  },

  /**
   * Reset local user data.
   *
   * @since 5.27.0
   */

  resetUserData() {
    const data = FcnUtils.parseJSON(localStorage.getItem('fcnUserData')) || {};

    const reset = {
      'lastLoaded': data.lastLoaded ?? 0,
      'loggedIn': (FcnGlobals.ajaxAuth && data.loggedIn === false) ? false : 'pending'
    };

    localStorage.setItem('fcnUserData', JSON.stringify({ ...data, ...FcnUtils.userDataDefaults, ...reset }));
  },

  /**
   * Remove user data in web storage.
   *
   * @since 5.27.0
   */

  removeUserData() {
    localStorage.removeItem('fcnUserData');
  },

  /**
   * Update user data in web storage.
   *
   * @since 5.27.0
   * @param {Object} data - User data.
   */

  setUserData(data) {
    localStorage.setItem('fcnUserData', JSON.stringify(data));
  },

  loggedInCache: null,
  loggedInCacheTime: 0,

  /**
   * Returns whether the user is logged in (cookie).
   *
   * Note: This function temporarily stores the result for
   * 20 ms to avoid having to process the cookie on each
   * subsequent call in such a short time span.
   *
   * @since 5.27.0
   * @return {Boolean} True or false
   */

  loggedIn() {
    const now = Date.now();

    if (FcnUtils.loggedInCache !== null && now - FcnUtils.loggedInCacheTime < 20) {
      return FcnUtils.loggedInCache;
    }

    FcnUtils.loggedInCache = FcnUtils.hasLoginCookie();
    FcnUtils.loggedInCacheTime = now;

    return FcnUtils.loggedInCache;
  },

  /**
   * Returns whether the sidecar login cookie is set.
   *
   * @since 5.27.0
   * @return {Boolean} True or false
   */

  hasLoginCookie() {
    const cookies = document.cookie.split(';');

    for (let i = 0; i < cookies.length; i++) {
      let cookie = cookies[i].trim();

      if (cookie.indexOf('fcnLoggedIn=') !== -1) {
        return true;
      }
    }

    return false;
  },

  /**
   * Wrapper for utility fcn_ajaxGet().
   *
   * @since 5.27.0
   * @param {Object} data - The payload, including the action and nonce.
   * @param {String} [url=null] - Optional. The request URL if different from the default.
   * @param {Object} [headers={}] - Optional. Headers for the request.
   * @return {Promise<JSON>} A Promise that resolves to the parsed JSON response.
   */

  async aGet(data = {}, url = null, headers = {}) {
    try {
      const response = fcn_ajaxGet(data, url, headers);
      return response;
    } catch (error) {
      console.error('aGet Error:', error);
      throw error;
    }
  },

  /**
   * Wrapper for utility fcn_ajaxPost().
   *
   * @since 5.27.0
   * @param {Object} data - The payload, including the action and nonce.
   * @param {String} [url=null] - Optional. The request URL if different from the default.
   * @param {Object} [headers={}] - Optional. Headers for the request.
   * @return {Promise<JSON>} A Promise that resolves to the parsed JSON response.
   */

  async aPost(data = {}, url = null, headers = {}) {
    try {
      const response = await fcn_ajaxPost(data, url, headers);
      return response;
    } catch (error) {
      console.error('aPost Error:', error);
      throw error;
    }
  },

  /**
   * AJAX: Helper to execute a POST action.
   *
   * @since 5.27.0
   * @param {string} action - The action to perform.
   * @param {Object} [options] - Optional. Additional options for the request.
   * @param {HTMLElement|null} [options.element=null] - Optional. Element to mark as "in-progress".
   * @param {Function|null} [options.callback=null] - Optional. Callback with response and element.
   * @param {Function|null} [options.errorCallback=null] - Optional. Callback with error and element.
   * @param {Function|null} [options.finalCallback=null] - Optional. Callback at the end with element.
   * @param {string|null} [options.nonce=null] - Optional. The nonce to use.
   * @param {boolean} [options.fast=true] - Optional. Whether to use the Fast AJAX pipeline.
   * @param {Object} [options.payload={}] - Optional. Additional payload merged with the defaults.
   *
   * @example
   * remoteAction('someAction', {
   *   element: document.getElementById('myElement'),
   *   callback: (response, element) => {
   *     console.log('Success:', response);
   *   },
   *   nonce: 'abcd1234',
   *   fast: false
   * });
   */

  remoteAction(action, { element = null, callback = null, errorCallback = null, finalCallback = null, nonce = null, fast = true, payload = {} } = {}) {
    element?.classList.add('ajax-in-progress');

    FcnUtils.aPost({
      ...{
        'action': action,
        'fcn_fast_ajax': !!fast,
        'nonce': nonce ?? FcnUtils.nonce()
      },
      ...payload
    })
    .then(response => {
      if (callback) {
        callback(response, element);
      }

      if (!response.success) {
        fcn_showNotification(
          response.data.failure ?? response.data.error ?? fictioneer_tl.notification.error,
          10,
          'warning'
        );

        if (response.data.error || response.data.failure) {
          console.error('Error:', response.data.error ?? response.data.failure);
        }
      }
    })
    .catch(error => {
      if (error.status === 429) {
        fcn_showNotification(fictioneer_tl.notification.slowDown, 3, 'warning');
      } else if (error.status && error.statusText) {
        fcn_showNotification(`${error.status}: ${error.statusText}`, 5, 'warning');
      }

      if (errorCallback) {
        errorCallback(error, element);
      }

      console.error(error);
    })
    .then(() => {
      element?.classList.remove('ajax-in-progress');

      if (finalCallback) {
        finalCallback(element);
      }
    });
  },

  /**
   * Parse JSON and account for invalid strings.
   *
   * @since 5.7.0
   * @param {String} str - The string to parse.
   * @return {Object|null} Parsed JSON or null if not a JSON string.
   */

  parseJSON(str) {
    if (str === null || typeof str === 'undefined' || typeof str !== 'string') {
      return null;
    }

    try {
      return JSON.parse(str);
    } catch (e) {
      return null;
    }
  },

  /**
   * Copy valid to clipboard and optionally show a notice.
   *
   * @since 4.0.0
   * @param {String} text - The text to be copied.
   * @param {String} [message] - Optional notice to show.
   */

  copyToClipboard(text, message = false) {
    message = message ? message : fictioneer_tl.notification.copiedToClipboard;

    if (navigator.clipboard) {
      navigator.clipboard.writeText(text);

      if (message) {
        fcn_showNotification(message, 2);
      }
    }
  },

  /**
   * Clamp a number between a minimum and a maximum.
   *
   * @since 4.0.0
   * @param {Number} min - The minimum.
   * @param {Number} max - The maximum.
   * @param {Number} val - The value to clamp.
   */

  clamp(min, max, val) {
    return Math.min(Math.max(val, min), max);
  },

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

  offset(element) {
    const rect = element.getBoundingClientRect();

    return {
      top: rect.top + window.scrollY,
      left: rect.left + window.scrollX
    };
  },

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

  throttle(func, wait, options) {
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
  },

  /**
   * Delete a cookie.
   *
   * @since 4.7.0
   * @param {String} cname - Name of the cookie to delete.
   */

  deleteCookie(cname) {
    // Delete cookie by setting expiration date to the past
    document.cookie = cname + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/';
  },

  /**
   * Delete all cookies.
   *
   * @since 4.7.0
   * @link https://stackoverflow.com/questions/179355/clearing-all-cookies-with-javascript
   */

  deleteAllCookies() {
    // Clear local storage as well
    localStorage.clear();

    // Delete cookies by setting expiration date to the past
    document.cookie.split(';').forEach(
      (c) => {
        document.cookie = c.replace(/^ +/, '').replace(/=.*/, '=;expires=' + new Date().toUTCString() + ';path=/');
      }
    );
  },

  /**
   * Set a cookie.
   *
   * @since 4.7.0
   * @param {String} cname - Name of the cookie to set.
   * @param {String} value - Value of the cookie to set.
   * @param {Number} [days=30] - Days the cookie will last.
   */

  setCookie(cname, value, days = 30) {
    const d = new Date();

    d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));

    const expires = 'expires=' + d.toUTCString();

    document.cookie = cname + '=' + encodeURIComponent(value) + ';' + expires + ';SameSite=Strict;path=/';
  },

  /**
   * Retrieves the given cookie if available.
   *
   * @since 4.7.0
   * @param {String} cname - Name of the cookie to retrieve.
   * @return {String|Null} The cookie or null if not found.
   */

  getCookie(cname) {
    const name = cname + '=';
    const cookies = document.cookie.split(';');

    for (var i = 0; i < cookies.length; i++) {
      const c = cookies[i].trim();

      if (c.indexOf(name) == 0) {
        return decodeURIComponent(c.substring(name.length, c.length));
      }
    }

    return null;
  },

  /**
   * Return the Fictioneer nonce, accounting for dynamic nonces.
   *
   * @since 5.0.0
   * @return {String} The nonce value.
   */

  nonce() {
    return _$$$('fictioneer-ajax-nonce')?.value ??
      _$$$('general-fictioneer-nonce')?.value ??
      _$('[name="fictioneer_nonce"]')?.value ?? 0;
  },

  /**
   * Build new URL with search parameters.
   *
   * @since 5.0.0
   * @param {Object} [params=] - Optional. Search params to add.
   * @param {String|null} [url=] - Optional. The base URL string.
   * @return {URL} The built URL.
   */

  buildUrl(params = {}, url = null) {
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
  },

  /**
   * Build and returns error message notice HTML.
   *
   * @since 5.0.0
   * @param {String} message - Message of the error notice.
   * @param {String} [id=false] - Optional. ID of the element.
   * @param {Boolean} [sanitize=true] - Optional. Whether to sanitize the HTML.
   * @return {HTMLElement} Error notice to be added.
   */

  buildErrorNotice(message, id = false, sanitize = true) {
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
    notice.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i><div>${sanitize ? FcnUtils.sanitizeHTML(text) : text}</div>`;
    return notice;
  },

  /**
   * Detects if an element is about to leave the visible screen
   * and returns the collision directions. Threshold: 50px.
   *
   * @since 5.2.5
   * @since 5.18.0 - Add horizontal collisions.
   * @param {HTMLElement} element - The element to check for collision.
   * @returns {Array} - Array of collision directions ('top', 'bottom', 'left', 'right').
   */

  detectScreenCollision(element) {
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
  },

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

  sanitizeHTML(html) {
    const temp = document.createElement('div');

    temp.innerText = html instanceof HTMLElement ? html.innerHTML : html;
    temp.textContent = typeof html === 'string' ? html : html.textContent;

    return temp.innerHTML;
  },

  /**
   * Scroll to a specific target on the page.
   *
   * @since 5.4.0
   *
   * @param {HTMLElement} target - The target element to scroll to.
   * @param {Number} [offset=64] - Optional. Offset from top.
   */

  scrollTo(target, offset = 64) {
    // Scroll to position - offset
    window.scrollTo({
      top: target.getBoundingClientRect().top + window.scrollY - offset,
      behavior: 'smooth'
    });
  },

  /**
   * Creates an HTML element from a template string.
   *
   * @since 5.4.5
   * @param {...string} args - The template strings and placeholders.
   * @return {HTMLElement} The HTML element created from the template string.
   *
   * @example
   * const element = FcnUtils.html`
   *   <div class="my-element">
   *     <h1>Title</h1>
   *     <p>Content</p>
   *   </div>
   * `;
   *
   * document.body.appendChild(element);
   */

  html(...args) {
    const template = document.createElement('template');
    template.innerHTML = String.raw(...args).trim();

    return template.content.firstChild;
  },

  /**
   * Toggles progression state of an element.
   *
   * @since 5.26.0
   * @param {HTMLElement} element - The element.
   * @param {Boolean|null} force - Whether to disable or enable. Defaults to
   *                               the opposite of the current state.
   */

  toggleInProgress(element, force = null) {
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
  },

  /**
   * Adjust textarea height to fit the value without vertical scroll bar.
   *
   * @since 4.7.0
   * @param {HTMLElement} area - The textarea element to adjust.
   */

  adjustTextarea(area) {
    if (area) {
      area.style.height = 'auto'; // Reset if lines are removed
      area.style.height = `${area.scrollHeight}px`;
    }
  },

  /**
   * Checks if the user is a crawler (if JS is enabled)
   *
   * @returns {Boolean} - True or false.
   */

  isSearchEngineCrawler() {
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
  },

  /**
   * Remove item from array once.
   *
   * @since 4.0.0
   * @param {Any[]} array - The array from which to remove the item from.
   * @param {Any} value - The value of the item to remove.
   * @return {Any[]} The modified array.
   */

  removeArrayItemOnce(array, value) {
    var index = array.indexOf(value);

    if (index > -1) {
      array.splice(index, 1);
    }

    return array;
  },

  /**
   * Extract and join all text nodes of an element.
   *
   * @since 5.27.0
   * @param {HTMLElement} element - The element.
   * @param {Set<String>} allowedTags - Set of allowed tag names.
   * @return {String} Extracted text or empty string.
   */

  extractTextNodes(element, allowedTags = new Set(['strong', 'b', 'em', 'i', 'u', 'code', 'a', 's', 'kbd', 'sub', 'sup', 'span', 'label', 'button', 'ins', 'del', 'small', 'mark', 'q', 'abbr', 'time', 'cite'])) {
    let result = '';

    element.childNodes.forEach(node => {
      if (node.nodeType === Node.TEXT_NODE) {
        result += node.textContent.replace(/\r?\n/g, ' ');
      } else if (node.nodeType === Node.ELEMENT_NODE) {
        const tagName = node.tagName.toLowerCase();

        if (tagName === 'br') {
          result += ' ';
        } else if (allowedTags.has(tagName)) {
          result += FcnUtils.extractTextNodes(node, allowedTags);
        }
      }
    });

    return result.replace(/\s+/g, ' ').trim();
  },

  /**
   * Appends a string to the comment form.
   *
   * @since 5.27.0
   * @param {String} content - The content to append.
   */

  appendToComment(content) {
    const editor = _$(FcnGlobals.commentFormSelector);

    if (!editor) {
      FcnGlobals.commentStack.push(content);
      return;
    }

    switch (editor.tagName) {
      case 'TEXTAREA':
        editor.value += content;
        FcnUtils.adjustTextarea(editor);
        break;
      case 'DIV':
        editor.innerHTML += content;
        break;
    }
  }
};

// =============================================================================
// FICTIONEER AJAX REQUESTS
// =============================================================================

/**
 * Helper function to make Fetch API requests.
 *
 * Note: Legacy helper function.
 *
 * @since 5.27.0
 * @param {String} method - The HTTP method (GET or POST).
 * @param {Object} data - The payload, including the action and nonce.
 * @param {String} [url] - Optional. The request URL if different from the default.
 * @param {Object} [headers] - Optional. Headers for the request.
 * @return {Promise} Promise that resolves to the parsed JSON response (200) or null (204).
 */

async function fcn_ajaxRequest(method, data = {}, url = null, headers = {}) {
  // Auto-complete REST request if not a full URL
  if (url && !url.startsWith('http')) {
    url = FcnGlobals.restURL + url;
  }

  // Get default URL if not provided
  url = url ? url : (fictioneer_ajax.ajax_url ?? FcnGlobals.ajaxURL);

  // Merge default nonce into data
  data = { nonce: FcnUtils.nonce(), ...data };

  // Request options
  const options = {
    method,
    credentials: 'same-origin',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'Cache-Control': 'no-cache',
      ...headers
    },
    mode: 'same-origin'
  };

  // Add body or query string based on method
  if (method === 'POST') {
    options.body = new URLSearchParams(data);
  } else if (method === 'GET') {
    url = FcnUtils.buildUrl(data, url);
  }

  // Fetch promise
  const response = await fetch(url, options);

  // Handle response by status code
  switch (response.status) {
    case 200: // OK
      return response.json();
    case 204: // No Content
      return null;
    default:
      return Promise.reject(response);
  }
}

/**
 * Make a POST request with the Fetch API.
 *
 * Note: Legacy helper function.
 *
 * @since 5.0.0
 * @param {Object} data - The payload, including the action and nonce.
 * @param {String} [url] - Optional. The request URL if different from the default.
 * @param {Object} [headers] - Optional. Headers for the request.
 * @return {Promise} Promise that resolves to the parsed JSON response (200) or null (204).
 */

async function fcn_ajaxPost(data = {}, url = null, headers = {}) {
  return fcn_ajaxRequest('POST', data, url, headers);
}

/**
 * Make a GET request with the Fetch API.
 *
 * Note: Legacy helper function.
 *
 * @since 5.0.0
 * @param {Object} data - The payload, including the action and nonce.
 * @param {String} [url] - Optional. The request URL if different from the default.
 * @param {Object} [headers] - Optional. Headers for the request.
 * @return {Promise} Promise that resolves to the parsed JSON response (200) or null (204).
 */

async function fcn_ajaxGet(data = {}, url = null, headers = {}) {
  return fcn_ajaxRequest('GET', data, url, headers);
}
