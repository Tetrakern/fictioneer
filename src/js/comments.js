// =============================================================================
// CHECK IF USER IS POST AUTHOR
// =============================================================================

document.addEventListener('fcnUserDataReady', () => {
  if (fcn().userData().fingerprint == document.documentElement.dataset.authorFingerprint) {
    document.body.classList.add('is-post-author');
  }
});

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
// REQUEST COMMENTS FORM VIA AJAX
// =============================================================================

const /** @const {HTMLElement} */ fcn_ajaxCommentForm = _$$$('ajax-comment-form-target');

// Check whether form target exists...
if (fcn_ajaxCommentForm) {
  document.addEventListener('fcnUserDataReady', () => {
    fcn_setupCommentFormObserver();
  });
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

      // AJAX nonce
      _$$$('fictioneer-ajax-nonce')?.remove();
      document.body.appendChild(fcn_html`${response.data.nonceHtml}`);
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
  editor = editor ?? _$(FcnGlobals.commentFormSelector);

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

application.register('fictioneer-comment-form', class extends Stimulus.Controller  {
  static get targets() {
    return ['submit', 'cancel', 'textarea', 'privateToggle', 'emailNotificationToggle', 'author', 'email', 'cookies', 'privacyPolicy']
  }

  initialize() {
    // Setup (cannot properly set data attributes on all form elements)
    this.respond = this.element.closest('#respond');
    this.order = this.respond.closest('.fictioneer-comments')?.dataset.order ?? 'desc';
    this.parent = this.element.querySelector('#comment_parent');

    // Spam protection
    this.addJSTrap();
  }

  /**
   * Resize textarea on input when necessary
   *
   * @since 5.xx.x
   */

  adjustTextarea() {
    fcn_adjustTextarea(this.textareaTarget);
  }

  /**
   * Handle clicks into toolbar
   *
   * @since 5.xx.x
   * @param {Event} event - The event.
   */

  toolbarButtons(event) {
    const formattingButton = event.target.closest('[data-bbcode]');

    if (formattingButton) {
      fcn_wrapInTag(
        this.textareaTarget,
        formattingButton.dataset.bbcode,
        { 'shortcode': true }
      );
    }
  }

  /**
   * Apply BBCodes on key combo
   *
   * @since 5.xx.x
   * @param {Event} event - The event.
   */

  keyComboCodes(event) {
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
    this.element.appendChild(fcn_html`
      <input type="hidden" name="fictioneer_comment_validator" value="299792458">
    `);
  }

  /**
   * Reveal comment input fields.
   *
   * @since 5.xx.x
   */

  revealFormInputs() {
    this.respond.querySelectorAll('.fictioneer-respond__form-actions, .fictioneer-respond__form-bottom').forEach(element => {
      element.classList.remove('hidden');
    });
  }

  /**
   * AJAX: Submit comment (if enabled).
   *
   * @since 5.xx.x
   * @param {Event} event - The event.
   */

  ajaxSubmit(event) {
    // Delay trap (cannot be done server-side because of caching)
    if (Date.now() < FcnGlobals.pageLoadTimestamp + 3000) {
      return;
    }

    this.element.classList.remove('_invalid');

    // Check whether AJAX submit is enabled, use normal form submission
    if (!document.documentElement.dataset.ajaxSubmit) {
      if (!this.element.reportValidity()) {
        this.element.classList.add('_invalid');
        return;
      }

      this.submitTarget.value = this.submitTarget.dataset.disabled;
      this.submitTarget.classList.add('_disabled');

      setTimeout(() => {
        this.submitTarget.value = this.submitTarget.dataset.enabled;
        this.submitTarget.classList.remove('_disabled');
      }, 2000);

      return;
    }

    event.preventDefault();

    if (!this.element.reportValidity()) {
      this.element.classList.add('_invalid');
      return;
    }

    // Get comment form input
    const formData = new FormData(this.element);
    const formDataObj = Object.fromEntries(formData.entries());
    const parentId = formDataObj['comment_parent'] ?? 0;
    const parent = _$$$(`comment-${parentId}`);

    const contentValidation = this.textareaTarget.value.length > 1;
    let emailValidation = true;
    let privacyValidation = true;

    // Validate content
    this.textareaTarget.classList.toggle('_error', !contentValidation);

    // Validate privacy policy checkbox
    if (this.hasPrivacyPolicyTarget) {
      privacyValidation = this.privacyPolicyTarget.checked;
      this.privacyPolicyTarget.classList.toggle('_error', !privacyValidation);
    }

    // Validate email address pattern (better validation server-side)
    if (this.hasEmailTarget && this.emailTarget?.value.length > 0) {
      emailValidation = /\S+@\S+\.\S+/.test(this.emailTarget.value);
      this.emailTarget.classList.toggle('_error', !emailValidation);
    }

    // Abort if...
    if (!contentValidation || !privacyValidation || !emailValidation) {
      return;
    }

    // Disable form during submit
    this.element.classList.add('ajax-in-progress');
    this.submitTarget.value = this.submitTarget.dataset.disabled;
    this.submitTarget.disabled = true;

    // Payload
    const payload = {
      ...{
        'action': 'fictioneer_ajax_submit_comment',
        'content': this.textareaTarget.value,
        'depth': parent ? parseInt(parent.dataset.depth) + 1 : 1,
        'fcn_fast_comment_ajax': 1
      },
      ...formDataObj
    }

    // Optional payload
    if (this.hasEmailTarget && this.emailTarget?.value) {
      payload['email'] = this.emailTarget.value;
    }

    if (this.hasAuthorTarget && this.authorTarget?.value) {
      payload['author'] = this.authorTarget.value;
    }

    // Remove any old error
    _$$$('comment-submit-error-notice')?.remove();

    // Request
    fcn_ajaxPost(payload)
    .then(response => {
      // Comment successfully saved?
      if (!response.success || !response.data?.comment) {
        this.element.insertBefore(
          fcn_buildErrorNotice(
            response.data.failure ?? response.data.error ?? fictioneer_tl.notification.error,
            'comment-submit-error-notice',
            false
          ),
          this.element.firstChild
        );

        console.error('Error:', response.data.error ?? response.data.failure ?? 'Unknown');
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

        // Prepare element
        let commentNode = document.createElement('div');

        commentNode.innerHTML = response.data.comment;
        commentNode = commentNode.firstChild;

        // Add to DOM
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

        // Clean up form
        this.textareaTarget.value = '';
        this.textareaTarget.style.height = '';

        if (this.parent.value != '0') {
          this.cancelTarget.click();
        }

        // Update URL
        const refresh = window.location.protocol + '//' + window.location.host + window.location.pathname;

        if (this.order != 'desc' || FcnGlobals.urlParams.corder) {
          FcnGlobals.urlParams['corder'] = this.order;
        }

        if (response.data.commentcode) {
          FcnGlobals.urlParams['commentcode'] = response.data.commentcode;
        }

        let params = Object.entries(FcnGlobals.urlParams).map(([key, value]) => `${key}=${value}`).join('&');

        if (params !== '') {
          params = `?${params}`;
        }

        history.replaceState({ path: refresh }, '', refresh + params + `#comment-${response.data.comment_id}`);

        // Scroll to new comment
        commentNode.scrollIntoView({ behavior: 'smooth' });
      }
    })
    .catch(error => {
      this.element.insertBefore(
        fcn_buildErrorNotice(`${error.statusText} (${error.status})`, 'comment-submit-error-notice'),
        this.element.firstChild
      );
    })
    .then(() => {
      this.element.classList.remove('ajax-in-progress');
      this.submitTarget.disabled = false;
      this.submitTarget.value = this.submitTarget.dataset.enabled;
    });
  }

  /**
   * Perform actions when the private comment toggle changes.
   *
   * @since 5.xx.x
   */

  togglePrivate() {
    this.respond.classList.toggle('_private', this.privateToggleTarget.checked);
  }
});

// =============================================================================
// STIMULUS: FICTIONEER COMMENT
// =============================================================================

application.register('fictioneer-comment', class extends Stimulus.Controller {
  static get targets() {
    return ['modMenuToggle', 'modMenu', 'modIcon', 'editLink', 'flagButton', 'deleteButton', 'editButton', 'content', 'inlineEditWrapper', 'inlineEditTextarea', 'inlineEditButtons', 'inlineEditSubmit', 'editNote']
  }

  static values = {
    id: Number,
    timestamp: Number,
    fingerprint: String
  }

  comment = this.element.closest('.fictioneer-comment');
  menuOpen = false;

  /**
   * Stimulus Controller initialize lifecycle callback.
   *
   * @since 5.xx.x
   */

  initialize() {
    // Listen to global last-clicked handler
    document.addEventListener('fcnLastClicked', event => {
      if (!event.detail?.element?.closest(`[data-fictioneer-comment-id-value="${this.idValue}"]`)) {
        this.#hideMenu();
      }
    });

    // Listen to clicks outside
    document.addEventListener('click', event => {
      if (!event.target.closest(`[data-fictioneer-comment-id-value="${this.idValue}"]`)) {
        this.#hideMenu();
      }
    });
  }

  /**
   * Stimulus Controller connect lifecycle callback.
   *
   * @since 5.xx.x
   */

  connect() {
    // Get comment edit link
    if (this.hasModMenuTarget) {
      this.editLink = this.modMenuTarget.dataset.editLink;
    }

    // Reveal self-delete and inline edit buttons
    if (fcn().userData().fingerprint) {
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

    // Listen to outside last-clicked toggles
    document.addEventListener('toggledLastClick', event => {
      if (!event.detail.force && this.menuOpen) {
        this.#hideMenu();
      }
    });
  }

  /**
   * Add or remove moderation menu HTML.
   *
   * @since 5.xx.x
   * @param {String} [force] - Optional. Force 'open' or 'close'.
   */

  toggleMenu(force = null) {
    if (!this.hasModMenuTarget) {
      return;
    }

    if ((this.menuOpen || force === 'close') && force !== 'open') {
      this.#hideMenu();
    } else {
      this.#showMenu();
    }
  }

  mouseLeave() {
    if (this.hasModMenuTarget && this.menuOpen) {
      this.#hideMenu();
      document.dispatchEvent(new CustomEvent('fcnRemoveLastClicked'));
    }
  }

  /**
   * Handle clicks anywhere in comment.
   *
   * @since 5.xx.x
   * @param {Event} event - The event.
   */

  commentClick(event) {
    if (!event.target.closest('[data-fictioneer-comment-target="modMenuToggle"]')) {
      this.#hideMenu();
    }
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
   * AJAX: Flag a comment.
   *
   * @since 5.xx.x
   */

  flag() {
    // Only if user is logged in and button exists
    if (!fcn().loggedIn() || !this.hasFlagButtonTarget) {
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
    if (!fcn().loggedIn() || !this.hasDeleteButtonTarget) {
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

  /**
   * Handle cmd/ctrl + key combos.
   *
   * @since 5.xx.x
   * @param {Event} event - The event.
   */

  keyComboCodes(event) {
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
    const userFingerprint = fcn().userData().fingerprint;

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
   * Append menu template and set edit link.
   *
   * @since 5.xx.x
   */

  #showMenu() {
    const template = _$$$('template-comment-frontend-moderation-menu')?.content.cloneNode(true);

    if (template && this.hasModMenuTarget) {
      this.modMenuTarget.appendChild(template);
      this.editLinkTarget.href = this.editLink;
      this.menuOpen = true;
    }
  }

  /**
   * Remove menu items and reset last-clicked element.
   *
   * @since 5.xx.x
   */

  #hideMenu() {
    if (this.hasModMenuTarget && this.menuOpen) {
      this.menuOpen = false;

      while (this.modMenuTarget.firstChild) {
        this.modMenuTarget.removeChild(this.modMenuTarget.firstChild);
      }
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
      this.#hideMenu();
      document.dispatchEvent(new CustomEvent('fcnRemoveLastClicked'));
    });
  }
});
