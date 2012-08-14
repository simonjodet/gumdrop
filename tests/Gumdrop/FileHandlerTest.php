<?php
namespace Gumdrop\Tests;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/FileHandler.php';
require_once __DIR__ . '/../../Gumdrop/PageCollection.php';

class FileHandler extends \Gumdrop\Tests\TestCase
{
    private $testLocation;

    public function setUp()
    {
        parent::setUp();
        $this->testLocation = TMP_FOLDER . 'Gumdrop_FileOperations';
    }

    public function createTestLocation($id)
    {
        mkdir($this->testLocation . '/' . $id, 0777, true);
    }

    public function deleteTestLocation($id)
    {
        exec('rm -rf ' . $this->testLocation . '/' . $id);
    }

    public function testListMarkdownFilesListsFilesRecursively()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->createTree(array(
            'folders' => array(),
            'files' => array(
                array(
                    'path' => 'folder/file1.md',
                    'content' => ''
                ),
                array(
                    'path' => 'file2.markdown',
                    'content' => ''
                ),
                array(
                    'path' => 'file3.txt',
                    'content' => ''
                )
            )
        ));

        $app = $this->getApp();
        $app->setSourceLocation($FSTestHelper->getTemporaryPath() . '/');
        $FileHandler = new \Gumdrop\FileHandler($app);
        $list = $FileHandler->listMarkdownFiles();
        $expected = array(
            realpath($FSTestHelper->getTemporaryPath() . '/folder/file1.md'),
            realpath($FSTestHelper->getTemporaryPath() . '/file2.markdown')
        );

