// =============================================================================
// EXTEND DIFF-MATCH-PATCH
// =============================================================================

diff_match_patch.prototype.fcn_prettyHtml = function(diffs) {
  var html = [];
  var pattern_amp = /&/g;
  var pattern_lt = /</g;
  var pattern_gt = />/g;
  var pattern_para = /\n/g;
  for (var x = 0; x < diffs.length; x++) {
    var op = diffs[x][0]; // Operation (insert, delete, equal)
    var data = diffs[x][1]; // Text of change.
    var text = data.replace(pattern_amp, '&amp;').replace(pattern_lt, '&lt;')
        .replace(pattern_gt, '&gt;').replace(pattern_para, '&para;<br>');
    switch (op) {
      case 1:
        html[x] = `<ins>${text}</ins>`;
        break;
      case -1:
        html[x] = `<del>${text}</del>`;
        break;
      case 0:
        html[x] = `${text}`;
        break;
    }
  }
  return html.join('');
};

// =============================================================================
// STIMULUS: FICTIONEER SUGGESTION
// =============================================================================

application.register('fictioneer-suggestion', class extends Stimulus.Controller {

  modal = _$$$('suggestions-modal');
  floatingButton = _$$$('selection-tools');
  input = _$$$('suggestions-modal-input');
  current = _$$$('suggestions-modal-original');
  diff = _$$$('suggestions-modal-diff');
  reset = _$$$('button-suggestion-reset');
  submit = _$$$('button-suggestion-submit');
  original = '';
  text = '';
  open = false;
  paragraph = null;
  dmp = new diff_match_patch();

  initialize() {
    if (this.floatingButton) {
      this.floatingButton.addEventListener('click', event => {
        this.toggleModalViaSelection(event.currentTarget);
      });
    }

    this.input.addEventListener('input', () => this.#edit());
    this.reset.addEventListener('click', () => this.#reset());
    this.submit.addEventListener('click', () => this.#append());
  }

  connect() {
    window.FictioneerApp.Controllers.fictioneerSuggestion = this;
  }

  /**
   * Hide floating button on click outside.
   *
   * Note: Event action dispatched by 'fictioneer' Stimulus Controller.
   *
   * @since 5.xx.x
   */

  clickOutside() {
    if (this.open) {
      this.#hideFloatingButton();
    }
  }

  /**
   * Toggle modal with the text selection floating button.
   *
   * @since 5.xx.x
   * @param {HTMLElement} source - Trigger of the modal.
   */

  toggleModalViaSelection(source) {
    this.paragraph = this.#closestParagraph();
    this.toggleModalVisibility(source);
  }

  /**
   * Toggle modal from the paragraph tools.
   *
   * @since 5.xx.x
   * @param {Event} event - The event.
   */

  toggleModalViaParagraph(event) {
    this.paragraph = _$('.selected-paragraph');
    this.text = FcnUtils.extractTextNodes(this.paragraph);
    this.toggleModalVisibility(event.currentTarget);
  }

  /**
   * Toggle modal visibility.
   *
   * @since 5.xx.x
   * @param {HTMLElement} source - Trigger of the modal.
   */

  toggleModalVisibility(source) {
    const fictioneer = fcn();
    const chapter = window.FictioneerApp.Controllers.fictioneerChapter;

    if (!fictioneer) {
      fcn_showNotification('Error: Fictioneer Controller not connected.', 3, 'warning');
      return;
    }

    if (!chapter) {
      fcn_showNotification('Error: Chapter Controller not connected.', 3, 'warning');
      return;
    }

    fictioneer.toggleModalVisibility(source, 'suggestions-modal');
    chapter.closeTools();

    this.#clearSelection();
    this.original = this.text;
    this.current.innerText = this.text;
    this.diff.innerText = this.text;
    this.input.value = this.text;
    this.input.focus();
  }

  /**
   * Toggle floating button.
   *
   * @since 5.xx.x
   */

  toggleFloatingButton() {
    if (
      FcnGlobals.eSite.classList.contains('transformed-site') ||
      window.getSelection().rangeCount < 1 ||
      !window.getSelection().getRangeAt(0).startContainer.parentNode.closest('.content-section')
    ) {
      return;
    }

    setTimeout(() => {
      this.text = window.getSelection().toString().replaceAll('\n\n', '\n').trim();

      if (this.text !== '') {
        const pos = this.#caretCoordinates();

        this.#showFloatingButton(pos.x, pos.y);
      } else {
        this.#hideFloatingButton();
      }
    }, 10);
  }

  /**
   * Close and reset the modal.
   *
   * @since 5.xx.x
   */

  closeModal() {
    this.open = false;
    this.original = '';
    this.text = '';
    this.paragraph = null;

    fcn().closeModals();
  }

  // =====================
  // ====== PRIVATE ======
  // =====================

  /**
   * Display floating button on coordinates.
   *
   * @since 5.xx.x
   * @param {Integer} x - The x coordinate.
   * @param {Integer} y - The y coordinate.
   */

  #showFloatingButton(x, y) {
    this.open = true;

    const offset = document.documentElement.offsetWidth / 2 + this.floatingButton.offsetWidth;
    const adminBarOffset = _$$$('wpadminbar')?.offsetHeight ?? 0;

    if (x > offset) {
      this.floatingButton.style.transform = 'translate(-100%)';
    } else {
      this.floatingButton.style.transform = 'translate(0)';
    }

    this.floatingButton.style.top = `${y + 4 - adminBarOffset}px`;
    this.floatingButton.style.left = `${x}px`;
    this.floatingButton.classList.remove('invisible');
  }

  /**
   * Hide the floating button.
   *
   * @since 5.xx.x
   */

  #hideFloatingButton() {
    this.open = false;
    this.floatingButton.style.top = '0';
    this.floatingButton.style.left = '-1000px';
    this.floatingButton.classList.add('invisible');
  }

  /**
   * Find closest paragraph to the text selection.
   *
   * @since 5.xx.x
   */

  #closestParagraph() {
    const selection = window.getSelection();

    if (!selection.rangeCount) {
      return null;
    }

    return selection.getRangeAt(0).startContainer.parentNode.closest('p');
  }

  /**
   * Returns the bottom-right x/y coordinates for the text selection.
   *
   * @since 5.xx.x
   * @return {Object} An object containing the x and y coordinates.
   */

  #caretCoordinates() {
    let x = 0;
    let y = 0;

    const isSupported = typeof window.getSelection !== 'undefined';

    if (isSupported) {
      const selection = window.getSelection();

      if (selection.rangeCount !== 0) {
        const range = selection.getRangeAt(0).cloneRange();

        let rect = range.getClientRects();
        rect = rect[rect.length - 1];

        if (rect) {
          x = rect.right + window.scrollX;
          y = rect.bottom + window.scrollY;
        }
      }
    }

    return { x, y };
  }

  /**
   * Clear the current text selection.
   *
   * @since 5.xx.x
   */

  #clearSelection() {
    if (window.getSelection) {
      if (window.getSelection().empty) {
        window.getSelection().empty(); // Chrome
      } else if (window.getSelection().removeAllRanges) {
        window.getSelection().removeAllRanges(); // Firefox
      }
    }
  }

  /**
   * Handle editing of the suggestion.
   *
   * @since 5.xx.x
   */

  #edit() {
    this.diff.innerHTML = this.#diff(this.original, this.input.value);
  }

  /**
   * Handle resetting the suggestion.
   *
   * @since 5.xx.x
   */

  #reset() {
    this.input.value = this.original;
    this.diff.innerText = this.original;
  }

  /**
   * Returns diff of two strings.
   *
   * @since 5.xx.x
   * @param {String} old - Original string.
   * @param {String} now - Current string.
   */

  #diff(old, now) {
    const diff = this.dmp.diff_main(old, now);

    this.dmp.diff_cleanupEfficiency(diff);

    return this.dmp.fcn_prettyHtml(diff);
  }

  /**
   * Finalize suggestion and append to comment.
   *
   * @since 5.xx.x
   */

  #append() {
    const paragraphID = this.paragraph?.id ?? null;
    const replacements = [
      ['¶', '&para;\n'],
      ['<br>', '\n'],
      ['<ins>', '[ins]'],
      ['</ins>', '[/ins]'],
      ['<del>', '[del]'],
      ['</del>', '[/del]']
    ];

    let final = this.diff.innerHTML;

    replacements.forEach(([source, target]) => {
      final = final.replaceAll(source, target);
    });

    if (paragraphID) {
      final = `${final} [anchor]${paragraphID}[/anchor]`;
    }

    FcnUtils.appendToComment(`\n[quote]${final}[/quote]\n`);

    this.closeModal();

    fcn_showNotification(fictioneer_tl.notification.suggestionAppendedToComment);
  }
});
