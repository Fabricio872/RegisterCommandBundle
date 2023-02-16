<?php

declare(strict_types=1);

namespace Fabricio872\RegisterCommand\Services;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface UserEditorInterface
{
    public function __construct(
        InputInterface $input,
        OutputInterface $output,
        EntityManagerInterface $em,
        array $userList,
        int $colWidth,
        Ask $ask
    );

    public function drawEdiTable(): void;
}
