<?php

namespace Sunnysideup\CMSNiceties\Api;

use SilverStripe\ORM\FieldType\DBHTMLVarchar;

use SilverStripe\ORM\DataObject;

use SilverStripe\Forms\FormField;

class AddLinkToHasOneField
{
    public static function add_link(FormField $field, DataObject $object)
    {
        $dbFieldNameWithID = $field->getName();
        $dbFieldName = substr($dbFieldNameWithID, 0, -2);
        $options = $object->config()->get('has_one');
        $className = $options[$dbFieldName] ?? '';
        if ($className) {
            $linkedObject = $object->{$dbFieldName}();
            $link = '';
            if ($linkedObject->exists() && $linkedObject->hasMethod('CMSEditLink')) {
                $link = $linkedObject->CMSEditLink();
                $linkAsHtml = '
                    <a href="' . $link . '" style="text-decoration: none!important;">✎ edit '.$linkedObject->getTitle().'</a>';
            } elseif($linkedObject->hasMethod('CMSAddLink')) {
                $link = $linkedObject->CMSAddLink();
                $linkAsHtml = '
                    <a href="' . $link . '" style="text-decoration: none!important;">✎ add '.$field->Title().'</a>';
            }
            if ($link) {
                $field->setRightTitle(DBHTMLVarchar::create_field(DBHTMLVarchar::class, $linkAsHtml));
            }
        }
    }
}
