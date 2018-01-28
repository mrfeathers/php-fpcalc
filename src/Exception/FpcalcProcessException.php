<?php

namespace Fpcalc\Exception;

use Symfony\Component\Process\Exception\ProcessFailedException;

class FpcalcProcessException extends FpcalcException
{
    /**
     * FpcalcProcessException constructor.
     *
     * @param string $message
     * @param int $code
     * @param ProcessFailedException|null $previous
     */
    public function __construct(string $message = "", int $code = 0, ProcessFailedException $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
