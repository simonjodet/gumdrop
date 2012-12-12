<?php

/**
 * Page configuration container
 * @package Gumdrop
 */
namespace Gumdrop;

class PageConfiguration extends \Gumdrop\Configuration
{
    public function extractPageHeader($content)
    {
        $count = preg_match('#^\*\*\*\n(.*)\n\*\*\*\n(.*)$#sUD', $content, $matches);
        if ($count == 1)
        {
            $this->configuration = json_decode($matches[1], true);
            if (json_last_error() != JSON_ERROR_NONE)
            {
                throw new Exception('Invalid configuration');
            }
            return $matches[2];
        }
        return $content;
    }
}