<?php

use SilverStripe\View\Requirements;

use SilverStripe\Control\Director;

if(!Director::isDev()) {
    Director::forceSSL();
}
