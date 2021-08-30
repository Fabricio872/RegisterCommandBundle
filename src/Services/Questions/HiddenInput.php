<?php

namespace Fabricio872\RegisterCommand\Services\Questions;

use Symfony\Component\Console\Style\SymfonyStyle;

class HiddenInput extends QuestionAbstract
{
    public function getAnswer(): ?string
    {
        return $this->io->askHidden($this->question);
    }
}
