<?php

/**
 * 	MockMaker
 *
 * 	@author		Evan Johnson <evan.johnson@rapp.com>
 * 	@created	Apr 24, 2015
 * 	@version	1.0
 */

namespace MockMaker;

require dirname(dirname(dirname(__FILE__))) . '/vendor/autoload.php';

use MockMaker\Model\MockMakerConfig;

class MockMaker
{

    /**
     * MockMaker configuration class.
     *
     * @var MockMakerConfig
     */
    private $config;

    /**
     * Create a new MockMaker instance.
     *
     * @return	MockMaker
     */
    public function __construct()
    {
        $this->config = new MockMakerConfig();
    }

    /**
     * Set your project's root directory path.
     *
     * MockMaker will do its best to guess this based on class namespaces,
     * but you can set it manually if it's having problems.
     *
     * @param	$rootDir	string	Project root directory.
     * @return	MockMaker
     */
    public function setRootDirectory($rootDir)
    {
        $this->config->setRootDirectory($rootDir);

        return $this;
    }

    /**
     * Add one or an array of files to the list of files to be generated.
     *
     * Any files specified here will be merged with files found in the
     * read directory specified in the getFilesFrom() method.
     *
     * @param	$files	mixed	Single or array of file names to parse.
     * @return	MockMaker
     */
    public function mockFiles($files)
    {
        $this->config->addFilesToMock($files);

        return $this;
    }

    /**
     * Set a single or array of directories to scan for files to mock.
     *
     * Any files returned from these directories will be merged with files
     * specified through the mockFiles() method.
     *
     * @param	$readDirectory	mixed	Single or array of directories to scan.
     * @return	MockMaker
     */
    public function getFilesFrom($readDirectory)
    {
        $this->config->addReadDirectory($readDirectory);

        return $this;
    }

    /**
     * Parse the read directory recursively.
     *
     * Default MockMaker setting is FALSE.
     *
     * @param	$recursively	bool	Default true.
     * @return	MockMaker
     */
    public function recursively($recursively = true)
    {
        $this->config->setRecursiveRead($recursively);

        return $this;
    }

    /**
     * Set a directory to save generated mock files in.
     *
     * If you do not specify a write directory, MockMaker will return
     * the generated code as a string.
     *
     * @param	$writeDirectory	string	Directory to save output files in.
     * @return	MockMaker
     */
    public function saveFilesTo($writeDirectory)
    {
        $this->config->setWriteDirectory($writeDirectory);

        return $this;
    }

    /**
     * Tell MockMaker to ignore the read directory's file structure and
     * save all generated files into the same directory.
     *
     * Default MockMaker setting is TRUE.
     *
     * @return	MockMaker
     */
    public function ignoreDirectoryStructure()
    {
        $this->config->setPreserveDirectoryStructure(false);

        return $this;
    }

    /**
     * Set whether or not to overwrite existing files.
     *
     * Default MockMaker setting is FALSE.
     *
     * @param	$overwriteExistingFiles		bool
     * @return	MockMaker
     */
    public function overwriteExistingFiles()
    {
        $this->config->setOverwriteExistingFiles(true);

        return $this;
    }

    /**
     * Define a regex pattern used to EXCLUDE files.
     *
     * This is only used when a read directory has been specified.
     *
     * @param	$ignoreRegex	string	Regex pattern.
     * @return	MockMaker
     */
    public function ignoreFilesWithFormat($ignoreRegex)
    {
        $this->config->setIgnoreFileRegex($ignoreRegex);

        return $this;
    }

    /**
     * Define a regex pattern used to INCLUDE files.
     *
     * This is only used when a read directory has been specified.
     *
     * @param	$includeRegex	string	Regex pattern.
     * @return	MockMaker
     */
    public function includeFilesWithFormat($includeRegex)
    {
        $this->config->setIncludeFileRegex($includeRegex);

        return $this;
    }

    /**
     * Test (in|ex)clude regex patterns against a specified read directory.
     *
     * TODO: finish this
     *
     * @return	array
     */
    public function testRegexPatterns()
    {
        return array( 'undefined' );
    }

    /**
     * Use to verify MockMaker's settings before you kick things off for real.
     *
     * @return	MockMakerConfig
     */
    public function verifySettings()
    {
        return $this->config;
    }

}

$mm = new MockMaker();

echo "\n\n";
print_r($mm);
die("\n\n");
