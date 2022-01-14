<?php

namespace Sunnysideup\CMSNiceties\Extensions;

use SilverStripe\Core\Extension;

use SilverStripe\Core\Injector\Injector;

use SilverStripe\Core\Config\Config;

use SilverStripe\Forms\GridField\GridField;

use SilverStripe\Admin\ModelAdmin;

use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;

class ModelAdminExtension extends Extension
{

    private static $excluded_models_from_ssu_extension = [];

    private static $excluded_modeladmins_from_ssu_extension = [];

    private static $sort_fields_from_ssu_extension = ['SortNumber', 'Sort'];

    function updateEditForm($form)
    {
        $owner = $this->getOwner();
        $excludedModelAdmins = Config::inst()->get(ModelAdmin::class, 'excluded_modeladmins_from_ssu_extension');
        if(! in_array(get_class($owner), $excludedModelAdmins)) {
            $excludedModels = Config::inst()->get(ModelAdmin::class, 'excluded_models_from_ssu_extension');
            if(! in_array($owner->modelClass, $excludedModels)) {
                // This check is simply to ensure you are on the managed model you want adjust accordingly
                $obj = Injector::inst()->get($owner->modelClass);
                if ($obj) {
                    $dbFields = $obj->stat('db');
                    $sortFields = Config::inst()->get(ModelAdmin::class, 'sort_fields_from_ssu_extension');;
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
