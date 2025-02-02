
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

    // Template (not working)
    // if (!fictioneerData?.userCapabilities?.fcn_select_page_template) {
    //   wp.data.dispatch('core/editor').removeEditorPanel('template');
    // }

    // Only do this once
    unsubscribe();
  }
});
