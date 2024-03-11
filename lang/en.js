if (typeof ss === 'undefined' || typeof ss.i18n === 'undefined') {
  /* eslint-disable-next-line no-console */
  console.error('Class ss.i18n not defined')
} else {
  console.log('Adding dictionary for AssetAdmin.DETAILS')
  ss.i18n.addDictionary('en', {
    'AssetAdmin.DETAILS': 'Edit Details'
  })
}
