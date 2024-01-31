// =============================================================================
// CHECK IF USER IS POST AUTHOR
// =============================================================================

document.addEventListener('fcnUserDataReady', () => {
  if (fcn_getUserData().fingerprint == fcn_theRoot.dataset.authorFingerprint) {
    fcn_theBody.classList.add('is-post-author');
  }
});

// =============================================================================
// THEME COMMENTS JS TRAP
// =============================================================================

/**
 * Append required field after the site has loaded.
 *
 * @description This prevents comment form submits from working if JavaScript
 * is not enabled, which is true for most bots. Yes, the value is just for
 * show and could be used better. A hashcash for example, to make spamming
 * really expensive!
 *
 * @since 5.0.0
 */

function fcn_addJSTrap() {
  // Setup
  const red = document.querySelector('.comment-form'); // Red makes it faster!

  // Add validation field to form
  if (red) {
    red.appendChild(fcn_html`
      <input type="hidden" id="fictioneer-comment-validator" name="fictioneer_comment_validator" value="299792458">
    `);
  }
}

// Initialize
fcn_addJSTrap();

// =============================================================================
// THEME COMMENTS AJAX MODERATION
// =============================================================================

/**
 * Performs moderation action via AJAX.
 *
 * @description Takes the ID of a comment and calls the server function specified
 * by the operation string. Applies a successful result directly to the DOM
 * without reloading the page.
 *
 * @since 4.7.0
 * @param {Number} id - ID of the comment to be moderated.
 * @param {String} operation - The moderation action to perform. Choose between
 *   'Sticky', 'Unsticky', 'Approve', 'Unapprove', 'Open', 'Close', 'Trash', or 'Spam'.
 */

function fcn_moderateComment(id, operation) {
  // Setup
  const comment = _$$$(`comment-${id}`),
        menuToggleIcon = comment.querySelector('.mod-menu-toggle-icon');

  // Abort if another AJAX action is in progress
  if (comment.classList.contains('ajax-in-progress')) {
    return;
  }

  // Lock comment until AJAX action is complete
  comment.classList.add('ajax-in-progress');

  // Prepare comment for style transition
  if (operation == 'trash' || operation == 'spam') {
    comment.style.height = comment.clientHeight + 'px';
  }

  // Request
  fcn_ajaxPost({
    'action': 'fictioneer_ajax_moderate_comment',
    'operation': operation,
    'id': id
  })
  .then(response => {
    if (response.success) {
      // Server action succeeded
      switch (response.data.operation) {
        case 'sticky':
          comment.classList.add('_sticky');
          break;
        case 'unsticky':
          comment.classList.remove('_sticky');
          break;
        case 'approve':
          comment.classList.remove('_unapproved');
          break;
        case 'unapprove':
          comment.classList.add('_unapproved');
          break;
        case 'open':
          comment.classList.remove('_closed');
          break;
        case 'close':
          comment.classList.add('_closed');
          break;
        case 'trash':
        case 'spam':
          // Let comment collapse and fade to nothing
          comment.style.overflow = 'hidden';
          comment.style.height = '0';
          comment.style.margin = '0';
          comment.style.opacity = '0';
          break;
        }
    } else {
      // Server action failed, mark comment with alert
      menuToggleIcon.classList = 'fa-solid fa-triangle-exclamation mod-menu-toggle-icon';
      menuToggleIcon.style.color = 'var(--warning)';
      comment.querySelector('.popup-menu-toggle').style.opacity = '1';

      if (response.data.error) {
        fcn_showNotification(response.data.error, 5, 'warning');
      }
    }
  })
  .catch(error => {
    // Server action failed, mark comment with alert
    menuToggleIcon.classList = 'fa-solid fa-triangle-exclamation mod-menu-toggle-icon';
    menuToggleIcon.style.color = 'var(--warning)';
    comment.querySelector('.popup-menu-toggle').style.opacity = '1';

    if (error.status && error.statusText) {
      fcn_showNotification(`${error.status}: ${error.statusText}`, 5, 'warning');
    } else if (error) {
      fcn_showNotification(error, 5, 'warning');
    }
  })
  .then(() => {
    // Remove progress state
    comment.classList.remove('ajax-in-progress');

    // Close menu
    if ( fcn_lastClicked ) fcn_lastClicked.classList.remove('last-clicked');
    fcn_lastClicked = null;
  });
}

