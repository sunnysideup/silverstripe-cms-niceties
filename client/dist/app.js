(self.webpackChunkpublic=self.webpackChunkpublic||[]).push([[143],{985:function(){var e=window.jQuery;e.cookie("cms-menu-sticky","true",{path:"/",expires:31}),e.cookie("cms-panel-collapsed-cms-menu","false",{path:"/",expires:31}),e.cookie("cms-panel-collapsed-cms-content-tools-CMSMain","false",{path:"/",expires:31})},166:function(){var e=window.jQuery,o="sunnysideup/cms-niceties: LeftAndMain",n=e(document),t=function(){console.log("".concat(o," [init]"));var n=e(".search-holder"),t=e('[name="showFilter"]'),c=e('<button type="button" title="Close" aria-expanded="true" class="search-box__close font-icon-cancel btn--no-text btn--icon-lg btn btn-secondary" aria-label="Close"></button>'),i=function(e){e.preventDefault(),e.stopImmediatePropagation(),console.log("".concat(o,' | [name="showFilter"]')),n.toggleClass("grid-field__search-holder--hidden"),t.show()};t.off("click"),c.off("click"),t.on("click",i),c.on("click",i),e(".search-box__cancel").remove(),e(".search-box__group").append(c),e(".cms-edit-form.changed").removeClass("changed")};n.ready((function(){setTimeout(t,500)})),n.ajaxComplete((function(){setTimeout(t,500)}))},95:function(){window.addEventListener("load",(function(){setTimeout((function(){if(window.location.hash){var e=document.querySelectorAll('[aria-controls="'+window.location.hash.substring(1)+'"]');e.length&&(el=e[0],"#"+el.getAttribute("aria-controls")===window.location.hash&&(el.firstChild.click(),el.firstChild.style.fontWeight="bold",el.firstChild.style.color="#0071c4"))}}),500)}))},574:function(e,o,n){"use strict";n(985),n(166),n(95)}},function(e){var o;o=574,e(e.s=o)}]);