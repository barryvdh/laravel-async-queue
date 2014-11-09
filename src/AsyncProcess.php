<?php

namespace Barryvdh\Queue;

use Symfony\Component\Process\Process;

class AsyncProcess extends Process
{
    public function __destruct()
    {
        // do not stop the process
    }
}
