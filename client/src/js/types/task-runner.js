document.addEventListener('DOMContentLoaded', () => {
  // 1. Wait until the DOM is loaded before attempting to query the element
  const root = document.querySelector('#cms-content')

  // 2. IMPORTANT: Check if the element was actually found
  if (root) {
    new MutationObserver(mutations => {
      mutations.forEach(m => {
        // --- Fade-In Logic (Node Added) ---
        m.addedNodes.forEach(n => {
          if (
            n.nodeType === 1 &&
            n.classList.contains('cms-loading-container')
          ) {
            // Use requestAnimationFrame for a smoother, guaranteed DOM update cycle
            requestAnimationFrame(() => n.classList.add('fade-in'))
          }
        })

        // --- Fade-Out Logic (Class Attribute Changed) ---
        if (m.type === 'attributes' && m.attributeName === 'class') {
          const el = m.target

          // Your fade-out condition logic:
          // The element currently contains the 'cms-loading-container' class
          // AND it does NOT contain the 'fade-in' class (meaning it's being removed/cleared)
          // NOTE: Your original fade-out logic is unusual. A common fade-out
          // is triggered when the `cms-loading-container` class is REMOVED.
          // I will assume your logic intends to remove the element if it's
          // a loading container but is missing the 'fade-in' class.
          if (
            el.classList.contains('cms-loading-container') &&
            !el.classList.contains('fade-in')
          ) {
            // Start fade-out effect by removing the 'fade-in' class
            el.classList.remove('fade-in')

            // Wait 200ms (CSS transition time) then remove element from DOM
            setTimeout(() => el.remove(), 200)
          }
        }
      })
    }).observe(root, {
      // This line now executes with a valid 'root' Node
      childList: true,
      subtree: true,
      attributes: true,
      attributeFilter: ['class']
    })
  } else {
    // Helpful log for debugging if the element is missing from the HTML
    console.error(
      "MutationObserver failed to start: Element with ID '#cms-content' not found."
    )
  }
})
