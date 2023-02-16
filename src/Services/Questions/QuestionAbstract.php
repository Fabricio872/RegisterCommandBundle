<?php

declare(strict_types=1);

namespace Fabricio872\RegisterCommand\Services\Questions;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class QuestionAbstract implements QuestionInterface
{
    /** @var SymfonyStyle */
    protected $io;

    /** @var InputInterface */
    protected $input;

    /** @var OutputInterface */
    protected $output;

    /** @var string */
    protected $question;

    /** @var UserPasswordHasherInterface */
    protected $passwordEncoder;

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
        protected $user,
        protected $options
    ) {
        $this->io = $io;
        $this->question = $question;
        $this->input = $input;
        $this->output = $output;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function writeQuestion()
    {
        $this->io->writeln($this->question);
    }
}
