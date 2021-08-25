<?php

namespace Fabricio872\RegisterCommand\Services\Questions;

class InputYesNo extends QuestionAbstract
{
    public function getAnswer()
    {
        $answer = substr(strtolower($this->io->ask($this->question . ' [Y/N]', 'n')), 0, 1) == 'y';
        $this->io->writeln("Your answer is " . ($answer ? 'Yes' : 'No'));
        return $answer;
    }
}
