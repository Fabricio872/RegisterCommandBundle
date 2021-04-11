<?php

namespace Fabricio872\RegisterCommand\Services\Questions;

class InputArray extends QuestionAbstract
{
    public function getAnswer()
    {
        $this->io->info('Individual array items divide with ","');
        return explode(',', $this->io->ask($this->question));
    }
}