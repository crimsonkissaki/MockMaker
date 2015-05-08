<?php

/**
 * MockMaker
 *
 * Simple generation of seeder mock files for entities and classes
 *
 * @package        MockMaker
 * @author         Evan Johnson
 * @created        Apr 24, 2015
 * @version        1.0
 */

namespace MockMaker;

//require dirname(dirname(dirname(__FILE__))) . '/vendor/autoload.php';

use MockMaker\Model\ConfigData;
use MockMaker\Exception\MockMakerFatalException;
use MockMaker\Worker\AbstractDataPointWorker;
use MockMaker\Worker\ConfigDataWorker;
use MockMaker\Worker\FileWorker;
use MockMaker\Worker\FileProcessorWorker;

class MockMaker
{

    /**
     * MockMaker configuration class
     *
     * @var ConfigData
     */
    private $config;

    /**
     * Class that handles some ConfigData operations
     *
     * @var ConfigDataWorker
     */
    private $configDataWorker;

    /**
     * Class that handles file name operations
     *
     * @var FileWorker
     */
    private $fileNameWorker;

    /**
     * Get the configuration options class
     *
     * @return  ConfigData
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Create a new MockMaker instance
     */
    public function __construct()
    {
        $this->config = new ConfigData();
        $this->configDataWorker = new ConfigDataWorker();
        $this->fileNameWorker = new FileWorker();
    }

    /**
     * Sets the project's root directory path
     *
     * This can be set manually if MockMaker's best guess for root path is wrong
     *
     * @param    string $projectRootPath Project root directory path
     * @return   MockMaker
     */
    public function setProjectRootPath($projectRootPath)
    {
        $this->config->setProjectRootPath($projectRootPath);

        return $this;
    }

    /**
     * Adds file(s) to the mocking list
     *
     * Files specified here will be merged with files found in the
     * read directory specified in the mockEntitiesInDirectory() method
     *
     * @param    string|array $files File names to parse
     * @return   MockMaker
     */
    public function mockTheseEntities($files)
    {
        $this->config->addToAllDetectedFiles($files);

        return $this;
    }

    /**
     * Sets directories to scan for files to mock
     *
     * Any files returned from these directories will be merged with files
     * specified through the mockTheseEntities() method
     *
     * @param    string|array $readDirectory Directories to scan
     * @return   MockMaker
     */
    public function mockEntitiesInDirectory($readDirectory)
    {
        $this->config->addReadDirectories($readDirectory);

        return $this;
    }

    /**
     * Tells MockMaker to recursively parse read directories
     *
     * This overrides the default MockMaker setting of false
     *
     * @return    MockMaker
     */
    public function recursively()
    {
        $this->config->setRecursiveRead(true);

        return $this;
    }

    /**
     * Sets a directory to save generated mock files in
     *
     * If this is not specified MockMaker will still return the generated
     * code as a string.
     *
     * @param    string $writeDirectory Directory to save output files in
     * @return   MockMaker
     */
    public function saveMockFilesIn($writeDirectory)
    {
        $this->config->setMockWriteDir($writeDirectory);

        return $this;
    }

    /**
     * Sets the directory where unit test files will be saved
     *
     * If no directory has been specified the unit test code will be returned
     * with the mock file code.
     *
     * @param   string $unitTestDirectory Directory to save unit test files in
     * @return  MockMaker
     */
    public function saveUnitTestsIn($unitTestDirectory)
    {
        $this->config->setUnitTestWriteDir($unitTestDirectory);

        return $this;
    }

    /**
     * Tells MockMaker to ignore read directory's file structure and
     * save all generated files into the same directory
     *
     * Default MockMaker setting is TRUE
     *
     * @return    MockMaker
     */
    public function ignoreDirectoryStructure()
    {
        $this->config->setPreserveDirStructure(false);

        return $this;
    }

    /**
     * Tells MockMaker to overwrite existing mock files
     *
     * Default MockMaker setting is FALSE
     *
     * @return    MockMaker
     */
    public function overwriteMockFiles()
    {
        $this->config->setOverwriteMockFiles(true);

        return $this;
    }

    /**
     * Tells MockMaker to overwrite existing unit test files
     *
     * Default MockMaker setting is FALSE
     *
     * @return    MockMaker
     */
    public function overwriteUnitTestFiles()
    {
        $this->config->setOverwriteUnitTestFiles(true);

        return $this;
    }

    /**
     * Sets a regex pattern used to EXCLUDE files
     *
     * TODO: allow multiple exclude regex?
     *
     * Pattern is compared against the file name, sans extension.
     *
     * @param    string $excludeRegex Regex pattern
     * @return    MockMaker
     */
    public function excludeFilesWithFormat($excludeRegex)
    {
        $this->config->setExcludeFileRegex($excludeRegex);

        return $this;
    }

