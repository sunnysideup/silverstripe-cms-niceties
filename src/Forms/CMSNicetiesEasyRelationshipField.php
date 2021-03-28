<?php

namespace Sunnysideup\CMSNiceties\Forms;

use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_ActionMenu;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;

use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\HeaderField;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\GridFieldArchiveAction;
use SilverStripe\Versioned\Versioned;
use SilverStripe\Versioned\VersionedGridFieldDetailForm;

// use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
// TODO: use undefinedoffset/sortablegridfield


/**
 * usage:
 *      $fields->addFieldToTab(
 *          'Root.RelationFoo',
 *          CMSNicetiesManyManyGridField::create($this, 'RelationFoo')
 *              ->setSortField('SortOrder')
 *              ->setLabelForField('Check this Out')
 *              ->setHasEditRelation(true)
 *              ->setHasUnlink(true)
 *              ->setHasDelete(true)
 *              ->setHasAdd(true)
 *              ->setHasAddExisting(true)
 *              ->setMaxItemsForCheckBoxSet(150)
 *              ->setDataColumns(['Title' => 'My Title'])
 *              ->setSearchFields(['Title' => 'My Title'])
 *              ->setSearchOutputFormat('')
 *      );
 */

class CMSNicetiesEasyRelationshipField extends CompositeField
{
    /**
     * the object calling this class, aka the class where we add the fields
     * @var object
     */
    protected $callingObject;

    /**
     * name of the relations e.g. Members as defined in has_many or many_many
     * @var string
     */
    protected $relationName = '';

    /**
     * name of the class that we are linking to
     * @var string
     */
    protected $relationClassName = '';

    /**
     * name of the sort field used
     * works with:
     *  - UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows
     * @var string
     */
    protected $sortField = '';

    /**
     * heading above field
     * @var string
     */
    protected $labelForField = '';

    /**
     * name for Add - e.g. My Product resulting in a button "Add My Product"
     * @var string
     */
    protected $addLabel = '';

    /**
     * should the relationship be editable in the form?
     * @var bool
     */
    protected $hasEditRelation = true;

    /**
     * can the link be removed?
     * @var bool
     */
    protected $hasUnlink = true;

    /**
     * can the linked item be deleted?
     * @var bool
     */
    protected $hasDelete = true;

    /**
     * can new items be added?
     * @var bool
     */
    protected $hasAdd = true;

    /**
     * can existing items be linked?
     * @var bool
     */
    protected $hasAddExisting = true;

    /**
     * @var int
     */
    protected $maxItemsForCheckBoxSet = 300;

    /**
     * data columns
     * @var array
     */
    protected $dataColumns = [];

    /**
     * data columns
     * @var array
     */
    protected $searchFields = [];

    /**
     * data columns
     * @var string
     */
    protected $searchOutputFormat = '';

    /**
     * @var GridFieldConfig|null
     */
    private $gridFieldConfig;

    /**
     * @var GridField|null
     */
    private $gridField;

    /**
     * @var CheckboxSetField|null
     */
    private $checkboxSetField;

    /**
     * @var DataList|null
     */
    private $dataListForCheckboxSetField;

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

