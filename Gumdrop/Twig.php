<?php
/**
 * Twig Environment provider
 * @package Gumdrop
 */

namespace Gumdrop;

/**
 * Twig Environment provider
 */
class Twig
{
    /**
     * Dependency injector
     * @var \Gumdrop\Application
     */
    private $app;

    /**
     * Constructor
     *
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
        try
        {
            return new \Twig_Environment(
                new \Twig_Loader_Filesystem($this->app->getSourceLocation() . '/_layout/'),
                array(
                    'autoescape' => false,
                    'strict_variables' => false
                )
            );
        }
        catch (\Twig_Error_Loader $e)
        {
            return null;
        }
    }

    /**
     * Get the Twig page environment
     * @return \Twig_Environment Twig page environment
     */
    public function getPageEnvironment()
    {
        return new \Twig_Environment(
            new \Twig_Loader_String(),
            array(
                'autoescape' => false,
                'strict_variables' => false
            )
        );
    }

    /**
     * Get Twig site environment
     * @return \Twig_Environment Twig site environment
     */
    public function getSiteEnvironment()
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
