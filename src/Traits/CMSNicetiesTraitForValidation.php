<?php

namespace Sunnysideup\CMSNiceties\Traits;

// use SilverStripe\Forms\GridField\GridFieldArchiveAction;

trait CMSNicetiesTraitForValidation
{
    public function validate()
    {
        $result = parent::validate();
        $fieldLabels = $this->FieldLabels();
        $indexes = $this->Config()->get('indexes');
        $requiredFields = $this->Config()->get('required_fields');
        if (is_array($requiredFields)) {
            foreach ($requiredFields as $field) {
                $isUniqueEntry =
                    isset($indexes[$field], $indexes[$field]['type'])
                     &&
                    'unique' === $indexes[$field]['type'];
                if ($isUniqueEntry) {
                    $id = (empty($this->ID) ? 0 : $this->ID);
                    $value = $this->{$field};
                    $count = self::get()
                        ->filter([$field => $value])
                        ->exclude(['ID' => $id])
                        ->exists()
                    ;
                    if ($count) {
                        $myName = $fieldLabels[$field];
                        $result->addError(
                            _t(
                                self::class . '.' . $field . '_UNIQUE',
                                $myName . ' needs to be unique'
                            ),
                            'UNIQUE_' . self::class . '.' . $field
                        );
                    }
                }
            }
        }

        return $result;
    }
}
