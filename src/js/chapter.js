// =============================================================================
// SETUP
// =============================================================================

const /** @const {Number} */ fcn_letterSpacingDefault = 0.0,
      /** @const {Number} */ fcn_paragraphSpacingDefault = 1.5,
      /** @const {Number} */ fcn_lineHeightDefault = 1.7,
      /** @const {Number} */ fcn_siteWidthDefault = fcn_theRoot.dataset.siteWidthDefault ?? '960',
      /** @const {HTMLElement} */ fcn_chapterFormatting = _$('.chapter-formatting');

// Initialize
var /** @type {Object} */ fcn_formatting = fcn_getFormatting();

// =============================================================================
// PARAGRAPH TOOLS
// =============================================================================

const /** @const {HTMLElement} */ fcn_paragraphTools = _$$$('paragraph-tools');

var /** @type {Number} */ fcn_lastSelectedParagraphId,
    /** @type {String} */ fcn_bookmarkColor = 'none';

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
  // Do not call paragraph tools when mobile menu is open
  if (fcn_theSite.classList.contains('transformed-site')) return;

  // Do not call paragraphs tolls on spoilers
  if (e.target.classList.contains('spoiler')) return;

  // Ignore nested paragraphs
  if (!e.target.closest('p')?.parentElement?.classList.contains('chapter-formatting')) return;

  // Ignore paragraphs with special classes
  if (
    e.target.closest('.hidden') ||
    e.target.closest('.inside-epub')
  ) return;

  // Text selection in progress
  if (window.getSelection().toString() != '') return;

  // Bubble up and search for valid paragraph (if click was on nested tag)
  const target = e.target.closest('p[data-paragraph-id]');

  // Ignore clicks inside TTS and paragraph tools buttons
  if (
    e.target.closest('.tts-interface') ||
    e.target.closest('.paragraph-tools__actions')
  ) return;

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
    const endClick = new Date().getTime(),
          long = startClick + 300;

    // Click was short, which probably means the user wants to toggle the tools
    if (endClick <= long) fcn_toggleParagraphTools(id, target);
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
  const selection = fcn_cleanTextSelectionFromButtons(window.getSelection().toString()),
        anchor = `[anchor]${e.target.closest('p[data-paragraph-id]').id}[/anchor]`;

  let quote = e.target.closest('p[data-paragraph-id]').querySelector('.paragraph-inner').innerHTML,
      pre = '[…] ',
      suf = ' […]';

  // Build from text selection and add ellipsis if necessary
  if (quote.length > 16 && selection.replace(/\s/g, '').length) {
    const fraction = Math.ceil(selection.length * .25),
          first = quote.substring(0, fraction + 1),
          last = quote.substring(quote.length - fraction, quote.length);

    if (selection.startsWith(first)) pre = '';
    if (selection.endsWith(last)) suf = '';

    quote = `${pre}${selection}${suf}`;
  }

  // Add anchor to quote
  quote = `${quote} ${anchor}`;

  // Append to comment
  fcn_addQuoteToStack(quote);

  // Show notification
  fcn_showNotification(
    __( 'Quote appended to comment!<br><a style="font-weight: 700;" href="#comments">Go to comment section.</a>', 'fictioneer' )
  );
}

/**
 * Appends a blockquote to the comment form.
 *
 * @since 3.0
 * @param {String} quote - The quote to wrap inside the blockquote.
 */

