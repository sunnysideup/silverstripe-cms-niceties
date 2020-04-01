<?php

namespace Sunnysideup\CMSNiceties\Forms;

use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_ActionMenu;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;

use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\HeaderField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\GridFieldArchiveAction;
use SilverStripe\Versioned\Versioned;

// use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
// TODO: use undefinedoffset/sortablegridfield


/**
 * usage:
 *      $fields->addFieldToTab(
 *          'root.RelationFoo',
 *          CMSNicetiesManyManyGridField::create($this, 'RelationFoo')
 *              ->setSortField('SortOrder')
 *              ->setLabelForField('Check this Out')
 *              ->setHasEditRelation(true)
 *              ->setHasUnlink(true)
 *              ->setHasDelete(true)
 *              ->setHasAdd(true)
 *              ->setHasAddExisting(true)
 *              ->setMaxItemsForCheckBoxSet(150)
 *      );
 */

class CMSNicetiesManyManyGridField extends CompositeField
{
    /**
     * the object calling this class, aka the class where we add the fields
     * @var object
     */
    protected $callingObject = null;

    protected $relationName = '';

    /**
     * name of the class that we are linking to
     * @var string
     */
    protected $relationClassName = '';

    protected $sortField = '';

    protected $labelForField = '';

    protected $hasEditRelation = true;

    protected $hasUnlink = true;

    protected $hasDelete = true;

    protected $hasAdd = true;

    protected $hasAddExisting = true;

    protected $maxItemsForCheckBoxSet = 150;

    /**
     * provides a generic Grid Field for Many Many relations
     * @param  DataObject  $callingObject   Name of the Relationship - e.g. MyWidgets
     * @param  string      $relationName  Name of the Relationship - e.g. MyWidgets
     *
     * @return array
     */
    public function __construct($callingObject, $relationName)
    {
        $this->callingObject = $callingObject;
        $this->relationName = $relationName;

        parent::__construct($this->getFieldCollection());
    }

    public function setSortField(string $sortField)
    {
        $this->sortField = $sortField;

        return $this;
    }

    public function setLabelForField(string $labelForField)
    {
        $this->labelForField = $labelForField;

        return $this;
    }

    public function setHasEditRelation(bool $hasEditRelation)
    {
        $this->hasEditRelation = $hasEditRelation;

        return $this;
    }

    public function setHasUnlink(bool $hasUnlink)
    {
        $this->hasUnlink = $hasUnlink;

        return $this;
    }

    public function setHasDelete(bool $hasDelete)
    {
        $this->hasDelete = $hasDelete;

        return $this;
    }

    public function setHasAdd(bool $hasAdd)
    {
        $this->hasAdd = $hasAdd;

        return $this;
    }

    public function setHasAddExisting(bool $hasAddExisting)
    {
        $this->hasAddExisting = $hasAddExisting;

        return $this;
    }

    public function setMaxItemsForCheckBoxSet(int $maxItemsForCheckBoxSet)
    {
        $this->maxItemsForCheckBoxSet = $maxItemsForCheckBoxSet;

        return $this;
    }

