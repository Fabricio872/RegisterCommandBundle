<?php

namespace Fabricio872\RegisterCommand\Command;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractList extends Command
{
    /** @var string */
    protected $userClassName;
    /** @var int */
    private $tableLimit;
    /** @var int */
    private $maxColWidth;
    /** @var InputInterface */
    protected $input;
    /** @var OutputInterface */
    protected $output;
    /** @var SymfonyStyle */
    protected $io;
    /** @var EntityManagerInterface */
    protected $em;
    /** @var int */
    protected $colWidth;
    /** @var int */
    protected $totalUsers;
    /** @var int */
    protected $limitUsers;
    /** @var int */
    protected $currentPage = 0;
    /** @var NormalizerInterface */
    protected $normalizer;
    /** @var UserPasswordEncoderInterface */
    protected $passwordEncoder;
    /** @var Reader */
    protected $reader;
    /** @var ValidatorInterface */
    protected $validator;

    /**
     * @param string $userClassName
     * @param int $tableLimit
     * @param int $maxColWidth
     * @param EntityManagerInterface $em
     */
    public function __construct(
        string                       $userClassName,
        int                          $tableLimit,
        int                          $maxColWidth,
        EntityManagerInterface       $em,
        NormalizerInterface          $normalizer,
        UserPasswordEncoderInterface $passwordEncoder,
        Reader                       $reader,
        ValidatorInterface           $validator
    ) {
        $this->userClassName = $userClassName;
        $this->tableLimit = $tableLimit;
        $this->maxColWidth = $maxColWidth;
        $this->em = $em;
        $this->normalizer = $normalizer;
        $this->passwordEncoder = $passwordEncoder;
        $this->reader = $reader;
        $this->validator = $validator;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('page', InputArgument::OPTIONAL, 'Page', 1)
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Limit rows on single page', $this->tableLimit)
            ->addOption('col-width', 'w', InputOption::VALUE_REQUIRED, 'Set maximum width for one column', $this->maxColWidth);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;
        $this->io = new SymfonyStyle($this->input, $this->output);
        /** @var int $colWidth */
        $this->colWidth = $this->input->getOption('col-width');

        $this->totalUsers = $this->em
            ->getRepository($this->userClassName)
            ->count([]);

        $this->limitUsers = $this->input->getOption('limit');

        if ($this->totalUsers == 0) {
            $this->io->warning("User Table is empty");
            return 0;
        }

        $userClass = new $this->userClassName();
        if (!$userClass instanceof UserInterface) {
            throw new \Exception("Provided user must implement " . UserInterface::class);
        }

        return $this->draw(null);
    }

    /**
     * @return string
     */
    protected function askPage(): string
    {
        return $this->io->ask('page', ($this->currentPage < $this->getTotalPages()) ? $this->currentPage + 1 : 'q');
    }

    /**
     * @return false|float
     */
    protected function getTotalPages()
    {
        return ceil($this->totalUsers / $this->limitUsers);
    }

    /**
     * @param int $page
     * @return int
     */
    abstract protected function draw(?int $page): int;
}
