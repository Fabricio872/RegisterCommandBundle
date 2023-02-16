<?php

declare(strict_types=1);

namespace Fabricio872\RegisterCommand\Services\Questions;

class YesNoInput extends QuestionAbstract
{
    public function getAnswer(): bool
    {
        $this->io->writeln("<info> $this->question</info>:");
        $answer = str_starts_with(strtolower((string) $this->io->ask("<info> $this->question</info>: [Y/N]", 'n')), 'y');
        $this->io->writeln("Your answer is " . ($answer ? 'Yes' : 'No'));
        return $answer;
    }
}
