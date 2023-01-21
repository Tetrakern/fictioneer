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
        valid = false;

    // If found...
    if (target) {
      // Prevent links from working
      e.preventDefault();

      // Cleanup previous content (if any)
      fcn_lightboxTarget.innerHTML = '';

      // Image or link?
      if (target.tagName == 'IMG') {
        fcn_lightboxTarget.appendChild(target.cloneNode());
        valid = true;
      } else if (target.href) {
        let img = document.createElement('img');
        img.src = target.href;
        fcn_lightboxTarget.appendChild(img);
        valid = true;
      }

      // Stop scrolling and show lightbox
      if (valid) {
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
