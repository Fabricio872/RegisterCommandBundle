<?php

declare(strict_types=1);

namespace Fabricio872\RegisterCommand\Services;

use DateTime;
use Doctrine\Common\Annotations\Reader;
use Exception;
use Fabricio872\RegisterCommand\Annotations\RegisterCommand;
use Fabricio872\RegisterCommand\Services\Questions\QuestionAbstract;
use Fabricio872\RegisterCommand\Services\Questions\QuestionInterface;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Ask
{
    private string $userIdentifier = ' ';

    public function __construct(
        private readonly string $userClassName,
        private readonly Reader $reader,
        private readonly SymfonyStyle $io,
        private readonly InputInterface $input,
        private readonly OutputInterface $output,
        private readonly UserPasswordHasherInterface $passwordEncoder,
        private readonly ValidatorInterface $validator
    ) {
    }

    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }

    /**
     * @return string|array|int|float|null
     * @throws ReflectionException
     */
    public function ask(string $propertyName)
    {
        $userReflection = new ReflectionClass($this->userClassName);
        $annotation = StaticMethods::getRegisterCommand($this->userClassName, $propertyName);

        if ($annotation === null) {
            return null;
        }
        if ($value = $this->getDefaultValue($annotation)) {
            return $value;
        }

        /** @var QuestionAbstract $question */
        $question = $this->makeQuestion($annotation, $propertyName);

        if (! $question instanceof QuestionAbstract) {
            throw new Exception('Input class: ' . $question::class . ' must implement ' . QuestionInterface::class);
        }

        if ($this->reader->getPropertyAnnotation($userReflection->getProperty($propertyName), Constraint::class)) {
            $answer = $this->validate($question, $userReflection, $propertyName);
        } else {
            $answer = $question->getAnswer();
        }

        if ($annotation->userIdentifier) {
            $this->userIdentifier = ' ' . $answer . ' ';
        }

        return $answer;
    }

    public function getUserClassName(): string
    {
        return $this->userClassName;
    }

    public function getReader(): Reader
    {
        return $this->reader;
    }

    private function makeQuestion(RegisterCommand $annotation, string $propertyName): QuestionInterface
    {
        $questionName = 'Fabricio872\RegisterCommand\Services\Questions\\' . ucfirst($annotation->field) . 'Input';

        return new $questionName(
            $this->io,
            $this->input,
            $this->output,
            $annotation->question ?? 'Set ' . $annotation->field . ' for field ' . $propertyName,
            $this->passwordEncoder,
            new $this->userClassName(),
            $annotation->options
        );
    }

    /**
     * @throws Exception
     */
    private function getDefaultValue(RegisterCommand $command): float|DateTime|int|bool|array|string|null
    {
        foreach (get_object_vars($command) as $annotation => $value) {
            if (
                str_starts_with((string) $annotation, 'value') &&
                $value !== null
            ) {
                return $this->processValue($value, $annotation);
            }
        }
        return null;
    }

    /**
     * @param $value
     * @throws Exception
     */
    private function processValue($value, string $annotation): array|bool|DateTime|float|int|string
    {
        return match ($annotation) {
            'valueBoolean' => (bool) $value,
            'valueString' => (string) $value,
            'valuePassword' => (string) $this->passwordEncoder->hashPassword(new $this->userClassName(), $value),
            'valueArray' => (array) $value,
            'valueInt' => (int) $value,
            'valueFloat' => (float) $value,
            'valueDateTime' => new DateTime($value),
            default => throw new Exception("Unsupported value type: " . $annotation),
        };
    }

    private function validate(QuestionInterface $question, ReflectionClass $userReflection, string $propertyName)
    {
        $answer = $question->getAnswer();

        /** @var ConstraintViolation $violation */
        foreach (
            $this->validator->validate(
                $answer,
                array_filter(
                    array_map(
                        function ($annotation) {
                            if ($annotation instanceof Constraint) {
                                return $annotation;
                            }
                            return null;
                        },
                        $this->reader->getPropertyAnnotations(
                            $userReflection->getProperty($propertyName)
                        )
                    )
                )
            )
            as $violation) {
            $this->io->warning($violation->getMessage());
        }

        if (isset($violation)) {
            return $this->validate($question, $userReflection, $propertyName);
        }
        return $answer;
    }
}
