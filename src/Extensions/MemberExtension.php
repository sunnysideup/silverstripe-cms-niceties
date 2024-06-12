<?php

namespace Sunnysideup\CMSNiceties\Extensions;

use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

class MemberExtension extends DataExtension
{
    private static $fix_locales = [
        'af_ZA' => 'en_GB',
    ];

    public function onBeforeWrite()
    {
        $owner = $this->getOwner();
        $localeFixes = Config::inst()->get(self::class, 'fix_locales') ?: [];
        $owner->Locale = $localeFixes[$owner->Locale] ?? $owner->Locale;
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
