
// =============================================================================
// RESTRICT EDITOR ELEMENTS (ENGLISH ONLY)
// =============================================================================

wp.domReady(function() {

  setTimeout(() => {
    document.querySelectorAll('label').forEach(element => {
      if (element.textContent === 'Stick to the top of the blog') {
        if (!fictioneerData.userCapabilities?.fcn_make_sticky) {
          element.closest('.components-panel__row').remove();
          return;
        }
      }

      if (element.textContent === 'Allow pingbacks & trackbacks') {
        element.closest('.components-panel__row').remove();
        return;
      }
    });
  }, 200); // Wait for async; not great, not terrible solution

});
