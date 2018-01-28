<?php

namespace Fpcalc;


use Symfony\Component\Process\Process;

class FpcalcFactory
{
    /**
     * @return FpcalcProcess
     */
    public function create(): FpcalcProcess
    {
        return new FpcalcProcess(new Process(''));
    }
}
