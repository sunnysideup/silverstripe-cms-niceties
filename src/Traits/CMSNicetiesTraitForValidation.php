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


trait CMSNicetiesTraitForValidation
{

    public function validate()
    {
        $result = parent::validate();
        $fieldLabels = $this->FieldLabels();
        $indexes = $this->Config()->get('indexes');
        $requiredFields = $this->Config()->get('required_fields');
        if (is_array($requiredFields)) {
            foreach ($requiredFields as $field) {
                $isUniqueEntry =
                    isset($indexes[$field]) &&
                    isset($indexes[$field]['type']) &&
                    $indexes[$field]['type'] === 'unique'
                ? true : false;
                if ($isUniqueEntry) {
                    $id = (empty($this->ID) ? 0 : $this->ID);
                    $value = $this->$field;
                    $count = self::get()
                        ->filter([$field => $value])
                        ->exclude(['ID' => $id])
                        ->count();
                    if ($count > 0) {
                        $myName = $fieldLabels[$field];
                        $result->addError(
                            _t(
                                self::class.'.'.$field.'_UNIQUE',
                                $myName.' needs to be unique'
                            ),
                            'UNIQUE_'.self::class.'.'.$field
                        );
                    }
                }
            }
        }

        return $result;
    }
}
