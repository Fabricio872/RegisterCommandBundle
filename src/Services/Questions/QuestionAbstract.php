<?php

namespace Fabricio872\RegisterCommand\Services\Questions;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
    /** @var UserPasswordEncoderInterface $passwordEncoder */
    protected $passwordEncoder;
    protected $user;
    protected $options;

    /**
     * QuestionAbstract constructor.
     * @param SymfonyStyle $io
     * @param string $question
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        SymfonyStyle $io,
        InputInterface $input,
        OutputInterface $output,
        string $question,
        UserPasswordEncoderInterface $passwordEncoder,
        $user,
        $options
    ) {
        $this->io = $io;
        $this->question = $question;
        $this->passwordEncoder = $passwordEncoder;
        $this->user = $user;
        $this->options = $options;
        $this->input = $input;
        $this->output = $output;
    }

    protected function writeQuestion(){
        $this->io->writeln($this->question);
    }
}
