<?php

namespace Sunnysideup\CMSNiceties\Extensions;

use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Control\Director;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Extension;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\View\Requirements;
use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;

/**
 * Class \Sunnysideup\CMSNiceties\Extensions\ModelAdminExtension.
 *
 * @property ModelAdmin|ModelAdminExtension $owner
 */
class ModelAdminExtension extends Extension
{
    private static $excluded_models_from_ssu_extension = [];

    private static $excluded_modeladmins_from_ssu_extension = [];

    private static $sort_fields_from_ssu_extension = ['SortNumber', 'Sort'];

    public function updateEditForm($form)
    {
        $owner = $this->getOwner();
        $excludedModelAdmins = Config::inst()->get(ModelAdmin::class, 'excluded_modeladmins_from_ssu_extension');
        if (! in_array(get_class($owner), $excludedModelAdmins, true)) {
            $excludedModels = Config::inst()->get(ModelAdmin::class, 'excluded_models_from_ssu_extension');
            if (! in_array($owner->modelClass, $excludedModels, true)) {
                // This check is simply to ensure you are on the managed model you want adjust accordingly
                $obj = Injector::inst()->get($owner->modelClass);
                if ($obj) {
                    $dbFields = $obj->config()->get('db');
                    $sortFields = Config::inst()->get(ModelAdmin::class, 'sort_fields_from_ssu_extension');
                    foreach ($sortFields as $sortField) {
                        if (isset($dbFields[$sortField])) {
                            $gridField = $form->Fields()->dataFieldByName($this->sanitiseClassNameHelper($owner->modelClass));
                            // This is just a precaution to ensure we got a GridField from dataFieldByName() which you should have
                            if ($gridField instanceof GridField) {
                                $config = $gridField->getConfig();
                                if($config->getComponentByType(GridFieldSortableRows::class) === null) {
                                    $config->addComponent(new GridFieldSortableRows($sortField));
                                }
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
    protected function sanitiseClassNameHelper(string $class): string
    {
        return str_replace('\\', '-', (string) $class);
    }

    // /**
    //  * checks if there is only one record and if no further records can be created.
    //  * If those conditions are true, then redirect immediately to the record
    //  * for the convenience of the user.
    //  *
    //  * @return void
    //  */
    // protected function updateList($list)
    // {
    //     $owner = $this->getOwner();
    //     if($owner->getRequest()->param('ModelClass')) {
    //         $obj = $list->first();
    //         $link = $owner->getCMSEditLinkForManagedDataObject($obj);
    //         // die($_SERVER['HTTP_REFERER'] ?? 'xxx');
    //         if($list->count() === 1) {
    //             $obj = $list->first();
    //             if($obj->canCreate() === true) {
    //                 $link = $owner->getCMSEditLinkForManagedDataObject($obj);
    //                 if($link && $owner->getRequest()->getURL() !== $link) {
    //                     if(Director::is_ajax()) {
    //                         die('<script>window.location.replace("/' . $link . '");</script>');
    //                     }
    //                 }
    //             }
    //         }
    //     }
    // }
}
