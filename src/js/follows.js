// =============================================================================
// STIMULUS: FICTIONEER FOLLOWS
// =============================================================================

application.register('fictioneer-follows', class extends Stimulus.Controller {
  static get targets() {
    return ['toggleButton']
  }

  followsLoaded = false;
  timeout = 0;

  /**
   * Stimulus Controller initialize lifecycle callback.
   *
   * @since 5.27.0
   */

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

  /**
   * Stimulus Controller connect lifecycle callback.
   *
   * @since 5.27.0
   */

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

  toggleFollow({ params: { id } }) {
    if (this.#loggedIn()) {
      fcn_toggleFollow(id, !this.isFollowed(id));
      this.refreshView();
    }
  }

  clear() {
    const userData = FcnUtils.userData();

    userData.follows = { data: {} };

    fcn().setUserData(userData);
    this.refreshView();
  }

  /**
   * Refresh view with current data.
   *
   * @since 5.27.0
   */

  refreshView() {
    this.toggleButtonTargets.forEach(button => {
      const storyId = button.dataset.storyId;

      button.classList.toggle('_followed', this.isFollowed(storyId));
    });

    localStorage.removeItem('fcnBookshelfContent');
  }

  // =====================
  // ====== PRIVATE ======
  // =====================

  #ready = false;
  #paused = false;

  /**
   * Check whether the user is still logged-in.
   *
   * Note: Also stops watchers and intervals.
   *
   * @since 5.27.0
   * @return {boolean} True if logged-in, false otherwise.
   */

  #loggedIn() {
    if (!FcnUtils.loggedIn()) {
      this.#unwatch();
      this.#paused = true;

      return false;
    }

    return true;
  }

  /**
   * Check whether the follows data has changed.
   *
   * @since 5.27.0
   * @return {boolean} True if changed and logged-in, false otherwise.
   */

  #followsDataChanged() {
    return this.#loggedIn() && JSON.stringify(this.followsCachedData ?? 0) !== JSON.stringify(this.data());
  }

  /**
   * Start refresh interval (if data has changed).
   *
   * @since 5.27.0
   */

  #startRefreshInterval() {
    if (this.refreshInterval) {
      return;
    }

    this.refreshInterval = setInterval(() => {
      if (!this.#paused && this.#followsDataChanged()) {
        this.refreshView()
      }
    }, 30000 + Math.random() * 1000);
  }

  /**
   * Watch for window visibility changes to refresh and toggle intervals.
   *
   * @since 5.27.0
   */

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

  /**
   * Stop watching for window visibility changes.
   *
   * @since 5.27.0
   */

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
 * @since 5.27.0 - Refactored for Stimulus Controller.
 * @param {Number} storyId - The ID of the story.
 * @param {Boolean} [set] - Optional. Whether to set (true) or unset (false).
 */

function fcn_toggleFollow(storyId, set = null) {
  const controller = window.FictioneerApp.Controllers.fictioneerFollows;

  if (!controller) {
    fcn_showNotification('Error: Follows Controller not connected.', 3, 'warning');
    return;
  }

  const userData = FcnUtils.userData();

  // Ensure data object is properly initialized
  if (
    typeof userData.follows.data !== 'object' ||
    userData.follows.data === null ||
    Array.isArray(userData.follows.data)
  ) {
    userData.follows.data = {};
  }

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
    FcnUtils.remoteAction(
      'fictioneer_ajax_toggle_follow',
      {
        payload: {
          set: set,
          story_id: storyId
        }
      }
    );
  }, FcnGlobals.debounceRate); // Debounce
}
