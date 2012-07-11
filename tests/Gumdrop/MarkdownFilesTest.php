<?php
namespace Gumdrop\tests\units;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/MarkdownFiles.php';

class MarkdownFiles extends \tests\units\TestCase
{
    public function testConvertToHtmlUsesMarkdownParser()
    {
        $app = new \Gumdrop\Application();
        $MarkdownParserMock = \Mockery::mock('MarkdownParserMock');
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
        $app->MarkdownParser = $MarkdownParserMock;


        $files = array(
            __DIR__ . '/markdownFiles/testFile.md',
            __DIR__ . '/markdownFiles/testFile2.md'
        );
        $destination = TMP_FOLDER . $this->getUniqueId();
        mkdir($destination);

        $MarkdownFiles = new \Gumdrop\MarkdownFiles($app);
        $MarkdownFiles->convertToHtml($files, $destination);

        $this->string(file_get_contents($destination . '/testFile.htm'))->isEqualTo('html_content');
        $this->string(file_get_contents($destination . '/testFile2.htm'))->isEqualTo('html_content2');

        unlink($destination . '/testFile.htm');
        unlink($destination . '/testFile2.htm');
    }
}