<?php

namespace Fabricio872\RegisterCommand\Services\Questions;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface QuestionInterface
{
    public function __construct(
        SymfonyStyle $io,
        InputInterface $input,
        OutputInterface $output,
        string $question,
        UserPasswordHasherInterface $passwordEncoder,
        UserInterface $user,
        $options
    );

    /**
     * @return mixed
     */
    public function getAnswer();
}
