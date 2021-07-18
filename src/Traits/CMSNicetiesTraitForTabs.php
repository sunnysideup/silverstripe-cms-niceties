<?php

namespace Sunnysideup\CMSNiceties\Traits;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Tab;

trait CMSNicetiesTraitForTabs
{
    public function addSeparator(FieldList $fields, string $name, ?string $after = 'Main')
    {
        if (false !== $after) {
            $tab = Tab::create($name, '|');
            $fields->insertAfter($tab, $after);
        } else {
            $fields->addFieldsToTab(
                'Root.' . $name,
                []
            );
            $fields->fieldByName('Root.' . $name)->setTitle('|');
        }
    }

    public function addTab(FieldList $fields, string $name, ?string $after = 'Main')
    {
        // add spaces between capitals
        $items = preg_split('#(?=[A-Z])#', $name);
        $title = is_array($items) ? trim(implode(' ', $items)) : $name;
        if (false !== $after) {
            if (! $this->isArchived()) {
                $fields->removeFieldFromTab(
                    'Root',
                    $name
                );
                $tab = Tab::create($name, $title);
                $fields->insertAfter($tab, $after);
            }
        } else {
            $fields->addFieldsToTab(
                'Root.' . $name,
                []
            );
            $fields->fieldByName('Root.' . $name)->setTitle($title);
        }
    }
}
