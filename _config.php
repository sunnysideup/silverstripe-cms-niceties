<?php

use SilverStripe\View\Requirements;

use SilverStripe\Control\Director;

Requirements::add_i18n_javascript('sunnysideup:cms-niceties lang/en.js');

if(!Director::isDev()) {
    Director::forceSSL();
}
