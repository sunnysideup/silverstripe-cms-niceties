<?php

namespace Sunnysideup\CMSNiceties\Traits;

use SilverStripe\ORM\ValidationResult;

// use SilverStripe\Forms\GridField\GridFieldArchiveAction;

trait CMSNicetiesTraitForValidation
{
    public function validateForUniqueValues(ValidationResult $result) : ValidationResult
    {
        foreach ($this->Config()->get('indexes') as $index) {
            $isUniqueEntry = isset($index['type']) && 'unique' === $index['type'];
            if ($isUniqueEntry) {
                $fields = $index['columns'] ??[];
                if(count($fields)) {
                    $filter = [];
                    foreach($fields as $field) {
                        $filter[$field] = $this->$field;
                    }

                    $id = (empty($this->ID) ? 0 : $this->ID);
                    // https://stackoverflow.com/questions/63227834/return-self-for-the-return-type-of-a-function-inside-a-php-trait
                    $exists = self::get()
                        ->filter($filter)
                        ->exclude(['ID' => $id])
                        ->exists()
                    ;
                    if ($exists) {
                        $result->addError(
                            _t(
                                self::class . '.' . $index['type'] . '_UNIQUE',
                                $index['type'] . ' needs to be unique'
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
