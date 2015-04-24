<?php

/**
 *	MockMakerFileGenerator
 *
 *	Generate and save mock files for single files or an entire directory.
 *
 *	TODO:
 *		If files are found in a sub-directory, save them to the same sub-directory
 *		Set up constructor to accept basic arguments so you know wtf to do
 *		Create "validate" method so you can verify your settings are good before you run it.
 *		Ensure "write directory" has a / on the end
 *		Add "include files with" and "exclude files with" regex statements
 *			Add a test utility to show what you'd get
 *
 *	@author		Evan Johnson <evan.johnson@rapp.com>
 *	@created	Apr 16, 2015
 *	@version	1.0
 */

namespace Minion\MockMakerBundle\Library;

use Minion\MockMakerBundle\Library\MockMaker;
use Minion\MockMakerBundle\Library\MockMaker\Model\FileGeneratorOptions;

use Minion\MockMakerBundle\Library\MockMaker\Worker\FileWorker;
use Minion\MockMakerBundle\Library\MockMaker\Formatter\FormatterInterface;
use Minion\MockMakerBundle\Library\MockMaker\Formatter\DefaultFormatter;
use Minion\MockMakerBundle\Library\MockMaker\Exception\MockMakerErrors as MMErrors;
use Minion\MockMakerBundle\Library\MockMaker\Exception\MockMakerException;
use RAPP\Bundle\LoyaltyBundle\Tests\Helpers\Utilities\MockMaker\Worker\StringFormatterWorker;

use Minion\UnitTestBundle\Library\DebuggerMinion;

class MockMakerFileGenerator
{

	/**
	 * Options class for the file generator
	 *
	 * @var FileGeneratorOptions
	 */
	private $options;

	/**
	 * Array of files to generate mocks for.
	 *
	 * @var	array
	 */
	private $filesToMock = [];

	/**
	 * Array of class namespaces for the mocks.
	 *
	 * @var	array
	 */
	private $mockClassNamespaces = [];

	/**
	 * MockMaker Formatter instance
	 *
	 * @var FormatterInterface
	 */
	private $formatter;

	/**
	 * Best guess at the path to the project's root directory.
	 * Used later when generating a namespace for the mocked classes.
	 *
	 * @var	string
	 */
	protected $projectRootDir;

	/**
	 * File worker class.
	 *
	 * @var FileWorker
	 */
	protected $fileWorker;

	/**
	 * Get the class that holds FileGenerator config options.
	 *
	 * @return	FileGeneratorOptions
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * Get an array of individual files to generate mocks for.
	 *
	 * @return	array
	 */
	public function getFilesToMock()
	{
		return $this->filesToMock;
	}

	/**
	 * Get an array of class namespaces for the mocks.
	 *
	 * @return	array
	 */
	public function getMockClassNamespaces()
	{
		return $this->mockClassNamespaces;
	}

	/**
	 * Get Formatter instance
	 *
	 * @return	FormatterInterface
	 */
	public function getFormatter()
	{
		if( !$this->formatter ) {
			return new DefaultFormatter;
		}

		return $this->formatter;
	}

	/**
	 * Get the project's root directory.
	 *
	 * @return	string
	 */
	public function getProjectRootDir()
	{
		return $this->projectRootDir;
	}

	/**
	 * Get the file worker
	 *
	 * @return	FileWorker
	 */
	public function getFileWorker()
	{
		return $this->fileWorker;
	}

	/**
	 * Set an array of individual files to generate mocks for.
	 *
	 * @param	$files	array
	 */
	public function setFilesToMock( $files )
	{
		if( !is_array( $files ) ) {
			$files = array( $files );
		}
		$this->filesToMock = $files;
	}

	/**
	 * Add either a single file or an array
	 * of files to the "files to mock" array.
	 *
	 * @param	$files	mixed
	 */
	public function addFilesToMock( $files )
	{
		if( is_array( $files ) ) {
			$this->setFilesToMock( array_merge( $this->filesToMock, $files ) );
		} else {
			array_push( $this->filesToMock, $files );
		}
	}

	/**
	 * Set an array of class names for the mocks.
	 *
	 * @param	$mockClassNamespaces		array
	 */
	public function setMockClassNamespaces( $mockClassNamespaces )
	{
		$this->mockClassNamespaces = $mockClassNamespaces;
	}

