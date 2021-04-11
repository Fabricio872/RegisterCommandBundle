<?php

namespace Fabricio872\RegisterCommand\Services\Questions;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

abstract class QuestionAbstract
{
    protected SymfonyStyle $io;
    protected string $question;
    protected UserPasswordEncoderInterface $passwordEncoder;
    protected $user;

    /**
     * QuestionAbstract constructor.
     * @param SymfonyStyle $io
     * @param string $question
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        SymfonyStyle $io,
        string $question,
        UserPasswordEncoderInterface $passwordEncoder,
        $user)
    {
        $this->io = $io;
        $this->question = $question;
        $this->passwordEncoder = $passwordEncoder;
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public abstract function getAnswer();
}