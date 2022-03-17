<?php

namespace Fabricio872\RegisterCommand\Annotations;

abstract class AbstractEditor
{
    public ?string $question;

    public function __construct(?string $question = null)
    {
        $this->question = $question;
    }
}