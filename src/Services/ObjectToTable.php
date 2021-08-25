<?php

namespace Fabricio872\RegisterCommand\Services;

use Fabricio872\RegisterCommand\Serializer\UserEntityNormalizer;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Serializer;

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
    ) {
        $this->users = $users;
        $this->output = $output;
    }

    public function makeTable(): Table
    {
        $table = new Table($this->output);
        $table->setHeaders(
            array_map(function ($getter) {
                return substr($getter, 3);
            }, $this->getUserGetters($this->users[0]))
        );
        $table->setRows(
            array_map(function ($user) {
                return $this->makeCols($user);
            }, $this->users)
        );
        $table->setStyle('box');

        return $table;
    }

    private function makeCols($user)
    {
        return $this->getSerializer()->normalize($user);
    }

    /**
     * @return array
     */
    public function getUserGetters(UserInterface $user): array
    {
        return array_filter(get_class_methods($user), function ($var) {
            return substr($var, 0, 3) == 'get';
        });
    }

    /**
     * @return Serializer
     */
    private function getSerializer(): Serializer
    {
        $normalizers = [new UserEntityNormalizer($this->getUserGetters($this->users[0]))];

        return new Serializer($normalizers);
    }
}