        $this->assertEquals($list, $expected);
    }

    public function testGetMarkdownFilesReturnsFilesContent()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->createTree(array(
            'folders' => array(),
            'files' => array(
                array(
                    'path' => 'folder/file1.md',
                    'content' => 'md content 1'
                ),
                array(
                    'path' => 'file2.markdown',
                    'content' => 'md content 2'
                )
            )
        ));

        $app = $this->getApp();
        $app->setSourceLocation($FSTestHelper->getTemporaryPath() . '/');

        $FileHandler = new \Gumdrop\FileHandler($app);
        $Pages = $FileHandler->getMarkdownFiles(array(
            realpath($FSTestHelper->getTemporaryPath() . '/folder/file1.md'),
            realpath($FSTestHelper->getTemporaryPath() . '/file2.markdown')
        ));

        $expected = new \Gumdrop\PageCollection();
        $Page1 = new \Gumdrop\Page($app);
        $Page1->setLocation('folder/file1.md');
        $Page1->setMarkdownContent('md content 1');
        $expected->offsetSet(null, $Page1);
        $Page2 = new \Gumdrop\Page($app);
        $Page2->setLocation('file2.markdown');
        $Page2->setMarkdownContent('md content 2');
        $expected->offsetSet(null, $Page2);

        $this->assertEquals($Pages, $expected);
    }

    public function testFindPageTwigFileReturnsTrueIfThisTwigFileExists()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->createTree(array(
            'folders' => array(),
            'files' => array(
                array(
                    'path' => '_layout/page.twig',
                    'content' => ''
                )
            )
        ));

        $location = $FSTestHelper->getTemporaryPath();

        $app = $this->getApp();
        $app->setSourceLocation($location);

        $FileHandler = new \Gumdrop\FileHandler($app);
        $this->assertTrue($FileHandler->findPageTwigFile());
    }

    public function testFindPageTwigFileReturnsFalseIfThisTwigFileDoesNotExist()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->createTree(array(
            'folders' => array(),
            'files' => array(
                array(
                    'path' => '_layout/empty',
                    'content' => ''
                )
            )
        ));

        $location = $FSTestHelper->getTemporaryPath();

        $app = $this->getApp();
        $app->setSourceLocation($location);

        $FileHandler = new \Gumdrop\FileHandler($app);
        $this->assertFalse($FileHandler->findPageTwigFile());
    }

    public function testFindStaticFilesReturnsStaticFiles()
    {
        $FSTestHelper = $this->createTestFSForStaticFiles();
        $location = $FSTestHelper->getTemporaryPath();

        $app = $this->getApp();
        $app->setSourceLocation($location);

        $FileHandler = new \Gumdrop\FileHandler($app);
        $staticFiles = $FileHandler->listStaticFiles();
        $this->assertEquals(
            array(
                'file1',
                'folder/file2'
            ),
            $staticFiles
        );
    }

    public function testFindStaticFilesReturnsDoesNotReturnLayoutFiles()
    {
        $FSTestHelper = $this->createTestFSForStaticFiles();
        $location = $FSTestHelper->getTemporaryPath();

        $app = $this->getApp();
        $app->setSourceLocation($location);

        $FileHandler = new \Gumdrop\FileHandler($app);
        $staticFiles = $FileHandler->listStaticFiles();
        $this->assertFalse(in_array('_layout/file1', $staticFiles));
    }

    public function testFindStaticFilesReturnsDoesNotReturnMarkdownFiles()
    {
        $FSTestHelper = $this->createTestFSForStaticFiles();
        $location = $FSTestHelper->getTemporaryPath();

        $app = $this->getApp();
        $app->setSourceLocation($location);

        $FileHandler = new \Gumdrop\FileHandler($app);
        $staticFiles = $FileHandler->listStaticFiles();
        $this->assertFalse(in_array('markdown_file.md', $staticFiles));
        $this->assertFalse(in_array('folder/markdown_file.markdown', $staticFiles));
    }

    public function testCopyStaticFilesCopiesAllTheFilesAtTheCorrectPlace()
    {
        $FSTestHelperForDestination = new \FSTestHelper\FSTestHelper();
        $destination = $FSTestHelperForDestination->getTemporaryPath();
        $destination = realpath($destination);

        $FSTestHelper = $this->createTestFSForStaticFiles();
        $location = $FSTestHelper->getTemporaryPath();

        $app = $this->getApp();
        $app->setSourceLocation($location);
        $app->setDestinationLocation($destination);

        $FileHandler = new \Gumdrop\FileHandler($app);
        $FileHandler->copyStaticFiles();

        try
        {
            $this->assertFileExists($destination . '/file1');
            $this->assertFileExists($destination . '/folder/file2');
        }
        catch (\Exception $e)
        {
            exec('rm -rf ' . $destination);
            throw $e;
        }
        exec('rm -rf ' . $destination);
    }

    public function testCopyStaticFilesCreatesFoldersWithTheSamePermissionsAsSource()
    {
        $FSTestHelperForDestination = new \FSTestHelper\FSTestHelper();
        $destination = $FSTestHelperForDestination->getTemporaryPath();
        $destination = realpath($destination);

        $FSTestHelper = $this->createTestFSForStaticFiles();
        $location = $FSTestHelper->getTemporaryPath();
        chmod($location . '/folder', 0755);


        $app = $this->getApp();
        $app->setSourceLocation($location);
        $app->setDestinationLocation($destination);

        $FileHandler = new \Gumdrop\FileHandler($app);
        $FileHandler->copyStaticFiles();

        $stats = stat($destination . '/folder');
        $mode = decoct($stats['mode']);
        $this->assertEquals('40755', $mode);

        exec('rm -rf ' . $destination);
    }

    private function createTestFSForStaticFiles()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->createTree(array(
            'folders' => array(),
            'files' => array(
                array(
                    'path' => '_layout/file1',
                    'content' => ''
                ),
                array(
                    'path' => 'folder/file2',
                    'content' => ''
                ),
                array(
                    'path' => 'folder/markdown_file.markdown',
                    'content' => ''
                ),
                array(
                    'path' => 'file1',
                    'content' => ''
                ),
                array(
                    'path' => 'markdown_file.md',
                    'content' => ''
                ),
            )
        ));
        return $FSTestHelper;
    }
}