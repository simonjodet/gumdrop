<?php

/**
 * Site configuration container
 * @package Gumdrop
 */
namespace Gumdrop;

class SiteConfiguration extends \Gumdrop\Configuration
{
    public function __construct($location)
    {
        $location = $location . '/conf.json';
        if (!file_exists($location))
        {
            throw new Exception('Could not find the configuration file at ' . $location);
        }

        $this->configuration = json_decode(file_get_contents($location), true);
        if (json_last_error() != JSON_ERROR_NONE)
        {
            throw new Exception('Invalid configuration in ' . $location);
        }
    }
}