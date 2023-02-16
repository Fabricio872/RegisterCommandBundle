<?php

declare(strict_types=1);

namespace Fabricio872\RegisterCommand\Services\Questions;

class HiddenInput extends QuestionAbstract
{
    public function getAnswer(): ?string
    {
        return $this->io->askHidden($this->question);
    }
}
