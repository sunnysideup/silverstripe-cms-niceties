<?php


namespace Sunnysideup\CMSNiceties\Traits;

use SilverStripe\Core\Injector\Injector;
use SilverStripe\Versioned\Versioned;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_ActionMenu;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
// use SilverStripe\Forms\GridField\GridFieldArchiveAction;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\CMS\Model\SiteTree;

trait CMSNicetiesTraitForRightTitles
{


    public function addRightTitles($fields)
    {
        $rightFieldDescriptions = $this->Config()->get('field_labels_right');
        if (is_array($rightFieldDescriptions) && count($rightFieldDescriptions)) {
            foreach ($rightFieldDescriptions as $field => $desc) {
                $formField = $fields->DataFieldByName($field);
                if (! $formField) {
                    $formField = $fields->DataFieldByName($field.'ID');
                }
                if ($formField) {
                    $formField->setDescription($desc);
                }
            }
        }
        //...
    }
}
