<?php

declare(strict_types=1);

namespace Fabricio872\RegisterCommand\Command;

use Fabricio872\RegisterCommand\Services\ArrayToTable;
use Fabricio872\RegisterCommand\Services\Ask;
use Fabricio872\RegisterCommand\Services\StaticMethods;
use Fabricio872\RegisterCommand\Services\UserEditor;
use Fabricio872\RegisterCommand\Services\UserEditorInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'user:list',
    description: 'List all existing users',
)]
class UserListCommand extends AbstractList
{
    /** @var array|object[] */
    private ?array $userList = null;

    protected function configure(): void
    {
        parent::configure();
    }

    /**
     * @param ?int $page
     * @return int
     */
    protected function draw(?int $page): int
    {
        if ($page === null) {
            /** @var int $page */
            $page = $this->getPage($this->input->getArgument('page'));
        }
        $this->userList = $this->em
            ->getRepository($this->userClassName)
            ->findBy([], [], $this->limitUsers, $this->limitUsers * ($page - 1));

        $userArray = [];
        foreach ($this->userList as $user) {
            $userArray[] = StaticMethods::getSerializer()->normalize($user);
        }
        $objectToTable = new ArrayToTable(
            $userArray,
            $this->io
        );

        $table = $objectToTable->makeTable();
        $table->setFooterTitle("Page $page / " . $this->getTotalPages());

        for ($i = 0; $i < count($objectToTable->getCols()); $i++) {
            $table->setColumnMaxWidth($i, $this->colWidth);
        }

        $table->render();

        $this->io->writeln('To exit type "q" and press <return>');
        $this->io->writeln('To switch to editing mode type "e" and press <return>');
        $page = $this->getPage($this->askPage());

        if ($page === null) {
            return 0;
        }

        $this->draw($page);
        return 0;
    }

    /**
     * @param $page
     */
    protected function getPage($page): ?int
    {
        if (strtolower((string) $page) === 'q') {
            $this->io->writeln('Bye (=◉ᆽ◉=)');
            return null;
        }
        if (strtolower((string) $page) === 'e') {
            /** @var UserEditorInterface $userEditor */
            $userEditor = new UserEditor(
                $this->input,
                $this->output,
                $this->em,
                $this->userList,
                $this->colWidth,
                $this->buildAsk()
            );
            $userEditor->drawEdiTable();
            return $this->getPage($this->askPage());
        }
        if (! is_numeric($page)) {
            $this->io->writeln('Bad input');
            return $this->getPage($this->askPage());
        }
        if ($page < 1) {
            $this->io->warning("Pages must be higher or equal to 1");
            return $this->getPage($this->askPage());
        }

        if ($page > $this->getTotalPages()) {
            $this->io->warning("Page must be lower or equal to " . $this->getTotalPages());
            return $this->getPage($this->askPage());
        }

        $this->currentPage = $page;
        return $page;
    }

    private function buildAsk(): Ask
    {
        return new Ask(
            $this->userClassName,
            $this->reader,
            $this->io,
            $this->input,
            $this->output,
            $this->passwordEncoder,
            $this->validator
        );
    }
}
