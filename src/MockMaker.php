<?php

/**
 * 	MockMaker
 *
 * 	@author		Evan Johnson <evan.johnson@rapp.com>
 * 	@created	Apr 24, 2015
 * 	@version	1.0
 */

namespace MockMaker;

use MockMaker\Model\MockMakerConfig;

class MockMaker
{

	/**
	 * MockMaker configuration class
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
	 * Add a single or array of files to the list of files
	 * to be generated.
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
	 * Set directory name to scan for files to mock.
	 *
	 * @param	$readDirectory	string	Directory to scan for files.
	 * @return	MockMaker
	 */
	public function getFilesFrom($readDirectory)
	{
		$this->config->setReadDirectory($readDirectory);

		return $this;
	}

	/**
	 * Set directory name to save generated mock files in.
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
	 * Parse the read directory recursively
	 *
	 * Default MockMaker setting is to NOT recursively parse the read directory
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
	 * Set whether to mimick the read directory file structure
	 * in the write directory.
	 *
	 * @param	$preserveStructure	bool	Default true.
	 * @return	MockMaker
	 */
	public function preserveDirectoryStructure($preserveStructure = true)
	{
		$this->config->setPreserveDirectoryStructure($preserveStructure);

		return $this;
	}

	/**
	 * Set whether or not to overwrite existing files.
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

}
