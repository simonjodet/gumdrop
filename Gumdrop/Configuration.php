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
    protected $configuration;

    /**
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        if(!is_object($this->configuration))
        {
            $this->configuration = new \StdClass();
        }
        $this->configuration->$key = $value;
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