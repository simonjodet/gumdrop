<?php
namespace Gumdrop\Tests;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/FileHandler.php';
require_once __DIR__ . '/../../Gumdrop/PageCollection.php';
require_once __DIR__ . '/../../Gumdrop/TwigEnvironments.php';

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
                    'path' => 'file3',
                    'content' => ''
                )
            )
        ));

        $app = $this->getApp();
        $app->setSiteConfiguration(array());

        $app->setSourceLocation($FSTestHelper->getTemporaryPath() . '/');
        $FileHandler = new \Gumdrop\FileHandler($app);
        $list = $FileHandler->listMarkdownFiles();
        $expected = array(
            realpath($FSTestHelper->getTemporaryPath() . '/file2.markdown'),
            realpath($FSTestHelper->getTemporaryPath() . '/folder/file1.md')
        );

        $this->assertEquals($expected, $list);
    }

    public function testListMarkdownFilesIgnoresFilesAndFoldersWithUnderscorePrefix()
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
                    'path' => 'folder/_file1.md',
                    'content' => ''
                ),
                array(
                    'path' => '_folder/file2.markdown',
                    'content' => ''
                ),
                array(
                    'path' => '_file2.markdown',
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
            realpath($FSTestHelper->getTemporaryPath() . '/folder/file1.md')
        );

        $this->assertEquals($list, $expected);
    }

    public function testListMarkdownFilesIgnoresBlackListedFiles()
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
        $app->setSiteConfiguration(array(
            'blacklist' => array('file2.markdown')
        ));
        $app->setSourceLocation($FSTestHelper->getTemporaryPath() . '/');
        $FileHandler = new \Gumdrop\FileHandler($app);
        $list = $FileHandler->listMarkdownFiles();


        $expected = array(
            realpath($FSTestHelper->getTemporaryPath() . '/folder/file1.md')
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

    public function testListStaticFilesReturnsStaticFiles()
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

    public function testListStaticFilesReturnsDoesNotReturnMarkdownFiles()
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

    public function testListStaticFilesReturnsDoesNotReturnHTMLFiles()
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

    public function testListStaticFilesIgnoresFilesAndFoldersWithUnderscorePrefix()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->createTree(array(
            'folders' => array(),
            'files' => array(
                array(
                    'path' => 'folder/file1',
                    'content' => ''
                ),
                array(
                    'path' => 'folder/_file1',
                    'content' => ''
                ),
                array(
                    'path' => '_folder/file2',
                    'content' => ''
                ),
                array(
                    'path' => '_file2',
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
        $list = $FileHandler->listStaticFiles();


        $expected = array(
            'file3.txt',
            'folder/file1'
        );

        $this->assertEquals($expected, $list);
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
        $this->assertTrue(in_array('index.htm.twig', $twigFiles));
        $this->assertTrue(in_array('folder/pages.rss.twig', $twigFiles));

    }

    public function testListTwigFilesIgnoresFilesAndFoldersWithUnderscorePrefix()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->createTree(array(
            'folders' => array(),
            'files' => array(
                array(
                    'path' => 'folder/file1.twig',
                    'content' => ''
                ),
                array(
                    'path' => 'folder/_file1.twig',
                    'content' => ''
                ),
                array(
                    'path' => '_folder/file2.twig',
                    'content' => ''
                ),
                array(
                    'path' => '_file2.twig',
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
        $list = $FileHandler->listTwigFiles();


        $expected = array(
            'folder/file1.twig'
        );

        $this->assertEquals($expected, $list);
    }

    public function testClearDestinationLocationRemovesAllContent()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->createTree(array(
            'folders' => array(),
            'files' => array(
                array(
                    'path' => 'folder/file1.htm',
                    'content' => ''
                ),
                array(
                    'path' => 'file2.htm',
                    'content' => ''
                ),
                array(
                    'path' => 'file3.txt',
                    'content' => ''
                )
            )
        ));

        $location = $FSTestHelper->getTemporaryPath();

        $app = $this->getApp();
        $app->setDestinationLocation($location);

        $FileHandler = new \Gumdrop\FileHandler($app);
        $FileHandler->clearDestinationLocation();
        $this->assertEquals(array('.', '..'), scandir($location));
    }

    public function testGetSourceFolderHashUpdatesTheReturnedHashWhenFileIsModified()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->createTree(array(
            'folders' => array(),
            'files' => array(
                array(
                    'path' => 'file1',
                    'content' => 'content'
                ),
                array(
                    'path' => 'folder/file2',
                    'content' => 'content'
                ),
                array(
                    'path' => 'file3',
                    'content' => 'content'
                )
            )
        ));
        $app = $this->getApp();
        $app->setSourceLocation($FSTestHelper->getTemporaryPath());

        $FileHandler = new \Gumdrop\FileHandler($app);
        $initialHash = $FileHandler->getSourceFolderHash();

        //Testing file edit
        file_put_contents($FSTestHelper->getTemporaryPath() . '/file1', 'something else');
        $updatedHash = $FileHandler->getSourceFolderHash();
        $this->assertNotEquals(
            $initialHash,
            $updatedHash
        );

        // Testing file deletion
        $initialHash = $updatedHash;

        unlink($FSTestHelper->getTemporaryPath() . '/file1');
        $updatedHash = $FileHandler->getSourceFolderHash();

        $this->assertNotEquals(
            $initialHash,
            $updatedHash
        );

        // Testing file creation
        $initialHash = $updatedHash;

        touch($FSTestHelper->getTemporaryPath() . '/file1');
        $updatedHash = $FileHandler->getSourceFolderHash();

        $this->assertNotEquals(
            $initialHash,
            $updatedHash
        );

        // Testing file rename
        $initialHash = $updatedHash;

        rename($FSTestHelper->getTemporaryPath() . '/file3', $FSTestHelper->getTemporaryPath() . '/file4');
        $updatedHash = $FileHandler->getSourceFolderHash();

        $this->assertNotEquals(
            $initialHash,
            $updatedHash
        );
    }
}