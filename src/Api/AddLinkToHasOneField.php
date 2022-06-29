<?php

namespace Sunnysideup\CMSNiceties\Api;

class AddLinkToHasOneField
{
    public static function add_link($field, $object)
    {
        $dbFieldNameWithID = $field->getName();
        $dbFieldName = preg_replace('#ID$#', '', $dbFieldNameWithID);
        $options = $object->stat('has_one');
        $className = $options[$dbFieldName] ?? '';
        if ($className) {
            $value = (int) $object->{$dbFieldName}()->ID;
            if ($value) {
                $linkedObject = $className::get()->byId($value);
                if ($linkedObject && $linkedObject->hasMethod('CMSEditLink')) {
                    $link = $linkedObject->CMSEditLink();
                    if ($link) {
                        $originalDescription = $field->getDescription();
                        $descriptions = [$originalDescription];
                        $descriptions[] = '<a href="' . $link . '">edit ' . $linkedObject->getTitle() . '</a>';
                        $descriptions = array_filter($descriptions);
                        $field->setDescription(implode('<br />', $descriptions));
                    }
                }
            }
        }
    }
}
