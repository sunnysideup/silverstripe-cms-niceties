<?php

namespace Sunnysideup\CMSNiceties\Traits;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Tab;

use SilverStripe\Versioned\Versioned;

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

    public function reorderTabs(FieldList $fields, array $tabOrder): FieldList
    {
        $tabs = [];
        foreach ($tabOrder as $tabName => $title) {
            // non-associative array..
            if (intval($tabName) === $tabName) {
                $tabName = $title;
                $items = preg_split('#(?=[A-Z])#', $tabName);
                $title = is_array($items) ? trim(implode(' ', $items)) : $tabName;
            }

            $tabNamePlus = $tabName . 'Tab';

            // fixd existing existing tab
            $tab = $fields->fieldByName('Root.' . $tabName);
            if (! $tab) {
                $tab = $fields->fieldByName('Root.' . $tabNamePlus);
            }

            if (! $tab) {
                $tab = new Tab($tabNamePlus, $tabName);
            }

            $fields->removeByName(['Root.' . $tabName]);
            $fields->removeByName(['Root.' . $tabNamePlus]);
            $fields->removeFieldFromTab('Root', $tabName);
            $fields->removeFieldFromTab('Root', $tabNamePlus);
            $fields->removeFieldsFromTab('Root', [$tabName]);
            $fields->removeFieldsFromTab('Root', [$tabNamePlus]);
            $tab->setTitle($tabName);
            $tab->setName($tabNamePlus);
            $tabs[] = $tab;
            // $fields->addFieldsToTab('Root', $tab);
        }

        // $tabs = array_reverse($tabs);
        foreach ($tabs as $tab) {
            $fields->addFieldToTab('Root', $tab);
        }
    }

    public function addTab(FieldList $fields, string $name, ?string $after = 'Main', ?string $title = '')
    {
        // add spaces between capitals
        if (! $title) {
            $items = preg_split('#(?=[A-Z])#', $name);
            $title = is_array($items) ? trim(implode(' ', $items)) : $name;
        }

        if (null !== $after) {
            if ($this->hasExtension(Versioned::class) && $this->isArchived()) {
                // do nothing
            } else {
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
