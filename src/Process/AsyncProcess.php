<?php

namespace Barryvdh\Queue\Process;

use Symfony\Component\Process\Process;

class AsyncProcess extends Process
{
    /**
     * Destructor.
     *
     * @return void
     */
    public function __destruct()
    {
        // do not stop the process
    }
}
