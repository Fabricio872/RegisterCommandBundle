<?php

namespace Fabricio872\RegisterCommand\Services\Questions;

class InputDate extends QuestionAbstract
{
    public function getAnswer()
    {
        $this->io->writeln($this->question);
        $day = $this->ask('Day (1-31)', [1, 31]);
        $month = $this->ask('Month (1-12)', [1, 12]);
        $year = $this->ask('Year (1900-9999)', [1900, 9999]);

        return new \DateTime("$year-$month-$day");
    }

    private function ask(string $question, array $range)
    {
        $value = $this->io->ask($question);
        if (!is_numeric($value)) {
            $this->io->warning("Value: " . $value . " is not numeric");
            $value = $this->ask($question, $range);
        } elseif ($value < $range[0] || $value > $range[1]) {
            $this->io->warning("Value: " . $value . " is not within range (" . $range[0] . "-" . $range[1] . ")");
            $value = $this->ask($question, $range);
        }
        return $value;
    }
}
