<?php

namespace Fabricio872\RegisterCommand\Services;

use Doctrine\Common\Annotations\Reader;
use Fabricio872\RegisterCommand\Annotations\RegisterCommand;
use Fabricio872\RegisterCommand\Serializer\UserEntityNormalizer;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DataUriNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
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
    /**
     * @var int
     */
    private $maxRows;

    public function __construct(
        iterable $users,
        OutputInterface $output,
        int $maxRows
    )
    {
        $this->users = $users;
        $this->output = $output;
        $this->maxRows = $maxRows;
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
            array_merge(
                array_map(function ($user) {
                    return $this->makeCols($user);
                }, $this->users),
                $this->emptyRows()
            )
        );
        $table->setStyle('box');
//        $table->;

        return $table;
    }

    private function makeCols($user)
    {
        return $this->getSerializer()->normalize($user);
    }

    /**
     * @return array
     */
    private function emptyRows(): array
    {
        $rows = [];
        for ($i = count($this->users); $i < $this->maxRows; $i++) {
            $rows[] = [''];
        }
        return $rows;
    }

    /**
     * @return array
     */
    private function getUserGetters(UserInterface $user): array
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