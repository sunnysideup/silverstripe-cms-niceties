<?php

namespace Sunnysideup\CMSNiceties\Extensions;

use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Control\Director;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Extension;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Lumberjack\Forms\GridFieldSiteTreeState;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionChecker;
use SilverStripe\Security\Security;
use SilverStripe\Versioned\Versioned;
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

    private static $sort_fields_from_ssu_extension = ['SortNumber', 'Sort', 'SortOrder'];

    private static $assume_view_all = [
        'ADMIN',
    ];

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
                    $sortFields = Config::inst()->get(ModelAdmin::class, 'sort_fields_from_ssu_extension');
                    $gridField = $form->Fields()->dataFieldByName($this->sanitiseClassNameHelper($owner->modelClass));
                    if ($gridField instanceof GridField) {
                        $config = $gridField->getConfig();
                        $dbFields = $obj->config()->get('db');
                        foreach ($sortFields as $sortField) {
                            if (isset($dbFields[$sortField])) {
                                // This is just a precaution to ensure we got a GridField from dataFieldByName() which you should have
                                if (! $config->getComponentByType(GridFieldSortableRows::class)) {
                                    $obj = $owner->modelClass::singleton();
                                    if ($obj->hasExtension(Versioned::class)) {
                                        $sorter = (new GridFieldSortableRows($sortField))
                                            ->setUpdateVersionedStage(Versioned::LIVE);
                                    } else {
                                        $sorter = new GridFieldSortableRows($sortField);
                                    }
                                    $config->addComponent($sorter);
                                }
                                break;
                            }
                        }

                        if ($obj->hasExtension(Versioned::class) && $obj->hasStages() && class_exists(GridFieldSiteTreeState::class)) {
                            $config->addComponent(new GridFieldSiteTreeState());
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
        return str_replace('\\', '-', $class);
    }


    protected function updateList(&$list)
    {
        $owner = $this->getOwner();
        $member = Security::getCurrentUser();
        if (Permission::checkMember($member, $owner->Config()->get('assume_view_all'))) {
            return;
        }
        $count = $list->count();
        if ($count > 10000) {
            return;
        }
        $includeIDs = [0 => 0];
        foreach ($list as $record) {
            if ($record->canEdit($member)) {
                $includeIDs[] = $record->ID;
            }
        }
        $list = $list->filter(['ID' => $includeIDs]);
        return $list;
    }
}
