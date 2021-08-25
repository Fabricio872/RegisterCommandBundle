<?php

namespace Fabricio872\RegisterCommand\Services\Questions;

use Symfony\Component\Console\Style\SymfonyStyle;

class InputHidden extends QuestionAbstract
{
    public function getAnswer()
    {
        return $this->io->askHidden($this->question);
    }
}
