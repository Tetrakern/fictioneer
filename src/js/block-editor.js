
// =============================================================================
// RESTRICT EDITOR ELEMENTS (ENGLISH ONLY)
// =============================================================================

const unsubscribe = wp.data.subscribe(() => {
  // Wait for elements to be loaded...
  if (document.querySelector('.editor-post-panel__row')) {

    // Permalink
    if (!fictioneerData?.userCapabilities?.fcn_edit_permalink) {
      document.querySelector('.editor-post-url__panel-dropdown')?.closest('.editor-post-panel__row')?.remove();
    }

    // Only do this once
    unsubscribe();
  }
});
