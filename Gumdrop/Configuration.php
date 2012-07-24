<?php

namespace Gumdrop;

/**
 * Configuration container
 */
class Configuration
{

    /**
     * @var object
     */
    private $configuration;

    /**
     * @param string $location
     */
    public function __construct($location)
    {
        $location = $location . '/conf.json';
        if (!file_exists($location))
        {
            throw new Exception('Could not find the configuration file at ' . $location);
        }

        $this->configuration = json_decode(file_get_contents($location));
        if (json_last_error() != JSON_ERROR_NONE)
        {
            throw new Exception('Invalid configuration in ' . $location);
        }
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        if (!isset($this->configuration->$key))
        {
            return null;
        }
        return $this->configuration->$key;
    }
}