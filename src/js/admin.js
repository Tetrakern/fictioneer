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
