function abrir(link) {
  // Fixes dual-screen position                             Most browsers      Firefox
    const dualScreenLeft = window.screenLeft !==  undefined ? window.screenLeft : window.screenX;
    const dualScreenTop = window.screenTop !==  undefined   ? window.screenTop  : window.screenY;

    const width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    const height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    const systemZoom = width / window.screen.availWidth;
    const left = (width - 1100) / 2 / systemZoom + dualScreenLeft
    const top = (height - 600) / 2 / systemZoom + dualScreenTop
    const newWindow = window.open(link, 'Descripción',
      `
      scrollbars=yes,
      width=${1150 / systemZoom},
      height=${600 / systemZoom},
      top=${top},
      left=${left}
      `
    );

    if (window.focus) newWindow.focus();
}
