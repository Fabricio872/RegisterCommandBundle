<?php

namespace Fabricio872\RegisterCommand\Annotations;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class RegisterCommand
{
    /**
     * Type of field you want to use
     * @Enum({"string", "hidden", "hiddenRepeat", "password", "array", "dateTime"})
     * @var string
     */
    public $field;

    /**
     * Question that has to be asked user
     * @var string
     */
    public $question;

    /**
     * Set field that should be used in success message after user is created
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

    /**
     * Use datetime input type is same as PHP DateTime function
     * @var string
     */
    public $valueDateTime;
}