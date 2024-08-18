// =============================================================================
// SETUP
// =============================================================================

const /** @const {HTMLElement} */ fcn_chapterFormatting = _$('.chapter-formatting');

var /** @type {Object} */ fcn_formatting = fcn_getFormatting();

// =============================================================================
// PARAGRAPH TOOLS
// =============================================================================

const /** @const {HTMLElement} */ fcn_paragraphTools = _$$$('paragraph-tools');

var /** @type {Number} */ fcn_lastSelectedParagraphId;
var /** @type {String} */ fcn_bookmarkColor = 'none';

/**
 * Toggles the paragraph tools on a chapter paragraph.
 *
 * @since 3.0
 * @param {Number|Boolean} id - ID of the paragraph or false to close the tools.
 * @param {HTMLElement} [value=null] - The clicked paragraph if any.
 */

function fcn_toggleParagraphTools(id = false, target = null) {
  if (
    target &&
    target.classList.contains('spoiler') &&
    !target.classList.contains('_open')
  ) {
    return;
  }

  // Always close last paragraph tools (if open)
  _$$$(`paragraph-${fcn_lastSelectedParagraphId}`)?.classList.remove('selected-paragraph');

  // Open paragraph tools?
  if (id && fcn_formatting['show-paragraph-tools']) {
    fcn_lastSelectedParagraphId = id;
    target.classList.add('selected-paragraph');

    // Wrap if necessary
    if (!target.classList.contains('is-wrapped')) {
      target.innerHTML = `<span class="paragraph-inner">${target.innerHTML}</span>`;
      target.classList.add('is-wrapped');
    }

    // Append tools to paragraph
    target.append(fcn_paragraphTools);
  } else {
    // Close
    fcn_lastSelectedParagraphId = null;
  }
}

// Close on click outside selected paragraph (if not another paragraph)
document.addEventListener('click', event => {
  if (!fcn_paragraphTools?.closest('p')?.contains(event.target)) {
    fcn_toggleParagraphTools(false);
  }
});

/**
 * Analyzes clicks and tabs on paragraphs.
 *
 * @since 3.0
 * @param {Event} e - The event.
 */

function fcn_touchParagraph(e) {
  // Do not call paragraphs tools on spoilers, popup menus, actions, or escape class
  if (
    e.target.classList.contains('spoiler') ||
    e.target.closest('.popup-menu-toggle, .skip-tools, a, button, label, input, textarea') ||
    !e.target.closest('p')?.textContent.trim().length
  ) {
    return;
  }

  // Ignore nested paragraphs
  if (!e.target.closest('p')?.parentElement?.classList.contains('chapter-formatting')) {
    return;
  }

  // Ignore paragraphs with special classes
  if (e.target.closest('.hidden, .inside-epub')) {
    return;
  }

  // Text selection in progress
  if (window.getSelection().toString() != '') {
    return;
  }

  // Bubble up and search for valid paragraph (if click was on nested tag)
  const target = e.target.closest('p[data-paragraph-id]');

  // Ignore clicks inside TTS and paragraph tools buttons
  if (e.target.closest('.tts-interface, .paragraph-tools__actions')) {
    return;
  }

  // Clicked anywhere except on a valid paragraph or TTS
  if (!target) {
    fcn_toggleParagraphTools(false);
    return;
  }

  // Clicked on valid paragraph; check if already selected before toggling
  let id = target.dataset.paragraphId ? target.dataset.paragraphId : false;
  id = id == fcn_lastSelectedParagraphId ? false : id;

  // Remember click start time
  const startClick = new Date().getTime();

  // Evaluate click...
  target.addEventListener('mouseup', () => {
    const endClick = new Date().getTime();
    const long = startClick + 300;

    // Click was short, which probably means the user wants to toggle the tools
    if (endClick <= long) {
      fcn_toggleParagraphTools(id, target);
    }
  }, { once: true });
}

/**
 * Get quote from paragraph or text selection.
 *
 * @since 3.0
 * @param {Event} e - The event.
 */

