<?php
/**
 * Twig Environment provider
 * @package Gumdrop
 */

namespace Gumdrop;

class TwigEnvironments
{
    private $app;

    public function __construct(\Gumdrop\Application $app)
    {
        $this->app = $app;
    }

    public function getLayoutEnvironment()
    {
        try {
            return new \Twig_Environment(
                new \Twig_Loader_Filesystem($this->app->getSourceLocation() . '/_layout/'),
                array(
                    'autoescape' => false,
                    'strict_variables' => false
                )
            );
        } catch (\Twig_Error_Loader $e) {
            return null;
        }
    }

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
