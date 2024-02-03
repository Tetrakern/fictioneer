// =============================================================================
// MOBILE MENU
// =============================================================================

/**
 * Delegate toggling of the mobile menu.
 *
 * @since 5.8.7
 * @param {boolean} isOpened - True or false.
 */

function fcn_toggleMobileMenu(isOpened) {
  // Clone main navigation into mobile menu
  fcn_copyNavIntoMobileMenu();

  if (_$('.mobile-menu._advanced-mobile-menu')) {
    fcn_toggleAdvancedMobileMenu(isOpened);
  } else {
    fcn_toggleSimpleMobileMenu(isOpened);
  }
}

/**
 * Open or close the simple mobile menu.
 *
 * @since 5.8.7
 * @param {boolean} isOpened - True or false.
 */

function fcn_toggleSimpleMobileMenu(isOpened) {
  if (isOpened) {
    // Mobile menu was opened
    fcn_theBody.classList.add('mobile-menu-open', 'scrolling-down');
    fcn_theBody.classList.remove('scrolling-up');
  } else {
    // Mobile menu was closed
    fcn_theBody.classList.remove('mobile-menu-open');
    fcn_closeMobileFrames(); // Close all frames
    fcn_openMobileFrame('main'); // Reset to main frame

    // Reset control checkbox
    _$$$('mobile-menu-toggle').checked = false;
  }
}

/**
 * Open or close the advanced mobile menu.
 *
 * @description When the site is transformed, the scroll context is changed as
 * well. So the scroll position needs to be corrected by remembering the
 * position before the transformation and setting it after.
 *
 * @since 4.0.0
 * @param {boolean} isOpened - True or false.
 */

function fcn_toggleAdvancedMobileMenu(isOpened) {
  // Get and preserve values before DOM changes destroy them
  const adminBarOffset = _$$$('wpadminbar')?.offsetHeight ?? 0;
  const windowScrollY = window.scrollY;
  const siteScrollTop = fcn_theSite.scrollTop;

  if (isOpened) {
    // Mobile menu was opened
    fcn_theBody.classList.add('mobile-menu-open', 'scrolling-down');
    fcn_theBody.classList.remove('scrolling-up');
    fcn_theSite.classList.add('transformed-scroll', 'transformed-site');
    fcn_theSite.scrollTop = windowScrollY - adminBarOffset;
    fcn_updateThemeColor();
  } else {
    // Mobile menu was closed
    fcn_theSite.classList.remove('transformed-site', 'transformed-scroll');
    fcn_theBody.classList.remove('mobile-menu-open');
    fcn_updateThemeColor();
    fcn_closeMobileFrames(); // Close all frames
    fcn_openMobileFrame('main'); // Reset to main frame

    // Restore scroll position
    fcn_theRoot.style.scrollBehavior = 'auto';
    window.scroll(0, siteScrollTop + adminBarOffset);
    fcn_theRoot.style.scrollBehavior = '';

    // Reset control checkbox
    _$$$('mobile-menu-toggle').checked = false;

    // Restart progress bar
    if (typeof fcn_trackProgress === 'function') {
      fcn_trackProgress();
    }
  }
}

// Listen for change of mobile menu toggle checkbox
_$$$('mobile-menu-toggle')?.addEventListener('change', e => {
  fcn_toggleMobileMenu(e.currentTarget.checked);
});

// Listen for click on the site to close mobile menu
fcn_theSite.addEventListener('click', e => {
  if (fcn_theBody.classList.contains('mobile-menu-open')) {
    e.preventDefault();
    fcn_toggleMobileMenu(false);
  }
});

/**
 * Copy and adjust navigation to mobile menu.
 *
 * @since 5.4.2
 */

function fcn_copyNavIntoMobileMenu() {
  const mainMenu = _$('[data-menu-id="main"]');
  const mobileNav = _$$$('mobile-navigation');

  if (mainMenu && mobileNav && !mobileNav.querySelector('ul')) {
    const clone = mainMenu.cloneNode(true);

    clone.id = 'mobile-menu-navigation';
    clone.classList.replace('main-navigation__list', 'mobile-navigation__list');
    clone.dataset.menuId = 'mobile';

    mobileNav.appendChild(clone);
  }
}

