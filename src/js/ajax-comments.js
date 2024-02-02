// =============================================================================
// SETUP
// =============================================================================

const /** @const {HTMLElement} */ fcn_commentSection = _$$$('comments');

// =============================================================================
// REQUEST COMMENTS FORM VIA AJAX
// =============================================================================

// See comments.js (this is to reduce the amount of unnecessary JS)

// =============================================================================
// REQUEST COMMENTS SECTION VIA AJAX
// =============================================================================

/**
 * Load comment section via AJAX.
 *
 * @since 5.0.0
 * @param {Number=} post_id - The current post ID.
 * @param {Number=} page - The requested page number.
 */

function fcn_getCommentSection(post_id = null, page = null, scroll = false) {
  //Abort conditions...
  if (!fcn_commentSection) {
    return;
  }

  // Setup
  let commentText = '',
      commentTextarea = _$$$('comment'),
      errorNote;

  // Preserve comment text (in case of pagination)
  if (commentTextarea) {
    commentText = commentTextarea.value;
  }

  // Abort if another AJAX action is in progress
  if (fcn_commentSection.classList.contains('ajax-in-progress')) {
    return;
  }

  // Lock comment until AJAX action is complete
  fcn_commentSection.classList.add('ajax-in-progress');

  // Get page
  if (!page) {
    page = fcn_urlParams.pg ?? 1;
  }

  // Abort if Fictioneer comment section not found
  if (!fcn_commentSection) {
    return;
  }

  // Payload
  const payload = {
    'action': 'fictioneer_ajax_get_comment_section',
    'post_id': post_id ?? fcn_commentSection.dataset.postId,
    'page': parseInt(page)
  }

  if (fcn_urlParams.commentcode) {
    payload['commentcode'] = fcn_urlParams.commentcode;
  }

  // Request
  fcn_ajaxGet(payload)
  .then(response => {
    // Check for success
    if (response.success) {
      // Update page
      page = response.data.page;

      // Get HTML
      const temp = document.createElement('div');
      temp.innerHTML = response.data.html;

      // Fix comment form
      if (temp.querySelector('#comment_post_ID')) {
        temp.querySelector('#comment_post_ID').value = response.data.postId;
        temp.querySelector('#cancel-comment-reply-link').href = "#respond";

        const logoutLink = temp.querySelector('.logout-link');

        if (logoutLink) {
          logoutLink.href = fcn_commentSection.dataset.logoutUrl;
        }
      }

      // Output HTML
      fcn_commentSection.innerHTML = temp.innerHTML;
      temp.remove();

      // Append stored content (in case of pagination)
      if (!response.data.disabled) {
        commentTextarea = _$$$('comment');
        commentTextarea.value = commentText;

        // Append stack contents (if any)
        fcn_applyCommentStack(commentTextarea);
      }

      // Bind events
      fcn_addCommentMouseleaveEvents();
      fcn_addCommentFormEvents();
      fcn_bindAJAXCommentSubmit();

      // JS trap (if active)
      fcn_addJSTrap();

      // Reveal edit/delete buttons
      fcn_revealEditButton();
      fcn_revealDeleteButton();

      // Scroll to top of comment section
      const scrollTargetSelector = location.hash.includes('#comment') ? location.hash : '.respond';
      const scrollTarget = document.querySelector(scrollTargetSelector) ?? _$$$('respond');

      if (scroll) {
        scrollTarget.scrollIntoView({ behavior: 'smooth' });
      }

      // Add page to URL and preserve params/anchor
      const refresh = window.location.protocol + '//' + window.location.host + window.location.pathname;
      let urlPart = '';

      if (fcn_urlParams.commentcode) {
        urlPart += `?commentcode=${fcn_urlParams.commentcode}`;
      }

      if (page > 1) {
        urlPart += urlPart.length > 1 ? `&pg=${page}` : `?pg=${page}`;
      }

      window.history.pushState({ path: refresh }, '', refresh + urlPart + location.hash);
    } else {
      errorNote = fcn_buildErrorNotice(response.data.error);
    }
  })
  .catch(error => {
    errorNote = fcn_buildErrorNotice(error);
  })
  .then(() => {
    // Update view regardless of success
    fcn_commentSection.classList.remove('ajax-in-progress');

    // Add error (if any)
    if (errorNote) {
      fcn_commentSection.innerHTML = '';
      fcn_commentSection.appendChild(errorNote);
    }
  });
}

// =============================================================================
// COMMENTS PAGINATION
// =============================================================================

/**
 * Wrapper to reload the comments section with only the page number
 * as parameter and scroll set to true.
 *
 * @since 5.0.0
 * @param {Number=} page - The requested page number.
 */

function fcn_reloadCommentsPage(page = null) {
  fcn_getCommentSection(null, page, true);
}

function fcn_jumpToCommentPage() {
  // Prompt user to enter desired page number
  const input = parseInt(window.prompt(_x('Enter page number:', 'Pagination jump prompt.', 'fictioneer')));

  // Reload comments on entered page
  if (input > 0) {
    fcn_reloadCommentsPage(input);
  }
}

// =============================================================================
// COMMENTS OBSERVER
// =============================================================================

var /** @type {IntersectionObserver} */ fct_commentSectionObserver;

// In case of AJAX authentication...
if (fcn_theRoot.dataset.ajaxAuth) {
  document.addEventListener('fcnAuthReady', () => {
    fcn_setupCommentSectionObserver();
  });
} else {
  fcn_setupCommentSectionObserver();
}

/**
 * Helper to set up comment section observer.
 *
 * @since 5.0.0
 */

function fcn_setupCommentSectionObserver() {
  fct_commentSectionObserver = new IntersectionObserver(
    ([entry]) => {
      if (entry.isIntersecting) {
        fcn_getCommentSection();
        fct_commentSectionObserver.disconnect();
      }
    },
    { rootMargin: '450px', threshold: 1 }
  );

  // Observe comment section to fire AJAX request once
  if (fcn_commentSection) {
    fct_commentSectionObserver.observe(fcn_commentSection);
  }
}

// =============================================================================
// SCROLL NEW COMMENT INTO VIEW SUBMITTING VIA RELOAD
// =============================================================================

// In case of AJAX authentication...
if (fcn_theRoot.dataset.ajaxAuth) {
  document.addEventListener('fcnAuthReady', () => {
    fcn_loadCommentEarly();
  });
} else {
  fcn_loadCommentEarly();
}

/**
 * Load comment section early if there is a comment* anchor.
 *
 * @since 5.0.0
 */

function fcn_loadCommentEarly() {
  // Check URL whether there is a comment anchor
  if (fcn_commentSection && location.hash.includes('#comment')) {
    // Start loading comments via AJAX if not done already
    if (!_$$$('comment')) {
      fct_commentSectionObserver.disconnect();
      fcn_reloadCommentsPage();
    }
  }
}

// =============================================================================
// COMMENTS EVENT DELEGATES
// =============================================================================

_$('.fictioneer-comments')?.addEventListener('click', event => {
  // Handle page number jump
  if (event.target.closest('[data-page-jump]')) {
    fcn_jumpToCommentPage();
    return;
  }

  // Handle page number
  const pageButton = event.target.closest('[data-page]');

  if (pageButton) {
    fcn_reloadCommentsPage(pageButton.dataset.page);
    return;
  }
});
