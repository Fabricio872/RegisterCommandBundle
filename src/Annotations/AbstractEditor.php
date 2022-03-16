<?php

namespace Fabricio872\RegisterCommand\Annotations;

abstract class AbstractEditor
{
    public ?string $question;

    public bool $userIdentifier;

    public function __construct(?string $question = null, ?bool $userIdentifier = false)
    {
        $this->question = $question;
        $this->userIdentifier = $userIdentifier;
    }
}