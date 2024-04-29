(self["webpackChunkpublic"] = self["webpackChunkpublic"] || []).push([["app"],{

/***/ "../../vendor/sunnysideup/cms-niceties/client/src/js/types/left-menu-fix.js":
/*!**********************************************************************************!*\
  !*** ../../vendor/sunnysideup/cms-niceties/client/src/js/types/left-menu-fix.js ***!
  \**********************************************************************************/
/***/ (function() {

/**
 * Side menu labels were dissappearing
 * when editing pages in the CMS. This bug was due to the 3rd party library
 * thinking the side menu was collapsed then failing to properly collapse the sidemenu
 * The bug is present in the vendor folder so this script sets specific cookies to trick the 3rd
 * party code into thinking the side menu is always pulled out
 *
 * @author Tristan Mastrodicasa
 */
var $ = window.jQuery;
$.cookie('cms-menu-sticky', 'true', {
  path: '/',
  expires: 31
});
$.cookie('cms-panel-collapsed-cms-menu', 'false', {
  path: '/',
  expires: 31
});
$.cookie('cms-panel-collapsed-cms-content-tools-CMSMain', 'false', {
  path: '/',
  expires: 31
});

/***/ }),

/***/ "../../vendor/sunnysideup/cms-niceties/client/src/js/types/search.js":
/*!***************************************************************************!*\
  !*** ../../vendor/sunnysideup/cms-niceties/client/src/js/types/search.js ***!
  \***************************************************************************/
/***/ (function() {

// import '../../scss/LeftAndMain.scss';

var $ = window.jQuery;
var NAME = 'sunnysideup/cms-niceties: LeftAndMain';
var $document = $(document);
var init = function init() {
  // console.log(`${NAME} [init]`);

  var $searchHolder = $('.search-holder');
  var $searchHolderShow = $('[name="showFilter"]');
  var $searchHolderHide = $('<button type="button" title="Close" aria-expanded="true" class="search-box__close font-icon-cancel btn--no-text btn--icon-lg btn btn-secondary" aria-label="Close"></button>');
  var searchHolderToggle = function searchHolderToggle(e) {
    e.preventDefault();
    e.stopImmediatePropagation();

    // console.log(`${NAME} | [name="showFilter"]`);

    $searchHolder.toggleClass('grid-field__search-holder--hidden');
    $searchHolderShow.show();
  };
  $searchHolderShow.off('click');
  $searchHolderHide.off('click');
  $searchHolderShow.on('click', searchHolderToggle);
  $searchHolderHide.on('click', searchHolderToggle);
  $('.search-box__cancel').remove();
  $('.search-box__group').append($searchHolderHide);

  // Remove changed class to prevent confirmation dialog
  $('.cms-edit-form.changed').removeClass('changed');
};

// FIX: dirty timeout
$document.ready(function () {
  setTimeout(init, 500);
});
$document.ajaxComplete(function () {
  setTimeout(init, 500);
});

/***/ }),

/***/ "../../vendor/sunnysideup/cms-niceties/client/src/js/types/tabs.js":
/*!*************************************************************************!*\
  !*** ../../vendor/sunnysideup/cms-niceties/client/src/js/types/tabs.js ***!
  \*************************************************************************/
/***/ (function() {

window.addEventListener('load', function () {
  setTimeout(function () {
    if (window.location.hash) {
      var inputString = window.location.hash.substring(1);
      var els = document.querySelectorAll('[aria-controls="' + inputString + '"]');
      if (els.length) {
        // Function to recursively find the element with role "tabpanel".
        var findTabPanelAndClick = function findTabPanelAndClick(element) {
          var tabPanel = element.parentElement.closest('.ui-tabs-panel');
          if (tabPanel) {
            // If a parent tabPanel is found, call the function recursively.
            // No parent tabPanel found, use the ID of the last tabPanel to click the element with aria-controls.
            if (tabPanel.id) {
              findClicker(tabPanel.id, false);
            } else {
              console.log('There is no ID for tabPanel', tabPanel);
            }
          }
        };
        var runClick = function runClick(el) {
          var ahref = el.querySelector('a');
          ahref.click();
          ahref.style.fontWeight = 'bold';
          ahref.style.color = '#0071c4';
          el.click();
          el.style.fontWeight = 'bold';
          el.style.color = '#0071c4';
        };
        var findClicker = function findClicker(selector) {
          var initialSelector = "[aria-controls=\"".concat(selector, "\"]");
          var initialElement = document.querySelector(initialSelector);
          if (initialElement) {
            findTabPanelAndClick(initialElement);
            runClick(initialElement);
          } else {
            console.log("No element found with aria-controls=\"".concat(ariaControlsValue, "\""));
          }
        };
        var parts = inputString.split(/_(?=[A-Z])/);

        // Find the last part and build the aria-controls value.
        var lastPart = parts.pop();
        var ariaControlsValue = parts.length ? "".concat(parts.join('_'), "_").concat(lastPart) : lastPart;
        findClicker(ariaControlsValue);

        // Find the element with the corresponding aria-controls attribute and start the recursive process.
      }
    }
  }, 500);
});

/***/ }),

/***/ "../../vendor/sunnysideup/cms-niceties/client/src/js/types/task-runner.js":
/*!********************************************************************************!*\
  !*** ../../vendor/sunnysideup/cms-niceties/client/src/js/types/task-runner.js ***!
  \********************************************************************************/
/***/ (function() {



/***/ }),

/***/ "../../vendor/sunnysideup/cms-niceties/client/src/main.js":
/*!****************************************************************!*\
  !*** ../../vendor/sunnysideup/cms-niceties/client/src/main.js ***!
  \****************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _js_types_left_menu_fix__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./js/types/left-menu-fix */ "../../vendor/sunnysideup/cms-niceties/client/src/js/types/left-menu-fix.js");
/* harmony import */ var _js_types_left_menu_fix__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_js_types_left_menu_fix__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _js_types_search__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./js/types/search */ "../../vendor/sunnysideup/cms-niceties/client/src/js/types/search.js");
/* harmony import */ var _js_types_search__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_js_types_search__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _js_types_tabs__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./js/types/tabs */ "../../vendor/sunnysideup/cms-niceties/client/src/js/types/tabs.js");
/* harmony import */ var _js_types_tabs__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_js_types_tabs__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _js_types_task_runner__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./js/types/task-runner */ "../../vendor/sunnysideup/cms-niceties/client/src/js/types/task-runner.js");
/* harmony import */ var _js_types_task_runner__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_js_types_task_runner__WEBPACK_IMPORTED_MODULE_3__);