	/**
	 * Add a single class name or array of class names to the mocks class names.
	 *
	 * @param	$mockClassNamespace		mixed
	 */
	public function addMockClassNamespace( $mockClassNamespace )
	{
		if( is_array($mockClassNamespace) ) {
			$this->setMockClassNamespaces( array_merge( $this->mockClassNamespaces, $mockClassNamespace ) );
		} else {
			array_push( $this->mockClassNamespaces, $mockClassNamespace );
		}
	}

	/**
	 * Set format string for generated file names.
	 *
	 *
	 * @param	string
	 */
	public function setFileNameFormat( $fileNameFormat )
	{
		$this->fileNameFormat = $fileNameFormat;
	}

	/**
	 * Set Formatter instance.
	 *
	 * Use this if you want/need a custom Formatter for MockMaker.
	 *
	 * @param	$formatter	FormatterInterface
	 */
	public function setFormatter( FormatterInterface $formatter )
	{
		$this->formatter = $formatter;
	}

	/**
	 * Set the project's root directory.
	 *
	 * @param	$projectRootDir	string
	 */
	public function setProjectRootDir( $projectRootDir )
	{
		$this->projectRootDir = $projectRootDir;
	}

	/**
	 * Set the file worker
	 *
	 * @param	$fileWorker	FileWorker
	 */
	private function setFileWorker( FileWorker $fileWorker )
	{
		$this->fileWorker = $fileWorker;
	}



	/**
	 * get options for making mock files
	 * pass data to fileworker to get files to mock
	 *
	 */
















	/**
	 * Get a new MockMakerFileGenerator instance.
	 */
	public function __construct()
	{
		$this->setFileWorker( new FileWorker );
		$this->options = new FileGeneratorOptions;
	}

	/**
	 * Go through the indicated files and generate mock files for each one.
	 */
	public function generateMockFiles()
	{
		/*
		$this->validateReadDirectory();
		$this->validateWriteDirectory();
		$this->addFilesFromReadDirectoryToFilesToMock();
		$this->validateFilesToMock();
		$this->determineQualifiedClassNames();
		$this->determineProjectRootDir();
		$this->processFilesToMock();
		*/
	}

	/**
	 * Verify the provided read directory is valid and has files.
	 *
	 * @throws	MockMakerException
	 * @return	bool
	 */
	private function validateReadDirectory()
	{
		if( $this->options->getReadDirectory() ) {
			if( !is_dir( $this->options->getReadDirectory() ) ) {
				throw new MockMakerException(
					MMErrors::generateMessage( MMErrors::READ_DIR_NOT_EXIST, array('dir'=>"'{$this->options->getReadDirectory()}'") )
				);
			}
			if( !is_readable( $this->options->getReadDirectory()) ) {
				throw new MockMakerException(
					MMErrors::generateMessage( MMErrors::READ_DIR_INVALID_PERMISSIONS, array('dir'=>"'{$this->options->getReadDirectory()}'") )
				);
			}
		}

		return TRUE;
	}

	/**
	 * Verify the provided write directory is valid and has files.
	 *
	 * @throws	MockMakerException
	 * @return	bool
	 */
	private function validateWriteDirectory()
	{
		if( !is_dir( $this->options->getWriteDirectory() ) ) {
			throw new MockMakerException(
				MMErrors::generateMessage( MMErrors::WRITE_DIR_NOT_EXIST, array('dir'=>"'{$this->options->getWriteDirectory()}'") )
			);
		}
		if( !is_writeable( $this->options->getWriteDirectory() ) ) {
			throw new MockMakerException(
				MMErrors::generateMessage( MMErrors::WRITE_DIR_INVALID_PERMISSIONS, array('dir'=>"'{$this->options->getWriteDirectory()}'") )
			);
		}

		return TRUE;
	}

