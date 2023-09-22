// =============================================================================
// PROGRESSIVE ACTIONS
// =============================================================================

/**
 * Toggles progression state of an element.
 *
 * @since 5.7.2
 * @param {HTMLElement} element - The element.
 * @param {Boolean|null} force - Whether to disable or enable. Defaults to
 *                               the opposite of the current state.
 */

function fcn_toggleInProgress(element, force = null) {
  force = force !== null ? force : !element.disabled;

  if (force) {
    element.dataset.enableWith = element.innerHTML;
    element.innerHTML = element.dataset.disableWith;
    element.disabled = true;
    element.classList.add('disabled');
  } else {
    element.innerHTML = element.dataset.enableWith;
    element.disabled = false;
    element.classList.remove('disabled');
  }
}

// =============================================================================
// SCHEMAS
// =============================================================================

/**
 * Purges the schema of a post.
 *
 * @since 5.7.2
 * @param {Number} post_id - ID of the post.
 */

function fcn_purgeSchema(post_id) {
  const button = _$(`a[data-id="${post_id}"]`);
  const actions = button.closest('.row-actions');
  const row = button.closest('tr');

  row.classList.add('no-schema');
  actions.remove();

  // Request
  fcn_ajaxPost({
    'action': 'fictioneer_ajax_purge_schema',
    'nonce': document.getElementById('fictioneer_admin_nonce').value,
    'post_id': post_id
  })
  .then(response => {
    if (!response.success) {
      console.log(response.data);
    }
  })
  .catch(error => {
    console.log(error);
  });
}

// Listen for clicks on schema purge buttons
_$$('[data-purge-schema]').forEach(element => {
  element.addEventListener(
    'click',
    event => {
      event.preventDefault();
      fcn_purgeSchema(event.currentTarget.dataset.id)
    }
  );
});

/**
 * Purges the schema of all posts.
 *
 * @since 5.7.2
 * @param {Number=0} offset - Offset of post IDs to query.
 * @param {Number|null} total - The total number of posts to be processed.
 * @param {Number=0} processed - The total number of posts processed so far.
 */

function fcn_purgeAllSchemas(offset = 0, total = null, processed = 0) {
   // --- Setup ----------------------------------------------------------------

  const buttons = _$$('[data-action-purge-all-schemas]');

  if ( total === null ) {
    buttons.forEach(element => {
      fcn_toggleInProgress(element, true);
    });
  }

  // --- Request ---------------------------------------------------------------

  fcn_ajaxPost({
    'action': 'fictioneer_ajax_purge_all_schemas',
    'offset': offset,
    'nonce': document.getElementById('fictioneer_admin_nonce').value
  })
  .then(response => {
    if (!response.data.finished) {
      const progress = parseInt(processed / Math.max(response.data.total, 1) * 100);
      const part = progress > 0 ? ` ${progress} %` : '';

      buttons.forEach(element => {
        element.innerHTML = element.dataset.disableWith + part;
      });

      fcn_purgeAllSchemas(
        response.data.next_offset,
        response.data.total,
        processed + response.data.processed
      );
    } else {
      buttons.forEach(element => {
        fcn_toggleInProgress(element, false);
      });

      _$$('tr').forEach(row => {
        row.classList.add('no-schema');
      });
    }
  })
  .catch(error => {
    console.log(error);
  });
}

// Listen to click on "Purge All" buttons
_$$('[data-action-purge-all-schemas]').forEach(element => {
  element.addEventListener('click', event => {
    event.preventDefault();
    fcn_purgeAllSchemas();
  });
});

// Listen to click on schema dialog buttons
_$$('[data-dialog-target="schema-dialog"]').forEach(element => {
  element.addEventListener('click', event => {
    const target = _$('[data-target="schema-content"]');
    target.value = event.currentTarget.closest('tr').querySelector('[data-schema-id]').textContent;
    target.scrollTop = 0;

    setTimeout(() => { target.scrollTop = 0; }, 10);
  });
});

/* =============================================================================
** EPUBS
** ========================================================================== */

