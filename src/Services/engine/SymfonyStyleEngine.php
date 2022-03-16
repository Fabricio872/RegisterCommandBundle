<?php

namespace Fabricio872\RegisterCommand\Services\engine;

use Symfony\Component\Console\Style\SymfonyStyle;

class SymfonyStyleEngine implements EngineInterface
{
    private SymfonyStyle $symfonyStyle;

    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    /**
     * @return SymfonyStyle
     */
    public function getSymfonyStyle(): SymfonyStyle
    {
        return $this->symfonyStyle;
    }
}