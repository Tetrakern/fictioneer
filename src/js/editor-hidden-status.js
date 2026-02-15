(function (wp) {
  const { __, _x } = wp.i18n;
  const { domReady } = wp;
  const { registerPlugin } = wp.plugins;
  const { PluginPostStatusInfo } = wp.editor;
  const { createElement } = wp.element;
  const { useSelect, useDispatch } = wp.data;

  const ALLOWED_TYPES = ['fcn_story', 'fcn_chapter'];

  let hiddenPreference = null;

  function isHiddenStatus(status) {
    return status === 'fcn_hidden';
  }

  function setEditorStatusFromHidden(isHidden) {
    return isHidden ? 'fcn_hidden' : 'publish';
  }

  function syncDefaultStatusLabels(status) {
    const label = _x('Unlisted', 'Post status unlisted/hidden.', 'fictioneer');
    const targets = document.querySelectorAll('.editor-post-status button.editor-post-status__toggle');

    targets.forEach(target => {
      if (!target) {
        return;
      }

      const existingFallback = target.querySelector('.fcn-status-fallback');

      if (status !== 'fcn_hidden') {
        if (existingFallback) {
          existingFallback.remove();
        }

        return;
      }

      if (!existingFallback) {
        const fallback = document.createElement('span');

        fallback.className = 'fcn-status-fallback';
        fallback.textContent = label;

        target.appendChild(fallback);
      }
    });
  }

  function syncPublishButtonLabels(status) {
    const buttons = document.querySelectorAll('.editor-post-publish-button, .editor-post-publish-button__button');

    buttons.forEach(button => {
      if (!button || typeof button.textContent !== 'string') {
        return;
      }

      if (status === 'fcn_hidden') {
        if (button.dataset.fcnStatusOverridden !== '1') {
          button.dataset.fcnOriginalLabel = button.textContent;
        }

        button.dataset.fcnStatusOverridden = '1';
        button.textContent = __('Save', 'wp');

        return;
      }

      if (status === 'future') {
        if (button.dataset.fcnStatusOverridden !== '1') {
          button.dataset.fcnOriginalLabel = button.textContent;
        }

        button.dataset.fcnStatusOverridden = '1';
        button.textContent = __('Schedule', 'wp');

        return;
      }

      if (button.dataset.fcnStatusOverridden === '1' && button.dataset.fcnOriginalLabel) {
        button.textContent = button.dataset.fcnOriginalLabel;
      }

      button.dataset.fcnStatusOverridden = '0';
    });
  }

  function HiddenStatusControl() {
    const postType = useSelect(select => {
      return select('core/editor').getCurrentPostType();
    }, []);

    const status = useSelect(select => {
      return select('core/editor').getEditedPostAttribute('status');
    }, []);

    const editPost = useDispatch('core/editor').editPost;

    if (!ALLOWED_TYPES.includes(postType)) {
      return null;
    }

    if (hiddenPreference === null) {
      hiddenPreference = isHiddenStatus(status);
    }

    return createElement(
      PluginPostStatusInfo,
      null,
      createElement(
        'div',
        { className: 'fictioneer-hidden-status-control' },
        createElement(
          'label',
          {
            htmlFor: 'fcn-hidden-status-toggle',
            className: 'fictioneer-hidden-status-control__label'
          },
          _x('Unlisted', 'Post status unlisted/hidden.', 'fictioneer')
        ),
        createElement(
          'div',
          { className: 'fictioneer-hidden-status-control__input-wrapper' },
          createElement(
            'input',
            {
              id: 'fcn-hidden-status-toggle',
              className: 'fictioneer-hidden-status-control__input',
              type: 'checkbox',
              checked: hiddenPreference,
              onChange: event => {
                hiddenPreference = event.target.checked;

                const nextStatus = setEditorStatusFromHidden(hiddenPreference);

                editPost({ status: nextStatus });
              }
            }
          )
        )
      )
    );
  }

  registerPlugin('fictioneer-hidden-status', { render: HiddenStatusControl });

  domReady(() => {
    let pendingLabelSync = false;

    function scheduleLabelSync(status) {
      if (pendingLabelSync) {
        return;
      }

      pendingLabelSync = true;

      window.requestAnimationFrame(() => {
        pendingLabelSync = false;

        syncDefaultStatusLabels(status);
        syncPublishButtonLabels(status);

        window.setTimeout(() => {
          syncDefaultStatusLabels(status);
          syncPublishButtonLabels(status);
        }, 120);
      });
    }

    wp.data.subscribe(() => {
      const select = wp.data.select('core/editor');

      if (!select) {
        return;
      }

      const postType = select.getCurrentPostType();

      if (!ALLOWED_TYPES.includes(postType)) {
        return;
      }

      let status = select.getEditedPostAttribute('status');

      if (hiddenPreference === null) {
        hiddenPreference = status === 'fcn_hidden';
      }

      if (hiddenPreference && status !== 'fcn_hidden') {
        const dispatch = wp.data.dispatch( 'core/editor' );

        if (dispatch && typeof dispatch.editPost === 'function') {
          dispatch.editPost({ status: 'fcn_hidden' });
          status = 'fcn_hidden';
        }
      }

      scheduleLabelSync(status);
    });
  });
})(window.wp);
