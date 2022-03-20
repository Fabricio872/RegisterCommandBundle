<?php

namespace Fabricio872\RegisterCommand\Exceptions;

use Exception;
use Throwable;

class QuestionNotFoundException extends Exception
{

    public function __construct(string $question, string $engine, string $parameter, ?Throwable $previous = null)
    {
        parent::__construct(sprintf("Question: %s Not found for engine: %s on parameter: %s", $question, $engine, $question), 0, $previous);
    }
}