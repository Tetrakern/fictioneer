// =============================================================================
// RESET ALL COLORS
// =============================================================================

wp.customize.control( 'reset_all_colors', control => {
  control.container.find( '.button' ).on( 'click', () => {
    if (confirm(fictioneerData.confirmationDialog ?? 'Are you sure?') == true) {
      wp.ajax.post(
        'fictioneer_ajax_reset_theme_colors',
        {
          fictioneer_nonce: wp.customize.settings.nonce['fictioneer-reset-colors']
        }
      ).done(response => {
        if (response.success) {
          location.reload();
        }
      });
    }
  });
});