/**
 * Deletes an ePUB by filename.
 *
 * @since 4.7
 * @param {String} name - Name of the ePUB.
 */

function fcn_delete_epub(name) {
  fcn_ajaxPost({
    'action': 'fictioneer_ajax_delete_epub',
    'name': name,
    'nonce': document.getElementById('fictioneer_admin_nonce').value
  })
  .then(response => {
    if (response.success) {
      document.querySelector(`[data-action="delete-epub"][data-name="${name}"]`).closest('tr').remove();
    } else {
      console.log(response);
    }
  })
  .catch(error => {
    console.log(error);
  });
}

// Listen for clicks in ePUB delete buttons
_$$('[data-action="delete-epub"]').forEach(element => {
  element.addEventListener(
    'click',
    (e) => {
      e.preventDefault();
      fcn_delete_epub(e.target.dataset.name)
    }
  );
});

// =============================================================================
// SEO METABOX
// =============================================================================

/**
 * Add attachment via WordPress media uploader.
 *
 * @since 4.0
 * @link https://codex.wordpress.org/Javascript_Reference/wp.media
 * @link https://stackoverflow.com/a/28549014/17140970
 *
 * @param {Event} event - The event.
 */

function fcn_ogMediaUpload(event) {
  event.preventDefault();
  var uploader = wp.media({
    multiple: false,
    library: {
      type: 'image'
    }
  })
  .open()
  .on('select', function() {
    const attachment = uploader.state().get('selection').first().toJSON();
    _$$$('fictioneer-seo-og-image').value = attachment.id;
    _$$$('fictioneer-seo-og-display').setAttribute('src', attachment.url);
    _$$$('fictioneer-button-seo-og-image-remove').classList.remove('hidden');
    _$('.og-source').classList.add('hidden');
  })
}

// Listen for click on media upload
if (button = _$$$('fictioneer-button-og-upload')) {
  button.addEventListener('click', fcn_ogMediaUpload);
}

/**
 * Update SEO metabox with character count of title
 *
 * @since 4.0
 */

function fcn_update_seo_title_chars() {
  const input = _$$$('fictioneer-seo-title');

  _$$$('fictioneer-seo-title-chars').innerHTML = `(${input.value.length}/70)`;
}

// Initial call and listen for keyup input
if (button = _$$$('fictioneer-seo-title')) {
  fcn_update_seo_title_chars();
  button.addEventListener('keyup', fcn_update_seo_title_chars);
}

/**
 * Remove OG image
 *
 * @since 4.0
 *
 * @param {Event} event - The event.
 */

function fcn_remove_seo_og_image(event) {
  event.preventDefault();

  const placeholder = _$$$('fictioneer-seo-og-display').dataset.placeholder;

  _$$$('fictioneer-seo-og-image').value = '';
  _$$$('fictioneer-seo-og-display').setAttribute('src', placeholder);
  _$$$('fictioneer-button-seo-og-image-remove').classList.add('hidden');
}

// Listen for click on button
if (button = _$$$('fictioneer-button-seo-og-image-remove')) {
  button.addEventListener('click', fcn_remove_seo_og_image);
}

// =============================================================================
// CONFIRM DIALOG
// =============================================================================

/**
 * Ask something via dialog and expect a certain answer.
 *
 * The element needs to have the data attributes 'data-dialog-message'
 * and 'data-dialog-confirm'.
 *
 * @since 5.0
 *
 * @param {Event} event - The event.
 */

function fcn_confirmIt(event) {
  // Get texts
  const message = event.currentTarget.dataset.dialogMessage,
        confirm = event.currentTarget.dataset.dialogConfirm;

  // Abort if...
  if (!message || !confirm) {
    return;
  }

  // Prompt
  const result = prompt(message);

  // Cancel
  if (!result) {
    event.preventDefault();
    return;
  }

  // Evaluate...
  if (result.toLowerCase() != confirm.toLowerCase()) {
    event.preventDefault();
  }
}

