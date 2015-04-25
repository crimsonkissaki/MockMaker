<?php

/**
 *	FileWorker
 *
 *	Processes files as required by MockMakerFileGenerator
 *
 *	This assumes PSR-0 adherence for namespacing.
 *
 *	@author		Evan Johnson <evan.johnson@rapp.com>
 *	@created	Apr 20, 2015
 *	@version	1.0
 */

namespace Minion\MockMakerBundle\Library\MockMaker\Worker;

use Minion\UnitTestBundle\Library\DebuggerMinion;

class FileWorker
{

	/**
	 * Array of validated namespaces
	 *
	 * @var	string
	 */
	private $validNamespaces = [];

	/**
	 * We've already done the work, so why repeat ourselves
	 * especially when we're probably iterating over a directory
	 * of entity classes.
	 *
	 * @param	$classPath	string
	 */
	public function addClassNamespacePathToArray( $classPath )
	{
		$namespace = join('\\', array_slice(explode('\\', $classPath), 0, -1));
		if( !in_array( $namespace, $this->validNamespaces ) ) {
			$this->validNamespaces[] = $namespace;
		}
	}

	/**
	 * Get the fully qualified class name for the file, if possible.
	 *
	 * @param	$file	string
	 * @throws	MockMakerException
	 * @return	bool|string
	 */
	public function getQualifiedClassName( $file )
	{
		if( $class = $this->getClassNameThroughClassExistsIteration( $file ) ) {
			$this->addClassNamespacePathToArray( $class );
			return $class;
		}

		return FALSE;
	}

	/**
	 * Iterate through known valid namespaces and filepaths to find a valid class name.
	 *
	 * @param	$file	string
	 * @return	mixed
	 */
	public function getClassNameThroughClassExistsIteration( $file )
	{
		$classPath = rtrim( ltrim( str_replace( array(DIRECTORY_SEPARATOR,'_'), '\\', $file ), '\\' ), '.php' );
		$className = join('', array_slice(explode('\\', $classPath), -1));
		foreach( $this->validNamespaces as $namespace ) {
			$possible = "{$namespace}\\{$className}";
			if( class_exists($possible) ) {
				return $possible;
			}
		}
		// not already in our array, so we have to find it the hard way
		$result = $this->recurseClassCheck( $classPath );

		return $result;
	}

	/**
	 * Recurse over the full file path to find the qualified class name.
	 *
	 * @param	$filePath	string
	 * @return	mixed
	 */
	public function recurseClassCheck( $filePath )
	{
		if( !class_exists($filePath) ) {
			if( ( $pos = strpos( $filePath, '\\' ) ) === FALSE ) {
				return FALSE;
			}
			$shorter = substr( $filePath, ($pos + 1) );
			return $this->recurseClassCheck( $shorter );
		}

		return $filePath;
	}

}
