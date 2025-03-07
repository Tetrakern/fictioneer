// =============================================================================
// GLOBALS
// =============================================================================

const FcnGlobals = {
  /**
   * Reference to the main site element.
   *
   * @type {HTMLElement}
   */

  eSite: _$$$('site'),

  /**
   * Parsed URL query parameters as an object.
   *
   * @type {Object}
   */

  urlParams: Object.fromEntries(new URLSearchParams(window.location.search).entries()),

  /**
   * Timestamp when the page was loaded.
   *
   * @type {Number}
   */

  pageLoadTimestamp: Date.now(),

  /**
   * Threshold for AJAX request limits default 60 seconds).
   *
   * @type {Number}
   */

  ajaxLimitThreshold: Date.now() - parseInt(fictioneer_ajax.ttl),

  /**
   * URL for AJAX requests.
   *
   * @type {String}
   */

  ajaxURL: fictioneer_ajax.ajax_url,

  /**
   * URL for REST requests.
   *
   * @type {String}
   */

  restURL: fictioneer_ajax.rest_url,

  /**
   * URL for FFCNR requests.
   *
   * @type {String}
   */

  ffcnrURL: fictioneer_ajax.ffcnr_url,

  /**
   * Whether to use the FFCNR user authentication.
   *
   * @type {Boolean}
   */

  ffcnrAuth: fictioneer_ajax.ffcnr_auth,

  /**
   * Debounce rate in milliseconds (default 700).
   *
   * @type {Number}
   */

  debounceRate: fictioneer_ajax.post_debounce_rate,

  /**
   * Whether AJAX authentication is enabled.
   *
   * @type {Boolean}
   */

  ajaxAuth: document.documentElement.dataset.fictioneerAjaxAuthValue ?? false,

  /**
   * Theme fonts.
   *
   * @type {Object}
   */

  fonts: fictioneer_fonts ?? [],

  /**
   * Theme font colors.
   *
   * @type {Object}
   */

  fontColors: fictioneer_font_colors ?? [],

  /**
   * CSS selector for the comment from (default '#comment').
   *
   * @type {Object}
   */

  commentFormSelector: fictioneer_comments?.selector ?? '#comment',

  /**
   * Array that holds content to be applied to a delayed
   * AJAX comment form.
   *
   * @type {String[]}
   */

  commentStack: []
};

Object.freeze(FcnGlobals);

// =============================================================================
// STIMULUS: FICTIONEER
// =============================================================================

window.FictioneerApp = window.FictioneerApp || {};
window.FictioneerApp.Controllers = window.FictioneerApp.Controllers || {};

