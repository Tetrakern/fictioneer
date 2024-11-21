// =============================================================================
// CHECK IF USER IS POST AUTHOR
// =============================================================================

document.addEventListener('fcnUserDataReady', () => {
  if (fcn_getUserData().fingerprint == fcn_theRoot.dataset.authorFingerprint) {
    fcn_theBody.classList.add('is-post-author');
  }
});

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
  _$(fictioneer_comments.form_selector ?? '#comment')?.addEventListener(
    'focus',
    event => {
      fcn_revealCommentFormInputs(event.currentTarget);
    },
    { once: true }
  );
}

fcn_addCommentFormEvents();

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
  const href = options.href ? ' href="' + options.href + '" target="_blank" rel="nofollow noreferrer noopener"' : '';
  const brackets = options.shortcode ? ['[', ']'] : ['<', '>'];
  const start = element.selectionStart;
  const end = element.selectionEnd;
  const open = brackets[0] + tag + href + brackets[1];
  const close = brackets[0] + '/' + tag + brackets[1];
  const text = open + element.value.substring(start, end) + close;

  element.value = element.value.substring(0, start) + text + element.value.substring(end, element.value.length);
  element.setSelectionRange((start + open.length), (end + open.length));
  element.focus();
}

// =============================================================================
// AJAX COMMENT SUBMISSION
// =============================================================================

/**
 * Bind AJAX request to comment form submit and override default behavior.
 *
 * @since 5.0.0
 * @since 5.20.3 - Use the FormData interface.
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
    const form = e.currentTarget;
    const formData = new FormData(form);
    const formDataObj = Object.fromEntries(formData.entries());
    const button = form.querySelector('[name="submit"]');
    const content = _$(fictioneer_comments.form_selector ?? '#comment');
    const author = form.querySelector('[name="author"]');
    const email = form.querySelector('[name="email"]');
    const privacy_consent = form.querySelector('[name="fictioneer-privacy-policy-consent"]');
    const parentId = formDataObj['comment_parent'] ?? 0;
    const parent = _$$$(`comment-${parentId}`);
    const order = form.closest('.fictioneer-comments')?.dataset.order ?? 'desc';

    let emailValidation = true;
    let contentValidation = true;
    let privacyValidation = true;

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
      ...{
        'action': 'fictioneer_ajax_submit_comment',
        'content': content.value,
        'depth': parent ? parseInt(parent.dataset.depth) + 1 : 1,
        'fcn_fast_comment_ajax': 1
      },
      ...formDataObj
    }

    // Optional payload
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
            response.data.failure ?? response.data.error ?? fictioneer_tl.notification.error,
            'comment-submit-error-notice',
            false
          ),
          form.firstChild
        );

        // Make sure the actual error (if any) is printed to the console too
        if (response.data.failure && response.data.error) {
          console.error('Error:', response.data.error);
        }
      } else {
        // Insert new comment
        let target = _$('.commentlist');
        let insertMode = 'insertBefore';

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

        // Clean-up form
        if (_$$$('comment_parent').value != '0') {
          _$$$('cancel-comment-reply-link').click();
        }

        content.value = '';
        content.style.height = '';

        // Update URL
        const refresh = window.location.protocol + '//' + window.location.host + window.location.pathname;

        if (order != 'desc' || fcn_urlParams.corder) {
          fcn_urlParams['corder'] = order;
        }

        if (response.data.commentcode) {
          fcn_urlParams['commentcode'] = response.data.commentcode;
        }

        let params = Object.entries(fcn_urlParams).map(([key, value]) => `${key}=${value}`).join('&');

        if (params !== '') {
          params = `?${params}`;
        }

        history.replaceState({ path: refresh }, '', refresh + params + `#comment-${response.data.comment_id}`);

        // Scroll to new comment
        commentNode.scrollIntoView({ behavior: 'smooth' });
      }
    })
    .catch(error => {
      // Remove any old error
      _$$$('comment-submit-error-notice')?.remove();

      // Add error message
      form.insertBefore(
        fcn_buildErrorNotice(`${error.statusText} (${error.status})`, 'comment-submit-error-notice'),
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
  const fct_commentFormObserver = new IntersectionObserver(
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
    'post_id': _$$$('comments').dataset.postId,
    'fcn_fast_comment_ajax': 1
  })
  .then(response => {
    if (response.success) {
      // Get HTML
      const temp = document.createElement('div');

      temp.innerHTML = response.data.html;

      // Get form elements
      const commentPostId = temp.querySelector('#comment_post_ID');
      const cancelReplyLink = temp.querySelector('#cancel-comment-reply-link');
      const logoutLink = temp.querySelector('.logout-link');

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
      fcn_addCommentFormEvents();

      if (fcn_theRoot.dataset.ajaxSubmit) {
        fcn_bindAJAXCommentSubmit();
      }

      // AJAX nonce
      fcn_addNonceHTML(response.data.nonceHtml);
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
 * @param {HTMLElement=} editor - Optional. The targeted editor element.
 */

