# how to build dist?

- install in composer.json
```json
  "require-dev": {
    "sunnysideup/sswebpack_engine_only": "dev-master",
  },

```

then add the following into a bash file to run from the root of the project:
```shell
echo '------------------------------'
echo ' run build'
echo '------------------------------'
cd themes/sswebpack_engine_only/
npm install
npm run build --theme_dir=vendor/sunnysideup/cms-niceties/client --include_jquery=no
echo '------------------------------'

```
