<?php

namespace Gumdrop;

/**
 * Twig Environment provider
 */
class Twig
{
    /**
     * @var \Gumdrop\Application
     */
    private $app;

    /**
     * @param \Gumdrop\Application $app Dependency injector
     */
    public function __construct(\Gumdrop\Application $app)
    {
        $this->app = $app;
    }

    /**
     * Get Twig layout environment
     * @return \Twig_Environment Twig layout environment
     */
    public function getLayoutEnvironment()
    {
        return new \Twig_Environment(
            new \Twig_Loader_Filesystem($this->app->getSourceLocation() . '/_layout/'),
            array(
                'autoescape' => false,
                'strict_variables' => false
            )
        );
    }

    /**
     * Get the Twig page environment
     * @return \Twig_Environment Twig page environment
     */
    public function getPageEnvironment()
    {
        return new \Twig_Environment(
            new \Twig_Loader_Filesystem($this->app->getSourceLocation() . '/'),
            array(
                'autoescape' => false,
                'strict_variables' => false
            )
        );
    }
}
