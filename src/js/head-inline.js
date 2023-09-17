// To be minified and added to fictioneer_output_head_critical_scripts()

let _s = localStorage.getItem('fcnSiteSettings'),
    _r = document.documentElement,
    _t, _m;

if (_s && (_s = JSON.parse(_s))) {
  if (null !== _s && 'object' == typeof _s) {
    Object.entries(_s).forEach(_e => {
      switch (_e[0]) {
        case 'minimal':
          _r.classList.toggle('minimal', _e[1]);
          break;
        case 'darken':
          _t = _s['darken'];
          _m = _t >= 0 ? 1 + Math.pow(_t, 2) : 1 - Math.pow(_t, 2);
          _r.style.setProperty('--darken', _m)
          break;
        case 'saturation':
          _t = _s['saturation'];
          _m = _t >= 0 ? 1 + Math.pow(_t, 2) : 1 - Math.pow(_t, 2);
          _r.style.setProperty('--saturation', _m)
          break;
        case 'font-saturation':
          _t = _s['font-saturation'];
          _m = _t >= 0 ? 1 + Math.pow(_t, 2) : 1 - Math.pow(_t, 2);
          _r.style.setProperty('--font-saturation', _m)
          break;
        case 'hue-rotate':
          _m = Number.isInteger(_s['hue-rotate']) ? _s['hue-rotate'] : 0;
          _r.style.setProperty('--hue-rotate', `${_m}deg`);
          break;
        default:
          _r.classList.toggle(`no-${_e[0]}`, !_e[1])
      }
    });

    _r.dataset.fontWeight = _s.hasOwnProperty('font-weight') ? _s['font-weight'] : 'default';
    _r.dataset.theme = _s.hasOwnProperty('site-theme') && ! _r.dataset.forceChildTheme ? _s['site-theme'] : 'default';

    let _tc = getComputedStyle(document.documentElement).getPropertyValue('--theme-color-base').trim().split(' '),
        darken = _s['darken'] ? _s['darken'] : 0,
        saturation = _s['saturation'] ? _s['saturation'] : 0,
        hueRotate = _s['hue-rotate'] ? _s['hue-rotate'] : 0,
        _d = darken >= 0 ? 1 + Math.pow(darken, 2) : 1 - Math.pow(darken, 2);
        _s = saturation >= 0 ? 1 + Math.pow(saturation, 2) : 1 - Math.pow(saturation, 2);

    _tc = `hsl(${(parseInt(_tc[0]) + hueRotate) % 360}deg ${(parseInt(_tc[1]) * _s).toFixed(2)}% ${(parseInt(_tc[2]) * _d).toFixed(2)}%)`;

    document.querySelector('meta[name=theme-color]').setAttribute('content', _tc);
  }
}

if (localStorage.getItem('fcnLightmode')) {
  _r.dataset.mode = localStorage.getItem('fcnLightmode') == 'true' ? 'light' : '';
}
