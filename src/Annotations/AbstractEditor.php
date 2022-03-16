<?php

namespace Fabricio872\RegisterCommand\Annotations;

abstract class AbstractEditor
{
    public ?string $question;

    public bool $userIdentifier;

    /*
     * This will be called if tty is enabled
     */
    abstract public function askTTY();

    /*
     * This will be called if tty is disabled
     */
    abstract public function askNoTTY();

    public function __construct(?string $question = null, ?bool $userIdentifier = false)
    {
        $this->question = $question;
        $this->userIdentifier = $userIdentifier;
    }
}