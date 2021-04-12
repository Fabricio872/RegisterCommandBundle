<?php

namespace Fabricio872\RegisterCommand\Services\Questions;

class InputArray extends QuestionAbstract
{
    private array $values = [];

    public function getAnswer()
    {
        $this->io->writeln($this->question);
        $this->ask();
        return $this->values;
    }

    private function ask()
    {
        $value = $this->io->ask('value ' . count($this->values) . ' (press <return> to stop adding fields)');
        if ($value != null) {
            $this->values[] = $value;
            $this->ask();
        }
    }
}