// =============================================================================
// STIMULUS: FICTIONEER STORY
// =============================================================================

application.register('fictioneer-story', class extends Stimulus.Controller {
  static get targets() {
    return ['tab', 'tabContent', 'commentsPlaceholder', 'commentsWrapper', 'commentsList']
  }

  static values = {
    id: Number
  }

  commentPage = 1;

  connect() {
    window.FictioneerApp.Controllers.fictioneerStory = this;
    this.#applySettings();
  }

  toggleChapterOrder() {
    const settings = this.#getSettings();

    settings.order = settings.order === 'asc' ? 'desc' : 'asc';

    this.#setSettings(settings);
    this.#applySettings();
  }

  toggleChapterView() {
    const settings = this.#getSettings();

    settings.view = settings.view=== 'list' ? 'grid' : 'list';

    this.#setSettings(settings);
    this.#applySettings();
  }

  unfoldChapters(event) {
    const group = event.currentTarget.closest('.chapter-group[data-folded]');

    if (group) {
      group.dataset.folded = group.dataset.folded == 'true' ? 'false' : 'true';
    }
  }

  toggleTab(event) {
    const newTab = event.currentTarget;

    this.tabTargets.forEach(target => {
      target.classList.remove('_current');
    });

    this.tabContentTargets.forEach(target => {
      if (target.dataset.tabName === event.params.tabName) {
        target.classList.add('_current');
      } else {
        target.classList.remove('_current');
      }
    });

    newTab.classList.add('_current');
  }

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

  startEpubDownload(event) {
    event.preventDefault();
    this.#downloadEpub(event.currentTarget, 0)
  }

  // =====================
  // ====== PRIVATE ======
  // =====================

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
        setTimeout(() => { link.classList.remove('ajax-in-progress') }, 2000);
      } else {
        setTimeout(() => { fcn_startEpubDownload(link, times + 1) }, 2000);
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
