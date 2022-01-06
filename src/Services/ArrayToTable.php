<?php

namespace Fabricio872\RegisterCommand\Services;

use Fabricio872\RegisterCommand\Serializer\UserEntityNormalizer;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Serializer;

class ArrayToTable
{
    /** @var array */
    private $array;
    /** @var OutputInterface */
    private $output;

    public function __construct(
        array           $array,
        OutputInterface $output
    ) {
        $this->array = $array;
        $this->output = $output;
    }

    public function makeTable(): Table
    {
        $table = new Table($this->output);

        if (!$this->array) {
            throw new \Exception("User Table is empty");
        }
        $table->setHeaders(array_keys($this->array[0]));

        $table->setRows(array_map(function ($user) {
            return StaticMethods::getSerializer()->normalize($user);
        }, $this->array));
        $table->setStyle('box');

        return $table;
    }

    public function getCols(): array
    {
        return array_keys($this->array[0]);
    }
}
