
// =============================================================================
// RESTRICT STICKY CHECKBOX
// =============================================================================

wp.domReady(function() {

  setTimeout(() => {
    document.querySelectorAll('label').forEach(element => {
      if (element.textContent === 'Stick to the top of the blog') {
        if (!fictioneerData.userCapabilities?.fcn_make_sticky) {
          element.closest('.components-panel__row').remove();
        }
      }
    });
  }, 100);

});
