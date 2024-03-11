<?php

use SilverStripe\View\Requirements;

use SilverStripe\Control\Director;

Requirements::javascript('silverstripe/admin:client/dist/js/i18n.js');
Requirements::add_i18n_javascript('sunnysideup:cms-niceties lang');

if(!Director::isDev()) {
    Director::forceSSL();
}
