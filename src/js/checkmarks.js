// =============================================================================
// STIMULUS: FICTIONEER CHECKMARKS
// =============================================================================

application.register('fictioneer-checkmarks', class extends Stimulus.Controller {
  static get targets() {
    return ['chapterCheck', 'storyCheck', 'ribbon']
  }

  timeout = 0;

  initialize() {
    if (fcn()?.userReady) {
      this.#ready = true;
    } else {
      document.addEventListener('fcnUserDataReady', () => {
        if (FcnUtils.userData().checkmarks) {
          this.refreshView();
          this.#ready = true;
          this.#watch();
        }
      });
    }
  }

  connect() {
    window.FictioneerApp.Controllers.fictioneerCheckmarks = this;

    if (this.#ready && FcnUtils.userData().checkmarks) {
      this.refreshView();
      this.#watch();
    }
  }

  data() {
    this.checkmarksCachedData = FcnUtils.userData().checkmarks?.data;

    if (Array.isArray(this.checkmarksCachedData) && this.checkmarksCachedData.length === 0) {
      this.checkmarksCachedData = {};
    }

    return this.checkmarksCachedData;
  }

  toggleChapter({ params: { chapter, story } }) {
    fcn_toggleCheckmark(story, chapter);
  }

  toggleStory({ params: { story } }) {
    fcn_toggleCheckmark(story);
  }

  clear() {
    const userData = FcnUtils.userData();

    userData.checkmarks = { data: {}, updated: Date.now() };

    fcn().setUserData(userData);
    this.refreshView();
  }

  refreshView() {
    const checkmarks = this.data();

    if (!checkmarks || Object.keys(checkmarks).length < 1) {
      this.uncheckAll();
      return;
    }

    let storyChecked = false;

    Object.entries(checkmarks).forEach(([storyId, chapterArray]) => {
      storyId = parseInt(storyId);

      if (this.hasStoryCheckTarget && storyId == this.storyCheckTarget.dataset.fictioneerCheckmarksStoryParam) {
        storyChecked = chapterArray?.includes(storyId);
      }

      if (this.hasChapterCheckTarget) {
        if (storyChecked) {
          this.chapterCheckTargets.forEach(chapterCheckmark => {
            chapterCheckmark.classList.toggle('marked', true);
            chapterCheckmark.setAttribute('aria-checked', true);
          });
        } else {
          this.chapterCheckTargets.forEach(chapterCheckmark => {
            const chapterId = parseInt(chapterCheckmark.dataset.fictioneerCheckmarksChapterParam);
            const chapterStoryId = parseInt(chapterCheckmark.dataset.fictioneerCheckmarksStoryParam);

            if (chapterStoryId === storyId) {
              const chapterChecked = chapterArray?.includes(chapterId);

              chapterCheckmark.classList.toggle('marked', chapterChecked);
              chapterCheckmark.setAttribute('aria-checked', chapterChecked);
            }
          });
        }
      }

      if (this.hasStoryCheckTarget) {
        this.storyCheckTarget.classList.toggle('marked', storyChecked);
        this.storyCheckTarget.setAttribute('aria-checked', storyChecked);
      }

      if (this.hasRibbonTarget) {
        this.ribbonTarget.classList.toggle('hidden', !storyChecked);
      }
    });

    localStorage.removeItem('fcnBookshelfContent');
  }

  uncheckAll() {
    if (this.hasChapterCheckTarget) {
      this.chapterCheckTargets.forEach(chapterCheckmark => {
        chapterCheckmark.classList.toggle('marked', false);
        chapterCheckmark.setAttribute('aria-checked', false);
      });
    }

    if (this.hasStoryCheckTarget) {
      this.storyCheckTarget.classList.toggle('marked', false);
      this.storyCheckTarget.setAttribute('aria-checked', false);
    }
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
    return this.#loggedIn() && JSON.stringify(this.checkmarksCachedData ?? 0) !== JSON.stringify(this.data());
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
// TOGGLE CHECKMARK
// =============================================================================

/**
 * Toggle checkmarks for chapters and stories.
 *
 * @since 4.0.0
 * @since 5.27.0 - Refactored.
 * @param {Number} storyId - ID of the story.
 * @param {Number} [chapterId=null] - ID of the chapter.
 * @param {Boolean} [set=null] - True or false. Toggling by default.
 */

function fcn_toggleCheckmark(storyId, chapterId = null, set = null) {
  const controller = window.FictioneerApp.Controllers.fictioneerCheckmarks;

  if (!controller) {
    fcn_showNotification('Error: Checkmarks Controller not connected.', 3, 'warning');
    return;
  }

  const userData = FcnUtils.userData();

  if (!userData.checkmarks) {
    return;
  }

  let type = 'story';

  // Validate story ID
  storyId = parseInt(storyId ?? 0);

  if (storyId < 1) {
    fcn_showNotification('Error: Invalid story ID.', 3, 'warning');
    return;
  }

  // Validate chapter ID
  if (chapterId !== null) {
    chapterId = parseInt(chapterId);

    if (chapterId < 1) {
      fcn_showNotification('Error: Invalid chapter ID.', 3, 'warning');
      return;
    }

    type = 'chapter';
  }

  // Ensure data object is properly initialized
  if (
    typeof userData.checkmarks.data !== 'object' ||
    userData.checkmarks.data === null ||
    Array.isArray(userData.checkmarks.data)
  ) {
    userData.checkmarks.data = {};
  }

  // Initialize if story is not yet tracked
  if (!userData.checkmarks.data[storyId]) {
    userData.checkmarks.data[storyId] = [];
  }

  // Decide force if not given
  if (set === null) {
    if (type === 'chapter') {
      set = userData.checkmarks.data[storyId]?.includes(chapterId) ? false : true;
    } else {
      set = userData.checkmarks.data[storyId]?.includes(storyId) ? false : true;
    }
  }

  // Add/Remove checkmark
  const targetId = type === 'chapter' ? chapterId : storyId;

  if (set) {
    userData.checkmarks.data[storyId].push(targetId);
  } else {
    FcnUtils.removeArrayItemOnce(userData.checkmarks.data[storyId], targetId);

    if (type === 'chapter') {
      FcnUtils.removeArrayItemOnce(userData.checkmarks.data[storyId], storyId);
    }
  }

  // Make sure IDs are unique
  userData.checkmarks.data[storyId] = userData.checkmarks.data[storyId].filter(
    (value, index, self) => self.indexOf(value) == index
  );

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
      'fictioneer_ajax_set_checkmark',
      {
        payload: {
          update: userData.checkmarks.data[storyId].join(' '),
          story_id: storyId
        }
      }
    );
  }, FcnGlobals.debounceRate); // Debounce
}
