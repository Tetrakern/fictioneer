// =============================================================================
// SETUP
// =============================================================================

const fcn_lightbox = _$$$('fictioneer-lightbox'),
      fcn_lightboxTarget = fcn_lightbox.querySelector('.target');

// =============================================================================
// EVENTS
// =============================================================================

fcn_theBody.addEventListener(
  'click',
  e => {
    // Search for eligible source...
    let target = e.target.closest('[data-lightbox]'),
        valid = false,
        img = null;

    // If found...
    if (target) {
      // Prevent links from working
      e.preventDefault();

      // Cleanup previous content (if any)
      fcn_lightboxTarget.innerHTML = '';

      // Image or link?
      if (target.tagName == 'IMG') {
        img = target.cloneNode();
        valid = true;
      } else if (target.href) {
        let img = document.createElement('img');
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
        fcn_theBody.classList.add('no-scroll');
        fcn_lightbox.classList.add('show');
      }
    }
  }
);

document.querySelectorAll('.lightbox__close, .lightbox').forEach(element => {
  element.addEventListener(
    'click',
    e => {
      if (e.target.tagName != 'IMG') {
        fcn_theBody.classList.remove('no-scroll');
        fcn_lightbox.classList.remove('show');
      }
    }
  );
});
