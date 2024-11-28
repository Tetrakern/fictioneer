// =============================================================================
// STIMULUS: FICTIONEER REMINDERS
// =============================================================================

application.register('fictioneer-chapter', class extends Stimulus.Controller {
  static get targets() {
    return ['bookmarkScroll', 'contentWrapper']
  }

  static values = {
    id: Number
  }

  startClick = 0;
  lastToolsParagraph = null;
  tools = document.getElementById('paragraph-tools');

  initialize() {
  }

  connect() {
    window.FictioneerApp.Controllers.fictioneerChapter = this;
  }

  /**
   * Close paragraph tools on click outside.
   *
   * Note: Event action dispatched by 'fictioneer' Stimulus Controller.
   *
   * @since 5.xx.x
   * @param {HTMLElement} target -  The event target.
   */

  clickOutside({ detail: { target } }) {
    if (this.lastToolsParagraph && !target.closest('.selected-paragraph')) {
      this.closeTools();
    }
  }

  scrollDown() {

  }

  scrollUp() {

  }

  scrollToBookmark() {

  }

  /**
   * Open browser fullscreen view.
   *
   * @since 5.xx.x
   */

  openFullscreen() {
    if (document.documentElement.requestFullscreen) {
      document.documentElement.requestFullscreen();
    } else if (document.documentElement.webkitRequestFullscreen) {
      document.documentElement.webkitRequestFullscreen();
    }
  }

  /**
   * Close browser fullscreen view.
   *
   * @since 5.xx.x
   */

  closeFullscreen() {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    } else if (document.webkitExitFullscreen) {
      document.webkitExitFullscreen();
    }
  }

  /**
   * Toggle paragraph tools.
   *
   * @since 5.xx.x
   * @param {HTMLElement} paragraph - The clicked paragraph.
   */

  toggleTools(paragraph) {
    // Always close last paragraph tools (if open)
    this.lastToolsParagraph?.classList.remove('selected-paragraph');

    // Close if same paragraph
    if (this.lastToolsParagraph === paragraph) {
      this.closeTools();
      return;
    }

    // Add tools to paragraph
    this.lastToolsParagraph = paragraph;
    paragraph.classList.add('selected-paragraph');
    paragraph.append(this.tools);
  }

  /**
   * Close paragraph tools.
   *
   * @since 5.xx.x
   */

  closeTools() {
    this.lastToolsParagraph?.classList.remove('selected-paragraph');
    this.lastToolsParagraph = null;
  }

  /**
   * Listen to fast click on paragraph to open paragraph tools.
   *
   * @since 5.xx.x
   * @param {Event} event - The event.
   */

  fastClick(event) {
    if (window.getSelection().toString() != '') {
      return;
    }

    this.startClick = Date.now();

    document.addEventListener('mouseup', () => {
      const endClick = Date.now();

      if (endClick < this.startClick + 400) {
        if (this.#isValidParagraph(event.target) && this.tools) {
          this.toggleTools(event.target.closest('p'));
        }
      }
    }, { once: true });
  }

  /**
   * Get quote from paragraph or text selection.
   *
   * @since 3.0
   * @since 5.xx.x - Folded into Stimulus Controller.
   * @param {Event} event - The event.
   */

  quote(event) {
    const target = event.target.closest('p[data-paragraph-id]');

    if (!target) {
      return;
    }

    const selection = window.getSelection() ? window.getSelection().toString().trim() : '';
    const anchor = `[anchor]${target.id}[/anchor]`;

    let quote = Array.from(target.childNodes)
      .filter(node => node.nodeType === Node.TEXT_NODE)
      .map(node => node.textContent.trim())
      .join(' ');

    if (quote.length > 16 && selection.replace(/\s/g, '').length) {
      const fraction = Math.ceil(selection.length * .25);

      let pre = fictioneer_tl.partial.quoteFragmentPrefix;
      let suf = fictioneer_tl.partial.quoteFragmentSuffix;

      if (selection.startsWith(quote.substring(0, fraction + 1))) {
        pre = '';
      }

      if (selection.endsWith(quote.substring(quote.length - fraction, quote.length))) {
        suf = '';
      }

      quote = `${pre}${selection}${suf}`;
    }

    fcn_showNotification(fictioneer_tl.notification.quoteAppendedToComment);

    this.#appendToComment(`\n[quote]${quote} ${anchor}[/quote]\n`);
  }

  suggest(event) {

  }

  toggleBookmark({ target }) {
    fcn_toggleBookmark(
      target.closest('p[data-paragraph-id]').dataset.paragraphId,
      target.closest('[data-color]')?.dataset.color ?? 'default'
    );

    if (window.matchMedia('(min-width: 1024px)').matches) {
      this.closeTools();
    }
  }

  /**
   * Copy paragraph link to clipboard.
   *
   * @since 5.xx.x
   * @param {Event} event - The event.
   */

  copyLink(event) {
    const target = event.target.closest('p[data-paragraph-id]');

    if (target) {
      FcnUtils.copyToClipboard(
        `${location.protocol}//${location.host}${location.pathname}#${target.id}`,
        fictioneer_tl.notification.linkCopiedToClipboard
      );
    }
  }

  // =====================
  // ====== PRIVATE ======
  // =====================

  /**
   * Appends a content to the comment form.
   *
   * @since 3.0
   * @since 5.xx.x - Folded into Stimulus Controller.
   * @param {String} content - The content to append.
   */

  #appendToComment(content) {
    const defaultEditor = _$(FcnGlobals.commentFormSelector);

    if (!defaultEditor) {
      FcnGlobals.commentStack.push(content);
      return;
    }

    switch (defaultEditor.tagName) {
      case 'TEXTAREA':
        defaultEditor.value += content;
        FcnUtils.adjustTextarea(defaultEditor);
        break;
      case 'DIV':
        defaultEditor.innerHTML += content;
        break;
    }
  }

  #isValidParagraph(target) {
    const interactiveSelector = '.popup-menu-toggle, .skip-tools, .tts-interface, .paragraph-tools__actions, .hidden, .inside-epub, a, button, label, input, textarea, select, option';

    if (
      target.classList.contains('spoiler') ||
      !target.closest('p[data-paragraph-id]')?.textContent.trim().length ||
      !target.closest('p')?.parentElement?.classList.contains('chapter-formatting') ||
      target.closest(interactiveSelector)
    ) {
      return false;
    }

    return true;
  }
});













