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
        $id = '/' . $this->getUniqueId();
        mkdir($this->testLocation . '/' . $id . '/folder', 0777, true);
        touch($this->testLocation . '/' . $id . '/folder/file1.md');
        touch($this->testLocation . '/' . $id . '/file2.markdown');
        touch($this->testLocation . '/' . $id . '/file3.txt');

        $FileHandler = new \Gumdrop\FileHandler($this->getApp(), $this->testLocation . '/' . $id . '/');
        $list = $FileHandler->listMarkdownFiles();
        $expected = array(
            realpath($this->testLocation . '/' . $id . '/folder/file1.md'),
            realpath($this->testLocation . '/' . $id . '/file2.markdown')
        );

        unlink($this->testLocation . '/' . $id . '/folder/file1.md');
        unlink($this->testLocation . '/' . $id . '/file2.markdown');
        unlink($this->testLocation . '/' . $id . '/file3.txt');
        rmdir($this->testLocation . '/' . $id . '/folder');
        $this->deleteTestLocation($id);

        $this->assertEquals($list, $expected);
    }

    public function testGetMarkdownFilesReturnsFilesContent()
    {
        $id = '/' . $this->getUniqueId();
        mkdir($this->testLocation . '/' . $id . '/folder', 0777, true);
        file_put_contents($this->testLocation . '/' . $id . '/folder/file1.md', 'md content 1');
        file_put_contents($this->testLocation . '/' . $id . '/file2.markdown', 'md content 2');

        $app = $this->getApp();

        $FileHandler = new \Gumdrop\FileHandler($app, $this->testLocation . '/' . $id . '/');
        $Pages = $FileHandler->getMarkdownFiles(array(
            realpath($this->testLocation . '/' . $id . '/folder/file1.md'),
            realpath($this->testLocation . '/' . $id . '/file2.markdown')
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

        unlink($this->testLocation . '/' . $id . '/folder/file1.md');
        unlink($this->testLocation . '/' . $id . '/file2.markdown');
        rmdir($this->testLocation . '/' . $id . '/folder');
        $this->deleteTestLocation($id);

        $this->assertEquals($Pages, $expected);
    }

    public function testFindPageTwigFileReturnsTrueIfThisTwigFileExists()
    {
        $location = __DIR__ . '/FileHandler/with_page_twig';

        $FileHandler = new \Gumdrop\FileHandler($this->getApp(), $location);
        $this->assertTrue($FileHandler->findPageTwigFile());
    }

    public function testFindPageTwigFileReturnsFalseIfThisTwigFileDoesNotExist()
    {
        $location = __DIR__ . '/FileHandler/without_page_twig';

        $FileHandler = new \Gumdrop\FileHandler($this->getApp(), $location);
        $this->assertFalse($FileHandler->findPageTwigFile());
    }
}