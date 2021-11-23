<?php

namespace Fabricio872\RegisterCommand\Annotations;

use Attribute;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"PROPERTY"})
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class RegisterCommand
{
    /**
     * Type of field you want to use
     * @Enum({"string", "hidden", "hiddenRepeat", "password", "array", "list", "dateTime", "date", "yesNo"})
     * @var string
     */
    public $field;

    /**
     * Question that has to be asked user
     * @var string
     */
    public $question;

    /**
     * Question that has to be asked user
     * @var array
     */
    public $options;

    /**
     * Set field that should be used in success message after user is created
     * @var bool
     */
    public $userIdentifier = false;

    /**
     * @var bool
     */
    public $valueBoolean;

    /**
     * @var string
     */
    public $valueString;

    /**
     * This value gets encrypted by current password encryptor
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

    public function __construct(
        ?string $field = null,
        ?string $question = null,
        ?array  $options = null,
        bool   $userIdentifier = false,
        ?bool   $valueBoolean = null,
        ?string $valueString = null,
        ?string $valuePassword = null,
        ?array  $valueArray = null,
        ?int    $valueInt = null,
        ?float  $valueFloat = null,
        ?string $valueDateTime = null
    ) {
        $this->field = $field;
        $this->question = $question;
        $this->options = $options;
        $this->userIdentifier = $userIdentifier;
        $this->valueBoolean = $valueBoolean;
        $this->valueString = $valueString;
        $this->valuePassword = $valuePassword;
        $this->valueArray = $valueArray;
        $this->valueInt = $valueInt;
        $this->valueFloat = $valueFloat;
        $this->valueDateTime = $valueDateTime;
    }
}
