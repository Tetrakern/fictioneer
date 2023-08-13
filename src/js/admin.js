/* =============================================================================
** SCHEMAS
** ========================================================================== */

/**
 * Purges a schema from a post.
 *
 * @description Takes the ID of a post to delete the schema via AJAX.
 * Removes the schema row if successful.
 *
 * @since 4.7
 * @param {Number} id - ID of the post.
 */

function fcn_purgeSchema(id) {
  jQuery.ajax({
    url: fictioneer_ajax.ajax_url,
    type: 'post',
    data: {
      action: 'fictioneer_ajax_purge_schema',
      nonce: document.getElementById('fictioneer_admin_nonce').value,
      id: id
    },
    dataType: 'json',
    success: function(response) {
      if (response.success) {
        const tr = _$$$(`schema-${id}`);
        tr.querySelector('.text-blob').innerHTML = tr.querySelector('summary').innerHTML;
        tr.querySelector('.delete').remove();
        tr.querySelector('.no-schema-note').classList.remove('hidden');
      }
    }
  });
}

// Listen for clicks on schema purge buttons
_$$('.button-purge-schema').forEach(element => {
  element.addEventListener(
    'click',
    (e) => {
      fcn_purgeSchema(e.currentTarget.dataset.id)
    }
  );
});

/* =============================================================================
** EPUBS
** ========================================================================== */

/**
 * Deletes an ePUB by filename.
 *
 * @description Takes the ID of the row in the table and filename of an ePUB to
 * delete the ePUB via AJAX. Removes the ePUB row if successful.
 *
 * @since 4.7
 * @param {String} filename - Filename of the ePUB to delete.
 * @param {Number} rowId - The row ID in the table view.
 */

function fcn_delete_epub(filename, rowId) {
  jQuery.ajax({
    url: fictioneer_ajax.ajax_url,
    type: 'post',
    data: {
      action: 'fictioneer_ajax_delete_epub',
      nonce: document.getElementById('fictioneer_admin_nonce').value,
      name: filename
    },
    dataType: 'json',
    success: function(response) {
      if (response.success) {
        document.getElementById(`epub-id-${rowId}`).remove();
      }
		}
  });
}

// Listen for clicks in ePUB delete buttons
_$$('.button-delete-epub').forEach(element => {
  element.addEventListener(
    'click',
    (e) => {
      fcn_delete_epub(
        e.target.dataset.filename,
        e.target.dataset.id
      )
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

  if (input.value != '{{title}} – {{site}}') {
    _$$$('fictioneer-seo-title-chars').innerHTML = `(${input.value.length}/70)`;
  } else {
    _$$$('fictioneer-seo-title-chars').innerHTML = '';
  }
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
  if (!message || !confirm) return;

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

// Add event listener to each element with the 'confirm-dialog' class
_$$('.confirm-dialog').forEach(element => {
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
  localStorage.removeItem('fcnStoryFollows')
  localStorage.removeItem('fcnStoryReminders');
  localStorage.removeItem('fcnCheckmarks');
  localStorage.removeItem('fcnFingerprint');
  localStorage.removeItem('fcnLoginState');
  localStorage.removeItem('fcnNonce');
  localStorage.removeItem('fcnBookshelfContent');
  localStorage.removeItem('fcnChapterBookmarks');
});

// =============================================================================
// EVENT DELEGATES
// =============================================================================

_$('.fictioneer-settings')?.addEventListener('click', event => {
  const clickTarget = event.target.closest('[data-click]'),
        clickAction = clickTarget?.dataset.click;

  if (!clickAction) return;

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
// SIDEBAR LAYOUT
// =============================================================================

_$('.fictioneer-settings__subnav')?.addEventListener('click', event => {
  const clickTarget = event.target.closest('[data-sidebar-click]');

  if (!clickTarget) {
    return;
  }

  const navTarget = _$(`[data-sidebar-target="${clickTarget.dataset.sidebarClick}"]`);

  if (!navTarget) {
    return;
  }

  _$$('[data-sidebar-target]').forEach(element => {
    element.classList.add('hidden');
  });

  _$$('[data-sidebar-click]').forEach(element => {
    element.classList.remove('active');
  });

  navTarget.classList.remove('hidden');
  clickTarget.classList.add('active');
});
