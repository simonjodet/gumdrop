<?php
/**
 * Installation helper class
 * @package Gumdrop
 */
namespace Gumdrop;

/**
 * Installation helper class
 */
class Installation
{
    public static function setPermissions()
    {
        echo 'Setting file permissions' . PHP_EOL;
        exec('chmod +x ' . __DIR__ . '/../bin/gumdrop ' . __DIR__ . '/../gumdrop.php');
    }
}
