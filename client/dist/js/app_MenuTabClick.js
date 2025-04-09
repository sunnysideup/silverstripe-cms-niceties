window.addEventListener('load', function () {
  setTimeout(() => {
    if (window.location.hash) {
      const els = document.querySelectorAll(
        '[aria-controls="' + window.location.hash.substring(1) + '"]'
      )
      if (els.length) {
        el = els[0]
        if ('#' + el.getAttribute('aria-controls') === window.location.hash) {
          el.firstChild.dispatchEvent(new Event('click'))
          el.firstChild.style.fontWeight = 'bold'
          el.firstChild.style.color = '#0071c4'
        }
      }
    }
  }, 500)
})
