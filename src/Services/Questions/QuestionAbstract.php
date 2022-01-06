<?php

namespace Fabricio872\RegisterCommand\Services\Questions;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class QuestionAbstract implements QuestionInterface
{
    /** @var SymfonyStyle $io */
    protected $io;
    /** @var InputInterface */
    protected $input;
    /** @var OutputInterface */
    protected $output;
    /** @var string $question */
    protected $question;
    /** @var UserPasswordHasherInterface $passwordEncoder */
    protected $passwordEncoder;
    protected $user;
    protected $options;

    /**
     * QuestionAbstract constructor.
     * @param SymfonyStyle $io
     * @param string $question
     * @param UserPasswordHasherInterface $passwordEncoder
     */
    public function __construct(
        SymfonyStyle $io,
        InputInterface $input,
        OutputInterface $output,
        string $question,
        UserPasswordHasherInterface $passwordEncoder,
        $user,
        $options
    ) {
        $this->io = $io;
        $this->question = $question;
        $this->input = $input;
        $this->output = $output;
        $this->passwordEncoder = $passwordEncoder;
        $this->user = $user;
        $this->options = $options;
    }

    protected function writeQuestion()
    {
        $this->io->writeln($this->question);
    }
}
