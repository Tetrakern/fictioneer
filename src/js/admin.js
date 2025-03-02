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
  FcnUtils.aPost({
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
      FcnUtils.toggleInProgress(element, true);
    });
  }

  // --- Request ---------------------------------------------------------------

  FcnUtils.aPost({
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
        FcnUtils.toggleInProgress(element, false);
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
_$$('[data-fcn-dialog-target="schema-dialog"]').forEach(element => {
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
 * @since 4.7.0
 * @param {String} name - Name of the ePUB.
 */

function fcn_delete_epub(name) {
  FcnUtils.aPost({
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
 * @since 4.0.0
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
 * @since 4.0.0
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
 * @since 4.0.0
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
 * @since 5.0.0
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
// USER
// =============================================================================

/**
 * AJAX: Refresh local user data.
 *
 * @since 5.27.0
 */

function fcn_fetchUserData() {
  FcnUtils.aGet({
    'action': 'fictioneer_ajax_get_user_data',
    'fcn_fast_ajax': 1
  })
  .then(response => {
    if (response.success) {
      const userData = response.data;
      userData['lastLoaded'] = Date.now();
      FcnUtils.setUserData(userData);
    }
  })
  .catch(error => {
    localStorage.removeItem('fcnUserData');
    console.error(error);
  });
}

// Fetch new user data after purge
(() => {
  const currentUserData = FcnUtils.parseJSON(localStorage.getItem('fcnUserData'));

  if (!currentUserData) {
    fcn_fetchUserData();
  }
})();

// Admin bar logout link
_$('#wp-admin-bar-logout a')?.addEventListener('click', () => {
  localStorage.removeItem('fcnUserData');
  localStorage.removeItem('fcnBookshelfContent');
});

// Data node purge
_$$('[data-action="click->fictioneer-admin-profile#purgeLocalUserData"]').forEach(link => {
  link.addEventListener('click', () => {
    localStorage.removeItem('fcnUserData');
    localStorage.removeItem('fcnBookshelfContent');
  });
});

// =============================================================================
// EVENT DELEGATES
// =============================================================================

_$('.fictioneer-settings')?.addEventListener('click', event => {
  const clickTarget = event.target.closest('[data-click]');
  const clickAction = clickTarget?.dataset.click;

  if (!clickAction) {
    return;
  }

  // This was more once lol
  switch (clickAction) {
    case 'warning-dialog':
      if (!confirm(clickTarget.dataset.dialog)) {
        event.preventDefault();
      }
      break;
  }
});

// =============================================================================
// DIALOGS
// =============================================================================

_$$('[data-fcn-dialog-target]').forEach(element => {
  element.addEventListener('click', event => {
    _$$$(event.currentTarget.dataset.fcnDialogTarget)?.showModal();
  });
});

// Close regardless of required fields
_$$('.fictioneer-dialog button[formmethod="dialog"][value="cancel"]').forEach(element => {
  element.addEventListener('click', event => {
    event.preventDefault();
    event.currentTarget.closest('dialog').close();
  });
});

// Close dialog on click outside
_$$('.fictioneer-dialog').forEach(element => {
  element.addEventListener('mousedown', event => {
    if (event.target === event.currentTarget) {
      const rect = element.getBoundingClientRect();
      const outside = event.clientX < rect.left || event.clientX > rect.right ||
        event.clientY < rect.top || event.clientY > rect.bottom;

      if (outside) {
        event.preventDefault();
        event.target.close();
      }
    }
  });
});

// =============================================================================
// IMAGE UPLOAD META FIELD
// =============================================================================

/**
 * Add image attachment via WordPress media uploader to meta field.
 *
 * @since 5.7.4
 * @link https://codex.wordpress.org/Javascript_Reference/wp.media
 * @link https://stackoverflow.com/a/28549014/17140970
 *
 * @param {Event} event - The event.
 */

function fcn_imageMediaUpload(event) {
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
  button.addEventListener('click', fcn_imageMediaUpload);
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
// FILE UPLOAD META FIELD
// =============================================================================

/**
 * Add text/ebook attachment via WordPress media uploader to meta field.
 *
 * @since 5.8.0
 * @link https://codex.wordpress.org/Javascript_Reference/wp.media
 * @link https://stackoverflow.com/a/28549014/17140970
 *
 * @param {Event} event - The event.
 */

function fcn_ebookMediaUpload(event) {
  // Stop trigger element behavior
  event.preventDefault();

  // Meta field
  const metabox = event.currentTarget.closest('[data-target="fcn-meta-field-ebook"]');

  // Open media modal
  var uploader = wp.media({
    multiple: false,
    library: {
      type: ['application/pdf', 'text/plain', 'application/rtf', 'application/x-mobipocket-ebook', 'application/epub+zip']
    }
  })
  .open()
  .on('select', () => {
    const attachment = uploader.state().get('selection').first().toJSON();

    metabox.querySelector('[data-target*="id"]').value = attachment.id;
    metabox.querySelector('[data-target*="name"]').textContent = attachment.title;
    metabox.querySelector('[data-target*="size"]').textContent = attachment.filesizeHumanReadable;
    metabox.querySelector('[data-target*="filename"]').textContent = attachment.filename;
    metabox.querySelector('[data-target*="filename"]').href = attachment.url;

    metabox.querySelector('[data-target*="upload"]').classList.add('hidden');

    metabox.querySelectorAll('[data-target*="replace"], [data-target*="remove"], [data-target*="display"]')
      .forEach(el => el.classList.remove('hidden'));
  });
}

/**
 * Remove text/ebook attachment from meta field.
 *
 * @since 5.8.0
 * @link https://codex.wordpress.org/Javascript_Reference/wp.media
 * @link https://stackoverflow.com/a/28549014/17140970
 *
 * @param {Event} event - The event.
 */

function fcn_ebookMediaRemove(event) {
  // Stop trigger element behavior
  event.preventDefault();

  // Meta field
  const metabox = event.currentTarget.closest('[data-target="fcn-meta-field-ebook"]');

  // Reset
  metabox.querySelector('[data-target*="id"]').value = 0;
  metabox.querySelector('[data-target*="name"]').textContent = '';
  metabox.querySelector('[data-target*="size"]').textContent = '';
  metabox.querySelector('[data-target*="filename"]').textContent = '';
  metabox.querySelector('[data-target*="filename"]').href = '';

  metabox.querySelector('[data-target*="upload"]').classList.remove('hidden');

  metabox.querySelectorAll('[data-target*="replace"], [data-target*="remove"], [data-target*="display"]')
    .forEach(el => el.classList.add('hidden'));
}

// Listen for clicks on upload and replace
_$$('[data-target="fcn-meta-field-ebook-upload"], [data-target="fcn-meta-field-ebook-replace"]').forEach(button => {
  button.addEventListener('click', fcn_ebookMediaUpload);
});

// Listen for clicks on remove
_$$('[data-target="fcn-meta-field-ebook-remove"]').forEach(button => {
  button.addEventListener('click', fcn_ebookMediaRemove);
});

// =============================================================================
// TOKENS META FIELD
// =============================================================================

/**
 * Split string into an array
 *
 * @since 5.7.4
 *
 * @param {string} list - The input string to split.
 * @param {string} [separator=','] - The substring to use for splitting.
 *
 * @return {Array<string>} - An array of items.
 */

function fcn_splitList(list, separator = ',') {
  if (!list || list.trim() === '') {
    return [];
  }

  let array = list.replace(/\r?\n|\r/g, '').split(separator);
  array = array.map(item => item.trim()).filter(item => item.length > 0);

  return array;
}

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
  const tokenOptions = FcnUtils.parseJSON(parent.querySelector('[data-target="fcn-meta-field-tokens-options"]').value);
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
    const name = FcnUtils.sanitizeHTML(tokenOptions[item] ? tokenOptions[item] : item);

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
    const remove = event.target.closest('.fictioneer-meta-field__token-button');
    const field = event.target.closest('.fictioneer-meta-field');

    // Remove token
    if (token && remove) {
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

/**
 * Change icon class on icon field
 *
 * @since 5.7.4
 *
 * @param {HTMLElement} button - The button clicked.
 */

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

// =============================================================================
// DISABLE EDITOR FOR REQUIRED FIELDS
// =============================================================================

/**
 * Lock or unlock post saving
 *
 * @since 5.7.4
 */

function fcn_checkRequiredFields() {
  let requiredMissing = null;

  _$$('.fictioneer-meta-field [required]').forEach(element => {
    if (requiredMissing === true) {
      return;
    }

    requiredMissing = element?.value.trim() === '';
  });

  if (requiredMissing) {
    wp.data?.dispatch('core/editor').lockPostSaving('requiredValueLock');
  } else {
    wp.data?.dispatch('core/editor').unlockPostSaving('requiredValueLock');
  }
}

document.addEventListener('DOMContentLoaded', () => {
  wp.data?.dispatch('core/editor').lockPostSaving('requiredValueLock');
  fcn_checkRequiredFields();

  _$$('.fictioneer-meta-field [required]').forEach(element => {
    const wrapper = element.closest('.fictioneer-meta-field');

    wrapper.classList.add('pending-validation');

    element.addEventListener('input', () => {
      fcn_checkRequiredFields();
    });

    element.addEventListener('input', () => {
      wrapper.classList.remove('pending-validation');
    }, { once: true });
  });

  wp.data?.subscribe(() => {
    _$$('.fictioneer-meta-field [required]').forEach(element => {
      const wrapper = element.closest('.fictioneer-meta-field');

      wrapper.classList.toggle('fictioneer-meta-field--invalid', element?.value.trim() === '');
    });
  });
});

// =============================================================================
// SHOW STORY GROUPS
// =============================================================================

/**
 * Get options for story chapter groups.
 *
 * @since 5.7.4
 * @since 5.27.4 - Switch to select options instead of datalist.
 * @param {HTMLElement} source - Element that triggered the request.
 */

function fcn_setGroupSelectOptions(source) {
  // Setup
  const storyId = source.value;

  if (storyId == 0) {
    return;
  }

  // Request group options (if any)
  FcnUtils.aGet({
    'action': 'fictioneer_ajax_get_chapter_group_options',
    'story_id': storyId,
    'nonce': fictioneer_ajax.fictioneer_nonce
  })
  .then(response => {
    if (response.success) {
      _$$('[name="combo-select-fictioneer_chapter_group"]').forEach(select => {
        select.innerHTML = response.data.html;

        const input = select.closest('div').querySelector('input');

        select.value = input.value;

        if (!select.value) {
          select.value = 0;
        }
      });
    } else if (response.data.error) {
      console.error('Error:', response.data.error);
    }
  }).catch(error => {
    console.error(error);
  });
}

document.addEventListener('DOMContentLoaded', () => {
  _$$('[data-action="select-story"]').forEach(element => {
    // Initialize
    fcn_setGroupSelectOptions(element);

    // Listen for changes
    element.addEventListener('change', event => {
      fcn_setGroupSelectOptions(event.currentTarget);
    });
  });
});

// =============================================================================
// COMBO SELECT
// =============================================================================

document.addEventListener('DOMContentLoaded', () => {
  _$$('[data-meta-field-controller-target="combo"]').forEach(select => {
    select.addEventListener('change', ({ currentTarget }) => {
      const newValue = currentTarget.value;
      const input = currentTarget.closest('div').querySelector('input');

      if (input) {
        if (newValue == '0' || newValue == '-1') {
          input.value = newValue == '-1' ? '' : input.value;
          input.focus();
        } else {
          input.value = newValue;
        }
      }
    });

    select.closest('div').querySelector('input')?.addEventListener('blur', ({ currentTarget }) => {
      const select = currentTarget.closest('div').querySelector('select');

      select.value = currentTarget.value;

      if (!select.value) {
        select.value = currentTarget.value != '' ? -1 : 0;
      }
    });
  });
});

// =============================================================================
// STORY SELECT
// =============================================================================

document.addEventListener('DOMContentLoaded', () => {
  _$('select[id="fictioneer_chapter_story"]')?.addEventListener('change', event => {
    const addChapterLink = _$('#wp-admin-bar-fictioneer-add-chapter a');

    if (addChapterLink) {
      const url = new URL(addChapterLink.href);
      url.searchParams.set('story_id', event.currentTarget.value);
      addChapterLink.href = url.toString();
      addChapterLink.closest('li').classList.toggle('hidden', event.currentTarget.value < 1);
    }
  });
});

// =============================================================================
// RELATIONSHIP FIELDS
// =============================================================================

// Initialize relationship field functionality
_$$('.fictioneer-meta-field--relationships').forEach(element => {
  const infoBox = element.querySelector('[data-target="fcn-relationships-info"]');

  fcn_observeRelationshipSource(element);

  element.addEventListener('click', event => {
    // Remove
    if (event.target.closest('[data-action="remove"]')) {
      fcn_removeRelationship(event.target.closest('[data-id]'));
    }

    // Add
    if (event.target.closest('[data-action="add"]')) {
      fcn_addRelationship(
        event.target.closest('[data-id]'),
        element.querySelector('[data-target="fcn-relationships-values"]')
      );
    }
  });

  element.addEventListener('mouseover', event => {
    // Info
    if (infoBox && (info = event.target.closest('[data-info]'))) {
      infoBox.innerHTML = info.dataset.info;
    }
  });
});

// Initialize relationship drag-and-drop sorting
_$$('[data-target="fcn-relationships-values"]').forEach(element => {
  // Sorting
  Sortable.create(element, {
    selectedClass: 'is-dragged',
    ghostClass: 'is-dragged-ghost',
    fallbackTolerance: 3,
    animation: 150,
    onStart: event => {
      // Hover position wrong due to shifting elements
      event.target.closest('ol, ul').classList.add('is-sorting');
    },
    onEnd: event => {
      // Prevent jumping of hover element
      setTimeout(() => { event.target.closest('ol, ul').classList.remove('is-sorting'); }, 5);
    }
  });
});

// Relationship search input
var relationshipSearchTimer;

_$$('[data-target="fcn-relationships-search"]').forEach(search => {
  search.addEventListener('input', () => {
    // Clear previous timer (if any)
    clearTimeout(relationshipSearchTimer);

    // Trigger search after delay
    relationshipSearchTimer = setTimeout(() => {
      // Get elements and values
      const field = search.closest('.fictioneer-meta-field');
      const sourceList = field.querySelector('[data-target="fcn-relationships-source"]');

      // Clear source list and add spinner
      sourceList.innerHTML = '';
      sourceList.appendChild(_$('[data-target="spinner-template"]').content.cloneNode(true));

      // Crop search input
      if (search.value > 200) {
        search.value = search.value.slice(0, 200);
      }

      // Request
      fcn_queryRelationshipPosts(
        {
          'action': field.dataset.ajaxAction,
          'nonce': field.dataset.nonce,
          'key': field.dataset.key,
          'post_id': field.dataset.postId,
          'search': search.value,
          'post_type': field.querySelector('[data-target="fcn-relationships-post-type"]')?.value ?? '',
          'fcn_fast_ajax': 1
        },
        sourceList
      );

      // Reset info (if any)
      if (infoBox = field.querySelector('[data-target="fcn-relationships-info"]')) {
        infoBox.innerHTML = infoBox.dataset.default;
      }
    }, 800);
  });
});

// Relationship post type select
_$$('[data-target="fcn-relationships-post-type"]').forEach(select => {
  select.addEventListener('change', (event) => {
    // Get elements and values
    const field = event.currentTarget.closest('.fictioneer-meta-field--relationships');
    const sourceList = field.querySelector('[data-target="fcn-relationships-source"]');
    let search = field.querySelector('[data-target="fcn-relationships-search"]')?.value ?? '';

    // Clear source list and add spinner
    sourceList.innerHTML = '';
    sourceList.appendChild(_$('[data-target="spinner-template"]').content.cloneNode(true));

    // Crop search input
    if (search > 200) {
      search = search.slice(0, 200);
    }

    // Request
    fcn_queryRelationshipPosts(
      {
        'action': field.dataset.ajaxAction,
        'nonce': field.dataset.nonce,
        'key': field.dataset.key,
        'post_id': field.dataset.postId,
        'search': search,
        'post_type': event.currentTarget.value,
        'fcn_fast_ajax': 1
      },
      sourceList
    );

    // Reset info (if any)
    if (infoBox = field.querySelector('[data-target="fcn-relationships-info"]')) {
      infoBox.innerHTML = infoBox.dataset.default;
    }
  });
});

// Scroll buttons for mobile
_$$('[data-action="fcn-relationships-scroll"]').forEach(button => {
  const field = button.closest('.fictioneer-meta-field');
  const target = field.querySelector('[data-target="fcn-relationships-values"');

  button.addEventListener('click', event => {
    const direction = parseInt(event.currentTarget.dataset.direction);

    target.scrollBy({
      top: (direction * target.offsetHeight) * 0.75,
      behavior: 'smooth'
    });
  });
});

/**
 * Observe relationship source container for successive loading.
 *
 * @since 5.8.0
 * @param {HTMLElement} container - Field container element.
 */

function fcn_observeRelationshipSource(container) {
  const field = container.closest('.fictioneer-meta-field');

  const observer = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        let search = field.querySelector('[data-target="fcn-relationships-search"]')?.value ?? '';

        if (search.length > 200) {
          search = search.slice(0, 200);
        }

        fcn_queryRelationshipPosts(
          {
            'action': entry.target.dataset.ajaxAction,
            'page': entry.target.dataset.page,
            'nonce': entry.target.dataset.nonce,
            'key': entry.target.dataset.key,
            'post_id': entry.target.dataset.postId,
            'search': search,
            'post_type': field.querySelector('[data-target="fcn-relationships-post-type"]')?.value ?? '',
            'fcn_fast_ajax': 1
          },
          entry.target.closest('[data-target="fcn-relationships-source"')
        );

        observer.unobserve(entry.target);
      }
    });
  }, { root: container, rootMargin: '0px', threshold: 0.5 });

  container.querySelectorAll('[data-target="fcn-relationships-observer"]').forEach(target => {
    observer.observe(target);
  });
}

/**
 * Query more relationship posts.
 *
 * @since 5.8.0
 * @param {JSON} payload - GET request payload.
 * @param {HTMLElement} container - Field container element.
 * @param {boolean} [append=true] - Whether to append the response or replace the previous content.
 */

function fcn_queryRelationshipPosts(payload, container, append = true) {
  const field = container.closest('.fictioneer-meta-field');
  const sourceList = field.querySelector('[data-target="fcn-relationships-source"]');
  const selectedList = field.querySelector('[data-target="fcn-relationships-values"]');
  let errorMessage = null;

  FcnUtils.aPost(payload)
  .then(response => {
    // Remove observer (if any)
    container.querySelector('[data-target="fcn-relationships-observer"]')?.remove();

    // Pass on response
    return response;
  })
  .then(response => {
    // Evaluate response...
    if (response.success) {
      if (append) {
        container.innerHTML += response.data.html;
      } else {
        container.innerHTML = response.data.html;
      }

      container.querySelectorAll('li').forEach(item => {
        if (selectedList.querySelector(`[data-id="${item.dataset.id}"]`)) {
          item.classList.add('disabled');
        }
      });

      fcn_observeRelationshipSource(container);
    } else {
      errorMessage = `Error: ${response.data.error}`;
      console.error('Error:', response.data.error);
    }
  })
  .catch(error => {
    errorMessage = error;
    console.error(error);
  })
  .then(() => {
    if (errorMessage) {
      const clone = field.querySelector('[data-target="loading-error-template"]').content.cloneNode(true);

      clone.querySelector('.error-message').textContent = errorMessage;
      sourceList.appendChild(clone);
    }
  });
}

/**
 * Remove relationship item.
 *
 * @since 5.8.0
 * @param {HTMLElement} target - The element of the item to remove.
 */

function fcn_removeRelationship(target) {
  // Get field container
  const field = target.closest('.fictioneer-meta-field');

  // ... abort if not found
  if (!field) {
    return;
  }

  // Get source list
  const sourceList = field.querySelector('[data-target="fcn-relationships-source"]');

  // Enable removed item in source list
  const item = sourceList.querySelector(`[data-id="${target.dataset.id}"]`);

  if (item) {
    item.classList.remove('disabled');
  }

  // Remove from selection
  target.remove();
}

/**
 * Add relationship item.
 *
 * @since 5.8.0
 * @param {HTMLElement} source - The element of the source item.
 * @param {HTMLElement} destination - The container to append the item to.
 */

function fcn_addRelationship(source, destination) {
  // Check
  if (!source || !destination || source.classList.contains('disabled')) {
    return;
  }

  // Get field container
  const field = source.closest('.fictioneer-meta-field');

  // Clone template
  const clone = field.querySelector('[data-target="selected-item-template"]').content.cloneNode(true);

  // Fill data
  clone.querySelector('li').setAttribute('data-id', source.dataset.id);
  clone.querySelector('input[type="hidden"]').value = source.dataset.id;
  clone.querySelector('span').innerHTML = source.querySelector('span').innerHTML;

  if (source.dataset.info) {
    clone.querySelector('li').setAttribute('data-info', source.dataset.info);
  }

  // Append
  destination.appendChild(clone);

  // Disable
  source.classList.add('disabled');
}

// =============================================================================
// UNLOCK POSTS
// =============================================================================

var /** @type {timeoutID} */ fcn_unlockPostsTimer;

_$('[data-controller="fcn-unlock-posts"]')?.addEventListener('click', event => {
  // Evaluate clicks on the interface
  fcn_unlockPostsHandleClicks(event.target.closest('[data-target]'));
});

_$('[data-target="fcn-unlock-posts-search"]')?.addEventListener('input', () => {
  // Clear previous timer (if any)
  clearTimeout(fcn_unlockPostsTimer);

  // Trigger search after delay
  fcn_unlockPostsTimer = setTimeout(() => {
    fcn_unlockPostsSearch();
  }, 800);
});

_$('[data-target="fcn-unlock-posts-select"]')?.addEventListener('input', () => {
  // Clear previous timer (if any)
  clearTimeout(fcn_unlockPostsTimer);

  // Start search
  fcn_unlockPostsSearch();
});

/**
 * Handle clicks on the Unlock Posts interface.
 *
 * @since 5.16.0
 * @param {HTMLElement} target - The element that was clicked.
 */

function fcn_unlockPostsHandleClicks(target) {
  if (!target) {
    return;
  }

  // Delegate or perform action...
  switch (target.dataset.target) {
    case 'fcn-unlock-posts-delete':
      target.closest('[data-target="fcn-unlock-posts-item"').remove();
      break;
    case 'fcn-unlock-posts-search-item':
      fcn_unlockPostsAdd(target);
      break;
  }
}

/**
 * Search posts to unlock.
 *
 * @since 5.16.0
 */

function fcn_unlockPostsSearch() {
  // Setup
  const container = _$('.unlock-posts');
  const search = _$('[data-target="fcn-unlock-posts-search"]');
  const results = _$('[data-target="fcn-unlock-posts-search-results"]');
  const selected = _$('[data-target="fcn-unlock-posts-selected"]');

  // Abort if...
  if (!container || !search || (!search.value && !_$('[data-target="fcn-unlock-posts-search-item"]'))) {
    return;
  }

  // Payload
  const payload = {
    'action': 'fictioneer_ajax_search_posts_to_unlock',
    'search': search.value,
    'type': _$('[data-target="fcn-unlock-posts-select"]')?.value || 'any',
    'nonce': container.querySelector('[name="unlock_posts_nonce"]').value
  };

  // Indicate process
  container.classList.add('ajax-in-progress');

  // Request
  FcnUtils.aPost(payload)
  .then(response => {
    if (response.success) {
      results.innerHTML = response.data.html;
    } else {
      results.innerHTML = response.data.error;
      console.error('Error:', response.data.error);
    }
  })
  .catch(error => {
    results.innerHTML = error;
    console.error('Error:', error);
  })
  .then(() => {
    container.classList.remove('ajax-in-progress');

    results.querySelectorAll('[data-target="fcn-unlock-posts-search-item"]').forEach(item => {
      if (selected.querySelector(`[data-post-id="${item.dataset.postId}"]`)) {
        item.remove();
      }
    });
  });
}

/**
 * Add post items to the Unlock Posts selection.
 *
 * @since 5.16.0
 * @param {HTMLElement} target - The element that was clicked.
 */

function fcn_unlockPostsAdd(target) {
  // Setup
  const template = _$('[data-target="fcn-unlock-posts-item-template"]').content.cloneNode(true);
  const item = template.querySelector('.unlock-posts__item');

  // Build node
  item.dataset.postId = target.dataset.postId;
  item.title = target.title;
  item.querySelector('input[type="hidden"]').value = target.dataset.postId;
  item.querySelector('.unlock-posts__item-title').innerText = target.querySelector('.unlock-posts__item-title').innerText;
  item.querySelector('.unlock-posts__item-meta').innerText = target.querySelector('.unlock-posts__item-meta').innerText;

  // Append
  _$('[data-target="fcn-unlock-posts-selected"]').appendChild(template);

  // Remove search node
  target.remove();
}

// =============================================================================
// SHOW HELP MODAL
// =============================================================================

_$('.fictioneer-settings')?.addEventListener('click', event => {
  const target = event.target.closest('.fcn-help');

  // Help icon clicked?
  if (!target) {
    return;
  }

  // Stop event from bubbling up
  event.preventDefault();

  // Fill modal
  const modal = _$$$(target.dataset.fcnDialogTarget);

  if (modal) {
    modal.querySelector('[data-target="fcn-help-modal-header"]').textContent = target.dataset.label;
    modal.querySelector('[data-target="fcn-help-modal-content"]').innerHTML = target.dataset.help;
  }
});

// =============================================================================
// INTERVAL ACTION
// =============================================================================

/**
 * Repeats AJAX action until a specific goal is reached
 *
 * @since 5.25.0
 * @param {HTMLElement} trigger - The element that triggered the action.
 * @param {String} action - The action to perform
 * @param {JSON} payload - Data for the action.
 */

function fcn_intervalAction(trigger, action, payload = {}) {
  // Current index?
  const index = parseInt(payload['index'] ?? 0);
  const goal = parseInt(payload['goal'] ?? 10);
  const progress = (index / goal * 100).toFixed(0);

  // Payload
  payload = {
    ...{
      'action': action,
      'nonce': document.getElementById('fictioneer_admin_nonce').value
    },
    ...payload
  };

  // Indicate process
  if (index < 1 || index >= goal) {
    FcnUtils.toggleInProgress(trigger, index < 1);
  }

  if (index < goal) {
    trigger.innerHTML = `${trigger.dataset.disableWith} ${progress} %`;
  }

  // Stop if done
  if (index > goal) {
    return;
  }

  // Request
  FcnUtils.aPost(payload)
  .then(response => {
    if (!response?.data?.done) {
      fcn_intervalAction(
        trigger,
        action,
        {...payload, ...{ 'index': response.data.index + 1, 'goal': response.data.goal }}
      );
    }
  })
  .catch(error => {
    console.error(error);
  });
}

_$$('[data-click-action="fictioneer_ajax_recount_words"]').forEach(button => {
  button.addEventListener('click', e => {
    if (confirm(button.dataset.dialog)) {
      fcn_intervalAction(e.currentTarget, e.currentTarget.dataset.clickAction);
    }
  });
});

// =============================================================================
// NOTIFICATIONS
// =============================================================================

/**
 * Show notification that will vanish after a while.
 *
 * @since 5.26.0
 * @param {String} message - The message to display.
 * @param {Number} [duration=] - Seconds the message will be visible. Default 3.
 * @param {String} [type=] - Type of the message. Default 'base'.
 */

function fcn_showNotification(message, duration = 3, type = 'base') {
  // This is currently a dummy
  return;
}