/**
 * Listen to clicks on moderation action.
 *
 * @since 4.7.0
 */

function fcn_addModerationEvents() {
  _$$('.button-ajax-moderate-comment').forEach(element => {
    element.addEventListener(
      'click',
      e => {
        fcn_moderateComment(
          e.currentTarget.dataset.id,
          e.currentTarget.dataset.action
        );
      }
    );
  });
}

fcn_addModerationEvents();

/**
 * Listen to mouseleave to close the moderation menu.
 *
 * @since 4.7.0
 */

function fcn_addCommentMouseleaveEvents() {
  _$$('.fictioneer-comment__container').forEach(element => {
    element.addEventListener(
      'mouseleave',
      e => {
        if (fcn_lastClicked) {
          fcn_lastClicked.classList.remove('last-clicked');
        }

        fcn_lastClicked = null;
        e.stopPropagation();
      }
    );
  });
}

fcn_addCommentMouseleaveEvents();

// =============================================================================
// THEME COMMENTS AJAX REPORTING
// =============================================================================

/**
 * Report a comment via AJAX.
 *
 * @since 4.7.0
 * @param {Number} id - ID of the comment to be moderated.
 */

function fcn_flagComment(source) {
  // Only if user is logged in
  if (!fcn_isLoggedIn) {
    return;
  }

  // Setup
  const comment = source.closest('.fictioneer-comment'),
        reportButton = comment.querySelector('.fictioneer-report-comment-button');

  // Abort if another AJAX action is in progress
  if (comment.classList.contains('ajax-in-progress')) {
    return;
  }

  // Lock comment until AJAX action is complete
  comment.classList.add('ajax-in-progress');

  // Request
  fcn_ajaxPost({
    'action': 'fictioneer_ajax_report_comment',
    'id': comment.dataset.id,
    'dubious': reportButton.classList.contains('_dubious')
  })
  .then(response => {
    // Comment successfully reported?
    if (response.success) {
      reportButton.classList.toggle('on', response.data.flagged);
      reportButton.classList.remove('_dubious');

      // If report was dubious, it will be resynchronized
      if (response.data.resync) {
        fcn_showNotification(response.data.resync);
      }
    } else {
      // Show error notice
      if (response.data?.error) {
        fcn_showNotification(response.data.error, 5, 'warning');
      }
    }
  })
  .catch(error => {
    // Show server error
    if (error.status && error.statusText) {
      fcn_showNotification(`${error.status}: ${error.statusText}`, 5, 'warning');
    }
  })
  .then(() => {
    // Regardless of success
    comment.classList.remove('ajax-in-progress');
  });
}

// =============================================================================
// THEME COMMENTS RESPONSE
// =============================================================================

/**
 * Reveal comment form inputs.
 *
 * @since 5.0.21
 */

function fcn_revealCommentFormInputs(area) {
  area.closest('form').querySelectorAll('.fictioneer-respond__form-actions, .fictioneer-respond__form-bottom').forEach(element => {
    element.classList.remove('hidden');
  });
}

/**
 * Listen for events on comment form.
 *
 * @since 4.7.0
 */

function fcn_addCommentFormEvents() {
  _$$$('comment')?.addEventListener(
    'focus',
    event => {
      fcn_revealCommentFormInputs(event.currentTarget);
    },
    { once: true }
  );
}

fcn_addCommentFormEvents();

/**
 * Adjust textarea height to fit the value without vertical scroll bar.
 *
 * @since 4.7.0
 * @param {HTMLElement} area - The textarea element to adjust.
 */

function fcn_textareaAdjust(area) {
  area.style.height = 'auto'; // Reset if lines are removed
  area.style.height = area.scrollHeight + 'px';
}

/**
 * Listen for input on adaptive textareas.
 *
 * @since 4.7.0
 */

function fcn_addTextareaEvents() {
  _$$('.adaptive-textarea').forEach(element => {
    element.addEventListener(
      'input',
      e => {
        fcn_textareaAdjust(e.currentTarget);
      }
    );
  });
}

fcn_addTextareaEvents();

/**
 * Listen for changes of the private toggle.
 *
 * @since 5.0.0
 */

function fcn_addPrivateToggleEvents() {
  _$$$('fictioneer-private-comment-toggle')?.addEventListener(
    'change',
    e => {
      _$$$('respond')?.classList.toggle('_private', e.currentTarget.checked);
    }
  );
}

