// =============================================================================
// STIMULUS: FICTIONEER STORY
// =============================================================================

application.register('fictioneer-story', class extends Stimulus.Controller {
  static get targets() {
    return ['tabSection', 'tab', 'tabContent', 'commentsPlaceholder', 'commentsWrapper', 'commentsList', 'chapterGroup', 'filterReel', 'filterItem']
  }

  static values = {
    id: Number
  }

  commentPage = 1;

  /**
   * Stimulus Controller connect lifecycle callback.
   *
   * @since 5.27.0
   */

  connect() {
    window.FictioneerApp.Controllers.fictioneerStory = this;
    this.#applySettings();

    if (this.hasFilterReelTarget) {
      if (typeof Splide !== 'undefined') {
        new Splide(this.filterReelTarget).mount();
      }
    }
  }

  /**
   * Toggle order of chapter list between desc/asc.
   *
   * @since 5.27.0
   */

  toggleChapterOrder() {
    const settings = this.#getSettings();

    settings.order = settings.order === 'asc' ? 'desc' : 'asc';

    this.#setSettings(settings);
    this.#applySettings();
  }

  /**
   * Toggle view style of chapter list between list and grid.
   *
   * @since 5.27.0
   */

  toggleChapterView() {
    const settings = this.#getSettings();

    settings.view = settings.view=== 'list' ? 'grid' : 'list';

    this.#setSettings(settings);
    this.#applySettings();
  }

  /**
   * Unfold chapter list.
   *
   * @since 5.27.0
   * @param {Event} event - The event.
   * @param {HTMLElement} event.currentTarget - The trigger element.
   */

  unfoldChapters({currentTarget}) {
    const group = currentTarget.closest('.chapter-group[data-folded]');

    if (group) {
      group.dataset.folded = group.dataset.folded == 'true' ? 'false' : 'true';
    }
  }

  /**
   * Toggle between story tabs.
   *
   * @since 5.27.0
   * @param {Event} event - The event.
   * @param {HTMLElement} event.currentTarget - The trigger element.
   * @param {string} event.params.tabName - Name of the clicked tab.
   */

  toggleTab({currentTarget, params: {tabName}}) {
    const newTab = currentTarget;

    this.tabTargets.forEach(target => {
      target.classList.remove('_current');
    });

    this.tabContentTargets.forEach(target => {
      if (target.dataset.tabName === tabName) {
        target.classList.add('_current');
      } else {
        target.classList.remove('_current');
      }
    });

    this.tabSectionTarget.dataset.current = tabName;

    if (this.hasFilterItemTarget) {
      this.filterReelTarget.classList.toggle('hidden', tabName !== 'chapters');
    }

    newTab.classList.add('_current');
  }

  /**
   * Select or unselect a filter item.
   *
   * @since 5.29.0
   * @param {Event} event - The event.
   * @param {HTMLElement} event.currentTarget - The trigger element.
   * @param {string} event.params.groups - Comma-separated string of chapter group keys.
   * @param {number} event.params.ids - Comma-separated string of post IDs.
   */

  selectFilter({currentTarget, params: {groups, ids}}) {
    const clickedItem = currentTarget.closest('[data-fictioneer-story-target="filterItem"]');

    if (!this.hasFilterItemTarget || !this.hasChapterGroupTarget) {
      return;
    }

    this.filterItemTargets.forEach(item => {
      if (item != clickedItem) {
        item.classList.remove('selected');
      }
    });

    clickedItem.classList.toggle('selected');

    this.#filterChapters(clickedItem, groups ? groups.split(',') : [], ids ? `${ids}`.split(',') : []);
  }

  /**
   * Load next batch of story comments.
   *
   * @since 5.27.0
   * @param {Event} event - The event.
   */

  loadComments(event) {
    if (!this.hasIdValue || !this.hasCommentsListTarget) {
      return;
    }

    event.currentTarget.remove();

    if (this.hasCommentsPlaceholderTarget) {
      this.commentsPlaceholderTarget.classList.remove('hidden');
    }

    // REST
    FcnUtils.aGet(
      { 'post_id': this.idValue, 'page': this.commentPage },
      'get_story_comments'
    )
    .then(response => {
      if (this.hasCommentsPlaceholderTarget) {
        this.commentsPlaceholderTarget.remove();
      }

      if (response.success) {
        this.commentsListTarget.innerHTML += response.data.html;
        this.commentPage++;
      } else if (response.data?.error) {
        this.commentsListTarget.appendChild(FcnUtils.buildErrorNotice(response.data.error));
      }
    })
    .catch(error => {
      if (this.hasCommentsPlaceholderTarget) {
        this.commentsPlaceholderTarget.remove();
      }

      this.commentsListTarget.appendChild(FcnUtils.buildErrorNotice(error));
    });
  }

  /**
   * Start download of ePUB.
   *
   * @since 5.27.0
   * @param {Event} event - The event.
   */

  startEpubDownload(event) {
    event.preventDefault();
    this.#downloadEpub(event.currentTarget, 0)
  }

  // =====================
  // ====== PRIVATE ======
  // =====================

  /**
   * Filter chapter lists by groups and post IDs.
   *
   * @since 5.29.0
   * @param {HTMLElement} item - The clicked filter item.
   * @param {string[]} [groupKeys=[]] - Array of group keys.
   * @param {number[]} [postIds=[]] - Array of post IDs.
   */

  #filterChapters(item, groupKeys = [], postIds = []) {
    const chapterListItems = _$$('.chapter-group__list-item');

    // Reset filters
    if (!item.classList.contains('selected')) {
      this.chapterGroupTargets.forEach(element => element.classList.remove('filtered-in', 'filtered-out'));
      chapterListItems.forEach(element => element.classList.remove('filtered-in', 'filtered-out'));
      return;
    }

    // Filter
    this.chapterGroupTargets.forEach(element => element.classList.add('filtered-out'));
    this.chapterGroupTargets.forEach(element => element.classList.remove('filtered-in'));

    if (postIds.length < 1) {
      chapterListItems.forEach(element => element.classList.remove('filtered-in', 'filtered-out'));
    } else {
      chapterListItems.forEach(element => element.classList.add('filtered-out'));
      chapterListItems.forEach(element => element.classList.remove('filtered-in'));
    }

    groupKeys.forEach(group_key => {
      const element = _$$$(`chapter-group-${group_key}`);

      if (element) {
        element.classList.remove('filtered-out');
        element.classList.add('filtered-in');

        if (postIds.length > 0) {
          element.querySelectorAll('.filtered-out').forEach(element => element.classList.remove('filtered-out'));
        }

        if (element.classList.contains('_closed')) {
          fcn().toggleChapterGroup({currentTarget: element});
        }
      }
    });

    if (postIds.length > 0) {
      postIds.forEach(id => {
        const item = _$(`[data-post-id="${id}"]`);

        if (!item) {
          return;
        }

        if (groupKeys.includes(item.dataset.group)) {
          // Whole group is already filtered in
          return;
        }

        item.classList.add('filtered-in');
        item.classList.remove('filtered-out');

        const group = item.closest('.chapter-group');

        if (group && !group.classList.contains('filtered-in')) {
          group.classList.add('filtered-in');
          group.classList.remove('filtered-out');

          if (group.classList.contains('_closed')) {
            fcn().toggleChapterGroup({currentTarget: group});
          }
        }
      });
    }
  }

  /**
   * Get story settings JSON from local storage or create new one.
   *
   * @since 5.0.6
   * @return {Object} The story settings.
   */

  #getSettings() {
    let settings = FcnUtils.parseJSON(localStorage.getItem('fcnStorySettings')) ?? this.#getDefaultSettings();

    // Timestamp allows to force resets after script updates (will annoy users)
    if (settings['timestamp'] < 1674770712849) {
      settings = this.#getDefaultSettings();
      settings['timestamp'] = Date.now();
    }

    this.#setSettings(settings);

    return settings;
  }

  /**
   * Returns default story settings.
   *
   * @since 5.0.6
   * @return {Object} The formatting settings.
   */

  #getDefaultSettings() {
    return {
      'view': 'list',
      'order': 'asc',
      'timestamp': 1674770712849 // Used to force resets on script updates
    };
  }

  /**
   * Set the story settings object and save to local storage.
   *
   * @since 5.0.6
   * @param {Object} settings - The formatting settings.
   */

  #setSettings(settings) {
    if (typeof settings !== 'object') {
      return;
    }

    localStorage.setItem('fcnStorySettings', JSON.stringify(settings));
  }

  /**
   * Apply story settings.
   *
   * @since 5.0.6
   */

  #applySettings() {
    const settings = this.#getSettings();

    if (typeof settings !== 'object') {
      return;
    }

    // List view
    _$$('[data-view]').forEach(element => {
      element.dataset.view = settings.view;
    });

    // List order
    _$$('[data-order]').forEach(element => {
      element.dataset.order = settings.order;
    });
  }

  /**
   * Initiate the download of an EPUB file.
   *
   * @since 5.27.0
   * @param {HTMLElement} link - The link that triggered the download.
   * @param {number} times - The number of times the function has been called recursively.
   */

  #downloadEpub(link, times = 0) {
    if (link.classList.contains('ajax-in-progress') || !this.hasIdValue) {
      return;
    }

    if (times > 3) {
      link.classList.remove('ajax-in-progress')
      return;
    }

    link.classList.add('ajax-in-progress');

    FcnUtils.aGet({
      'action': 'fictioneer_ajax_download_epub',
      'story_id': this.idValue
    })
    .then(response => {
      if (response.success) {
        window.location.href = link.href;
        setTimeout(() => link.classList.remove('ajax-in-progress'), 2000);
      } else {
        setTimeout(() => fcn_startEpubDownload(link, times + 1), 2000);
      }
    })
    .catch(error => {
      link.classList.remove('ajax-in-progress');

      if (error.status && error.statusText) {
        fcn_showNotification(`${error.status}: ${error.statusText}`, 5, 'warning');
      }
    });
  }
});

/**
 * Remove hidden story actions after page load.
 *
 * @since 5.22.2
 */

function fcn_cleanUpActions() {
  _$$('.story__actions > *').forEach(e => {
    const s = window.getComputedStyle(e);

    if (s.display === 'none' || s.visibility === 'hidden') {
      e.remove();
    }
    }
  );
}

document.addEventListener('fcnUserDataReady', () => {
  fcn_cleanUpActions();
});
