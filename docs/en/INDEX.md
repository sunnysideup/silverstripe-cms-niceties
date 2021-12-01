

Add any of the Traits to any DataObject to add functionality.

# CMSNicetiesEasyRelationshipField

## example usage:

### Basic usage

```php

     $fields->addFieldToTab(
         'Root.RelationFoo',
         CMSNicetiesEasyRelationshipField::create($this, 'RelationFoo')
     );
```
### Full Usage

```php

     $fields->addFieldToTab(
         'Root.RelationFoo',
         CMSNicetiesEasyRelationshipField::create($this, 'RelationFoo')
             ->setSortField('SortOrder')
             ->setLabelForField('Check this Out')
             ->setHasEditRelation(false) //defaults to TRUE
             ->setHasUnlink(false) //defaults to TRUE
             ->setHasDelete(false) //defaults to TRUE
             ->setHasAdd(false) //defaults to TRUE
             ->setHasAddExisting(false) //defaults to TRUE
             ->setMaxItemsForCheckBoxSet(150) //defaults to 150
             ->setDataColumns(['Title' => 'My Title'])
             ->setSearchFields(['Title', 'Header'])
             ->setSearchOutputFormat('')
     );

```
#CMS Niceties Trait For Can Methods

Simply way to manage canCreate, canEdit, canDelete, when the object is owned by
another object (be it a parent or a child, or something else).

For example:
```php
MyDataObject extends DataObject
{
    use Sunnysideup\CMSNiceties\Traits\CMSNicetiesTraitForCanMethods;

    /**
     *
     * @return ArrayList|DataObject
     */
    public function canEditingOwnerObject()
    {
        // return $this->Children();
        return $this->getParent();
    }

    public function canEditingGroupsCodes() : array
    {
        return [
            'MyOTHERCMS_GROUP'
        ];
    }

}
```

# CMS Niceties Trait For CMS Links
Adds: `CMSEditLink`, `CMSAddLink`, and `CMSListLink` to your `DataObject`

For example:
```php
MyDataObject extends DataObject
{
    use Sunnysideup\CMSNiceties\Traits\CMSNicetiesTraitForCMSLinks;



}
```
# CMS Niceties Trait For Right Titles

Adds a way to add right titles / descriptions to form fields in a simpler way using:
`private static $field_labels_right`.

For example:
```php
MyDataObject extends DataObject
{
    use Sunnysideup\CMSNiceties\Traits\CMSNicetiesTraitForRightTitles;

    private static $field_labels_right = [
        'Title' => 'A bit of extra info about title goes here...'
    ];

    public function getCMSField()
    {
        $fields = parent::getCMSFields();
        //...
        $this->addRightTitles($fields);

        return $fields;
     }

}
```


# CMSNicetiesTraitForTabs

Adds separation between tabs or adds a tab to a specific spot

For example:

```php
MyDataObject extends DataObject
{
    use Sunnysideup\CMSNiceties\Traits\CMSNicetiesTraitForTabs;

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $this->addSeparator($fields, 'RandomName', 'Content');

        $this->addTab($fields, 'More CMS Fields', 'RandomName');

        return $fields;
    }

}
```
# CMSNicetiesTraitForValidation

Adds basic validation to required and unique fields. In the example below, any value for
`MyUniuqeField` needs to be unique and the editor will be required to enter any value in
the `MyOtherImportantField` field.

```php
MyDataObject extends DataObject
{
    use Sunnysideup\CMSNiceties\Traits\CMSNicetiesTraitForValidation;

    private static $indexes = [
        'MyUniuqeField' => [
            'type' => 'unique'
        ],
    ];

    private static $required_fields = [
        'MyOtherImportantField',
    ];


}
```
