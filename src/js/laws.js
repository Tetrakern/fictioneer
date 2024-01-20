// =============================================================================
// CONSENT MANAGEMENT
// =============================================================================

const /** @type {String} */ fcn_hasConsent = fcn_getCookie('fcn_cookie_consent') ?? '',
      /** @const {HTMLElement} */ fcn_consentBanner = _$$$('consent-banner');

// Show consent banner if no consent has been set, remove otherwise;
if (fcn_consentBanner && fcn_hasConsent === '') {
  // Delay to avoid impacting web vitals
  setTimeout(() => {
    fcn_loadConsentBanner();
  }, 2000);
} else {
  fcn_consentBanner.remove();
}

/**
 * Load consent banner if required.
 *
 * @since 3.0
 */

function fcn_loadConsentBanner() {
  fcn_consentBanner.classList.remove('hidden');

  // Listen for click on full consent button
  _$$$('consent-accept-button')?.addEventListener('click', () => {
    fcn_setCookie('fcn_cookie_consent', 'full')
    fcn_consentBanner.classList.add('hidden');
  });

  // Listen for click on necessary only consent button
  _$$$('consent-reject-button')?.addEventListener('click', () => {
    fcn_setCookie('fcn_cookie_consent', 'necessary')
    fcn_consentBanner.classList.add('hidden');
  });
}

// =============================================================================
// AGE CONFIRMATION
// =============================================================================

if (_$$$('age-confirmation-modal') && localStorage.getItem('fcnAgeConfirmation') !== '1') {
  // Delay to avoid impacting web vitals
  setTimeout(() => {
    fcn_showAgeConfirmationModal();
  }, 2000);
} else {
  _$$$('age-confirmation-modal')?.remove();
}

/**
 * Show age confirmation modal if required.
 *
 * @since 5.9.0
 */

function fcn_showAgeConfirmationModal() {
  // Adult story or chapter?
  const rating = _$('.story__article, .chapter__article')?.dataset.ageRating;

  if (rating && rating !== 'adult') {
    _$$$('age-confirmation-modal')?.remove();
    return;
  }

  // Setup
  const leave = _$$$('age-confirmation-leave');

  // Disable site and show modal
  fcn_theRoot.classList.add('age-modal-open');
  _$$$('age-confirmation-modal').classList.add('_open');

  // Confirm button
  _$$$('age-confirmation-confirm')?.addEventListener('click', event => {
    fcn_theRoot.classList.remove('age-modal-open');
    event.currentTarget.closest('.modal').remove();
    localStorage.setItem('fcnAgeConfirmation', '1');
  });

  // Leave button
  leave?.addEventListener('click', () => {
    window.location.href = leave.dataset.redirect ?? 'https://search.brave.com/';
    localStorage.removeItem('fcnAgeConfirmation');
  });
}
