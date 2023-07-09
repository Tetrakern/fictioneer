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

  // Some browsers (e.g. Safari as of 2022/09/09) do not support the  voiceschanged event,
  // so we just wait for two seconds and hope for the best!
  const fcn_ttsSetupTimeout = setTimeout(() => {
    fcn_setupTTS();
  }, 2000);

  // If they support the event, we clear the timeout
  if ('onvoiceschanged' in speechSynthesis) {
    fcn_synth.addEventListener('voiceschanged', () => {
      fcn_setupTTS();
      clearTimeout(fcn_ttsSetupTimeout);
    }, { once: true });
  }
}

/**
 * Initial setup of TTS.
 *
 * @since 5.4.6
 */

function fcn_setupTTS() {
  if (fcn_voices.length > 0) {
    return;
  }

  fcn_setUpVoices();
  fcn_updateVolume(fcn_ttsSettings['volume']);
  fcn_updatePitch(fcn_ttsSettings['pitch']);
  fcn_updateRate(fcn_ttsSettings['rate']);
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
  const systemVoices = fcn_synth.getVoices(),
        select = _$$$('tts-voice-select');

  if (!select) return;

  let index = 0;

  for (let i = 0; i < systemVoices.length; i++) {
    const voice = systemVoices[i];

    // Add voice to selection
    fcn_voices.push(voice);

    const option = document.createElement('option');

    option.value = index;
    option.innerHTML = `${voice.name} (${voice.lang})`;
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
  // Ensure Samantha (en-US) is the default/fallback voice
  if (isNaN(id) || fcn_voices[id] === undefined) {
    let index = fcn_voices.findIndex(voice => {
      return voice.name === 'Samantha' && (voice.lang === 'en-US' || voice.lang === 'en_US');
    });

    // Samantha not found, use first English voice found instead
    if (index < 0) {
      index = fcn_voices.findIndex(voice => {
        return voice.lang === 'en-US' || voice.lang === 'en_US';
      });
    }

    // Use found voice or surrender and use the first horrible thing in the array
    id = index > -1 ? index : 0;
  }

  fcn_utter.voice = fcn_voices[id];

  _$$$('tts-voice-select').value = id;

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
  _$$$('tts-volume-reset').classList.toggle('_modified', value != 100);

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
  _$$$('tts-pitch-reset').classList.toggle('_modified', value != 1.0);

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
  _$$$('tts-rate-reset').classList.toggle('_modified', value != 1.0);

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
  const current = _$('.current-reading');

  // End of stack reached
  if (fcn_ttsStack.length === 0) {
    fcn_ttsInterface.classList.add('ended');
    if (current) current.classList.remove('current-reading');
    fcn_currentReadingId = -1;
    fcn_ttsCurrentText = '';
    return;
  }

  // Get next item from stack
  const item = fcn_ttsStack.shift();

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
    const hideSensitive = _$('.chapter-formatting')?.classList.contains('hide-sensitive') ?? false,
          sensitiveClass = hideSensitive ? 'sensitive-content' : 'sensitive-alternative',
          playButton = _$$$('button-tts-play'),
          regex = new RegExp(fcn_ttsInterface.dataset.regex, 'gm');

    // Cancel ongoing reading if any
    if (fcn_synth.speaking) fcn_utter.removeEventListener('end', fcn_readTextStack);
    fcn_synth.cancel();

    // Compile reading stack
    let node = e.target.closest('p[data-paragraph-id]');

    fcn_ttsStack.push(node);

    while (node = node.nextElementSibling) {
      if (
        fcn_ttsAllowedTags.includes(node.tagName) &&
        !node.classList.contains('skip-tts') &&
        !node.classList.contains('inside-epub') &&
        !node.classList.contains(sensitiveClass)
      ) {
        fcn_ttsStack.push(node);
      }
    }

    // Prepare items to read
    fcn_ttsStack = fcn_ttsStack.flatMap(node => {
      const result = [],
            inner = node.querySelector('.paragraph-inner'),
            text = inner ? inner.textContent : node.textContent;

      // Split text into array of sentences using a regex pattern
      const sentences = text.replace(regex, '$1|').split('|');

      sentences.forEach(sentence => {
        const trimmedSentence = sentence.trim();

        if (trimmedSentence.length > 0) {
          result.push([node.id, trimmedSentence]);
        }
      });

      return result;
    });

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
    const current = _$('.current-reading');

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
    () => {
      fcn_scrollTo(_$(`p[id="${fcn_currentReadingId}"]`), 128)
    }
  );

  // Volume inputs
  _$$('#tts-volume-range, #tts-volume-text').forEach(element => {
    element.addEventListener('input', (e) => {
      fcn_updateVolume(e.target.value);
    });
  });

  // Volume reset
  _$$$('tts-volume-reset')?.addEventListener('click', () => { fcn_updateVolume(100) });

  // Pitch inputs
  _$$('#tts-pitch-range, #tts-pitch-text').forEach(element => {
    element.addEventListener('input', (e) => {
      fcn_updatePitch(e.target.value);
    });
  });

  // Pitch reset
  _$$$('tts-pitch-reset')?.addEventListener('click', () => { fcn_updatePitch(1.0) });

  // Rate inputs
  _$$('#tts-rate-range, #tts-rate-text').forEach(element => {
    element.addEventListener('input', (e) => {
      fcn_updateRate(e.target.value);
    });
  });

  // Pitch reset
  _$$$('tts-rate-reset')?.addEventListener('click', () => { fcn_updateRate(1.0) });

  // Terminate TTS on any chapter page reload (otherwise, it will keep running in the background)
  window.addEventListener('beforeunload', () => {
    fcn_synth.cancel();
  });
}
