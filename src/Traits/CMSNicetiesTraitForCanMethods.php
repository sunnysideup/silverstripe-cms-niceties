<?php


namespace Sunnysideup\CMSNiceties\Traits;

use SilverStripe\Security\Security;
use SilverStripe\Security\Permission;
use SilverStripe\ORM\ArrayList;

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
                return $outcome;
            } else {
                return $obj->canCreate($member, $context);
            }
        }
        if ($this->hasMethod('canEditingGroupsCodes')) {
            $groupCodes = $this->canEditingGroupsCodes();
        } else {
            $groupCodes = [
                'CMS_ACCESS_CMSMain',
            ];
        }
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
