<?php

namespace Sunnysideup\CMSNiceties\Extensions;

use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Core\Extension;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Lumberjack\Forms\GridFieldSiteTreeState;
use SilverStripe\Security\Permission;
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

    private static array $assume_to_allow_all = [
        'ADMIN',
    ];

    private static int $max_records_to_check_for_can_view = 10000;

    public function updateEditForm($form)
    {
        $owner = $this->getOwner();
        if ($this->IsIncludedInExtension()) {
            $obj = Injector::inst()->get($owner->modelClass);
            if ($obj) {
                $sortFields = $owner->config()->get('sort_fields_from_ssu_extension');
                $gridField = $form->Fields()->dataFieldByName($this->sanitiseClassNameHelper($owner->modelClass));
                if ($gridField instanceof GridField) {
                    $config = $gridField->getConfig();
                    $dbFields = $obj->config()->get('db');
                    foreach ($sortFields as $sortField) {
                        if (isset($dbFields[$sortField])) {
                            // This is just a precaution to ensure we got a GridField from dataFieldByName() which you should have
                            if (! $config->getComponentByType(GridFieldSortableRows::class)) {
                                $obj = $owner->modelClass::singleton();
                                if ($obj->hasExtension(Versioned::class) && $this->hasLiveVersionForObject($obj)) {
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

        return $form;
    }

    /**
     * Sanitise a model class' name for inclusion in a link
     */
    protected function sanitiseClassNameHelper(string $class): string
    {
        return str_replace('\\', '-', $class);
    }

    protected function updateList(&$list)
    {
        $owner = $this->getOwner();
        if ($this->IsIncludedInExtension() && ! Permission::check($owner->config()->get('assume_to_allow_all'))) {
            $count = $list->count();
            $limit = $owner->config()->get('max_records_to_check_for_can_view');
            $ids = [0 => 0];
            if ($count > $limit) {
                $list = $list->limit($limit);
            }
            foreach ($list as $record) {
                if ($record->canView()) {
                    $ids[] = $record->ID;
                }
            }
            $list = $list->filter(['ID' => $ids]);
        }
    }

    protected function IsIncludedInExtension(): bool
    {
        $owner = $this->getOwner();
        $excludedModelAdmins = $owner->config()->get('excluded_modeladmins_from_ssu_extension');
        if (! in_array(get_class($owner), $excludedModelAdmins, true)) {
            $excludedModels = $owner->config()->get('excluded_models_from_ssu_extension');
            if (! in_array($owner->modelClass, $excludedModels, true)) {
                return true;
            }
        }
        return false;
    }

    protected function hasLiveVersionForObject($obj): bool
    {
        $extensions = $obj->getExtensionInstances();
        return ! isset($extensions[Versioned::class . '.versioned']);
    }
}