// Add event listener to each element with the 'data-confirm-dialog' attribute
_$$('[data-confirm-dialog]').forEach(element => {
  element.addEventListener(
    'click',
    (e) => {
      fcn_confirmIt(e);
    }
  );
});

// =============================================================================
// LOGOUT
// =============================================================================

// Admin bar logout link
_$('#wp-admin-bar-logout a')?.addEventListener('click', () => {
  localStorage.removeItem('fcnProfileAvatar');
  localStorage.removeItem('fcnUserData');
  localStorage.removeItem('fcnAuth');
  localStorage.removeItem('fcnBookshelfContent');
  localStorage.removeItem('fcnChapterBookmarks');
});

// =============================================================================
// EVENT DELEGATES
// =============================================================================

_$('.fictioneer-settings')?.addEventListener('click', event => {
  const clickTarget = event.target.closest('[data-click]'),
        clickAction = clickTarget?.dataset.click;

  if (!clickAction) {
    return;
  }

  switch (clickAction) {
    case 'purge-all-epubs':
    case 'purge-all-schemas':
    case 'purge-all-meta':
    case 'reset-post-relationship-registry':
      if (!confirm(clickTarget.dataset.prompt)) {
        event.preventDefault();
      }
      break;
  }
});

// =============================================================================
// DIALOGS
// =============================================================================

_$$('[data-dialog-target]').forEach(element => {
  element.addEventListener('click', event => {
    _$$$(event.currentTarget.dataset.dialogTarget)?.showModal();
  });
});

// Close regardless of required fields
_$$('button[formmethod="dialog"][value="cancel"]').forEach(element => {
  element.addEventListener('click', event => {
    event.preventDefault();
    event.currentTarget.closest('dialog').close();
  });
});

// Close dialog on click outside
_$$('dialog').forEach(element => {
  element.addEventListener('mousedown', event => {
    event.target.tagName.toLowerCase() === 'dialog' && event.target.close();
  });
});

// =============================================================================
// IMAGE UPLOAD META FIELD
// =============================================================================

/**
 * Add image attachment via WordPress media uploader.
 *
 * @since 5.7.4
 * @link https://codex.wordpress.org/Javascript_Reference/wp.media
 * @link https://stackoverflow.com/a/28549014/17140970
 *
 * @param {Event} event - The event.
 */

function fcn_mediaUpload(event) {
  // Stop trigger element behavior
  event.preventDefault();

  // Metabox
  const metabox = event.currentTarget.closest('[data-target="fcn-meta-field-image"]');

  // Open media modal
  var uploader = wp.media({
    multiple: false,
    library: {
      type: 'image'
    }
  })
  .open()
  .on('select', () => {
    const attachment = uploader.state().get('selection').first().toJSON();

    metabox.querySelector('[data-target="fcn-meta-field-image-id"]').value = attachment.id;
    metabox.querySelector('.fictioneer-meta-field__image-display').style.backgroundImage = `url("${attachment.url}")`;
    metabox.querySelector('.fictioneer-meta-field__image-upload').classList.add('hidden');
    metabox.querySelector('.fictioneer-meta-field__image-actions').classList.remove('hidden');
  });
}

// Listen for clicks on image upload and replace
_$$('.fictioneer-meta-field__image-upload, .fictioneer-meta-field__image-replace').forEach(button => {
  button.addEventListener('click', fcn_mediaUpload);
});

// Listen for clicks on image remove
_$$('.fictioneer-meta-field__image-remove').forEach(button => {
  button.addEventListener('click', event => {
    // Stop trigger element behavior
    event.preventDefault();

    // Metabox
    const metabox = event.currentTarget.closest('[data-target="fcn-meta-field-image"]');

    // Reset
    metabox.querySelector('[data-target="fcn-meta-field-image-id"]').value = '';
    metabox.querySelector('.fictioneer-meta-field__image-display').style.backgroundImage = '';
    metabox.querySelector('.fictioneer-meta-field__image-upload').classList.remove('hidden');
    metabox.querySelector('.fictioneer-meta-field__image-actions').classList.add('hidden');
  });
});

