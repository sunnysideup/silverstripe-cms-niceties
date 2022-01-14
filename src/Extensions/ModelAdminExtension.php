<?php

namespace Sunnysideup\CMSNiceties\Extensions;

use SilverStripe\Core\Extension;

use SilverStripe\Core\Injector\Injector;

use SilverStripe\Forms\GridField\GridField;

use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;

class ModelAdminExtension extends Extension
{
    function updateEditForm($form)
    {
        $owner = $this->getOwner();
        // This check is simply to ensure you are on the managed model you want adjust accordingly
        $obj = Injector::inst()->get($owner->modelClass);
        if ($obj) {
            $dbFields = $obj->stat('db');
            $sortFields = ['SortNumber', 'Sort'];
            foreach ($sortFields as $sortField) {
                if (isset($dbFields[$sortField])) {
                    $gridField = $form->Fields()->dataFieldByName($this->sanitiseClassNameHelper($owner->modelClass));
                    // This is just a precaution to ensure we got a GridField from dataFieldByName() which you should have
                    if ($gridField instanceof GridField) {
                        $gridField->getConfig()->addComponent(new GridFieldSortableRows($sortField));
                    }

                    break;
                }
            }
        }

        return $form;
    }
    //
    // /**
    //  * Sanitise a model class' name for inclusion in a link
    //  *
    //  * @param string $class
    //  * @return string
    //  */
    protected function sanitiseClassNameHelper($class)
    {
        return str_replace('\\', '-', $class);
    }

}
