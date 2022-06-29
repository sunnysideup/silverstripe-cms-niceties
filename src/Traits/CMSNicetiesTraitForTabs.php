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

    public function addTab(FieldList $fields, string $name, ?string $after = 'Main', ?string $title = '')
    {
        // add spaces between capitals
        $items = preg_split('#(?=[A-Z])#', $name);
        if (! $title) {
            $title = is_array($items) ? trim(implode(' ', $items)) : $name;
        }

        if (false !== $after) {
            if (! $this->isArchived()) {
                $tab = $fields->fieldByName('Root.' . $name);
                $fields->removeFieldFromTab(
                    'Root',
                    $name
                );
                if (! $tab) {
                    $tab = Tab::create($name, $title);
                }

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
