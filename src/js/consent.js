// =============================================================================
// CONSENT MANAGEMENT
// =============================================================================

const /** @type {String} */ fcn_hasConsent = fcn_getCookie('fcn_cookie_consent') ?? '',
      /** @const {HTMLElement} */ fcn_consentBanner = _$$$('consent-banner');

// Show consent banner if no consent has been set
if (fcn_consentBanner && fcn_hasConsent === '') fcn_loadConsentBanner();

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
