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
use function Composer\Autoload\includeFile;

class UserListCommand extends Command
{
    protected static $defaultName = 'user:list';
    protected static $defaultDescription = 'List all existing users';
    /**
     * @var string
     */
    private $userClassName;
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
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Limit rows on single page', 10)
            ->addOption('col-width', 'w', InputOption::VALUE_REQUIRED, 'Set maximum width for one column', 64);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        /** @var int $colWidth */
        $this->colWidth = $input->getOption('col-width');

        $userClass = new $this->userClassName();
        if (!$userClass instanceof UserInterface) {
            throw new \Exception("Provided user must implement " . UserInterface::class);
        }

        /** @var int $page */
        $page = $input->getArgument('page');
        /** @var int $limit */
        $limit = $input->getOption('limit');

        return $this->draw($page, $limit);
    }

    private function draw(int $page, int $limit)
    {
        $counetr = $this->em
            ->getRepository($this->userClassName)
            ->count([]);
        $userList = $this->em
            ->getRepository($this->userClassName)
            ->findBy([], [], $limit, $limit * ($page - 1));

        if ($counetr == 0) {
            $this->io->warning("Table is empty");
            return 0;
        }

        $objectToTable = new ObjectToTable(
            $userList,
            $this->io
        );

        $table = $objectToTable->makeTable();
        $table->setFooterTitle("Page $page / " . ceil($counetr / $limit));

        for ($i = 0; $i < count($objectToTable->getUserGetters(new $this->userClassName)); $i++) {
            $table->setColumnMaxWidth($i, $this->colWidth);
        }

        $table->render();
        $this->io->writeln('To exit type "q" and pres <return>');

        if (ceil($counetr / $limit) > 1) {
            $page = $this->getPage($page, ceil($counetr / $limit));

            if ($page === null){
                return 0;
            }

            $this->draw($page, $limit);
        }
        return 0;
    }

    private function getPage(int $currentPage, int $totalPages): ?int
    {
        $page = $this->io->ask("Page", ($currentPage < $totalPages) ? $currentPage + 1 : null);
        dump(strtolower($page));
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
            return $this->getPage($currentPage, $totalPages);
        }

        if ($page > $totalPages) {
            $this->io->warning("Page must be lower or equal to $totalPages");
            return $this->getPage($currentPage, $totalPages);
        }

        return $page;
    }
}
