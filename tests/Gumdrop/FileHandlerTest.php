<?php
namespace Gumdrop\Tests;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/FileHandler.php';
require_once __DIR__ . '/../../Gumdrop/PageCollection.php';
require_once __DIR__ . '/../../Gumdrop/Twig.php';

class FileHandler extends \Gumdrop\Tests\TestCase
{
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
        $FSTestHelper = $this->createTestFSForStaticAndHtmlFiles();
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
        $FSTestHelper = $this->createTestFSForStaticAndHtmlFiles();
        $location = $FSTestHelper->getTemporaryPath();

        $app = $this->getApp();
        $app->setSourceLocation($location);

        $FileHandler = new \Gumdrop\FileHandler($app);
        $staticFiles = $FileHandler->listStaticFiles();
        $this->assertFalse(in_array('_layout/file1', $staticFiles));
    }

    public function testFindStaticFilesReturnsDoesNotReturnMarkdownFiles()
    {
        $FSTestHelper = $this->createTestFSForStaticAndHtmlFiles();
        $location = $FSTestHelper->getTemporaryPath();

        $app = $this->getApp();
        $app->setSourceLocation($location);

        $FileHandler = new \Gumdrop\FileHandler($app);
        $staticFiles = $FileHandler->listStaticFiles();
        $this->assertFalse(in_array('markdown_file.md', $staticFiles));
        $this->assertFalse(in_array('folder/markdown_file.markdown', $staticFiles));
    }

    public function testFindStaticFilesReturnsDoesNotReturnHTMLFiles()
    {
        $FSTestHelper = $this->createTestFSForStaticAndHtmlFiles();
        $location = $FSTestHelper->getTemporaryPath();

        $app = $this->getApp();
        $app->setSourceLocation($location);

        $FileHandler = new \Gumdrop\FileHandler($app);
        $staticFiles = $FileHandler->listStaticFiles();
        $this->assertFalse(in_array('index.htm', $staticFiles));
        $this->assertFalse(in_array('folder/index.html', $staticFiles));
    }

    public function testCopyStaticFilesCopiesAllTheFilesAtTheCorrectPlace()
    {
        $FSTestHelperForDestination = new \FSTestHelper\FSTestHelper();
        $destination = $FSTestHelperForDestination->getTemporaryPath();
        $destination = realpath($destination);

        $FSTestHelper = $this->createTestFSForStaticAndHtmlFiles();
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
            throw $e;
        }
    }

    public function testCopyStaticFilesCreatesFoldersWithTheSamePermissionsAsSource()
    {
        $FSTestHelperForDestination = new \FSTestHelper\FSTestHelper();
        $destination = $FSTestHelperForDestination->getTemporaryPath();
        $destination = realpath($destination);

        $FSTestHelper = $this->createTestFSForStaticAndHtmlFiles();
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

    }

    public function testListTwigFilesReturnsTwigFiles()
    {
        $FSTestHelper = $this->createTestFSForStaticAndHtmlFiles();
        $location = $FSTestHelper->getTemporaryPath();

        $app = $this->getApp();
        $app->setSourceLocation($location);

        $FileHandler = new \Gumdrop\FileHandler($app);
        $twigFiles = $FileHandler->listTwigFiles();
        $this->assertTrue(in_array('index.twig', $twigFiles));
        $this->assertTrue(in_array('folder/index.twig', $twigFiles));

    }

    public function testListTwigFilesIgnoresTheLayoutFolder()
    {
        $FSTestHelper = $this->createTestFSForStaticAndHtmlFiles();
        $location = $FSTestHelper->getTemporaryPath();

        $app = $this->getApp();
        $app->setSourceLocation($location);

        $FileHandler = new \Gumdrop\FileHandler($app);
        $twigFiles = $FileHandler->listTwigFiles();
        $this->assertFalse(in_array('_layout/file1.twig', $twigFiles));
    }

    public function testRenderTwigFilesRendersTheTwigFiles()
    {
        $FSTestHelper = $this->createTestFSForStaticAndHtmlFiles();
        $location = $FSTestHelper->getTemporaryPath();

//        $FSTestHelperForDestination = new \FSTestHelper\FSTestHelper();
//        $destination = realpath($FSTestHelperForDestination->getTemporaryPath());

        $PageCollectionMock = \Mockery::mock('\Gumdrop\PageCollection');
        $PageCollectionMock
            ->shouldReceive('exportForTwig')
            ->andReturn(array('some array'));


        $twigFiles = array(
            'index.twig',
            'folder/index.twig'
        );

        $app = $this->getApp();
        $app->setSourceLocation($location);
        $SiteTwigMock = \Mockery::mock('\Twig_Environment');
        $SiteTwigMock
            ->shouldReceive('render')
            ->once()
            ->with('index.twig', array('some array'));

        $SiteTwigMock
            ->shouldReceive('render')
            ->once()
            ->with('folder/index.twig', array('some array'));


        $TwigMock = \Mockery::mock('\Gumdrop\Twig');
        $TwigMock
            ->shouldReceive('getSiteEnvironment')
            ->once()
            ->andReturn($SiteTwigMock);

        $app->setTwig($TwigMock);

        $FileHandler = new \Gumdrop\FileHandler($app);
        $FileHandler->renderTwigFiles();

    }

    private function createTestFSForStaticAndHtmlFiles()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->createTree(array(
            'folders' => array(),
            'files' => array(
                array(
                    'path' => '_layout/file1.twig',
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
                array(
                    'path' => 'index.twig',
                    'content' => ''
                ),
                array(
                    'path' => 'folder/index.twig',
                    'content' => ''
                )
            )
        ));
        return $FSTestHelper;
    }
}