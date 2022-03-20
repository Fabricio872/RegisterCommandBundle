<?php

namespace Fabricio872\RegisterCommand\Services\Engines;

use Doctrine\ORM\EntityManagerInterface;
use Fabricio872\RegisterCommand\Services\Editor;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\User\UserInterface;

class SymfonyStyleEngine implements EngineInterface
{
    private SymfonyStyle $symfonyStyle;
    private Editor $editor;
    private EntityManagerInterface $em;

    public function __construct(
        Editor $editor,
        EntityManagerInterface $em
    )
    {
        $this->editor = $editor;
        $this->em = $em;
        Editor::$ENGINE = $this;
    }

    function setIO(InputInterface $input, OutputInterface $output)
    {
        $this->symfonyStyle = new SymfonyStyle($input, $output);
    }

    /**
     * @return SymfonyStyle
     */
    public function getSymfonyStyle(): SymfonyStyle
    {
        return $this->symfonyStyle;
    }

    function listMode(int $start, int $limit): void
    {
        // TODO: Implement listMode() method.
    }

    function editMode(UserInterface $user): void
    {
        $this->editor->setUser($user);
        $this->editor->run();

        $this->em->persist($this->editor->getUser());
        $this->em->flush();
        $this->symfonyStyle->success(sprintf("User %s registered", $this->editor->getUser()->getUserIdentifier()));
    }
}