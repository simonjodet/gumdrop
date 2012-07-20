<?php
namespace Gumdrop\tests\units;

require_once __DIR__ . '/../TestCase.php';
require_once __DIR__ . '/../../Gumdrop/Engine.php';
require_once __DIR__ . '/../../vendor/dflydev/markdown/src/dflydev/markdown/IMarkdownParser.php';
require_once __DIR__ . '/../../vendor/dflydev/markdown/src/dflydev/markdown/MarkdownParser.php';

class Engine extends \tests\units\TestCase
{
    /**
     * @isNotVoid
     */
    public function testConvertMarkdownToHtmlCallsThePageMethod()
    {
        $Page1 = \Mockery::mock('\Gumdrop\Page');
        $Page1
            ->shouldReceive('convertMarkdownToHtml')
            ->once();
        $Page2 = \Mockery::mock('\Gumdrop\Page');
        $Page2
            ->shouldReceive('convertMarkdownToHtml')
            ->once();
        $PageCollection = new \Gumdrop\PageCollection(array(
            $Page1,
            $Page2
        ));

        $MarkdownFiles = new \Gumdrop\Engine($this->getApp());
        $MarkdownFiles->convertMarkdownToHtml($PageCollection);
    }

    public function testApplyTwigLayoutCallsThePageMethod()
    {
        $Page1 = \Mockery::mock('\Gumdrop\Page');
        $Page1
            ->shouldReceive('applyTwigLayout')
            ->once();
        $Page2 = \Mockery::mock('\Gumdrop\Page');
        $Page2
            ->shouldReceive('applyTwigLayout')
            ->once();
        $PageCollection = new \Gumdrop\PageCollection(array(
            $Page1,
            $Page2
        ));

        $MarkdownFiles = new \Gumdrop\Engine($this->getApp());
        $MarkdownFiles->applyTwigLayout($PageCollection);

    }

    public function testWriteHtmlFilesWritePagesToHtmFiles()
    {
        $app = new \Gumdrop\Application();

        $Page1 = new \Gumdrop\Page($this->getApp());
        $Page1->setLocation('folder/file_1_path.md');
        $Page1->setHtmlContent('twig content 1');
        $Page2 = new \Gumdrop\Page($this->getApp());
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