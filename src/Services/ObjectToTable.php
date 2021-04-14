<?php

namespace Fabricio872\RegisterCommand\Services;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ObjectToTable
{
    /**
     * @var array|UserInterface
     */
    private $users;
    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(
        iterable $users,
        OutputInterface $output
    )
    {
        $this->users = $users;
        $this->output = $output;
    }

    public function getTable()
    {
        $this->makeTable();

    }

    private function makeTable()
    {
        $table = new Table($this->output);

        $table->setHeaders(
            $this->getUserGetters($this->users[0], true)
        );

        $table->setRows(array_map('self::makeCols', $this->users));

        $table->render();
    }

    private static function makeCols($user)
    {
        $userArray = [];
        foreach (self::getUserGetters($user) as $getter) {
            $userArray[] = self::serializeCol($user->$getter());
        }
        return $userArray;
    }

    /**
     * @return array
     */
    private static function getUserGetters(UserInterface $user, bool $unGetter = false): array
    {
        $methods = [];
        foreach (get_class_methods($user) as $method) {
            if (substr($method, 0, 3) == 'get') {
                if ($unGetter) {
                    $methods[] = substr($method, 3);
                } else {
                    $methods[] = $method;
                }
            }
        }
        return $methods;
    }

    private static function serializeCol($col): string
    {
        if (is_numeric($col)) {
            return $col;
        }
        if (is_null($col)) {
            return 'NULL';
        }
        if (is_array($col)) {
            return implode(', ', $col);
        }
        if (is_string($col)) {
            return $col;
        }
        if ($col instanceof \DateTime) {
            return $col->format('c');
        }

        return 'Unknown datatype';
    }
}