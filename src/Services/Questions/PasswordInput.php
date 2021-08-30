<?php

namespace Fabricio872\RegisterCommand\Services\Questions;

class PasswordInput extends QuestionAbstract
{
    public function getAnswer(): ?string
    {
        return is_null($password = $this->validated()) ? null : $this->passwordEncoder->encodePassword($this->user, $password);
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
