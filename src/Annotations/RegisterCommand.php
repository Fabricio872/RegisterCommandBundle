<?php

namespace Fabricio872\RegisterCommand\Annotations;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class RegisterCommand
{
    /**
     * @Enum({"string", "hidden", "hiddenRepeat", "password", "array"})
     */
    public string $field;

    /**
     * @var string
     */
    public string $question;

    /**
     * @var bool
     */
    public bool $userIdentifier = false;

    /**
     * @var string
     */
    public string $valueString;

    /**
     * @var string
     */
    public string $valuePassword;

    /**
     * @var array
     */
    public array $valueArray;

    /**
     * @var int
     */
    public int $valueInt;

    /**
     * @var float
     */
    public float $valueFloat;
}