// =============================================================================
// SETUP CHAPTER INDEX DIALOG MODAL
// =============================================================================

_$('[data-click-action*="toggle-chapter-index-order"]')?.addEventListener('click', event => {
  const wrapper = event.currentTarget.closest('.chapter-index');
  wrapper.dataset.order = wrapper.dataset.order === 'asc' ? 'desc' : 'asc';
});

document.addEventListener('DOMContentLoaded', () => {
  const chapterIndex = _$('.chapter-index');
  const postId = chapterIndex.closest('dialog').dataset.postId ?? 0;

  if (chapterIndex) {
    chapterIndex.querySelector(`[data-id="${postId}"]`)?.classList.add('current');
  }
});

// =============================================================================
// KEYBOARD NAVIGATION
// =============================================================================

// Keep removable reference
const fcn_chapterKeyboardNavigation = event => {
  const editableTags = ['INPUT', 'TEXTAREA', 'SELECT', 'OPTION'];

  // Abort if inside input...
  if (editableTags.includes(event.target.tagName) || event.target.isContentEditable) {
    return;
  }

  let link = null;

  // Check if arrow keys were pressed...
  if (event.code === 'ArrowLeft') {
    link = _$('a.button._navigation._prev');
  } else if (event.code === 'ArrowRight') {
    link = _$('a.button._navigation._next');
  }

  // Change page with scroll anchor
  if (link && link.href) {
    window.location.href = link + '#start';
  }
}

document.addEventListener('keydown', fcn_chapterKeyboardNavigation);

// =============================================================================
// SCROLL TO START OF CHAPTER
// =============================================================================

if (window.location.hash === '#start') {
  // remove anchor from URL
  history.replaceState(null, document.title, window.location.pathname);

  // Scroll to beginning of article
  const targetElement = _$('.chapter__article');

  if (targetElement) {
    FcnUtils.scrollTo(targetElement, 128);
  }
}








// =============================================================================
// SETUP
// =============================================================================

const /** @const {HTMLElement} */ fcn_chapterFormatting = _$('.chapter-formatting');

var /** @type {Object} */ fcn_formatting = fcn_getFormatting();

// =============================================================================
// PARAGRAPH TOOLS
// =============================================================================


var /** @type {Number} */ fcn_lastSelectedParagraphId; // Used by suggestions






// =============================================================================
// GET FORMATTING
// =============================================================================

/**
 * Get formatting JSON from local storage or create new one.
 *
 * @since 4.0.0
 * @see fcn_defaultFormatting()
 * @see fcn_setFormatting();
 * @return {Object} The formatting settings.
 */

