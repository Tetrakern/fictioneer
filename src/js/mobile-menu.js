// =============================================================================
// MOBILE MENU
// =============================================================================

const /** @const {HTMLElement} */ fcn_mobileMenuToggle = _$$$('mobile-menu-toggle'),
      /** @const {HTMLElement} */ fcn_radioMain = _$$$('mobile-menu-frame-main'),
      /** @const {HTMLElement} */ fcn_radioChapters = _$$$('mobile-menu-frame-chapters'),
      /** @const {HTMLElement} */ fcn_mobileBookmarksDeleteToggle = _$$$('toggle-mobile-menu-bookmarks-delete');

var /** @type {HTMLElement} */ fcn_chapterList = _$('#story-chapter-list > ul')?.cloneNode(true);

/**
 * Open or close the mobile menu.
 *
 * @description When the site is transformed, the scroll context is changed as
 * well. So the scroll position needs to be corrected by remembering the
 * position before the transformation and setting it after.
 *
 * @since 4.0
 * @param {boolean} isOpened - True or false.
 */

function fcn_toggleMobileMenu(isOpened) {
  let adminBarOffset = _$$$('wpadminbar')?.offsetHeight ?? 0;

  if (isOpened) {
    let newScroll = window.scrollY - adminBarOffset;
    fcn_theBody.classList.add('mobile-menu-open', 'scrolling-down');
    fcn_theBody.classList.remove('scrolling-up');
    fcn_theSite.classList.add('transformed-scroll', 'transformed-site');
    fcn_theSite.scrollTop = newScroll;
    fcn_updateThemeColor();
  } else {
    let newScroll = fcn_theSite.scrollTop + adminBarOffset;
    fcn_theSite.classList.remove('transformed-site', 'transformed-scroll');
    fcn_theBody.classList.remove('mobile-menu-open');
    fcn_updateThemeColor();

    window.scroll(0, newScroll);

    fcn_radioMain.checked = true;
    fcn_mobileMenuToggle.checked = false;

    if (typeof fcn_trackProgress === 'function') fcn_trackProgress();
  }
}

/**
 * Appends the pre-cloned chapter list to the mobile menu.
 *
 * @since 4.0
 */

function fcn_appendChapterList() {
  if (fcn_chapterList) {
    _$$$('mobile-menu-chapters-list').appendChild(fcn_chapterList);
    fcn_chapterList = false;
  }
}

// Listen for change of mobile menu toggle checkbox
_$$$('mobile-menu-toggle')?.addEventListener('change', (e) => {
  fcn_toggleMobileMenu(e.currentTarget.checked);
});

// Listen for click on the site to close mobile menu
fcn_theSite.addEventListener('click', e => {
  if (fcn_theBody.classList.contains('mobile-menu-open')) {
    e.preventDefault();
    fcn_mobileMenuToggle.checked = false;
    fcn_toggleMobileMenu(false);
  }
});

// Listen for click on mobile menu toggle to append the chapters, once
_$('[for=mobile-menu-toggle]')?.addEventListener('click', (e) => {
  fcn_appendChapterList();
}, { once: true });

// Listen for click on chapter list in the micro menu
_$$$('micro-menu-label-open-chapter-list')?.addEventListener('click', () => {
  fcn_appendChapterList();
  fcn_radioChapters.checked = true;
});

// Listen for change of the frame control to end bookmarks delete mode
_$('[name=mobile-frame-control]')?.addEventListener('change', (e) => {
  fcn_mobileBookmarksDeleteToggle.checked = false;
});

// Listen for click on bookmarks mobile menu item to append bookmarks, once
_$('label[for=mobile-menu-frame-bookmarks]')?.addEventListener('click', (e) => {
  fcn_setMobileMenuBookmarks();
}, { once: true });

// Listen for click on chapter mobile menu item to append chapters, once
_$('label[for=mobile-menu-frame-chapters]')?.addEventListener('click', (e) => {
  fcn_appendChapterList();
}, { once: true });

// Listen for click on comment jump mobile menu button
_$$$('mobile-menu-comment-jump')?.addEventListener(
  'click',
  e => {
    fcn_toggleMobileMenu(false);

    setTimeout(function() {
      let target = _$$$('comments');

      if (!target) return;

      let position = target.getBoundingClientRect().top,
          offset = position + window.pageYOffset - 64;

      window.scrollTo({ top: offset, behavior: 'smooth' });
    }, 200); // Wait for mobile menu to close
  }
);

// Listen for click on bookmark jump mobile menu button
_$$$('mobile-menu-bookmark-jump')?.addEventListener(
  'click',
  e => {
    fcn_toggleMobileMenu(false);

    setTimeout(function() {
      let target = _$(`[data-paragraph-id="${fcn_bookmarks.data[_$('article').id]['paragraph-id']}"]`);

      if (!target) return;

      let position = target.getBoundingClientRect().top,
          offset = position + window.pageYOffset - 64;

      window.scrollTo({ top: offset, behavior: 'smooth' });
    }, 200); // Wait for mobile menu to close
  }
);

// Listen for clicks on the darken/brighten quick buttons
_$$('.button-change-lightness').forEach(element => {
  element.addEventListener('click', (e) => {
    fcn_updateDarken(fcn_siteSettings['darken'] + parseFloat(e.currentTarget.value));
  });
});
