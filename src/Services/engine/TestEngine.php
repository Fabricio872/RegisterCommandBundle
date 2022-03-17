<?php

namespace Fabricio872\RegisterCommand\Services\engine;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TestEngine implements EngineInterface
{
    function setIO(InputInterface $input, OutputInterface $output)
    {
        // TODO: Implement setIO() method.
    }

    function listMode(int $start, int $limit): void
    {
        // TODO: Implement listMode() method.
    }

    function editMode(UserInterface $user): void
    {
        // TODO: Implement edit() method.
    }
}