fcn_addPrivateToggleEvents();

// =============================================================================
// THEME COMMENTS FORMATTING BUTTONS
// =============================================================================

/**
 * Wrap a text selection within an editable element in tags.
 *
 * @since 4.7.0
 * @param {HTMLElement} element - The editable element with the selection.
 * @param {String} tag - The tag to be used.
 * @param {String[]=} options - Options defined as strings.
 */

function fcn_wrapInTag(element, tag, options = {}) {
  const href = options.href ? ' href="' + options.href + '" target="_blank" rel="nofollow noreferrer noopener"' : '',
        brackets = options.shortcode ? ['[', ']'] : ['<', '>'],
        start = element.selectionStart,
        end = element.selectionEnd,
        open = brackets[0] + tag + href + brackets[1],
        close = brackets[0] + '/' + tag + brackets[1],
        text = open + element.value.substring(start, end) + close;

  element.value = element.value.substring(0, start) + text + element.value.substring(end, element.value.length);
  element.setSelectionRange((start + open.length), (end + open.length));
  element.focus();
}

// Listen for clicks on BBCode buttons...
_$('.comment-section')?.addEventListener('click', event => {
  const formattingButton = event.target.closest('[data-bbcode]');

  if (formattingButton) {
    fcn_wrapInTag(_$$$('comment'), formattingButton.dataset.bbcode, {'shortcode': true});
  }
});

// Listen for BBCode key combinations...
_$('.comment-section')?.addEventListener('keydown', event => {
  // Start key combination...
  if (
    _$('.fictioneer-comment-toolbar') &&
    document.activeElement.tagName === 'TEXTAREA' &&
    (event.ctrlKey || event.metaKey)
  ) {
    const key = event.key.toLowerCase();

    // ... with associated BBCode keys
    if (['b', 'i', 's', 'q', 'h', 'l'].includes(key)) {
      event.preventDefault();

      const keyMapping = {
        'q': 'quote',
        'h': 'spoiler',
        'l': 'link'
      };

      fcn_wrapInTag(
        document.activeElement,
        keyMapping[key] || key,
        { 'shortcode': true }
      );
    }
  }
});

// =============================================================================
// AJAX COMMENT SUBMISSION
// =============================================================================

/**
 * Bind AJAX request to comment form submit and override default behavior.
 *
 * @since 5.0.0
 */

