<?php

namespace Fabricio872\RegisterCommand\Exceptions;

use Exception;
use Throwable;

class EngineNotSupported extends Exception
{

    public function __construct(string $engine, string $question, ?Throwable $previous = null)
    {
        parent::__construct(sprintf("Engine: %s is not supported for %s", $engine, $question), 0, $previous);
    }
}