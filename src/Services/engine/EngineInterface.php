<?php

namespace Fabricio872\RegisterCommand\Services\engine;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface EngineInterface
{
    function setIO(InputInterface $input, OutputInterface $output);

    /**
     * Displays list of existing users in some kind of table
     *
     * @param int $start
     * @param int $limit
     * @return void
     */
    function listMode(int $start, int $limit): void;

    /**
     * This method creates or update user
     *
     * @param UserInterface $user
     * @return void
     */
    function editMode(UserInterface $user): void;
}