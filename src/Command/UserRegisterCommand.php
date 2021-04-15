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
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class UserRegisterCommand extends Command
{
    protected static $defaultDescription = 'Register new user';
    /** @var string $userClassName */
    private $userClassName;
    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;
    /** @var Reader $reader */
    private $reader;
    /** @var SymfonyStyle $io */
    private $io;
    /** @var EntityManagerInterface $em */
    private $em;

    /**
     * UserRegisterCommand constructor.
     * @param string $userClassName
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Reader $reader
     * @param EntityManagerInterface $em
     */
    public function __construct(
        string $userClassName,
        UserPasswordEncoderInterface $passwordEncoder,
        Reader $reader,
        EntityManagerInterface $em)
    {
        $this->userClassName = $userClassName;
        parent::__construct();
        $this->passwordEncoder = $passwordEncoder;
        $this->reader = $reader;
        $this->em = $em;
    }

    protected function configure()
    {
        $this->setDescription(self::$defaultDescription);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \ReflectionException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $userClassReflection = new \ReflectionClass($this->userClassName);

        $userClass = new $this->userClassName();
        if (!$userClass instanceof UserInterface) {
            throw new \Exception("Provided user must implement " . UserInterface::class);
        }

        $data = [];
        $ask = new Ask(
            $this->userClassName,
            $this->reader,
            $this->io,
            $this->passwordEncoder
        );
        foreach ($userClassReflection->getProperties() as $property) {
            $data[$property->getName()] = $ask->ask($property->getName());
        }

        $user = $this->getSerializer()->denormalize($data, $this->userClassName);

        $this->em->persist($user);
        $this->em->flush();

        $this->io->success('User' . $ask->getUserIdentifier() . 'registered.');
        return 0;
    }

    /**
     * @return Serializer
     */
    private function getSerializer(): Serializer
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        return new Serializer($normalizers, $encoders);
    }
}
