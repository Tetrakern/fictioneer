// =============================================================================
// STIMULUS: FICTIONEER FOLLOWS
// =============================================================================

application.register('fictioneer-follows', class extends Stimulus.Controller {
  static get targets() {
    return ['toggleButton', 'newDisplay', 'scrollList', 'mobileScrollList', 'mobileMarkRead']
  }

  followsLoaded = false;
  markedRead = false;
  timeout = 0;

  initialize() {
    if (fcn()?.userReady) {
      this.#ready = true;
    } else {
      document.addEventListener('fcnUserDataReady', () => {
        this.refreshView();
        this.#ready = true;
        this.#watch();
      });
    }
  }

  connect() {
    window.FictioneerApp.Controllers.fictioneerFollows = this;

    if (this.#ready) {
      this.refreshView();
      this.#watch();
    }
  }

  data() {
    this.followsCachedData = FcnUtils.userData().follows?.data;

    if (Array.isArray(this.followsCachedData) && this.followsCachedData.length === 0) {
      this.followsCachedData = {};
    }

    return this.followsCachedData;
  }

  isFollowed(id) {
    return !!(FcnUtils.loggedIn() && this.data()?.[id]);
  }

  unreadCount() {
    return parseInt(FcnUtils.userData()?.follows?.new ?? 0)
  }

  toggleFollow({ params: { id } }) {
    if (this.#loggedIn()) {
      fcn_toggleFollow(id, !this.isFollowed(id));
      this.refreshView();
    }
  }

  refreshView() {
    this.toggleButtonTargets.forEach(button => {
      const storyId = button.dataset.storyId;

      button.classList.toggle('_followed', this.isFollowed(storyId));
    });

    const unreadCount = this.unreadCount();

    this.newDisplayTargets.forEach(display => {
      display.classList.toggle('_new', unreadCount > 0);

      if (unreadCount > 0) {
        display.dataset.newCount = unreadCount;

        if (this.hasMobileMarkReadTarget) {
          this.mobileMarkReadTarget.classList.remove('hidden');
        }
      }
    });
  }

  loadFollowsHtml() {
    if (this.followsLoaded) {
      return;
    }

    FcnUtils.aGet({
      'action': 'fictioneer_ajax_get_follows_notifications',
      'fcn_fast_ajax': 1
    })
    .then(response => {
      if (response.data.html) {
        if (this.hasScrollListTarget) {
          this.scrollListTarget.innerHTML = response.data.html;
        }

        if (this.hasMobileScrollListTarget) {
          this.mobileScrollListTarget.innerHTML = response.data.html;
        }

        // Use opportunity to fix broken login state
        if (FcnUtils.userData().loggedIn === false) {
          fcn().removeUserData();
          fcn().fetchUserData();
        }
      }
    })
    .catch(error => {
      if (error.status === 429) {
        fcn_showNotification(fictioneer_tl.notification.slowDown, 3, 'warning');
      } else if (error.status && error.statusText) {
        fcn_showNotification(`${error.status}: ${error.statusText}`, 5, 'warning');
      }

      if (this.hasScrollListTarget) {
        this.scrollListTarget.remove();
      }

      if (this.hasMobileScrollListTarget) {
        this.mobileScrollListTarget.remove();
      }
    })
    .then(() => {
      this.followsLoaded = true;

      if (this.hasNewDisplayTarget) {
        this.newDisplayTargets.forEach(display => {
          display.classList.add('_loaded');
        });
      }
    });
  }

  markRead() {
    if ((!this.followsLoaded && this.unreadCount() > 0) || this.markedRead) {
      return;
    }

    this.markedRead = true;

    this.newDisplayTargets.forEach(display => {
      display.classList.remove('_new');
    });

    if (this.hasMobileMarkReadTarget) {
      this.mobileMarkReadTarget.classList.add('hidden');
    }

    const userData = FcnUtils.userData();

    userData.new = 0;
    userData.lastLoaded = 0;

    FcnUtils.setUserData(userData);

    // Request
    FcnUtils.aPost({
      'action': 'fictioneer_ajax_mark_follows_read',
      'fcn_fast_ajax': 1
    })
    .catch(error => {
      if (error.status && error.statusText) {
        fcn_showNotification(`${error.status}: ${error.statusText}`, 5, 'warning');
      }
    });
  }

  // =====================
  // ====== PRIVATE ======
  // =====================

  #ready = false;
  #paused = false;

  #loggedIn() {
    const loggedIn = FcnUtils.loggedIn();

    if (!loggedIn) {
      this.#unwatch();
      this.#paused = true;
    }

    return loggedIn;
  }

  #userDataChanged() {
    return this.#loggedIn() && JSON.stringify(this.followsCachedData ?? 0) !== JSON.stringify(this.data());
  }

  #startRefreshInterval() {
    if (this.refreshInterval) {
      return;
    }

    this.refreshInterval = setInterval(() => {
      if (!this.#paused && this.#userDataChanged()) {
        this.refreshView()
      }
    }, 30000 + Math.random() * 1000);
  }

  #watch() {
    this.#startRefreshInterval();

    this.visibilityStateCheck = () => {
      if (this.#loggedIn()) {
        if (document.visibilityState === 'visible') {
          this.#paused = false;
          this.refreshView();
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
});

// =============================================================================
// AJAX: TOGGLE FOLLOW
// =============================================================================

/**
 * Adds or removes a story ID from the Follows, then calls for an update
 * of the view to reflect the changes and saves to the database.
 *
 * @since 4.3.0
 * @since 5.xx.x - Refactored for Stimulus Controller.
 * @param {Number} storyId - The ID of the story.
 * @param {Boolean} [set] - Optional. Whether to set (true) or unset (false).
 */

function fcn_toggleFollow(storyId, set = null) {
  const controller = window.FictioneerApp.Controllers.fictioneerFollows;

  if (!controller) {
    fcn_showNotification(fictioneer_tl.notification.controllerNotConnected, 3, 'warning');
    return;
  }

  const userData = FcnUtils.userData();

  // Decide force if not given
  set = set ?? !userData.follows.data[storyId];

  // Set/Unset follow
  if (!set) {
    delete userData.follows.data[storyId];
  } else {
    userData.follows.data[storyId] = { 'story_id': parseInt(storyId), 'timestamp': Date.now() };
  }

  // Update local storage
  userData.lastLoaded = 0;
  FcnUtils.setUserData(userData);

  // Update view
  controller.refreshView();

  // Clear previous timeout (if still pending)
  clearTimeout(controller.timeout);

  // Update in database; only one request every n seconds
  controller.timeout = setTimeout(() => {
    FcnUtils.aPost({
      'action': 'fictioneer_ajax_toggle_follow',
      'fcn_fast_ajax': 1,
      'story_id': storyId,
      'set': set
    })
    .then(response => {
      // Check for failure
      if (!response.success) {
        fcn_showNotification(
          response.data.failure ?? response.data.error ?? fictioneer_tl.notification.error,
          5,
          'warning'
        );

        // Make sure the actual error (if any) is printed to the console too
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

      console.error(error);
    });
  }, FcnGlobals.debounceRate); // Debounce synchronization
}
