// =============================================================================
// CONSENT MANAGEMENT
// =============================================================================

const /** @const {HTMLElement} */ fcn_consentBanner = _$$$('consent-banner');

// Show consent banner if no consent has been set, remove otherwise;
if (fcn_consentBanner && (FcnUtils.getCookie('fcn_cookie_consent') ?? '') === '' && !FcnUtils.isSearchEngineCrawler()) {
  // Delay to avoid impacting web vitals
  setTimeout(() => {
    fcn_loadConsentBanner();
  }, 4000);
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
  fcn_consentBanner.hidden = false;

  // Listen for click on full consent button
  _$$$('consent-accept-button')?.addEventListener('click', () => {
    FcnUtils.setCookie('fcn_cookie_consent', 'full')
    fcn_consentBanner.classList.add('hidden');
    fcn_consentBanner.hidden = true;
  });

  // Listen for click on necessary only consent button
  _$$$('consent-reject-button')?.addEventListener('click', () => {
    FcnUtils.setCookie('fcn_cookie_consent', 'necessary')
    fcn_consentBanner.classList.add('hidden');
    fcn_consentBanner.hidden = true;
  });
}
