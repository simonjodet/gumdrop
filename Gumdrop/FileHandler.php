<?php

namespace Gumdrop;

class FileHandler
{
    public function listMarkdownFiles($location)
    {
        return glob($location . '/*.{md,markdown}', GLOB_BRACE);
    }
}