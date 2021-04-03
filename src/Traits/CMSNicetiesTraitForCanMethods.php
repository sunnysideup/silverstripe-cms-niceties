<?php

namespace Sunnysideup\CMSNiceties\Traits;

use SilverStripe\ORM\ArrayList;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;

trait CMSNicetiesTraitForCanMethods
{
    public function canCreate($member = null, $context = [])
    {
        if (! $member) {
            $member = Security::getCurrentUser();
        }

        if ($this->hasMethod('canEditingOwnerObject')) {
            $obj = $this->canEditingOwnerObject();
            if ($obj instanceof ArrayList) {
                $outcome = true;
                foreach ($obj as $item) {
                    if (! $item->canCreate($member, $context)) {
                        $outcome = false;

                        break;
                    }
                }
                if ($outcome) {
                    return parent::canCreate($member, $context);
                }

                return false;
            }

            return $obj->canCreate($member, $context);
        }
        $groupCodes = $this->hasMethod('canEditingGroupsCodes') ? $this->canEditingGroupsCodes() : [
            'CMS_ACCESS_CMSMain',
        ];
        $groupCodes = array_merge(['ADMIN'], $groupCodes);

        foreach ($groupCodes as $groupCode) {
            if (Permission::checkMember($member, $groupCode)) {
                return parent::canCreate($member, $context);
            }
        }

        return false;
    }

    public function canEdit($member = null)
    {
        return $this->canCreate($member);
    }

    public function canDelete($member = null)
    {
        return $this->canCreate($member);
    }
}
