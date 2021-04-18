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
     * @var int
     */
    private $tableLimit;
    /**
     * @var int
     */
    private $maxColWidth;
    /**
     * @var SymfonyStyle
     */
    private $io;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var int
     */
    private $colWidth;
    /**
     * @var int
     */
    private $totalPages;
    /**
     * @var int
     */
    private $currentPage = 0;

    public function __construct(
        string $userClassName,
        int $tableLimit,
        int $maxColWidth,
        EntityManagerInterface $em
    )
    {
        $this->userClassName = $userClassName;
        $this->tableLimit = $tableLimit;
        $this->maxColWidth = $maxColWidth;
        $this->em = $em;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('page', InputArgument::OPTIONAL, 'Page', 1)
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Limit rows on single page', $this->tableLimit)
            ->addOption('col-width', 'w', InputOption::VALUE_REQUIRED, 'Set maximum width for one column', $this->maxColWidth);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        /** @var int $colWidth */
        $this->colWidth = $input->getOption('col-width');

        $this->totalPages = $this->em
            ->getRepository($this->userClassName)
            ->count([]);

        $userClass = new $this->userClassName();
        if (!$userClass instanceof UserInterface) {
            throw new \Exception("Provided user must implement " . UserInterface::class);
        }

        /** @var int $page */
        $page = $this->getPage($input->getArgument('page'));
        if ($page === null) {
            return 0;
        }
        /** @var int $limit */
        $limit = $input->getOption('limit');

        return $this->draw($page, $limit);
    }

    private function draw(int $page, int $limit)
    {
        $userList = $this->em
            ->getRepository($this->userClassName)
            ->findBy([], [], $limit, $limit * ($page - 1));

        if ($this->totalPages == 0) {
            $this->io->warning("Table is empty");
            return 0;
        }

        $objectToTable = new ObjectToTable(
            $userList,
            $this->io
        );

        $table = $objectToTable->makeTable();
        $table->setFooterTitle("Page $page / " . ceil($this->totalPages / $limit));

        for ($i = 0; $i < count($objectToTable->getUserGetters(new $this->userClassName)); $i++) {
            $table->setColumnMaxWidth($i, $this->colWidth);
        }

        $table->render();

        if (ceil($this->totalPages / $limit) > 1) {
            $this->io->writeln('To exit type "q" and pres <return>');
            $page = $this->getPage($this->askPage());

            if ($page === null) {
                return 0;
            }

            $this->draw($page, $limit);
        }
        return 0;
    }

    private function getPage($page): ?int
    {
        if (strtolower($page) == 'q') {
            $this->io->writeln('Bye');
            return null;
        }
        if (!is_numeric($page)) {
            $this->io->writeln('Unknown input');
            return null;
        }
        if ($page < 1) {
            $this->io->warning("Page must be higher or equal to 1");
            return $this->getPage($this->askPage());
        }

        if ($page > $this->totalPages) {
            $this->io->warning("Page must be lower or equal to $this->totalPages");
            return $this->getPage($this->askPage());
        }

        $this->currentPage = $page;
        return $page;
    }

    private function askPage(): string
    {
        return $this->io->ask('page', ($this->currentPage < $this->totalPages) ? $this->currentPage + 1 : 'q');
    }
}
