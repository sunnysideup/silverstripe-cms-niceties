<?php

namespace Sunnysideup\CMSNiceties\Traits;

use SilverStripe\Admin\ModelAdmin;
// use SilverStripe\Forms\GridField\GridFieldArchiveAction;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\HTMLReadonlyField;

trait CMSNicetiesTraitForCMSLinks
{
    public function CMSEditLink(): string
    {
        if ($this instanceof SiteTree) {
            return parent::CMSEditLink();
        }

        $cont = $this->myModelAdminController();
        if ($cont) {
            return $this->editLink($cont);
        }

        return '404-cms-edit-link-not-found';
    }

    public function CMSEditLinkLimited(): string
    {
        $cont = $this->myModelAdminController();
        if ($cont) {
            return $this->editLink($cont);
        }

        return '404-cms-edit-link-not-found';
    }

    public function editLink($controller = null): string
    {
        if(! $controller) {
            $controller = $this->myModelAdminController();
        }
        return '/' .
            Controller::join_links(
                $controller->Link(),
                $this->sanitisedClassName(),
                'EditForm',
                'field',
                $this->sanitisedClassName(),
                'item',
                $this->ID,
                'edit'
            );
    }

    public function CMSEditLinkField(string $relName, string $name = ''): HTMLReadonlyField
    {
        $obj = $this->{$relName}();
        if (!$name) {
            $nameOptions = $this->fieldLabels();
            $name = $nameOptions[$relName] ?? $nameOptions[$relName . 'ID'] ?? 'error';
        }

        if ($obj && $obj->exists()) {
            $value = '<a href="' . $obj->CMSEditLink() . '">' . $obj->getTitle() . '</a>';
        } else {
            $value = '<em>(none)</em>';
        }

        return HTMLReadonlyField::create(
            $relName . 'Link',
            $name,
            $value
        );
    }

    public function CMSAddLink(): string
    {
        $controller = $this->myModelAdminController();
        if ($controller) {
            return '/' .
                Controller::join_links(
                    $controller->Link(),
                    $this->sanitisedClassName(),
                    'EditForm',
                    'field',
                    $this->sanitisedClassName(),
                    'item',
                    'new'
                );
        }

        return '404-cms-add-link-not-found';
    }

    public function CMSListLink(): string
    {
        $controller = $this->myModelAdminController();
        if ($controller) {
            return Controller::join_links(
                $controller->Link(),
                $this->sanitisedClassName()
            );
        }

        return '404-cms-list-link-not-found';
    }

    /**
     * @return null|ModelAdmin
     */
    protected function myModelAdminController()
    {
        $modelAdminClassName = $this->Config()->get('primary_model_admin_class');
        $obj = null;
        if ($modelAdminClassName) {
            /** @var null|ModelAdmin $obj */
            $obj = Injector::inst()->get($modelAdminClassName);
        }

        return $obj;
    }

    /**
     * Sanitise a model class' name for inclusion in a link.
     */
    protected function sanitisedClassName(): string
    {
        $className = (string) $this->hasMethod('classNameForModelAdmin') ? $this->classNameForModelAdmin() : $this->ClassName;

        return str_replace('\\', '-', $className);
    }
}