function fcn_bindAJAXCommentSubmit() {
  // Check whether AJAX submit is enabled, abort if not...
  if (!fcn_theRoot.dataset.ajaxSubmit) {
    return;
  }

  // Override form submit behavior
  _$$$('commentform')?.addEventListener('submit', (e) => {
    // Stop default form submission
    e.preventDefault();

    // Delay trap (cannot be done server-side because of caching)
    if (Date.now() < fcn_pageLoadTimestamp + 3000) {
      return;
    }

    // Get comment form input
    const form = e.currentTarget,
          button = _$$$('submit'),
          content = _$$$('comment'),
          author = _$$$('author'),
          email = _$$$('email'),
          cookie_consent = _$$$('wp-comment-cookies-consent'),
          privacy_consent = _$$$('fictioneer-privacy-policy-consent'),
          jsValidator = _$$$('fictioneer-comment-validator'),
          parentId = _$$$('comment_parent').value,
          parent = _$$$(`comment-${parentId}`),
          private_comment = _$$$('fictioneer-private-comment-toggle'),
          notification = _$$$('fictioneer-comment-notification-toggle');

    let emailValidation = true,
        contentValidation = true,
        privacyValidation = true;

    // Validate content
    contentValidation = content.value.length > 1;
    content.classList.toggle('_error', !contentValidation);

    // Validate privacy policy checkbox
    if (privacy_consent) {
      privacyValidation = privacy_consent.checked;
      privacy_consent.classList.toggle('_error', !privacyValidation);
    }

    // Validate email address pattern (better validation server-side)
    if (email && email.value.length > 0) {
      emailValidation = /\S+@\S+\.\S+/.test(email.value);
      email.classList.toggle('_error', !emailValidation);
    }

    // Abort if...
    if (!contentValidation || !privacyValidation || !emailValidation) {
      return false;
    }

    // Disable form during submit
    form.classList.add('ajax-in-progress');
    button.disabled = true;
    button.value = button.dataset.disabled;

    // Payload
    const payload = {
      'action': 'fictioneer_ajax_submit_comment',
      'post_id': _$$$('comment_post_ID').value,
      'content': content.value,
      'private_comment': private_comment?.checked ?? 0,
      'notification': notification?.checked ?? 0,
      'cookie_consent': cookie_consent?.checked ?? 0,
      'privacy_consent': privacy_consent?.checked ?? 0,
      'unfiltered_html': _$$$('_wp_unfiltered_html_comment_disabled')?.value ?? '',
      'depth': parent ? parseInt(parent.dataset.depth) + 1 : 1,
      'fictioneer_comment_validator': jsValidator?.value ?? 0
    }

    // Optional payload
    if (parentId) {
      payload['parent_id'] = parentId;
    }

    if (email?.value) {
      payload['email'] = email?.value;
    }

    if (author?.value) {
      payload['author'] = author?.value;
    }

    // Request
    fcn_ajaxPost(payload)
    .then(response => {
      // Remove previous error notices (if any)
      _$$$('comment-submit-error-notice')?.remove();

      // Comment successfully saved?
      if (!response.success || !response.data?.comment) {
        // Add error message
        form.insertBefore(
          fcn_buildErrorNotice(
            response.data?.error ?? __('Error', 'fictioneer'),
            'comment-submit-error-notice',
            false
          ),
          form.firstChild
        );
      } else {
        // Insert new comment
        let target = _$('.commentlist'),
            insertMode = 'insertBefore';

        // Account for sticky comments
        if (target && !parent && target.firstElementChild) {
          let maybeSticky = null;

          if (target.firstElementChild.classList.contains('_sticky')) {
            maybeSticky = target.firstElementChild;
            target = maybeSticky;
            insertMode = 'insertAfter';

            while (maybeSticky.nextElementSibling && maybeSticky.nextElementSibling.classList.contains('_sticky')) {
              maybeSticky = target.nextElementSibling;
              target = maybeSticky;
            }
          }
        }

        // Create list if missing
        if (!target) {
          // First comment, list missing!
          target = document.createElement('ol');
          target.classList = 'fictioneer-comments__list commentlist';
          _$$$('comments').appendChild(target);
          insertMode = 'append';
        }

        // Account for parent of reply
        if (parent) {
          // Get child comment list
          target = parent.querySelector('.children');

          // Append in this case
          insertMode = 'append';

          // Create child comment list if missing
          if (!target) {
            const node = document.createElement('ol');
            parent.appendChild(node);
            target = node;
          }
        }

        let commentNode = document.createElement('div');
        commentNode.innerHTML = response.data.comment;
        commentNode = commentNode.firstChild;

        switch (insertMode) {
          case 'append':
            target.appendChild(commentNode);
            break;
          case 'insertBefore':
            target.insertBefore(commentNode, target.firstChild);
            break;
          case 'insertAfter':
            if (target.nextSibling) {
              target.parentNode.insertBefore(commentNode, target.nextSibling);
            } else {
              target.parentNode.appendChild(commentNode);
            }
        }

        // Bind events
        fcn_addModerationEvents();
        fcn_addCommentMouseleaveEvents();

        // Clean-up form
        if (_$$$('comment_parent').value != '0') {
          _$$$('cancel-comment-reply-link').click();
        }

        content.value = '';
        content.style.height = '';

        // Update URL
        const refresh = window.location.protocol + '//' + window.location.host + window.location.pathname;
        let urlPart = '';

        if (response.data.commentcode) {
          urlPart += `?commentcode=${response.data.commentcode}`;
        }

        history.pushState({ path: refresh }, '', refresh + urlPart + `#comment-${response.data.comment_id}`);

        // Scroll to new comment
        commentNode.scrollIntoView({ behavior: 'smooth' });
      }
    })
    .catch(error => {
      // Remove any old error
      _$$$('comment-submit-error-notice')?.remove();

      // Add error message
      form.insertBefore(
        fcn_buildErrorNotice(`${error.status}: ${error.statusText}`, 'comment-submit-error-notice'),
        form.firstChild
      );
    })
    .then(() => {
      // Remove progress state
      form.classList.remove('ajax-in-progress');

      // Re-enable the submit button
      button.disabled = false;
      button.value = button.dataset.enabled;
    });
  });
}

