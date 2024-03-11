<?php

use SilverStripe\View\Requirements;

use SilverStripe\Control\Director;

Requirements::add_i18n_javascript('silverstripe/asset-admin: client/lang');
Requirements::add_i18n_javascript('sunnysideup/cms-niceties: client/lang');

if(!Director::isDev()) {
    Director::forceSSL();
}
