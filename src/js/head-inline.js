// To be minified and added to fictioneer_output_head_critical_scripts()

(function() {
  if (typeof localStorage !== 'undefined') {
    const lightMode = localStorage.getItem('fcnLightmode'), root = document.documentElement;
    let modifier, settings = localStorage.getItem('fcnSiteSettings');

    if (settings && (settings = JSON.parse(settings))) {
      if (null !== settings && 'object' == typeof settings) {
        Object.entries(settings).forEach(([key, value]) => {
          switch (key) {
            case 'minimal':
              root.classList.toggle('minimal', value);
              break;
            case 'darken':
              modifier = value >= 0 ? 1 + value ** 2 : 1 - value ** 2;
              root.style.setProperty('--darken', `(${modifier} + var(--lightness-offset))`); // Beware darken and lightness!
              break;
            case 'saturation':
            case 'font-lightness':
            case 'font-saturation':
              modifier = value >= 0 ? 1 + value ** 2 : 1 - value ** 2;
              root.style.setProperty(`--${key}`, `(${modifier} + var(--${key}-offset))`);
              break;
            case 'hue-rotate':
              modifier = Number.isInteger(settings['hue-rotate']) ? settings['hue-rotate'] : 0;
              root.style.setProperty('--hue-rotate', `(${modifier}deg + var(--hue-offset))`);
              break;
            default:
              root.classList.toggle(`no-${key}`, !value)
          }
        });

        root.dataset.fontWeight = settings.hasOwnProperty('font-weight') ? settings['font-weight'] : 'default';
        root.dataset.theme = settings.hasOwnProperty('site-theme') && ! root.dataset.forceChildTheme ? settings['site-theme'] : 'default';

        let themeColor = getComputedStyle(document.documentElement).getPropertyValue('--theme-color-base').trim().split(' ');

        const darken = settings['darken'] ? settings['darken'] : 0;
        const saturation = settings['saturation'] ? settings['saturation'] : 0;
        const hueRotate = settings['hue-rotate'] ? settings['hue-rotate'] : 0;
        const _d = darken >= 0 ? 1 + darken ** 2 : 1 - darken ** 2;

        settings = saturation >= 0 ? 1 + saturation ** 2 : 1 - saturation ** 2;

        themeColor = `hsl(${(parseInt(themeColor[0]) + hueRotate) % 360}deg ${(parseInt(themeColor[1]) * settings).toFixed(2)}% ${(parseInt(themeColor[2]) * _d).toFixed(2)}%)`;

        document.querySelector('meta[name=theme-color]').setAttribute('content', themeColor);
      }
    }

    if (lightMode) {
      root.dataset.mode = lightMode == 'true' ? 'light' : 'dark';
    }
  }
})();
