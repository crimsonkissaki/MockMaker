## MockMaker

An automated entity class "mock" file seeder.

Whether you call them doubles, stubs, mocks, partials, fakes, or something else there are times when a mocking library such as PHPUnit's mockBuilder, Mockery, Prophecy, etc. just doesn't do exactly what you need or want. Sometimes you just _need_ a concrete class implementation to run through the unit test wringer or a full end to end functional unit test suite.

MockMaker aims to simplify the process of generating concrete fake object with a particular emphasis on ORM entities (because that's the original problem I wrote this library so solve).


## Why use MockMaker:

Let's face it, if you've ever inherited a large code base with a couple dozen entity classes that need to quickly be set up for use in unit/functional tests you know how big of a PITA standing those up can be. Jumping back and forth between files, keeping the properties, methods, and entity relationships straight while manually writing up the code to generate those mocks is horribly time consuming. If those entities have non-public properties it makes dynamic data setting annoying as hell. Toss in some heavy association mapping and a few many-to-one relationships that circle around each other and it's enough to make you need a stiff drink.

So, if you are:

 * Porting a legacy database over to an ORM and need to make sure the generated entities will work with your existing unit test suite.
 * Inheriting a project that relies heavily on Doctrine (or another ORM) with a few dozen entity classes, and there's NO unit testing whatsoever.
 * On a project and a schema update just changed all the things!
 * Working on a project that requires extensive functional tests involving database access.
 * Need to stand up valid entities with randomized data for load/unit/functional testing.
 * Efficiently lazy and prefer to let a script do the grunt work.

MockMaker might be able to lend you a hand.


## What MockMaker does:

At its core, MockMaker takes a list of files and/or directories and generates mock 'seeder' files for any instantiable classes. Those mock files can be used to generate on-the-fly instantiations of entity classes for use in testing. Interfaces and Abstracts need not apply.

 * If the property is public, it just sets the value.
 * If the property is private/protected/static it uses reflection to dynamically set those values.
 * If there is a typehinted class or a default value it will be automatically included/hinted at in the default values section to help keep things straight.
 * A default settings option is included that allows for configuration of 'bare bones' object creation for those times when you just need a valid instance, regardless of data.

Flexible and extendable, the seed code can be altered to suit your particular project with relative ease, so after the initial setup you can re-run MockMaker for any new entities that get added in, or existing entities that change with little to no fuss. What's more, once MockMaker has made your files it's done. You don't have to include it in your code base and can use the generated files like any other project class.

Please be aware, I consider MockMaker to be in Beta stage. There is still some functionality that is not completely working,
but I needed to get this up and available for testing with pulling into Composer.


## Installation:

Through Composer:

```bin
    composer require-dev crimsonkissaki/mockmaker:dev-master
```

*NOTE* You will have to update your composer.json file with a repository entry for this github repo as I have not yet uploaded the repo to packagist. I will do so when I'm happy that it will work on most use-cases.

```json
        {
            "type": "vcs",
            "url": "https://github.com/crimsonkissaki/MockMaker.git"
        }
```

## MockMaker Configuration & Usage:

I've tried to include enough configuration options to cover a good majority of use cases.

MockMaker is configured through human-readable settings that should make sense
to just about everyone. Hopefully.

MockMaker supports method chaining, so virtually all of them can be stacked up.
Exceptions to this are the `verifySettings()`, `testRegexPatterns()`, and
`createMocks()` methods since they are for returning actual results based on
configuration settings.

Create a new MockMaker instance.
```php
use MockMaker\MockMaker;

$mocks = new MockMaker();
```

Define files you want mocked.
```php
// @param   string|array    $files  Fully qualified file paths
$mocks->mockTheseEntities($files);
```

Have MockMaker parse directories and find `.php` files for you.
```php
// @param   string|array   $dirs    Fully qualified directory paths
$mocks->mockEntitiesInDirectory($dirs);
```

Tell MockMaker to recursively check through the read directories for files.

_The default setting is `false`_
```php
$mocks->recursively();
```

Tell MockMaker where your project's root path is.

_MockMaker tries to auto-detect this, so you only have to set it if that's failing._
```php
// @param   string  $path   Fully qualified path to your project's root directory
$mocks->setProjectRootPath($path);
```

Have MockMaker create & save the mock files for you.

_If there is no directory specified here, the mock code will be returned
as a string you can copy/paste/stdout from wherever you dump it._
```php
// @param   string  $dir    Fully qualified directory path
$mocks->saveMockFilesIn($dir);
```

Have MockMaker create unit tests for the mock files.

_Unit tests are very basic and only make sure the mocks return
valid instances of the entity class_
```php
// @param   string  $dir    Fully qualified directory path
$mocks->createUnitTests();
```

Have MockMaker save the mock files for you.

_If there is no directory specified here, the mock code will be returned
as a string you can copy/paste/stdout from wherever you dump it._
```php
// @param   string  $dir    Fully qualified directory path
$mocks->saveUnitTestsIn($dir);
```

By default, MockMaker will replicate the directory structure of the read directories you specify
as it's saving files, if it's reading through them recursively.

_If you'd rather they get lumped together into one directory, use this._
```php
$mocks->ignoreDirectoryStructure();
```

By default MockMaker will not overwrite existing files if you've told it to re-mock something
that's already been done. (This is to prevent accidentally overwriting files that have already
been customized with defaults and losing your work.)

