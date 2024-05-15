#!/bin/bash

source ~/.nvm/nvm.sh
nvm use node

echo '------------------------------'
echo ' run build'
echo '------------------------------'
cd themes/sswebpack_engine_only/
npm install
npm run watch --theme_dir=vendor/sunnysideup/cms-niceties/client --include_jquery=no
echo '------------------------------'
