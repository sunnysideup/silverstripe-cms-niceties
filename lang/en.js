if (typeof ss === 'undefined' || typeof ss.i18n === 'undefined') {
  /* eslint-disable-next-line no-console */
  console.error('Class ss.i18n not defined')
} else {
  ss.i18n.addDictionary('en', {
    'AssetAdmin.DETAILS': 'Edit Details'
  })
}
