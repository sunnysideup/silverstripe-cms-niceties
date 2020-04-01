<?php


namespace Sunnysideup\CMSNiceties\Api;

use SilverStripe\Core\Injector\Injector;
use SilverStripe\Core\Config\Config;
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


/**
 * usage:
 *      $fields->addFieldToTab(
 *          'root.RelationFoo',
 *          CMSNicetiesManyManyGridField::create($this, 'RelationFoo')
 *              ->setSortField('SortOrder')
 *              ->setLabelForField('Check this Out')
 *              ->setHasUnlink(true)
 *              ->setHasDelete(true)
 *              ->setHasAdd(true)
 *              ->setHasAddExisting(true)
 *              ->setMaxItemsForCheckBoxSet(50)
 *          )
 *      );
 */

class CMSNicetiesManyManyGridField extends CompositeField
{



    /**
     * the object calling this class, aka the class where we add the fields
     * @var string
     */
    protected $callingObject = '';

    protected $relationName = '';

    protected $type = '';

    protected $sortField = '';

    public function setSortField(string $sortField)
    {
        $this->sortField = $sortField;

        return $this;
    }
    protected $labelForField = '';

    public function setLabelForField(string $labelForField)
    {
        $this->labelForField = $labelForField;

        return $this;
    }

    protected $hasUnlink = true;

    public function setHasUnlink(bool $hasUnlink)
    {
        $this->hasUnlink = $hasUnlink;

        return $this;
    }

    protected $hasDelete = true;


    public function setHasDelete(bool $hasDelete)
    {
        $this->hasDelete = $hasDelete;

        return $this;
    }

    protected $hasAdd = true;


    public function setHasAdd(bool $hasAdd)
    {
        $this->hasAdd = $hasAdd;

        return $this;
    }

    protected $hasAddExisting = true;

    public function setHasAddExisting(bool $hasAddExisting)
    {
        $this->hasAddExisting = $hasAddExisting;

        return $this;
    }


    protected $maxItemsForCheckBoxSet = 100;

    public function setMaxItemsForCheckBoxSet(int $maxItemsForCheckBoxSet)
    {
        $this->maxItemsForCheckBoxSet = $maxItemsForCheckBoxSet;

        return $this;
    }

    /**
     * provides a generic Grid Field for Many Many relations
     * @param  string  $this->relationName   Name of the Relationship - e.g. MyWidgets
     *
     * @return array
     */
    public function __construct($callingObject, $relationName)
    {
        $this->callingObject = $callingObject;
        $this->relationName = $relationName;

        parent::__construct($this->getFieldCollection());
    }

    public function getFieldCollection() : array
    {
        $isVersioned = $this->isVersioned();
        $hasCheckboxSet = $this->hasCheckboxSet();
        $this->sortField = $this->getSortField();
        $this->type = $this->foreignType();

        if (! $this->labelForField) {
            $fieldLabels = Config::inst()->get($this->callingObject->ClassName, 'field_labels');
            $this->labelForField = isset($fieldLabels[$this->relationName]) ? $fieldLabels[$this->relationName] : '';
        }
        $safeLabel = preg_replace("/[^A-Za-z0-9 ]/", '', $this->labelForField);

        $config = GridFieldConfig_RelationEditor::create();

        if ($hasCheckboxSet) {
            $this->hasUnlink = false;
            $config->removeComponentsByType(GridFieldAddExistingAutocompleter::class);
        }
        if ($this->sortField) {
            //todo: add undefinedoffset/sortablegridfield
            // $config->addComponent($sorter = new GridFieldOrderableRows($sortField));
            //may need some help finding relation!
            // $sorter->setCustomRelationName($this->relationName);
        }


        $config->removeComponentsByType(GridField_ActionMenu::class);
        $gridField = GridField::create(
            $this->relationName.'GridField',
            $this->labelForField,
            $this->callingObject->{$this->relationName}(),
            $config
        );

        //we remove both - just in case the type is unknown.
        $config->removeComponentsByType(GridFieldArchiveAction::class);
        $config->removeComponentsByType(GridFieldDeleteAction::class);

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

        if ($hasCheckboxSet) {
            $className = $this->type;
            return [
                HeaderField::create($safeLabel.'Header', $this->labelForField),
                CheckboxSetField::create(
                    $this->relationName,
                    'Quick Add / Remove ',
                    $className::get()->map()
                ),
                $gridField->setTitle('Added '.$this->labelForField),
            ];
        } else {
            return [
                HeaderField::create($safeLabel.'Header', $this->labelForField),
                $gridField,
            ];
        }
    }

    private function foreignType() :string
    {
        if($this->type === '') {

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
                    $this->type = $typeArray[0];
                    break;
                }
            }
        }

        return $this->type;
    }

    private function isVersioned() :bool
    {
        $this->type = $this->foreignType();
        if ($this->type && class_exists($this->type)) {
            $foreignSingleton = Injector::inst()->get($this->type);

            return $foreignSingleton->hasExtension(Versioned::class) ? true : false;
        }

        return false;
    }

    private function hasCheckboxSet() :bool
    {
        $this->type = $this->foreignType();
        if ($this->type && class_exists($this->type)) {
            $className = $this->type;
            return $className::get()->count() < $this->maxItemsForCheckBoxSet;
        }

        return false;
    }

    private function getSortField() :string
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