function fcn_getQuote(e) {
  // Get paragraph text, selection, anchor, and prepare ellipsis
  const selection = fcn_cleanTextSelectionFromButtons(window.getSelection().toString());
  const anchor = `[anchor]${e.target.closest('p[data-paragraph-id]').id}[/anchor]`;

  let quote = e.target.closest('p[data-paragraph-id]').querySelector('.paragraph-inner').innerText;
  let pre = '[…] ';
  let suf = ' […]';

  // Build from text selection and add ellipsis if necessary
  if (quote.length > 16 && selection.replace(/\s/g, '').length) {
    const fraction = Math.ceil(selection.length * .25);
    const first = quote.substring(0, fraction + 1);
    const last = quote.substring(quote.length - fraction, quote.length);

    if (selection.startsWith(first)) {
      pre = '';
    }

    if (selection.endsWith(last)) {
      suf = '';
    }

    quote = `${pre}${selection}${suf}`;
  }

  // Add anchor to quote
  quote = `${quote} ${anchor}`;

  // Append to comment
  fcn_addQuoteToStack(quote);

  // Show notification
  fcn_showNotification(fictioneer_tl.notification.quoteAppendedToComment);
}

/**
 * Appends a blockquote to the comment form.
 *
 * @since 3.0
 * @param {String} quote - The quote to wrap inside the blockquote.
 */

function fcn_addQuoteToStack(quote) {
  // Get comment form
  const defaultEditor = _$(fictioneer_comments.form_selector ?? '#comment');

  // Add quote
  if (defaultEditor) {
    if (defaultEditor.tagName == 'TEXTAREA') {
      defaultEditor.value += `\n[quote]${quote}[/quote]\n`;
      fcn_textareaAdjust(_$('textarea#comment')); // Adjust height of textarea if necessary
    } else if (defaultEditor.tagName == 'DIV') {
      defaultEditor.innerHTML += `\n[quote]${quote}[/quote]\n`;
    }
  } else {
    fcn_commentStack?.push(`\n[quote]${quote}[/quote]\n`); // AJAX comment form or section
  }
}

if (fcn_paragraphTools) {
  // Listen for clicks/tabs on paragraphs
  document.addEventListener('mousedown', (e) => { fcn_touchParagraph(e); });

  // Listen for click on paragraph tools close button
  _$$$('button-close-paragraph-tools').onclick = (e) => { fcn_toggleParagraphTools(false); }

  // Listen for click on paragraph tools copy link button
  _$$$('button-get-link').onclick = (e) => {
    fcn_copyToClipboard(
      `${location.host}${location.pathname}#${e.target.closest('p[data-paragraph-id]').id}`,
      fictioneer_tl.notification.linkCopiedToClipboard
    );
  }

  // Listen for click on paragraph tools quote button
  _$$$('button-comment-stack')?.addEventListener(
    'click',
    (e) => { fcn_getQuote(e); }
  );

  // Listen for click on paragraph tools bookmark color button
  _$$('.paragraph-tools__bookmark-colors > div').forEach(element => {
    element.onclick = (e) => {
      fcn_bookmarkColor = e.target.dataset.color;
    }
  });

  // Listen for click on paragraph tools bookmark button
  _$$$('button-set-bookmark')?.addEventListener(
    'click',
    (e) => {
      fcn_toggleBookmark(
        e.target.closest('p[data-paragraph-id]').dataset.paragraphId,
        fcn_bookmarkColor
      );

      // Close paragraph tools on desktop after adding bookmark
      if (window.matchMedia('(min-width: 1024px)').matches) {
        fcn_toggleParagraphTools(false);
      }

      // Reset bookmark color
      fcn_bookmarkColor = 'none';
    }
  );
}

// =============================================================================
// ENTER FULLSCREEN
// =============================================================================

/**
 * Enter fullscreen mode.
 *
 * @since 4.2.0
 */

function fcn_openFullscreen() {
  if (document.documentElement.requestFullscreen) {
    document.documentElement.requestFullscreen();
  } else if (document.documentElement.webkitRequestFullscreen) {
    // Safari
    document.documentElement.webkitRequestFullscreen();
  }
}

// Listen for click to enter fullscreen mode
_$$('.open-fullscreen').forEach(element => {
  element.addEventListener(
    'click',
    () => {
      fcn_openFullscreen();
    }
  );
});

// =============================================================================
// CLOSE FULLSCREEN
// =============================================================================

/**
 * Close fullscreen mode.
 *
 * @since 4.2.0
 */

function fcn_closeFullscreen() {
  if (document.exitFullscreen) {
    document.exitFullscreen();
  } else if (document.webkitExitFullscreen) {
    // Safari
    document.webkitExitFullscreen();
  }
}