// Initialize AJAX submit (if enabled)
fcn_bindAJAXCommentSubmit();

// =============================================================================
// AJAX INLINE COMMENT EDIT
// =============================================================================

const /** @const {HTMLElement} */ fcn_commentEditActionsTemplate = _$('.comment-edit-actions-template');

var /** @type {Object} */ fcn_commentEditUndos = {};

/**
 * Trigger inline comment edit.
 *
 * @description This is called with on inline onclick event handler because the
 * comments can be loaded via AJAX and constantly watching the DOM to rebind the
 * event listeners is unreasonable. All that extra code, all the extra work for
 * the system, for something that you can get at no cost. Seriously.
 *
 * @since 5.0.0
 * @param {HTMLElement} source - Event source.
 */

function fcn_triggerInlineCommentEdit(source) {
  // Setup
  const red = source.closest('.fictioneer-comment'); // Red makes it faster!

  // Main comment container found...
  if (red) {
    const content = red.querySelector('.fictioneer-comment__content'),
          edit = red.querySelector('.fictioneer-comment__edit'),
          textarea = red.querySelector('.comment-inline-edit-content');

    // Append buttons
    edit.appendChild(fcn_commentEditActionsTemplate.content.cloneNode(true));

    // Save unedited content
    fcn_commentEditUndos[red.id] = textarea.value;

    // Set comment edit state
    red.classList.add('_editing');
    content.hidden = true;
    edit.hidden = false;
    textarea.style.height = textarea.scrollHeight + 'px';
  }
}

/**
 * Submit inline comment edit via AJAX
 *
 * @since 5.0.0
 * @param {HTMLElement} source - Event source.
 */

function fcn_submitInlineCommentEdit(source) {
  // Setup
  const red = source.closest('.fictioneer-comment'), // Red makes it faster!
        edit = red.querySelector('.fictioneer-comment__edit'),
        content = red.querySelector('.comment-inline-edit-content').value;

  let editNote = red.querySelector('.fictioneer-comment__edit-note');

  // Abort if...
  if (content == fcn_commentEditUndos[red.id]) {
    fcn_restoreComment(red, true);
    return;
  }

  // Send update
  if (red) {
    // Disable edit form
    edit.classList.add('ajax-in-progress');
    source.innerHTML = source.dataset.disabled;
    source.disabled = true;

    // Request
    fcn_ajaxPost({
      'action': 'fictioneer_ajax_edit_comment',
      'comment_id': red.id.replace('comment-', ''),
      'content': content
    })
    .then(response => {
      // Comment successfully updated?
      if (response.success) {
        // Replace content in view and restore non-edit state
        const content = red.querySelector('.fictioneer-comment__content');

        content.innerHTML = response.data.content;
        fcn_restoreComment(red, false, response.data.raw);

        // Edit note
        if (!editNote) {
          editNote = document.createElement('div');
        }

        editNote.classList.add('fictioneer-comment__edit-note');
        editNote.innerHTML = response.data.edited;
        content.parentNode.appendChild(editNote);
      } else {
        // Restore non-edit state
        fcn_restoreComment(red, true);

        // Show error notice
        if (response.data?.error) {
          fcn_showNotification(response.data.error, 5, 'warning');
        }
      }
    })
    .catch(error => {
      // Restore non-edit state
      fcn_restoreComment(red, true);

      // Show server error
      if (error.status && error.statusText) {
        fcn_showNotification(`${error.status}: ${error.statusText}`, 5, 'warning');
      }
    })
    .then(() => {
      // Regardless of result
      edit.classList.remove('ajax-in-progress');
      source.innerHTML = source.dataset.enabled;
      source.disabled = false;
    });
  }
}

/**
 * Cancel inline comment edit.
 *
 * @since 5.0.0
 * @param {HTMLElement} source - Event source.
 */

function fcn_cancelInlineCommentEdit(source) {
  // Setup
  const red = source.closest('.fictioneer-comment'); // Red makes it faster!

  // Restore comment to non-edit state
  if (red) {
    fcn_restoreComment(red, true);
  }
}

/**
 * Restore comment to non-editing state.
 *
 * @since 5.0.0
 * @param {HTMLElement} target - The comment to restore.
 */