function fcn_getFormatting() {
  // Get settings from local storage or use defaults
  let formatting = FcnUtils.parseJSON(localStorage.getItem('fcnChapterFormatting')) ?? fcn_defaultFormatting();

  // Simple validation
  if (Object.keys(formatting).length < 15) {
    formatting = fcn_defaultFormatting();
  }

  // Timestamp allows to force resets after script updates (may annoy users)
  if (formatting['timestamp'] < 1651164557584) {
    formatting = fcn_defaultFormatting();
    formatting['timestamp'] = Date.now();
  }

  // Update local storage and return
  fcn_setFormatting(formatting);

  return formatting;
}

/**
 * Returns default formatting.
 *
 * @since 4.0.0
 * @return {Object} The formatting settings.
 */

function fcn_defaultFormatting() {
  return {
    ...{
      'font-saturation': 0,
      'font-color': FcnGlobals.fontColors[0].css, // Set with wp_localize_script()
      'font-name': FcnGlobals.fonts[0].css, // Set with wp_localize_script()
      'font-size': 100,
      'letter-spacing': 0.0,
      'line-height': 1.7,
      'paragraph-spacing': 1.5,
      'site-width': document.documentElement.dataset.siteWidthDefault ?? '960',
      'indent': true,
      'show-sensitive-content': true,
      'show-chapter-notes': true,
      'justify': false,
      'show-comments': true,
      'show-paragraph-tools': true,
      'timestamp': 1664797604825 // Used to force resets on script updates
    },
    ...FcnUtils.parseJSON(document.documentElement.dataset.defaultFormatting ?? '{}')
  };
}

// =============================================================================
// SET FORMATTING
// =============================================================================

/**
 * Set the formatting settings object and save to local storage.
 *
 * @since 4.0.0
 * @param {Object} value - The formatting settings.
 */

function fcn_setFormatting(value) {
  // Simple validation
  if (typeof value !== 'object') {
    return;
  }

  // Keep global updated
  fcn_formatting = value;

  // Update local storage
  localStorage.setItem('fcnChapterFormatting', JSON.stringify(value));
}

// =============================================================================
// CHAPTER FORMATTING: FONT SIZE
// =============================================================================

(() => {
  'use strict';

  const /** @const {HTMLInputElement} */ text = _$$$('reader-settings-font-size-text');
  const /** @const {HTMLInputElement} */ range = _$$$('reader-settings-font-size-range');
  const /** @const {HTMLElement} */ reset = _$$$('reader-settings-font-size-reset');

  // Abort if nothing to do
  if (!reset) {
    return;
  }

  /**
   * Update font size formatting on chapters.
   *
   * @since 4.0.0
   * @see fcn_setFormatting();
   * @param {Number} value - Integer between 50 and 200.
   * @param {Boolean} [save=true] - Optional. Whether to save the change.
   */

  function fcn_updateFontSize(value, save = true) {
    // Evaluate
    value = FcnUtils.clamp(50, 200, value ?? 100);

    // Update associated elements
    text.value = value;
    range.value = value;
    reset.classList.toggle('_modified', value != 100);

    // Update inline style
    _$$('.resize-font').forEach(element => {
      element.style.fontSize = `${value}%`;
    });

    // Update local storage
    fcn_formatting['font-size'] = value;

    if (save) {
      fcn_setFormatting(fcn_formatting);
    }
  }

  /**
   * Helper to call fcn_setFontSize() with input value.
   *
   * @since 4.0.0
   */

  function fcn_setFontSize() {
    fcn_updateFontSize(this.value);
  }

  /**
   * Helper to call fcn_setFontSize() with incremental/decremental value.
   *
   * @since 4.0.0
   */

  function fcn_modifyFontSize() {
    fcn_updateFontSize(
      parseFloat(fcn_formatting['font-size']) + parseFloat(this.dataset.modifier)
    );
  }

  // Listen for clicks on font size reset buttons
  _$$$('reset-font')?.addEventListener('click', () => { fcn_updateFontSize(100) });
  reset?.addEventListener('click', () => { fcn_updateFontSize(100) });

  // Listen for font size range input
  range?.addEventListener('input', FcnUtils.throttle(fcn_setFontSize, 1000 / 24));

  // Listen for font size text input
  text?.addEventListener('input', fcn_setFontSize);

  // Listen for font size increment
  _$$$('increase-font')?.addEventListener('click', fcn_modifyFontSize);

  // Listen for font size decrement
  _$$$('decrease-font')?.addEventListener('click', fcn_modifyFontSize);

  // Initialize
  fcn_updateFontSize(fcn_formatting['font-size'], false);
})();

// =============================================================================
// CHAPTER FORMATTING: FONT COLOR
// =============================================================================

