// =============================================================================
// STIMULUS: FICTIONEER CHAPTER
// =============================================================================

application.register('fictioneer-chapter', class extends Stimulus.Controller {
  static get targets() {
    return ['bookmarkScroll', 'contentWrapper', 'index', 'content']
  }

  static values = {
    chapterId: Number,
    storyId: Number
  }

  startClick = 0;
  lastToolsParagraph = null;
  tools = _$$$('paragraph-tools');
  progressBar = _$('.progress__bar');
  hasPassword = !!_$('article._password');
  checkmarkUpdated = false;
  checkboxProgressBar = _$$$('site-setting-chapter-progress-bar');
  checkboxMinimalist = _$$$('site-setting-minimal');

  initialize() {
    if (this.progressBar && this.hasContentTarget && !this.hasPassword) {
      this.trackProgress();
      window.addEventListener('scroll.rAF', FcnUtils.throttle(this.trackProgress.bind(this), 1000 / 48));
    }
  }

  connect() {
    window.FictioneerApp.Controllers.fictioneerChapter = this;

    // Mark current chapter in index
    if (this.hasIndexTarget) {
      this.indexTargets.forEach(element => {
        element.querySelector(`[data-id="${this.chapterIdValue}"]`)?.classList.add('current');
      });
    }
  }

  /**
   * Close paragraph tools on click outside.
   *
   * Note: Event action dispatched by 'fictioneer' Stimulus Controller.
   *
   * @since 5.27.0
   * @param {HTMLElement} target -  The event target.
   */

  clickOutside({ detail: { target } }) {
    if (this.lastToolsParagraph && !target.closest('.selected-paragraph')) {
      this.closeTools();
    }
  }

  scrollToBookmark() {
    const controller = window.FictioneerApp.Controllers.fictioneerBookmarks;

    if (!controller) {
      fcn_showNotification('Error: Bookmarks Controller not connected.', 3, 'warning');
      return;
    }

    const paragraphID = controller.data()?.[`ch-${this.chapterIdValue}`]?.['paragraph-id'];

    if (!paragraphID) {
      return;
    }

    const target = _$(`[data-paragraph-id="${paragraphID}"]`);

    target?.scrollIntoView({ behavior: 'smooth' });
  }

  /**
   * Open browser fullscreen view.
   *
   * @since 5.27.0
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
   * @since 5.27.0
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
   * @since 5.27.0
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
   * @since 5.27.0
   */

  closeTools() {
    this.lastToolsParagraph?.classList.remove('selected-paragraph');
    this.lastToolsParagraph = null;
  }

  /**
   * Listen to fast click on paragraph to open paragraph tools.
   *
   * @since 5.27.0
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
   * @since 5.27.0 - Folded into Stimulus Controller.
   * @param {Event} event - The event.
   */

  quote(event) {
    const target = event.target.closest('p[data-paragraph-id]');

    if (!target) {
      return;
    }

    const selection = window.getSelection() ? window.getSelection().toString().trim() : '';
    const anchor = `[anchor]${target.id}[/anchor]`;

    let quote = FcnUtils.extractTextNodes(target);

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

    FcnUtils.appendToComment(`\n[quote]${quote} ${anchor}[/quote]\n`);
  }

  toggleBookmark({ target }) {
    const controller = window.FictioneerApp.Controllers.fictioneerBookmarks;

    if (!controller) {
      fcn_showNotification('Error: Bookmarks Controller not connected.', 3, 'warning');
      return;
    }

    controller.toggle(
      target.closest('p[data-paragraph-id]').dataset.paragraphId,
      target.closest('[data-color]')?.dataset.color ?? 'none'
    );

    if (window.matchMedia('(min-width: 1024px)').matches) {
      this.closeTools();
    }
  }

  /**
   * Copy paragraph link to clipboard.
   *
   * @since 5.27.0
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

  toggleIndexOrder({ currentTarget }) {
    const wrapper = currentTarget.closest('.chapter-index');
    wrapper.dataset.order = wrapper.dataset.order === 'asc' ? 'desc' : 'asc';
  }

  trackProgress() {
    // Abort if...
    if (
      !this.progressBar || !this.hasContentTarget || this.hasPassword ||
      (this.checkboxMinimalist.checked ?? 0) || !(this.checkboxProgressBar?.checked ?? 1)
    ) {
      return;
    }

    // Calculate progress
    const rect = this.contentTarget.getBoundingClientRect();
    const height = rect.height;
    const w = (height - rect.bottom - Math.max(rect.top, 0) + window.innerHeight);

    let p = 100 * w / height;

    // Show progress bar depending on progress
    document.body.classList.toggle('hasProgressBar', !(p < 0 || w > height + 500));

    // Clamp percent between 0 and 100
    p = FcnUtils.clamp(0, 100, p);

    // Apply the percentage to the bar fill
    this.progressBar.style.width = `${p}%`;

    // If end of chapter has been reached and the user is logged in...
    if (!this.checkmarkUpdated && this.hasStoryIdValue && !!this.storyIdValue && p >= 100 && FcnUtils.loggedIn()) {
      this.checkmarkUpdated = true;

      // Make sure necessary data is available
      if (!this.hasChapterIdValue || !this.chapterIdValue || typeof fcn_toggleCheckmark !== 'function') {
        return;
      }

      // Mark chapter as read
      fcn_toggleCheckmark(this.storyIdValue, this.chapterIdValue, true);
    }
  }

  // =====================
  // ====== PRIVATE ======
  // =====================

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
// KEYBOARD NAVIGATION
// =============================================================================

// Keep removable reference
const fcn_chapterKeyboardNavigation = event => {
  if (
    ['INPUT', 'TEXTAREA', 'SELECT', 'OPTION'].includes(event.target.tagName) ||
    event.target.isContentEditable
  ) {
    return;
  }

  const keyMap = {
    ArrowLeft: 'a.button._navigation._prev',
    ArrowRight: 'a.button._navigation._next'
  };

  if (!keyMap[event.code]) {
    return;
  }

  const link = _$(keyMap[event.code]);

  if (link?.href) {
    window.location.href = link.href.includes("#start") ? link.href : `${link.href}#start`;
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
// FORMATTING UTILITIES
// =============================================================================

const FcnFormatting = {
  eFormattingTarget: _$('.chapter-formatting'),

  /**
   * Returns default chapter formatting.
   *
   * @since 4.0.0
   * @since 5.27.0 - Folded into FcnFormatting.
   * @return {Object} Default chapter formatting settings.
   */

  defaults() {
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
        'timestamp': 1664797604825
      },
      ...FcnUtils.parseJSON(document.documentElement.dataset.defaultFormatting ?? '{}')
    };
  },

  /**
   * Returns chapter formatting from local storage.
   *
   * Note: The timestamp can be used to force a reset,
   * which will absolutely annoy users.
   *
   * @since 4.0.0
   * @since 5.27.0 - Folded into FcnFormatting.
   * @return {Object} Chapter formatting settings.
   */

  get() {
    let formatting = FcnUtils.parseJSON(localStorage.getItem('fcnChapterFormatting')) ?? FcnFormatting.defaults();

    if (Object.keys(formatting).length < 15) {
      formatting = FcnFormatting.defaults();

      FcnFormatting.set(formatting);
    }

    if (formatting['timestamp'] < 1651164557584) {
      formatting = FcnFormatting.defaults();
      formatting['timestamp'] = Date.now();

      FcnFormatting.set(formatting);
    }

    return formatting;
  },

  /**
   * Save the chapter formatting settings to local storage.
   *
   * @since 4.0.0
   * @since 5.27.0 - Folded into FcnFormatting.
   * @param {Object} value - Chapter formatting settings.
   */

  set(value) {
    if (typeof value === 'object') {
      localStorage.setItem('fcnChapterFormatting', JSON.stringify(value));
    }
  }
};

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
   * @param {Number} value - Integer between 50 and 200.
   * @param {Boolean} [save=true] - Optional. Whether to save the change.
   */

  function fcn_updateFontSize(value, save = true) {
    const formatting = FcnFormatting.get();

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
    formatting['font-size'] = value;

    if (save) {
      FcnFormatting.set(formatting);
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
    const formatting = FcnFormatting.get();

    fcn_updateFontSize(
      parseFloat(formatting['font-size']) + parseFloat(this.dataset.modifier)
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
  const formatting = FcnFormatting.get();
  fcn_updateFontSize(formatting['font-size'], false);
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
   * @param {String} index - Index of the CSS color value to set.
   * @param {Boolean} [save=true] - Optional. Whether to save the change.
   */

  function fcn_updateFontColor(index, save = true) {
    const formatting = FcnFormatting.get();

    // Stay within bounds
    index = FcnUtils.clamp(0, FcnGlobals.fontColors.length - 1, index);

    // Update associated elements
    reset.classList.toggle('_modified', index > 0);
    select.value = index;

    // Update inline style
    FcnFormatting.eFormattingTarget.style.setProperty('--text-chapter', FcnGlobals.fontColors[index].css);

    // Update local storage
    formatting['font-color'] = FcnGlobals.fontColors[index].css;

    if (save) {
      FcnFormatting.set(formatting);
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
  select.onchange = (event) => { fcn_updateFontColor(event.target.value); }

  // Listen for font color step buttons
  _$$('.font-color-stepper').forEach(element => {
    element.addEventListener('click', (event) => {
      fcn_setFontColor(event.currentTarget.value);
    });
  });

  // Initialize (using the CSS name makes it independent from the array position)
  const formatting = FcnFormatting.get();
  fcn_updateFontColor(FcnGlobals.fontColors.findIndex((item) => { return item.css == formatting['font-color'] }), false);
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
   * @param {String} index - Index of the font.
   * @param {Boolean} [save=true] - Optional. Whether to save the change.
   */

  function fcn_updateFontFamily(index, save = true) {
    const formatting = FcnFormatting.get();

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
    formatting['font-name'] = FcnGlobals.fonts[index].css;

    if (save) {
      FcnFormatting.set(formatting);
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
  const formatting = FcnFormatting.get();
  fcn_updateFontFamily(FcnGlobals.fonts.findIndex((item) => { return item.css == formatting['font-name'] }), false);
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
   * @param {Number} value - Float between -1 and 1.
   * @param {Boolean} [save=true] - Optional. Whether to save the change.
   */

  function fcn_updateFontSaturation(value, save = true) {
    const formatting = FcnFormatting.get();

    // Evaluate
    value = FcnUtils.clamp(-1, 1, value ?? 0);

    // Update associated elements
    text.value = parseInt(value * 100);
    range.value = value;
    reset.classList.toggle('_modified', value != 0);

    // Update font saturation property (squared for smooth progression)
    FcnFormatting.eFormattingTarget.style.setProperty(
      '--font-saturation',
      `(${value >= 0 ? 1 + value ** 2 : 1 - value ** 2} + var(--font-saturation-offset))`
    );

    // Update local storage
    formatting['font-saturation'] = value;

    if (save) {
      FcnFormatting.set(formatting);
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
  const formatting = FcnFormatting.get();
  fcn_updateFontSaturation(formatting['font-saturation'], false);
})();

// =============================================================================
// CHAPTER FORMATTING: LETTER SPACING
// =============================================================================

(() => {
  'use strict';

  const /** @const {HTMLInputElement} */ text = _$$$('reader-settings-letter-spacing-text');
  const /** @const {HTMLInputElement} */ range = _$$$('reader-settings-letter-spacing-range');
  const /** @const {HTMLElement} */ reset = _$$$('reader-settings-letter-spacing-reset');
  const /** @const {Number} */ _default = FcnFormatting.defaults()['letter-spacing'];

  // Abort if nothing to do
  if (!reset) {
    return;
  }

  /**
   * Update letter-spacing formatting on chapters.
   *
   * @since 4.0.0
   * @param {Number} value - Float between -0.1 and 0.2.
   * @param {Boolean} [save=true] - Optional. Whether to save the change.
   */

  function fcn_updateLetterSpacing(value, save = true) {
    const formatting = FcnFormatting.get();

    // Evaluate
    value = FcnUtils.clamp(-0.1, 0.2, value ?? _default);

    // Update associated elements
    text.value = value;
    range.value = value;
    reset.classList.toggle('_modified', value != _default);

    // Update inline style
    FcnFormatting.eFormattingTarget.style.letterSpacing = `calc(${value}em + var(--font-letter-spacing-base))`;

    // Update local storage
    formatting['letter-spacing'] = value;

    if (save) {
      FcnFormatting.set(formatting);
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
  const formatting = FcnFormatting.get();
  fcn_updateLetterSpacing(formatting['letter-spacing'], false);
})();

// =============================================================================
// CHAPTER FORMATTING: PARAGRAPH SPACING
// =============================================================================

(() => {
  'use strict';

  const /** @const {HTMLInputElement} */ text = _$$$('reader-settings-paragraph-spacing-text');
  const /** @const {HTMLInputElement} */ range = _$$$('reader-settings-paragraph-spacing-range');
  const /** @const {HTMLElement} */ reset = _$$$('reader-settings-paragraph-spacing-reset');
  const /** @const {Number} */ _default = FcnFormatting.defaults()['paragraph-spacing'];

  // Abort if nothing to do
  if (!reset) {
    return;
  }

  /**
   * Update paragraph spacing formatting on chapters.
   *
   * @since 4.0.0
   * @param {Number} value - Float between 0 and 3.
   * @param {Boolean} [save=true] - Optional. Whether to save the change.
   */

  function fcn_updateParagraphSpacing(value, save = true) {
    const formatting = FcnFormatting.get();

    // Evaluate
    value = FcnUtils.clamp(0, 3, value ?? _default);

    // Update associated elements
    text.value = value;
    range.value = value;
    reset.classList.toggle('_modified', value != _default);

    // Update paragraph-spacing property
    FcnFormatting.eFormattingTarget.style.setProperty('--paragraph-spacing', `${value}em`);

    // Update local storage
    formatting['paragraph-spacing'] = value;

    if (save) {
      FcnFormatting.set(formatting);
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
  const formatting = FcnFormatting.get();
  fcn_updateParagraphSpacing(formatting['paragraph-spacing'], false);
})();

// =============================================================================
// CHAPTER FORMATTING: LINE HEIGHT
// =============================================================================

(() => {
  'use strict';

  const /** @const {HTMLInputElement} */ text = _$$$('reader-settings-line-height-text');
  const /** @const {HTMLInputElement} */ range = _$$$('reader-settings-line-height-range');
  const /** @const {HTMLElement} */ reset = _$$$('reader-settings-line-height-reset');
  const /** @const {Number} */ _default = FcnFormatting.defaults()['line-height'];

  // Abort if nothing to do
  if (!reset) {
    return;
  }

  /**
   * Update line height formatting on chapters.
   *
   * @since 4.0.0
   * @param {Number} value - Float between 0.8 and 3.
   * @param {Boolean} [save=true] - Optional. Whether to save the change.
   */

  function fcn_updateLineHeight(value, save = true) {
    const formatting = FcnFormatting.get();

    // Evaluate
    value = FcnUtils.clamp(0.8, 3.0, value ?? _default);

    // Update associated elements
    text.value = value;
    range.value = value;
    reset.classList.toggle('_modified', value != _default);

    // Update inline style
    FcnFormatting.eFormattingTarget.style.lineHeight = `${value}`;

    // Update local storage
    formatting['line-height'] = value;

    if (save) {
      FcnFormatting.set(formatting);
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
  const formatting = FcnFormatting.get();
  fcn_updateLineHeight(formatting['line-height'], false);
})();

// =============================================================================
// CHAPTER FORMATTING: SITE WIDTH
// =============================================================================

(() => {
  'use strict';

  const /** @const {HTMLInputElement} */ text = _$$$('reader-settings-site-width-text');
  const /** @const {HTMLInputElement} */ range = _$$$('reader-settings-site-width-range');
  const /** @const {HTMLElement} */ reset = _$$$('reader-settings-site-width-reset');
  const /** @const {Number} */ _default = FcnFormatting.defaults()['site-width'];

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
   * @param {Number} value - Float between 640 and 1920.
   * @param {Boolean} [save=true] - Optional. Whether to save the change.
   */

  function fcn_updateSiteWidth(value, save = true) {
    const formatting = FcnFormatting.get();

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
    formatting['site-width'] = value;

    if (save) {
      FcnFormatting.set(formatting);
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
  const formatting = FcnFormatting.get();
  fcn_updateSiteWidth(formatting['site-width'], false);
})();

// =============================================================================
// CHAPTER FORMATTING TOGGLES
// =============================================================================

/**
 * Update formatting and toggles on chapters.
 *
 * @since 5.9.4
 * @param {Any} value - The value that will be evaluated as boolean.
 * @param {String} selector - The selector of the toggle.
 * @param {String} setting - The name of the setting.
 * @param {Object} [args={}] - Optional arguments.
 */

function fcn_updateToggle(value, selector, setting, args = {}) {
  const formatting = FcnFormatting.get();

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
    FcnFormatting.eFormattingTarget.classList.toggle(args.toggleClass, args.invertClass ? !checked : checked);
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
  formatting[setting] = checked;

  if (args.save) {
    FcnFormatting.set(formatting);
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
  const formatting = FcnFormatting.get();
  fcn_updateToggle(formatting[setting], `#${toggle.id}`, setting, {...{ save: false }, ...args});
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
  const formatting = FcnFormatting.get();
  fcn_updateToggle(formatting[setting], `#${toggle.id}`, setting, {...{ save: false }, ...args});
});

// --- PARAGRAPH TOOLS -------------------------------------------------------

_$$('#reader-settings-paragraph-tools-toggle').forEach(toggle => {
  const setting = 'show-paragraph-tools';

  // Listen for click
  toggle.onclick = (e) => {
    fcn_updateToggle(e.currentTarget.checked, `#${toggle.id}`, setting);
  }

  // Initialize
  const formatting = FcnFormatting.get();
  fcn_updateToggle(formatting[setting], `#${toggle.id}`, setting, { save: false });
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
  const formatting = FcnFormatting.get();
  fcn_updateToggle(formatting[setting], `#${toggle.id}`, setting, {...{ save: false }, ...args});
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
  const formatting = FcnFormatting.get();
  fcn_updateToggle(formatting[setting], `#${toggle.id}`, setting, {...{ save: false }, ...args});
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
  const formatting = FcnFormatting.get();
  fcn_updateToggle(formatting[setting], `#${toggle.id}`, setting, {...{ save: false }, ...args});
});
