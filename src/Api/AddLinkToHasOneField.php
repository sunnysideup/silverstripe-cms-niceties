<?php

namespace Sunnysideup\CMSNiceties\Api;

use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\FormField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBHTMLVarchar;

class AddLinkToHasOneField
{
    private const GET_CMS_API_CLASS_NAME = 'Sunnysideup\\CmsEditLinkField\\Api\\CMSEditLinkAPI';
    public static function add_link(?FormField $field = null, ?DataObject $object = null)
    {
        if (!$field || !$object) {
            return;
        }
        $dbFieldNameWithID = $field->getName();
        if (substr($dbFieldNameWithID, -2) === 'ID') {
            $dbFieldName = substr((string) $dbFieldNameWithID, 0, -2);
        } else {
            $dbFieldName = $dbFieldNameWithID;
        }
        $options = $object->config()->get('has_one');
        $className = $options[$dbFieldName] ?? '';
        $linkAsHtml = '';
        if ($className && class_exists($className)) {
            $apiClassName = self::GET_CMS_API_CLASS_NAME;
            $hasApi = class_exists($apiClassName);
            $linkedObject = $object->{$dbFieldName}();
            $title = '';
            $link = '';
            $action = '';
            if ($linkedObject && $linkedObject->exists() && $linkedObject->canEdit()) {
                $action = 'edit';
                $title = $linkedObject->getTitle();
                //@TODO: add other methods... see Sunnysideup\CmsEditLinkField\Forms\Fields\CMSEditLinkField
                if ($linkedObject->hasMethod('CMSEditLink')) {
                    $link = $linkedObject->CMSEditLink();
                } elseif ($hasApi) {
                    $link = $apiClassName::find_edit_link_for_object($linkedObject);
                }
            } elseif ($hasApi) {
                if (! $linkedObject || !$linkedObject->exists()) {
                    $linkedObject = Injector::inst()->get($className);
                }
                if ($linkedObject->canCreate()) {
                    $title = $linkedObject->i18n_singular_name();
                    $link = $apiClassName::find_add_link_for_object($linkedObject);
                    $action = 'add';
                } else {
                    $title = $linkedObject->i18n_plural_name();
                    $link = $apiClassName::find_list_link_for_object($linkedObject);
                    $action = 'list';
                }
            }
            if ($link) {
                $linkAsHtml = '
                        <a href="' . $link . '" style="text-decoration: none!important;" target="_blank" rel="noopener noreferrer">✎ '.$action.' ' . $title . '</a><br />';
            }
            $field->setDescription(DBHTMLVarchar::create_field(DBHTMLVarchar::class, $linkAsHtml));

        }
    }

    public static function add_link_to_all_has_one_fields(DataObject $object, $fields)
    {
        $options = $object->config()->get('has_one');
        if (is_array($options)) {
            foreach ($options as $dbFieldName => $className) {
                $fieldA = $fields->dataFieldByName($dbFieldName);
                if ($fieldA) {
                    self::add_link($fieldA, $object);
                } else {
                    $fieldB = $fields->dataFieldByName($dbFieldName . 'ID');
                    if ($fieldB) {
                        self::add_link($fieldB, $object);
                    }
                }
            }
        }
    }
}