(() => {
  'use strict';

  const /** @const {HTMLElement} */ reset = _$$$('reader-settings-font-color-reset');
  const /** @const {HTMLElement} */ select = _$$$('reader-settings-font-color-select');

  // Abort if nothing to do
  if (!reset) {
    return;
  }

  /**
   * Update font color on chapters.
   *
   * @since 4.0.0
   * @see fcn_setFormatting();
   * @param {String} index - Index of the CSS color value to set.
   * @param {Boolean} [save=true] - Optional. Whether to save the change.
   */

  function fcn_updateFontColor(index, save = true) {
    // Stay within bounds
    index = FcnUtils.clamp(0, FcnGlobals.fontColors.length - 1, index);

    // Update associated elements
    reset.classList.toggle('_modified', index > 0);
    select.value = index;

    // Update inline style
    fcn_chapterFormatting.style.setProperty('--text-chapter', FcnGlobals.fontColors[index].css);

    // Update local storage
    fcn_formatting['font-color'] = FcnGlobals.fontColors[index].css;

    if (save) {
      fcn_setFormatting(fcn_formatting);
    }
  }

  /**
   * Helper to call fcn_setFontColor() with color code and name.
   *
   * @since 4.0.0
   * @param {Number} [step=1] - Index modifier to move inside the font color array.
   */

  function fcn_setFontColor(step = 1) {
    let index = (select.selectedIndex + parseInt(step)) % FcnGlobals.fontColors.length;
    index = index < 0 ? FcnGlobals.fontColors.length - 1 : index;
    fcn_updateFontColor(index);
  }

  // Listen for click on font color reset button
  reset.onclick = () => { fcn_updateFontColor(0) }

  // Listen for font color select input
  select.onchange = (e) => { fcn_updateFontColor(e.target.value); }

  // Listen for font color step buttons
  _$$('.font-color-stepper').forEach(element => {
    element.addEventListener('click', (e) => {
      fcn_setFontColor(e.currentTarget.value);
    });
  });

  // Initialize (using the CSS name makes it independent from the array position)
  fcn_updateFontColor(FcnGlobals.fontColors.findIndex((item) => { return item.css == fcn_formatting['font-color'] }), false);
})();

// =============================================================================
// CHAPTER FORMATTING: FONT FAMILY
// =============================================================================

(() => {
  'use strict';

  const /** @const {HTMLElement} */ reset = _$$$('reader-settings-font-reset');
  const /** @const {HTMLElement} */ select = _$$$('reader-settings-font-select');

  // Abort if nothing to do
  if (!reset) {
    return;
  }

  /**
   * Update font family on chapters.
   *
   * @since 4.0.0
   * @see fcn_setFormatting();
   * @param {String} index - Index of the font.
   * @param {Boolean} [save=true] - Optional. Whether to save the change.
   */

  function fcn_updateFontFamily(index, save = true) {
    // Stay within bounds
    index = FcnUtils.clamp(0, FcnGlobals.fonts.length - 1, index);

    // Prepare font family
    let fontFamily = FcnGlobals.fonts[index].css;

    // Add alternative fonts if any
    if (FcnGlobals.fonts[index].alt) {
      fontFamily = `${fontFamily}, ${FcnGlobals.fonts[index].alt}`;
    }

    // Catch non-indexed values
    if (index < 0) {
      fcn_updateFontFamily(0);
      return;
    }

    // Update associated elements
    reset.classList.toggle('_modified', index > 0);
    select.value = index;

    // Update inline style
    _$$('.chapter-font-family').forEach(element => {
      element.style.fontFamily = fontFamily === '' ? 'var(--ff-system)' : fontFamily + ', var(--ff-system)';
    });

    // Update local storage
    fcn_formatting['font-name'] = FcnGlobals.fonts[index].css;

    if (save) {
      fcn_setFormatting(fcn_formatting);
    }
  }

  /**
   * Helper to call fcn_setFontFamily() with font name.
   *
   * @since 4.0.0
   * @param {Number} [step=1] - Index modifier to move inside the font array.
   */

  function fcn_setFontFamily(step = 1) {
    let index = (select.selectedIndex + parseInt(step)) % FcnGlobals.fonts.length;
    index = index < 0 ? FcnGlobals.fonts.length - 1 : index;
    fcn_updateFontFamily(index);
  }

  // Listen for click on font reset button
  reset.onclick = () => { fcn_updateFontFamily(0) }

  // Listen for font select input
  select.onchange = (e) => { fcn_updateFontFamily(e.target.value) }

  // Listen for font step buttons (including mobile menu quick buttons)
  _$$('.font-stepper').forEach(element => {
    element.addEventListener('click', (e) => {
      fcn_setFontFamily(e.currentTarget.value);
    });
  });

  // Initialize (using the CSS name makes it independent from the array position)
  fcn_updateFontFamily(FcnGlobals.fonts.findIndex((item) => { return item.css == fcn_formatting['font-name'] }), false);
})();

