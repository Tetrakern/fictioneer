// =============================================================================
// GET POSITION OF ELEMENT RELATIVE TO WINDOW
// =============================================================================

/**
 * @typedef {Object} TopLeftPosition
 * @property {number} left - The X Coordinate.
 * @property {number} top - The Y Coordinate.
 * @inner
 */

/**
 * Returns the top-left position of an element relative to the window.
 *
 * @since 4.7
 * @param {HTMLElement} element - Element to get the position for.
 * @return {TopLeftPosition} Top-left position of the element.
 */

function fcn_getWindowPosition(element) {
  let rect = element.getBoundingClientRect();

  return {
    top: rect.top + window.scrollY,
    left: rect.left + window.scrollX
  };
}

// =============================================================================
// POPUP MENU
// =============================================================================

/**
 * The last opened popup menu.
 *
 * @type {HTMLElement}
 */

var fcn_lastPopupMenu;

/**
 * Toggles a popup menu.
 *
 * @description Gets the targeted menu by ID and checks whether it is currently
 * open or not. If closed, the menu is made visible and immediately relocated to the
 * closest parent with ".fictioneer-settings" class to escape overflow clippings,
 * the position set absolute to the new direct parent to remain in place. If open,
 * this whole process is reversed to restore the original conditions.
 *
 * @since 4.7
 * @param {Number} id - ID of the menu element
 */

function fcn_togglePopupMenu(id) {
  let menu = _$$$(`popup-${id}`);

  if (menu.classList.contains('hidden')) {
    // Is closed...
    menu.classList.remove('hidden');

    if (fcn_lastPopupMenu) {
      // Close other open menu
      fcn_togglePopupMenu(fcn_lastPopupMenu.dataset.id);
    }

    let dest = menu.closest('.fictioneer-settings'),
        pos_menu = fcn_getWindowPosition(menu),
        pos_dest = fcn_getWindowPosition(dest),
        offset = window.innerWidth < 600 ? 0 : pos_dest.top; // Adminbar

    // Position based on new parent and window offset
    menu.style.top = `${pos_menu.top - offset}px`;
    menu.style.left = `${pos_menu.left - pos_dest.left}px`;
    menu.style.transform = 'translate(-100%, -50%)';

    // Move to parent and remember
    dest.append(menu);
    fcn_lastPopupMenu = menu;
  } else {
    // Is open...
    menu.classList.add('hidden');

    let dest = _$$$(`popup-container-${id}`);

    // Reverse positioning
    menu.style.top = '50%';
    menu.style.left = '0';
    menu.style.transform = '';

    // Move back to origin and unset reference
    dest.append(menu);
    fcn_lastPopupMenu = null;
  }
}

/**
 * Clicking on any element with the .popup-menu-toggle class
 * toggles the associated popup menu via the data-id attribute.
 */

_$$('.popup-menu-toggle').forEach(element => {
  element.addEventListener(
    'click',
    (e) => {
      fcn_togglePopupMenu(e.currentTarget.dataset.id);
      e.stopPropagation(); // Prevent close click from firing
    }
  );
});

/**
 * Clicking anywhere except the menu toggle will close the last opened
 * popup menu, including the popup menu itself. This does not matter
 * since a click into the menu will also bubble up to the menu action,
 * dismissing the menu as expected.
 */

_$('body').addEventListener(
  'click',
  () => {
    if (fcn_lastPopupMenu) {
      fcn_togglePopupMenu(fcn_lastPopupMenu.dataset.id);
    }
  }
);

/**
 * Resizing the window would cause an open popup menu to become misplaced,
 * so better close it and reset the location.
 */

window.addEventListener(
  'resize',
  () => {
    if (fcn_lastPopupMenu) {
      fcn_togglePopupMenu(fcn_lastPopupMenu.dataset.id);
    }
  }
);

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
        let tr = _$$$(`schema-${id}`);
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
// INPUT VALIDATIONS
// =============================================================================

/**
 * Adds .invalid class to an :invalid input.
 *
 * @description If the element is :invalid by HTML5 validation, the .invalid class
 * is added. Otherwise, the class is removed.
 *
 * @since 4.7
 * @param {HTMLElement} element - The element to check for validity.
 */

function fcn_checkInputValidity(element) {
  element.classList.toggle(
    'invalid',
    (element.hasAttribute('required') && element.value == '') ||
    !element.checkValidity()
  );
}

// Listen for blur on email inputs
_$$('.fictioneer-ui input[type=email]').forEach(element => {
  element.addEventListener(
    'blur',
    () => {
      fcn_checkInputValidity(element)
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
    multiple: false
  })
  .open()
  .on('select', function() {
    let attachment = uploader.state().get('selection').first().toJSON();
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
  let input = _$$$('fictioneer-seo-title');

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

  let placeholder = _$$$('fictioneer-seo-og-display').dataset.placeholder;
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
  let message = event.currentTarget.dataset.dialogMessage,
      confirm = event.currentTarget.dataset.dialogConfirm;

  // Abort if...
  if (!message || !confirm) return;

  // Prompt
  let result = prompt(message);

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
  localStorage.removeItem('fcnLoginState');
  localStorage.removeItem('fcnNonce');
  localStorage.removeItem('fcnFingerprint');
  localStorage.removeItem('fcnBookshelfContent');
  localStorage.removeItem('fcnChapterBookmarks');
});

console.log('foo');
