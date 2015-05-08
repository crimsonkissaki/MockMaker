<?php

/**
 * AbstractDataPointWorker
 *
 * Any child classes have one job: take a DataContainer object and transform
 * the data into 'data points' that are interpolated into a template file by
 * the TemplateWorker.
 *
 * @package        MockMaker
 * @author         Evan Johnson
 * @created        5/8/15
 * @version        1.0
 */

namespace MockMaker\Worker;

use MockMaker\Exception\MockMakerErrors;
use MockMaker\Exception\MockMakerFatalException;
use MockMaker\Model\DataContainer;
use MockMaker\Model\MockData;
use MockMaker\Model\EntityData;

abstract class AbstractDataPointWorker
{

    /**
     * Template file DataPointWorker is meant to work with
     *
     * @var string
     */
    protected $template;

    /**
     * Methods that MUST be run after datapoints are generated
     *
     * @var array
     */
    protected $additionalProcesses = [];

    /**
     * Gets the array of processes that MUST be executed
     *
     * @return array
     */
    public function getAdditionalProcesses()
    {
        return $this->additionalProcesses;
    }

    /**
     * Sets the template used by the DataPointWorker
     *
     * @param   string $template
     * @throws  MockMakerFatalException
     */
    public function setTemplate($template)
    {
        if (!is_file($template)) {
            throw new MockMakerFatalException(
                MockMakerErrors::generateMessage(MockMakerErrors::INVALID_TEMPLATE_FILE,
                    array('file' => $template)
                )
            );
        }
        $this->template = $template;
    }

    /**
     * Returns the text contents of a DataPoint template file
     *
     * @return  string
     * @throws  MockMakerFatalException
     */
    public function getTemplateContents()
    {
        if (!$this->template) {
            throw new MockMakerFatalException(
                MockMakerErrors::generateMessage(MockMakerErrors::TEMPLATE_FILE_NOT_DEFINED,
                    array('file' => get_class($this))
                )
            );
        }

        // TODO: code to allow for .txt or .php files?
        return include($this->template);
    }

    /**
     * Adds a post-datapoint generation process that must be executed
     *
     * @param $process
     */
    protected function registerAdditionalProcess($process)
    {
        array_push($this->additionalProcesses, $process);
    }

    /**
     * Generates the data points required to populate the template file
     *
     * @param   DataContainer $dataContainer
     * @return  array
     */
    abstract public function generateDataPoints(DataContainer $dataContainer);

    /**
     * Processes the generated code as appropriate
     *
     * @param   DataContainer $dataContainer
     * @param   string        $code
     * @return  bool
     */
    abstract public function processCode(DataContainer $dataContainer, $code);

    /**
     * Generates use statements out of namespaces for all classes used by the mocked class
     *
     * To help out with generating mocks, we're gathering the namespaces
     * that are used in typehinting as well as the ones declared at the
     * top of the file.
     *
     * @param   MockData   $mockData   MockData object
     * @param   EntityData $entityData EntityData object
     * @return  string
     */
    protected function generateUseStatements(MockData $mockData, EntityData $entityData)
    {
        $classUse = $mockData->getUseStatements();
        $mockedClassUse = "use {$entityData->getClassNamespace()}\\{$entityData->getClassName()};";
        array_unshift($classUse, $mockedClassUse);
        $propUse = $this->getUseStatementsFromClassProperties($mockData->getProperties());
        $methodUse = $this->getUseStatementsFromClassMethods($mockData->getMethods());
        $statements = array_merge($classUse, $propUse, $methodUse);

        return join(PHP_EOL, array_unique($statements));
    }

    /**
     * Extracts use statements from class properties
     *
     * @param   array $classProperties Array of PropertyData objects
     * @return  array
     */
    protected function getUseStatementsFromClassProperties($classProperties)
    {
        $statements = [];
        foreach ($classProperties as $properties) {
            if (!empty($properties)) {
                foreach ($properties as $property) {
                    if ($property->dataType === 'object') {
                        $nameSpace = ($property->classNamespace) ? $property->classNamespace . "\\" : "";
                        array_push($statements, "use {$nameSpace}{$property->className};");
                    }
                }
            }
        }

        return $statements;
    }

    /**
     * Extracts use statements from class methods
     *
     * @param   array $classMethods Array of MethodData objects
     * @return  array
     */
    protected function getUseStatementsFromClassMethods($classMethods)
    {
        $statements = [];
        foreach ($classMethods as $methods) {
            if (!empty($methods)) {
                foreach ($methods as $method) {
                    if (!empty($method->arguments)) {
                        foreach ($method->arguments as $argument) {
                            if ($argument->dataType === 'object') {
                                $nameSpace = ($argument->classNamespace) ? $argument->classNamespace . "\\" : "";
                                array_push($statements, "use {$nameSpace}{$argument->className};");
                            }
                        }
                    }
                }
            }
        }

        return $statements;
    }

    /**
     * Saves code to a file
     *
     * Does not save if file exists and overwrites are disallowed.
     *
     * @param   string $filePath Fully qualified path to new file
     * @param   string $code     Code to write in file
     * @return  bool
     * @throws  MockMakerException
     */
    protected function writeFile($filePath, $code)
    {
        // ensure that the directory we're attempting to write to exists
        // important for recursive writes!
        $writeDir = PathWorker::getPathUpToName($filePath);
        DirectoryWorker::validateWriteDir($writeDir);
        if (!file_put_contents($filePath, $code)) {
            throw new MockMakerException(
                MockMakerErrors::generateMessage(MockMakerErrors::DATA_POINT_WORKER_WRITE_ERR,
                    array('file' => $filePath))
            );
        }

        return true;
    }
}