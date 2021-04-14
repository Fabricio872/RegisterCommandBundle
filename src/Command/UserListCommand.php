<?php

namespace Fabricio872\RegisterCommand\Command;

use Doctrine\ORM\EntityManagerInterface;
use Fabricio872\RegisterCommand\Services\ObjectToTable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\User\UserInterface;

class UserListCommand extends Command
{
    protected static $defaultName = 'user:list';
    protected static $defaultDescription = 'List all existing users';
    /**
     * @var string
     */
    private $userClassName;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(
        string $userClassName,
        EntityManagerInterface $em
    )
    {
        parent::__construct();
        $this->userClassName = $userClassName;
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('page', InputArgument::OPTIONAL, 'Page', 1)
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Limit rows on single page', 10);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $userClass = new $this->userClassName();
        if (!$userClass instanceof UserInterface) {
            throw new \Exception("Provided user must implement " . UserInterface::class);
        }

        /** @var int $page */
        $page = $input->getArgument('page');
        /** @var int $limit */
        $limit = $input->getOption('limit');

        $counetr = $this->em
            ->getRepository($this->userClassName)
            ->count([]);
        $userList = $this->em
            ->getRepository($this->userClassName)
            ->findBy([], [], $limit, $limit * ($page - 1));

        $table = new ObjectToTable(
            $userList,
            $io
        );

        $table->getTable();

        $io->success("$page $limit");

        return 0;
    }
}