    public function getFieldCollection(): array
    {
        $isVersioned = $this->isVersioned();
        $hasCheckboxSet = $this->hasCheckboxSet();
        $this->sortField = $this->getSortField();
        $this->relationClassName = $this->getRelationClassName();

        if (! $this->labelForField) {
            $fieldLabels = Config::inst()->get($this->callingObject->ClassName, 'field_labels');
            $this->labelForField = isset($fieldLabels[$this->relationName]) ? $fieldLabels[$this->relationName] : '';
        }
        $safeLabel = preg_replace('/[^A-Za-z0-9 ]/', '', $this->labelForField);

        $config = GridFieldConfig_RelationEditor::create();

        if ($hasCheckboxSet) {
            $this->hasUnlink = false;
            $this->hasAddExisting = false;
        }

        $gridField = null;
        if($this->hasGridField()) {
            $config->removeComponentsByType(GridField_ActionMenu::class);
            $gridField = GridField::create(
                $this->relationName . 'GridField',
                $this->labelForField,
                $this->callingObject->{$this->relationName}(),
                $config
            );

            //we remove both - just in case the type is unknown.
            $config->removeComponentsByType(GridFieldArchiveAction::class);
            $config->removeComponentsByType(GridFieldDeleteAction::class);

            //deletes
            if ($this->hasDelete) {
                if ($isVersioned) {
                    // $config->addComponent(new GridFieldArchiveAction());
                    // $config->addComponent(new GridFieldDeleteAction($unlink = false));
                } else {
                    $config->addComponent(new GridFieldDeleteAction($unlink = false));
                }
            } elseif ($this->hasUnlink) {
                $config->addComponent(new GridFieldDeleteAction($unlink = true));
            }

            if (! $this->hasAdd) {
                $config->removeComponentsByType(GridFieldAddNewButton::class);
            }

            if (! $this->hasAddExisting) {
                $config->removeComponentsByType(GridFieldAddExistingAutocompleter::class);
            }
            if($hasCheckboxSet) {
                $gridField->setTitle('Added ' . $this->labelForField);
            }
            if ($this->sortField) {
                //todo: add undefinedoffset/sortablegridfield
                // $config->addComponent($sorter = new GridFieldOrderableRows($sortField));
                //may need some help finding relation!
                // $sorter->setCustomRelationName($this->relationName);
            }
        }

        $checkboxSetField = null;
        if ($hasCheckboxSet) {
            $className = $this->relationClassName;
            $checkboxSetField = CheckboxSetField::create(
                $this->relationName,
                'Quick Add / Remove ',
                $className::get()->map()
            );
        }

        $return = [
            HeaderField::create($safeLabel . 'Header', $this->labelForField)
        ];
        if($checkboxSetField) {
            $return[] = $checkboxSetField;
        }
        if($gridField) {
            $return[] = $gridField;
        }

        return $return;

    }

    private function getRelationClassName(): string
    {
        if ($this->relationClassName === '') {
            $hasMany = Config::inst()->get($this->callingObject->ClassName, 'has_many');
            $manyMany = Config::inst()->get($this->callingObject->ClassName, 'many_many');
            $belongsManyMany = Config::inst()->get($this->callingObject->ClassName, 'belongs_many_many');
            foreach ([
                $hasMany,
                $manyMany,
                $belongsManyMany,
            ] as $types) {
                if (isset($types[$this->relationName])) {
                    $typeOptions = $types[$this->relationName];
                    $typeArray = explode('.', $typeOptions);
                    $this->relationClassName = $typeArray[0];
                    break;
                }
            }
        }

        return $this->relationClassName;
    }

    private function isVersioned(): bool
    {
        $this->relationClassName = $this->getRelationClassName();
        if ($this->relationClassName && class_exists($this->relationClassName)) {
            $foreignSingleton = Injector::inst()->get($this->relationClassName);

            return $foreignSingleton->hasExtension(Versioned::class) ? true : false;
        }

        return false;
    }

    private function hasCheckboxSet(): bool
    {
        $this->relationClassName = $this->getRelationClassName();
        if ($this->relationClassName && class_exists($this->relationClassName)) {
            $className = $this->relationClassName;
            return $className::get()->count() < $this->maxItemsForCheckBoxSet;
        }

        return false;
    }

    private function hasGridField(): bool
    {
        //do we need it to edit the relationship?
        if($this->hasEditRelation || $this->hasDelete || $this->hasAdd) {
            return true;
        }

        // do we need it because we do not have a checkboxset?
        if($this->hasCheckboxSet() === false) {
            return true;
        }

        //we can go without!
        return false;
    }

    private function getSortField(): string
    {
        //todo - add undefinedoffset/sortablegridfield
        if (! $this->sortField) {
            $manyManyExtras = Config::inst()->get($this->callingObject->ClassName, 'many_many_extraFields');
            if (isset($manyManyExtras[$this->relationName])) {
                foreach ($manyManyExtras[$this->relationName] as $field => $tempType) {
                    if (strtolower($tempType) === 'int') {
                        $this->sortField = $field;
                    }
                }
            }
        }

        return $this->sortField;
    }
}