function fcn_restoreComment(target, undo = false, update = null) {
  target.querySelector('.fictioneer-comment__content').hidden = false;
  target.querySelector('.fictioneer-comment__edit').hidden = true;
  target.querySelector('.fictioneer-comment__edit-actions')?.remove();
  target.classList.remove('_editing');

  if (undo && fcn_commentEditUndos[target.id]) {
    target.querySelector('.comment-inline-edit-content').value = fcn_commentEditUndos[target.id];
  } else if (update) {
    target.querySelector('.comment-inline-edit-content').value = update;
  }
}

// =============================================================================
// SHOW EDIT BUTTON BASED ON FINGERPRINT
// =============================================================================

/**
 * Reveal edit buttons that match the user's fingerprint
 *
 * @since 5.0.0
 */

function fcn_revealEditButton() {
  // Get time limit for editing
  let editTime = parseInt(fcn_theRoot.dataset.editTime);

  // Abort if no time set
  if (!editTime) {
    return;
  }

  // Prepare comparison
  editTime = editTime > 0 ? editTime * 60000 : editTime;

  // Loop over comments with matching fingerprint (if any)
  _$$('.fictioneer-comment[data-fingerprint]').forEach(element => {
    if (fcn_matchFingerprint(element.dataset.fingerprint)) {
      // -1 means no time limit
      if (editTime > 0 && parseInt(element.dataset.timestamp) + editTime < Date.now()) {
        return;
      }

      // Reveal button
      const button = element.querySelector('.fictioneer-comment__edit-toggle');

      if (button) {
        button.hidden = false;
      }
    }
  });
}

// Reveal edit buttons
document.addEventListener('fcnUserDataReady', () => {
  fcn_revealEditButton();
});

// =============================================================================
// SHOW DELETE BUTTON BASED ON FINGERPRINT
// =============================================================================

/**
 * Reveal delete buttons that match the user's fingerprint
 *
 * @since 5.0.0
 */

function fcn_revealDeleteButton() {
  // Loop over comments with matching fingerprint (if any)
  _$$('.fictioneer-comment[data-fingerprint]').forEach(element => {
    if (fcn_matchFingerprint(element.dataset.fingerprint)) {
      // Reveal button
      const button = element.querySelector('.fictioneer-comment__delete');

      if (button) {
        button.hidden = false;
      }
    }
  });
}

// Reveal edit buttons
document.addEventListener('fcnUserDataReady', () => {
  fcn_revealDeleteButton();
});

// =============================================================================
// AJAX USER COMMENT DELETE
// =============================================================================

/**
 * Soft-delete comment on user request
 *
 * @since 5.0.0
 * @param {HTMLElement} button - The clicked delete button.
 */

function fcn_deleteMyComment(button) {
  // Only if user is logged in
  if (!fcn_isLoggedIn) {
    return;
  }

  // Prompt for confirmation
  const confirm = prompt(button.dataset.dialogMessage);

  if (!confirm || confirm.toLowerCase() != button.dataset.dialogConfirm.toLowerCase()) {
    return;
  }

  // Setup
  const comment = button.closest('.fictioneer-comment');

  // Abort if another AJAX action is in progress
  if (comment.classList.contains('ajax-in-progress')) {
    return;
  }

  // Lock comment until AJAX action is complete
  comment.classList.add('ajax-in-progress');

  // Request
  fcn_ajaxPost({
    'action': 'fictioneer_ajax_delete_my_comment',
    'comment_id': comment.dataset.id
  })
  .then(response => {
    if (response.success) {
      comment.classList.add('_deleted');
      comment.querySelector('.fictioneer-comment__container').innerHTML = response.data.html;
    } else {
      if (response.data.error) {
        fcn_showNotification(response.data.error, 5, 'warning');
      }
    }
  })
  .catch(error => {
    if (error.status && error.statusText) {
      fcn_showNotification(`${error.status}: ${error.statusText}`, 5, 'warning');
    }
  })
  .then(() => {
    // Update view regardless of success
    comment.classList.remove('ajax-in-progress');
  });
}

// =============================================================================
// REQUEST COMMENTS FORM VIA AJAX
// =============================================================================

const /** @const {HTMLElement} */ fcn_ajaxCommentForm = _$$$('ajax-comment-form-target');