// =============================================================================
// CHAPTER FORMATTING: FONT SATURATION
// =============================================================================

(() => {
  'use strict';

  const /** @const {HTMLInputElement} */ text = _$$$('reader-settings-font-saturation-text');
  const /** @const {HTMLInputElement} */ range = _$$$('reader-settings-font-saturation-range');
  const /** @const {HTMLElement} */ reset = _$$$('reader-settings-font-saturation-reset');

  // Abort if nothing to do
  if (!reset) {
    return;
  }

  /**
   * Update font saturation formatting on chapters.
   *
   * @since 4.0.0
   * @see fcn_setFormatting();
   * @param {Number} value - Float between -1 and 1.
   * @param {Boolean} [save=true] - Optional. Whether to save the change.
   */

  function fcn_updateFontSaturation(value, save = true) {
    // Evaluate
    value = FcnUtils.clamp(-1, 1, value ?? 0);

    // Update associated elements
    text.value = parseInt(value * 100);
    range.value = value;
    reset.classList.toggle('_modified', value != 0);

    // Update font saturation property (squared for smooth progression)
    fcn_chapterFormatting.style.setProperty(
      '--font-saturation',
      `(${value >= 0 ? 1 + value ** 2 : 1 - value ** 2} + var(--font-saturation-offset))`
    );

    // Update local storage
    fcn_formatting['font-saturation'] = value;

    if (save) {
      fcn_setFormatting(fcn_formatting);
    }
  }

  /**
   * Helper to call fcn_updateFontSaturation() with range input value.
   *
   * @since 4.0.0
   */

  function fcn_setFontSaturationFromRange() {
    fcn_updateFontSaturation(this.value);
  }

  /**
   * Helper to call fcn_updateFontSaturation() with text input value.
   *
   * @since 4.0.0
   */

  function fcn_setFontSaturationFromText() {
    fcn_updateFontSaturation(parseInt(this.value) / 100);
  }

  // Listen for click on text saturation reset
  reset?.addEventListener('click', () => { fcn_updateFontSaturation(0) });

  // Listen for text saturation range input
  range?.addEventListener('input', FcnUtils.throttle(fcn_setFontSaturationFromRange, 1000 / 24));

  // Listen for text saturation text input
  text?.addEventListener('input', fcn_setFontSaturationFromText);

  // Initialize
  fcn_updateFontSaturation(fcn_formatting['font-saturation'], false);
})();

// =============================================================================
// CHAPTER FORMATTING: LETTER SPACING
// =============================================================================

(() => {
  'use strict';

  const /** @const {HTMLInputElement} */ text = _$$$('reader-settings-letter-spacing-text');
  const /** @const {HTMLInputElement} */ range = _$$$('reader-settings-letter-spacing-range');
  const /** @const {HTMLElement} */ reset = _$$$('reader-settings-letter-spacing-reset');
  const /** @const {Number} */ _default = fcn_defaultFormatting()['letter-spacing'];

  // Abort if nothing to do
  if (!reset) {
    return;
  }

  /**
   * Update letter-spacing formatting on chapters.
   *
   * @since 4.0.0
   * @see fcn_setFormatting();
   * @param {Number} value - Float between -0.1 and 0.2.
   * @param {Boolean} [save=true] - Optional. Whether to save the change.
   */

  function fcn_updateLetterSpacing(value, save = true) {
    // Evaluate
    value = FcnUtils.clamp(-0.1, 0.2, value ?? _default);

    // Update associated elements
    text.value = value;
    range.value = value;
    reset.classList.toggle('_modified', value != _default);

    // Update inline style
    fcn_chapterFormatting.style.letterSpacing = `calc(${value}em + var(--font-letter-spacing-base))`;

    // Update local storage
    fcn_formatting['letter-spacing'] = value;

    if (save) {
      fcn_setFormatting(fcn_formatting);
    }
  }

  /**
   * Helper to call fcn_updateLetterSpacing() with input value.
   *
   * @since 4.0.0
   */

  function fcn_setLetterSpacing() {
    fcn_updateLetterSpacing(this.value);
  }

  // Listen for click on letter-spacing reset button
  reset?.addEventListener('click', () => { fcn_updateLetterSpacing(_default) });

  // Listen for letter-spacing range input
  range?.addEventListener('input', FcnUtils.throttle(fcn_setLetterSpacing, 1000 / 24));

  // Listen for letter-spacing text input
  text?.addEventListener('input', fcn_setLetterSpacing);

  // Initialize
  fcn_updateLetterSpacing(fcn_formatting['letter-spacing'], false);
})();

