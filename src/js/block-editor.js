
// =============================================================================
// RESTRICT EDITOR ELEMENTS (ENGLISH ONLY)
// =============================================================================

// https://github.com/WordPress/gutenberg/tree/trunk/packages/edit-post/src/components/sidebar

wp.domReady(() => {

  // Page attributes
  if (!fictioneerData.userCapabilities?.manage_options) {
    wp.data.dispatch('core/edit-post').removeEditorPanel('page-attributes');
  }

  // Permalink
  if (!fictioneerData.userCapabilities?.fcn_edit_permalink) {
    wp.data.dispatch('core/edit-post').removeEditorPanel('post-link');
  }

  // Template
  if (!fictioneerData.userCapabilities?.fcn_select_page_template) {
    wp.data.dispatch('core/edit-post').removeEditorPanel('template');
  }

  // Sticky checkbox and pink-/trackbacks
  setTimeout(() => {
    document.querySelectorAll('label').forEach(element => {
      if (element.textContent === 'Stick to the top of the blog') {
        if (!fictioneerData.userCapabilities?.fcn_make_sticky) {
          element.closest('.components-panel__row').remove();
        }
      }

      if (element.textContent === 'Allow pingbacks & trackbacks') {
        element.closest('.components-panel__row').remove();
      }
    });
  }, 200);

  // Inserter media tab (insufficient but I cannot do better =/)
  if (!fictioneerData.userCapabilities?.fcn_read_others_files) {
    const fcn_editorObserver = new MutationObserver(mutations => {
      mutations.forEach(mutation => {
        if (mutation.addedNodes && mutation.addedNodes.length > 0) {
          const hasMediaTab = document.querySelector('.block-editor-inserter__tabs > .components-tab-panel__tabs');

          if (hasMediaTab) {
            hasMediaTab.remove();
          }
        }
      });
    });

    fcn_editorObserver.observe(
      document.querySelector('#editor'),
      { childList: true, subtree: true }
    );
  }

});
