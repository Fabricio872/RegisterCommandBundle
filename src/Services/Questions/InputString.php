<?php

namespace Fabricio872\RegisterCommand\Services\Questions;

class InputString extends QuestionAbstract
{
    public function getAnswer()
    {
        return $this->io->ask($this->question);
    }
}