// =============================================================================
// JUMP BUTTONS
// =============================================================================

// Listen for click on comment jump mobile menu button
_$$$('mobile-menu-comment-jump')?.addEventListener(
  'click',
  () => {
    fcn_toggleMobileMenu(false);

    setTimeout(() => {
      const target = _$$$('comments');

      // Scroll to position + offset
      if (target) {
        fcn_scrollTo(target);
      }
    }, 200); // Wait for mobile menu to close
  }
);

// Listen for click on bookmark jump mobile menu button
_$$$('mobile-menu-bookmark-jump')?.addEventListener(
  'click',
  () => {
    fcn_toggleMobileMenu(false);

    setTimeout(() => {
      const target = _$(`[data-paragraph-id="${fcn_bookmarks.data[_$('article').id]['paragraph-id']}"]`);

      // Scroll to position + offset
      if (target) {
        fcn_scrollTo(target);
      }
    }, 200); // Wait for mobile menu to close
  }
);

// =============================================================================
// QUICK BUTTONS
// =============================================================================

// Listen for clicks on the darken/brighten quick buttons
_$$('.button-change-lightness').forEach(element => {
  element.addEventListener('click', (e) => {
    fcn_updateDarken(fcn_siteSettings['darken'] + parseFloat(e.currentTarget.value));
  });
});

// =============================================================================
// FRAMES
// =============================================================================

function fcn_openMobileFrame(target) {
  fcn_closeMobileFrames();
  _$(`.mobile-menu__frame[data-frame="${target}"]`)?.classList.add('_active');
}

function fcn_closeMobileFrames() {
  // Close all open frames (should only be one)
  _$$('.mobile-menu__frame._active').forEach(element => {
    element.classList.remove('_active');
  });

  // End bookmarks edit mode
  const bookmarksPanel = _$('.mobile-menu__bookmarks-panel');

  if (bookmarksPanel) {
    bookmarksPanel.dataset.editing = 'false';
  }
}

// Listen for clicks on frame buttons...
_$$('.mobile-menu__frame-button').forEach(element => {
  element.addEventListener('click', event => {
    fcn_openMobileFrame(event.currentTarget.dataset.frameTarget);
  });
});

// Listen for clicks on back buttons...
_$$('.mobile-menu__back-button').forEach(element => {
  element.addEventListener('click', () => {
    fcn_openMobileFrame('main');
  });
});

// =============================================================================
// CHAPTERS FRAME
// =============================================================================

/**
 * Appends a cloned chapter list to the mobile menu.
 *
 * @since 4.0.0
 */

function fcn_appendChapterList() {
  const target = _$$$('mobile-menu-chapters-list');

  if (fcn_chapterList && !target.hasChildNodes()) {
    target.appendChild(fcn_chapterList.cloneNode(true));
  }
}

// Append chapters when chapter frame is opened, once
_$('.mobile-menu__frame-button[data-frame-target="chapters"]')?.addEventListener('click', () => {
  fcn_appendChapterList();
}, { once: true });

// Listen for click on chapter list in the micro menu
_$$$('micro-menu-label-open-chapter-list')?.addEventListener('click', () => {
  fcn_appendChapterList();
  fcn_openMobileFrame('chapters');
});

// =============================================================================
// FOLLOWS FRAME
// =============================================================================

/* See follows.js */

// =============================================================================
// BOOKMARKS FRAME
// =============================================================================

_$$$('button-mobile-menu-toggle-bookmarks-edit')?.addEventListener('click', event => {
  const panel = event.currentTarget.closest('.mobile-menu__bookmarks-panel');
  panel.dataset.editing = panel.dataset.editing == 'false' ? 'true' : 'false';
});

// Append bookmarks when bookmarks frame is opened, once
_$('.mobile-menu__frame-button[data-frame-target="bookmarks"]')?.addEventListener('click', (e) => {
  fcn_setMobileMenuBookmarks();
}, { once: true });