	/**
	 * Add files from read directory (if defined) to filesToMock
	 *
	 * @return	bool
	 */
	private function addFilesFromReadDirectoryToFilesToMock()
	{
		if( $this->options->getReadDirectory() ) {
			$this->addFilesToMock( $this->getFilesToMockFromReadDirectory() );

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Parse the provided read directory for files to mock.
	 *
	 * @return	array
	 */
	private function getFilesToMockFromReadDirectory()
	{
		$files = [];
		if( !$this->options->getRecursiveRead() ) {
			$dir = new \DirectoryIterator( $this->options->getReadDirectory() );
		} else {
			$dir = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator($this->options->getReadDirectory() ) );
		}
		foreach( $dir as $file ) {
			if( !$file->isDir() && $file->getExtension() === 'php' ) {
				$files[] = $file->getPathname();
			}
		}

		return $files;
	}

	/**
	 * Validate the files that are to be processed.
	 *
	 * @throws	MockMakerException
	 * @return	bool
	 */
	private function validateFilesToMock()
	{
		if( empty($this->filesToMock) ) {
			throw new MockMakerException(
				//MMErrors::generateMessage( MMErrors::INVALID_SOURCE_FILE, array('file'=>"'{$file}'") )
				"No files supplied to be mocked."
			);
		}
		foreach( $this->filesToMock as $file ) {
			if( !is_file( $file ) ) {
				throw new MockMakerException(
					MMErrors::generateMessage( MMErrors::INVALID_SOURCE_FILE, array('file'=>"'{$file}'") )
				);
			}
		}

		return TRUE;
	}

	/**
	 * Generate the qualified class names for the files we need to mock.
	 *
	 * @return	array
	 */
	private function determineQualifiedClassNames()
	{
		$classNames = [];
		foreach( $this->filesToMock as $file ) {
			$classNames[] = $this->getFileWorker()->getQualifiedClassName( $file );
		}
		$this->addMockClassNamespace( $classNames );

		return $classNames;
	}

	/**
	 * Best guess at the project root directory path.
	 *
	 * @throws	InvalidArgumentException
	 * @return	string
	 */
	private function determineProjectRootDir()
	{
		if( $this->projectRootDir ) {
			return;
		}

		if( !isset($this->filesToMock[0]) || !isset($this->mockClassNamespaces[0]) ) {
			throw new \InvalidArgumentException("Insufficient data to determine root project directory.");
		}

		$file = $this->filesToMock[0];
		$namespace = $this->mockClassNamespaces[0];

		$needle = str_replace( '\\', DIRECTORY_SEPARATOR, $namespace) . ".php";
		$this->projectRootDir = str_replace( $needle, '', $file );

		return $this->projectRootDir;
	}

	/**
	 * Process all files that need to be mocked.
	 */
	private function processFilesToMock()
	{
		foreach( $this->filesToMock as $file ) {
			if( !$class = $this->getClassNamespaceForFile( $file ) ) {
				throw new \Exception("No valid class name found for file");
			}
			$mockMaker = new MockMaker( new $class, $this->getFormatter() );
			$mockMaker->analyzeClass();
			$fileNameFormatString = $this->getFormatter()->getClassNameFormat();
			$args = array('ClassName'=>$mockMaker->getClassShortName());
			$fileName = StringFormatterWorker::vsprintf2( $fileNameFormatString, $args );
			$mockMaker->setMockNamespace( $this->generateMockFileNamespace() );
			$code = $mockMaker->getBasicMockCode();
			$this->saveFile( $fileName, $code );
		}
	}

	/**
	 * Get the namespace for a file out of the mockClassNamespaces array
	 *
	 * @param	$file	string
	 * @return	mixed
	 */
	private function getClassNamespaceForFile( $file )
	{
		$class = rtrim( join('', array_slice(explode('/', $file), -1)), '.php' );
		foreach( $this->mockClassNamespaces as $namespace ) {
			$pattern = "/{$class}$/";
			if( preg_match( $pattern, $namespace ) ) {
				return $namespace;
			}
		}

		return false;
	}

	/**
	 * Generate the namespace that's going to be used in the generated php code.
	 *
	 * @return	string
	 */
	private function generateMockFileNamespace()
	{
		$trimmedPath = rtrim( str_replace( $this->getProjectRootDir(), '', $this->options->getWriteDirectory() ), '/' );
		$namespace = str_replace('/', '\\', $trimmedPath );

		return $namespace;
	}

	/**
	 * Save the file.
	 *
	 * @param	$fileName	string
	 * @param	$code		string
	 */
	private function saveFile( $fileName, $code )
	{
		$filePath = $this->options->getWriteDirectory() . $fileName . '.php';
		if( !$this->getOverwriteExistingFiles() && file_exists($filePath) ) {
			return FALSE;
		}
		$writeResults = file_put_contents( $filePath, $code );
		if( !$writeResults ) {
			throw new \Exception("error writing code to file");
		}
	}

}
