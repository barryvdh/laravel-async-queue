<?php

namespace Barryvdh\Queue;

use Symfony\Component\Process\PhpExecutableFinder;

class PhpFinder extends PhpExecutableFinder
{
    /**
     * Finds the PHP executable, escaped for use in a command line.
     *
     * @return string
     */
    public function findForShell()
    {
        $path = escapeshellarg($this->find(false));
        $args = implode(' ', $this->findArguments());

        return trim($path.' '.$args);
    }
}
