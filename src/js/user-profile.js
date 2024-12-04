// =============================================================================
// UNSET OAUTH CONNECTION
// =============================================================================

/**
 * Unset OAuth connection.
 *
 * @description Requests the deletion of an OAuth connection via AJAX, which
 * must be confirmed in a prompt with a localized confirmation string (DELETE
 * per default). In case this function is called maliciously, the request is
 * validated server-side as well.
 *
 * @since 4.5.0
 * @param {HTMLElement} button - The clicked button.
 */

function fcn_unsetOauth(button) {
  const confirm = prompt(button.dataset.warning);

  if (!confirm || confirm.toLowerCase() != button.dataset.confirm.toLowerCase()) {
    return;
  }

  FcnUtils.remoteAction(
    'fictioneer_ajax_unset_my_oauth',
    {
      nonce: button.dataset.nonce,
      element: _$$$(`oauth-${button.dataset.channel}`),
      payload: {
        channel: button.dataset.channel,
        id: button.dataset.id
      },
      callback: (response, element) => {
        if (response.success) {
          element.classList.remove('_connected');
          element.classList.add('_disconnected');
          element.querySelector('button').remove();
          fcn_showNotification(element.dataset.unset);
        } else {
          element.style.background = 'var(--notice-warning-background)';
        }
      },
      errorCallback: (error, element) => {
        element.style.background = 'var(--notice-warning-background)';
      }
    }
  );
}

// Listen to click on unset buttons
_$$('.button-unset-oauth').forEach(element => {
  element.addEventListener(
    'click',
    event => {
      fcn_unsetOauth(event.currentTarget);
    }
  );
});

// =============================================================================
// DELETE MY ACCOUNT
// =============================================================================

/**
 * Delete an user's account on request.
 *
 * @description Requests the deletion of an account via AJAX, which must be
 * confirmed in a prompt with a localized confirmation string (DELETE per
 * default). Roles above subscribers cannot delete their account this way.
 * In case this function is called maliciously, the request is validated
 * server-side as well.
 *
 * @since 4.5.0
 * @param {HTMLElement} button - The clicked button.
 */

function fcn_deleteMyAccount(button) {
  const confirm = prompt(button.dataset.warning);

  if (!confirm || confirm.toLowerCase() != button.dataset.confirm.toLowerCase()) {
    return;
  }

  button.disabled = true;

  FcnUtils.remoteAction(
    'fictioneer_ajax_delete_my_account',
    {
      nonce: button.dataset.nonce,
      element: button,
      payload: {
        id: button.dataset.id
      },
      callback: (response, element) => {
        if (response.success) {
          location.reload();
        } else {
          element.innerHTML = response.data.button;
        }
      },
      errorCallback: (error, element) => {
        element.innerHTML = error.status ?? fictioneer_tl.notification.error;
      }
    }
  );
}

// Listen for click on delete profile button
_$$$('button-delete-my-account')?.addEventListener(
  'click',
  event => {
    fcn_deleteMyAccount(event.currentTarget);
  }
);

// =============================================================================
// CLEAR DATA
// =============================================================================

/**
 * Prompt with string submission before deletion of user data.
 *
 * @since 5.0.0
 * @param {HTMLElement} button - The clicked button.
 * @return {Boolean} True or false.
 */

function fcn_dataDeletionPrompt(button) {
  const confirm = prompt(button.dataset.warning);

  if (!confirm || confirm.toLowerCase() != button.dataset.confirm.toLowerCase()) {
    return false;
  }

  return true;
}

/**
 * AJAX request to clear specific data.
 *
 * @since 4.5.0
 * @param {HTMLElement} button - The clicked button.
 * @param {String} action - The action to perform.
 */

function fcn_clearData(button, action) {
  const element = button.closest('.card');

  localStorage.removeItem('fcnBookshelfContent');
  button.remove();

  FcnUtils.remoteAction(
    action,
    {
      nonce: button.dataset.nonce,
      element: element,
      callback: (response, element) => {
        if (response.success && element) {
          element.querySelector('.card__content').innerHTML = response.data.success;
        }
      }
    }
  );
}

// =============================================================================
// BUTTON: CLEAR COMMENTS
// =============================================================================

// Listen for click on clear comments button
_$('.button-clear-comments')?.addEventListener(
  'click',
  event => {
    if (fcn_dataDeletionPrompt(event.currentTarget)) {
      fcn_clearData(event.currentTarget, 'fictioneer_ajax_clear_my_comments');
    }
  }
);

// =============================================================================
// BUTTON: CLEAR COMMENT SUBSCRIPTIONS
// =============================================================================

// Listen for click on clear comment subscriptions button
_$('.button-clear-comment-subscriptions')?.addEventListener(
  'click',
  event => {
    if (fcn_dataDeletionPrompt(event.currentTarget)) {
      fcn_clearData(event.currentTarget, 'fictioneer_ajax_clear_my_comment_subscriptions');
    }
  }
);

// =============================================================================
// BUTTON: CLEAR CHECKMARKS
// =============================================================================

// Listen for click on clear checkmarks button
_$('.button-clear-checkmarks')?.addEventListener(
  'click',
  event => {
    if (fcn_dataDeletionPrompt(event.currentTarget)) {
      const controller = window.FictioneerApp.Controllers.fictioneerCheckmarks;

      if (!controller) {
        fcn_showNotification('Error: Checkmarks Controller not connected.', 3, 'warning');
        return;
      }

      controller.clear();
      fcn_clearData(event.currentTarget, 'fictioneer_ajax_clear_my_checkmarks', true);
    }
  }
);

// =============================================================================
// BUTTON: CLEAR REMINDERS
// =============================================================================

// Listen for click on clear reminders button
_$('.button-clear-reminders')?.addEventListener(
  'click',
  event => {
    if (fcn_dataDeletionPrompt(event.currentTarget)) {
      const controller = window.FictioneerApp.Controllers.fictioneerReminders;

      if (!controller) {
        fcn_showNotification('Error: Reminders Controller not connected.', 3, 'warning');
        return;
      }

      controller.clear();
      fcn_clearData(event.currentTarget, 'fictioneer_ajax_clear_my_reminders', true);
    }
  }
);

// =============================================================================
// BUTTON: CLEAR FOLLOWS
// =============================================================================

// Listen for click on clear follows button
_$('.button-clear-follows')?.addEventListener(
  'click',
  event => {
    if (fcn_dataDeletionPrompt(event.currentTarget)) {
      const controller = window.FictioneerApp.Controllers.fictioneerFollows;

      if (!controller) {
        fcn_showNotification('Error: Follows Controller not connected.', 3, 'warning');
        return;
      }

      controller.clear();
      fcn_clearData(event.currentTarget, 'fictioneer_ajax_clear_my_follows', true);
    }
  }
);

// =============================================================================
// BUTTON: CLEAR BOOKMARKS
// =============================================================================

// Listen for click on clear bookmarks button
_$('.button-clear-bookmarks')?.addEventListener(
  'click',
  event => {
    if (fcn_dataDeletionPrompt(event.currentTarget)) {
      const controller = window.FictioneerApp.Controllers.fictioneerBookmarks;
      const translations = _$$$('profile-data-translations')?.dataset;

      if (!controller) {
        fcn_showNotification('Error: Bookmarks Controller not connected.', 3, 'warning');
        return;
      }

      controller.clear();
      event.currentTarget.closest('.card').querySelector('.card__content').innerHTML = translations.clearedSuccess;
    }
  }
);
