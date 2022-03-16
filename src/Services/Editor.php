<?php

namespace Fabricio872\RegisterCommand\Services;

use Fabricio872\RegisterCommand\Annotations\AbstractEditor;
use Fabricio872\RegisterCommand\Exceptions\EngineNotSetException;
use Fabricio872\RegisterCommand\Exceptions\EngineNotSupported;
use Fabricio872\RegisterCommand\Services\engine\EngineInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Editor
{
    private UserInterface $user;
    public static ?EngineInterface $ENGINE = null;

    public function __construct(
        $user
    )
    {
        $this->user = $user;
    }

    public function getEntity()
    {
        return $this->user;
    }

    /**
     * @throws EngineNotSetException
     */
    public function run()
    {
        if (is_null(self::$ENGINE)) {
            throw new EngineNotSetException();
        }

        $reflection = new \ReflectionClass($this->user);
        foreach ($reflection->getProperties() as $parameter) {
            foreach ($parameter->getAttributes() as $attribute) {
                if (is_subclass_of($attribute->getName(), AbstractEditor::class)) {
                    $parameter->setAccessible(true);
                    /** @var AbstractEditor $ask */
                    $ask = $attribute->newInstance();
                    if (!method_exists($ask, $this->getEngineMethod())){
                        throw new EngineNotSupported(get_class(self::$ENGINE), $attribute->getName());
                    }
                    $parameter->setValue($this->user, $ask->{$this->getEngineMethod()}());
                }
            }
        }
    }

    private function getEngineMethod(): string
    {
        return sprintf("ask%s", last(explode('\\', get_class(self::$ENGINE))));
    }
}