// =============================================================================
// CHAPTER FORMATTING: PARAGRAPH SPACING
// =============================================================================

(() => {
  'use strict';

  const /** @const {HTMLInputElement} */ text = _$$$('reader-settings-paragraph-spacing-text');
  const /** @const {HTMLInputElement} */ range = _$$$('reader-settings-paragraph-spacing-range');
  const /** @const {HTMLElement} */ reset = _$$$('reader-settings-paragraph-spacing-reset');
  const /** @const {Number} */ _default = fcn_defaultFormatting()['paragraph-spacing'];

  // Abort if nothing to do
  if (!reset) {
    return;
  }

  /**
   * Update paragraph spacing formatting on chapters.
   *
   * @since 4.0.0
   * @see fcn_setFormatting();
   * @param {Number} value - Float between 0 and 3.
   * @param {Boolean} [save=true] - Optional. Whether to save the change.
   */

  function fcn_updateParagraphSpacing(value, save = true) {
    // Evaluate
    value = FcnUtils.clamp(0, 3, value ?? _default);

    // Update associated elements
    text.value = value;
    range.value = value;
    reset.classList.toggle('_modified', value != _default);

    // Update paragraph-spacing property
    fcn_chapterFormatting.style.setProperty('--paragraph-spacing', `${value}em`);

    // Update local storage
    fcn_formatting['paragraph-spacing'] = value;

    if (save) {
      fcn_setFormatting(fcn_formatting);
    }
  }

  /**
   * Helper to call fcn_setParagraphSpacing() with input value.
   *
   * @since 4.0.0
   */

  function fcn_setParagraphSpacing() {
    fcn_updateParagraphSpacing(this.value);
  }

  // Listen for click on paragraph spacing reset button
  reset?.addEventListener('click', () => { fcn_updateParagraphSpacing(_default) });

  // Listen for paragraph spacing range input
  range?.addEventListener('input', FcnUtils.throttle(fcn_setParagraphSpacing, 1000 / 24));

  // Listen for paragraph spacing text input
  text?.addEventListener('input', fcn_setParagraphSpacing);

  // Initialize
  fcn_updateParagraphSpacing(fcn_formatting['paragraph-spacing'], false);
})();

// =============================================================================
// CHAPTER FORMATTING: LINE HEIGHT
// =============================================================================

(() => {
  'use strict';

  const /** @const {HTMLInputElement} */ text = _$$$('reader-settings-line-height-text');
  const /** @const {HTMLInputElement} */ range = _$$$('reader-settings-line-height-range');
  const /** @const {HTMLElement} */ reset = _$$$('reader-settings-line-height-reset');
  const /** @const {Number} */ _default = fcn_defaultFormatting()['line-height'];

  // Abort if nothing to do
  if (!reset) {
    return;
  }

  /**
   * Update line height formatting on chapters.
   *
   * @since 4.0.0
   * @see fcn_setFormatting();
   * @param {Number} value - Float between 0.8 and 3.
   * @param {Boolean} [save=true] - Optional. Whether to save the change.
   */

  function fcn_updateLineHeight(value, save = true) {
    // Evaluate
    value = FcnUtils.clamp(0.8, 3.0, value ?? _default);

    // Update associated elements
    text.value = value;
    range.value = value;
    reset.classList.toggle('_modified', value != _default);

    // Update inline style
    fcn_chapterFormatting.style.lineHeight = `${value}`;

    // Update local storage
    fcn_formatting['line-height'] = value;

    if (save) {
      fcn_setFormatting(fcn_formatting);
    }
  }

  /**
   * Helper to call fcn_setLineHeight() with input value.
   *
   * @since 4.0.0
   */

  function fcn_setLineHeight() {
    fcn_updateLineHeight(this.value);
  }

  // Listen for click on line height reset button
  reset?.addEventListener('click', () => { fcn_updateLineHeight(_default) });

  // Listen for line height range input
  range?.addEventListener('input', FcnUtils.throttle(fcn_setLineHeight, 1000 / 24));

  // Listen for line height text input
  text?.addEventListener('input', fcn_setLineHeight);

  // Initialize
  fcn_updateLineHeight(fcn_formatting['line-height'], false);
})();

// =============================================================================
// CHAPTER FORMATTING: SITE WIDTH
// =============================================================================

