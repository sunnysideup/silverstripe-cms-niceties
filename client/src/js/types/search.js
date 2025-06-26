// // import '../../scss/LeftAndMain.scss';

// const $ = window.jQuery
// const NAME = 'sunnysideup/cms-niceties: LeftAndMain'
// const $document = $(document)

// const init = () => {
//   // console.log(`${NAME} [init]`);

//   const $searchHolder = $('.search-holder')
//   const $searchHolderShow = $('[name="showFilter"]')
//   const $searchHolderHide = $(
//     '<button type="button" title="Close" aria-expanded="true" class="search-box__close font-icon-cancel btn--no-text btn--icon-lg btn btn-secondary" aria-label="Close"></button>'
//   )

//   const searchHolderToggle = e => {
//     e.preventDefault()
//     e.stopImmediatePropagation()

//     // console.log(`${NAME} | [name="showFilter"]`);

//     $searchHolder.toggleClass('grid-field__search-holder--hidden')
//     $searchHolderShow.show()
//   }

//   $searchHolderShow.off('click')
//   $searchHolderHide.off('click')

//   $searchHolderShow.on('click', searchHolderToggle)
//   $searchHolderHide.on('click', searchHolderToggle)

//   $('.search-box__cancel').remove()
//   $('.search-box__group').append($searchHolderHide)

//   // Remove changed class to prevent confirmation dialog
//   $('.cms-edit-form.changed').removeClass('changed')
// }

// // FIX: dirty timeout
// $document.ready(() => {
//   setTimeout(init, 500)
// })
// $document.ajaxComplete(() => {
//   setTimeout(init, 500)
// })
