<?php

namespace Fabricio872\RegisterCommand\Services\Questions;

class InputDateTime extends QuestionAbstract
{
    private $values = [];

    public function getAnswer()
    {
        $this->io->writeln($this->question);
        $day = (int)$this->io->ask('Day (1-31)');
        $month = (int)$this->io->ask('Month (1-12)');
        $year = (int)$this->io->ask('Year (1900 - 2100)');
        $hour = (int)$this->io->ask('Hour (0 - 23)');
        $minute = (int)$this->io->ask('Minute (0 - 59)');
        $second = (int)$this->io->ask('Second (0.0 - 59.9)');

        return new \DateTime("$year-$month-$day" . "T$hour:$minute:$second" . "Z");
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