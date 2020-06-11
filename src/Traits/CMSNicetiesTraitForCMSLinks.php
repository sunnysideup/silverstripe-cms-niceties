<?php

namespace Sunnysideup\CMSNiceties\Traits;

use SilverStripe\Admin\ModelAdmin;
// use SilverStripe\Forms\GridField\GridFieldArchiveAction;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Control\Director;
use SilverStripe\Forms\HTMLReadonlyField;

trait CMSNicetiesTraitForCMSLinks
{
    public function CMSEditLink(): string
    {
        if ($this instanceof SiteTree) {
            return Director::absoluteBaseURL() . '/admin/pages/edit/show/' . $this->ID . '/';
        } else {
            $cont = $this->myModelAdminController();
            if($cont) {
                return $cont->Link() .
                        $this->sanitisedClassName() . '/EditForm/field/' .
                        $this->sanitisedClassName() . '/item/' . $this->ID .
                        '/edit';
            }
        }

        return '404-cms-edit-link-not-found';
    }

    public function CMSEditLinkField(string $relName, string $name = ''): HTMLReadonlyField
    {
        $obj = $this->{$relName}();
        if(! $name) {
            $nameOptions = $this->fieldLabels();
            $name = $nameOptions[$relName] ??  $nameOptions[$relName.'ID'] ?? 'error';
        }
        if($obj && $obj->exists()) {
            $value = '<a href="'.$obj->CMSEditLink().'">'.$obj->getTitle().'</a>';
        } else {
            $value = '<em>(none)</em>';
        }
        return HTMLReadonlyField::create(
            $relName.'Link',
            $name,
            $value
        );
    }

    public function CMSAddLink(): string
    {
        if ($this instanceof SiteTree) {
            return parent::CMSListLink();
        } elseif ($cont = $this->myModelAdminController()) {
            return $cont->Link() .
            $this->sanitisedClassName() . '/EditForm/field/' .
            $this->sanitisedClassName() . '/item/new';
        }
        return '404-cms-add-link-not-found';
    }

    public function CMSListLink(): string
    {
        if ($this instanceof SiteTree) {
            return parent::CMSListLink();
        } elseif ($cont = $this->myModelAdminController()) {
            return $cont->Link() . $this->sanitisedClassName();
        }

        return '404-cms-list-link-not-found';
    }

    /**
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
    protected function sanitisedClassName(): string
    {
        if($this->hasMethod('classNameForModelAdmin')) {
            $className =  $this->classNameForModelAdmin();
        } else {
            $className = $this->ClassName;
        }
        return str_replace('\\', '-', $className);
    }
}
