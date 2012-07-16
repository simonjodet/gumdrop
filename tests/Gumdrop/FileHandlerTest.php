<?php
namespace Gumdrop\tests\units;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/FileHandler.php';

class FileHandler extends \tests\units\TestCase
{
    private $testLocation;

    public function beforeTestMethod($method)
    {
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

    public function testListMarkdwonFilesListsFilesRecursively()
    {
        $id = '/' . $this->getUniqueId();
        mkdir($this->testLocation . '/' . $id . '/folder', 0777, true);
        touch($this->testLocation . '/' . $id . '/folder/file1.md');
        touch($this->testLocation . '/' . $id . '/file2.markdown');
        touch($this->testLocation . '/' . $id . '/file3.txt');

        $FileHandler = new \Gumdrop\FileHandler();
        $list = $FileHandler->listMarkdownFiles($this->testLocation . '/' . $id . '/');
        $expected = array(
            realpath($this->testLocation . '/' . $id . '/folder/file1.md'),
            realpath($this->testLocation . '/' . $id . '/file2.markdown')
        );

        unlink($this->testLocation . '/' . $id . '/folder/file1.md');
        unlink($this->testLocation . '/' . $id . '/file2.markdown');
        unlink($this->testLocation . '/' . $id . '/file3.txt');
        rmdir($this->testLocation . '/' . $id . '/folder');
        $this->deleteTestLocation($id);

        $this->array($list)->isEqualTo($expected);
    }

    public function testGetMarkdownFilesReturnsFilesContent()
    {
        $id = '/' . $this->getUniqueId();
        mkdir($this->testLocation . '/' . $id . '/folder', 0777, true);
        file_put_contents($this->testLocation . '/' . $id . '/folder/file1.md', 'md content 1');
        file_put_contents($this->testLocation . '/' . $id . '/file2.markdown', 'md content 2');

        $FileHandler = new \Gumdrop\FileHandler();
        $list = $FileHandler->getMarkdownFiles(array(
            realpath($this->testLocation . '/' . $id . '/folder/file1.md'),
            realpath($this->testLocation . '/' . $id . '/file2.markdown')
        ), $this->testLocation . '/' . $id . '/');

        $expected = array(
            'folder/file1.md' => 'md content 1',
            'file2.markdown' => 'md content 2'
        );

        unlink($this->testLocation . '/' . $id . '/folder/file1.md');
        unlink($this->testLocation . '/' . $id . '/file2.markdown');
        rmdir($this->testLocation . '/' . $id . '/folder');
        $this->deleteTestLocation($id);

        $this->array($list)->isEqualTo($expected);
    }
}