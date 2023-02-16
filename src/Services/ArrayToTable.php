<?php

declare(strict_types=1);

namespace Fabricio872\RegisterCommand\Services;

use Exception;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class ArrayToTable
{
    public function __construct(
        private readonly array $array,
        private readonly OutputInterface $output
    ) {
    }

    public function makeTable(): Table
    {
        $table = new Table($this->output);

        if (! $this->array) {
            throw new Exception("User Table is empty");
        }
        $table->setHeaders(array_keys($this->array[0]));

        $table->setRows(array_map(fn ($user) => StaticMethods::getSerializer()->normalize($user), $this->array));
        $table->setStyle('box');

        return $table;
    }

    public function getCols(): array
    {
        return array_keys($this->array[0]);
    }
}
