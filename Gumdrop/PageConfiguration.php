<?php

namespace Gumdrop;

/**
 * Page configuration container
 */
class PageConfiguration extends \Gumdrop\Configuration
{
    /**
     * Extracts configuration header from Markdown content
     *
     * @param string $content
     * @throws \Gumdrop\Exception
     */
    public function extractHeader($content)
    {
        $count = preg_match('#^\*\*\*\n(.*)\n\*\*\*\n(.*)$#sUD', $content, $matches);
        if ($count == 1)
        {
            $this->configuration = json_decode($matches[1]);
            if (json_last_error() != JSON_ERROR_NONE)
            {
                throw new Exception('Invalid configuration');
            }
            return $matches[2];
        }
        return $content;
    }
}