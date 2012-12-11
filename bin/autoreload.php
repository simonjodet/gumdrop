#!/usr/bin/env php
<?php
$source = realpath($_SERVER['argv'][1]) . '/';
$destination = realpath($_SERVER['argv'][2]) . '/';
if ($source == '/' || $source == realpath(__DIR__) . '/' || $source === false)
{
    $destination = '';
}
if ($destination == '/' || $destination == realpath(__DIR__) . '/' || $destination === false)
{
    $destination = '';
}

renderSite($source, $destination);
$last_checksum = getChecksum($source, $destination);

while (true)
{
    $checksum = getChecksum($source, $destination);
    if ($last_checksum != $checksum)
    {
        renderSite($source, $destination);
        $last_checksum = $checksum;
    }
    sleep(2);
}
exit(0);

function renderSite($source, $destination)
{
    passthru(__DIR__ . '/../gumdrop.php ' . $source . ' ' . $destination);
}

function getChecksum($source, $destination)
{
    exec(__DIR__ . '/../gumdrop.php ' . $source . ' ' . $destination . ' -c', $output);
    return $output[0];
}