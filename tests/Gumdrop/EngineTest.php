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
            ->with('md content 1')
            ->andReturn('html content 1');

        $MarkdownParserMock
            ->shouldReceive('transformMarkdown')
            ->once()
            ->with('md content 2')
            ->andReturn('html content 2');
        $app->setMarkdownParser($MarkdownParserMock);

        $Page1 = new \Gumdrop\Page();
        $Page1->setMarkdownContent('md content 1');
        $Page2 = new \Gumdrop\Page();
        $Page2->setMarkdownContent('md content 2');
        $PageCollection = new \Gumdrop\PageCollection(array(
            $Page1,
            $Page2
        ));

        $MarkdownFiles = new \Gumdrop\Engine($app);
        $convertedPageCollection = $MarkdownFiles->convertMarkdownToHtml($PageCollection);

        $this->string($convertedPageCollection[0]->getHtmlContent())->isEqualTo('html content 1');
        $this->string($convertedPageCollection[1]->getHtmlContent())->isEqualTo('html content 2');
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

        $Page1 = new \Gumdrop\Page();
        $Page1->setHtmlContent('html content 1');
        $Page2 = new \Gumdrop\Page();
        $Page2->setHtmlContent('html content 2');
        $PageCollection = new \Gumdrop\PageCollection(array(
            $Page1,
            $Page2
        ));

        $app->setTwigEnvironment($Twig_Environment);
        $MarkdownFiles = new \Gumdrop\Engine($app);
        $convertedPageCollection = $MarkdownFiles->applyTwigLayout($PageCollection);

        $this->string($convertedPageCollection[0]->getHtmlContent())->isEqualTo('twig content 1');
        $this->string($convertedPageCollection[1]->getHtmlContent())->isEqualTo('twig content 2');
    }

    public function testWriteHtmlFilesWritePagesToHtmFiles()
    {
        $app = new \Gumdrop\Application();

        $Page1 = new \Gumdrop\Page();
        $Page1->setLocation('folder/file_1_path.md');
        $Page1->setHtmlContent('twig content 1');
        $Page2 = new \Gumdrop\Page();
        $Page2->setLocation('file_2_path.md');
        $Page2->setHtmlContent('twig content 2');
        $PageCollection = new \Gumdrop\PageCollection(array(
            $Page1,
            $Page2
        ));

        $destination = TMP_FOLDER . $this->getUniqueId();
        mkdir($destination);

        $MarkdownFiles = new \Gumdrop\Engine($app);
        $MarkdownFiles->writeHtmFiles($PageCollection, $destination);

        $this->string(file_get_contents($destination . '/folder/file_1_path.htm'))->isEqualTo('twig content 1');
        $this->string(file_get_contents($destination . '/file_2_path.htm'))->isEqualTo('twig content 2');

        unlink($destination . '/folder/file_1_path.htm');
        unlink($destination . '/file_2_path.htm');
        rmdir($destination . '/folder');
        rmdir($destination);
    }
}