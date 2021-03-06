<?php

namespace Fabricio872\RegisterCommand\Command;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManagerInterface;
use Fabricio872\RegisterCommand\Services\Ask;
use Fabricio872\RegisterCommand\Services\StaticMethods;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserRegisterCommand extends Command
{
    protected static $defaultDescription = 'Register new user';
    /** @var string $userClassName */
    private $userClassName;
    /** @var UserPasswordHasherInterface $passwordEncoder */
    private $passwordEncoder;
    /** @var Reader $reader */
    private $reader;
    /** @var SymfonyStyle $io */
    private $io;
    /** @var EntityManagerInterface $em */
    private $em;
    /** @var ValidatorInterface */
    private $validator;

    /**
     * UserRegisterCommand constructor.
     * @param string $userClassName
     * @param UserPasswordHasherInterface $passwordEncoder
     * @param Reader $reader
     * @param EntityManagerInterface $em
     */
    public function __construct(
        string $userClassName,
        UserPasswordHasherInterface $passwordEncoder,
        Reader $reader,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ) {
        $this->userClassName = $userClassName;
        parent::__construct();
        $this->passwordEncoder = $passwordEncoder;
        $this->reader = $reader;
        $this->em = $em;
        $this->validator = $validator;
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
            $input,
            $output,
            $this->passwordEncoder,
            $this->validator
        );
        foreach ($userClassReflection->getProperties() as $property) {
            $data[$property->getName()] = $ask->ask($property->getName());
        }

        $user = StaticMethods::getSerializer()->denormalize($data, $this->userClassName);

        $this->em->persist($user);
        $this->em->flush();

        $this->io->success('User' . $ask->getUserIdentifier() . 'registered.');
        return 0;
    }
}
