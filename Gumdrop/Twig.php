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
}
