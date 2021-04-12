<?php

namespace Fabricio872\RegisterCommand\Services;

use Doctrine\Common\Annotations\Reader;
use Fabricio872\RegisterCommand\Annotations\RegisterCommand;
use Fabricio872\RegisterCommand\Services\Questions\QuestionAbstract;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Ask
{
    private string $className;
    private Reader $reader;
    private SymfonyStyle $io;
    private UserPasswordEncoderInterface $passwordEncoder;
    private string $userIdentifier = ' ';

    public function __construct(
        string $className,
        Reader $reader,
        SymfonyStyle $io,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {
        $this->className = $className;
        $this->reader = $reader;
        $this->io = $io;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }

    /**
     * @param $property
     * @return string|null
     * @throws \ReflectionException
     */
    public function ask($property)
    {
        $userReflection = new \ReflectionClass($this->className);
        $annotation = $this->reader->getPropertyAnnotation($userReflection->getProperty($property), RegisterCommand::class);
        if ($annotation == null) {
            return null;
        }

        if ($value = $this->getDefaultValue($annotation)) {
            return $value;
        }

        $questionName = 'Fabricio872\RegisterCommand\Services\Questions\Input' . ucfirst(isset($annotation->field) ? $annotation->field : 'string');
        /** @var QuestionAbstract $question */
        $question = new $questionName(
            $this->io,
            $annotation->question ?? 'Set ' . $annotation->field . ' for field ' . $property,
            $this->passwordEncoder,
            new $this->className
        );

        /*
         * Validate input class
         */
        if (get_parent_class($question) != QuestionAbstract::class) {
            throw new \Exception('Input class: ' . get_class($question) . ' must extend ' . QuestionAbstract::class);
        }

        $answer = $question->getAnswer();

        if ($annotation->userIdentifier) {
            $this->userIdentifier = ' ' . $answer . ' ';
        }

        return $answer;
    }

    /**
     * @param RegisterCommand $command
     * @return mixed|null
     */
    private function getDefaultValue(RegisterCommand $command)
    {
        foreach ($command as $annotation => $value) {
            if (substr($annotation, 0, strlen('value')) == 'value') {
                return $this->processValue($value, $annotation);
            }
        }
        return null;
    }

    private function processValue($value, string $annotation)
    {
        switch ($annotation) {
            case 'valueString':
                return (string)$value;
            case 'valuePassword':
                return (string)$this->passwordEncoder->encodePassword(new $this->className, $value);
            case 'valueArray':
                return (array)$value;
            case 'valueInt':
                return (int)$value;
            case 'valueFloat':
                return (float)$value;
            default:
                throw new \Exception("Unsupported value type: " . $annotation);
        }
    }
}