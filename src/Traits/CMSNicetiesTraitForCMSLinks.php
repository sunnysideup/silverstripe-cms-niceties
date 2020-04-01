<?php

namespace Sunnysideup\CMSNiceties\Traits;

use SilverStripe\Admin\ModelAdmin;
// use SilverStripe\Forms\GridField\GridFieldArchiveAction;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\Injector\Injector;

trait CMSNicetiesTraitForCMSLinks
{
    public function CMSEditLink()
    {
        if ($this instanceof SiteTree) {
            return 'admin/pages/edit/show/' . $this->ID . '/';
        }
        if ($cont = $this->myModelAdminController()) {
            return $cont->Link() .
                    $this->sanitisedClassName() . '/EditForm/field/' .
                    $this->sanitisedClassName() . '/item/' . $this->ID .
                    '/edit';
        }
    }

    public function CMSAddLink()
    {
        if (! $this instanceof SiteTree) {
            if ($cont = $this->myModelAdminController()) {
                return $cont->Link() .
                    $this->sanitisedClassName() . '/EditForm/field/' .
                    $this->sanitisedClassName() . '/item/new';
            }
        }
    }

    public function CMSListLink()
    {
        if (! $this instanceof SiteTree) {
            if ($cont = $this->myModelAdminController()) {
                return $cont->Link() . $this->sanitisedClassName();
            }
        }
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
    protected function sanitisedClassName()
    {
        return str_replace('\\', '-', $this->ClassName);
    }
}
