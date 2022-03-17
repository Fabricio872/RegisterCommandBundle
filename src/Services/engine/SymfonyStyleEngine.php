<?php

namespace Fabricio872\RegisterCommand\Services\engine;

use Doctrine\ORM\EntityManagerInterface;
use Fabricio872\RegisterCommand\Services\Editor;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\User\UserInterface;

class SymfonyStyleEngine implements EngineInterface
{
    private SymfonyStyle $symfonyStyle;
    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em
    )
    {
        Editor::$ENGINE = $this;
        $this->em = $em;
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
        $editor = new Editor($user);
        $editor->run();

        $this->em->persist($editor->getEntity());
        $this->em->flush();
        $this->symfonyStyle->success(sprintf("User %s registered", $editor->getEntity()->getUserIdentifier()));
    }
}