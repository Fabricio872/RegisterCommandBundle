<?php

declare(strict_types=1);

namespace Fabricio872\RegisterCommand\Services;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Fabricio872\RegisterCommand\Helpers\StreamableInput;
use ReflectionClass;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class UserEditor implements UserEditorInterface
{
    use StreamableInput;

    /** @var InputInterface */
    private $input;

    private array $cursor = [0, 0];

    private ?array $cursorEnd = null;

    private ?int $tableLines = null;

    /** @var bool|resource */
    private $stream;

    private string|bool|null $sttyMode = null;

    public function __construct(
        InputInterface $input,
        private readonly OutputInterface $output,
        private readonly EntityManagerInterface $em,
        private array $userList,
        /*** @var int */
        private readonly int $colWidth,
        private readonly Ask $ask
    ) {
        $this->input = $input;
    }

    /**
     * @throws ExceptionInterface
     */
    public function drawEdiTable(): void
    {
        $this->stream = $this->getInputStream();

        $this->startInteractiveMode();

        $this->table();

        while (! feof($this->stream) && ($char = fread($this->stream, 1)) !== "\n") {
            if (" " === $char) {
                $this->stopInteractiveMode();
                $this->updateValue();
                $this->startInteractiveMode();
                $this->table();
            } elseif ("\033" === $char) {
                $this->tryCellNavigation($char);
            }
        }

        $this->stopInteractiveMode();
    }

    private function startInteractiveMode(): void
    {
        $this->sttyMode = shell_exec('stty -g');

        // Disable icanon (so we can fread each keypress) and echo (we'll do echoing here instead)
        shell_exec('stty -icanon -echo');
    }

    private function stopInteractiveMode(): void
    {
        shell_exec(sprintf('stty %s', $this->sttyMode));
    }

    /**
     * @throws ExceptionInterface
     */
    private function table()
    {
        $bufferedOutput = new BufferedOutput();
        $io = new SymfonyStyle($this->input, $bufferedOutput);

        $userArray = [];
        $this->cursorEnd[0] = count($this->userList);
        foreach ($this->userList as $row => $user) {
            foreach (array_values(StaticMethods::userToArray($user)) as $col => $item) {
                $userArray[$row][array_keys(StaticMethods::userToArray($user))[$col]] = (($this->cursor === [$row, $col]) ? "> " : "  ") . $item;
            }
            $this->cursorEnd[1] = empty(StaticMethods::userToArray($user)) ? 0 : count(StaticMethods::userToArray($user));
        }
        if (! $userArray) {
            $this->output->writeln([
                "User Table is empty",
                "Press <RETURN> to continue",
            ]);
            return;
        }
        $arrayToTable = new ArrayToTable(
            $userArray,
            $io
        );

        $table = $arrayToTable->makeTable();

        for ($i = 0; $i < count($arrayToTable->getCols()); $i++) {
            $table->setColumnMaxWidth($i, $this->colWidth);
        }

        $table->render();
        $io->writeln("Use arrows to navigate");
        $io->writeln("<spacebar> to enter editing mode");
        $io->writeln("<delete> to remove user");
        $io->writeln("<enter> to exit editing mode");
        $this->renderFrame($bufferedOutput->fetch());
    }

    private function tryCellNavigation($char): void
    {
        // Did we read an escape sequence?
        $char .= fread($this->stream, 2);
        if (empty($char[2]) || ! in_array($char[2], ['A', 'B', 'C', 'D'], true)) {
            if (empty($char[2]) || $char[2] === "3") {
                $this->stopInteractiveMode();
                $this->deleteUser();
                $this->startInteractiveMode();
                $this->table();
            }

            // Input stream was not an arrow key.
            return;
        }

        switch ($char[2]) {
            case 'A': // go up!
                $this->up();
                break;
            case 'B': // go down!
                $this->down();
                break;
            case 'C': // go left!
                $this->left();
                break;
            case 'D': // go right!
                $this->right();
                break;
        }
    }

    private function up()
    {
        if ($this->cursor[0] > 0) {
            $this->cursor[0]--;
        }
        $this->table();
    }

    private function down()
    {
        if ($this->cursor[0] < $this->cursorEnd[0] - 1) {
            $this->cursor[0]++;
        }
        $this->table();
    }

    private function left()
    {
        if ($this->cursor[1] < $this->cursorEnd[1] - 1) {
            $this->cursor[1]++;
        }
        $this->table();
    }

    private function right()
    {
        if ($this->cursor[1] > 0) {
            $this->cursor[1]--;
        }
        $this->table();
    }

    private function renderFrame(string $frame)
    {
        if ($this->tableLines) {
            $this->output->write(sprintf("\033[%dA", $this->tableLines));
        }

        $this->tableLines = substr_count($frame, "\n");
        $this->output->write($frame);
    }

    private function updateValue(): void
    {
        $io = new SymfonyStyle($this->input, $this->output);
        $user = $this->userList[$this->cursor[0]];
        try {
            $annotation = StaticMethods::getRegisterCommand($this->ask->getUserClassName(), array_keys(StaticMethods::userToArray($user))[$this->cursor[1]]);
            $userReflection = new ReflectionClass($this->ask->getUserClassName());
            $property = $userReflection->getProperty(array_keys(StaticMethods::userToArray($user))[$this->cursor[1]]);
            if (
                $annotation &&
                $annotation->field
            ) {
                $property->setAccessible(true);
                $property->setValue($user, $this->ask->ask($property->getName()));

                $this->em->persist($user);
                $this->em->flush();
            } else {
                $io->warning("property " . $property->getName() . " cannot be modified");
                $io->success("press any key to continue");
                fread($this->stream, 1);
            }
        } catch (Exception $e) {
            $io->warning($e->getMessage());
            $io->success("press any key to continue");
            fread($this->stream, 1);
        }

        $this->tableLines = null;
    }

    private function deleteUser()
    {
        $identifier = null;
        $io = new SymfonyStyle($this->input, $this->output);
        $userReflection = new ReflectionClass($this->ask->getUserClassName());
        $user = $this->userList[$this->cursor[0]];

        foreach ($userReflection->getProperties() as $property) {
            $annotation = StaticMethods::getRegisterCommand($this->ask->getUserClassName(), $property->getName());
            if ($annotation && $annotation->userIdentifier === true) {
                $property->setAccessible(true);
                $identifier = $property->getValue($user);
            }
        }

        if (substr(strtolower((string) $io->ask("<info> Are you sure you want to delete user $identifier </info>: [Y/N]", 'n')), 1, 1) === 'y') {
            $this->em->remove($user);
            $this->em->flush();
            unset($this->userList[$this->cursor[0]]);
            $this->userList = array_values($this->userList);

            $io->success("User $identifier deleted");
        }

        $this->tableLines = null;
    }
}
