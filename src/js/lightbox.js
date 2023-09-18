// =============================================================================
// SETUP
// =============================================================================

const /** @const {HTMLElement} */ fcn_lightbox = _$$$('fictioneer-lightbox'),
      /** @const {HTMLElement} */ fcn_lightboxTarget = _$('.lightbox__content');

// =============================================================================
// SHOW LIGHTBOX
// =============================================================================

/**
 * Show image in lightbox.
 *
 * @since 5.0.3
 * @param {HTMLElement} target - The lightbox source image.
 */

function fcn_showLightbox(target) {
  let valid = false,
      img = null;

  // Cleanup previous content (if any)
  fcn_lightboxTarget.innerHTML = '';

  // Bookmark source element for later use
  target.classList.add('lightbox-last-trigger');

  // Image or link?
  if (target.tagName == 'IMG') {
    img = target.cloneNode();
    valid = true;
  } else if (target.href) {
    img = document.createElement('img');
    img.src = target.href;
    valid = true;
  }

  // Show lightbox
  if (valid && img) {
    ['class', 'style', 'height', 'width'].forEach(attr => img.removeAttribute(attr));
    fcn_lightboxTarget.appendChild(img);
    fcn_lightbox.classList.add('show');

    const close = fcn_lightbox.querySelector('.lightbox__close');

    close?.focus();
    close?.blur();
  }
}

// =============================================================================
// EVENTS
// =============================================================================

fcn_theBody.addEventListener('click', e => {
  const target = e.target.closest('[data-lightbox]:not(.no-auto-lightbox)');

  if (target) {
    // Prevent links from working
    e.preventDefault();

    // Call lightbox
    fcn_showLightbox(target);
  }
});

fcn_theBody.addEventListener('keydown', e => {
  const target = e.target.closest('[data-lightbox]:not(.no-auto-lightbox)');

  if (target) {
    // Check pressed key
    if (e.keyCode == 32 || e.keyCode == 13) {
      // Prevent links from working
      e.preventDefault();

      // Call lightbox
      fcn_showLightbox(target);
    }
  }
});

// Lightbox controls
document.querySelectorAll('.lightbox__close, .lightbox').forEach(element => {
  element.addEventListener(
    'click',
    e => {
      if (e.target.tagName != 'IMG') {
        // Restore default view
        fcn_lightbox.classList.remove('show');

        // Restore last tab focus
        const lastTrigger = _$('.lightbox-last-trigger');

        lastTrigger?.focus();
        lastTrigger?.blur();
        lastTrigger?.classList.remove('lightbox-last-trigger');
      }
    }
  );
});
