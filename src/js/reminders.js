// =============================================================================
// STIMULUS: FICTIONEER REMINDERS
// =============================================================================

application.register('fictioneer-reminders', class extends Stimulus.Controller {
  static get targets() {
    return ['toggleButton']
  }

  remindersLoaded = false;
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
   * @since 5.31.0
   */

  connect() {
    window.FictioneerApp.Controllers.fictioneerReminders = this;

    if (this.#ready) {
      this.refreshView();
      this.#watch();
    }
  }

  /**
   * Return currently loaded reminders from user data.
   *
   * @since 5.27.0
   * @return {Object} The user data.
   */

  data() {
    this.remindersCachedData = FcnUtils.userData().reminders?.data;

    if (Array.isArray(this.remindersCachedData) && this.remindersCachedData.length === 0) {
      this.remindersCachedData = {};
    }

    return this.remindersCachedData;
  }

  /**
   * Return whether story ID is remembered.
   *
   * @since 5.27.0
   * @param {Number} id - The ID of the story.
   * @return {boolean} True if remembered, false if not (or logged-out).
   */

  isRemembered(id) {
    return !!(FcnUtils.loggedIn() && this.data()?.[id]);
  }

  /**
   * Toggle reminder and refresh view.
   *
   * @since 5.27.0
   * @param {Event} event - The event.
   * @param {Object} event.params - Additional parameters.
   * @param {String} event.params.id - Story ID.
   */

  toggleReminder({ params: { id } }) {
    if (this.#loggedIn()) {
      fcn_toggleReminder(id, !this.isRemembered(id));
      this.refreshView();
    }
  }

  /**
   * Clear all reminders and refresh view.
   *
   * @since 5.27.0
   */

  clear() {
    const userData = FcnUtils.userData();

    userData.reminders = { data: {} };

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

      button.classList.toggle('_remembered', this.isRemembered(storyId));
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
    const loggedIn = FcnUtils.loggedIn();

    if (!loggedIn) {
      this.#unwatch();
      this.#paused = true;
    }

    return loggedIn;
  }

  /**
   * Check whether the reminders data has changed.
   *
   * @since 5.27.0
   * @return {boolean} True if changed and logged-in, false otherwise.
   */

  #remindersDataChanged() {
    return this.#loggedIn() && JSON.stringify(this.remindersCachedData ?? 0) !== JSON.stringify(this.data());
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
      if (!this.#paused && this.#remindersDataChanged()) {
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
// AJAX: TOGGLE REMINDER
// =============================================================================

/**
 * Adds or removes a story ID from the Reminders, then calls for an update
 * of the view to reflect the changes and saves to the database.
 *
 * @since 5.0.0
 * @since 5.27.0 - Refactored for Stimulus Controller.
 * @param {Number} storyId - The ID of the story.
 * @param {Boolean} [set] - Optional. Whether to set (true) or unset (false).
 */

function fcn_toggleReminder(storyId, set = null) {
  const controller = window.FictioneerApp.Controllers.fictioneerReminders;

  if (!controller) {
    fcn_showNotification('Error: Reminders Controller not connected.', 3, 'warning');
    return;
  }

  const userData = FcnUtils.userData();

  // Ensure data object is properly initialized
  if (
    typeof userData.reminders.data !== 'object' ||
    userData.reminders.data === null ||
    Array.isArray(userData.reminders.data)
  ) {
    userData.reminders.data = {};
  }

  // Decide force if not given
  set = set ?? !userData.reminders.data[storyId];

  // Set/Unset reminder
  if (!set) {
    delete userData.reminders.data[storyId];
  } else {
    userData.reminders.data[storyId] = { 'story_id': parseInt(storyId), 'timestamp': Date.now() };
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
      'fictioneer_ajax_toggle_reminder',
      {
        payload: {
          set: set,
          story_id: storyId
        }
      }
    );
  }, FcnGlobals.debounceRate); // Debounce
}
