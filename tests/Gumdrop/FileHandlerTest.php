<?php
namespace Gumdrop\tests\units;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/FileHandler.php';

class FileHandler extends \tests\units\TestCase
{
    private $testLocation = '/tmp/Gumdrop_FileOperations';

    public function beforeTestMethod($method)
    {
        mkdir($this->testLocation, 0777, true);
    }

    public function afterTestMethod($method)
    {
        rmdir($this->testLocation);
    }

    public function testListMarkdownFilesReturnsFilesWithMarkdownExtensions()
    {
        touch($this->testLocation . '/file1.md');
        touch($this->testLocation . '/file2.markdown');
        touch($this->testLocation . '/file3.txt');

        $FileHandler = new \Gumdrop\FileHandler();
        $list = $FileHandler->listMarkdownFiles($this->testLocation);

        unlink($this->testLocation . '/file1.md');
        unlink($this->testLocation . '/file2.markdown');
        unlink($this->testLocation . '/file3.txt');

        $this->array($list)->isEqualTo(
            array(
                $this->testLocation . '/file1.md',
                $this->testLocation . '/file2.markdown'
            )
        );
    }
}