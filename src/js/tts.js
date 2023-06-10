// =============================================================================
// SETUP
// =============================================================================

const /** @const {HTMLElement} */ fcn_ttsInterface = _$$$('tts-interface'),
      /** @const {String[]} */ fcn_ttsAllowedTags = ['P', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6'];

var /** @type {HTMLElement[]} */ fcn_ttsStack = [],
    /** @type {Number} */ fcn_currentReadingId = -1,
    /** @type {Number} */ fcn_ttsPauseTimestamp = -1,
    /** @type {String} */ fcn_ttsCurrentText = '',
    /** @type {Object} */ fcn_ttsSettings = fcn_getTTSsettings(),
    /** @type {SpeechSynthesisVoice[]} */ fcn_voices = [],
    /** @type {SpeechSynthesisUtterance} */ fcn_utter,
    /** @type {SpeechSynthesis} */ fcn_synth;

// =============================================================================
// INITIALIZE TTS
// =============================================================================

// Check whether the feature is supported...
if (typeof speechSynthesis !== 'undefined') {
  fcn_synth = window.speechSynthesis;
  fcn_utter = new SpeechSynthesisUtterance();
  fcn_utter.lang = fcn_theRoot.lang;

  // Listen to onvoiceschanged event once when voices are initially loaded
  if ('onvoiceschanged' in speechSynthesis) {
    fcn_synth.addEventListener('voiceschanged', () => {
      fcn_setUpVoices();
      fcn_updateVolume(fcn_ttsSettings['volume']);
      fcn_updatePitch(fcn_ttsSettings['pitch']);
      fcn_updateRate(fcn_ttsSettings['rate']);
    }, { once: true });
  } else {
    // Some browsers (e.g. Safari as of 2022/09/09) do not support the event,
    // so we just wait for two seconds and hope for the best!
    setTimeout(() => {
      fcn_setUpVoices();
      fcn_updateVolume(fcn_ttsSettings['volume']);
      fcn_updatePitch(fcn_ttsSettings['pitch']);
      fcn_updateRate(fcn_ttsSettings['rate']);
    }, 2000);
  }
}

// =============================================================================
// GET/SET LOCAL TTS SETTINGS
// =============================================================================

/**
 * Save TTS settings to global variable and local storage.
 *
 * @since 3.0
 * @param {Object} value - The settings to be saved.
 */

function fcn_setTTSsettings(value) {
  fcn_ttsSettings = value;
  localStorage.setItem('ttsSettings', JSON.stringify(value));
}

/**
 * Get TTS settings from local storage.
 *
 * @description Looks for text-to-speech setting in local storage. If none are
 * found, an empty settings JSON is created and returned.
 *
 * @since 3.0
 * @see fcn_isValidJSONString()
 * @see fcn_setTTSsettings()
 */

function fcn_getTTSsettings() {
  // Get JSON string from local storage
  let s = localStorage.getItem('ttsSettings');

  // Parse JSON string if valid, otherwise create new
  s = (s && fcn_isValidJSONString(s)) ? JSON.parse(s) : {};

  // Set up locals
  fcn_setTTSsettings(s);
  return s;
}

// =============================================================================
// SET UP TTS VOICES
// =============================================================================

/**
 * Load voices depending on language and set up voice selection.
 *
 * @since 3.0
 */

function fcn_setUpVoices() {
  let systemVoices = fcn_synth.getVoices(),
      index = 0,
      select = _$$$('tts-voice-select'),
      languages = [fcn_theRoot.lang];

  if (!select) return;

  for (let i = 0; i < systemVoices.length; i++) {
    let voice = systemVoices[i];

    // Filter voices by set language
    if (fcn_theRoot.lang == 'en-US') languages.push('en-GB');
    if (fcn_theRoot.lang == 'en-GB') languages.push('en-US');
    if (!languages.includes(voice.lang)) continue;

    // Add voice to selection
    fcn_voices.push(voice);
    let option = document.createElement('option');
    option.value = index;
    option.innerHTML = voice.name;
    select.appendChild(option);

    index++;
  }

  // Remove button if no voices have been found
  if (fcn_voices.length < 1) {
    _$('.tts-interface__voice-selection').remove();
    return;
  }

  // Listen for voice select
  select.addEventListener('change', (e) => {
    fcn_updateVoice(e.currentTarget.value);
  });

  // Voice on load
  fcn_updateVoice(fcn_ttsSettings['voice']);
}

// =============================================================================
// UPDATE TTS VOICES
// =============================================================================

/**
 * Update selected voice.
 *
 * @since 3.0
 * @param {Number} id - Array index of the voice.
 */

function fcn_updateVoice(id) {
  id = isNaN(id) ? 0 : id;

  let selection = fcn_voices[id];
  if (selection === undefined) selection = fcn_voices[0];

  fcn_utter.voice = selection;

  _$$$('tts-voice-select').value = id;
  _$$$('selected-voice-label').innerHTML = `Selected Voice: ${selection.name} (${selection.lang})`;

  fcn_ttsSettings['voice'] = id;
  fcn_setTTSsettings(fcn_ttsSettings);
}

// =============================================================================
// UPDATE VOICE SETTINGS
// =============================================================================

/**
 * Update voice volume.
 *
 * @since 3.0
 * @param {Number} value - The new volume.
 */

function fcn_updateVolume(value) {
  value = isNaN(value) ? 100 : parseInt(value);
  value = fcn_clamp(0, 100, value);

  _$$$('tts-volume-range').value = value;
  _$$$('tts-volume-text').value = value;

  fcn_ttsSettings['volume'] = value;
  fcn_setTTSsettings(fcn_ttsSettings);

  fcn_utter.volume = value / 100;
}

/**
 * Update voice pitch.
 *
 * @since 3.0
 * @param {Number} value - The new pitch.
 */

function fcn_updatePitch(value) {
  value = isNaN(value) ? 1.0 : parseFloat(value);
  value = fcn_clamp(0.2, 1.8, value);

  _$$$('tts-pitch-range').value = value;
  _$$$('tts-pitch-text').value = value;

  fcn_ttsSettings['pitch'] = value;
  fcn_setTTSsettings(fcn_ttsSettings);

  fcn_utter.pitch = value;
}

/**
 * Update voice rate.
 *
 * @since 3.0
 * @param {Number} value - The new rate.
 */

function fcn_updateRate(value) {
  value = isNaN(value) ? 1.0 : parseFloat(value);
  value = fcn_clamp(0.2, 1.8, value);

  _$$$('tts-rate-range').value = value;
  _$$$('tts-rate-text').value = value;

  fcn_ttsSettings['rate'] = value;
  fcn_setTTSsettings(fcn_ttsSettings);

  fcn_utter.rate = value;
}

// =============================================================================
// READ TEXT STACK
// =============================================================================

/**
 * Read next item in text stack until it is empty.
 *
 * @since 3.0
 */

function fcn_readTextStack() {
  let current = _$('.current-reading');

  // End of stack reached
  if (fcn_ttsStack.length === 0) {
    fcn_ttsInterface.classList.add('ended');
    if (current) current.classList.remove('current-reading');
    fcn_currentReadingId = -1;
    fcn_ttsCurrentText = '';
    return;
  }

  // Get next item from stack
  let item = fcn_ttsStack.shift();

  fcn_ttsCurrentText = item[1];

  if (fcn_currentReadingId != item[0]) {
    fcn_currentReadingId = item[0];
    if (current) current.classList.remove('current-reading');
    _$$$(fcn_currentReadingId).classList.add('current-reading');
  }

  fcn_utter.text = fcn_ttsCurrentText;
  fcn_utter.addEventListener('end', fcn_readTextStack, {once: true});
  fcn_synth.speak(fcn_utter);
}

// =============================================================================
// EVENT LISTENERS
// =============================================================================

if (typeof speechSynthesis !== 'undefined') {
  // Paragraph tools button
  _$$$('button-tts-set').addEventListener('click', (e) => {
    fcn_ttsStack = [];
    fcn_currentReadingId = -1;

    // Hide sensitive content?
    let hideSensitive = _$('.chapter-formatting')?.classList.contains('hide-sensitive') ?? false,
        sensitiveClass = hideSensitive ? 'sensitive-content' : 'sensitive-alternative',
        playButton = _$$$('button-tts-play');

    // Cancel ongoing reading if any
    if (fcn_synth.speaking) fcn_utter.removeEventListener('end', fcn_readTextStack);
    fcn_synth.cancel();

    // Compile reading stack
    let node = e.target.closest('p[data-paragraph-id]');

    fcn_ttsStack.push(node);

    while (node = node.nextSibling) {
      if (
        node.nodeType == 1 &&
        fcn_ttsAllowedTags.includes(node.tagName) &&
        !node.classList.contains('skip-tts') &&
        !node.classList.contains('inside-epub') &&
        !node.classList.contains(sensitiveClass)
      ) {
        fcn_ttsStack.push(node);
      }
    }

    // Prepare items to read
    fcn_ttsStack = fcn_ttsStack.map(node => {
      let result = [],
          inner = node.querySelector('.paragraph-inner'),
          sentences = inner ? inner.innerText : node.innerText; // Check if wrapped

      // Split text into array of sentences (regex is... acceptable)
      sentences = sentences.replace(/(\.+|\:|\!|\?)(\"*|\'*|\)*|}*|]*)(\s|\n|\r|\r\n)/gm, "$1$2|").split("|");

      sentences.forEach(s => {
        s = s.trim();
        if (s) result.push([node.id, s]);
      });

      return result;
    }).flat();

    // Start reading stack
    fcn_readTextStack();

    // Show interface
    fcn_theBody.classList.add('tts-open');
    fcn_ttsInterface.classList.remove('hidden', 'ended', 'paused');
    fcn_ttsInterface.classList.add('playing');
    playButton.focus();
    playButton.blur();
  });

  // Stop button
  _$$$('button-tts-stop')?.addEventListener('click', (e) => {
    let current = _$('.current-reading');

    fcn_ttsInterface.classList.add('hidden', 'ended');
    fcn_ttsInterface.classList.remove('playing', 'paused');
    fcn_theBody.classList.remove('tts-open');
    if (current) current.classList.remove('current-reading');
    fcn_ttsStack = [];
    fcn_currentReadingId = -1;
    fcn_utter.removeEventListener('end', fcn_readTextStack);
    fcn_synth.cancel();
  });

  // Play button
  _$$$('button-tts-play')?.addEventListener('click', (e) => {
    fcn_synth.resume();

    // Fix for frozen reading paused mid-speaking
    if (fcn_ttsPauseTimestamp !== -1 && Date.now() - fcn_ttsPauseTimestamp > 10000 ) {
      // Put back the previous item to be read again
      fcn_ttsStack.unshift([fcn_currentReadingId, fcn_ttsCurrentText]);
      fcn_synth.cancel();
      fcn_readTextStack();
      fcn_ttsPauseTimestamp = -1;
    }

    fcn_ttsInterface.classList.add('playing');
    fcn_ttsInterface.classList.remove('paused');
  });

  // Pause button
  _$$$('button-tts-pause')?.addEventListener('click', (e) => {
    fcn_synth.pause();
    fcn_ttsPauseTimestamp = Date.now();
    fcn_ttsInterface.classList.remove('playing');
    fcn_ttsInterface.classList.add('paused');
  });

  // Skip button
  _$$$('button-tts-skip')?.addEventListener('click', (e) => {
    fcn_utter.removeEventListener('end', fcn_readTextStack);
    fcn_synth.cancel();
    fcn_readTextStack();
    fcn_ttsInterface.classList.remove('paused');
    fcn_ttsInterface.classList.add('playing');
  });

  _$$$('button-tts-scroll')?.addEventListener(
    'click',
    e => {
      let target = _$(`p[id="${fcn_currentReadingId}"]`),
          position = target.getBoundingClientRect().top,
          offset = position + window.pageYOffset - 128;

      window.scrollTo({ top: offset, behavior: 'smooth' });
    }
  );

  // Volume range input
  _$$$('tts-volume-range')?.addEventListener('input', (e) => {
    fcn_updateVolume(e.target.value);
  });

  // Volume text input
  _$$$('tts-volume-text')?.addEventListener('input', (e) => {
    fcn_updateVolume(e.target.value);
  });

  // Pitch range input
  _$$$('tts-pitch-range')?.addEventListener('input', (e) => {
    fcn_updatePitch(e.target.value);
  });

  // Pitch text input
  _$$$('tts-pitch-text')?.addEventListener('input', (e) => {
    fcn_updatePitch(e.target.value);
  });

  // Rate range input
  _$$$('tts-rate-range')?.addEventListener('input', (e) => {
    fcn_updateRate(e.target.value);
  });

  // Rate text input
  _$$$('tts-rate-text')?.addEventListener('input', (e) => {
    fcn_updateRate(e.target.value);
  });

  _$$$('tts-settings-toggle')?.addEventListener('change', event => {
    event.currentTarget.closest('#tts-interface').dataset.showSettings = event.currentTarget.checked;
  });
}
