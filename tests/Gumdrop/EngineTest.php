<?php
namespace Gumdrop\tests\units;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/Engine.php';
require_once __DIR__ . '/../../vendor/dflydev/markdown/src/dflydev/markdown/IMarkdownParser.php';
require_once __DIR__ . '/../../vendor/dflydev/markdown/src/dflydev/markdown/MarkdownParser.php';

class Engine extends \tests\units\TestCase
{
    public function testConvertMarkdownToHtmlUsesTheMarkdownParser()
    {
        $app = new \Gumdrop\Application();
        $MarkdownParserMock = \Mockery::mock('\dflydev\markdown\MarkdownParser');
        $MarkdownParserMock
            ->shouldReceive('transformMarkdown')
            ->once()
            ->with('page 1 content')
            ->andReturn('html content 1');

        $MarkdownParserMock
            ->shouldReceive('transformMarkdown')
            ->once()
            ->with('page 2 content')
            ->andReturn('html content 2');
        $app->setMarkdownParser($MarkdownParserMock);

        $pages = array(
            'file_1_path.md' => 'page 1 content',
            'file_2_path.md' => 'page 2 content'
        );

        $MarkdownFiles = new \Gumdrop\Engine($app);
        $converted_pages = $MarkdownFiles->convertMarkdownToHtml($pages);

        $this->array($converted_pages)->isEqualTo(
            array(
                'file_1_path.md' => 'html content 1',
                'file_2_path.md' => 'html content 2'
            )
        );
    }

    public function testApplyTwigLayoutAppliesTheLayoutToPages()
    {
        $app = new \Gumdrop\Application();
        $Twig_Environment = \Mockery::mock('\Twig_Environment');
        $Twig_Environment
            ->shouldReceive('render')
            ->with(
            'page.twig',
            array(
                'content' => 'html content 1'
            ))
            ->once()
            ->andReturn('twig content 1');

        $Twig_Environment
            ->shouldReceive('render')
            ->with(
            'page.twig',
            array(
                'content' => 'html content 2'
            ))
            ->once()
            ->andReturn('twig content 2');

        $pages = array(
            'file_1_path.md' => 'html content 1',
            'file_2_path.md' => 'html content 2'
        );

        $app->setTwigEnvironment($Twig_Environment);
        $MarkdownFiles = new \Gumdrop\Engine($app);
        $converted_pages = $MarkdownFiles->applyTwigLayout($pages);

        $this->array($converted_pages)->isEqualTo(
            array(
                'file_1_path.md' => 'twig content 1',
                'file_2_path.md' => 'twig content 2'
            )
        );
    }

    public function testWriteHtmlFilesWritePagesToHtmFiles()
    {
        $app = new \Gumdrop\Application();
        $pages = array(
            'folder/file_1_path.md' => 'twig content 1',
            'file_2_path.md' => 'twig content 2'
        );
        $destination = TMP_FOLDER . $this->getUniqueId();
        mkdir($destination);

        $MarkdownFiles = new \Gumdrop\Engine($app);
        $MarkdownFiles->writeHtmFiles($pages, $destination);

        $this->string(file_get_contents($destination . '/folder/file_1_path.htm'))->isEqualTo('twig content 1');
        $this->string(file_get_contents($destination . '/file_2_path.htm'))->isEqualTo('twig content 2');

        unlink($destination . '/folder/file_1_path.htm');
        unlink($destination . '/file_2_path.htm');
        rmdir($destination.'/folder');
        rmdir($destination);
    }
}