// =============================================================================
// TOKENS META FIELD
// =============================================================================

/**
 * Add or remove ID from token field
 *
 * @since 5.7.4
 *
 * @param {number} id - The ID of the token.
 * @param {HTMLElement} parent - The parent element.
 */

function fcn_tokensToggle(id, parent) {
  // Abort if...
  if (id < 1) {
    parent.querySelector('select').value = 0;
    return;
  }

  // Setup
  const tokenOptions = JSON.parse(parent.querySelector('[data-target="fcn-meta-field-tokens-options"]').value);
  const tokenTrack = parent.querySelector('[data-target="fcn-meta-field-tokens-track"]');
  const tokenInput = parent.querySelector('[data-target="fcn-meta-field-tokens-values"]');
  const tokenValues = fcn_splitList(tokenInput.value).filter(item => !isNaN(item)).map(item => Math.abs(parseInt(item)));
  const index = tokenValues.indexOf(id);

  // Add or remove
  if (index === -1) {
    tokenValues.push(id)
  } else {
    tokenValues.splice(index, 1);
  }

  // Update
  tokenInput.value = tokenValues.join(', ');
  tokenTrack.innerHTML = '';

  tokenValues.forEach(item => {
    const name = tokenOptions[item] ? tokenOptions[item] : item;

    tokenTrack.innerHTML += `<span class="fictioneer-meta-field__token" data-id="${item}"><span class="fictioneer-meta-field__token-name">${name}</span><button type="button" class="fictioneer-meta-field__token-button" data-id="${item}"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M12 13.06l3.712 3.713 1.061-1.06L13.061 12l3.712-3.712-1.06-1.06L12 10.938 8.288 7.227l-1.061 1.06L10.939 12l-3.712 3.712 1.06 1.061L12 13.061z"></path></svg></button></span>`;
  });

  // Reset
  parent.querySelector('select').value = 0;
}

// Listen for clicks on the token select
_$$('[data-target="fcn-meta-field-tokens-add"]').forEach(select => {
  select.addEventListener('change', event => {
    event.preventDefault();

    const id = parseInt(event.currentTarget.value);
    const parent = event.currentTarget.closest('[data-target="fcn-meta-field-tokens"]');

    fcn_tokensToggle(id, parent);
  });
});

// Listen for clicks on the token track
_$$('[data-target="fcn-meta-field-tokens-track"]').forEach(track => {
  track.addEventListener('click', event => {
    event.preventDefault();

    const token = event.target.closest('.fictioneer-meta-field__token');
    const field = event.target.closest('.fictioneer-meta-field');

    // Remove token
    if (token) {
      fcn_tokensToggle(
        parseInt(token.dataset.id),
        token.closest('[data-target="fcn-meta-field-tokens"]')
      );

      return;
    }

    // Open select options
    if (field) {
      field.querySelector('select').focus();
    }
  });
});

// =============================================================================
// ICONS META FIELD
// =============================================================================

function fcn_insertIconClass(button) {
  const parent = button.closest('.fictioneer-meta-field');

  if (!parent || !button) {
    return;
  }

  parent.querySelector('.fictioneer-meta-field__input').value = button.dataset.value;

  parent.querySelector('.fictioneer-meta-field__fa-icon')
    .setAttribute('class', `fictioneer-meta-field__fa-icon ${button.dataset.value}`);
}

// Listen for clicks on icons
_$$('.fictioneer-meta-field__icon-button[data-value]').forEach(button => {
  button.addEventListener('click', event => {
    event.preventDefault();
    fcn_insertIconClass(event.currentTarget);
  });
});

// Listen for clicks on current icon
_$$('.fictioneer-meta-field__fa-icon').forEach(field => {
  field.addEventListener('click', event => {
    const icons = event.currentTarget.closest('.fictioneer-meta-field').querySelector('.fictioneer-meta-field__button-grid');

    icons.classList.toggle('hidden', !icons.classList.contains('hidden'));
  });
});
