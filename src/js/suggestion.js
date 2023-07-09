// =============================================================================
// SUGGESTION TOOLS
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

class FCN_Suggestion {
  constructor() {
    this.toggle = _$$$('suggestions-modal-toggle');
    this.tools = _$$$('selection-tools');
    this.button = _$$$('button-add-suggestion');
    this.toolsButton = _$$$('button-tools-add-suggestion');
    this.reset = _$$$('button-suggestion-reset');
    this.submit = _$$$('button-suggestion-submit');
    this.current = _$$$('suggestions-modal-original');
    this.input = _$$$('suggestions-modal-input');
    this.output = _$$$('suggestions-modal-diff');
    this.chapter = _$('.chapter__article');
    this.text = '';
    this.original = '';
    this.latest = '';
    this.dmp = new diff_match_patch();
    this.bindEvents();
  }

  getCaretCoordinates() {
    let x = 0,
        y = 0;

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

  showTools(top, left) {
    const offset = document.documentElement.offsetWidth / 2 + this.tools.offsetWidth,
          adminBar = _$$$('wpadminbar') ? _$$$('wpadminbar').offsetHeight : 0;

    if (left > offset) {
      this.tools.style.transform = 'translate(-100%)';
    } else {
      this.tools.style.transform = 'translate(0)';
    }

    this.tools.style.top = `${top + 4 - adminBar}px`;
    this.tools.style.left = `${left}px`;
    this.tools.classList.remove('invisible');
  }

  hideTools() {
    this.tools.style.top = '0';
    this.tools.style.left = '-1000px';
    this.tools.classList.add('invisible');
  }

  textSelection() {
    return fcn_cleanTextSelectionFromButtons(window.getSelection().toString());
  }

  clearSelection() {
    if (window.getSelection) {
      if (window.getSelection().empty) {  // Chrome
        window.getSelection().empty();
      } else if (window.getSelection().removeAllRanges) {  // Firefox
        window.getSelection().removeAllRanges();
      }
    } else if (document.selection) {  // IE?
      document.selection.empty();
    }
  }

  getDiff(old, now) {
    const diff = this.dmp.diff_main(old, now);

    this.dmp.diff_cleanupEfficiency(diff);

    return this.dmp.fcn_prettyHtml(diff);
  }

  toggleTools(instance) {
    if (fcn_theSite.classList.contains('transformed-site')) return;
    if (window.getSelection().rangeCount < 1) return;
    if (!window.getSelection().getRangeAt(0).startContainer.parentNode.closest('.content-section')) return;

    setTimeout(() => {
      instance.text = instance.textSelection().replace('Add Suggestion', '').replaceAll('\n\n', '\n');

      if (instance.text !== '') {
        const pos = instance.getCaretCoordinates();

        instance.showTools(pos.y, pos.x);
      } else {
        instance.hideTools();
      }
    }, 10);
  }

  toggleViaParagraphTools(instance) {
    if (fcn_theSite.classList.contains('transformed-site')) return;

    instance.text = _$('.selected-paragraph')
      .querySelector('.paragraph-inner').innerHTML;

    instance.showModal(instance);
  }

  resizeInput() {
    this.input.style.height = 'auto';
    this.input.style.height = `${fcn_clamp(32, 108, this.input.scrollHeight + 4)}px`;
  }

  showModal(instance) {
    // Close paragraph tools if open
    if (fcn_lastSelectedParagraphId) fcn_toggleParagraphTools(false);

    // Setup modal content
    instance.original = instance.text;
    instance.current.innerHTML = instance.text.replaceAll('\n', '<br>');
    instance.input.value = instance.text;
    instance.output.innerHTML = instance.getDiff(instance.original, instance.text);

    // Open modal
    instance.toggle.click();
    instance.toggle.checked = true;

    // Clean up and set focus
    instance.clearSelection();
    instance.hideTools();
    instance.resizeInput();
    instance.input.focus();
  }

  editSuggestion(instance) {
    instance.resizeInput();
    instance.output.innerHTML = instance.getDiff(instance.original, instance.input.value);
  }

  resetSuggestion(instance) {
    instance.input.value = instance.original;
    instance.resizeInput();
    instance.output.innerHTML = instance.getDiff(instance.original, instance.original);
  }

  submitSuggestion(instance) {
    const defaultEditor = _$$$('comment'),
          replacements = [
            ['¶', '&para;\n'],
            ['<br>', '\n'],
            ['<ins>', '[ins]'],
            ['</ins>', '[/ins]'],
            ['<del>', '[del]'],
            ['</del>', '[/del]']
          ];

    // Perform replacements
    let final = instance.output.innerHTML;

    replacements.forEach(([source, target]) => {
      final = final.replaceAll(source, target);
    });

    // Output
    instance.latest = `\n[quote]${final}[/quote]\n`;

    // Send to comment form or remember for later
    if (defaultEditor) {
      defaultEditor.value += instance.latest;
      fcn_textareaAdjust(_$('textarea#comment')); // Adjust height of textarea if necessary
    } else {
      fcn_commentStack.push(instance.latest);
    }

    // Close modal
    instance.toggle.click();
    instance.toggle.checked = false;

    // Inform user
    fcn_showNotification(
      __('Suggestion appended to comment!<br><a style="font-weight: 700;" href="#comments">Go to comment section.</a>', 'fictioneer')
    );
  }

  bindEvents() {
    // Show Button
    this.chapter?.addEventListener('mouseup', this.toggleTools.bind(null, this));

    // Show Modal
    this.button?.addEventListener('click', this.showModal.bind(null, this));
    this.toolsButton?.addEventListener('click', this.toggleViaParagraphTools.bind(null, this));

    // Edit
    this.input?.addEventListener('input', this.editSuggestion.bind(null, this));

    // Reset
    this.reset?.addEventListener('click', this.resetSuggestion.bind(null, this));

    // Submit
    this.submit?.addEventListener('click', this.submitSuggestion.bind(null, this));
  }
}

const fcn_suggestions = _$('.chapter__article') && _$('.comment-section') ? new FCN_Suggestion() : null;

if (fcn_suggestions) {
  document.addEventListener('click', function(e) {
    if (!e.target.closest('.content-section')) fcn_suggestions.hideTools();
  });
}
