

Add any of the Traits to any DataObject to add functionality.

# CMSNicetiesEasyRelationshipField

## example usage:

### Basic usage

     $fields->addFieldToTab(
         'root.RelationFoo',
         CMSNicetiesManyManyGridField::create($this, 'RelationFoo')
     );

### Full Usage

```php


     $fields->addFieldToTab(
         'root.RelationFoo',
         CMSNicetiesManyManyGridField::create($this, 'RelationFoo')
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
