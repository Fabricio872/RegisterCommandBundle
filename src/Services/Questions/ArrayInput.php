<?php

declare(strict_types=1);

namespace Fabricio872\RegisterCommand\Services\Questions;

class ArrayInput extends QuestionAbstract
{
    private array $values = [];

    public function getAnswer(): array
    {
        $this->io->writeln("<info> $this->question</info>:");
        $this->ask();
        return $this->values;
    }

    private function ask()
    {
        $value = $this->io->ask('value ' . count($this->values) . ' (press <return> to stop adding fields)');
        if ($value !== null) {
            $this->values[] = $value;
            $this->ask();
        }
    }
}
