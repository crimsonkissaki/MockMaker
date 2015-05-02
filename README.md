## MockMaker

An automated entity/class "mock" file seeder.

Whether you call them doubles, stubs, mocks, partials, fakes, or something else there are times when a mocking library such as PHPUnit's mockBuilder, Mockery, Prophecy, etc. just doesn't do exactly what you need or want. Sometimes you just _need_ a concrete class implementation to run through the unit test wringer or a full end to end functional unit test suite.

MockMaker aims to simplify the process of generating concrete fake object with a particular emphasis on ORM entities (because that's the original problem I wrote this library so solve).

## Why you would use MockMaker:

Let's face it, if you've ever inherited a large code base with a couple dozen entity classes that need to quickly be set up for use in unit/functional tests you know how big of a PITA standing those up can be. Jumping back and forth between files, keeping the properties, methods, and entity relationships straight while manually writing up the code to generate those mocks is horribly time consuming. If those entities have non-public properties it makes dynamic data setting annoying as hell. Toss in some heavy association mapping and a few many-to-one relationships that circle around each other and it's enough to make you need a stiff drink.

So, if you are:

 * Porting a legacy database over to an ORM and need to make sure the generated entities will work with your existing unit test suite.
 * Inheriting a project that relies heavily on Doctrine (or another ORM) with a few dozen entity classes, and there's NO unit testing whatsoever.
 * Working on a project that requires extensive functional tests involving database access.
 * Need to stand up valid entities with randomized data for load/unit testing.
 * Efficiently lazy and prefer to let a script do the grunt work.

MockMaker might be able to lend you a hand.

## What MockMaker does:

At its core, MockMaker takes a list of files and/or directories and generates mock 'seeder' files for any instantiable classes. Interfaces and Abstracts need not apply.

 * If a property doesn't have a setter it will create a straight value assignment.
 * If there is a typehinted class or a default value it will be automatically included in the seed file to simplify the process.
 * If the property is private or protected it uses reflection to dynamically set those values.
 * A default settings option is included that allows for configuration of 'bare bones' object creation for those times when you just need a valid instance, regardless of data.

Flexible and extendable, the seed code can be altered to suit your particular project with relative ease, so after the initial setup you can re-run MockMaker for any new entities that get added in, or existing entities that change with little to no fuss. What's more, once MockMaker has made your files, it's done. You don't have to include it in your code base and can use the generated files like any other project class.

## Mock File Usage:

### Setting Defaults:

You do have to do *SOME* work, but at least it's kept to a minimum. By default, MockMaker will set up a 'best guess' array of defaults for any entity properties, regardless of visibility (constant/public/private/protected/static).

```php
return array(
    'propertyName' => array( 'setter' => 'setPropertyName', 'default' => 'propertyName value' ),
    'propertyName2' => array( 'setter' => 'setNameAfterFormatting', 'default' => 'John Q. Public' ),
    'propertyName3' => array( 'setter' => '', 'default' => new \stdClass() ),
    'propertyName4' => array( 'setter' => 'generateSimpleEntity', 'default' => new SimpleEntity() ),
);
```

If MockMaker detects a setter method (using standard `setPropertyName()` format), it's included automatically. If the property uses a non-standard setter format, just enter the method name as the `setter` value.

If a default value is typehinted in the setter, assigned in a \__construct(), or set in-line in the class property declaration, it is included automatically. Change or add the `default` value as appropriate.

This setup allows for special cases of entities that have _conditional_ properties (e.g. a customer entity with a 'disabledOn' property) that, if defined, will influence business logic. Properties that are omitted/deleted from this array will not be used when setting up a mock unless you specifically tell it to.

### 'Basic' Mocks:

_When store-brand generic works the same._

If you just an object with pre-populated default values using the minimum required properties for viability:

```php
use Path\To\EntityMock;

$mock = EntityMock::getMock();
```

The mock file returns an instance of the class with only the properties defined in the array having your predefined default values. Properties **NOT** in the array will be ignored.

### 'Advanced' Mocks:

_Because off-the-rack doesn't fit quite right._

This is especially useful for run-time customization of entity values so you can use PHPUnit data providers, factories, or random data generators to create as many customized entity instances as your tests require.

If you need to override a default value for particular propert(y|ies), or include a property that is omitted from the defaults array:

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

When the mock is returned, the 'propertyName' property in the array will be set to 'desiredValue'. If no value is set then the property will automatically be set to `NULL`.

But, what if you have a value in the default array because it's one of those things that is _usually-needed-but-just-not-right-now-dangit_? We've got you covered.

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