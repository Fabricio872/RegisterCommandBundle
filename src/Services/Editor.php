<?php

namespace Fabricio872\RegisterCommand\Services;

use Fabricio872\RegisterCommand\Annotations\AbstractEditor;

class Editor
{
    static bool $TTY = false;
    private $user;

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

    public function run()
    {
        $reflection = new \ReflectionClass($this->user);
        foreach ($reflection->getProperties() as $parameter) {
            foreach ($parameter->getAttributes() as $attribute) {
                if (is_subclass_of($attribute->getName(), AbstractEditor::class)) {
                    $parameter->setAccessible(true);
                    /** @var AbstractEditor $ask */
                    $ask = $attribute->newInstance();
                    if (self::$TTY){
                        $parameter->setValue($this->user, $ask->askTTY());
                    } else {
                        $parameter->setValue($this->user, $ask->askNoTTY());
                    }
                }
            }
        }
    }
}