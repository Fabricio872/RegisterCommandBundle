<?php

declare(strict_types=1);

namespace Fabricio872\RegisterCommand\Command;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Fabricio872\RegisterCommand\Services\Ask;
use Fabricio872\RegisterCommand\Services\StaticMethods;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'user:register',
    description: 'Register new user',
)]
class UserRegisterCommand extends Command
{
    private ?SymfonyStyle $io = null;

    /**
     * UserRegisterCommand constructor.
     * @param string $userClassName
     * @param UserPasswordHasherInterface $passwordEncoder
     * @param EntityManagerInterface $em
     */
    public function __construct(
        private readonly string $userClassName,
        private readonly UserPasswordHasherInterface $passwordEncoder,
        private readonly EntityManagerInterface $em,
        private readonly ValidatorInterface $validator
    ) {
        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ReflectionException
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $userClassReflection = new ReflectionClass($this->userClassName);

        $userClass = new $this->userClassName();
        if (! $userClass instanceof UserInterface) {
            throw new Exception("Provided user must implement " . UserInterface::class);
        }

        $data = [];
        $ask = new Ask(
            $this->userClassName,
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
