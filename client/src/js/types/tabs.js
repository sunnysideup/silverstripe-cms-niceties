window.addEventListener('load', function () {
  setTimeout(() => {
    if (window.location.hash) {
      const inputString = window.location.hash.substring(1)
      const els = document.querySelectorAll(
        '[aria-controls="' + inputString + '"]'
      )

      if (els.length) {
        // Function to recursively find the element with role "tabpanel".
        const findTabPanelAndClick = element => {
          const tabPanel = element.parentElement.closest('.ui-tabs-panel')
          if (tabPanel) {
            // If a parent tabPanel is found, call the function recursively.
            // No parent tabPanel found, use the ID of the last tabPanel to click the element with aria-controls.
            if (tabPanel.id) {
              findClicker(tabPanel.id, false)
            } else {
              console.log('There is no ID for tabPanel', tabPanel)
            }
          }
        }

        const runClick = function (el) {
          let ahref = el.querySelector('a')
          ahref.dispatchEvent(new Event('click'))
          ahref.style.fontWeight = 'bold'
          ahref.style.color = '#0071c4'
          el.dispatchEvent(new Event('click'))
          el.style.fontWeight = 'bold'
          el.style.color = '#0071c4'
        }

        const findClicker = function (selector) {
          const initialSelector = `[aria-controls="${selector}"]`
          const initialElement = document.querySelector(initialSelector)

          if (initialElement) {
            findTabPanelAndClick(initialElement)
            runClick(initialElement)
          } else {
            console.log(
              `No element found with aria-controls="${ariaControlsValue}"`
            )
          }
        }

        const parts = inputString.split(/_(?=[A-Z])/)

        // Find the last part and build the aria-controls value.
        const lastPart = parts.pop()
        const ariaControlsValue = parts.length
          ? `${parts.join('_')}_${lastPart}`
          : lastPart

        findClicker(ariaControlsValue)

        // Find the element with the corresponding aria-controls attribute and start the recursive process.
      }
    }
  }, 500)
})
