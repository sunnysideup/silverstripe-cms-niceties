<?php

namespace Sunnysideup\CMSNiceties\Api;

use SilverStripe\ORM\FieldType\DBHTMLVarchar;

class AddLinkToHasOneField
{
    public static function add_link($field, $object)
    {
        $dbFieldNameWithID = $field->getName();
        $dbFieldName = preg_replace('#ID$#', '', $dbFieldNameWithID);
        $options = $object->config()->get('has_one');
        $className = $options[$dbFieldName] ?? '';
        if ($className) {
            $value = (int) $object->{$dbFieldName}()->ID;
            if ($value) {
                $linkedObject = $className::get()->byId($value);
                if ($linkedObject && $linkedObject->hasMethod('CMSEditLink')) {
                    $link = $linkedObject->CMSEditLink();
                    if ($link) {
                        $linkAsHtml = '
                            <a href="' . $link . '" style="text-decoration: none!important;">✎</a> '.
                            $linkedObject->getTitle();
                        $field->setTitle(DBHTMLVarchar::create($linkAsHtml));
                    }
                }
            }
        }
    }
}