_By using this option you avow you're a responsible developer and understand what 'overwrite' means
and what its consequences are._
```php
$mocks->overwriteMockFiles();
```

By default MockMaker will not overwrite existing unit tests if you've told it to re-mock something
that's already been done. (This is to prevent accidentally overwriting files that have already
been customized and losing your work.)

_By using this option you avow you're a responsible developer and understand what 'overwrite' means
and what its consequences are._
```php
$mocks->overwriteUnitTestFiles();
```

Define a regex pattern used to _exclude_ files from being processed. (default allow)

_This will override any files included through `includeFilesWithFormat()`._

_This will be applied to any files obtained through `mockTheseEntities()` or `mockEntitiesInDirectory()`._
```php
// @param   string  $regex   Regex pattern
$mocks->excludeFilesWithFormat($regex);
```

Define a regex pattern used to _include_ files from being processed. (default deny)

_This will be applied to any files obtained through `mockTheseEntities()` or `mockEntitiesInDirectory()`._
```php
// @param   string  $regex   Regex pattern
$mocks->includeFilesWithFormat($regex);
```

If you don't want the default mock name format of {FileName}Mock, then you can
specify a new format here. If you want the file/class name in the string just
put %FileName% somewhere in there.

_e.g. If you want MockedMyEntity to be the class name, use 'Mocked%FileName%'_
```php
// @param   string  $format     Format for mock file names and class names
$mocks->saveMocksWithFileNameFormat($format);
```

MockMaker will make a good effort at determining a proper class namespace for
the mock classes it generates, but that depends on having access to Composer.

If the automatic namespaces are not right, you can specify a 'base namespace'
here.

If you're recursively reading files from a directory with sub-directories, MockMaker should
be able to adjust the namespace accordingly.

_If you don't use PSR-4 or PSR-0 namespaces, you're gonna have a bad time._
```php
// @param   string  $namespace  Base namespace to use for mocks
$mocks->useBaseNamespaceForMocks($namespace);
```

Returns an object with the results of your MockMaker configuration  settings.

_This is good for checking things out before you actually have MockMaker write stuff._
```php
// @return  ConfigData
$mocks->verifySettings();
```

Returns an associative array of files that are filtered out by your regex patterns.

`$results = array( 'include' => [], 'exclude' => [], 'workable' => [] );`
```php
// @return  array
$mocks->testRegexPatterns();
```

Generate the mock file code, and either return it or write it to files.
```php
// @return  string
$mocks->createMocks();
```

## Use Case Examples:

MockMaker is set up in such a way that options can be added in any order, so don't worry about
where in the stack it ends up.

### Mock single file and return code as string:

```php
use MockMaker\MockMaker;

$rootDir = '/Applications/XAMPP/xamppfiles/htdocs/yourproject/src/';
$projectDir = $rootDir . 'Project/Bundle/MyAwesomeBundle/';

$mm = new MockMaker();
$code = $mm->mockTheseEntities($projectDir . 'Entity/TestEntity.php')
    ->createMocks();
    
echo $code;
```

### Mock everything excluding Doctrine's "{EntityName}Repository" classes:

```php
use MockMaker\MockMaker;

$rootDir = '/Applications/XAMPP/xamppfiles/htdocs/yourproject/src/';
$projectDir = $rootDir . 'Project/Bundle/MyAwesomeBundle/';

$mm = new MockMaker();
$mm->mockEntitiesInDirectory($projectDir . 'Entity')
    ->recursively()
    ->exclude('/Repository$/')
    ->saveMockFilesIn($projectDir . 'Tests/Mocks/Entity')
    ->saveUnitTestsIn($projectDir . 'Tests/Mocks/EntityTests')
    ->overwriteMockFiles()
    ->overwriteUnitTestFiles()
    ->createMocks();
```