application.register('fictioneer', class extends Stimulus.Controller {
  static get targets() {
    return ['avatarWrapper', 'modal', 'mobileMenuToggle', 'dcjProtected']
  }

  static values = {
    fingerprint: String,
    ageConfirmation: { type: Boolean, default: false },
    ajaxAuth: { type: Boolean, default: false },
    ajaxSubmit: { type: Boolean, default: false },
    cachingActive: { type: Boolean, default: false },
    publicCaching: { type: Boolean, default: false },
    forceChildTheme: { type: Boolean, default: false },
    editTime: { type: Number, default: 15 }
  }

  userReady = false;
  lastModalToggle = null;
  currentModal = null;
  dcjProtection = true;

  /**
   * Stimulus Controller initialize lifecycle callback.
   *
   * @since 5.27.0
   */

  initialize() {
    if (FcnUtils.loggedIn() || this.ajaxAuthValue) {
      this.fetchUserData();
    } else {
      // Clean up old data (if any)
      fcn_cleanUpWebStorage();

      // Prepare event
      const event = new CustomEvent(
        'fcnUserDataReady',
        {
          detail: { data: this.userData(), time: new Date(), loggedOut: true },
          bubbles: true,
          cancelable: false
        }
      );

      // Fire event
      document.dispatchEvent(event);
    }

    if (this.hasDcjProtectedTarget) {
      ['mousemove', 'touchstart', 'keydown'].forEach(event => {
        window.addEventListener(event, this.liftProtection.bind(this), { once: true });
      });
    }
  }

  /**
   * Stimulus Controller connect lifecycle callback.
   *
   * @since 5.27.0
   */

  connect() {
    window.FictioneerApp.Controllers.fictioneer = this;
  }

  /**
   * Lift DoubleclickJack protection measures.
   *
   * @since 5.27.2
   */

  liftProtection() {
    if (this.dcjProtection && this.hasDcjProtectedTarget) {
      this.dcjProtectedTargets.forEach(element => element.disabled = false);
      this.dcjProtection = false;
    }
  }

  /**
   * Returns or prepares locally cached user data.
   *
   * @since 5.27.0
   * @return {Object} The user data.
   */

  userData() {
    return FcnUtils.userData();
  }

  /**
   * Update user data in web storage.
   *
   * @since 5.27.0
   * @param {Object} data - User data.
   */

  setUserData(data) {
    FcnUtils.setUserData(data);
  }

  /**
   * Reset user data in web storage.
   *
   * @since 5.27.0
   */

  resetUserData() {
    FcnUtils.resetUserData();
  }

  /**
   * Remove user data in web storage.
   *
   * @since 5.27.0
   */

  removeUserData() {
    FcnUtils.removeUserData();
  }

  /**
   * AJAX: Refresh local user data if expired, fire global events.
   *
   * @since 5.27.0
   */

  fetchUserData() {
    let currentUserData = this.userData();

    // Fix broken login state
    if (FcnUtils.loggedIn() && currentUserData.loggedIn === false) {
      this.removeUserData();

      currentUserData = this.userData();
    }

    // Only update from server after some time has passed (e.g. 60 seconds)
    if (
      (FcnGlobals.ajaxLimitThreshold < currentUserData.lastLoaded || currentUserData.loggedIn === false) &&
      (currentUserData.loggedIn !== 'pending' || this.ajaxAuthValue)
    ) {
      // Prepare event
      const event = new CustomEvent(
        'fcnUserDataReady',
        {
          detail: { data: currentUserData, time: new Date(), cached: true },
          bubbles: !currentUserData.loggedIn, // Bubbles only for logged-out case
          cancelable: currentUserData.loggedIn // Cancelable only for logged-in case
        }
      );

      // Set avatar
      fcn_setAvatar();

      // Append nonce HTML
      if (currentUserData.nonceHtml) {
        this.#appendAjaxNonce(currentUserData.nonceHtml);
      }

      // Fire event
      document.dispatchEvent(event);

      // Mark user as ready
      this.userReady = true;
      return;
    }

    // Request
    FcnUtils.aGet(
      {
        'action': FcnGlobals.ffcnrAuth ? 'auth' : 'fictioneer_ajax_get_user_data',
        'fcn_fast_ajax': 1
      },
      FcnGlobals.ffcnrAuth ? FcnGlobals.ffcnrURL : null
    )
    .then(response => {
      if (response.success) {
        // Prepare user data object
        let updatedUserData = this.userData();

        // Update local user data
        updatedUserData = response.data;
        updatedUserData['lastLoaded'] = Date.now();

        if (updatedUserData.checkmarks && updatedUserData.checkmarks.data) {
          for (let key in updatedUserData.checkmarks.data) {
            updatedUserData.checkmarks.data[key] = FcnUtils.ensureArray(updatedUserData.checkmarks.data[key]);
          }
        }

        this.setUserData(updatedUserData);

        // Set avatar
        fcn_setAvatar();

        // Append nonce HTML
        if (currentUserData.nonceHtml) {
          this.#appendAjaxNonce(currentUserData.nonceHtml);
        }

        // Prepare event
        const event = new CustomEvent('fcnUserDataReady', {
          detail: { data: response.data, time: new Date(), cached: false },
          bubbles: true,
          cancelable: false
        });

        // Fire event
        document.dispatchEvent(event);
      } else {
        // Prepare user data object
        const updatedUserData = this.userData();

        // Update guest data
        updatedUserData['lastLoaded'] = Date.now();
        updatedUserData['loggedIn'] = false;
        this.setUserData(updatedUserData);

        // Prepare event
        const event = new CustomEvent('fcnUserDataFailed', {
          detail: { data: response, time: new Date(), cached: false },
          bubbles: true,
          cancelable: false
        });

        // Fire event
        document.dispatchEvent(event);
      }

      // Mark user as ready
      this.userReady = true;
    })
    .catch(error => {
      // Something went extremely wrong; clear local storage
      localStorage.removeItem('fcnUserData');

      // Prepare event
      const event = new CustomEvent('fcnUserDataError', {
        detail: { error: error, time: new Date() },
        bubbles: true,
        cancelable: false
      });

      // Fire event
      document.dispatchEvent(event);
    });
  }

  /**
   * Copy value of an input to the clipboard.
   *
   * @since 5.27.0
   * @param {Event} event - The event.
   */

  copyInput(event) {
    event.currentTarget.select();
    FcnUtils.copyToClipboard(event.currentTarget.value, event.currentTarget.dataset.message);
  }

  /**
   * Clear the consent cookie.
   *
   * @since 5.27.0
   * @param {Event} event - The event.
   */

  clearConsent(event) {
    FcnUtils.toggleInProgress(event.currentTarget);
    FcnUtils.deleteCookie('fcn_cookie_consent');
    location.reload();
  }

  /**
   * AJAX: Clear all cookies and local storage.
   *
   * @since 5.27.0
   * @param {Event} event - The event.
   */

  clearCookies(event) {
    const target = event.currentTarget;

    if (!FcnUtils.loggedIn()) {
      fcn_cleanUpWebStorage();
      FcnUtils.deleteAllCookies();
      alert(target.dataset.message);
      return;
    }

    FcnUtils.toggleInProgress(target);

    // Remove http-only cookies and log out
    FcnUtils.aGet({
      'action': 'fictioneer_ajax_clear_cookies',
      'nonce': FcnUtils.nonce()
    })
    .then(response => {
      if (response.success) {
        fcn_cleanUpWebStorage();
        FcnUtils.deleteAllCookies();
        alert(response.data.success);
      } else if (response.data.error) {
        alert(response.data.failure);
        console.error('Error:', response.data.error);
      }
    }).catch(error => {
      alert(error);
      console.error(error);
    }).then(() => {
      FcnUtils.toggleInProgress(target);
    });
  }

  /**
   * Prepare logout
   *
   * @since 5.27.0
   */

  logout() {
    fcn_cleanUpWebStorage();
  }

  /**
   * Toggle obfuscation state.
   *
   * @since 5.27.0
   * @param {Event} event - The event.
   */

  toggleObfuscation(event) {
    event.target.closest('[data-fictioneer-target="obfuscated"]').classList.toggle('_obfuscated');
  }

  /**
   * Delete special clicks on the body not covered
   * by controller actions for various reasons.
   *
   * @since 5.27.0
   * @param {Event} event - The event.
   */

  bodyClick(event) {
    this.dispatch('bodyClick', { detail: { event: event, target: event.target } });

    let target;

    // Pagination jump
    if (target = event.target.closest('.page-numbers.dots:not(button)')) {
      this.#jumpPage(target);
      return;
    }

    // Spoiler tags
    if (target = event.target.closest('.spoiler')) {
      target.classList.toggle('_open');
      return;
    }
  }

  /**
   * Toggle chapter groups smoothly.
   *
   * @since 5.27.0
   * @param {Event} event - The event.
   */

  toggleChapterGroup(event) {
    const group = event.currentTarget.closest('.chapter-group');
    const list = group.querySelector('.chapter-group__list');
    const state = !group.classList.contains('_closed');

    // Prepare list for after transition
    list.addEventListener('transitionend', () => {
      // Remove inline height once transition is done
      list.style.height = '';

      // Adjust tabindex for accessibility
      list.querySelectorAll('a, button, label, input:not([hidden])').forEach(element => {
        element.tabIndex = list.parentElement.classList.contains('_closed') ? '-1' : '0';
      });

      // Remove will-change: transform
      _$('.main__background')?.classList.remove('will-change');
    }, { once: true } );

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
  }

  /**
   * Toggle a modal.
   *
   * @since 5.27.0
   * @param {Event} event - The event.
   */

  toggleModal(event) {
    event.preventDefault();
    this.toggleModalVisibility(event.currentTarget, event.params.id);
  }

  /**
   * Toggle a modal visibility.
   *
   * @since 5.27.0
   * @param {HTMLElement} source - The trigger element.
   * @param {String} modalId - The modal element ID.
   */

  toggleModalVisibility(source, modalId) {
    const target = _$$$(modalId);

    if (!target) {
      return;
    }

    if (this.currentModal !== target) {
      this.closeModals();
    }

    this.lastModalToggle = source;

    target.hidden = !target.hidden;

    if (!target.hidden) {
      const modalElement = target.querySelector('.close');

      modalElement?.focus();
      modalElement?.blur();
    } else {
      this.closeModals();
    }
  }

  /**
   * Close all modals.
   *
   * @since 5.27.0
   */

  closeModals() {
    if (this.hasModalTarget) {
      this.modalTargets.forEach(modal => {
        modal.hidden = true;
      });
    }

    if (this.lastModalToggle) {
      this.lastModalToggle?.focus();
      this.lastModalToggle?.blur();
      this.lastModalToggle.null;
    }
  }

  /**
   * Close all modals when clicking on a modal background.
   *
   * @since 5.27.0
   * @param {HTMLElement} target - The clicked element.
   */

  backgroundCloseModals({ target }) {
    if (target.classList.contains('modal')) {
      this.closeModals();
    }
  }

  /**
   * Toggle the mobile menu via its own controller.
   *
   * @since 5.27.0
   */

  toggleMobileMenu(event) {
    event.preventDefault();

    const controller = window.FictioneerApp.Controllers.fictioneerMobileMenu;

    if (controller) {
      controller.toggle();
    }
  }

  // =====================
  // ====== PRIVATE ======
  // =====================

  /**
   * Appends hidden input with a nonce.
   *
   * @since 5.27.0
   * @param {String} html - The input HTML.
   */

  #appendAjaxNonce(html) {
    _$$$('fictioneer-ajax-nonce')?.remove();
    document.body.appendChild(FcnUtils.html`${html}`);
  }

  /**
   * Prompt for and jump to page number
   *
   * @since 5.4.0
   *
   * @param {Number} source - Page jump element.
   */

  #jumpPage(source) {
    if (document.documentElement.dataset.disablePageJump) {
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
});

