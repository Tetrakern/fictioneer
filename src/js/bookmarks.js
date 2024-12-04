// =============================================================================
// STIMULUS: FICTIONEER BOOKMARKS
// =============================================================================

application.register('fictioneer-bookmarks', class extends Stimulus.Controller {
  static get targets() {
    return ['bookmarkScroll', 'shortcodeBlock', 'dataCard', 'overviewPageIconLink']
  }

  timeout = 0;
  chapterId = _$('.chapter__article')?.id;
  currentBookmark = null;
  cardTemplate = _$('.bookmark-small-card-template');

  initialize() {
    if (fcn()?.userReady || !FcnUtils.loggedIn()) {
      this.#ready = true;
    } else {
      document.addEventListener('fcnUserDataReady', () => {
        this.#ready = true;

        this.refreshView();
        this.#watch();
      });
    }
  }

  connect() {
    window.FictioneerApp.Controllers.fictioneerBookmarks = this;

    if (this.#ready) {
      this.refreshView();
      this.#watch();
    }
  }

  data() {
    this.bookmarksCachedData = FcnUtils.loggedIn() ? this.#getUserBookmarks() : this.#getLocalBookmarks();

    return this.#fixBookmarks(this.bookmarksCachedData).data;
  }

  toggle(paragraphId, color = 'none') {
    if (!this.chapterId) {
      return;
    }

    const bookmarksData = this.data();
    const currentBookmark = bookmarksData[this.chapterId];

    if (currentBookmark && currentBookmark['paragraph-id'] == paragraphId) {
      if (color !== 'none' && color !== currentBookmark['color']) {
        bookmarksData[this.chapterId]['color'] = color;
      } else {
        delete bookmarksData[this.chapterId];
      }
    } else {
      if (Object.keys(bookmarksData).length >= 50) {
        delete bookmarksData[Object.keys(bookmarksData)[0]];
      }

      const p = _$(`[data-paragraph-id="${paragraphId}"]`);
      const chapterBookmarkData = _$$$('chapter-bookmark-data').dataset;

      bookmarksData[this.chapterId] = {
        'paragraph-id': paragraphId,
        'progress': (FcnUtils.offset(p).top - FcnUtils.offset(p.parentElement).top) * 100 / p.parentElement.clientHeight,
        'date': (new Date()).toISOString(), // This happens anyway when stringified
        'color': color,
        'chapter': chapterBookmarkData.title.trim(),
        'link': chapterBookmarkData.link,
        'thumb': chapterBookmarkData.thumb,
        'image': chapterBookmarkData.image,
        'story': chapterBookmarkData.storyTitle.trim(),
        'content': FcnUtils.extractTextNodes(p).substring(0, 128) + '…'
      };
    }

    this.set(bookmarksData);
    this.refreshView();
  }

  remove({ params: { id } }) {
    const bookmarksData = this.data();

    delete bookmarksData[id];

    this.set(bookmarksData);
    this.refreshView();
  }

  clear() {
    this.set({ data: {} });
    this.refreshView();
  }

  set(data) {
    if (typeof data !== 'object') {
      return;
    }

    data = this.#fixBookmarks({ data: data });
    const stringifiedBookmarks = JSON.stringify(data);

    if (FcnUtils.loggedIn()) {
      const userData = fcn().userData();

      if (userData) {
        userData.bookmarks = stringifiedBookmarks;

        fcn().setUserData(userData);
      }

      this.#upload(stringifiedBookmarks);
      localStorage.removeItem('fcnChapterBookmarks');
    } else {
      localStorage.setItem('fcnChapterBookmarks', stringifiedBookmarks);
    }
  }

  refreshView() {
    const bookmarksData = this.data();
    const count = Object.keys(bookmarksData).length;

    if (this.hasOverviewPageIconLinkTarget) {
      this.overviewPageIconLinkTargets.forEach(element => {
        element.classList.toggle('hidden', count < 1);
      });
    }

    this.refreshChapterBookmark();
    this.refreshCards();
    this.refreshProfile();
  }

  refreshCards() {
    if (!this.hasShortcodeBlockTarget) {
      return;
    }

    const bookmarksData = this.data();
    const hidden = Object.keys(bookmarksData).length < 1;

    this.shortcodeBlockTargets.forEach(element => {
      element.classList.toggle('hidden', hidden);
    });

    _$$('.show-if-bookmarks').forEach(element => {
      element.classList.toggle('hidden', hidden);
    });

    if (this.hasNoBookmarksNoteTarget) {
      this.noBookmarksNoteTargets.forEach(element => {
        element.classList.toggle('hidden', hidden);
      });
    }

    if (hidden || !this.cardTemplate) {
      this.shortcodeBlockTargets.forEach(block => {
        block.querySelector('ul').innerHTML = '';
      });

      return;
    }

    this.shortcodeBlockTargets.forEach(block => {
      const ul = block.querySelector('ul');
      const fragment = document.createDocumentFragment();
      const sorted = Object.entries(bookmarksData).sort((a, b) => new Date(b[1].date) - new Date(a[1].date));

      let cardsToRender = parseInt(block.dataset.count) ?? 8; // -1 for all

      sorted.forEach(([id, { color, progress, link, chapter, 'paragraph-id': paragraphId, date, image, thumb, content }]) => {
        if (0 == cardsToRender--) {
          return;
        }

        const clone = this.cardTemplate.content.cloneNode(true);
        const formattedDate = new Date(date).toLocaleDateString(
          navigator.language ?? 'en-US',
          { year: '2-digit', month: 'short', day: 'numeric' }
        );

        if (image && clone.querySelector('.bookmark-card__image')) {
          clone.querySelector('.bookmark-card__image').href = image;
          clone.querySelector('.bookmark-card__image img').src = thumb;
        } else {
          clone.querySelector('.bookmark-card__image')?.remove();
        }

        clone.querySelector('.bookmark-card__excerpt').innerHTML += content;
        clone.querySelector('.bookmark-card').classList.add(`bookmark-${id}`);
        clone.querySelector('.bookmark-card').dataset.color = color;
        clone.querySelector('.bookmark-card__title > a').href = `${link}#paragraph-${paragraphId}`;
        clone.querySelector('.bookmark-card__title > a').innerText = chapter;
        clone.querySelector('.bookmark-card__percentage').innerText = `${progress.toFixed(1)} %`;
        clone.querySelector('.bookmark-card__progress').style.width = `calc(${progress.toFixed(1)}% - var(--bookmark-progress-offset, 0px))`;
        clone.querySelector('time').innerText = formattedDate;
        clone.querySelector('.button-delete-bookmark').setAttribute('data-fictioneer-bookmarks-id-param', id);

        fragment.appendChild(clone);
      });

      ul.innerHTML = '';
      ul.appendChild(fragment);
    });
  }

  refreshProfile() {
    if (this.hasDataCardTarget) {
      this.dataCardTarget.innerHTML = this.dataCardTarget
        .innerHTML.replace('%s', Object.keys(this.data()).length);
    }
  }

  refreshChapterBookmark() {
    const bookmarkData = this.data();

    this.currentBookmark?.classList.remove('current-bookmark');

    if (!this.chapterId || !bookmarkData[this.chapterId]) {
      return;
    }

    const paragraphId = bookmarkData[this.chapterId]['paragraph-id'];
    const p = _$(`[data-paragraph-id="${paragraphId}"]`);
    const color = bookmarkData[this.chapterId]['color'] ?? 'none';

    if (paragraphId && p) {
      p.classList.add('current-bookmark');
      p.setAttribute('data-bookmark-color', color);

      this.currentBookmark = p;

      if (this.hasBookmarkScrollTarget) {
        this.bookmarkScrollTargets.forEach(element => element.removeAttribute('hidden'));
      }
    }
  }

  // =====================
  // ====== PRIVATE ======
  // =====================

  #ready = false;
  #paused = false;

  #getLocalBookmarks() {
    return FcnUtils.parseJSON(localStorage.getItem('fcnChapterBookmarks')) ?? { data: {} };
  }

  #getUserBookmarks() {
    return FcnUtils.parseJSON(FcnUtils.userData()?.bookmarks) ?? { data: {} };
  }

  #fixBookmarks(bookmarks) {
    if (
      typeof bookmarks !== 'object' ||
      !('data' in bookmarks) ||
      (Array.isArray(bookmarks.data) && bookmarks.data.length === 0)
    ) {
      return { data: {} };
    }

    const fixedNodes = {};

    for (const key in bookmarks.data) {
      if (key.startsWith('ch-')) {
        const node = this.#fixNode(bookmarks.data[key]);

        if (node) {
          fixedNodes[key] = node;
        }
      }
    }

    return { data: fixedNodes };
  }

  #fixNode(node) {
    const fixedNode = {};
    const structure = {
      'paragraph-id': '',
      'progress': 0.0,
      'date': '',
      'color': '',
      'chapter': '',
      'link': '',
      'thumb': '',
      'image': '',
      'story': '',
      'content': ''
    };

    for (const key in structure) {
      if (typeof node[key] === typeof structure[key]) {
        fixedNode[key] = node[key];
      } else {
        return null;
      }
    }

    const date = new Date(fixedNode['date']);

    if (!date || Object.prototype.toString.call(date) !== '[object Date]' || isNaN(date)) {
      fixedNode['date'] = (new Date()).toISOString(); // This happens anyway when stringified
    }

    if (typeof fixedNode['progress'] !== 'number' || fixedNode['progress'] < 0) {
      fixedNode['progress'] = 0.0;
    }

    return fixedNode;
  }

  #userDataChanged() {
    return JSON.stringify(this.bookmarksCachedData ?? 0) !== JSON.stringify(this.data());
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
      if (document.visibilityState === 'visible') {
        this.#paused = false;
        this.refreshView();
        this.#startRefreshInterval();
      } else {
        this.#paused = true;
        clearInterval(this.refreshInterval);
        this.refreshInterval = null;
      }
    };

    document.addEventListener('visibilitychange', this.visibilityStateCheck);
  }

  /**
   * AJAX: Update user bookmarks in database.
   *
   * Note: The upload AJAX call is debounced and only fires
   * every n seconds to ease the load on the server.
   *
   * @since 5.27.0
   * @param {String} bookmarksString - Stringified JSON.
   */

  #upload(bookmarksString) {
    clearTimeout(this.timeout);

    this.timeout = setTimeout(() => {
      FcnUtils.aPost({
        'action': 'fictioneer_ajax_save_bookmarks',
        'fcn_fast_ajax': 1,
        'bookmarks': bookmarksString
      })
      .then(response => {
        if (!response.success) {
          fcn_showNotification(
            response.data.failure ?? response.data.error ?? fictioneer_tl.notification.error,
            3,
            'warning'
          );

          if (response.data.error || response.data.failure) {
            console.error('Error:', response.data.error ?? response.data.failure);
          }
        }
      })
      .catch(error => {
        if (error.status && error.statusText) {
          fcn_showNotification(`${error.status}: ${error.statusText}`, 3, 'warning');
        }

        console.error(error);
      });
    }, FcnGlobals.debounceRate); // Debounce
  }
});
