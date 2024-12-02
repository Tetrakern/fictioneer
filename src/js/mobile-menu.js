// =============================================================================
// STIMULUS: FICTIONEER MOBILE MENU
// =============================================================================

application.register('fictioneer-mobile-menu', class extends Stimulus.Controller {
  static get targets() {
    return ['frame', 'bookmarks', 'panelBookmarks']
  }

  advanced = !!_$('.mobile-menu._advanced-mobile-menu');
  open = false;
  editingBookmarks = false;
  currentFrame = null;
  bookmarkTemplate = _$$$('mobile-bookmark-template');
  chapterId = _$('article')?.id;

  /**
   * Stimulus Controller connect lifecycle callback.
   *
   * @since 5.27.0
   */

  connect() {
    window.FictioneerApp.Controllers.fictioneerMobileMenu = this;
  }

  toggle(open = null) {
    this.open = open ?? !this.open;

    if (this.advanced) {
      this.#toggleAdvanced(this.open);
    } else {
      this.#toggleSimple(this.open);
    }
  }

  clickOutside({ detail: { target } }) {
    if (this.open && target?.id === 'site') {
      this.toggle(false);
    }
  }

  openFrame({ params: { frame } }) {
    const targetId = `mobile-frame-${frame}`;

    this.editingBookmarks = false;
    this.panelBookmarksTarget.dataset.editing = false;

    if (this.hasFrameTarget) {
      this.frameTargets.forEach(element => {
        element.classList.toggle('_active', element.id === targetId);

        if (element.id === targetId) {
          this.currentFrame = element;
        }
      });
    }
  }

  back() {
    this.openFrame({ params: { frame: 'main' } });
  }

  setMobileBookmarks() {
    if (this.hasBookmarksTarget) {
      const bookmarksData = Object.entries(fcn_getBookmarks()?.data ?? {});

      this.bookmarksTarget.innerHTML = '';

      if (bookmarksData.length < 1) {
        const node = document.createElement('li');

        node.classList.add('no-bookmarks');
        node.textContent = this.bookmarksTarget.dataset.empty;

        this.bookmarksTarget.appendChild(node);

        return;
      }

      const fragment = document.createDocumentFragment();

      bookmarksData.forEach(([id, { color, progress, link, chapter, 'paragraph-id': paragraphId }]) => {
        const clone = this.bookmarkTemplate.content.cloneNode(true);
        const bookmarkElement = clone.querySelector('.mobile-menu__bookmark');

        bookmarkElement.classList.add(`bookmark-${id}`);
        bookmarkElement.dataset.color = color;
        clone.querySelector('.mobile-menu__bookmark-progress > div > div').style.width = `${progress.toFixed(1)}%`;
        clone.querySelector('.mobile-menu__bookmark a').href = `${link}#paragraph-${paragraphId}`;
        clone.querySelector('.mobile-menu__bookmark a span').innerText = chapter;
        clone.querySelector('.mobile-menu-bookmark-delete-button').setAttribute('data-fictioneer-mobile-menu-id-param', id);

        fragment.appendChild(clone);
      });

      this.bookmarksTarget.appendChild(fragment);
    }
  }

  deleteBookmark({ params: { id } }) {
    fcn_removeBookmark(id);
    fcn_setBookmarks(fcn_bookmarks);

    if (Object.keys(fcn_bookmarks.data).length < 1) {
      _$('.bookmarks-block')?.classList.add('hidden');

      _$$('.show-if-bookmarks').forEach(element => {
        element.classList.add('hidden');
      });
    }
  }

  toggleBookmarksEdit() {
    this.editingBookmarks = !this.editingBookmarks;
    this.panelBookmarksTarget.dataset.editing = this.editingBookmarks;
  }

  scrollToComments() {
    this.#scrollTo(_$$$('comments'));
  }

  scrollToBookmark() {
    const bookmarksData = fcn_getBookmarks()?.data ?? {};
    const paragraphId = bookmarksData[this.chapterId]?.['paragraph-id'];
    const target = _$(`[data-paragraph-id="${paragraphId}"]`);

    this.#scrollTo(target);
  }

  changeLightness({ currentTarget }) {
    fcn_updateDarken(fcn_siteSettings['darken'] + parseFloat(currentTarget.value));
  }

  // =====================
  // ====== PRIVATE ======
  // =====================

  #scrollTo(target) {
    this.toggle(false);

    if (target) {
      setTimeout(() => {
        FcnUtils.scrollTo(target);
      }, 200); // Wait for mobile menu to close
    }
  }

  #toggleSimple(open) {
    this.back();

    if (open) {
      document.body.classList.add('mobile-menu-open', 'scrolling-down');
      document.body.classList.remove('scrolling-up');
    } else {
      document.body.classList.remove('mobile-menu-open');
    }
  }

  #toggleAdvanced(open) {
    const adminBarOffset = _$$$('wpadminbar')?.offsetHeight ?? 0;
    const windowScrollY = window.scrollY;
    const siteScrollTop = FcnGlobals.eSite.scrollTop;

    this.back();

    if (open) {
      document.body.classList.add('mobile-menu-open', 'scrolling-down', 'scrolled-to-top');
      document.body.classList.remove('scrolling-up');
      FcnGlobals.eSite.classList.add('transformed-scroll', 'transformed-site');
      FcnGlobals.eSite.scrollTop = windowScrollY - adminBarOffset;
    } else {
      FcnGlobals.eSite.classList.remove('transformed-site', 'transformed-scroll');
      document.body.classList.remove('mobile-menu-open');
      document.documentElement.style.scrollBehavior = 'auto';
      window.scroll(0, siteScrollTop + adminBarOffset);
      document.documentElement.style.scrollBehavior = '';
    }
  }
});
