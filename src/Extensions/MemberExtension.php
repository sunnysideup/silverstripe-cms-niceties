<?php

namespace Sunnysideup\CMSNiceties\Extensions;

use SilverStripe\Forms\FieldList;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Extension;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Member;
use SilverStripe\View\Requirements;

/**
 *
 */
class MemberExtension extends DataExtension
{
    private static $fix_locales = [
        'af_ZA' => 'en_GB',
    ];

    public function onBeforeWrite()
    {
        $owner = $this->getOwner();
        $localeFixes = Config::inst()->get(self::class, 'fix_locales') ?: [];
        foreach ($localeFixes as $was => $is) {
            if ($owner->Locale === $was) {
                $owner->Locale = $is;
            }
        }
    }

    /**
     * Update Fields
     * @return FieldList
     */
    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeByName('FailedLoginCount');
        return $fields;
    }
}
