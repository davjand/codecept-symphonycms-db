# Readme

A codeception module to facilitate integration with Symphony CMS (http://getsymphony.com).

This is intended to be used in conjunction with the existing Db module for setup/teardown of databases. It currently offers functions putting your own fixture data into the database


**2015-10-29 - Version: 0.1**

 - Initial Release



## Setup





## Functions


### symHaveInDatabase($sectionHandle,$data)

$data should be a multi-dimensional array in the format

```php
array(
    array(
        field_name: value
        field_name: value
        ),
    etc
    );
```

It *returns* an array of the inserted IDs


### symHaveInDatabaseSingle($sectionHandle,$data)

Similar to symHaveInDatabase except exceptions only one entry.
Accepts the data in key value format.


### symUpdateDatabaseRecord($sectionHandle,$entryId,$data)

This will update an existing record with the data passed.
Accepts the data in key value format.