    /**
     * Sets a regex pattern used to INCLUDE files
     *
     * TODO: allow multiple include regex?
     *
     * Pattern is compared against the file name, sans extension.
     *
     * @param    string $includeRegex Regex pattern
     * @return    MockMaker
     */
    public function includeFilesWithFormat($includeRegex)
    {
        $this->config->setIncludeFileRegex($includeRegex);

        return $this;
    }

    /**
     * Sets a custom template for the mock files
     *
     * @param   string $template
     * @return  MockMaker
    public function useThisMockTemplate($template)
     * {
     * $this->config->getMockDataPointWorker()->setTemplate($template);
     *
     * return $this;
     * }
     */

    /**
     * Sets a custom code worker class to generate mocks
     *
     * @param   AbstractDataPointWorker $dataPointWorker
     * @return  MockMaker
    public function useCustomMockDataPointWorker(AbstractDataPointWorker $dataPointWorker)
     * {
     * $this->config->registerDataPointWorker($dataPointWorker);
     *
     * return $this;
     * }
     */

    /**
     * Sets a custom template for the mock unit test files
     *
     * @param   string $template
     * @return  MockMaker
    public function useThisMockUnitTestTemplate($template)
     * {
     * $this->config->getUtDataPointWorker()->setTemplate($template);
     *
     * return $this;
     * }
     */

    /**
     * Sets a custom code worker class to generate mocks
     *
     * @param   AbstractDataPointWorker $dataPointWorker
     * @return  MockMaker
    public function useCustomMockUnitTestDataPointWorker(AbstractDataPointWorker $dataPointWorker)
     * {
     * $this->config->registerDataPointWorker($dataPointWorker);
     *
     * return $this;
     * }
     */

    /**
     * Sets a format for mock file names to be saved with
     *
     * Use %FileName% somewhere in a string to denote where you want the
     * name of the original file to appear in the mock name.
     *
     * E.g. if your file is 'MyEntity.php', and your format is
     * 'Mock%FileName%Entity', the resulting file will be named
     * 'MockMyEntityEntity.php'.
     *
     * MockMaker default format is '%FileName%Mock'.
     *
     * @param   string $format
     * @return  MockMaker
     */
    public function saveMocksWithFileNameFormat($format)
    {
        $this->config->setMockFileNameFormat($format);

        return $this;
    }

    /**
     * Sets a 'base' namespace for mocks
     *
     * This is used when generating mock code. MockMaker can make
     * a best-guess attempt at generating this for you, but it's
     * difficult to be psychic and get it right. Basically, whatever
     * the valid namespace should be for Whatever\Save\Path\You\Picked
     * should be set here. It will be used for both top-level 'read dir'
     * classes, and appended to for sub-level classes detected during
     * a recursive read.
     *
     * @param   string $namespace
     * @return  MockMaker
     */
    public function useBaseNamespaceForMocks($namespace)
    {
        $this->config->setMockFileBaseNamespace($namespace);

        return $this;
    }

    /**
     * Returns the MockMaker config settings
     *
     * Should verifySettings include the results of the regex call?
     * no! there is a separate method for that...
     *
     * @return    ConfigData
     */
    public function verifySettings()
    {
        try {
            $this->validateRequiredConfigData();

            return $this->config;
        } catch (MockMakerFatalException $e) {
            echo "\n\nFatal MockMakerException: {$e->getMessage()}\n\n";
        }
    }

    /**
     * Tests (in|ex)clude regex patterns
     *
     * The regex patterns will be tested against any files found in the
     * read directories or specified manually and the results returned in
     * an associative array of [include],[exclude],[workable] files.
     *
     * @return    array
     */
    public function testRegexPatterns()
    {
        try {
            $this->validateRequiredConfigData();

            return $this->fileNameWorker->testRegexPatterns($this->config);
        } catch (MockMakerFatalException $e) {
            echo "\n\nFatal MockMakerException: {$e->getMessage()}\n\n";
        }
    }

    /**
     * Generates mocks for any valid target files
     *
     * If a write directory has been specified, the code will be saved
     * there, otherwise it will be returned as a string.
     *
     * -- WARNING -- Abstract/Interface classes cannot be mocked!
     *
     * @return  string
     */
    public function createMocks()
    {
        try {
            $this->validateRequiredConfigData();
            /* @var $fileProcessorWorker FileProcessorWorker */
            $fileProcessorWorker = new FileProcessorWorker($this->config);
            $generatedCode = $fileProcessorWorker->processFiles();

            return $generatedCode;
        } catch (MockMakerFatalException $e) {
            echo "\n\nFatal MockMakerException: {$e->getMessage()}\n\n";
        } catch (\Exception $e) {
            echo "\n\nMockMaker Uncaught \\Exception:\n\n{$e->getTraceAsString()}\n\n";
        }
    }

    /**
     * Ensures all required data is in the ConfigData object
     */
    private function validateRequiredConfigData()
    {
        $this->config = $this->configDataWorker->validateRequiredConfigData($this->config);
    }
}

