// To be minified and added to fictioneer_output_head_critical_scripts()

(function() {
  if (typeof localStorage !== 'undefined') {
    const lightMode = localStorage.getItem('fcnLightmode'), root = document.documentElement;
    let temp, modifier, settings = localStorage.getItem('fcnSiteSettings');

    if (settings && (settings = JSON.parse(settings))) {
      if (null !== settings && 'object' == typeof settings) {
        Object.entries(settings).forEach(entry => {
          switch (entry[0]) {
            case 'minimal':
              root.classList.toggle('minimal', entry[1]);
              break;
            case 'darken':
              temp = settings['darken'];
              modifier = temp >= 0 ? 1 + Math.pow(temp, 2) : 1 - Math.pow(temp, 2);
              root.style.setProperty('--darken', modifier)
              break;
            case 'saturation':
              temp = settings['saturation'];
              modifier = temp >= 0 ? 1 + Math.pow(temp, 2) : 1 - Math.pow(temp, 2);
              root.style.setProperty('--saturation', modifier)
              break;
            case 'font-saturation':
              temp = settings['font-saturation'];
              modifier = temp >= 0 ? 1 + Math.pow(temp, 2) : 1 - Math.pow(temp, 2);
              root.style.setProperty('--font-saturation', modifier)
              break;
            case 'hue-rotate':
              modifier = Number.isInteger(settings['hue-rotate']) ? settings['hue-rotate'] : 0;
              root.style.setProperty('--hue-rotate', `${modifier}deg`);
              break;
            default:
              root.classList.toggle(`no-${entry[0]}`, !entry[1])
          }
        });

        root.dataset.fontWeight = settings.hasOwnProperty('font-weight') ? settings['font-weight'] : 'default';
        root.dataset.theme = settings.hasOwnProperty('site-theme') && ! root.dataset.forceChildTheme ? settings['site-theme'] : 'default';

        let themeColor = getComputedStyle(document.documentElement).getPropertyValue('--theme-color-base').trim().split(' '),
            darken = settings['darken'] ? settings['darken'] : 0,
            saturation = settings['saturation'] ? settings['saturation'] : 0,
            hueRotate = settings['hue-rotate'] ? settings['hue-rotate'] : 0,
            _d = darken >= 0 ? 1 + Math.pow(darken, 2) : 1 - Math.pow(darken, 2);
            settings = saturation >= 0 ? 1 + Math.pow(saturation, 2) : 1 - Math.pow(saturation, 2);

        themeColor = `hsl(${(parseInt(themeColor[0]) + hueRotate) % 360}deg ${(parseInt(themeColor[1]) * settings).toFixed(2)}% ${(parseInt(themeColor[2]) * _d).toFixed(2)}%)`;

        document.querySelector('meta[name=theme-color]').setAttribute('content', themeColor);
      }
    }

    if (lightMode) {
      root.dataset.mode = lightMode == 'true' ? 'light' : '';
    }
  }
})();
