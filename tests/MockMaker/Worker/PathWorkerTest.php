<?php
/**
 * PathWorkerTest
 *
 * @package:    
 * @author:     johnsone
 * @created:    5/6/15
 */

namespace MockMaker\Worker;


use MockMaker\TestHelper\TestHelper;

class PathWorkerTest extends \PHPUnit_Framework_TestCase
{

    public $rootDir;

    public function setUp()
    {
        $this->rootDir = dirname(dirname(dirname(dirname(__FILE__)))) . '/';
    }

    public function getLastElementProvider()
    {
        return array(
            array( 'TestEntity.php', $this->rootDir.'TestEntity.php', '/' ),
            array( 'TestEntity.php', $this->rootDir.'SomeOtherPath/TestEntity.php', '/' ),
            array( 'TestEntity', 'MockMaker\Worker\TestEntity', '\\' ),
            array( 'Exception', '\Exception', '\\' ),
            array( 'Exception', 'Exception', '\\' ),
        );
    }

    /**
     * @dataProvider getLastElementProvider
     */
    public function test_getLastElementInPath_returnsCorrectFilename($expected, $path, $delim)
    {
        $actual = PathWorker::getLastElementInPath($path, $delim);
        $this->assertEquals($expected, $actual);
    }

    public function test_convertFilePathToClassPath_returnsProperNamespace()
    {
        $expected = 'tests\MockMaker\Entities\SimpleEntity';
        $filePath = $this->rootDir . 'tests/MockMaker/Entities/SimpleEntity';
        $actual = PathWorker::convertFilePathToClassPath($filePath, $this->rootDir);
        $this->assertEquals($expected, $actual);
    }

    public function getPathUpToNameProvider()
    {
        return array(
            array('', '\stdClass', '\\' ),
            array('', 'stdClass', '\\' ),
            array('MockMaker\Entities', 'MockMaker\Entities\SimpleEntity', '\\' ),
            array($this->rootDir.'tests/Entities', $this->rootDir.'tests/Entities/SimpleEntity.php', '/' ),
        );
    }

    /**
     * @dataProvider getPathUpToNameProvider
     */
    public function test_getPathUpToName_returnsValidName($expected, $path, $delim)
    {
        $actual = PathWorker::getPathUpToName($path, $delim);
        $this->assertEquals($expected, $actual);
    }

    public function findRelativePathProvider()
    {
        $root = TestHelper::getRootDir();
        return array(
            array( $root, $root, '' ),
            array( $root, $root.'tests/', 'tests/' ),
            array( $root, $root.'tests/Entities/', 'tests/Entities/' ),
            array( $root, $root.'tests/Entities/SubEntities/', 'tests/Entities/SubEntities/' ),
        );
    }

    /**
     * @dataProvider findRelativePathProvider
     */
    public function test_findRelativePath_returnsCorrectPath($parentPath, $childPath, $expected)
    {
        $actual = PathWorker::findRelativePath($parentPath, $childPath);
        $this->assertEquals($expected, $actual);
    }

    /*
        $method = TestHelper::getAccessibleNonPublicMethod($this->worker, 'convertFileNameToClassPath');
        $actual = $method->invoke($this->worker, $this->fileObj);
        $this->assertEquals(, $actual);
    */

    public function test_formatDirectoryPaths_returnsArrayOfTrimmedPathsWithTrailingSlashes()
    {
        $dirs = array(
            '  ' . dirname(__FILE__) . '   ',
            dirname(dirname(__FILE__)) . '/ ',
            dirname(dirname(dirname(__FILE__))) . '                     ',
        );
        $expected = array(
            dirname(__FILE__) . '/',
            dirname(dirname(__FILE__)) . '/',
            dirname(dirname(dirname(__FILE__))) . '/',
        );
        $actual = PathWorker::formatDirectoryPaths($dirs);
        $this->assertEquals($expected, $actual);
    }

    public function test_formatDirectoryPaths_returnsAssociativeArrayOfTrimmedPathsWithTrailingSlashes()
    {
        $dirs = array(
            'dir1' => '  ' . dirname(__FILE__) . '   ',
            'dir2' => dirname(dirname(__FILE__)) . '/ ',
            'dir3' => dirname(dirname(dirname(__FILE__))) . '                     ',
        );
        $expected = array(
            'dir1' => dirname(__FILE__) . '/',
            'dir2' => dirname(dirname(__FILE__)) . '/',
            'dir3' => dirname(dirname(dirname(__FILE__))) . '/',
        );
        $actual = PathWorker::formatDirectoryPaths($dirs);
        $this->assertEquals($expected, $actual);
    }

    public function formatDirectoryPathProvider()
    {
        return array(
            array( '  ' . dirname(__FILE__) . '   ', dirname(__FILE__) . '/' ),
            array( dirname(dirname(__FILE__)) . '/ ', dirname(dirname(__FILE__)) . '/' ),
            array( dirname(dirname(dirname(__FILE__))) . '                     ', dirname(dirname(dirname(__FILE__))) . '/' ),
        );
    }

    /**
     * @dataProvider formatDirectoryPathProvider
     */
    public function test_formatDirectoryPaths_returnsSingleTrimmedPathWithTrailingSlash($dir, $expected)
    {
        $actual = PathWorker::formatDirectoryPaths($dir);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider formatDirectoryPathProvider
     */
    public function test_formatDirectoryPath_returnsTrimmedPathWithTrailingSlash($dir, $expected)
    {
        $actual = PathWorker::formatDirectoryPath($dir);
        $this->assertEquals($expected, $actual);
    }

    public function test_getClassNameFromFilePath_returnsClassName()
    {
        $expected = 'TestEntity';
        $path = $this->rootDir . $expected . '.php';
        $actual = PathWorker::getClassNameFromFilePath($path);
        $this->assertEquals($expected, $actual);
    }

}
