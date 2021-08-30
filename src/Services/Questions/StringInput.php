<?php

namespace Fabricio872\RegisterCommand\Services\Questions;

class StringInput extends QuestionAbstract
{
    public function getAnswer(): ?string
    {
        return $this->io->ask($this->question);
    }
}
