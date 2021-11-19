<?php

namespace Fabricio872\RegisterCommand\Services;

use Doctrine\Common\Annotations\Reader;
use Fabricio872\RegisterCommand\Annotations\RegisterCommand;
use Fabricio872\RegisterCommand\Services\Questions\QuestionAbstract;
use Fabricio872\RegisterCommand\Services\Questions\QuestionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Ask
{
    /** @var string $userClassName */
    private $userClassName;
    /** @var Reader $reader */
    private $reader;
    /** @var SymfonyStyle $io */
    private $io;
    /** @var InputInterface */
    private $input;
    /** @var OutputInterface */
    private $output;
    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;
    /** @var string $userIdentifier */
    private $userIdentifier = ' ';
    /** @var ValidatorInterface */
    private $validator;

    public function __construct(
        string                       $userClassName,
        Reader                       $reader,
        SymfonyStyle                 $io,
        InputInterface               $input,
        OutputInterface              $output,
        UserPasswordEncoderInterface $passwordEncoder,
        ValidatorInterface           $validator
    ) {
        $this->userClassName = $userClassName;
        $this->reader = $reader;
        $this->io = $io;
        $this->input = $input;
        $this->output = $output;
        $this->passwordEncoder = $passwordEncoder;
        $this->validator = $validator;
    }

    /**
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }

    /**
     * @param string $propertyName
     * @return string|array|int|float|null
     * @throws \ReflectionException
     */
    public function ask(string $propertyName)
    {
        $userReflection = new \ReflectionClass($this->userClassName);
        /** @var ?RegisterCommand $annotation */
        $annotation = $this->reader->getPropertyAnnotation($userReflection->getProperty($propertyName), RegisterCommand::class);

        $attributes = $userReflection->getProperty($propertyName)->getAttributes();
        foreach ($attributes as $attribute) {
            if ($attribute->getName() == RegisterCommand::class) {
                $annotation = $attribute->newInstance();
            }
        }

        if ($annotation == null) {
            return null;
        }
        if ($value = $this->getDefaultValue($annotation)) {
            return $value;
        }

        /** @var QuestionAbstract $question */
        $question = $this->makeQuestion($annotation, $propertyName);

        if (!$question instanceof QuestionAbstract) {
            throw new \Exception('Input class: ' . get_class($question) . ' must implement ' . QuestionInterface::class);
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

    /**
     * @return string
     */
    public function getUserClassName(): string
    {
        return $this->userClassName;
    }

    /**
     * @return Reader
     */
    public function getReader(): Reader
    {
        return $this->reader;
    }

    /**
     * @param RegisterCommand $annotation
     * @param string $propertyName
     * @return ?QuestionInterface
     */
    private function makeQuestion(RegisterCommand $annotation, string $propertyName): ?QuestionInterface
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
     * @param RegisterCommand $command
     * @return string|array|int|float|null
     */
    private function getDefaultValue(RegisterCommand $command)
    {
        foreach ($command as $annotation => $value) {
            if (
                substr($annotation, 0, strlen('value')) == 'value' &&
                $value !== null
            ) {
                return $this->processValue($value, $annotation);
            }
        }
        return null;
    }

    /**
     * @param $value
     * @param string $annotation
     * @return array|bool|\DateTime|float|int|string
     * @throws \Exception
     */
    private function processValue($value, string $annotation)
    {
        switch ($annotation) {
            case 'valueBoolean':
                return (bool)$value;
            case 'valueString':
                return (string)$value;
            case 'valuePassword':
                return (string)$this->passwordEncoder->encodePassword(new $this->userClassName(), $value);
            case 'valueArray':
                return (array)$value;
            case 'valueInt':
                return (int)$value;
            case 'valueFloat':
                return (float)$value;
            case 'valueDateTime':
                return new \DateTime($value);
            default:
                throw new \Exception("Unsupported value type: " . $annotation);
        }
    }

    private function validate(QuestionInterface $question, \ReflectionClass $userReflection, string $propertyName)
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
