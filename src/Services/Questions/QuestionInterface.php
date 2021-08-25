<?php

namespace Fabricio872\RegisterCommand\Services\Questions;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface QuestionInterface
{
    public function __construct(
        SymfonyStyle $io,
        string $question,
        UserPasswordEncoderInterface $passwordEncoder,
        UserInterface $user
    );

    /**
     * @return mixed
     */
    public function getAnswer();
}
