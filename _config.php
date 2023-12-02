<?php

use SilverStripe\Control\Director;

if(!Director::isDev()) {
    Director::forceSSL();
}
