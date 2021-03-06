<?php

/**
 * Default mock template code
 *
 * @package:    MockMaker
 * @author :    Evan Johnson
 * @created:    5/1/15
 */

$mockCode = <<<MOCKCODE
<?php

/**
 * %ClassName%
 *
 * Automatically generated by MockMaker.
 *
 * @author      MockMaker
 * @created     %CreatedDate%
 */

namespace %NameSpace%;

%UseStatements%

class %ClassName%
{

    /**
     * Returns array of minimum required properties for generating
     * a valid %EntityClassName% mock file
     *
     * This associative array format of %EntityClassName%'s elements
     * allows for simple customization of how the class mock is hydrated.
     *
     * It's best to remove any properties that are not absolutely necessary for
     * instantiating a new %EntityClassName% object. However,
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
%PropertyDefaults%
        );
    }

    /**
     * Customized generation of mock %EntityClassName% objects
     *
     * @param   array   \$properties
     * Associative array in 'property' => 'value' format.
     * Returns a mock hydrated with given values for given properties.
     * Any properties specified in the 'mandatoryProperties' array that are
     * not supplied in the array will be hydrated based on the defined 'setter'
     * method and 'default' value.
     *
     * An empty array returns a 'bare bones' mock. Any properties in the
     * 'mandatoryProperties' array will be hydrated based on the defined
     * 'setter' method and 'default' value.
     *
     * @param	array   \$ignore
     * Properties that you want getMock() to completely ignore while
     * hydrating the mock object. Overrides properties defined in the
     * 'mandatoryProperties' array.
     *
     * @return	%EntityClassName%
     */
    public static function getMock(\$properties = [], \$ignore = [])
    {
        // fix for properties that are not passed in with values
        // and need to be assigned a null default
        foreach(\$properties as \$key => \$value ) {
            if(is_int(\$key)) {
                \$properties[\$value] = null;
                unset(\$properties[\$key]);
            }
        }
        \$defaults = self::getMandatoryProperties();
        \$mock = new %EntityClassName%();
        \$reflection = new \ReflectionClass('%ClassPath%');

%PropertyConstantsArr%

        foreach( \$defaults as \$property => \$default ) {
            if(!in_array(\$property, \$ignore) ) {
                \$value = (isset(\$properties[\$property])) ? \$properties[\$property] : \$defaults[\$property];
                if(!in_array(\$property, \$constant)) {
                    \$r_prop = \$reflection->getProperty(\$property);
                    \$r_prop->setAccessible(true);
                    \$r_prop->setValue(\$mock, \$value);
                }
            }
        }

        foreach( \$properties as \$property => \$value ) {
            if(!in_array(\$property, \$ignore) ) {
                if(!in_array(\$property, \$constant)) {
                    \$r_prop = \$reflection->getProperty(\$property);
                    \$r_prop->setAccessible(true);
                    \$r_prop->setValue(\$mock, \$value);
                }
            }
        }

        return \$mock;
    }

}

MOCKCODE;

return $mockCode;
