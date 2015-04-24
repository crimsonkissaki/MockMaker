# MockMaker
Automated Entity/Class "mock" file seeder.

Whether you call them doubles, stubs, mocks, partials, fakes, or something else there are times when a mocking library such as PHPUnit's mockBuilder, Mockery, Prophecy, etc. just doesn't do exactly what you need or want. Sometimes you just need a concrete implementation of a class (especially Doctrine Entities) to run through the unit test wringer or a full end to end functional unit test suite.

MockMaker aims to simplify the process of generating test doubles of various classes with a particular emphasis on ORM entities (because that's why I originally wrote this library).

Let's face it, if you inherit a large code base with a couple dozen entity classes that need to quickly be set up for use in unit/functional tests, jumping back and forth between files, keeping the properties and methods straight and manually writing up the code to generate those mocks is a huge PITA. And that's even when you don't have the worry of encountering private/protected properties/methods.

With a variety of options, MockMaker will take a list of files and generate 'seed' files for each.
- If a property doesn't have a setter it will create a straight value assignment.
- If there is a typehinted class or a default value it will be automatically included in the seed file to simplify the process.
- If the property is private or protected it will automatically include the reflection code required to set those values.

Flexible and extendable, the seed code can be altered to suit your particular project with relative ease, so after the initial setup you can re-run MockMaker for any new entities that get added in with little to no fuss. Default seed files have two primary methods. The first is a 'basic' mock creator with static value assignments. The second is a fully customizable 'advanced' mock creator that allows for compile-time customization of entity values so you can use PHPUnit data providers or factories to generate as many customized entity instances as your tests require.