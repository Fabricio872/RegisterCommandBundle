<?php

namespace Fabricio872\RegisterCommand\Services\Questions;

class YesNoInput extends QuestionAbstract
{
    public function getAnswer(): bool
    {
        $this->io->writeln("<info> $this->question</info>:");
        $answer = substr(strtolower($this->io->ask("<info> $this->question</info>: [Y/N]", 'n')), 0, 1) == 'y';
        $this->io->writeln("Your answer is " . ($answer ? 'Yes' : 'No'));
        return $answer;
    }
}
