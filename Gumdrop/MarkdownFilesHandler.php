<?php

namespace Gumdrop;

class MarkdownFilesHandler
{
    /**
     * @var \Gumdrop\Application
     */
    private $app;

    public function __construct(\Gumdrop\Application $app)
    {
        $this->app = $app;
    }

    public function convertToHtml($files, $destination)
    {
        foreach ($files as $file)
        {
            $destination_file = $destination . '/' . pathinfo($file, PATHINFO_FILENAME) . '.htm';
            file_put_contents($destination_file, $this->app->getMarkdownParser()->transformMarkdown(file_get_contents($file)));
        }
    }
}
