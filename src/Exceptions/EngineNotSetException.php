<?php

namespace Fabricio872\RegisterCommand\Exceptions;

use Exception;
use Throwable;

class EngineNotSetException extends Exception
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct("No engine set.", 0, $previous);
    }
}