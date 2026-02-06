<?php

namespace Sunnysideup\CMSNiceties\Extensions;

use SilverStripe\Core\Extension;

/**
 * Class \Sunnysideup\CMSNiceties\Extensions\CMSNicetiesFormFieldExtension
 *
 * @property FormField|CMSNicetiesFormFieldExtension $owner
 */
class CMSNicetiesFormFieldExtension extends Extension
{
    public function AddToDescription(string $description)
    {
        $owner = $this->getOwner();
        $descriptionArray = [$owner->getDescription()];
        $descriptionArray[] = $description;
        return $owner->setDescription(implode('<br />', array_filter($descriptionArray)));
    }

    public function AddToRightTitle(string $description)
    {
        $owner = $this->getOwner();
        $descriptionArray = [$owner->getRightTitle()];
        $descriptionArray[] = $description;
        return $owner->setRightTitle(implode('<br />', array_filter($descriptionArray)));
    }

    public function AddToLeftTitle(string $description)
    {
        $owner = $this->getOwner();
        $descriptionArray = [$owner->getLeftTitle()];
        $descriptionArray[] = $description;
        return $owner->setLeftTitle(implode('<br />', array_filter($descriptionArray)));
    }
}
