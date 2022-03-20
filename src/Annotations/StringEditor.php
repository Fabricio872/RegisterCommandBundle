<?php

namespace Fabricio872\RegisterCommand\Annotations;

use Attribute;
use Fabricio872\RegisterCommand\Services\Questions\SymfonyStyleEngine\StringInput;

#[Attribute(Attribute::TARGET_PROPERTY)]
class StringEditor extends AbstractEditor
{
    public ?string $question;

    public ?string $default;

    public function __construct(?string $question = null, ?string $default = null)
    {
        parent::__construct($question);
        $this->default = $default;
    }

    public function askSymfonyStyleEngine(): string
    {
        return StringInput::class;
    }
}