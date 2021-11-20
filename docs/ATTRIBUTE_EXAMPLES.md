# User Entity configuration
[Back to README](https://github.com/Fabricio872/RegisterCommandBundle/tree/main/README.md)


## Configuration with Inputs
To field to be asked from terminal you have to set "field" to one of those input types:
"string", "hidden", "hiddenRepeat", "password", "array", "dateTime", "date", "yesNo'

### Example with string value:

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    #[RegisterCommand(
        field: "string",
        userIdentifier: true
    )]
    private $email;
// ...
```

> note: "userIdentifier" is not required and has to be set to only one field and value from that field will be used in a success message to identify who was registered

#### Example with hidden value:

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    #[RegisterCommand(
        field: "hidden"
    )]
    private $hiddenValue;
// ...
```
### Example with hidden repeated value:

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    #[RegisterCommand(
        field: "hiddenRepeated"
    )]
    private $hiddenRepeatedValue;
// ...
```

### Example for password value:

```php
// src/Entity/User.php
// ...
    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    #[RegisterCommand(
        field: "password"
    )]
    private $password;
// ...
```
> note: "password" is same as "hiddenRepeat", but it gets encrypted by configured password encryption method

### Example for array value:

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="json")
     */
    #[RegisterCommand(
        field: "array"
    )]
    private $roles = [];
// ...
```

### Example for list value:

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="json")
     */
    #[RegisterCommand(
        field: "list",
        options: ["ROLE_USER", "ROLE_ADMIN", "ROLE_SUPER_ADMIN"]
    )]
    private $roles = [];
// ...
```
> this uses additional parameter "options" where you can specify multiple available options from which you can pick one or more at registering process

### Example for datetime value:

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="datetime")
     */
    #[RegisterCommand(
        field: "datetime"
    )]
    private $datetime;
// ...
```

### Example for date value:

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="datetime")
     */
    #[RegisterCommand(
        field: "date"
    )]
    private $date;
// ...
```

### Example for yesNo value:

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="boolean")
     */
    #[RegisterCommand(
        field: "yesNo"
    )]
    private $date;
// ...
```

### Additional options:

> To each defined field you can make customized question with "question" parameter

> To one defined field you can add "userIdentifier" to define value which will be used in a success message to identify who was registered

> For validating each value you can define Assert function ([more detailed documentation](https://symfony.com/doc/current/validation.html))

Example:
```php
// src/Entity/User.php
//...
    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email()
     */
    #[RegisterCommand(
        field: "string",
        userIdentifier: true,
        question: "Type your email"
    )]
    private $email;
//...

```

## Configuration with pre-defined values

If you want to define default value instead of asking for one you can use these settings

### Example for boolean value

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="boolean")
     */
    #[RegisterCommand(
        valueBoolean: true
    )]
    private $boolean;
// ...
```

### Example for string value

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    #[RegisterCommand(
        valueString: "some string"
    )]
    private $string;
// ...
```

### Example for password value

> This value gets automatically encrypted by current password encryption method
```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    #[RegisterCommand(
        valuePassword: "P4$$w0rd"
    )]
    private $password;
// ...
```

### Example for array value

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="json")
     */
    #[RegisterCommand(
        valueArray: ["ROLE_ADMIN"]
    )]
    private $roles;
// ...
```

### Example for int value

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="integer")
     */
    #[RegisterCommand(
        valueInt: 420
    )]
    private $integer;
// ...
```

### Example for float value

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="float")
     */
    #[RegisterCommand(
        valueFloat: 420.69
    )]
    private $float;
// ...
```

### Example for datetime value with current time

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="datetime")
     */
    #[RegisterCommand(
        valueDateTime: "now"
    )]
    private $roles;
// ...
```

> For "valueDateTime" you can specify same value as for DateTime PHP function

### Example for datetime value with defined value

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="datetime")
     */
    #[RegisterCommand(
        valueDateTime: "2020-04-20T16:20:00.69Z"
    )]
    private $date;
// ...
```
