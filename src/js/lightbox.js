// =============================================================================
// SETUP
// =============================================================================

const fcn_lightbox = _$$$('fictioneer-lightbox'),
      fcn_lightboxTarget = fcn_lightbox.querySelector('.target');

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

  // Stop scrolling and show lightbox
  if (valid && img) {
    img.removeAttribute('class');
    img.removeAttribute('style');
    img.removeAttribute('height');
    img.removeAttribute('width');
    fcn_lightboxTarget.appendChild(img);
    // fcn_theBody.classList.add('no-scroll');
    fcn_lightbox.classList.add('show');

    const close = fcn_lightbox.querySelector('.lightbox__close');

    close?.focus();
    close?.blur();
  }
}

// =============================================================================
// EVENTS
// =============================================================================

fcn_theBody.querySelectorAll('[data-lightbox]').forEach(element => {
  // Click on images
  element.addEventListener(
    'click',
    e => {
      // Prevent links from working
      e.preventDefault();

      // Call lightbox
      fcn_showLightbox(e.currentTarget);
    }
  );

  // When pressing space or enter
  element.addEventListener(
    'keydown',
    e => {
      // Check pressed key
      if (e.keyCode == 32 || e.keyCode == 13) {
        // Prevent links from working
        e.preventDefault();

        // Call lightbox
        fcn_showLightbox(e.currentTarget);
      }
    }
  );
});

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
