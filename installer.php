<?php
/**
 * Quick start script for Gumdrop-based projects
 * More information including license at http://simonjodet.github.com/gumdrop/
 * Usage: php -r "$(curl -s https://raw.github.com/simonjodet/gumdrop/master/installer.php|tail +2)"
 * @package Gumdrop
 */
if (!defined('STDIN'))
{
    define('STDIN', fopen('php://stdin', 'r'));
}
$project_name = '';
echo 'Please enter the name of your new Gumdrop-based project:' . PHP_EOL;
while (empty($project_name))
{
    $project_name = trim(fread(STDIN, 255));
    if (empty($project_name))
    {
        echo color_echo('You can\'t set an empty project name. Please retry:' . "\033[0m", 'red') . PHP_EOL;
    }
    if (is_dir($project_name))
    {
        echo color_echo('This folder already exist. Please choose another one.', 'red') . PHP_EOL;
        exit(1);
    }
}
echo PHP_EOL . color_echo('-> ', 'green') . 'Creating project folder' . PHP_EOL;
mkdir($project_name);

echo color_echo('-> ', 'green') . 'Downloading Composer' . PHP_EOL;
exec('curl -s https://getcomposer.org/installer | php -- --install-dir=' . $project_name, $output, $return_value);
if ($return_value != 0)
{
    echo color_echo('Something wrong happened!', 'red') . PHP_EOL . implode(PHP_EOL, $output) . PHP_EOL;
    exit(1);
}

echo color_echo('-> ', 'green') . 'Adding minimal configuration files' . PHP_EOL;
file_put_contents($project_name . DIRECTORY_SEPARATOR . 'composer.json', get_composer_json_file());
file_put_contents($project_name . DIRECTORY_SEPARATOR . 'conf.json', get_conf_json());

echo color_echo('-> ', 'green') . 'Installing dependencies' . PHP_EOL;
exec('php ' . $project_name . '/composer.phar install --quiet --working-dir=' . $project_name);

echo PHP_EOL . color_echo('Installation successful!', 'green') . PHP_EOL;

echo get_help($project_name) . PHP_EOL;

function get_composer_json_file()
{
    return
        <<<'EOD'
{
    "config": {
        "vendor-dir": "_vendor"
    },
    "require":{
        "simonjodet/gumdrop":"1.*"
    },
    "scripts":{
        "post-update-cmd":"php _vendor/simonjodet/gumdrop/gumdrop.php install",
        "post-install-cmd":"php _vendor/simonjodet/gumdrop/gumdrop.php install"
    }
}
EOD;
}

function get_conf_json()
{
    return
        <<<'EOD'
{
    "blacklist":[
        "composer.json",
        "composer.lock",
        "composer.phar"
    ]
}
EOD;
}

function get_help($project_name)
{
    return
        <<<EOD
Your project has been created in the "$project_name" folder.

You can now add some markdown files into it and run _vendor/bin/gumdrop to generate your site in the "$project_name/_site/" folder.

Example:
    $ cd $project_name
    $ echo "# My new website" > index.md
    $ _vendor/bin/gumdrop

You should now have a _site/index.htm file containing "<h1>My new website</h1>":
    $ cat _site/index.htm
    <h1>My new website</h1>

You can now run Gumdrop with its built-in webserver (if you're using PHP 5.4+) and change watcher:
    $ _vendor/bin/gumdrop -rw

Your site is available at http://localhost:8000/index.htm.
Any change in the "$project_name" folder will trigger an update in the "$project_name/_site" folder. Just refresh your browser to check the changes you've made.

\033[1;34mVisit http://simonjodet.github.com/gumdrop/ for more information.\033[0m
EOD;
}

function color_echo($msg, $color)
{
    $colors = array(
        'green' => '0;32',
        'red' => '0;31'
    );
    return "\033[" . $colors[$color] . 'm' . $msg . "\033[0m";
}