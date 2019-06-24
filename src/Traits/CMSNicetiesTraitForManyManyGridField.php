<?php


namespace Sunnysideup\CMSNiceties\Traits;

use SilverStripe\Core\Injector\Injector;
use SilverStripe\Versioned\Versioned;
use SilverStripe\Versioned\GridFieldArchiveAction;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldViewButton;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\GridField\GridField_ActionMenu;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;

use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\CMS\Model\SiteTree;
// use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
// TODO: use undefinedoffset/sortablegridfield
//
trait CMSNicetiesTraitForManyManyGridField
{

    /**
     * provides a generic Grid Field for Many Many relations
     * @param  string  $relationName   Name of the Relationship - e.g. MyWidgets
     * @param  string  $sortField      used sort field
     * @param  string  $title          form field title - can be left blank
     * @param  boolean $hasUnlink      include an unlink option - TRUE by default
     * @param  boolean $hasDelete      include a delete original object option - TRUE by default
     * @param  boolean $hasAdd         include a add a new object option - TRUE by default
     * @param  boolean $hasAddExisting include a has existing object option - TRUE by default
     *
     * @return array
     */
    public function getMyManyManyGridField(
        $relationName,
        $sortField = '',
        $title = '',
        $hasUnlink = true,
        $hasDelete = true,
        $hasAdd = true,
        $hasAddExisting = true
    ) {
        $versioned = $this->isVersionedForMyManyManyGridField($relationName);
        $hasCheckboxSet = $this->hasCheckboxSetForMyManyManyGridField($relationName);
        $sortField = $this->getSortFieldForMyManyManyGridField($relationName, $sortField);
        $type = $this->foreignTypeForMyManyManyGridField($relationName);

        if (! $title) {
            $fieldLabels = $this->Config()->get('field_labels');
            $title = isset($fieldLabels[$relationName]) ? $fieldLabels[$relationName] : '';
        }

        $config = GridFieldConfig_RelationEditor::create();

        if ($hasCheckboxSet) {
            $hasUnlink = false;
            $config->removeComponentsByType(GridFieldAddExistingAutocompleter::class);
        }
        if ($sortField) {
            //todo: add undefinedoffset/sortablegridfield
            // $config->addComponent($sorter = new GridFieldOrderableRows($sortField));
            //may need some help finding relation!
            // $sorter->setCustomRelationName($relationName);
        }


        $config->removeComponentsByType(GridField_ActionMenu::class);
        $gridField = GridField::create(
            $relationName.'GridField',
            $title,
            $this->$relationName(),
            $config
        );

        //we remove both - just in case the type is unknown.
        $config->removeComponentsByType(GridFieldArchiveAction::class);
        $config->removeComponentsByType(GridFieldDeleteAction::class);

        if ($hasDelete) {
            if ($versioned) {
                // $config->addComponent(new GridFieldArchiveAction());
                // $config->addComponent(new GridFieldDeleteAction($unlink = false));
            } else {
                $config->addComponent(new GridFieldDeleteAction($unlink = false));
            }
        } elseif ($hasUnlink) {
            $config->addComponent(new GridFieldDeleteAction($unlink = true));
        }
        if (! $hasAdd) {
            $config->removeComponentsByType(GridFieldAddNewButton::class);
        }
        if (! $hasAddExisting) {
            $config->removeComponentsByType(GridFieldAddExistingAutocompleter::class);
        }

        if ($hasCheckboxSet) {
            return [
                HeaderField::create($title.'Header', $title),
                CheckboxSetField::create(
                    $relationName,
                    'Quick Add / Remove ',
                    $type::get()->map()
                ),
                $gridField->setTitle('Added '.$title),
            ];
        } else {
            return [
                HeaderField::create($title.'Header', $title),
                $gridField
            ];
        }
    }

    private function foreignTypeForMyManyManyGridField($relationName) :string
    {
        $type = '';

        $hasMany = $this->config()->get('has_many');
        $manyMany = $this->config()->get('many_many');
        $belongsManyMany = $this->config()->get('belongs_many_many');
        foreach ([
            $hasMany,
            $manyMany,
            $belongsManyMany
        ] as $types) {
            if (isset($types[$relationName])) {
                $type = $types[$relationName];
                $typeArray = explode('.', $type);
                $type = $typeArray[0];
                break;
            }
        }
        return $type;
    }

    private function isVersionedForMyManyManyGridField($relationName) :bool
    {
        $type = $this->foreignTypeForMyManyManyGridField($relationName);
        if ($type) {
            $foreignSingleton = Injector::inst()->get($type);

            return $foreignSingleton->hasExtension(Versioned::class);
        }
        return false;
    }

    private function hasCheckboxSetForMyManyManyGridField($relationName) :bool
    {
        $type = $this->foreignTypeForMyManyManyGridField($relationName);
        if ($type && class_exists($type)) {
            return $type::get()->count() < 100;
        }

        return false;
    }

    private function getSortFieldForMyManyManyGridField($relationName, $sortField) :string
    {
        //todo - add undefinedoffset/sortablegridfield
        if (! $sortField) {
            $manyManyExtras = $this->Config()->get('many_many_extraFields');
            if (isset($manyManyExtras[$relationName])) {
                foreach ($manyManyExtras[$relationName] as $field => $tempType) {
                    if (strtolower($tempType) === 'int') {
                        $sortField = $field;
                    }
                }
            }
        }
        return null;

        return $sortField;
    }
}
