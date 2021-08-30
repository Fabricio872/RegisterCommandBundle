<?php

namespace Fabricio872\RegisterCommand\Services\Questions;

class HiddenRepeatInput extends QuestionAbstract
{
    public function getAnswer(): ?string
    {
        return $this->validated();
    }

    private function validated(): ?string
    {
        $q0 = $this->io->askHidden($this->question);
        $q1 = $this->io->askHidden($this->question . ' again');
        if ($q0 != $q1) {
            $this->io->warning("Fields doesn't match");
            $this->validated();
        }
        return $q0;
    }
}