        parent::__construct();
        $this->checkIfFieldsHaveBeenBuilt();
    }

    public function doBuild($force = false)
    {
        if ($this->listIsEmpty() || $force) {
            $this->getChildren();
        }
    }

    public function setSortField(string $sortField): self
    {
        $this->checkIfFieldsHaveBeenBuilt();
        $this->sortField = $sortField;

        return $this;
    }

    public function setLabelForField(string $labelForField): self
    {
        $this->checkIfFieldsHaveBeenBuilt();
        $this->labelForField = $labelForField;

        return $this;
    }

    public function setAddLabel(string $addLabel): self
    {
        $this->checkIfFieldsHaveBeenBuilt();
        $this->addLabel = $addLabel;

        return $this;
    }

    public function setHasEditRelation(bool $hasEditRelation): self
    {
        $this->checkIfFieldsHaveBeenBuilt();
        $this->hasEditRelation = $hasEditRelation;

        return $this;
    }

    public function setHasUnlink(bool $hasUnlink): self
    {
        $this->checkIfFieldsHaveBeenBuilt();
        $this->hasUnlink = $hasUnlink;

        return $this;
    }

    public function setHasDelete(bool $hasDelete): self
    {
        $this->checkIfFieldsHaveBeenBuilt();
        $this->hasDelete = $hasDelete;

        return $this;
    }

    public function setHasAdd(bool $hasAdd): self
    {
        $this->checkIfFieldsHaveBeenBuilt();
        $this->hasAdd = $hasAdd;

        return $this;
    }

    public function setHasAddExisting(bool $hasAddExisting): self
    {
        $this->checkIfFieldsHaveBeenBuilt();
        $this->hasAddExisting = $hasAddExisting;

        return $this;
    }

    public function setMaxItemsForCheckBoxSet(int $maxItemsForCheckBoxSet): self
    {
        $this->checkIfFieldsHaveBeenBuilt();
        $this->maxItemsForCheckBoxSet = $maxItemsForCheckBoxSet;

        return $this;
    }

    public function setDataColumns(array $dataColumns): self
    {
        $this->checkIfFieldsHaveBeenBuilt();
        $this->dataColumns = $dataColumns;

        return $this;
    }

    public function setSearchFields(array $searchFields): self
    {
        $this->checkIfFieldsHaveBeenBuilt();
        $this->searchFields = $searchFields;

        return $this;
    }

    public function setSearchOutputFormat(string $searchOutputFormat): self
    {
        $this->searchOutputFormat = $searchOutputFormat;

        return $this;
    }

    public function setDataListForCheckboxSetField(DataList $list): self
    {
        $this->dataListForCheckboxSetField = $list;

        return $this;
    }

    public function getDetailedFields()
    {
        $this->doBuild();
        $type = $this->getRelationClassName();
        $obj = $type::create();
        $defaultFields = $obj->getCMSFields();

        $this->setDetailedFields($defaultFields);

        return $defaultFields;
    }

    public function setDetailedFields(FieldList $fields)
    {
        $this->doBuild();
        $this->getDetailedForm()->setFields($fields);
    }

    /**
     * @return FieldList
     */
    public function getChildren()
    {
        if ($this->listIsEmpty()) {
            $isVersioned = $this->isVersioned();
            $hasCheckboxSet = $this->hasCheckboxSet();
            $this->sortField = $this->getSortField();
            $this->relationClassName = $this->getRelationClassName();

            if ($this->labelForField === '') {
                $fieldLabels = Config::inst()->get($this->callingObject->ClassName, 'field_labels');
                $this->labelForField = isset($fieldLabels[$this->relationName]) ? $fieldLabels[$this->relationName] : '';
            }
            $safeLabel = preg_replace('#[^A-Za-z0-9 ]#', '', $this->labelForField);

            $this->getGridFieldConfig = $this->getGridFieldConfig();

            if ($hasCheckboxSet) {
                $this->hasUnlink = false;
                $this->hasAddExisting = false;
            }

            $this->gridField = null;
            if ($this->hasGridField()) {
                $this->getGridFieldConfig->removeComponentsByType(GridField_ActionMenu::class);
                $this->gridField = GridField::create(
                    $this->relationName . 'GridField',
                    $this->labelForField,
                    $this->callingObject->{$this->relationName}(),
                    $this->getGridFieldConfig
                );

                //we remove both - just in case the type is unknown.
                $this->getGridFieldConfig->removeComponentsByType(GridFieldArchiveAction::class);
                $this->getGridFieldConfig->removeComponentsByType(GridFieldDeleteAction::class);

                //deletes
                if ($this->hasDelete) {
                    if ($isVersioned) {
                        // $this->getGridFieldConfig->addComponent(new GridFieldArchiveAction());
                        // $this->getGridFieldConfig->addComponent(new GridFieldDeleteAction($unlink = false));
                    } else {
                        $this->getGridFieldConfig->addComponent(new GridFieldDeleteAction($unlink = false));
                    }
                } elseif ($this->hasUnlink) {
                    $this->getGridFieldConfig->addComponent(new GridFieldDeleteAction($unlink = true));
                }

                if ($this->hasAdd) {
                    if ($this->addLabel !== '') {
                        $this->getGridFieldConfig->getComponentsByType(GridFieldAddNewButton::class)->first()->setButtonName('Add ' . $this->addLabel);
                    }
                } else {
                    $this->getGridFieldConfig->removeComponentsByType(GridFieldAddNewButton::class);
                }

                if (! $this->hasAddExisting) {
                    $this->getGridFieldConfig->removeComponentsByType(GridFieldAddExistingAutocompleter::class);
                }
                if ($hasCheckboxSet) {
                    $this->gridField->setTitle('Added ' . $this->labelForField);
                }
                if ($this->sortField !== '') {
                    $classA = 'UndefinedOffset\\SortableGridField\\Forms\\GridFieldSortableRows';
                    if (class_exists($classA)) {
                        $this->getGridFieldConfig->addComponent($sorter = new $classA($this->sortField));
                        $sorter->setCustomRelationName($this->relationName);
                    }
                }
                if (count($this->dataColumns) > 0) {
                    $dataColumns = $this->getGridFieldConfig->getComponentByType(GridFieldDataColumns::class);
                    if ($dataColumns) {
                        $dataColumns->setDisplayFields($this->dataColumns);
                    }
                }
                if (count($this->searchFields) > 0) {
                    $autocompleter = $this->getGridFieldConfig->getComponentByType(GridFieldAddExistingAutocompleter::class);
                    if ($autocompleter) {
                        $autocompleter->setSearchFields($this->searchFields);
                    }
                }
                if ($this->searchOutputFormat !== '') {
                    $autocompleter = $this->getGridFieldConfig->getComponentByType(GridFieldAddExistingAutocompleter::class);
                    if ($autocompleter) {
                        $autocompleter->setResultsFormat($this->searchOutputFormat);
                    }
                }
            }

            $this->checkboxSetField = null;
            if ($hasCheckboxSet) {
                $className = $this->relationClassName;
                $obj = Injector::inst()->get($className);
                if ($this->dataListForCheckboxSetField === null) {
                    $this->dataListForCheckboxSetField = $className::get();
                }
                if ($obj->hasMethod('getTitleForList')) {
                    $list = $this->dataListForCheckboxSetField->map('ID', 'getTitleForList');
                } else {
                    $list = $this->dataListForCheckboxSetField->map('ID', 'Title');
                }
                $this->checkboxSetField = CheckboxSetField::create(
                    $this->relationName,
                    'Add / Remove',
                    $list
                );
            }

            $fieldsArray = [
                HeaderField::create($safeLabel . 'Header', $this->labelForField, 1),
            ];
            if ($this->gridField !== null) {
                $fieldsArray[] = $this->gridField;
            }
            if ($this->checkboxSetField !== null) {
                $fieldsArray[] = $this->checkboxSetField;
            }
            $this->children = FieldList::create($fieldsArray);
            //important - as setChildren does more than just setting variable...
            $this->setChildren($this->children);
        }
        return $this->children;
    }

    public function getGridFieldConfig()
    {
        if ($this->gridFieldConfig === null) {
            $this->gridFieldConfig = GridFieldConfig_RelationEditor::create();
        }

        return $this->gridFieldConfig;
    }

    public function getGritField()
    {
        return $this->gridField;
    }

    public function getCheckboxSetField()
    {
        return $this->checkboxSetField;
    }

    protected function listIsEmpty(): bool
    {
        if (empty($this->children)) {
            return true;
        } elseif ($this->children instanceof FieldList && $this->children->count() === 0) {
            return true;
        }

        return false;
    }

    protected function checkIfFieldsHaveBeenBuilt()
    {
        if ($this->listIsEmpty()) {
            //all good
        } else {
            user_error('There is an error in the sequence of your logic. The fields have already been built!');
        }
    }

    /**
     * @return GridFieldDetailForm|VersionedGridFieldDetailForm
     */
    protected function getDetailedForm()
    {
        $this->doBuild();
        $this->getGridFieldConfig = $this->getGridFieldConfig();
        return $this->getGridFieldConfig->getComponentByType(GridFieldDetailForm::class);
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
        if ($this->relationClassName && class_exists($this->relationClassName)) {
            return $this->relationClassName;
        }
        user_error('Can not find related class: ' . $this->relationClassName);
    }

    private function isVersioned(): bool
    {
        $this->relationClassName = $this->getRelationClassName();
        $foreignSingleton = Injector::inst()->get($this->relationClassName);

        return (bool) $foreignSingleton->hasExtension(Versioned::class);
    }

    private function hasCheckboxSet(): bool
    {
        if ($this->callingObject->canEdit()) {
            $this->relationClassName = $this->getRelationClassName();
            $className = $this->relationClassName;

            return $className::get()->count() < $this->maxItemsForCheckBoxSet;
        }

        return false;
    }

    private function hasGridField(): bool
    {
        //do we need it to edit the relationship?
        if ($this->hasEditRelation || $this->hasDelete || $this->hasAdd || $this->sortField) {
            return true;
        }
        // do we need it because we do not have a checkboxset?
        //we can go without!
        return !$this->hasCheckboxSet();
    }

    private function getSortField(): string
    {
        //todo - add undefinedoffset/sortablegridfield
        if ($this->sortField === '') {
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
