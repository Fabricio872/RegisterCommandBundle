<?php

declare(strict_types=1);

namespace Fabricio872\RegisterCommand\Services\Questions;

use Exception;
use Fabricio872\RegisterCommand\Helpers\StreamableInput;

class ListInput extends QuestionAbstract
{
    use StreamableInput;

    private string|bool|null $sttyMode = null;

    private $stream;

    private array $activeList = [];

    private int $cursor = 0;

    public function getAnswer()
    {
        if (! is_array($this->options)) {
            throw new Exception('Please provide "options" option in User entity annotation with values in array example: {"ROLE_USER", "ROLE_ADMIN"}');
        }

        $this->io->writeln("<info> $this->question</info>:");
        $this->io->section("Navigate with arrows 'UP' and 'DOWN' mark item with 'SPACE' exit by pressing 'RETURN'");
        $this->io->writeln("\n");
        return $this->ask();
    }

    private function ask()
    {
        $this->stream = $this->getInputStream();

        $this->sttyMode = shell_exec('stty -g');

        // Disable icanon (so we can fread each keypress) and echo (we'll do echoing here instead)
        shell_exec('stty -icanon -echo');

        $this->table();

        while (! feof($this->stream) && ($char = fread($this->stream, 1)) !== "\n") {
            if (" " === $char) {
                if (in_array($this->options[$this->cursor], $this->activeList, true)) {
                    unset($this->activeList[array_search($this->options[$this->cursor], $this->activeList, true)]);
                } else {
                    $this->activeList[] = $this->options[$this->cursor];
                }
                $this->table();
            } elseif ("\033" === $char) {
                $this->tryCellNavigation($char);
            }
        }

        shell_exec(sprintf('stty %s', $this->sttyMode));

        return array_values(array_unique($this->activeList));
    }

    private function tryCellNavigation($char): void
    {
        // Did we read an escape sequence?
        $char .= fread($this->stream, 2);
        if (empty($char[2]) || ! in_array($char[2], ['A', 'B'], true)) {
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
        }
    }

    private function up()
    {
        if ($this->cursor > 0) {
            $this->cursor--;
        }
        $this->table();
    }

    private function down()
    {
        if ($this->cursor < (is_countable($this->options) ? count($this->options) : 0) - 1) {
            $this->cursor++;
        }
        $this->table();
    }

    private function table()
    {
        $this->io->write(sprintf("\033[%dA", is_countable($this->options) ? count($this->options) : 0));
        foreach ($this->options as $key => $item) {
            $this->io->writeln(($key === $this->cursor ? ">" : " ") . " [ " . (in_array($item, $this->activeList, true) ? "X" : " ") . " ] " . $item);
        }
    }
}
