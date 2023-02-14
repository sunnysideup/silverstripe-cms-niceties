<?php

namespace Sunnysideup\CMSNiceties\Traits;

// use SilverStripe\Forms\GridField\GridFieldArchiveAction;

trait CMSNicetiesTraitForRightTitles
{
    public function addRightTitles($fields)
    {
        $rightFieldDescriptions = $this->Config()->get('field_labels_right');
        if (is_array($rightFieldDescriptions) && count($rightFieldDescriptions)) {
            foreach ($rightFieldDescriptions as $field => $desc) {
                $formField = $fields->DataFieldByName($field);
                if (! $formField) {
                    $formField = $fields->DataFieldByName($field . 'ID');
                }

                if ($formField) {
                    $formField->setDescription($desc);
                }
            }
        }

        //...
    }
}