/***/ })

},
/******/ function(__webpack_require__) { // webpackRuntimeModules
/******/ var __webpack_exec__ = function(moduleId) { return __webpack_require__(__webpack_require__.s = moduleId); }
/******/ var __webpack_exports__ = (__webpack_exec__("../../vendor/sunnysideup/cms-niceties/client/src/main.js"));
/******/ }
]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,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QUFvQixDQUFDb0IsY0FBYyxDQUFDO1lBQ3BDWCxRQUFRLENBQUNXLGNBQWMsQ0FBQztVQUMxQixDQUFDLE1BQU07WUFDTGIsT0FBTyxDQUFDQyxHQUFHLDBDQUFBVyxNQUFBLENBQytCRSxpQkFBaUIsT0FDM0QsQ0FBQztVQUNIO1FBQ0YsQ0FBQztRQUVELElBQU1DLEtBQUssR0FBRzNCLFdBQVcsQ0FBQzRCLEtBQUssQ0FBQyxZQUFZLENBQUM7O1FBRTdDO1FBQ0EsSUFBTUMsUUFBUSxHQUFHRixLQUFLLENBQUNHLEdBQUcsQ0FBQyxDQUFDO1FBQzVCLElBQU1KLGlCQUFpQixHQUFHQyxLQUFLLENBQUN2QixNQUFNLE1BQUFvQixNQUFBLENBQy9CRyxLQUFLLENBQUNJLElBQUksQ0FBQyxHQUFHLENBQUMsT0FBQVAsTUFBQSxDQUFJSyxRQUFRLElBQzlCQSxRQUFRO1FBRVpsQixXQUFXLENBQUNlLGlCQUFpQixDQUFDOztRQUU5QjtNQUNGO0lBQ0Y7RUFDRixDQUFDLEVBQUUsR0FBRyxDQUFDO0FBQ1QsQ0FBQyxDQUFDOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUM3RGdDO0FBQ1A7QUFDRiIsInNvdXJjZXMiOlsid2VicGFjazovL3B1YmxpYy8uLi8uLi92ZW5kb3Ivc3VubnlzaWRldXAvY21zLW5pY2V0aWVzL2NsaWVudC9zcmMvanMvdHlwZXMvbGVmdC1tZW51LWZpeC5qcyIsIndlYnBhY2s6Ly9wdWJsaWMvLi4vLi4vdmVuZG9yL3N1bm55c2lkZXVwL2Ntcy1uaWNldGllcy9jbGllbnQvc3JjL2pzL3R5cGVzL3NlYXJjaC5qcyIsIndlYnBhY2s6Ly9wdWJsaWMvLi4vLi4vdmVuZG9yL3N1bm55c2lkZXVwL2Ntcy1uaWNldGllcy9jbGllbnQvc3JjL2pzL3R5cGVzL3RhYnMuanMiLCJ3ZWJwYWNrOi8vcHVibGljLy4uLy4uL3ZlbmRvci9zdW5ueXNpZGV1cC9jbXMtbmljZXRpZXMvY2xpZW50L3NyYy9tYWluLmpzIl0sInNvdXJjZXNDb250ZW50IjpbIi8qKlxuICogU2lkZSBtZW51IGxhYmVscyB3ZXJlIGRpc3NhcHBlYXJpbmdcbiAqIHdoZW4gZWRpdGluZyBwYWdlcyBpbiB0aGUgQ01TLiBUaGlzIGJ1ZyB3YXMgZHVlIHRvIHRoZSAzcmQgcGFydHkgbGlicmFyeVxuICogdGhpbmtpbmcgdGhlIHNpZGUgbWVudSB3YXMgY29sbGFwc2VkIHRoZW4gZmFpbGluZyB0byBwcm9wZXJseSBjb2xsYXBzZSB0aGUgc2lkZW1lbnVcbiAqIFRoZSBidWcgaXMgcHJlc2VudCBpbiB0aGUgdmVuZG9yIGZvbGRlciBzbyB0aGlzIHNjcmlwdCBzZXRzIHNwZWNpZmljIGNvb2tpZXMgdG8gdHJpY2sgdGhlIDNyZFxuICogcGFydHkgY29kZSBpbnRvIHRoaW5raW5nIHRoZSBzaWRlIG1lbnUgaXMgYWx3YXlzIHB1bGxlZCBvdXRcbiAqXG4gKiBAYXV0aG9yIFRyaXN0YW4gTWFzdHJvZGljYXNhXG4gKi9cbmNvbnN0ICQgPSB3aW5kb3cualF1ZXJ5O1xuXG4kLmNvb2tpZSgnY21zLW1lbnUtc3RpY2t5JywgJ3RydWUnLCB7XG4gIHBhdGg6ICcvJyxcbiAgZXhwaXJlczogMzEsXG59KTtcblxuJC5jb29raWUoJ2Ntcy1wYW5lbC1jb2xsYXBzZWQtY21zLW1lbnUnLCAnZmFsc2UnLCB7XG4gIHBhdGg6ICcvJyxcbiAgZXhwaXJlczogMzEsXG59KTtcblxuJC5jb29raWUoJ2Ntcy1wYW5lbC1jb2xsYXBzZWQtY21zLWNvbnRlbnQtdG9vbHMtQ01TTWFpbicsICdmYWxzZScsIHtcbiAgcGF0aDogJy8nLFxuICBleHBpcmVzOiAzMSxcbn0pO1xuIiwiLy8gaW1wb3J0ICcuLi8uLi9zY3NzL0xlZnRBbmRNYWluLnNjc3MnO1xuXG5jb25zdCAkID0gd2luZG93LmpRdWVyeVxuY29uc3QgTkFNRSA9ICdzdW5ueXNpZGV1cC9jbXMtbmljZXRpZXM6IExlZnRBbmRNYWluJ1xuY29uc3QgJGRvY3VtZW50ID0gJChkb2N1bWVudClcblxuY29uc3QgaW5pdCA9ICgpID0+IHtcbiAgLy8gY29uc29sZS5sb2coYCR7TkFNRX0gW2luaXRdYCk7XG5cbiAgY29uc3QgJHNlYXJjaEhvbGRlciA9ICQoJy5zZWFyY2gtaG9sZGVyJylcbiAgY29uc3QgJHNlYXJjaEhvbGRlclNob3cgPSAkKCdbbmFtZT1cInNob3dGaWx0ZXJcIl0nKVxuICBjb25zdCAkc2VhcmNoSG9sZGVySGlkZSA9ICQoXG4gICAgJzxidXR0b24gdHlwZT1cImJ1dHRvblwiIHRpdGxlPVwiQ2xvc2VcIiBhcmlhLWV4cGFuZGVkPVwidHJ1ZVwiIGNsYXNzPVwic2VhcmNoLWJveF9fY2xvc2UgZm9udC1pY29uLWNhbmNlbCBidG4tLW5vLXRleHQgYnRuLS1pY29uLWxnIGJ0biBidG4tc2Vjb25kYXJ5XCIgYXJpYS1sYWJlbD1cIkNsb3NlXCI+PC9idXR0b24+J1xuICApXG5cbiAgY29uc3Qgc2VhcmNoSG9sZGVyVG9nZ2xlID0gZSA9PiB7XG4gICAgZS5wcmV2ZW50RGVmYXVsdCgpXG4gICAgZS5zdG9wSW1tZWRpYXRlUHJvcGFnYXRpb24oKVxuXG4gICAgLy8gY29uc29sZS5sb2coYCR7TkFNRX0gfCBbbmFtZT1cInNob3dGaWx0ZXJcIl1gKTtcblxuICAgICRzZWFyY2hIb2xkZXIudG9nZ2xlQ2xhc3MoJ2dyaWQtZmllbGRfX3NlYXJjaC1ob2xkZXItLWhpZGRlbicpXG4gICAgJHNlYXJjaEhvbGRlclNob3cuc2hvdygpXG4gIH1cblxuICAkc2VhcmNoSG9sZGVyU2hvdy5vZmYoJ2NsaWNrJylcbiAgJHNlYXJjaEhvbGRlckhpZGUub2ZmKCdjbGljaycpXG5cbiAgJHNlYXJjaEhvbGRlclNob3cub24oJ2NsaWNrJywgc2VhcmNoSG9sZGVyVG9nZ2xlKVxuICAkc2VhcmNoSG9sZGVySGlkZS5vbignY2xpY2snLCBzZWFyY2hIb2xkZXJUb2dnbGUpXG5cbiAgJCgnLnNlYXJjaC1ib3hfX2NhbmNlbCcpLnJlbW92ZSgpXG4gICQoJy5zZWFyY2gtYm94X19ncm91cCcpLmFwcGVuZCgkc2VhcmNoSG9sZGVySGlkZSlcblxuICAvLyBSZW1vdmUgY2hhbmdlZCBjbGFzcyB0byBwcmV2ZW50IGNvbmZpcm1hdGlvbiBkaWFsb2dcbiAgJCgnLmNtcy1lZGl0LWZvcm0uY2hhbmdlZCcpLnJlbW92ZUNsYXNzKCdjaGFuZ2VkJylcbn1cblxuLy8gRklYOiBkaXJ0eSB0aW1lb3V0XG4kZG9jdW1lbnQucmVhZHkoKCkgPT4ge1xuICBzZXRUaW1lb3V0KGluaXQsIDUwMClcbn0pXG4kZG9jdW1lbnQuYWpheENvbXBsZXRlKCgpID0+IHtcbiAgc2V0VGltZW91dChpbml0LCA1MDApXG59KVxuIiwid2luZG93LmFkZEV2ZW50TGlzdGVuZXIoJ2xvYWQnLCBmdW5jdGlvbiAoKSB7XG4gIHNldFRpbWVvdXQoKCkgPT4ge1xuICAgIGlmICh3aW5kb3cubG9jYXRpb24uaGFzaCkge1xuICAgICAgY29uc3QgaW5wdXRTdHJpbmcgPSB3aW5kb3cubG9jYXRpb24uaGFzaC5zdWJzdHJpbmcoMSlcbiAgICAgIGNvbnN0IGVscyA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoXG4gICAgICAgICdbYXJpYS1jb250cm9scz1cIicgKyBpbnB1dFN0cmluZyArICdcIl0nXG4gICAgICApXG5cbiAgICAgIGlmIChlbHMubGVuZ3RoKSB7XG4gICAgICAgIC8vIEZ1bmN0aW9uIHRvIHJlY3Vyc2l2ZWx5IGZpbmQgdGhlIGVsZW1lbnQgd2l0aCByb2xlIFwidGFicGFuZWxcIi5cbiAgICAgICAgY29uc3QgZmluZFRhYlBhbmVsQW5kQ2xpY2sgPSBlbGVtZW50ID0+IHtcbiAgICAgICAgICBjb25zdCB0YWJQYW5lbCA9IGVsZW1lbnQucGFyZW50RWxlbWVudC5jbG9zZXN0KCcudWktdGFicy1wYW5lbCcpXG4gICAgICAgICAgaWYgKHRhYlBhbmVsKSB7XG4gICAgICAgICAgICAvLyBJZiBhIHBhcmVudCB0YWJQYW5lbCBpcyBmb3VuZCwgY2FsbCB0aGUgZnVuY3Rpb24gcmVjdXJzaXZlbHkuXG4gICAgICAgICAgICAvLyBObyBwYXJlbnQgdGFiUGFuZWwgZm91bmQsIHVzZSB0aGUgSUQgb2YgdGhlIGxhc3QgdGFiUGFuZWwgdG8gY2xpY2sgdGhlIGVsZW1lbnQgd2l0aCBhcmlhLWNvbnRyb2xzLlxuICAgICAgICAgICAgaWYgKHRhYlBhbmVsLmlkKSB7XG4gICAgICAgICAgICAgIGZpbmRDbGlja2VyKHRhYlBhbmVsLmlkLCBmYWxzZSlcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgIGNvbnNvbGUubG9nKCdUaGVyZSBpcyBubyBJRCBmb3IgdGFiUGFuZWwnLCB0YWJQYW5lbClcbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICBjb25zdCBydW5DbGljayA9IGZ1bmN0aW9uIChlbCkge1xuICAgICAgICAgIGxldCBhaHJlZiA9IGVsLnF1ZXJ5U2VsZWN0b3IoJ2EnKVxuICAgICAgICAgIGFocmVmLmNsaWNrKClcbiAgICAgICAgICBhaHJlZi5zdHlsZS5mb250V2VpZ2h0ID0gJ2JvbGQnXG4gICAgICAgICAgYWhyZWYuc3R5bGUuY29sb3IgPSAnIzAwNzFjNCdcbiAgICAgICAgICBlbC5jbGljaygpXG4gICAgICAgICAgZWwuc3R5bGUuZm9udFdlaWdodCA9ICdib2xkJ1xuICAgICAgICAgIGVsLnN0eWxlLmNvbG9yID0gJyMwMDcxYzQnXG4gICAgICAgIH1cblxuICAgICAgICBjb25zdCBmaW5kQ2xpY2tlciA9IGZ1bmN0aW9uIChzZWxlY3Rvcikge1xuICAgICAgICAgIGNvbnN0IGluaXRpYWxTZWxlY3RvciA9IGBbYXJpYS1jb250cm9scz1cIiR7c2VsZWN0b3J9XCJdYFxuICAgICAgICAgIGNvbnN0IGluaXRpYWxFbGVtZW50ID0gZG9jdW1lbnQucXVlcnlTZWxlY3Rvcihpbml0aWFsU2VsZWN0b3IpXG5cbiAgICAgICAgICBpZiAoaW5pdGlhbEVsZW1lbnQpIHtcbiAgICAgICAgICAgIGZpbmRUYWJQYW5lbEFuZENsaWNrKGluaXRpYWxFbGVtZW50KVxuICAgICAgICAgICAgcnVuQ2xpY2soaW5pdGlhbEVsZW1lbnQpXG4gICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIGNvbnNvbGUubG9nKFxuICAgICAgICAgICAgICBgTm8gZWxlbWVudCBmb3VuZCB3aXRoIGFyaWEtY29udHJvbHM9XCIke2FyaWFDb250cm9sc1ZhbHVlfVwiYFxuICAgICAgICAgICAgKVxuICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIGNvbnN0IHBhcnRzID0gaW5wdXRTdHJpbmcuc3BsaXQoL18oPz1bQS1aXSkvKVxuXG4gICAgICAgIC8vIEZpbmQgdGhlIGxhc3QgcGFydCBhbmQgYnVpbGQgdGhlIGFyaWEtY29udHJvbHMgdmFsdWUuXG4gICAgICAgIGNvbnN0IGxhc3RQYXJ0ID0gcGFydHMucG9wKClcbiAgICAgICAgY29uc3QgYXJpYUNvbnRyb2xzVmFsdWUgPSBwYXJ0cy5sZW5ndGhcbiAgICAgICAgICA/IGAke3BhcnRzLmpvaW4oJ18nKX1fJHtsYXN0UGFydH1gXG4gICAgICAgICAgOiBsYXN0UGFydFxuXG4gICAgICAgIGZpbmRDbGlja2VyKGFyaWFDb250cm9sc1ZhbHVlKVxuXG4gICAgICAgIC8vIEZpbmQgdGhlIGVsZW1lbnQgd2l0aCB0aGUgY29ycmVzcG9uZGluZyBhcmlhLWNvbnRyb2xzIGF0dHJpYnV0ZSBhbmQgc3RhcnQgdGhlIHJlY3Vyc2l2ZSBwcm9jZXNzLlxuICAgICAgfVxuICAgIH1cbiAgfSwgNTAwKVxufSlcbiIsImltcG9ydCAnLi9qcy90eXBlcy9sZWZ0LW1lbnUtZml4JztcbmltcG9ydCAnLi9qcy90eXBlcy9zZWFyY2gnO1xuaW1wb3J0ICcuL2pzL3R5cGVzL3RhYnMnO1xuaW1wb3J0ICcuL2pzL3R5cGVzL3Rhc2stcnVubmVyJztcblxuXG4iXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsImpRdWVyeSIsImNvb2tpZSIsInBhdGgiLCJleHBpcmVzIiwiTkFNRSIsIiRkb2N1bWVudCIsImRvY3VtZW50IiwiaW5pdCIsIiRzZWFyY2hIb2xkZXIiLCIkc2VhcmNoSG9sZGVyU2hvdyIsIiRzZWFyY2hIb2xkZXJIaWRlIiwic2VhcmNoSG9sZGVyVG9nZ2xlIiwiZSIsInByZXZlbnREZWZhdWx0Iiwic3RvcEltbWVkaWF0ZVByb3BhZ2F0aW9uIiwidG9nZ2xlQ2xhc3MiLCJzaG93Iiwib2ZmIiwib24iLCJyZW1vdmUiLCJhcHBlbmQiLCJyZW1vdmVDbGFzcyIsInJlYWR5Iiwic2V0VGltZW91dCIsImFqYXhDb21wbGV0ZSIsImFkZEV2ZW50TGlzdGVuZXIiLCJsb2NhdGlvbiIsImhhc2giLCJpbnB1dFN0cmluZyIsInN1YnN0cmluZyIsImVscyIsInF1ZXJ5U2VsZWN0b3JBbGwiLCJsZW5ndGgiLCJmaW5kVGFiUGFuZWxBbmRDbGljayIsImVsZW1lbnQiLCJ0YWJQYW5lbCIsInBhcmVudEVsZW1lbnQiLCJjbG9zZXN0IiwiaWQiLCJmaW5kQ2xpY2tlciIsImNvbnNvbGUiLCJsb2ciLCJydW5DbGljayIsImVsIiwiYWhyZWYiLCJxdWVyeVNlbGVjdG9yIiwiY2xpY2siLCJzdHlsZSIsImZvbnRXZWlnaHQiLCJjb2xvciIsInNlbGVjdG9yIiwiaW5pdGlhbFNlbGVjdG9yIiwiY29uY2F0IiwiaW5pdGlhbEVsZW1lbnQiLCJhcmlhQ29udHJvbHNWYWx1ZSIsInBhcnRzIiwic3BsaXQiLCJsYXN0UGFydCIsInBvcCIsImpvaW4iXSwic291cmNlUm9vdCI6IiJ9