/**
 * Return the primary Fictioneer Stimulus Controller or
 * placeholder with important utility functions.
 *
 * @since 5.27.0
 * @return {stimulus.Controller} Stimulus Controller.
 */

function fcn() {
  const stimulus = window.FictioneerApp?.Controllers?.fictioneer;

  if (stimulus) {
    return stimulus;
  }

  return {
    userData: FcnUtils.userData,
    setUserData: FcnUtils.setUserData,
    resetUserData: FcnUtils.resetUserData,
    removeUserData: FcnUtils.removeUserData
  };
}

/**
 * Legacy support for fcn().userData().
 *
 * @since 5.27.0
 */

function fcn_getUserData() {
  return FcnUtils.userData();
}

/**
 * Legacy support for fcn().setUserData().
 *
 * @since 5.27.0
 * @param {Object} data - User data.
 */

function fcn_setUserData(data) {
  return FcnUtils.setUserData(data);
}

// =============================================================================
// CLEANUPS
// =============================================================================

// Remove query args (defined in dynamic-scripts.js)
if (typeof fcn_removeQueryArgs === 'function') {
  fcn_removeQueryArgs();
}

// Remove old local data
document.addEventListener('fcnUserDataReady', () => {
  if (!fcn().userData().loggedIn) {
    fcn_cleanUpWebStorage();
    fcn_cleanUpGuestView();
  }
});

/**
 * Clean-up web storage.
 *
 * @since 4.5.0
 * @since 5.27.0 - Refactored.
 */

function fcn_cleanUpWebStorage() {
  const localItems = ['fcnBookshelfContent'];

  // Remove local data
  localItems.forEach(item => localStorage.removeItem(item));

  // Remove user data (if any)
  fcn().resetUserData();
}

/**
 * Cleanup view for non-authenticated guests.
 *
 * @since 5.0.0
 * @since 5.27.0 - Refactored.
 */

function fcn_cleanUpGuestView() {
  document.body.classList.remove('logged-in', 'is-admin', 'is-moderator', 'is-editor', 'is-author');

  _$$$('fictioneer-ajax-nonce')?.remove();

  _$$('.only-moderators, .only-admins, .only-authors, .only-editors, .only-logged-in').forEach(element => {
    element.remove();
  });
}

// Admin bar logout link
_$('#wp-admin-bar-logout a')?.addEventListener('click', () => {
  fcn_cleanUpWebStorage();
});

// Prepare for login
_$$('.subscriber-login, .oauth-login-link, [data-prepare-login]').forEach(element => {
  element.addEventListener('click', () => {
    fcn().removeUserData();
  });
});

// =============================================================================
// SET LOGGED-IN STATE
// =============================================================================

/**
 * Set view to logged-in state.
 *
 * @since 5.27.0
 */

function fcn_setLoggedInState() {
  const userData = fcn().userData();
  const loggedIn = userData.loggedIn === true; // Can be 'pending'

  document.body.classList.toggle('logged-in', loggedIn);
  document.body.classList.toggle('is-admin', userData.isAdmin);
  document.body.classList.toggle('is-moderator', userData.isModerator);
  document.body.classList.toggle('is-author', userData.isAuthor);
  document.body.classList.toggle('is-editor', userData.isEditor);

  // Cleanup view for users
  const removeSelectors = [];

  if (loggedIn) {
    removeSelectors.push('[data-fictioneer-id-param="login-modal"]');
    removeSelectors.push('#login-modal');
  }

  if (!userData.isAdmin) {
    removeSelectors.push('.only-admins');

    if (!userData.isModerator) {
      removeSelectors.push('.only-moderators');
    }

    if (!userData.isAuthor) {
      removeSelectors.push('.only-authors');
    }

    if (!userData.isEditor) {
      removeSelectors.push('.only-editors');
    }
  }

  _$$(removeSelectors.join(', ')).forEach(element => element.remove());
}

document.addEventListener('fcnUserDataReady', () => {
  fcn_setLoggedInState();
});

// =============================================================================
// AVATAR
// =============================================================================

/**
 * Set avatar image where required.
 *
 * @since 5.27.0
 */

function fcn_setAvatar() {
  const url = fcn().userData()?.avatarUrl;

  if (url) {
    _$$('[data-fictioneer-target="avatarWrapper"]').forEach(target => {
      const img = document.createElement('img');

      img.classList.add('user-profile-image');
      img.src = url;

      target.firstChild.remove();
      target.appendChild(img);
    });
  }
}

document.addEventListener('fcnUserDataReady', () => {
  fcn_setAvatar();
});

// =============================================================================
// BIND EVENTS TO ANIMATION FRAMES
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

fcn_bindEventToAnimationFrame('scroll', 'scroll.rAF');
fcn_bindEventToAnimationFrame('resize', 'resize.rAF');

// =============================================================================
// HANDLE GLOBAL CHECKBOX CHANGE EVENTS
// =============================================================================