(() => {
  'use strict';

  const /** @const {HTMLInputElement} */ text = _$$$('reader-settings-site-width-text');
  const /** @const {HTMLInputElement} */ range = _$$$('reader-settings-site-width-range');
  const /** @const {HTMLElement} */ reset = _$$$('reader-settings-site-width-reset');
  const /** @const {Number} */ _default = fcn_defaultFormatting()['site-width'];

  // Abort if nothing to do
  if (!reset) {
    return;
  }

  /**
   * Update site width formatting on chapters.
   *
   * @description Only affects the width of the main container since 5.0.10
   * to avoid potential layout issues.
   *
   * @since 4.0.0
   * @see fcn_setFormatting();
   * @param {Number} value - Float between 640 and 1920.
   * @param {Boolean} [save=true] - Optional. Whether to save the change.
   */

  function fcn_updateSiteWidth(value, save = true) {
    // Target
    const main = _$('main');

    // Evaluate
    value = FcnUtils.clamp(640, 1920, value ?? _default);

    // Update associated elements
    text.value = value;
    range.value = value;
    reset.classList.toggle('_modified', value != _default);

    // Update site-width property
    main.style.setProperty('--site-width', `${value}px`);

    // Toggle utility classes
    main.classList.toggle('_default-width', value == document.documentElement.dataset.siteWidthDefault);
    main.classList.toggle('_below-1024', value < 1024 && value >= 768);
    main.classList.toggle('_below-768', value < 768 && value > 640);
    main.classList.toggle('_640-and-below', value <= 640);

    // Update local storage
    fcn_formatting['site-width'] = value;

    if (save) {
      fcn_setFormatting(fcn_formatting);
    }
  }

  /**
   * Helper to call fcn_updateSiteWidth() with input value.
   *
   * @since 4.0.0
   */

  function fcn_setSiteWidth() {
    fcn_updateSiteWidth(this.value);
  }

  // Listen for click on site width reset button
  reset?.addEventListener('click', () => { fcn_updateSiteWidth(_default) });

  // Listen for site width range input
  range?.addEventListener('input', FcnUtils.throttle(fcn_setSiteWidth, 1000 / 24));

  // Listen for site width text input
  text?.addEventListener('input', fcn_setSiteWidth);

  // Initialize
  fcn_updateSiteWidth(fcn_formatting['site-width'], false);
})();

// =============================================================================
// CHAPTER FORMATTING TOGGLES
// =============================================================================

/**
 * Update formatting and toggles on chapters.
 *
 * @since 5.9.4
 * @see fcn_setFormatting();
 * @param {Any} value - The value that will be evaluated as boolean.
 * @param {String} selector - The selector of the toggle.
 * @param {String} setting - The name of the setting.
 * @param {Object} [args={}] - Optional arguments.
 */

function fcn_updateToggle(value, selector, setting, args = {}) {
  // Defaults
  args = {...{ save: true }, ...args};

  // Evaluate
  const checked = Boolean(value);
  const cb = _$(selector);

  // Update associated checkbox
  if (cb) {
    cb.checked = checked;
    cb.closest('label').setAttribute('aria-checked', checked);
  }

  // Toggle classes on chapter content
  if (args.toggleClass) {
    fcn_chapterFormatting.classList.toggle(args.toggleClass, args.invertClass ? !checked : checked);
  }

  // Case: Sensitive content
  if (args.sensitiveContent) {
    const sensitiveToggle = _$$$('inline-sensitive-content-toggle');

    sensitiveToggle?.classList.toggle('hide-sensitive', !checked);
    sensitiveToggle?.setAttribute('aria-checked', !checked);
  }

  // Case: Notes
  if (args.notes) {
    _$$('.chapter-note-hideable').forEach(element => {
      element.classList.toggle('hidden', !checked);
    });
  }

  // Case: Comments
  if (args.comments) {
    _$$('.chapter-comments-hideable').forEach(element => {
      element.classList.toggle('hidden', !checked);
    });
  }

  // Update local storage
  fcn_formatting[setting] = checked;

  if (args.save) {
    fcn_setFormatting(fcn_formatting);
  }
}

// --- INDENT ----------------------------------------------------------------

_$$('#reader-settings-indent-toggle').forEach(toggle => {
  const args = { invertClass: true, toggleClass: 'no-indent' };
  const setting = 'indent';

  // Listen for click
  toggle.onclick = (e) => {
    fcn_updateToggle(e.currentTarget.checked, `#${toggle.id}`, setting, args);
  }

  // Initialize
  fcn_updateToggle(fcn_formatting[setting], `#${toggle.id}`, setting, {...{ save: false }, ...args});
});

// --- JUSTIFY ---------------------------------------------------------------

_$$('#reader-settings-justify-toggle').forEach(toggle => {
  const args = { toggleClass: 'justify' };
  const setting = 'justify';

  // Listen for click
  toggle.onclick = (e) => {
    fcn_updateToggle(e.currentTarget.checked, `#${toggle.id}`, setting, args);
  }

  // Initialize
  fcn_updateToggle(fcn_formatting[setting], `#${toggle.id}`, setting, {...{ save: false }, ...args});
});

