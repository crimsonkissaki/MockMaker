<?php

/**
 *	FileGeneratorOptions
 *
 *	@author		Evan Johnson <evan.johnson@rapp.com>
 *	@created	Apr 22, 2015
 *	@version	1.0
 */

namespace Minion\MockMakerBundle\Library\MockMaker\Model;

class FileGeneratorOptions
{

	/**
	 * Set whether or not to read the directory recursively.
	 *
	 * @var	bool
	 */
	private $recursiveRead = FALSE;

	/**
	 * Overwrite existing files or not?
	 *
	 * @var	bool
	 */
	private $overwriteExistingFiles = TRUE;

	/**
	 * Directory to scan for files that need mocks generated.
	 *
	 * @var	string
	 */
	private $readDirectory;

	/**
	 * Directory to write generated mock files.
	 *
	 * @var	string
	 */
	private $writeDirectory;

	/**
	 * Regex pattern to use to filter out files from mocking.
	 *
	 * @var	string
	 */
	private $fileFilterRegex;

	/**
	 * Get whether to read the directory recursively.
	 *
	 * @return	bool
	 */
	public function getRecursiveRead()
	{
		return $this->recursiveRead;
	}

	/**
	 * Get whether or not to overwrite existing files.
	 *
	 * @return	bool
	 */
	public function getOverwriteExistingFiles()
	{
		return $this->overwriteExistingFiles;
	}

	/**
	 * Get directory name to scan for files to mock.
	 *
	 * @return	string
	 */
	public function getReadDirectory()
	{
		return $this->readDirectory;
	}

	/**
	 * Get directory name to save generated mock files in.
	 *
	 * @return	string
	 */
	public function getWriteDirectory()
	{
		return $this->writeDirectory;
	}

	/**
	 * Get the file filter regex string.
	 *
	 * @return	string
	 */
	public function getFileFilterRegex()
	{
		return $this->fileFilterRegex;
	}

	/**
	 * Set whether to read the directory recursively.
	 *
	 * @param	$recursiveRead	bool
	 */
	public function setRecursiveRead( $recursiveRead )
	{
		$this->recursiveRead = $recursiveRead;
	}

	/**
	 * Set whether or not to overwrite existing files.
	 *
	 * @param	$overwriteExistingFiles		bool
	 */
	public function setOverwriteExistingFiles( $overwriteExistingFiles )
	{
		$this->overwriteExistingFiles = $overwriteExistingFiles;
	}

	/**
	 * Set directory name to scan for files to mock.
	 *
	 * @param	$readDirectory	string
	 */
	public function setReadDirectory( $readDirectory )
	{
		$this->readDirectory = $readDirectory;
	}

	/**
	 * Set directory name to save generated mock files in.
	 *
	 * @param	$writeDirectory	string
	 */
	public function setWriteDirectory( $writeDirectory )
	{
		$this->writeDirectory = $writeDirectory;
	}

	/**
	 * Set the file filter regex string.
	 *
	 * @param	$fileFilterRegex	string
	 */
	public function setFileFilterRegex( $fileFilterRegex )
	{
		$this->fileFilterRegex = $fileFilterRegex;
	}

}
