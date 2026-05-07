// --- Configuration & Storage Helpers ---
const EXPIRY_MS = 10 * 60 * 1000 // 10 minutes

// Generates a unique key based on the current URL path
const getStorageKey = () => `active_ui_tab_${window.location.pathname}`

const saveTabToStorage = hash => {
  const key = getStorageKey()
  localStorage.setItem(
    key,
    JSON.stringify({ hash, expiry: Date.now() + EXPIRY_MS })
  )
}

const getTabFromStorage = () => {
  const key = getStorageKey()
  try {
    const storedData = localStorage.getItem(key)
    if (!storedData) return null

    const { hash, expiry } = JSON.parse(storedData)
    if (Date.now() > expiry) {
      localStorage.removeItem(key)
      return null
    }
    return hash
  } catch (e) {
    return null
  }
}

// --- Core Tab Application Logic ---
// We extract this so it can be run on Initial Load, Back Button, AND Ajax Swaps
const applySavedTab = () => {
  const activeHash = window.location.hash || getTabFromStorage()

  if (activeHash) {
    const inputString = activeHash.replace('#', '')
    const targetElement = document.querySelector(
      `[aria-controls="${inputString}"]`
    )

    // Bail if tab doesn't exist yet, or if we've already applied it (prevents infinite loops)
    if (!targetElement || targetElement.dataset.tabApplied === 'true') return

    const runClick = el => {
      // Intelligently find the 'a' and 'li' regardless of which one 'el' is
      const ahref =
        el.tagName.toLowerCase() === 'a' ? el : el.querySelector('a')
      const liNode = el.tagName.toLowerCase() === 'li' ? el : el.closest('li')

      const clickEvent = new Event('click', { bubbles: true })

      // Trigger click and add class to the anchor
      if (ahref) {
        ahref.dispatchEvent(clickEvent)
        ahref.classList.add('active')
      }

      // If 'el' is the li, dispatch the click on it as well (preserving your original logic)
      if (el !== ahref) {
        el.dispatchEvent(clickEvent)
      }

      // Add the active class to the list item
      if (liNode) {
        liNode.classList.add('ui-state-active')
      }
    }

    const findClicker = selector => {
      const initialElement = document.querySelector(
        `[aria-controls="${selector}"]`
      )
      if (initialElement) {
        const tabPanel = initialElement.parentElement.closest('.ui-tabs-panel')
        if (tabPanel && tabPanel.id) {
          findClicker(tabPanel.id)
        }
        runClick(initialElement)
      }
    }

    const parts = inputString.split(/_(?=[A-Z])/)
    const lastPart = parts.pop()
    const ariaControlsValue = parts.length
      ? `${parts.join('_')}_${lastPart}`
      : lastPart

    findClicker(ariaControlsValue)

    // Mark as applied so the MutationObserver doesn't trigger this continuously
    targetElement.dataset.tabApplied = 'true'

    // Update URL correctly
    if (!window.location.hash) {
      const newUrl =
        window.location.pathname + window.location.search + activeHash
      history.replaceState(null, '', newUrl)
    }
  }
}

// --- Event Listeners ---

// 1. Initial Page Load
window.addEventListener('load', () => setTimeout(applySavedTab, 500))

// 2. Browser Back/Forward Navigation (History API)
window.addEventListener('popstate', () => setTimeout(applySavedTab, 100))

// 3. AJAX Content Swaps (MutationObserver)
// This watches the DOM for injected elements and tries to apply the tab logic
const observer = new MutationObserver(() => {
  // We use a tiny timeout to let Silverstripe finish rendering the UI tabs
  setTimeout(applySavedTab, 100)
})

// Watch the body for changes. (Can be optimized by replacing document.body with the specific wrapper element)
observer.observe(document.body, { childList: true, subtree: true })

// 4. Click Listener (Event Delegation)
document.addEventListener('click', event => {
  const anchor = event.target.closest('a.ui-tabs-anchor')

  if (anchor) {
    const href = anchor.getAttribute('href')
    if (href && href.includes('#')) {
      const hash = href.substring(href.indexOf('#'))

      saveTabToStorage(hash)
      const newUrl = window.location.pathname + window.location.search + hash
      history.replaceState(null, '', newUrl)
    }
  }
})
