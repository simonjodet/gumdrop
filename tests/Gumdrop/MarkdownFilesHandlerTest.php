<?php
namespace Gumdrop\tests\units;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/MarkdownFilesHandler.php';
require_once __DIR__ . '/../../vendor/dflydev/markdown/src/dflydev/markdown/IMarkdownParser.php';
require_once __DIR__ . '/../../vendor/dflydev/markdown/src/dflydev/markdown/MarkdownParser.php';

class MarkdownFilesHandler extends \tests\units\TestCase
{
    public function testConvertToHtmlUsesMarkdownParser()
    {
        $app = new \Gumdrop\Application();
        $MarkdownParserMock = \Mockery::mock('\dflydev\markdown\MarkdownParser');
        $MarkdownParserMock
            ->shouldReceive('transformMarkdown')
            ->once()
            ->with(file_get_contents(__DIR__ . '/markdownFiles/testFile.md'))
            ->andReturn('html_content');

        $MarkdownParserMock
            ->shouldReceive('transformMarkdown')
            ->once()
            ->with(file_get_contents(__DIR__ . '/markdownFiles/testFile2.md'))
            ->andReturn('html_content2');
        $app->setMarkdownParser($MarkdownParserMock);


        $files = array(
            __DIR__ . '/markdownFiles/testFile.md',
            __DIR__ . '/markdownFiles/testFile2.md'
        );
        $destination = TMP_FOLDER . $this->getUniqueId();
        mkdir($destination);

        $MarkdownFiles = new \Gumdrop\MarkdownFilesHandler($app);
        $MarkdownFiles->convertToHtml($files, $destination);

        $this->string(file_get_contents($destination . '/testFile.htm'))->isEqualTo('html_content');
        $this->string(file_get_contents($destination . '/testFile2.htm'))->isEqualTo('html_content2');

        unlink($destination . '/testFile.htm');
        unlink($destination . '/testFile2.htm');
        rmdir($destination);
    }
}