document.body.addEventListener('change', e => {
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
// NAVIGATION SETUP
// =============================================================================

document.addEventListener('DOMContentLoaded', () => {
  const menuItem = _$('#menu-navigation > .menu-item');
  const nav = _$$$('full-navigation');

  if (menuItem && nav) {
    // If the navigation is larger than its items in height, preserve the border-radii
    if (menuItem.offsetHeight < nav.offsetHeight) {
      nav.classList.add('_oversized-navigation');
    }
  }

  _$$('.main-navigation .trigger-term-menu-categories').forEach(element => {
    fcn_appendTermMenu('category', element);
  });

  _$$('.main-navigation .trigger-term-menu-tags').forEach(element => {
    fcn_appendTermMenu('post_tag', element);
  });

  _$$('.main-navigation .trigger-term-menu-genres').forEach(element => {
    fcn_appendTermMenu('fcn_genre', element);
  });

  _$$('.main-navigation .trigger-term-menu-fandoms').forEach(element => {
    fcn_appendTermMenu('fcn_fandom', element);
  });

  _$$('.main-navigation .trigger-term-menu-characters').forEach(element => {
    fcn_appendTermMenu('fcn_character', element);
  });

  _$$('.main-navigation .trigger-term-menu-warnings').forEach(element => {
    fcn_appendTermMenu('fcn_content_warning', element);
  });
});

/**
 * Append term submenu to navigation menu item.
 *
 * @since 5.22.1
 *
 * @param {String} type - The taxonomy type.
 * @param {HTMLElement} target - The menu item element.
 */

function fcn_appendTermMenu(type, target) {
  const template = _$$$(`term-submenu-${type}`);

  if (!template) {
    return;
  }

  const submenu = template.content.cloneNode(true);

  target.classList.add('menu-item-has-children');

  target.querySelector('[href="#"]').addEventListener('click', event => {
    event.preventDefault();
  });

  target.appendChild(submenu);
}

// Hover over main menu
_$$$('full-navigation')?.addEventListener('mouseover', () => {
  document.dispatchEvent(new CustomEvent('fcnRemoveLastClicked'));
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
  // Stop if the mobile menu is open
  if (FcnGlobals.eSite.classList.contains('transformed-scroll')) {
    return;
  }

  // Get current scroll offset
  const root_overflow = window.getComputedStyle(document.documentElement).overflow !== 'hidden';
  const newScrollTop = root_overflow ? (window.scrollY ?? document.documentElement.scrollTop) : (document.body.scrollTop ?? 1);

  // Scrolled to top?
  document.body.classList.toggle('scrolled-to-top', newScrollTop === 0);

  // Determine scroll direction and apply respective thresholds
  if (newScrollTop > fcn_lastScrollTop && Math.abs(fcn_lastScrollTop - newScrollTop) >= 10) {
    // Scrolling down
    document.body.classList.add('scrolling-down');
    document.body.classList.remove('scrolling-up');
    fcn_lastScrollTop = Math.max(newScrollTop, 0);
  } else if (newScrollTop < fcn_lastScrollTop && Math.abs(fcn_lastScrollTop - newScrollTop) >= 50) {
    // Scrolling up
    document.body.classList.add('scrolling-up');
    document.body.classList.remove('scrolling-down');
    fcn_lastScrollTop = Math.max(newScrollTop, 0);
  }
}

// Listen for window scrolling
window.addEventListener('scroll.rAF', FcnUtils.throttle(fcn_scrollDirection, 200));

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
    document.body.classList.toggle('is-inside-main', e.intersectionRatio < 1 && e.boundingClientRect.top <= 0);
  };

  const endOfChapterCallback = e => {
    document.body.classList.toggle('is-end-of-chapter', e.isIntersecting || e.boundingClientRect.top < 0);
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
  node.addEventListener('transitionend', event => {
    if (event.propertyName === 'opacity') {
      container.removeChild(event.target);
    }
  });

  // Remove element when the user clicks on it
  node.addEventListener('click', event => { container.removeChild(event.currentTarget); });

  // Wait for the element to become visible, otherwise it will never show up
  setTimeout(() => { node.style.opacity = 0; }, 100);
}

// Show notices based on URL params (if any)
if (FcnGlobals.urlParams) {
  // Print all failures in console
  if (FcnGlobals.urlParams['failure']) {
    console.error('Failure:', FcnGlobals.urlParams['failure']);
  }

  // Failure cases
  switch (FcnGlobals.urlParams['failure']) {
    case 'oauth_email_taken':
      // Show OAuth 2.0 registration error notice (if any)
      fcn_showNotification(fictioneer_tl.notification.oauthEmailTaken, 5, 'warning');
      break;
    case 'oauth_already_linked':
      // Show OAuth 2.0 link error notice (if any)
      fcn_showNotification(fictioneer_tl.notification.oauthAccountAlreadyLinked, 5, 'warning');
      break;
  }

  // Success cases
  switch (FcnGlobals.urlParams['success']) {
    case 'oauth_new':
      // Show new subscriber notice (if any)
      fcn_showNotification(fictioneer_tl.notification.oauthNew, 10);
      break;
    default:
      // Show OAuth 2.0 account merge notice (if any)
      if (FcnGlobals.urlParams['success']?.startsWith('oauth_merged_')) {
        fcn_showNotification(fictioneer_tl.notification.oauthAccountLinked, 3, 'success');
      }
  }

  // Generic messages
  if (FcnGlobals.urlParams['fictioneer-notice']) {
    let type = FcnGlobals.urlParams['failure'] === '1' ? 'warning' : 'base';
    type = FcnGlobals.urlParams['success'] === '1' ? 'success' : type;

    fcn_showNotification(FcnUtils.sanitizeHTML(FcnGlobals.urlParams['fictioneer-notice']), 3, type);
  }
}

// =============================================================================
// UPDATE THEME COLOR META TAG
// =============================================================================

/**
 * Update theme color meta tag.
 *
 * @since 4.0.0
 * @param {String} [color=null] - Optional color code.
 */

function fcn_updateThemeColor(color = null) {
  const darken = fcn_siteSettings['darken'] ? fcn_siteSettings['darken'] : 0;
  const saturation = fcn_siteSettings['saturation'] ? fcn_siteSettings['saturation'] : 0;
  const hueRotate = fcn_siteSettings['hue-rotate'] ? fcn_siteSettings['hue-rotate'] : 0;
  const d = darken >= 0 ? 1 + darken ** 2 : 1 - darken ** 2;
  const s = saturation >= 0 ? 1 + saturation ** 2 : 1 - saturation ** 2;

  let themeColor = getComputedStyle(document.documentElement).getPropertyValue('--theme-color-base').trim().split(' ');

  themeColor = `hsl(${(parseInt(themeColor[0]) + hueRotate) % 360}deg ${(parseInt(themeColor[1]) * s).toFixed(2)}% ${(parseInt(themeColor[2]) * d).toFixed(2)}%)`;

  _$('meta[name=theme-color]').setAttribute('content', color ?? themeColor);
}

// =============================================================================
// SITE SETTINGS
// =============================================================================

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
  'taxonomies',
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
 */

function fcn_toggleLightMode() {
  const current =
    localStorage.getItem('fcnLightmode') ?
      localStorage.getItem('fcnLightmode') == 'true' :
      document.documentElement.dataset.modeDefault == 'light';

  fcn_setLightMode(!current);
}

/**
 * Set the light mode state.
 *
 * @since 4.3.0
 * @param {Boolean} boolean - Set light mode to true or false.
 * @param {Boolean} [silent=false] - Optional. Whether to not update the theme color meta tag.
 */

function fcn_setLightMode(boolean, silent = false) {
  // Update light mode state
  localStorage.setItem('fcnLightmode', boolean);
  document.documentElement.dataset.mode = boolean ? 'light' : 'dark';

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
    document.documentElement.dataset.modeDefault == 'light',
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
  document.documentElement.dataset.fontWeight = 'default';
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
    document.documentElement.dataset.fontWeight = e.target.value;
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
  value = FcnUtils.clamp(0, 360, value ?? 0);

  // Update associated elements
  fcn_settingHueRotateText.value = value;
  fcn_settingHueRotateRange.value = value;
  fcn_settingHueRotateReset.classList.toggle('_modified', value != 0);

  // Update hue-rotate property
  document.documentElement.style.setProperty('--hue-rotate', `(${value}deg + var(--hue-offset))`);

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
fcn_settingHueRotateRange?.addEventListener('input', FcnUtils.throttle(fcn_setHueRotate, 1000 / 24));

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
  value = FcnUtils.clamp(-1, 1, value ?? fcn_siteSettings['darken']);
  value = Math.round((value + Number.EPSILON) * 100) / 100;

  // Update associated elements
  fcn_settingDarkenResets.forEach(element => { element.classList.toggle('_modified', value != 0); });
  fcn_settingDarkenRanges.forEach(element => { element.value = value; });
  fcn_settingDarkenTexts.forEach(element => { element.value = parseInt(value * 100); });

  // Calculate property
  const d = value >= 0 ? 1 + value ** 2 : 1 - value ** 2;

  // Update property in DOM
  document.documentElement.style.setProperty('--darken', `(${d} + var(--lightness-offset))`);

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
  element.addEventListener('input', FcnUtils.throttle(fcn_setDarkenFromRange, 1000 / 24));
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
  value = FcnUtils.clamp(-1, 1, value ?? fcn_siteSettings['saturation']);

  // Update associated elements
  fcn_settingSaturationResets.forEach(element => { element.classList.toggle('_modified', value != 0); });
  fcn_settingSaturationRanges.forEach(element => { element.value = value; });
  fcn_settingSaturationTexts.forEach(element => { element.value = parseInt(value * 100); });

  // Calculate property
  const s = value >= 0 ? 1 + value ** 2 : 1 - value ** 2;

  // Update property in DOM
  document.documentElement.style.setProperty('--saturation', `(${s} + var(--saturation-offset))`);

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
  element.addEventListener('input', FcnUtils.throttle(fcn_setSaturationFromRange, 1000 / 24));
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
  value = FcnUtils.clamp(-1, 1, value ?? fcn_siteSettings['font-lightness'] ?? 1);

  // Update associated elements
  fcn_settingFontLightnessResets.forEach(element => { element.classList.toggle('_modified', value != 0); });
  fcn_settingFontLightnessRanges.forEach(element => { element.value = value; });
  fcn_settingFontLightnessTexts.forEach(element => { element.value = parseInt(value * 100); });

  // Calculate property
  const l = value >= 0 ? 1 + value ** 2 : 1 - value ** 2;

  // Update property in DOM
  document.documentElement.style.setProperty('--font-lightness', `(${l} + var(--font-lightness-offset))`);

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
  element.addEventListener('input', FcnUtils.throttle(fcn_setFontLightnessFromRange, 1000 / 24));
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
    'taxonomies': true,
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
 * @return {Object} The site settings.
 */

function fcn_getSiteSettings() {
  // Get settings from web storage or use defaults
  const s = FcnUtils.parseJSON(localStorage.getItem('fcnSiteSettings')) ?? fcn_defaultSiteSettings();

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
        document.documentElement.classList.toggle('minimal', setting[1]);
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
        document.documentElement.classList.toggle(`no-${setting[0]}`, !setting[1]);
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
  document.documentElement.dataset.theme = theme;

  // Update reset button
  _$$$('site-setting-theme-reset').classList.toggle('_modified', theme != 'default');

  // Apply
  fcn_applySiteSettings(fcn_siteSettings);
  fcn_updateThemeColor();
}

_$$('.site-setting-site-theme').forEach(element => {
  // Initialize site theme select elements
  element.value = fcn_siteSettings['site-theme'] ? fcn_siteSettings['site-theme'] : 'default';

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

  // Form valid?
  if (!form.reportValidity()) {
    return;
  }

  // Disable after click regardless of outcome
  button.disabled = true;
  button.innerHTML = button.dataset.disabled;

  // Delay trap (cannot be done server-side because of caching)
  if (Date.now() < FcnGlobals.pageLoadTimestamp + 3000) {
    return;
  }

  // Prepare payload
  const payload = {};

  for (const [key, value] of formData) {
    payload[key] = value;
  }

  // Request
  FcnUtils.remoteAction(
    'fictioneer_ajax_submit_contact_form',
    {
      element: form,
      payload: payload,
      callback: (response) => {
        if (response.success) {
          form.querySelector('textarea').value = '';
          button.innerHTML = button.dataset.done;
          fcn_showNotification(response.data.success, 3, 'success');
        } else if (response.data.failure) {
          button.disabled = false;
          button.innerHTML = button.dataset.enabled;
        }
      },
      errorCallback: () => {
        button.disabled = false;
        button.innerHTML = button.dataset.enabled;
      }
    }
  );
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
// DIALOG MODALS
// =============================================================================

// Open dialog modal
_$$('[data-click-action*="open-dialog-modal"]').forEach(element => {
  element.addEventListener('click', event => {
    document.querySelector(event.currentTarget.dataset.clickTarget).showModal();
  });
});

// Close dialog modal
_$$('[data-click-action*="close-dialog-modal"], button[formmethod="dialog"][value="cancel"]').forEach(element => {
  element.addEventListener('click', event => {
    event.preventDefault();
    event.target.closest('dialog').close();
  });
});

// Close dialog modal on click outside
_$$('dialog').forEach(element => {
  element.addEventListener('mousedown', event => {
    if (event.target === event.currentTarget) {
      const rect = element.getBoundingClientRect();
      const outside = event.clientX < rect.left || event.clientX > rect.right ||
        event.clientY < rect.top || event.clientY > rect.bottom;

      if (outside) {
        event.preventDefault();
        event.target.close();
      }
    }
  });
});

// Open tooltip modal
_$$('.content-section').forEach(section => {
  section.addEventListener('click', event => {
    if (event.target.closest('[data-click-action*="open-tooltip-modal"]') && !window.getSelection().toString()) {
      const modal = _$$$('fictioneer-tooltip-dialog');
      const header = event.target.dataset.dialogHeader;
      const content = event.target.dataset.dialogContent;

      if (content.length > 200) {
        modal.style.setProperty('--modal-width', '400px');
      } else {
        modal.style.removeProperty('--modal-width');
      }

      if (header) {
        modal.querySelector('[data-finder="tooltip-dialog-header"]').innerHTML = header;
      }

      modal.querySelector('[data-finder="tooltip-dialog-content"]').innerHTML = content;
      modal.showModal();
    }
  });
});

// =============================================================================
// KEYBOARD INPUTS
// =============================================================================

/*
 * Make elements accessible with keyboard.
 */

document.body.addEventListener(
  'keydown',
  e => {
    let tabFocus = document.activeElement.closest('[tabindex="0"]:not(a, input, button, select)');

    if (['BUTTON', 'A', 'INPUT', 'SELECT'].includes(document.activeElement.tagName)) {
      tabFocus = null;
    }

    // When pressing space or enter on a focused element
    if (tabFocus) {
      if (e.key === ' ' || e.key === 'Enter') {
        e.preventDefault();
        tabFocus.click();
      }
    }

    // Escape
    if (e.key === 'Escape') {
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
    this.type = input.dataset.type;
    this.operator = input.closest('.keyword-input').querySelector('.keyword-input__operator input');
    this.inputWrapper = input.closest('.keyword-input__input-wrapper');
    this.block = input.closest('.keyword-input');
    this.form = this.block.closest('.search-form');
    this.collection = this.block.querySelector('.keyword-input__collection');
    this.suggestionList = this.block.querySelector('.keyword-input__suggestion-list');
    this.tabSuggestion = this.block.querySelector('.keyword-input__tab-suggestion');
    this.allowText = this.form.querySelector('.allow-list')?.innerText ?? '{}';
    this.allowList = FcnUtils.parseJSON(this.allowText);
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
    const textEncoder = new TextEncoder();
    const encodedText = textEncoder.encode(text.toLowerCase());

    return btoa(String.fromCharCode.apply(null, encodedText));
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

          if (match == '' && suggestion.startsWith(value)) {
            match = suggestion; // Tab suggestion
          }
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

  addNode(text = null, trigger = null) {
    // Get and prepare value
    const name = text ?? this.input.value.replace(',', '');
    let value = this.allowList[`${this.type}_${this.encode(name)}`];

    // Author names can be duplicates
    if ((this.collection.name == 'authors' || this.collection.name == 'ex_authors') && trigger) {
      value = this.allowList[`${this.type}_${this.encode(name)}_${trigger.value}`];
    }

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
    // Adjust width on input
    this.input.addEventListener(
      'input',
      event => {
        // Check for comma, which indicates end of input
        if (event.currentTarget.value.includes(',')) {
          this.addNode();
        }

        // Empty?
        this.block.classList.toggle('_empty', event.currentTarget.value === '' && this.collection.value === '');

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
        if (event.key === 'Tab' || event.key === 'Enter') {
          if (this.tabSuggestion.innerText != '') {
            event.preventDefault(); // Prevent tab navigation and submit
            this.input.value = this.tabSuggestion.innerText;
            this.addNode();
          }
        }

        if (event.key === 'Escape') {
          this.input.value = '';
          this.tabSuggestion.innerHTML = '';
          document.activeElement.blur();
        }

        if (event.key === 'Backspace') {
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
          this.addNode(event.currentTarget.innerText, event.currentTarget);
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
  if (e.key == 'Tab') {
    document.body.classList.add('user-is-tabbing');

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
  document.body.classList.remove('user-is-tabbing');

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
 * @since 5.18.0 - Add horizontal collisions.
 */

function fcn_popupPosition() {
  // Look for technically open popup menus...
  _$$('.popup-menu-toggle.last-clicked .popup-menu:not(._fixed-position)').forEach(element => {
    // Visible?
    if (window.getComputedStyle(element).display === 'none') {
      return;
    }

    // Collision with screen borders?
    const collision = FcnUtils.detectScreenCollision(element);

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

    // Left/Right?
    if (!element.closest('._fixed-horizontal')) {
      if (collision.includes('left')) {
        element.classList.remove('_center', '_justify-right');
        element.classList.add('_justify-left');
      } else if(collision.includes('right')) {
        element.classList.remove('_center', '_justify-left');
        element.classList.add('_justify-right');
      }
    }
  });
}

// Initialize
window.addEventListener('scroll.rAF', FcnUtils.throttle(fcn_popupPosition, 250));

// =============================================================================
// SCROLL TO ANCHORS
// =============================================================================

document.body.addEventListener('click', event => {
  // Setup
  const trigger = event.target.closest('[href]');
  const href = trigger?.getAttribute('href');

  // Phew...
  if (!trigger || !trigger.tagName === 'A' || !href.startsWith('#') || href.length < 2 || href === '#respond') {
    return;
  }

  // More setup
  const target = href.replace('#', '');
  const targetElement = _$$(`[name="${target}"], [id="${target}"]`)[0]; // Safe for IDs starting with a number
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
  _$$(`.menu-item > [data-nav-object-id="${document.body.dataset.postId}"]`).forEach(element => {
    element.setAttribute('aria-current', 'page');
    element.closest('.menu-item').classList.add('current-menu-item');
  });
}

// Initialize
fcn_markCurrentMenuItem();

// =============================================================================
// AGE CONFIRMATION
// =============================================================================

if (_$$$('age-confirmation-modal') && localStorage.getItem('fcnAgeConfirmation') !== '1' && !FcnUtils.isSearchEngineCrawler()) {
  fcn_showAgeConfirmationModal();
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

  if (!document.documentElement.dataset.ageConfirmation && rating && rating !== 'adult') {
    _$$$('age-confirmation-modal')?.remove();
    return;
  }

  // Setup
  const leave = _$$$('age-confirmation-leave');

  // Disable site and show modal
  document.documentElement.classList.add('age-modal-open');
  _$$$('age-confirmation-modal').hidden = false;

  // Confirm button
  _$$$('age-confirmation-confirm')?.addEventListener('click', event => {
    document.documentElement.classList.remove('age-modal-open');
    event.currentTarget.closest('.modal').remove();
    localStorage.setItem('fcnAgeConfirmation', '1');
  });

  // Leave button
  leave?.addEventListener('click', () => {
    window.location.href = leave.dataset.redirect ?? 'https://search.brave.com/';
    localStorage.removeItem('fcnAgeConfirmation');
  });
}

// =============================================================================
// SPLIDE SLIDER (IF ANY)
// =============================================================================

document.addEventListener('DOMContentLoaded', () => {
  _$$('.splide:not(.no-auto-splide, .is-initialized)').forEach(slider => {
    if (slider.querySelector('.splide__list') && typeof Splide !== 'undefined') {
      slider.classList.remove('_splide-placeholder');
      new Splide(slider).mount();
    }
  });

  _$$('.temp-script, .splide-placeholder-styles').forEach(element => {
    element.remove();
  });
});

// =============================================================================
// STIMULUS: FICTIONEER LARGE CARD
// =============================================================================

application.register('fictioneer-large-card', class extends Stimulus.Controller {
  static get targets() {
    return ['controls', 'menu']
  }

  static values = {
    postId: Number,
    storyId: Number
  }

  initialize() {
    if (fcn()?.userReady) {
      this.#ready = true;
    } else {
      document.addEventListener('fcnUserDataReady', () => {
        this.#refreshAll();
        this.#ready = true;
        this.#watch();
      });
    }
  }

  connect() {
    window.FictioneerApp.Controllers.fictioneerLargeCard = this;

    if (this.#ready) {
      this.#refreshAll();
      this.#watch();
    }

    document.addEventListener('click', event => {
      if (!event.target.closest(`.card.post-${this.postIdValue}`)) {
        this.#clickOutside();
      }
    });

    document.addEventListener('toggledLastClick', event => {
      this.#toggledLastClick(event.detail.target, event.detail.force)
    });

    if (this.hasMenuTarget) {
      this.menuFragment = document.createDocumentFragment();

      while (this.menuTarget.firstChild) {
        this.menuFragment.appendChild(this.menuTarget.firstChild);
      }
    }
  }

  disconnect() {
    this.#unwatch();
  }

  isFollowed() {
    return !!(this.#loggedIn() && !!this.#data()?.follows?.data?.[this.storyIdValue]);
  }

  isRemembered() {
    return !!(this.#loggedIn() && !!this.#data()?.reminders?.data?.[this.storyIdValue]);
  }

  isRead() {
    if (!this.#loggedIn()) {
      return false;
    }

    const checkmarks = this.#data()?.checkmarks?.data?.[this.storyIdValue];

    return !!checkmarks &&
      (checkmarks.includes(this.postIdValue) || checkmarks.includes(this.storyIdValue));
  }

  cardClick(event) {
    if (!event.target.closest('.card__controls')) {
      this.#hideMenu();
    }
  }

  toggleMenu() {
    if (this.menuTarget.querySelector('*')) {
      this.#hideMenu();
    } else {
      this.#showMenu();
    }
  }

  toggleFollow() {
    if (this.#loggedIn()) {
      fcn_toggleFollow(this.storyIdValue, !this.isFollowed());
      this.#refreshFollowState();
    } else {
      _$('[data-fictioneer-id-param="login-modal"]')?.click();
    }
  }

  toggleReminder() {
    if (this.#loggedIn()) {
      fcn_toggleReminder(this.storyIdValue, !this.isRemembered());
      this.#refreshReminderState();
    } else {
      _$('[data-fictioneer-id-param="login-modal"]')?.click();
    }
  }

  toggleCheckmarks() {
    if (this.#loggedIn()) {
      fcn_toggleCheckmark(this.storyIdValue, this.postIdValue);
      this.#refreshCheckmarkState();
    } else {
      _$('[data-fictioneer-id-param="login-modal"]')?.click();
    }
  }

  setCheckmarks(event) {
    this.toggleCheckmarks('set', event.params?.type ?? 'story');
  }

  unsetCheckmarks(event) {
    this.toggleCheckmarks('unset', event.params?.type ?? 'story');
  }

  // =====================
  // ====== PRIVATE ======
  // =====================

  #menuOpen = false;
  #ready = false;
  #paused = false;

  #loggedIn() {
    const loggedIn = FcnUtils.loggedIn();

    if (!loggedIn) {
      this.#unwatch();
      this.#ready = false;
      this.#paused = true;
    }

    return loggedIn;
  }

  #data() {
    this.userData = fcn().userData();
    return this.userData;
  }

  #userDataChanged() {
    return this.#loggedIn() && JSON.stringify(this.userData ?? 0) !== JSON.stringify(this.#data());
  }

  #startRefreshInterval() {
    if (this.refreshInterval) {
      return;
    }

    this.refreshInterval = setInterval(() => {
      if (!this.#paused && this.#userDataChanged()) {
        this.#refreshAll()
      }
    }, 30000 + Math.random() * 1000);
  }

  #watch() {
    this.#startRefreshInterval();

    this.visibilityStateCheck = () => {
      if (this.#loggedIn()) {
        if (document.visibilityState === 'visible') {
          this.#paused = false;
          this.#refreshAll();
          this.#startRefreshInterval();
        } else {
          this.#paused = true;
          clearInterval(this.refreshInterval);
          this.refreshInterval = null;
        }
      }
    };

    document.addEventListener('visibilitychange', this.visibilityStateCheck);
  }

  #unwatch() {
    clearInterval(this.refreshInterval);
    document.removeEventListener('visibilitychange', this.visibilityStateCheck);
  }

  #refreshAll() {
    this.#refreshFollowState();
    this.#refreshReminderState();
    this.#refreshCheckmarkState();
  }

  #refreshFollowState() {
    this.element.classList.toggle('has-follow', this.isFollowed());
  }

  #refreshReminderState() {
    this.element.classList.toggle('has-reminder', this.isRemembered());
  }

  #refreshCheckmarkState() {
    this.element.classList.toggle('has-checkmark', this.isRead());
  }

  #clickOutside() {
    this.#hideMenu();
  }

  #showMenu() {
    this.#menuOpen = true;
    this.menuTarget.appendChild(this.menuFragment.cloneNode(true));
  }

  #hideMenu() {
    if (this.#menuOpen) {
      this.#menuOpen = false;

      while (this.menuTarget.firstChild) {
        this.menuTarget.removeChild(this.menuTarget.firstChild);
      }
    }
  }

  #toggledLastClick(target, force) {
    if (!force) {
      this.#hideMenu();
      return;
    }

    if (target && !target.closest(`.card.post-${this.postIdValue}`) && this.#menuOpen) {
      this.#hideMenu();
    }
  }
});

// =============================================================================
// STIMULUS: FICTIONEER LAST CLICK
// =============================================================================

application.register('fictioneer-last-click', class extends Stimulus.Controller {
  static get targets() {
    return ['toggle']
  }

  last = null;

  connect() {
    window.FictioneerApp.Controllers.fictioneerLastClick = this;

    document.addEventListener('fcnRemoveLastClicked', () => {
      if (this.last) {
        this.removeLastClick();
      }
    });
  }

  removeAll() {
    if (this.last) {
      this.#dispatchToggleEvent(this.last, false);
      this.removeLastClick();
    }
  }

  toggle(event) {
    const target = event.target.closest('[data-fictioneer-last-click-target="toggle"]');

    if (
      !target ||
      (
        ['BUTTON', 'A', 'INPUT', 'SELECT'].includes(event.target.tagName) &&
        !event.target.hasAttribute('data-fictioneer-last-click-target')
      )
    ) {
      return;
    }

    const set = !target.classList.contains('last-clicked');

    if (typeof fcn_popupPosition === 'function') {
      fcn_popupPosition();
    }

    target.classList.toggle('last-clicked', set);
    target.closest('.watch-last-clicked')?.classList.toggle('has-last-clicked', set);

    if (this.last && this.last != target) {
      this.removeLastClick();
    }

    this.last = target;

    this.#dispatchToggleEvent(target, set);
    event.stopPropagation();
  }

  removeLastClick() {
    if (this.last) {
      this.last.closest('.watch-last-clicked')?.classList.remove('has-last-clicked');
      this.last.classList.remove('last-clicked');
      this.last = null;
      document.activeElement?.blur();
    }
  }

  // =====================
  // ====== PRIVATE ======
  // =====================

  #dispatchToggleEvent(target, force) {
    document.dispatchEvent(new CustomEvent('toggledLastClick', { detail: { target: target, force: force } }));
  }
});

// =============================================================================
// CONSENT MANAGEMENT
// =============================================================================

const /** @const {HTMLElement} */ fcn_consentBanner = _$$$('consent-banner');

// Show consent banner if no consent has been set, remove otherwise;
if (fcn_consentBanner && (FcnUtils.getCookie('fcn_cookie_consent') ?? '') === '' && !FcnUtils.isSearchEngineCrawler()) {
  // Delay to avoid impacting web vitals
  setTimeout(() => {
    fcn_loadConsentBanner();
  }, 4000);
} else {
  fcn_consentBanner?.remove();
}

/**
 * Load consent banner if required.
 *
 * @since 3.0
 */

function fcn_loadConsentBanner() {
  fcn_consentBanner.classList.remove('hidden');
  fcn_consentBanner.hidden = false;

  // Listen for click on full consent button
  _$$$('consent-accept-button')?.addEventListener('click', () => {
    FcnUtils.setCookie('fcn_cookie_consent', 'full')
    fcn_consentBanner.classList.add('hidden');
    fcn_consentBanner.hidden = true;
  });

  // Listen for click on necessary only consent button
  _$$$('consent-reject-button')?.addEventListener('click', () => {
    FcnUtils.setCookie('fcn_cookie_consent', 'necessary')
    fcn_consentBanner.classList.add('hidden');
    fcn_consentBanner.hidden = true;
  });
}

// =============================================================================
// SHOW LIGHTBOX
// =============================================================================

/**
 * Show image in lightbox.
 *
 * @since 5.0.3
 * @param {HTMLElement} target - The lightbox source image.
 */

function fcn_showLightbox(target) {
  const lightbox = _$$$('fictioneer-lightbox');
  const loader = lightbox.querySelector('.loader');
  const lightboxContent = _$('.lightbox__content');

  let valid = false;
  let img = null;

  // Cleanup previous content (if any)
  lightboxContent.innerHTML = '';
  loader.style.opacity = 1;

  // Bookmark source element for later use
  target.classList.add('lightbox-last-trigger');

  // Image or link?
  if (target.tagName == 'IMG') {
    img = target.cloneNode();
    valid = true;
  } else if (target.href) {
    img = document.createElement('img');
    img.src = target.href;
    valid = true;
  }

  // Show lightbox
  if (valid && img) {
    ['class', 'style', 'height', 'width'].forEach(attr => img.removeAttribute(attr));
    lightboxContent.appendChild(img);
    lightbox.classList.add('show');

    setTimeout(() => {
      loader.style.opacity = 0;
    }, 1000);

    const close = lightbox.querySelector('.lightbox__close');

    close?.focus();
    close?.blur();
  }
}

document.body.addEventListener('click', e => {
  const target = e.target.closest('[data-lightbox]:not(.no-auto-lightbox)');

  if (target) {
    // Prevent links from working
    e.preventDefault();

    // Call lightbox
    fcn_showLightbox(target);
  }
});

document.body.addEventListener('keydown', e => {
  const target = e.target.closest('[data-lightbox]:not(.no-auto-lightbox)');

  if (target) {
    // Check pressed key
    if (e.key === ' ' || e.key === 'Enter') {
      // Prevent links from working
      e.preventDefault();

      // Call lightbox
      fcn_showLightbox(target);
    }
  }
});

// Lightbox controls
document.querySelectorAll('.lightbox__close, .lightbox').forEach(element => {
  element.addEventListener(
    'click',
    e => {
      if (e.target.tagName != 'IMG') {
        // Restore default view
        _$$$('fictioneer-lightbox').classList.remove('show');

        // Restore last tab focus
        const lastTrigger = _$('.lightbox-last-trigger');

        lastTrigger?.focus();
        lastTrigger?.blur();
        lastTrigger?.classList.remove('lightbox-last-trigger');
      }
    }
  );
});
