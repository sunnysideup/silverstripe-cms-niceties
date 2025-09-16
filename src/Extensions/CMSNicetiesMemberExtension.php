<?php

namespace Sunnysideup\CMSNiceties\Extensions;

use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\FieldList;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\DatetimeField;
use SilverStripe\Forms\NumericField;

/**
 * Class \Sunnysideup\CMSNiceties\Extensions\CMSNicetiesMemberExtension
 *
 * @property Member|CMSNicetiesMemberExtension $owner
 */
class CMSNicetiesMemberExtension extends Extension
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
     */
    public function updateCMSFields(FieldList $fields)
    {
        $owner = $this->getOwner();
        $fields->removeByName('FailedLoginCount');
        if (!$owner->exists()) {
            //ugly-ish but works. Defaults and populateDefaults don't.
            $owner->Locale = "en_GB";
        }
        // $fields->dataFieldByName('FailedLoginCount')->setReadonly(false);
        $fields->addFieldsToTab('Root.Security', [
            DatetimeField::create('LockedOutUntil', 'Locked Out Until'),
            NumericField::create('FailedLoginCount', 'Failed Login Count'),
        ]);
    }
}
