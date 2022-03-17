<?php

namespace Fabricio872\RegisterCommand\Command;

use Exception;
use Fabricio872\RegisterCommand\Services\engine\EngineInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'user:add',
    description: 'Add new user',
)]
class UserAddCommand extends Command
{
    private string $userClass;
    private string $defaultEngine;
    private array $engineConfigs;
    private EngineInterface $engine;

    public function __construct(
        string          $userClass,
        string          $defaultEngine,
        array           $engineConfigs,
        EngineInterface $engine,
    )
    {
        parent::__construct();
        $this->userClass = $userClass;
        $this->defaultEngine = $defaultEngine;
        $this->engineConfigs = $engineConfigs;
        $this->engine = $engine;
    }

    protected function configure(): void
    {
        $this
            ->addOption('engine', 'E', InputOption::VALUE_REQUIRED, "Define an engine for current instance")
        ;
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->engine->setIO($input, $output);
        $user = new $this->userClass();

        $this->engine->editMode($user);

        return Command::SUCCESS;
    }
}
