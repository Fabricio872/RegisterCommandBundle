<?php

namespace Fabricio872\RegisterCommand\Services;

use Fabricio872\RegisterCommand\Annotations\AbstractEditor;
use Fabricio872\RegisterCommand\Exceptions\EngineNotSetException;
use Fabricio872\RegisterCommand\Exceptions\EngineNotSupported;
use Fabricio872\RegisterCommand\Services\Engines\EngineInterface;
use Fabricio872\RegisterCommand\Services\Questions\QuestionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Editor
{
    private UserInterface $user;
    public static ?EngineInterface $ENGINE = null;
    private ContainerInterface $container;

    public function __construct(
        ContainerInterface $container
    )
    {
        $this->container = $container;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }

    /**
     * @throws EngineNotSetException|EngineNotSupported
     */
    public function run(): void
    {
        if (is_null(self::$ENGINE)) {
            throw new EngineNotSetException();
        }

        $reflection = new \ReflectionClass($this->user);
        foreach ($reflection->getProperties() as $parameter) {
            foreach ($parameter->getAttributes() as $attribute) {
                if (is_subclass_of($attribute->getName(), AbstractEditor::class)) {
                    $parameter->setAccessible(true);
                    /** @var AbstractEditor $editor */
                    $editor = $attribute->newInstance();
                    if (!method_exists($editor, $this->getEngineMethod())) {
                        throw new EngineNotSupported(get_class(self::$ENGINE), $attribute->getName());
                    }
                    /** @var QuestionInterface $question */
                    $question = $this->container->get($editor->{$this->getEngineMethod()}());
                    $question->setField($parameter->getName());
                    $question->setEditor($editor);
                    $question->setEngine(self::$ENGINE);
                    $parameter->setValue($this->user, $question->getAnswer());
                }
            }
        }
    }

    private function getEngineMethod(): string
    {
        return sprintf("ask%s", last(explode('\\', get_class(self::$ENGINE))));
    }
}