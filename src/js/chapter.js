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

  /**
   * Stimulus Controller initialize lifecycle callback.
   *
   * @since 5.27.0
   */

  initialize() {
    if (this.progressBar && this.hasContentTarget && !this.hasPassword) {
      this.trackProgress();
      window.addEventListener('scroll.rAF', FcnUtils.throttle(this.trackProgress.bind(this), 1000 / 48));
    }
  }

  /**
   * Stimulus Controller connect lifecycle callback.
   *
   * @since 5.27.0
   */

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
   * @param {Event} detail - Event param.
   * @param {HTMLElement} detail.target -  The event target.
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
    const formatting = FcnFormatting.get();

    if (!formatting['show-paragraph-tools'] || !this.tools) {
      if (this.lastToolsParagraph) {
        this.closeTools();
      }

      return;
    }

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
      !target.closest('p')?.parentElement?.classList.contains('tools-wrapper') ||
      target.closest(interactiveSelector)
    ) {
      return false;
    }

    return true;
  }
});

// =============================================================================
// STIMULUS: FICTIONEER FORMATTING MODAL
// =============================================================================

application.register('fictioneer-chapter-formatting', class extends Stimulus.Controller {
  static get targets() {
    return ['fontSizeReset', 'fontSizeRange', 'fontSizeText', 'siteWidthReset', 'siteWidthRange', 'siteWidthText', 'fontSaturationReset', 'fontSaturationRange', 'fontSaturationText', 'letterSpacingReset', 'letterSpacingRange', 'letterSpacingText', 'lineHeightReset', 'lineHeightRange', 'lineHeightText', 'paragraphSpacingReset', 'paragraphSpacingRange', 'paragraphSpacingText'];
  }

  /**
   * Stimulus Controller connect lifecycle callback.
   *
   * @since 5.27.0
   */

  connect() {
    this.#updateSiteWidthControls();
    this.#updateFontSizeControls();
    this.#updateFontSaturationControls();
    this.#updateLetterSpacingControls();
    this.#updateLineHeightControls();
    this.#updateParagraphSpacingControls();
  }

  /**
   * Action to change the site width.
   *
   * @since 5.29.3
   * @param {Event} event - The event.
   * @param {HTMLElement} event.currentTarget - Trigger element.
   * @param {Object} event.params - Additional parameters.
   * @param {String} event.params.type - Action type.
   */

  siteWidth({ currentTarget, params: { type } }) {
    const value = type === 'reset' ? null : parseInt(currentTarget.value);

    FcnFormatting.updateSiteWidth(value);
    this.#updateSiteWidthControls(value);
  }

  /**
   * Action to change the font saturation.
   *
   * @since 5.30.0
   * @param {Event} event - The event.
   * @param {HTMLElement} event.currentTarget - Trigger element.
   * @param {Object} event.params - Additional parameters.
   * @param {String} event.params.type - Action type.
   */

  fontSaturation({ currentTarget, params: { type } }) {
    let value = null;

    switch (type) {
      case 'range':
        value = parseFloat(currentTarget.value);
        break;
      case 'text':
        value = parseInt(currentTarget.value) / 100;
        break;
    }

    FcnFormatting.updateFontSaturation(value);
    this.#updateFontSaturationControls(value);
  }

  /**
   * Action to change the font size.
   *
   * @since 5.29.3
   * @param {Event} event - The event.
   * @param {HTMLElement} event.currentTarget - Trigger element.
   * @param {Object} event.params - Additional parameters.
   * @param {String} event.params.type - Action type.
   */

  fontSize({ currentTarget, params: { type } }) {
    const formatting = FcnFormatting.get();
    let value = null;

    switch (type) {
      case 'adjust':
        value = parseFloat(formatting['font-size']) + parseFloat(currentTarget.dataset.modifier);
        break;
      case 'range':
      case 'text':
        value = currentTarget.value;
        break;
    }

    FcnFormatting.updateFontSize(value);
    this.#updateFontSizeControls(value);
  }

  /**
   * Action to change the letter spacing.
   *
   * @since 5.30.0
   * @param {Event} event - The event.
   * @param {HTMLElement} event.currentTarget - Trigger element.
   * @param {Object} event.params - Additional parameters.
   * @param {String} event.params.type - Action type.
   */

  letterSpacing({ currentTarget, params: { type } }) {
    let value = type === 'reset' ? null : parseFloat(currentTarget.value);

    FcnFormatting.updateLetterSpacing(value);
    this.#updateLetterSpacingControls(value);
  }

  /**
   * Action to change the line height.
   *
   * @since 5.30.0
   * @param {Event} event - The event.
   * @param {HTMLElement} event.currentTarget - Trigger element.
   * @param {Object} event.params - Additional parameters.
   * @param {String} event.params.type - Action type.
   */

  lineHeight({ currentTarget, params: { type } }) {
    let value = type === 'reset' ? null : parseFloat(currentTarget.value);

    FcnFormatting.updateLineHeight(value);
    this.#updateLineHeightControls(value);
  }

  /**
   * Action to change the paragraph spacing.
   *
   * @since 5.30.0
   * @param {Event} event - The event.
   * @param {HTMLElement} event.currentTarget - Trigger element.
   * @param {Object} event.params - Additional parameters.
   * @param {String} event.params.type - Action type.
   */

  paragraphSpacing({ currentTarget, params: { type } }) {
    let value = type === 'reset' ? null : parseFloat(currentTarget.value);

    FcnFormatting.updateParagraphSpacing(value);
    this.#updateParagraphSpacingControls(value);
  }

  // =====================
  // ====== PRIVATE ======
  // =====================

  /**
   * Update font size view controls.
   *
   * @since 5.29.3
   * @param {Any|null} [value=null] - The new value. Falls back to default if null.
   */

  #updateFontSizeControls(value = null) {
    this.#updateFormattingControls('font-size', value);
  }

  /**
   * Update font font saturation controls.
   *
   * @since 5.30.0
   * @param {Any|null} [value=null] - The new value. Falls back to default if null.
   */

  #updateFontSaturationControls(value = null) {
    this.#updateFormattingControls('font-saturation', value, v => parseInt(v * 100));
  }

  /**
   * Update site width view controls.
   *
   * @since 5.30.0
   * @param {Any|null} [value=null] - The new value. Falls back to default if null.
   */

  #updateSiteWidthControls(value = null) {
    this.#updateFormattingControls('site-width', value);
  }

  /**
   * Update letter spacing view controls.
   *
   * @since 5.30.0
   * @param {Any|null} [value=null] - The new value. Falls back to default if null.
   */

  #updateLetterSpacingControls(value = null) {
    this.#updateFormattingControls('letter-spacing', value);
  }

  /**
   * Update line height view controls.
   *
   * @since 5.30.0
   * @param {Any|null} [value=null] - The new value. Falls back to default if null.
   */

  #updateLineHeightControls(value = null) {
    this.#updateFormattingControls('line-height', value);
  }

  /**
   * Update paragraph spacing view controls.
   *
   * @since 5.30.0
   * @param {Any|null} [value=null] - The new value. Falls back to default if null.
   */

  #updateParagraphSpacingControls(value = null) {
    this.#updateFormattingControls('paragraph-spacing', value);
  }

  /**
   * Generic updater for formatting controls.
   *
   * @since 5.30.0
   * @param {String} key - The formatting key.
   * @param {Any|null} value - The new value. Falls back to default if null.
   * @param {Function|null} transform - Optional function to transform value for text targets.
   */
  #updateFormattingControls(key, value = null, transform = null) {
    value = value ?? FcnFormatting.get()[key];

    const camelKey = key.replace(/-([a-z])/g, (_, c) => c.toUpperCase());

    if (this[`has${camelKey.charAt(0).toUpperCase() + camelKey.slice(1)}RangeTarget`])
      this[`${camelKey}RangeTargets`].forEach(e => e.value = value);

    if (this[`has${camelKey.charAt(0).toUpperCase() + camelKey.slice(1)}TextTarget`])
      this[`${camelKey}TextTargets`].forEach(e => e.value = transform ? transform(value) : value);

    if (this[`has${camelKey.charAt(0).toUpperCase() + camelKey.slice(1)}ResetTarget`])
      this[`${camelKey}ResetTargets`].forEach(e =>
        e.classList.toggle('_modified', value != FcnFormatting.defaults()[key])
      );
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
  },

  /**
   * Update font size formatting on chapters.
   *
   * @since 4.0.0
   * @since 5.29.3 - Moved function into FcnFormatting.
   * @param {Number} value - Integer between 50 and 200.
   * @param {Boolean} [save=true] - Optional. Whether to save the change.
   */

  updateFontSize(value, save = true) {
    const formatting = this.get();

    // Evaluate
    value = FcnUtils.clamp(50, 200, value ?? FcnFormatting.defaults()['font-size']);

    // Update inline style
    _$$('.resize-font').forEach(element => {
      element.style.fontSize = `${value}%`;
    });

    // Update local storage
    formatting['font-size'] = value;

    if (save) {
      this.set(formatting);
    }
  },

  /**
   * Update site width formatting on chapters.
   *
   * @description Only affects the width of the main container since 5.0.10
   * to avoid potential layout issues.
   *
   * @since 4.0.0
   * @since 5.29.3 - Moved function into FcnFormatting.
   * @param {Number} value - Float between 640 and 1920.
   * @param {Boolean} [save=true] - Optional. Whether to save the change.
   */

  updateSiteWidth(value, save = true) {
    const formatting = FcnFormatting.get();
    const main = _$('main');

    // Evaluate
    value = FcnUtils.clamp(640, 1920, value ?? FcnFormatting.defaults()['site-width']);

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
  },

  /**
   * Update font saturation formatting on chapters.
   *
   * @since 4.0.0
   * @since 5.29.4 - Moved function into FcnFormatting.
   * @param {Number} value - Float between -1 and 1.
   * @param {Boolean} [save=true] - Optional. Whether to save the change.
   */

  updateFontSaturation(value, save = true) {
    const formatting = FcnFormatting.get();

    // Evaluate
    value = FcnUtils.clamp(-1, 1, value ?? FcnFormatting.defaults()['font-saturation']);

    // Update font saturation property (squared for smooth progression)
    this.eFormattingTarget.style.setProperty(
      '--font-saturation',
      `(${value >= 0 ? 1 + value ** 2 : 1 - value ** 2} + var(--font-saturation-offset))`
    );

    // Update local storage
    formatting['font-saturation'] = value;

    if (save) {
      FcnFormatting.set(formatting);
    }
  },

  /**
   * Update letter-spacing formatting on chapters.
   *
   * @since 4.0.0
   * @since 5.30.0 - Moved function into FcnFormatting.
   * @param {Number} value - Float between -0.1 and 0.2.
   * @param {Boolean} [save=true] - Optional. Whether to save the change.
   */

  updateLetterSpacing(value, save = true) {
    const formatting = FcnFormatting.get();

    // Evaluate
    value = FcnUtils.clamp(-0.1, 0.2, value ?? FcnFormatting.defaults()['letter-spacing']);

    // Update inline style
    this.eFormattingTarget.style.letterSpacing = `calc(${value}em + var(--font-letter-spacing-base))`;

    // Update local storage
    formatting['letter-spacing'] = value;

    if (save) {
      FcnFormatting.set(formatting);
    }
  },

  /**
   * Update line height formatting on chapters.
   *
   * @since 4.0.0
   * @since 5.30.0 - Moved function into FcnFormatting.
   * @param {Number} value - Float between 0.8 and 3.
   * @param {Boolean} [save=true] - Optional. Whether to save the change.
   */

  updateLineHeight(value, save = true) {
    const formatting = FcnFormatting.get();

    // Evaluate
    value = FcnUtils.clamp(0.8, 3.0, value ?? FcnFormatting.defaults()['line-height']);

    // Update inline style
    this.eFormattingTarget.style.lineHeight = `${value}`;

    // Update local storage
    formatting['line-height'] = value;

    if (save) {
      FcnFormatting.set(formatting);
    }
  },

  /**
   * Update paragraph spacing formatting on chapters.
   *
   * @since 4.0.0
   * @since 5.30.0 - Moved function into FcnFormatting.
   * @param {Number} value - Float between 0 and 3.
   * @param {Boolean} [save=true] - Optional. Whether to save the change.
   */

  updateParagraphSpacing(value, save = true) {
    const formatting = FcnFormatting.get();

    // Evaluate
    value = FcnUtils.clamp(0, 3, value ?? FcnFormatting.defaults()['paragraph-spacing']);

    // Update paragraph-spacing property
    this.eFormattingTarget.style.setProperty('--paragraph-spacing', `${value}em`);

    // Update local storage
    formatting['paragraph-spacing'] = value;

    if (save) {
      FcnFormatting.set(formatting);
    }
  }
};

// Initialize on load
(() => {
  if (!FcnFormatting.eFormattingTarget) {
    return;
  }

  const formatting = FcnFormatting.get();

  FcnFormatting.updateSiteWidth(formatting['site-width'], false);
  FcnFormatting.updateFontSize(formatting['font-size'], false);
  FcnFormatting.updateFontSaturation(formatting['font-saturation'], false);
  FcnFormatting.updateLetterSpacing(formatting['letter-spacing'], false);
  FcnFormatting.updateLineHeight(formatting['line-height'], false);
  FcnFormatting.updateParagraphSpacing(formatting['paragraph-spacing'], false);
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