## Mock File Usage:

### Setting Defaults:

You do have to do *SOME* work, but at least it's kept to a minimum. By default, MockMaker will set up a 'best guess'
array of defaults for any entity properties, regardless of visibility (constant/public/private/protected/static).

```php
return array(
    'propertyName' => 'propertyName value',
    'propertyName2' => 'John Q. Public',
    'propertyName3' => new \stdClass(),
    'propertyName4' => new SimpleEntity(),
);
```

If a default value is typehinted in the setter, assigned in a \__construct(), or set in-line in the class property
declaration, it is included automatically. Change or add the `default` value as appropriate.

This setup allows for special cases of entities that have _conditional_ properties (e.g. a customer entity with a
'disabledOn' property) that, if defined, will influence business logic. Properties that are omitted/deleted from this
array will not be used when setting up a mock unless you specifically tell it to. And if for some reason you _don't_
want one of the default properties to be set, that option is covered later on.

### 'Basic' Mocks:

_When store-brand generic is all you need._

If you just need an object with pre-populated default values using the minimum required properties for viability:

```php
use Path\To\EntityMock;

$mock = EntityMock::getMock();
```

The mock file returns an instance of the class with only the properties defined in the array having your predefined
default values. Properties **NOT** in the array will be ignored.

### 'Advanced' Mocks:

_Because off-the-rack doesn't fit quite right._

This is especially useful for run-time customization of entity values so you can use PHPUnit data providers,
factories, or random data generators to create as many customized entity instances as your tests require.

If you need to override a default value for particular propert(y|ies), or include a property
that is omitted from the defaults array:

```php
use Path\To\EntityMock;
use Path\To\CustomerEntityMock;

// format is 'propertyName' => 'desiredValue'
$properties = array(
    'customerOrders',    // no defined value, defaultly assigned NULL
    'customerId' => 1,
    'customerName' => 'John Doe',
    'customerData' => CustomerEntityMock::getMock()
);
$mock = EntityMock::getMock( $properties );
```

When the mock is returned, the 'propertyName' property in the array will be set to 'desiredValue'. If no value
is set then the property will automatically be set to `NULL`.

But, what if you have a value in the default array because it's one of those things that is
_usually-needed-but-just-not-right-now-dangit_? We've got you covered.

```php
use Path\To\EntityMock;
use Path\To\CustomerEntityMock;

// format is 'propertyName' => 'desiredValue'
$properties = array(
    'customerOrders',
    'customerId' => 1,
    'customerName' => 'John Doe',
    'customerData' => CustomerEntityMock::getMock()
);
$ignore = array( 'customerOrders', 'customerPhone', 'disabledOn' );
$mock = EntityMock::getMock( $properties, $ignore );
```

When the mock is returned, any properties defined in the `$ignore` array will be ... ignored. Even if they're in the defaults array or passed in with the `$properties` array, nothing will happen to them.


## Known Bugs & Issues

 * Custom CodeWorkers and MockFileTemplates haven't been fully tested.
 * Currently not working on packagist, as I haven't uploaded it to them yet. Still testing phase for now.

## Future Improvements:

 * Custom mock/unit test templates and datapoint workers
 * Automatic creation of a 'MockManager' file that allows you to pull in mocks through a single class.
 * ORM awareness:
   * Ability to parse ORM specific annotations for data type hints and class relationships.
 * Resource aware "hereditary" mock generation (dependent on ORM feature):
   * Entity properties that are links to other entities (children classes, relationships, etc) will have their default value set to the mock of that entity.
   * If those mocked entities do not exist it will add them to a queue for mocking.
 * Data seeder:
   * Ability to connect to an existing DB and pull in N records for a generated "data seeder" file, which will allow you to pass in a parameter to a mock construct and get actual valid testing data pre-populated in your mock.
     * E.g. if you want a CustomerEntity pre-populated with known-good DB data, you could pass in a 'property'=>'value' pair ('id'=>1), and it will return the entr(y|ies*) that match that criteria.
     * *Multiple entries would only work for ArrayIterator-type (one-to-many) relationships.
 * Possible integration with a "lorum ipsum" generator to auto-fill values for entity properties.
 * Entity Mapper: show relationships between various entities in nice human-readable format