// Listen for click to close fullscreen
_$$('.close-fullscreen').forEach(element => {
  element.addEventListener(
    'click',
    () => {
      fcn_closeFullscreen();
    }
  );
});

// =============================================================================
// GET FORMATTING
// =============================================================================

/**
 * Get formatting JSON from local storage or create new one.
 *
 * @since 4.0.0
 * @see fcn_parseJSON()
 * @see fcn_defaultFormatting()
 * @see fcn_setFormatting();
 * @return {Object} The formatting settings.
 */

function fcn_getFormatting() {
  // Get settings from local storage or use defaults
  let formatting = fcn_parseJSON(localStorage.getItem('fcnChapterFormatting')) ?? fcn_defaultFormatting();

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
      'font-color': fictioneer_font_colors[0].css, // Set with wp_localize_script()
      'font-name': fictioneer_fonts[0].css, // Set with wp_localize_script()
      'font-size': 100,
      'letter-spacing': 0.0,
      'line-height': 1.7,
      'paragraph-spacing': 1.5,
      'site-width': fcn_theRoot.dataset.siteWidthDefault ?? '960',
      'indent': true,
      'show-sensitive-content': true,
      'show-chapter-notes': true,
      'justify': false,
      'show-comments': true,
      'show-paragraph-tools': true,
      'timestamp': 1664797604825 // Used to force resets on script updates
    },
    ...JSON.parse(fcn_theRoot.dataset.defaultFormatting ?? '{}')
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
   * @see fcn_clamp();
   * @see fcn_setFormatting();
   * @param {Number} value - Integer between 50 and 200.
   * @param {Boolean} [save=true] - Optional. Whether to save the change.
   */

  function fcn_updateFontSize(value, save = true) {
    // Evaluate
    value = fcn_clamp(50, 200, value ?? 100);

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
  range?.addEventListener('input', fcn_throttle(fcn_setFontSize, 1000 / 24));

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
    index = fcn_clamp(0, fictioneer_font_colors.length - 1, index);

    // Update associated elements
    reset.classList.toggle('_modified', index > 0);
    select.value = index;

    // Update inline style
    fcn_chapterFormatting.style.setProperty('--text-chapter', fictioneer_font_colors[index].css);

    // Update local storage
    fcn_formatting['font-color'] = fictioneer_font_colors[index].css;

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
    let index = (select.selectedIndex + parseInt(step)) % fictioneer_font_colors.length;
    index = index < 0 ? fictioneer_font_colors.length - 1 : index;
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
  fcn_updateFontColor(fictioneer_font_colors.findIndex((item) => { return item.css == fcn_formatting['font-color'] }), false);
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
    index = fcn_clamp(0, fictioneer_fonts.length - 1, index);

    // Prepare font family
    let fontFamily = fictioneer_fonts[index].css;

    // Add alternative fonts if any
    if (fictioneer_fonts[index].alt) {
      fontFamily = `${fontFamily}, ${fictioneer_fonts[index].alt}`;
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
    fcn_formatting['font-name'] = fictioneer_fonts[index].css;

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
    let index = (select.selectedIndex + parseInt(step)) % fictioneer_fonts.length;
    index = index < 0 ? fictioneer_fonts.length - 1 : index;
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
  fcn_updateFontFamily(fictioneer_fonts.findIndex((item) => { return item.css == fcn_formatting['font-name'] }), false);
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
   * @see fcn_clamp();
   * @see fcn_setFormatting();
   * @param {Number} value - Float between -1 and 1.
   * @param {Boolean} [save=true] - Optional. Whether to save the change.
   */

  function fcn_updateFontSaturation(value, save = true) {
    // Evaluate
    value = fcn_clamp(-1, 1, value ?? 0);

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
  range?.addEventListener('input', fcn_throttle(fcn_setFontSaturationFromRange, 1000 / 24));

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
   * @see fcn_clamp();
   * @see fcn_setFormatting();
   * @param {Number} value - Float between -0.1 and 0.2.
   * @param {Boolean} [save=true] - Optional. Whether to save the change.
   */

  function fcn_updateLetterSpacing(value, save = true) {
    // Evaluate
    value = fcn_clamp(-0.1, 0.2, value ?? _default);

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
  range?.addEventListener('input', fcn_throttle(fcn_setLetterSpacing, 1000 / 24));

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
   * @see fcn_clamp();
   * @see fcn_setFormatting();
   * @param {Number} value - Float between 0 and 3.
   * @param {Boolean} [save=true] - Optional. Whether to save the change.
   */

  function fcn_updateParagraphSpacing(value, save = true) {
    // Evaluate
    value = fcn_clamp(0, 3, value ?? _default);

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
  range?.addEventListener('input', fcn_throttle(fcn_setParagraphSpacing, 1000 / 24));

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
   * @see fcn_clamp();
   * @see fcn_setFormatting();
   * @param {Number} value - Float between 0.8 and 3.
   * @param {Boolean} [save=true] - Optional. Whether to save the change.
   */

  function fcn_updateLineHeight(value, save = true) {
    // Evaluate
    value = fcn_clamp(0.8, 3.0, value ?? _default);

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
  range?.addEventListener('input', fcn_throttle(fcn_setLineHeight, 1000 / 24));

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
   * @see fcn_clamp();
   * @see fcn_setFormatting();
   * @param {Number} value - Float between 640 and 1920.
   * @param {Boolean} [save=true] - Optional. Whether to save the change.
   */

  function fcn_updateSiteWidth(value, save = true) {
    // Target
    const main = _$('main');

    // Evaluate
    value = fcn_clamp(640, 1920, value ?? _default);

    // Update associated elements
    text.value = value;
    range.value = value;
    reset.classList.toggle('_modified', value != _default);

    // Update site-width property
    main.style.setProperty('--site-width', `${value}px`);

    // Toggle utility classes
    main.classList.toggle('_default-width', value == fcn_theRoot.dataset.siteWidthDefault);
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
  range?.addEventListener('input', fcn_throttle(fcn_setSiteWidth, 1000 / 24));

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
 * @see fcn_evaluateAsBoolean();
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
  const checked = fcn_evaluateAsBoolean(value, true);
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
 * @see fcn_readingProgress()
 * @see fcn_bindEventToAnimationFrame()
 */

function fcn_trackProgress() {
  if (!fcn_chapterContent) {
    return;
  }

  fcn_readingProgress();
  window.addEventListener('scroll.rAF', fcn_throttle(fcn_readingProgress, 1000 / 48));
}

/**
 * Calculates the approximate reading progress based on the scroll offset of the
 * chapter and visualizes that information as filling bar at the top. If the end
 * of the chapter is reached, the chapter is marked as read for logged-in users.
 *
 * @since 4.0.0
 * @see fcn_toggleCheckmark()
 * @see fcn_clamp()
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
  const w = (height - rect.bottom - Math.max(rect.top, 0) + viewPortHeight / 2);

  let p = 100 * w / height;

  // Show progress bar depending on progress
  fcn_theBody.classList.toggle('hasProgressBar', !(p < 0 || w > height + 500));

  // Clamp percent between 0 and 100
  p = fcn_clamp(0, 100, p);

  // Apply the percentage to the bar fill
  fcn_progressBar.style.width = `${p}%`;

  // If end of chapter has been reached and the user is logged in...
  if (p >= 100 && !fcn_chapterCheckmarkUpdated && fcn_isLoggedIn) {
    const storyId = _$$$('inline-storage')?.dataset.storyId;

    // Only do this once per page load
    fcn_chapterCheckmarkUpdated = true;

    // Make sure necessary data is available
    if (!storyId || typeof fcn_toggleCheckmark != 'function') {
      return;
    }

    // Mark chapter as read
    fcn_toggleCheckmark(storyId, 'progress', parseInt(fcn_inlineStorage.postId), null, 'set');
  }
}

// =============================================================================
// SETUP INDEX POPUP MENU
// =============================================================================

// Append cloned chapter list once when the popup menu is opened
_$$('.chapter-list-popup-toggle').forEach(element => {
  element.addEventListener('click', () => {
    element.querySelector('[data-target="popup-chapter-list"]')?.appendChild(fcn_chapterList.cloneNode(true));
  }, { once: true });
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
// SCROLL TO START OG CHAPTER
// =============================================================================

if (window.location.hash === '#start') {
  // remove anchor from URL
  history.replaceState(null, document.title, window.location.pathname);

  // Scroll to beginning of article
  const targetElement = _$('.chapter__article');

  if (targetElement) {
    fcn_scrollTo(targetElement, 128);
  }
}
