<?php

namespace Fabricio872\RegisterCommand\Command;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManagerInterface;
use Fabricio872\RegisterCommand\Services\Ask;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class UserRegisterCommand extends Command
{
    protected static $defaultDescription = 'Register new user';
    private string $className;
    private UserPasswordEncoderInterface $passwordEncoder;
    private Reader $reader;
    private SymfonyStyle $io;
    private EntityManagerInterface $em;

    public function __construct(
        string $className,
        UserPasswordEncoderInterface $passwordEncoder,
        Reader $reader,
        EntityManagerInterface $em)
    {
        $this->className = $className;
        parent::__construct();
        $this->passwordEncoder = $passwordEncoder;
        $this->reader = $reader;
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $userReflection = new \ReflectionClass($this->className);

        $data = [];
        $ask = new Ask(
            $this->className,
            $this->reader,
            $this->io,
            $this->passwordEncoder
        );
        foreach ($userReflection->getProperties() as $property) {
            $data[$property->getName()] = $ask->ask($property->getName());
        }

        $user = $this->getSerializer()->denormalize($data, $this->className);

        $this->em->persist($user);
        $this->em->flush();

        $this->io->success('User' . $ask->getUserIdentifier() . 'registered.');
        return Command::SUCCESS;
    }

    private function getSerializer()
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        return new Serializer($normalizers, $encoders);
    }
}
