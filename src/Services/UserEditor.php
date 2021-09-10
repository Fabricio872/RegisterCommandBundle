<?php

namespace Fabricio872\RegisterCommand\Services;

use Doctrine\ORM\EntityManagerInterface;
use Fabricio872\RegisterCommand\Helpers\StreamableInput;
use Fabricio872\RegisterCommand\Serializer\UserEntityNormalizer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;

class UserEditor implements UserEditorInterface
{
    use StreamableInput;

    /** @var InputInterface */
    private $input;
    /** @var OutputInterface */
    private $output;
    /** @var EntityManagerInterface */
    private $em;
    /** @var array */
    private $userList;
    /** @var NormalizerInterface */
    private $normalizer;
    /*** @var int */
    private $colWidth;
    /** @var array */
    private $cursor = [0, 0];
    /** @var array */
    private $cursorEnd;
    /** @var int */
    private $tableLines;

    public function __construct(
        InputInterface         $input,
        OutputInterface        $output,
        EntityManagerInterface $em,
        array                  $userList,
        NormalizerInterface    $normalizer,
        int                    $colWidth
    )
    {
        $this->input = $input;
        $this->output = $output;
        $this->em = $em;
        $this->userList = $userList;
        $this->normalizer = $normalizer;
        $this->colWidth = $colWidth;
    }

    public function drawEdiTable(): void
    {
        $this->stream = $this->getInputStream();

        $this->sttyMode = shell_exec('stty -g');

        // Disable icanon (so we can fread each keypress) and echo (we'll do echoing here instead)
        shell_exec('stty -icanon -echo');

        $this->table();

        while (!feof($this->stream) && ($char = fread($this->stream, 1)) != "\n") {
            if (" " === $char) {
//                if (in_array($this->options[$this->cursor], $this->activeList)) {
//                    unset($this->activeList[array_search($this->options[$this->cursor], $this->activeList)]);
//                } else {
//                    $this->activeList[] = $this->options[$this->cursor];
//                }
                $this->table();
            } elseif ("\033" === $char) {
                $this->tryCellNavigation($char);
            }
        }

        shell_exec(sprintf('stty %s', $this->sttyMode));
    }

    private function table()
    {
        $bufferedOutput = new BufferedOutput();
        $io = new SymfonyStyle($this->input, $bufferedOutput);

        if ($this->tableLines) {
            $io->write(sprintf("\033[%dA", $this->tableLines));
        }

        $userArray = [];
        $this->cursorEnd[0] = count($this->userList);
        foreach ($this->userList as $row => $user) {
            foreach (iterator_to_array($this->getSerializer()->normalize($this->normalizer->normalize($user))) as $col => $item) {
                $userArray[$row][array_keys($this->normalizer->normalize($user))[$col]] = (($this->cursor == [$row, $col]) ? "> " : "  ") . $item;
            }
            $this->cursorEnd[1] = count($this->normalizer->normalize($user));
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
        $io->writeln("<enter> to exit editing mode");
        $tableRendered = $bufferedOutput->fetch();
        $this->tableLines = substr_count($tableRendered, "\n");
        $this->output->write($tableRendered);
    }

    private function tryCellNavigation($char): void
    {
        // Did we read an escape sequence?
        $char .= fread($this->stream, 2);
        if (empty($char[2]) || !in_array($char[2], ['A', 'B', 'C', 'D'])) {
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

    /**
     * @return Serializer
     */
    private function getSerializer(): Serializer
    {
        $normalizers = [new UserEntityNormalizer()];

        return new Serializer($normalizers);
    }
}
