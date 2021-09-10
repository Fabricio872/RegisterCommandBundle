<?php

namespace Fabricio872\RegisterCommand\Services;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

interface UserEditorInterface
{
    public function __construct(
        InputInterface         $input,
        OutputInterface        $output,
        EntityManagerInterface $em,
        array                  $userList,
        NormalizerInterface    $normalizer,
        int                    $colWidth
    );

    public function drawEdiTable(): void;
}
