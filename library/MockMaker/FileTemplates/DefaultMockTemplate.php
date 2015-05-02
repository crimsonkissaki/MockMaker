<?php

/**
 * Default mock template code
 *
 * @package:    MockMaker
 * @author :     Evan Johnson
 * @created:    5/1/15
 */

$mockCode = <<<MOCKCODE
<?php

/**
 * {$dataPoints['ClassMockName']}
 *
 * Automatically generated by MockMaker.
 *
 * @author      MockMaker
 * @created     {$dataPoints['CreatedDate']}
 */

namespace {$dataPoints['NameSpace']};

{$dataPoints['UseStatements']}

class {$dataPoints['ClassMockName']}
{

    /**
     * Returns array of minimum required properties for generating
     * a valid {$dataPoints['ClassName']} mock file
     *
     * This associative array format of {$dataPoints['ClassName']}'s elements
     * allows for simple customization of how the class mock is hydrated.
     *
     * It's best to remove any properties that are not absolutely necessary for
     * instantiating a new {$dataPoints['ClassName']} object. However,
     * it is still possible to ignore any of these on an at-will basis when
     * creating a new mock if the property is used enough that you want to
     * leave it in here to save work. See getMock() documentation for details.
     *
     * Since MockMaker already does a best guess attempt at detecting setters and
     * inserts them here if it finds one, if the 'setter' element of this array is
     * '' or boolean false MockMaker will assume the property has no setter
     * and needs to be manipulated through reflection.
     *
     * @return  array
     */
    public static function getMandatoryProperties()
    {
        return array(
{$dataPoints['PropertiesAndSettersArray']}
        );
    }

    /**
     * Customized generation of mock {$dataPoints['ClassName']} objects
     *
     * @param	array|null  \$properties
     * null (default): Returns a 'bare bones' mock. Any properties in the
     * 'mandatoryProperties' array will be hydrated based on the defined
     * 'setter' method and 'default' value.
     *
     * array: Associative array in 'property' => 'value' format.
     * Returns a mock hydrated with given values for given properties.
     * Any properties specified in the 'mandatoryProperties' array that are
     * not supplied in the array will be hydrated based on the defined 'setter'
     * method and 'default' value.
     *
     * @param	array|null  \$ignore
     * Properties that you want getMock() to completely ignore while
     * hydrating the mock object. Overrides properties defined in the
     * 'mandatoryProperties' array.
     *
     * @return	{$dataPoints['ClassName']}
     */
    public static function getMock(\$properties = null, \$ignore = null)
    {
        \$defaults = self::getMandatoryProperties();
        \$mock = new {$dataPoints['ClassName']}();
        \$reflection = new \ReflectionClass('{$dataPoints['ClassPath']}');

{$dataPoints['SetterCode']}
{$dataPoints['ReflectionCode']}

        return \$mock;
    }

}

MOCKCODE;

return $mockCode;
