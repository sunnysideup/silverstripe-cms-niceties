<?php

use SilverStripe\Control\Director;

if (! Director::isDev()) {
    Director::forceSSL();
}
// if (0 === strpos(ltrim(($_SERVER['REQUEST_URI'] ?? ''), '/'), 'admin')) {

// }
