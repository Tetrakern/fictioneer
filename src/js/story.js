// =============================================================================
// SETUP
// =============================================================================

var /** @const {Number} */ fcn_storyCommentPage = 1;

// =============================================================================
// LOAD STORY COMMENTS
// =============================================================================

/**
 * Fetch and insert the HTML for the next batch of comments on the story page.
 *
 * @since 4.0
 */

function fcn_loadStoryComments() {
  // Setup
  let errorNote;

  // Prepare view
  _$('.load-more-comments-button').remove();
  _$('.comments-loading-placeholder').classList.remove('hidden');

  // Payload
  let payload = {
    'action': 'fictioneer_request_story_comments',
    'post_id': fcn_inlineStorage.postId,
    'page': fcn_storyCommentPage
  }

  // Request
  fcn_ajaxGet(payload)
  .then((response) => {
    // Check for success
    if (response.success) {
      _$('.fictioneer-comments__list > ul').innerHTML += response.data.html;
      fcn_storyCommentPage++;
    } else if (response.data?.error) {
      errorNote = fcn_buildErrorNotice(response.data.error);
    }
  })
  .catch((error) => {
    errorNote = fcn_buildErrorNotice(error);
  })
  .then(() => {
    // Remove progress state
    _$('.comments-loading-placeholder').remove();

    // Add error if any
    if (errorNote) _$('.fictioneer-comments__list > ul').appendChild(errorNote);
  });
}
