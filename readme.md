# Readme

A codeception module to facilitate integration with Symphony CMS (http://getsymphony.com).

This is intended to be used in conjunction with the existing Db module for setup/teardown of databases. It currently offers functions putting your own fixture data into the database

**2015-10-30 - Version: 0.2**

 - Renamed haveInDatabseFunctions
 - Added YML loading functionality

**2015-10-29 - Version: 0.1**

 - Initial Release



## Setup

The module has no configuration parameters however you must define the symphony DOCROOT within your *bootstrap.php* file for this to work

An older version of symphony is included in the repository (the repo was developed for testing an old repo) however by changing the path below to **your included symphony source** that you are testing you can use that.

```php
define('DOCROOT',__DIR__ . '/../symphony/2.3.6/');
```

This will access the database through the settings in your **manifest/config.php** file.

Please note that in some environments, you need to have your host as **127.0.0.1** for the database rather than localhost for using with codeception.


## Config

Enable in your configuration file as below. You can define a custom path to your fixtures folder for included yml fixtures

```yml
modules:
    config:
        SymphonyCMSDb:
            fixtures: 'tests/_fixtures'
```


## Functions

## symHaveFixtureInDatabase($fixture)

Load a yml file of data from your fixtures folder (see config above).

Format is

```yml

secton-handle:
    -
        field-name-one: 'value'
        field-name-two: 'value2'
```

Subsequent entries can be associated with previous entries using the format *section:position* as seen in the example below. The position starts at 1, not 0. The section with the relation must **be defined after** the entry it is being related to or an error will be thrown.

NB: It is not currently possible to associate entries in the same section together.

For Example.

```yml
people:
    -
        first-name: 'James'
        last-name: 'Bond'
    -
        first-name: 'Daniel'
        last-name: 'Craig'
dogs:
    -
        name: 'Rover'
        owner: '%people:2%'
```



### symHaveEntriesInDatabase($sectionHandle,$data)

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


### symHaveEntryInDatabase($sectionHandle,$data)

Similar to symHaveInDatabase except exceptions only one entry.
Accepts the data in key value format.


### symUpdateDatabaseRecord($sectionHandle,$entryId,$data)

This will update an existing record with the data passed.
Accepts the data in key value format.