function fcn_addQuoteToStack(quote) {
  // Get comment form
  const defaultEditor = _$$$('comment');

  // Add quote
  if (defaultEditor) {
    defaultEditor.value += `\n[quote]${quote}[/quote]\n`;
    fcn_textareaAdjust(_$('textarea#comment')); // Adjust height of textarea if necessary
  } else {
    if (typeof fcn_commentStack !== 'undefined') fcn_commentStack.push(`\n[quote]${quote}[/quote]\n`); // AJAX comments
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
      __('Link copied to clipboard!', 'fictioneer')
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
      if (window.matchMedia('(min-width: 1024px)').matches) fcn_toggleParagraphTools(false);

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
 * @since 4.2
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
 * @since 4.2
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
 * @since 4.0
 * @see fcn_isValidJSONString()
 * @see fcn_defaultFormatting()
 * @see fcn_setFormatting();
 * @return {Object} The formatting settings.
 */

function fcn_getFormatting() {
  // Look in local storage...
  let f = localStorage.getItem('fcnChapterFormatting');

  // ... parse if found, set defaults otherwise
  f = (f && fcn_isValidJSONString(f)) ? JSON.parse(f) : fcn_defaultFormatting();

  // Simple validation
  if (!f || typeof f !== 'object' || Object.keys(f).length < 15) f = fcn_defaultFormatting();

  // Timestamp allows to force resets after script updates (may annoy users)
  if (!f.hasOwnProperty('timestamp') || f['timestamp'] < 1651164557584) {
    f = fcn_defaultFormatting();
    f['timestamp'] = Date.now();
  }

  // Update local storage and return
  fcn_setFormatting(f);
  return f;
}

/**
 * Returns default formatting.
 *
 * @since 4.0
 * @return {Object} The formatting settings.
 */

function fcn_defaultFormatting() {
  return {
    'font-saturation': 0,
    'font-color': fictioneer_font_colors[0].css,
    'font-name': fictioneer_fonts[0].css,
    'font-size': 100,
    'letter-spacing': fcn_letterSpacingDefault,
    'line-height': fcn_lineHeightDefault,
    'paragraph-spacing': fcn_paragraphSpacingDefault,
    'site-width': fcn_siteWidthDefault,
    'indent': true,
    'show-sensitive-content': true,
    'show-chapter-notes': true,
    'justify': false,
    'show-comments': true,
    'show-paragraph-tools': true,
    'timestamp': 1664797604825 // Used to force resets on script updates
  };
}

// =============================================================================
// SET FORMATTING
// =============================================================================

/**
 * Set the formatting settings object and save to local storage.
 *
 * @since 4.0
 * @param {Object} value - The formatting settings.
 */

function fcn_setFormatting(value) {
  // Simple validation
  if (typeof value !== 'object') return;

  // Keep global updated
  fcn_formatting = value;

  // Update local storage
  localStorage.setItem('fcnChapterFormatting', JSON.stringify(value));
}

// =============================================================================
// CHAPTER FORMATTING: FONT SIZE
// =============================================================================

const /** @const {HTMLInputElement} */ fcn_fontSizeText = _$$$('reader-settings-font-size-text'),
      /** @const {HTMLInputElement} */ fcn_fontSizeRange = _$$$('reader-settings-font-size-range'),
      /** @const {HTMLElement} */ fcn_fontSizeReset = _$$$('reader-settings-font-size-reset');

/**
 * Update font size formatting on chapters.
 *
 * @since 4.0
 * @see fcn_clamp();
 * @see fcn_setFormatting();
 * @param {Number} value - Integer between 50 and 200.
 * @param {Boolean} [save=true] - Optional. Whether to save the change.
 */

function fcn_updateFontSize(value, save = true) {
  // Evaluate
  value = fcn_clamp(50, 200, value ?? 100);

  // Update associated elements
  fcn_fontSizeText.value = value;
  fcn_fontSizeRange.value = value;
  fcn_fontSizeReset.classList.toggle('_modified', value != 100);

  // Update inline style
  _$$('.resize-font').forEach(element => {
    element.style.fontSize = `${value}%`;
  });

  // Update local storage
  fcn_formatting['font-size'] = value;
  if (save) fcn_setFormatting(fcn_formatting);
}

/**
 * Helper to call fcn_setFontSize() with input value.
 *
 * @since 4.0
 */

function fcn_setFontSize() {
  fcn_updateFontSize(this.value);
}

/**
 * Helper to call fcn_setFontSize() with incremental/decremental value.
 *
 * @since 4.0
 */

function fcn_modifyFontSize() {
  fcn_updateFontSize(
    parseFloat(fcn_formatting['font-size']) + parseFloat(this.dataset.modifier)
  );
}

// Listen for clicks on font size reset buttons
_$$$('reset-font')?.addEventListener('click', () => { fcn_updateFontSize(100) });
fcn_fontSizeReset?.addEventListener('click', () => { fcn_updateFontSize(100) });

// Listen for font size range input
fcn_fontSizeRange?.addEventListener('input', fcn_throttle(fcn_setFontSize, 1000 / 24));

// Listen for font size text input
fcn_fontSizeText?.addEventListener('input', fcn_setFontSize);

// Listen for font size increment
_$$$('increase-font')?.addEventListener('click', fcn_modifyFontSize);

// Listen for font size decrement
_$$$('decrease-font')?.addEventListener('click', fcn_modifyFontSize);

// Initialize
fcn_updateFontSize(fcn_formatting['font-size'], false);

// =============================================================================
// CHAPTER FORMATTING: FONT COLOR
// =============================================================================

const /** @const {HTMLElement} */ fcn_fontColorReset = _$$$('reader-settings-font-color-reset'),
      /** @const {HTMLElement} */ fcn_fontColorSelect = _$$$('reader-settings-font-color-select');

/**
 * Update font color on chapters.
 *
 * @since 4.0
 * @see fcn_setFormatting();
 * @param {String} index - Index of the CSS color value to set.
 * @param {Boolean} [save=true] - Optional. Whether to save the change.
 */

function fcn_updateFontColor(index, save = true) {
  // Stay within bounds
  index = fcn_clamp(0, fictioneer_font_colors.length - 1, index);

  // Update associated elements
  fcn_fontColorReset.classList.toggle('_modified', index > 0);
  fcn_fontColorSelect.value = index;

  // Update inline style
  fcn_chapterFormatting.style.setProperty('--text-chapter', fictioneer_font_colors[index].css);

  // Update local storage
  fcn_formatting['font-color'] = fictioneer_font_colors[index].css;
  if (save) fcn_setFormatting(fcn_formatting);
}

/**
 * Helper to call fcn_setFontColor() with color code and name.
 *
 * @since 4.0
 * @param {Number} [step=1] - Index modifier to move inside the font color array.
 */

function fcn_setFontColor(step = 1) {
  let index = (fcn_fontColorSelect.selectedIndex + parseInt(step)) % fictioneer_font_colors.length;
  index = index < 0 ? fictioneer_font_colors.length - 1 : index;
  fcn_updateFontColor(index);
}

// Listen for click on font color reset button
fcn_fontColorReset.onclick = () => { fcn_updateFontColor(0) }

// Listen for font color select input
fcn_fontColorSelect.onchange = (e) => { fcn_updateFontColor(e.target.value); }

// Listen for font color step buttons
_$$('.font-color-stepper').forEach(element => {
  element.addEventListener('click', (e) => {
    fcn_setFontColor(e.currentTarget.value);
  });
});

// Initialize (using the CSS name makes it independent from the array position)
fcn_updateFontColor(fictioneer_font_colors.findIndex((item) => { return item.css == fcn_formatting['font-color'] }), false);

// =============================================================================
// CHAPTER FORMATTING: FONT FAMILY
// =============================================================================

const /** @const {HTMLElement} */ fcn_fontFamilyReset = _$$$('reader-settings-font-reset'),
      /** @const {HTMLElement} */ fcn_fontFamilySelect = _$$$('reader-settings-font-select');

/**
 * Update font family on chapters.
 *
 * @since 4.0
 * @see fcn_setFormatting();
 * @param {String} index - Index of the font.
 * @param {Boolean} [save=true] - Optional. Whether to save the change.
 */

function fcn_updateFontFamily(index, save = true) {
  // Stay within bounds
  index = fcn_clamp(0, fictioneer_fonts.length - 1, index);

  // Prepare font family
  let fontFamily = `"${fictioneer_fonts[index].css}"`;

  // Add alternative fonts if any
  if (fictioneer_fonts[index].alt) fontFamily = `${fontFamily}, "${fictioneer_fonts[index].alt}"`;

  // Catch non-indexed values
  if (index < 0) {
    fcn_updateFontFamily(0);
    return;
  }

  // Update associated elements
  fcn_fontFamilyReset.classList.toggle('_modified', index > 0);
  fcn_fontFamilySelect.value = index;

  // Update inline style
  _$$('.chapter-font-family').forEach(element => {
    element.style.fontFamily = fontFamily === '""' ? 'var(--ff-system)' : fontFamily + ', var(--ff-system)';
  });

  // Update local storage
  fcn_formatting['font-name'] = fictioneer_fonts[index].css;
  if (save) fcn_setFormatting(fcn_formatting);
}

/**
 * Helper to call fcn_setFontFamily() with font name.
 *
 * @since 4.0
 * @param {Number} [step=1] - Index modifier to move inside the font array.
 */

function fcn_setFontFamily(step = 1) {
  let index = (fcn_fontFamilySelect.selectedIndex + parseInt(step)) % fictioneer_fonts.length;
  index = index < 0 ? fictioneer_fonts.length - 1 : index;
  fcn_updateFontFamily(index);
}

// Listen for click on font reset button
fcn_fontFamilyReset.onclick = () => { fcn_updateFontFamily(0) }

// Listen for font select input
fcn_fontFamilySelect.onchange = (e) => { fcn_updateFontFamily(e.target.value) }

// Listen for font step buttons (including mobile menu quick buttons)
_$$('.font-stepper').forEach(element => {
  element.addEventListener('click', (e) => {
    fcn_setFontFamily(e.currentTarget.value);
  });
});

// Initialize (using the CSS name makes it independent from the array position)
fcn_updateFontFamily(fictioneer_fonts.findIndex((item) => { return item.css == fcn_formatting['font-name'] }), false);

// =============================================================================
// CHAPTER FORMATTING: FONT SATURATION
// =============================================================================

const /** @const {HTMLInputElement} */ fcn_fontSaturationText = _$$$('reader-settings-font-saturation-text'),
      /** @const {HTMLInputElement} */ fcn_fontSaturationRange = _$$$('reader-settings-font-saturation-range'),
      /** @const {HTMLElement} */ fcn_fontSaturationReset = _$$$('reader-settings-font-saturation-reset');

/**
 * Update font saturation formatting on chapters.
 *
 * @since 4.0
 * @see fcn_clamp();
 * @see fcn_setFormatting();
 * @param {Number} value - Float between -1 and 1.
 * @param {Boolean} [save=true] - Optional. Whether to save the change.
 */

function fcn_updateFontSaturation(value, save = true) {
  // Evaluate
  value = fcn_clamp(-1, 1, value ?? 0);

  // Update associated elements
  fcn_fontSaturationText.value = parseInt(value * 100);
  fcn_fontSaturationRange.value = value;
  fcn_fontSaturationReset.classList.toggle('_modified', value != 0);

  // Update font saturation property (squared for smooth progression)
  fcn_chapterFormatting.style.setProperty(
    '--font-saturation',
    value >= 0 ? 1 + Math.pow(value, 2) : 1 - Math.pow(value, 2)
  );

  // Update local storage
  fcn_formatting['font-saturation'] = value;
  if (save) fcn_setFormatting(fcn_formatting);
}

/**
 * Helper to call fcn_updateFontSaturation() with range input value.
 *
 * @since 4.0
 */

function fcn_setFontSaturationFromRange() {
  fcn_updateFontSaturation(this.value);
}

/**
 * Helper to call fcn_updateFontSaturation() with text input value.
 *
 * @since 4.0
 */

function fcn_setFontSaturationFromText() {
  fcn_updateFontSaturation(parseInt(this.value) / 100);
}

// Listen for click on text saturation reset
fcn_fontSaturationReset?.addEventListener('click', () => { fcn_updateFontSaturation(0) });

// Listen for text saturation range input
fcn_fontSaturationRange?.addEventListener('input', fcn_throttle(fcn_setFontSaturationFromRange, 1000 / 24));

// Listen for text saturation text input
fcn_fontSaturationText?.addEventListener('input', fcn_setFontSaturationFromText);

// Initialize
fcn_updateFontSaturation(fcn_formatting['font-saturation'], false);

// =============================================================================
// CHAPTER FORMATTING: LETTER SPACING
// =============================================================================

const /** @const {HTMLInputElement} */ fcn_letterSpacingText = _$$$('reader-settings-letter-spacing-text'),
      /** @const {HTMLInputElement} */ fcn_letterSpacingRange = _$$$('reader-settings-letter-spacing-range'),
      /** @const {HTMLElement} */ fcn_letterSpacingReset = _$$$('reader-settings-letter-spacing-reset');

/**
 * Update letter-spacing formatting on chapters.
 *
 * @since 4.0
 * @see fcn_clamp();
 * @see fcn_setFormatting();
 * @param {Number} value - Float between -0.1 and 0.2.
 * @param {Boolean} [save=true] - Optional. Whether to save the change.
 */

function fcn_updateLetterSpacing(value, save = true) {
  // Evaluate
  value = fcn_clamp(-0.1, 0.2, value ?? fcn_letterSpacingDefault);

  // Update associated elements
  fcn_letterSpacingText.value = value;
  fcn_letterSpacingRange.value = value;
  fcn_letterSpacingReset.classList.toggle('_modified', value != fcn_letterSpacingDefault);

  // Update inline style
  fcn_chapterFormatting.style.letterSpacing = `calc(${value}em + var(--font-letter-spacing-base))`;

  // Update local storage
  fcn_formatting['letter-spacing'] = value;
  if (save) fcn_setFormatting(fcn_formatting);
}

/**
 * Helper to call fcn_updateLetterSpacing() with input value.
 *
 * @since 4.0
 */

function fcn_setLetterSpacing() {
  fcn_updateLetterSpacing(this.value);
}

// Listen for click on letter-spacing reset button
fcn_letterSpacingReset?.addEventListener('click', () => { fcn_updateLetterSpacing(fcn_letterSpacingDefault) });

// Listen for letter-spacing range input
fcn_letterSpacingRange?.addEventListener('input', fcn_throttle(fcn_setLetterSpacing, 1000 / 24));

// Listen for letter-spacing text input
fcn_letterSpacingText?.addEventListener('input', fcn_setLetterSpacing);

// Initialize
fcn_updateLetterSpacing(fcn_formatting['letter-spacing'], false);

// =============================================================================
// CHAPTER FORMATTING: PARAGRAPH SPACING
// =============================================================================

const /** @const {HTMLInputElement} */ fcn_paragraphSpacingText = _$$$('reader-settings-paragraph-spacing-text'),
      /** @const {HTMLInputElement} */ fcn_paragraphSpacingRange = _$$$('reader-settings-paragraph-spacing-range'),
      /** @const {HTMLElement} */ fcn_paragraphSpacingReset = _$$$('reader-settings-paragraph-spacing-reset');

/**
 * Update paragraph spacing formatting on chapters.
 *
 * @since 4.0
 * @see fcn_clamp();
 * @see fcn_setFormatting();
 * @param {Number} value - Float between 0 and 3.
 * @param {Boolean} [save=true] - Optional. Whether to save the change.
 */

function fcn_updateParagraphSpacing(value, save = true) {
  // Evaluate
  value = fcn_clamp(0, 3, value ?? 1.5);

  // Update associated elements
  fcn_paragraphSpacingText.value = value;
  fcn_paragraphSpacingRange.value = value;
  fcn_paragraphSpacingReset.classList.toggle('_modified', value != fcn_paragraphSpacingDefault);

  // Update paragraph-spacing property
  fcn_chapterFormatting.style.setProperty('--paragraph-spacing', `${value}em`);

  // Update local storage
  fcn_formatting['paragraph-spacing'] = value;
  if (save) fcn_setFormatting(fcn_formatting);
}

/**
 * Helper to call fcn_setParagraphSpacing() with input value.
 *
 * @since 4.0
 */

function fcn_setParagraphSpacing() {
  fcn_updateParagraphSpacing(this.value);
}

// Listen for click on paragraph spacing reset button
fcn_paragraphSpacingReset?.addEventListener('click', () => { fcn_updateParagraphSpacing(fcn_paragraphSpacingDefault) });

// Listen for paragraph spacing range input
fcn_paragraphSpacingRange?.addEventListener('input', fcn_throttle(fcn_setParagraphSpacing, 1000 / 24));

// Listen for paragraph spacing text input
fcn_paragraphSpacingText?.addEventListener('input', fcn_setParagraphSpacing);

// Initialize
fcn_updateParagraphSpacing(fcn_formatting['paragraph-spacing'], false);

// =============================================================================
// CHAPTER FORMATTING: LINE HEIGHT
// =============================================================================

const /** @const {HTMLInputElement} */ fcn_lineHeightText = _$$$('reader-settings-line-height-text'),
      /** @const {HTMLInputElement} */ fcn_lineHeightRange = _$$$('reader-settings-line-height-range'),
      /** @const {HTMLElement} */ fcn_lineHeightReset = _$$$('reader-settings-line-height-reset');

/**
 * Update line height formatting on chapters.
 *
 * @since 4.0
 * @see fcn_clamp();
 * @see fcn_setFormatting();
 * @param {Number} value - Float between 0.8 and 3.
 * @param {Boolean} [save=true] - Optional. Whether to save the change.
 */

function fcn_updateLineHeight(value, save = true) {
  // Evaluate
  value = fcn_clamp(0.8, 3.0, value ?? 1.7);

  // Update associated elements
  fcn_lineHeightText.value = value;
  fcn_lineHeightRange.value = value;
  fcn_lineHeightReset.classList.toggle('_modified', value != fcn_lineHeightDefault);

  // Update inline style
  fcn_chapterFormatting.style.lineHeight = `${value}`;

  // Update local storage
  fcn_formatting['line-height'] = value;
  if (save) fcn_setFormatting(fcn_formatting);
}

/**
 * Helper to call fcn_setLineHeight() with input value.
 *
 * @since 4.0
 */

function fcn_setLineHeight() {
  fcn_updateLineHeight(this.value);
}

// Listen for click on line height reset button
fcn_lineHeightReset?.addEventListener('click', () => { fcn_updateLineHeight(fcn_lineHeightDefault) });

// Listen for line height range input
fcn_lineHeightRange?.addEventListener('input', fcn_throttle(fcn_setLineHeight, 1000 / 24));

// Listen for line height text input
fcn_lineHeightText?.addEventListener('input', fcn_setLineHeight);

// Initialize
fcn_updateLineHeight(fcn_formatting['line-height'], false);

// =============================================================================
// CHAPTER FORMATTING: SITE WIDTH
// =============================================================================

const /** @const {HTMLInputElement} */ fcn_siteWidthText = _$$$('reader-settings-site-width-text'),
      /** @const {HTMLInputElement} */ fcn_siteWidthRange = _$$$('reader-settings-site-width-range'),
      /** @const {HTMLElement} */ fcn_siteWidthReset = _$$$('reader-settings-site-width-reset');

/**
 * Update site width formatting on chapters.
 *
 * @description Only affects the width of the main container since 5.0.10
 * to avoid potential layout issues.
 *
 * @since 4.0
 * @see fcn_clamp();
 * @see fcn_setFormatting();
 * @param {Number} value - Float between 640 and 1920.
 * @param {Boolean} [save=true] - Optional. Whether to save the change.
 */

function fcn_updateSiteWidth(value, save = true) {
  // Target
  const main = _$('main');

  // Evaluate
  value = fcn_clamp(640, 1920, value ?? 960);

  // Update associated elements
  fcn_siteWidthText.value = value;
  fcn_siteWidthRange.value = value;
  fcn_siteWidthReset.classList.toggle('_modified', value != fcn_siteWidthDefault);

  // Update site-width property
  main.style.setProperty('--site-width', `${value}px`);

  // Toggle utility classes
  main.classList.toggle('_default-width', value == fcn_theRoot.dataset.siteWidthDefault);
  main.classList.toggle('_below-1024', value < 1024 && value >= 768);
  main.classList.toggle('_below-768', value < 768 && value > 640);
  main.classList.toggle('_640-and-below', value <= 640);

  // Update local storage
  fcn_formatting['site-width'] = value;
  if (save) fcn_setFormatting(fcn_formatting);
}

/**
 * Helper to call fcn_updateSiteWidth() with input value.
 *
 * @since 4.0
 */

function fcn_setSiteWidth() {
  fcn_updateSiteWidth(this.value);
}

// Listen for click on site width reset button
fcn_siteWidthReset?.addEventListener('click', () => { fcn_updateSiteWidth(fcn_siteWidthDefault) });

// Listen for site width range input
fcn_siteWidthRange?.addEventListener('input', fcn_throttle(fcn_setSiteWidth, 1000 / 24));

// Listen for site width text input
fcn_siteWidthText?.addEventListener('input', fcn_setSiteWidth);

// Initialize
fcn_updateSiteWidth(fcn_formatting['site-width'], false);

// =============================================================================
// CHAPTER FORMATTING: TOGGLE INDENT
// =============================================================================

/**
 * Update indent formatting on chapters.
 *
 * @since 4.0
 * @see fcn_evaluateAsBoolean();
 * @see fcn_setFormatting();
 * @param {Any} value - The value that will be evaluated as boolean.
 * @param {Boolean} [save=true] - Optional. Whether to save the change.
 */

function fcn_updateIndent(value, save = true) {
  // Evaluate
  const boolean = fcn_evaluateAsBoolean(value, true),
        cb = _$$$('reader-settings-indent-toggle');

  // Update associated checkbox
  if (cb) {
    cb.checked = boolean;
    cb.closest('label').ariaChecked = boolean;
  }

  // Toggle classes on chapter content
  fcn_chapterFormatting.classList.toggle('no-indent', !boolean);

  // Update local storage
  fcn_formatting['indent'] = boolean;
  if (save) fcn_setFormatting(fcn_formatting);
}

// Listen for click on indent toggle
_$$$('reader-settings-indent-toggle').onclick = (e) => {
  fcn_updateIndent(e.currentTarget.checked);
}

// Initialize
fcn_updateIndent(fcn_formatting['indent'], false);

// =============================================================================
// CHAPTER FORMATTING: TOGGLE JUSTIFY
// =============================================================================

/**
 * Update justify formatting on chapters.
 *
 * @since 4.0
 * @see fcn_evaluateAsBoolean();
 * @see fcn_setFormatting();
 * @param {Any} value - The value that will be evaluated as boolean.
 * @param {Boolean} [save=true] - Optional. Whether to save the change.
 */

function fcn_updateJustify(value, save = true) {
  // Evaluate
  const boolean = fcn_evaluateAsBoolean(value, true),
        cb = _$$$('reader-settings-justify-toggle');

  // Update associated checkbox
  if (cb) {
    cb.checked = boolean;
    cb.closest('label').ariaChecked = boolean;
  }

  // Toggle classes on chapter content
  fcn_chapterFormatting.classList.toggle('justify', boolean);

  // Update local storage
  fcn_formatting['justify'] = boolean;
  if (save) fcn_setFormatting(fcn_formatting);
}

// Listen for click on justify toggle
_$$$('reader-settings-justify-toggle').onclick = (e) => {
  fcn_updateJustify(e.currentTarget.checked);
}

// Initialize
fcn_updateJustify(fcn_formatting['justify'], false);

// =============================================================================
// CHAPTER FORMATTING: TOGGLE PARAGRAPH TOOLS
// =============================================================================

/**
 * Enable or disable paragraph tools on chapters.
 *
 * @since 4.0
 * @see fcn_evaluateAsBoolean();
 * @see fcn_setFormatting();
 * @param {Any} value - The value that will be evaluated as boolean.
 * @param {Boolean} [save=true] - Optional. Whether to save the change.
 */

function fcn_updateParagraphTools(value, save = true) {
  // Evaluate
  const boolean = fcn_evaluateAsBoolean(value, true),
        cb = _$$$('reader-settings-paragraph-tools-toggle');

  // Update associated checkbox
  if (cb) {
    cb.checked = boolean;
    cb.closest('label').ariaChecked = boolean;
  }

  // Update local storage
  fcn_formatting['show-paragraph-tools'] = boolean;
  if (save) fcn_setFormatting(fcn_formatting);
}

// Listen for click on justify toggle
_$$$('reader-settings-paragraph-tools-toggle').onclick = (e) => {
  fcn_updateParagraphTools(e.currentTarget.checked);
}

// Initialize
fcn_updateParagraphTools(fcn_formatting['show-paragraph-tools'], false);

// =============================================================================
// CHAPTER FORMATTING: TOGGLE SENSITIVE CONTENT
// =============================================================================

/**
 * Show or hide marked sensitive content on chapters.
 *
 * @since 4.0
 * @see fcn_evaluateAsBoolean();
 * @see fcn_setFormatting();
 * @param {Any} value - The value that will be evaluated as boolean.
 * @param {Boolean} [save=true] - Optional. Whether to save the change.
 */

function fcn_updateSensitiveContent(value, save = true) {
  // Evaluate
  const boolean = fcn_evaluateAsBoolean(value, true),
        sensitiveToggle = _$$$('inline-sensitive-content-toggle'),
        cb = _$$$('reader-settings-sensitive-content-toggle');

  // Update associated checkbox
  if (cb) {
    cb.checked = boolean;
    cb.closest('label').ariaChecked = boolean;
  }

  // Update inline toggle
  if (sensitiveToggle) {
    sensitiveToggle.classList.toggle('hide-sensitive', !boolean);
    sensitiveToggle.ariaChecked = !boolean;
  }

  // Toggle classes on chapter content
  fcn_chapterFormatting.classList.toggle('hide-sensitive', !boolean);

  // Update local storage
  fcn_formatting['show-sensitive-content'] = boolean;
  if (save) fcn_setFormatting(fcn_formatting);
}

// Listen for click on justify toggle
_$$$('reader-settings-sensitive-content-toggle').onclick = (e) => {
  fcn_updateSensitiveContent(e.currentTarget.checked);
}

// Initialize
fcn_updateSensitiveContent(fcn_formatting['show-sensitive-content'], false);

// =============================================================================
// CHAPTER FORMATTING: TOGGLE FOREWORD/AFTERWORD/WARNINGS
// =============================================================================

/**
 * Show or hide chapter foreword, afterword, and warning.
 *
 * @since 4.0
 * @see fcn_evaluateAsBoolean();
 * @see fcn_setFormatting();
 * @param {Any} value - The value that will be evaluated as boolean.
 * @param {Boolean} [save=true] - Optional. Whether to save the change.
 */

function fcn_updateChapterNotes(value, save = true) {
  // Evaluate
  const boolean = fcn_evaluateAsBoolean(value, true),
        cb = _$$$('reader-settings-chapter-notes-toggle');

  // Update associated checkbox
  if (cb) {
    cb.checked = boolean;
    cb.closest('label').ariaChecked = boolean;
  }

  // Toggle classes on elements
  _$$('#chapter-foreword, #chapter-afterword, #chapter-warning').forEach(element => {
    element.classList.toggle('hidden', !boolean);
  });

  // Update local storage
  fcn_formatting['show-chapter-notes'] = boolean;
  if (save) fcn_setFormatting(fcn_formatting);
}

// Listen for click on justify toggle
_$$$('reader-settings-chapter-notes-toggle').onclick = (e) => {
  fcn_updateChapterNotes(e.currentTarget.checked);
}

// Initialize
fcn_updateChapterNotes(fcn_formatting['show-chapter-notes'], false);

// =============================================================================
// CHAPTER FORMATTING: TOGGLE COMMENT SECTION
// =============================================================================

function fcn_updateCommentSection(value, save = true) {
  // Evaluate
  const boolean = fcn_evaluateAsBoolean(value, true),
        cb = _$$$('reader-settings-comments-toggle');

  // Update associated checkbox
  if (cb) {
    cb.checked = boolean;
    cb.closest('label').ariaChecked = boolean;
  }

  // Toggle classes on elements
  _$$('.chapter__comments').forEach(element => {
    element.classList.toggle('hidden', !boolean);
  });

  // Update local storage
  fcn_formatting['show-comments'] = boolean;
  if (save) fcn_setFormatting(fcn_formatting);
}

// Listen for click on justify toggle
_$$$('reader-settings-comments-toggle').onclick = (e) => {
  fcn_updateCommentSection(e.currentTarget.checked);
}

// Initialize
fcn_updateCommentSection(fcn_formatting['show-comments'], false);

// =============================================================================
// READING PROGRESS BAR
// =============================================================================

const /** @const {HTMLElement} */ fcn_progressBar = _$('.progress__bar'),
      /** @const {HTMLElement} */ fcn_chapterContent = _$$$('chapter-content');

var /** @type {Boolean} */ fcn_chapterCheckmarkUpdated = false;

// Initialize
if (_$('article:not(._password)')) fcn_trackProgress();

/**
 * Checks whether this is a chapter, then adds a throttled event listener to a
 * custom scroll event that is tied to request animation frame.
 *
 * @since 4.0
 * @see fcn_readingProgress()
 * @see fcn_bindEventToAnimationFrame()
 */

function fcn_trackProgress() {
  if (!fcn_chapterContent) return;
  fcn_readingProgress();
  window.addEventListener('scroll.rAF', fcn_throttle(fcn_readingProgress, 1000 / 48));
}

/**
 * Calculates the approximate reading progress based on the scroll offset of the
 * chapter and visualizes that information as filling bar at the top. If the end
 * of the chapter is reached, the chapter is marked as read for logged-in users.
 *
 * @since 4.0
 * @see fcn_toggleCheckmark()
 * @see fcn_clamp()
 */

function fcn_readingProgress() {
  // Do nothing if minimal is enabled or the progress bar disabled
  if (
    fcn_settingMinimal.checked ||
    !fcn_settingChapterProgressBar.checked
  ) return;

  // Setup
  const rect = fcn_chapterContent.getBoundingClientRect(),
        height = rect.height,
        viewPortHeight = window.innerHeight,
        w = (height - rect.bottom - Math.max(rect.top, 0) + viewPortHeight / 2);

  let p = 100 * w / height;

  // Show progress bar depending on progress
  fcn_theBody.classList.toggle('hasProgressBar', !(p < 0 || w > height + 500));

  // Clamp percent between 0 and 100
  p = fcn_clamp(0, 100, p);

  // Apply the percentage to the bar fill
  fcn_progressBar.style.width = `${p}%`;

  // If end of chapter has been reached and the user is logged in...
  if (p >= 100 && !fcn_chapterCheckmarkUpdated && fcn_isLoggedIn) {
    const chapterList = _$$$('story-chapter-list');

    // Only do this once per page load
    fcn_chapterCheckmarkUpdated = true;

    // Make sure necessary data is available
    if (!chapterList || typeof fcn_toggleCheckmark != 'function') return;

    // Mark chapter as read
    fcn_toggleCheckmark(chapterList.dataset.storyId, 'progress', parseInt(fcn_inlineStorage.postId), null, 'set');
  }
}

// =============================================================================
// SETUP INDEX POPUP MENU
// =============================================================================

// Append cloned chapter list once when the popup menu is opened
_$$$('chapter-list-popup-toggle')?.addEventListener('click', () => {
  _$('[data-target="popup-chapter-list"]')?.appendChild(fcn_chapterList.cloneNode(true));
}, { once: true });

// =============================================================================
// KEYBOARD NAVIGATION
// =============================================================================

document.addEventListener('keydown', event => {
  const editableTags = ['INPUT', 'TEXTAREA', 'SELECT', 'OPTION'];

  // Abort if inside input...
  if (editableTags.includes(event.target.tagName) || event.target.isContentEditable) {
    return;
  }

  let link = false;

  // Check if arrow keys were pressed...
  if (event.code === 'ArrowLeft') {
    link = _$('a.button._navigation._prev');
  } else if (event.code === 'ArrowRight') {
    link = _$('a.button._navigation._next');
  }

  // Change page with scroll anchor
  if (link.href) {
    window.location.href = link + '#start';
  }
});

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
