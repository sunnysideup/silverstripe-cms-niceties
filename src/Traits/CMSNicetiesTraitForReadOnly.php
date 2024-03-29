<?php

namespace Sunnysideup\CMSNiceties\Traits;

use SilverStripe\Forms\FieldList;

// use SilverStripe\Forms\GridField\GridFieldArchiveAction;

trait CMSNicetiesTraitForReadOnly
{
    protected function makeReadonOnlyForCMSFields(FieldList $fields, string $fieldName)
    {
        $field = $fields->dataFieldByName($fieldName);
        if ($field) {
            $fields->replaceField(
                $fieldName,
                $field->performReadonlyTransformation()
            );
        }
    }

    protected function makeReadonOnlyForCMSFieldsAll(FieldList $fields, array $arrayOfFields)
    {
        foreach ($arrayOfFields as $fieldName) {
            $this->makeReadonOnlyForCMSFields($fields, $fieldName);
        }
    }
}
