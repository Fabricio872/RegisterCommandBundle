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
     * @var string
     */
    public $field;

    /**
     * @var string
     */
    public $question;

    /**
     * @var bool
     */
    public $userIdentifier = false;

    /**
     * @var string
     */
    public $valueString;

    /**
     * @var string
     */
    public $valuePassword;

    /**
     * @var array
     */
    public $valueArray;

    /**
     * @var int
     */
    public $valueInt;

    /**
     * @var float
     */
    public $valueFloat;
}