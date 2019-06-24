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
use SilverStripe\Admin\ModelAdmin;

trait CMSNicetiesTraitForCMSLinks
{


    public function CMSEditLink()
    {
        if ($this instanceof SiteTree) {
            return 'admin/pages/edit/show/'.$this->ID.'/';
        } else {
            if ($cont = $this->myModelAdminController()) {
                return $cont->Link().
                    $this->sanitisedClassName().'/EditForm/field/'.
                    $this->sanitisedClassName().'/item/'.$this->ID.
                    '/edit';
            }
        }
    }

    public function CMSAddLink()
    {
        if (! $this instanceof SiteTree) {
            if ($cont = $this->myModelAdminController()) {
                return $cont->Link().
                    $this->sanitisedClassName().'/EditForm/field/'.
                    $this->sanitisedClassName().'/item/new';
            }
        }
    }

    public function CMSListLink()
    {
        if (! $this instanceof SiteTree) {
            if ($cont = $this->myModelAdminController()) {
                return $cont->Link().$this->sanitisedClassName();
            }
        }
    }

    /**
     *
     * @return ModelAdmin|null
     */
    protected function myModelAdminController()
    {
        $modelAdminClassName = $this->Config()->get('primary_model_admin_class');
        if ($modelAdminClassName) {
            return Injector::inst()->get($modelAdminClassName);
        }
    }

    /**
     * Sanitise a model class' name for inclusion in a link
     *
     * @param string $class
     * @return string
     */
    protected function sanitisedClassName()
    {
        return str_replace('\\', '-', $this->ClassName);
    }
}
