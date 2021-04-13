<?php

namespace Fabricio872\RegisterCommand\Services\Questions;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

abstract class QuestionAbstract implements QuestionInterface
{
    /** @var SymfonyStyle $io */
    protected $io;
    /** @var string $question */
    protected $question;
    /** @var UserPasswordEncoderInterface $passwordEncoder */
    protected $passwordEncoder;
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
}