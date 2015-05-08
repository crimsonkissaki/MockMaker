<?php
/**
 * OrmData
 *
 * This class holds any ORM data declared for a property.
 *
 * @package        MockMaker
 * @author         Evan Johnson
 * @created        5/6/15
 * @version        1.0
 */

namespace MockMaker\Model;

class OrmData
{

    /**
     * DB column name property represents
     *
     * @var string
     */
    public $columnName;

    /**
     * Data type as declared by ORM
     *
     * @var string
     */
    public $ormDataType;

    /**
     * PHP data type equivalent of ormDataType
     *
     * @var string
     */
    public $dataType;

    /**
     * Is property nullable
     *
     * @var bool
     */
    public $nullable;

    /**
     * The entity relationship
     *
     * Many to one, one to many, etc.
     *
     * @var string
     */
    public $relationship;

    /**
     * The class used to represent the property
     *
     * @var string
     */
    public $targetEntity;

    /**
     * The column targeted in the related entity for joins
     *
     * @var string
     */
    public $targetEntityColumn;

}