// Check whether form target exists...
if (fcn_ajaxCommentForm) {
  // In case of AJAX authentication...
  if (fcn_theRoot.dataset.ajaxAuth) {
    // Load after nonce has been fetched
    document.addEventListener('fcnAuthReady', () => {
      fcn_setupCommentFormObserver();
    });
  } else {
    fcn_setupCommentFormObserver();
  }
}

/**
 * Helper to set up comment form observer.
 *
 * @since 5.7.0
 */

function fcn_setupCommentFormObserver() {
  fct_commentFormObserver = new IntersectionObserver(
    ([entry]) => {
      if (entry.isIntersecting) {
        fcn_getCommentForm();
        fct_commentFormObserver.disconnect();
      }
    },
    { rootMargin: '450px', threshold: 1 }
  );

  // Observe comment form to fire AJAX request once
  fct_commentFormObserver.observe(fcn_ajaxCommentForm);
}

/**
 * Load comment form via AJAX.
 *
 * @since 5.0.0
 */

function fcn_getCommentForm() {
  // Setup
  let errorNote;

  // Request
  fcn_ajaxGet({
    'action': 'fictioneer_ajax_get_comment_form',
    'post_id': _$$$('comments').dataset.postId
  })
  .then(response => {
    if (response.success) {
      // Get HTML
      const temp = document.createElement('div');

      temp.innerHTML = response.data.html;

      // Get form elements
      const commentPostId = temp.querySelector('#comment_post_ID'),
            cancelReplyLink = temp.querySelector('#cancel-comment-reply-link'),
            logoutLink = temp.querySelector('.logout-link');

      // Fix form elements
      if (commentPostId) {
        commentPostId.value = response.data.postId;
      }

      if (cancelReplyLink) {
        cancelReplyLink.href = "#respond";
      }

      if (logoutLink) {
        logoutLink.href = _$$$('comments').dataset.logoutUrl;
      }

      // Output HTML
      fcn_ajaxCommentForm.innerHTML = temp.innerHTML;
      temp.remove();

      // Append stack contents (if any)
      fcn_applyCommentStack();

      // Bind events
      fcn_addTextareaEvents();
      fcn_addCommentFormEvents();
      fcn_addPrivateToggleEvents()

      if (fcn_theRoot.dataset.ajaxSubmit) {
        fcn_bindAJAXCommentSubmit();
      }

      // JS trap (if active)
      fcn_addJSTrap();
    } else {
      errorNote = fcn_buildErrorNotice(response.data.error);
    }
  })
  .catch(error => {
    errorNote = fcn_buildErrorNotice(error);
  })
  .then(() => {
    // Update view regardless of success
    fcn_ajaxCommentForm.classList.remove('comments-skeleton');

    // Add error (if any)
    if (errorNote) {
      fcn_ajaxCommentForm.innerHTML = '';
      fcn_ajaxCommentForm.appendChild(errorNote);
    }
  });
}

// =============================================================================
// COMMENT STACK
// =============================================================================

var /** @type {String[]} */ fcn_commentStack = [];

/**
 * Append comment stack content to comment (if any)
 *
 * @since 5.7.0
 * @param {HTMLElement=} textarea - Optional. The targeted textarea.
 */

function fcn_applyCommentStack(textarea = null) {
  textarea = textarea ?? _$$$('comment');

  if (textarea) {
    // Append stack content to comment
    fcn_commentStack.forEach(node => {
      textarea.value += node;
    });

    // Empty stack
    fcn_commentStack = [];

    // Resize textarea if necessary
    fcn_textareaAdjust(textarea);
  }
}

// =============================================================================
// EVENT DELEGATES
// =============================================================================

_$('.fictioneer-comments')?.addEventListener('click', event => {
  const clickTarget = event.target.closest('[data-click]'),
        clickAction = clickTarget?.dataset.click;

  if (!clickAction) {
    return;
  }

  switch (clickAction) {
    case 'submit-inline-comment-edit':
      fcn_submitInlineCommentEdit(clickTarget);
      break;
    case 'cancel-inline-comment-edit':
      fcn_cancelInlineCommentEdit(clickTarget);
      break;
    case 'trigger-inline-comment-edit':
      fcn_triggerInlineCommentEdit(clickTarget);
      break;
    case 'delete-my-comment':
      fcn_deleteMyComment(clickTarget);
      break;
    case 'flag-comment':
      fcn_flagComment(clickTarget);
      break;
  }
});
