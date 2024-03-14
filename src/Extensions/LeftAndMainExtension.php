<?php

namespace Sunnysideup\CMSNiceties\Extensions;

use SilverStripe\Admin\LeftAndMain;
use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;

/**
 * Class \Sunnysideup\CMSNiceties\Extensions\LeftAndMainExtension.
 *
 * @property LeftAndMain|LeftAndMainExtension $owner
 */
class LeftAndMainExtension extends Extension
{
    public function init()
    {
        Requirements::add_i18n_javascript('sunnysideup/cms-niceties: client/lang');
    }
}