function fcn_applyCommentStack(editor = null) {
  editor = editor ?? _$(fictioneer_comments.form_selector ?? '#comment');

  // Abort
  if (!editor) {
    return;
  }

  // Append stack content to comment
  if (editor.tagName == 'TEXTAREA') {
    fcn_commentStack.forEach(node => {
      editor.value += node;
    });

    // Resize editor if necessary
    fcn_adjustTextarea(editor);
  } else if (editor.tagName == 'DIV') {
    fcn_commentStack.forEach(node => {
      editor.innerHTML += node;
    });
  }

  // Empty stack
  fcn_commentStack = [];
}

// =============================================================================
// STIMULUS: FICTIONEER COMMENT FORM
// =============================================================================

// application.getControllerForElementAndIdentifier(element, 'fictioneer-comment-form').method()

application.register('fictioneer-comment-form', class extends Stimulus.Controller  {
  initialize() {
    // Setup (cannot properly set data attributes on form)
    this.respond = _$$$('respond');
    this.form = this.respond.querySelector('.comment-form');
    this.textarea = this.respond.querySelector('[name="comment"]');
    this.toolbar = this.respond.querySelector('.fictioneer-comment-toolbar');
    this.privateToggle = this.respond.querySelector('[name="fictioneer-private-comment-toggle"]');

    // Private comment toggle
    this.privateToggle.addEventListener('change', () => {
      this.respond.classList.toggle('_private', this.privateToggle.checked);
    });

    // Textarea
    if (this.textarea.classList.contains('adaptive-textarea')) {
      this.textarea.addEventListener('input', () => {
        fcn_adjustTextarea(this.textarea);
      });

      this.textarea.addEventListener('keydown', event => {
        this.bbcodes(event);
      });
    }

    // Comment toolbar
    this.toolbar.addEventListener('click', event => {
      const formattingButton = event.target.closest('[data-bbcode]');

      if (formattingButton) {
        fcn_wrapInTag(
          _$(fictioneer_comments.form_selector ?? '#comment'),
          formattingButton.dataset.bbcode,
          { 'shortcode': true }
        );
      }
    });

    // Spam protection
    this.addJSTrap();
  }

  bbcodes(event) {
    if (
      _$('.fictioneer-comment-toolbar') &&
      document.activeElement.tagName === 'TEXTAREA' &&
      (event.ctrlKey || event.metaKey)
    ) {
      const key = event.key.toLowerCase();

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
  }

  /**
   * Append required field after the site has loaded.
   *
   * @description This prevents comment form submits from working if JavaScript
   * is not enabled, which is true for most bots. Yes, the value is just for
   * show and could be used better. A hashcash for example, to make spamming
   * really expensive!
   *
   * @since 5.xx.x
   */

  addJSTrap() {
    this.form.appendChild(fcn_html`
      <input type="hidden" name="fictioneer_comment_validator" value="299792458">
    `);
  }
});

// =============================================================================
// STIMULUS: FICTIONEER COMMENT
// =============================================================================

application.register('fictioneer-comment', class extends Stimulus.Controller {
  static targets = ['modMenuToggle', 'modMenu', 'modIcon', 'editLink', 'flagButton', 'deleteButton', 'editButton', 'content', 'inlineEditWrapper', 'inlineEditTextarea', 'inlineEditButtons', 'inlineEditSubmit', 'editNote'];

  static values = {
    id: Number,
    timestamp: Number,
    fingerprint: String
  }

  connect() {
    // Comment element separated because it also contains child comments
    this.comment = this.element.closest('.fictioneer-comment');

    // Get comment edit link
    if (this.hasModMenuTarget) {
      this.editLink = this.modMenuTarget.dataset.editLink;
    }

    // Reveal self-delete and inline edit buttons
    if (fcn_getUserData().fingerprint) {
      this.#revealDeleteButton();
      this.#revealEditButton();
    } else {
      document.addEventListener('fcnUserDataReady', () => {
        this.#revealDeleteButton();
        this.#revealEditButton();
      });
    }

    // Adaptable textarea
    if (this.hasInlineEditTextareaTarget) {
      this.inlineEditTextareaTarget.addEventListener('input', () => {
        fcn_adjustTextarea(this.inlineEditTextareaTarget);
      });
    }
  }

  /**
   * Toggle display of moderation popup menu.
   *
   * @since 5.xx.x
   */

  toggle() {
    this.#toggleMenu();
  }

  /**
   * Delegate to 'approve' action.
   *
   * @since 5.xx.x
   */

  approve() {
    this.#modRequest('approve');
  }

  /**
   * Delegate to 'unapprove' action.
   *
   * @since 5.xx.x
   */

  unapprove() {
    this.#modRequest('unapprove');
  }

  /**
   * Delegate to 'offensive' action.
   *
   * @since 5.xx.x
   */

  offensive() {
    this.#modRequest('offensive');
  }

  /**
   * Delegate to 'unoffensive' action.
   *
   * @since 5.xx.x
   */

  unoffensive() {
    this.#modRequest('unoffensive');
  }

  /**
   * Delegate to 'open' action.
   *
   * @since 5.xx.x
   */

  open() {
    this.#modRequest('open');
  }

  /**
   * Delegate to 'close' action.
   *
   * @since 5.xx.x
   */

  close() {
    this.#modRequest('close');
  }

  /**
   * Delegate to 'sticky' action.
   *
   * @since 5.xx.x
   */

  sticky() {
    this.#modRequest('sticky');
  }

  /**
   * Delegate to 'unsticky' action.
   *
   * @since 5.xx.x
   */

  unsticky() {
    this.#modRequest('unsticky');
  }

  /**
   * Delegate to 'trash' action.
   *
   * @since 5.xx.x
   */

  trash() {
    this.#modRequest('trash');
  }

  /**
   * Delegate to 'spam' action.
   *
   * @since 5.xx.x
   */

  spam() {
    this.#modRequest('spam');
  }

  /**
   * Close menu when mouse leaves comment.
   *
   * @since 5.xx.x
   */

  mouseLeave() {
    if (this.modMenuTarget.querySelector('*')) {
      this.#toggleMenu('close');
      fcn_lastClicked?.classList.remove('last-clicked'); // Global
      fcn_lastClicked = null; // Global
    }
  }

  /**
   * AJAX: Flag a comment.
   *
   * @since 5.xx.x
   */

  flag() {
    // Only if user is logged in and button exists
    if (!fcn_isLoggedIn || !this.hasFlagButtonTarget) {
      return;
    }

    // Abort if another AJAX action is in progress
    if (this.comment.classList.contains('ajax-in-progress')) {
      return;
    }

    // Lock comment until AJAX action is complete
    this.comment.classList.add('ajax-in-progress');

    // Request
    fcn_ajaxPost({
      'action': 'fictioneer_ajax_report_comment',
      'id': this.idValue,
      'dubious': this.flagButtonTarget.classList.contains('_dubious'),
      'fcn_fast_comment_ajax': 1
    })
    .then(response => {
      if (response.success) {
        this.flagButtonTarget.classList.toggle('on', response.data.flagged);
        this.flagButtonTarget.classList.remove('_dubious');

        // If report was dubious, it will be resynchronized
        if (response.data.resync) {
          fcn_showNotification(response.data.resync);
        }
      } else {
        fcn_showNotification(
          response.data.failure ?? response.data.error ?? fictioneer_tl.notification.error,
          5,
          'warning'
        );
        console.error('Error:', response.data.error ?? response.data.failure ?? 'Unknown');
      }
    })
    .catch(error => {
      this.#showError(error);
    })
    .then(() => {
      this.comment.classList.remove('ajax-in-progress');
    });
  }

  /**
   * AJAX: Delete comment (by the owner).
   *
   * @since 5.xx.x
   */

  selfDelete() {
    // Only if user is logged in and button exists
    if (!fcn_isLoggedIn || !this.hasDeleteButtonTarget) {
      return;
    }

    // Prompt for confirmation
    const confirm = prompt(this.deleteButtonTarget.dataset.dialogMessage);

    if (!confirm || confirm.toLowerCase() != this.deleteButtonTarget.dataset.dialogConfirm.toLowerCase()) {
      return;
    }

    // Abort if another AJAX action is in progress
    if (this.comment.classList.contains('ajax-in-progress')) {
      return;
    }

    // Lock comment until AJAX action is complete
    this.comment.classList.add('ajax-in-progress');

    // Request
    fcn_ajaxPost({
      'action': 'fictioneer_ajax_delete_my_comment',
      'comment_id': this.idValue,
      'fcn_fast_comment_ajax': 1
    })
    .then(response => {
      if (response.success) {
        this.comment.classList.add('_deleted');
        this.element.innerHTML = response.data.html;
      } else {
        fcn_showNotification(
          response.data.failure ?? response.data.error ?? fictioneer_tl.notification.error,
          5,
          'warning'
        );
        console.error('Error:', response.data.error ?? response.data.failure ?? 'Unknown');
      }
    })
    .catch(error => {
      this.#showError(error);
    })
    .then(() => {
      this.comment.classList.remove('ajax-in-progress');
    });
  }

  /**
   * Start editing comment inline.
   *
   * @since 5.xx.x
   */

  startEditInline() {
    const template = _$$$('template-comment-inline-edit-form')?.content.cloneNode(true);

    if (!template) {
      return;
    }

    this.comment.classList.add('_editing');
    this.commentEditUndo = this.inlineEditTextareaTarget.value;
    this.inlineEditWrapperTarget.appendChild(template);
    this.contentTarget.hidden = true;
    this.inlineEditWrapperTarget.hidden = false;
    this.inlineEditTextareaTarget.style.height = `${this.inlineEditTextareaTarget.scrollHeight}px`;

    fcn_adjustTextarea(this.inlineEditTextareaTarget);
  }

  /**
   * Cancel editing comment inline.
   *
   * @since 5.xx.x
   */

  cancelEditInline() {
    this.#restoreComment(true);
  }

  /**
   * Submit inline-edited comment.
   *
   * @since 5.xx.x
   */

  submitEditInline() {
    if (this.inlineEditTextareaTarget.value === this.commentEditUndo) {
      this.#restoreComment(true);
      return;
    }

    this.inlineEditWrapperTarget.classList.add('ajax-in-progress');
    this.inlineEditSubmitTarget.innerHTML = this.inlineEditSubmitTarget.dataset.disabled;
    this.inlineEditSubmitTarget.disabled = true;

    fcn_ajaxPost({
      'action': 'fictioneer_ajax_edit_comment',
      'comment_id': this.idValue,
      'content': this.inlineEditTextareaTarget.value,
      'fcn_fast_comment_ajax': 1
    })
    .then(response => {
      if (response.success) {
        this.contentTarget.innerHTML = response.data.content;

        this.#restoreComment(false, response.data.raw);

        // Edit note
        let editNote = this.editNoteTarget;

        if (!this.hasEditNoteTarget) {
          editNote = document.createElement('div');
        }

        editNote.classList.add('fictioneer-comment__edit-note');
        editNote.dataset.fictioneerCommentTarget = 'editNote';
        editNote.innerHTML = response.data.edited;

        this.contentTarget.parentNode.appendChild(editNote);
      } else {
        this.#restoreComment(true);

        fcn_showNotification(
          response.data.failure ?? response.data.error ?? fictioneer_tl.notification.error,
          5,
          'warning'
        );
        console.error('Error:', response.data.error ?? response.data.failure ?? 'Unknown');
      }
    })
    .catch(error => {
      this.#restoreComment(true);
      this.#showError(error);
    })
    .then(() => {
      this.inlineEditWrapperTarget.classList.remove('ajax-in-progress');

      if (this.hasInlineEditSubmitTarget) {
        this.inlineEditSubmitTarget.innerHTML = this.inlineEditSubmitTarget.dataset.enabled;
        this.inlineEditSubmitTarget.disabled = false;
      }
    });
  }

  bbcodes(event) {
    if (
      _$('.fictioneer-comment-toolbar') &&
      document.activeElement.tagName === 'TEXTAREA' &&
      (event.ctrlKey || event.metaKey)
    ) {
      const key = event.key.toLowerCase();

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
  }

  // =====================
  // ====== PRIVATE ======
  // =====================

  /**
   * Reveal self-delete button on comment
   *
   * @since 5.xx.x
   */

  #revealDeleteButton() {
    if (this.hasDeleteButtonTarget && this.#matchFingerprint()) {
      this.deleteButtonTarget.hidden = false;
    }
  }

  /**
   * Reveal inline edit button on comment
   *
   * @since 5.xx.x
   */

  #revealEditButton() {
    let editTime = parseInt(document.documentElement.dataset.editTime);

    if (!editTime) {
      return;
    }

    editTime = editTime > 0 ? editTime * 60000 : editTime;

    if (this.hasEditButtonTarget && this.#matchFingerprint()) {
      // -1 means no time limit
      if (editTime > 0 && parseInt(this.timestampValue) + editTime < Date.now()) {
        return;
      }

      this.editButtonTarget.hidden = false;
    }
  }

  /**
   * Check whether the comment fingerprint matches the user's
   *
   * @since 5.xx.x
   * @return {Boolean} True or false.
   */

  #matchFingerprint() {
    const userFingerprint = fcn_getUserData().fingerprint;

    if (!userFingerprint || !this.hasFingerprintValue) {
      return false;
    }

    return userFingerprint === this.fingerprintValue;
  }

  /**
   * Restore comment to non-editing state.
   *
   * @since 5.xx.x
   * @param {Boolean} [undo] - Whether to restore the previous content. Default false.
   * @param {String} [update] - Updated content. Default null.
   */

  #restoreComment(undo = false, update = null) {
    this.comment.classList.remove('_editing');
    this.contentTarget.hidden = false;
    this.inlineEditWrapperTarget.hidden = true;

    if (this.hasInlineEditButtonsTarget) {
      this.inlineEditButtonsTarget.remove();
    }

    if (undo && this.commentEditUndo) {
      this.inlineEditTextareaTarget.value = this.commentEditUndo;
    } else if (update) {
      this.inlineEditTextareaTarget.value = update;
    }

    this.commentEditUndo = null;
  }

  /**
   * Add or remove moderation menu HTML.
   *
   * @since 5.xx.x
   * @param {String} [force] - Optional. Force 'open' or 'close'.
   */

  #toggleMenu(force = null) {
    const template = _$$$('template-comment-frontend-moderation-menu')?.content.cloneNode(true);

    if (!template || !this.hasModMenuTarget) {
      return;
    }

    if ((this.modMenuTarget.querySelector('*') || force === 'close') && force !== 'open') {
      this.modMenuTarget.innerHTML = '';
    } else {
      this.modMenuTarget.appendChild(template);
      this.editLinkTarget.href = this.editLink;
    }
  }

  /**
   * Display an moderation error notification for 5 seconds.
   *
   * @since 5.xx.x
   * @param {String} message - Error message.
   */

  #showModError(message) {
    this.modIconTarget.classList = 'fa-solid fa-triangle-exclamation mod-menu-toggle-icon';
    this.modIconTarget.style.color = 'var(--notice-warning-background)';
    this.modMenuToggleTarget.style.opacity = '1';

    if (message) {
      fcn_showNotification(message, 5, 'warning');
    }
  }

  /**
   * Display and log an error notification for 5 seconds.
   *
   * @since 5.xx.x
   * @param {Object} error - Error object from Promise.
   */

  #showError(error) {
    if (error.status && error.statusText) {
      fcn_showNotification(`${error.status}: ${error.statusText}`, 5, 'warning');
    }

    console.error('Error:', error);
  }

  /**
   * AJAX: Perform moderation action.
   *
   * @since 5.xx.x
   * @param {String} operation - The moderation operation.
   */

  #modRequest(operation) {
    // Abort if another AJAX action is in progress
    if (this.comment.classList.contains('ajax-in-progress')) {
      return;
    }

    // Lock comment until AJAX action is complete
    this.comment.classList.add('ajax-in-progress');

    // Prepare comment for style transition
    if (operation == 'trash' || operation == 'spam') {
      this.comment.style.height = this.comment.clientHeight + 'px';
    }

    // Request
    fcn_ajaxPost({
      'action': 'fictioneer_ajax_moderate_comment',
      'operation': operation,
      'id': this.idValue,
      'fcn_fast_comment_ajax': 1
    })
    .then(response => {
      if (response.success) {
        const operation = response.data.operation;
        const operationMap = {
          sticky: { action: 'add', className: '_sticky' },
          unsticky: { action: 'remove', className: '_sticky' },
          offensive: { action: 'add', className: '_offensive' },
          unoffensive: { action: 'remove', className: '_offensive' },
          approve: { action: 'remove', className: '_unapproved' },
          unapprove: { action: 'add', className: '_unapproved' },
          open: { action: 'remove', className: '_closed' },
          close: { action: 'add', className: '_closed' }
        };

        if (operation in operationMap) {
          const { action, className } = operationMap[operation];

          this.comment.classList[action](className);
        } else if (operation === 'trash' || operation === 'spam') {
          this.comment.style.cssText = "overflow: hidden; height: 0; margin: 0; opacity: 0;";
        }
      } else {
        this.#showModError(response.data.failure ?? response.data.error ?? fictioneer_tl.notification.error);
      }
    })
    .catch(error => {
      this.#showModError((error?.status && error?.statusText) ? `${error.status}: ${error.statusText}` : error);
    })
    .then(() => {
      this.comment.classList.remove('ajax-in-progress');
      this.#toggleMenu('close');
      fcn_lastClicked?.classList.remove('last-clicked'); // Global
      fcn_lastClicked = null; // Global
    });
  }
});