// --- PARAGRAPH TOOLS -------------------------------------------------------

_$$('#reader-settings-paragraph-tools-toggle').forEach(toggle => {
  const setting = 'show-paragraph-tools';

  // Listen for click
  toggle.onclick = (e) => {
    fcn_updateToggle(e.currentTarget.checked, `#${toggle.id}`, setting);
  }

  // Initialize
  fcn_updateToggle(fcn_formatting[setting], `#${toggle.id}`, setting, { save: false });
});

// --- FOREWORD/AFTERWORD/WARNINGS -------------------------------------------

_$$('#reader-settings-chapter-notes-toggle').forEach(toggle => {
  const args = { notes: true };
  const setting = 'show-chapter-notes';

  // Listen for click
  toggle.onclick = (e) => {
    fcn_updateToggle(e.currentTarget.checked, `#${toggle.id}`, setting, args);
  }

  // Initialize
  fcn_updateToggle(fcn_formatting[setting], `#${toggle.id}`, setting, {...{ save: false }, ...args});
});

// --- COMMENTS --------------------------------------------------------------

_$$('#reader-settings-comments-toggle').forEach(toggle => {
  const args = { comments: true };
  const setting = 'show-comments';

  // Listen for click
  toggle.onclick = (e) => {
    fcn_updateToggle(e.currentTarget.checked, `#${toggle.id}`, setting, args);
  }

  // Initialize
  fcn_updateToggle(fcn_formatting[setting], `#${toggle.id}`, setting, {...{ save: false }, ...args});
});

// --- SENSITIVE CONTENT -----------------------------------------------------

_$$('#reader-settings-sensitive-content-toggle').forEach(toggle => {
  const args = { toggleClass: 'hide-sensitive', invertClass: true, sensitiveContent: true };
  const setting = 'show-sensitive-content';

  // Listen for click
  toggle.onclick = (e) => {
    fcn_updateToggle(e.currentTarget.checked, `#${toggle.id}`, setting, args);
  }

  // Initialize
  fcn_updateToggle(fcn_formatting[setting], `#${toggle.id}`, setting, {...{ save: false }, ...args});
});

// =============================================================================
// READING PROGRESS BAR
// =============================================================================

const /** @const {HTMLElement} */ fcn_progressBar = _$('.progress__bar');
const /** @const {HTMLElement} */ fcn_chapterContent = _$$$('chapter-content');

var /** @type {Boolean} */ fcn_chapterCheckmarkUpdated = false;

// Initialize
if (_$('article:not(._password)')) {
  fcn_trackProgress();
}

/**
 * Checks whether this is a chapter, then adds a throttled event listener to a
 * custom scroll event that is tied to request animation frame.
 *
 * @since 4.0.0
 */

function fcn_trackProgress() {
  if (!fcn_chapterContent) {
    return;
  }

  fcn_readingProgress();
  window.addEventListener('scroll.rAF', FcnUtils.throttle(fcn_readingProgress, 1000 / 48));
}

/**
 * Calculates the approximate reading progress based on the scroll offset of the
 * chapter and visualizes that information as filling bar at the top. If the end
 * of the chapter is reached, the chapter is marked as read for logged-in users.
 *
 * @since 4.0.0
 */

function fcn_readingProgress() {
  // Do nothing if minimal is enabled or the progress bar disabled
  if (fcn_settingMinimal.checked || !fcn_settingChapterProgressBar.checked) {
    return;
  }

  // Setup
  const rect = fcn_chapterContent.getBoundingClientRect();
  const height = rect.height;
  const viewPortHeight = window.innerHeight;
  const w = (height - rect.bottom - Math.max(rect.top, 0) + viewPortHeight);

  let p = 100 * w / height;

  // Show progress bar depending on progress
  document.body.classList.toggle('hasProgressBar', !(p < 0 || w > height + 500));

  // Clamp percent between 0 and 100
  p = FcnUtils.clamp(0, 100, p);

  // Apply the percentage to the bar fill
  fcn_progressBar.style.width = `${p}%`;

  // If end of chapter has been reached and the user is logged in...
  if (p >= 100 && !fcn_chapterCheckmarkUpdated && FcnUtils.loggedIn()) {
    const storyId = document.body.dataset.storyId;

    // Only do this once per page load
    fcn_chapterCheckmarkUpdated = true;

    // Make sure necessary data is available
    if (!storyId || typeof fcn_toggleCheckmark !== 'function') {
      return;
    }

    // Mark chapter as read
    console.log('progress check');
    fcn_toggleCheckmark(storyId, parseInt(document.body.dataset.postId), true);
  }
}
