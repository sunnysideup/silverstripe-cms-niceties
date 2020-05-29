<?php

namespace Sunnysideup\CMSNiceties\Traits;

use SilverStripe\Forms\Tab;

trait CMSNicetiesTraitForTabs
{
    public function addSeparator($fields, string $name, ?string $after = 'Main')
    {
        if ($after !== false) {
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

    public function addTab($fields, string $name, ?string $after = 'Main')
    {
        // add spaces between capitals
        $items = preg_split('/(?=[A-Z])/', $name);
        if (is_array($items)) {
            $title = trim(implode(' ', $items));
        } else {
            $title = $name;
        }
        if ($after !== false) {
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
