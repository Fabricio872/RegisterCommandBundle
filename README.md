
![GitHub tag (latest by date)](https://img.shields.io/github/v/tag/Fabricio872/RegisterCommand)
![GitHub last commit](https://img.shields.io/github/last-commit/Fabricio872/RegisterCommand)
[![PHP Composer Test and Tag](https://github.com/Fabricio872/RegisterCommandBundle/actions/workflows/php-composer.yml/badge.svg)](https://github.com/Fabricio872/RegisterCommandBundle/actions/workflows/php-composer.yml)
![Packagist Downloads](https://img.shields.io/packagist/dt/Fabricio872/register-command)
![GitHub Repo stars](https://img.shields.io/github/stars/Fabricio872/RegisterCommand?style=social)

Installation
============

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require fabricio872/register-command
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require fabricio872/register-command
```

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Fabricio872\RegisterCommand\RegisterCommandBundle::class => ['all' => true],
];
```

# Usage
Configuration example:
```yaml
# config/services.yaml

# ...
# Default configuration for extension with alias: "register_command"
RegisterBundle:

    # Entity for your user
    user_class:           App\Entity\User

    # Sets default value for maximum rows on single page of list table
    table_limit:          10

    # Sets maximum width for single column in characters
    max_col_width:        64
# ...
```

## Configure Entity

### Configuration with Inputs 
To field to be asked from terminal you have to set fields to one of those input types:
"string", "hidden", "hiddenRepeat", "password", "array", "dateTime", "date", "yesNo'

#### Example with string value:

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @RegisterCommand(
     *     field="string",
     *     userIdentifier=true
     * )
     */
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
     * @RegisterCommand(
     *     field="hidden"
     * )
     */
    private $hiddenValue;
// ...
```
#### Example with hidden repeated value:

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @RegisterCommand(
     *     field="hiddenRepeated"
     * )
     */
    private $hiddenRepeatedValue;
// ...
```

#### Example for password value:

```php
// src/Entity/User.php
// ...
    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @RegisterCommand(
     *     field="password"
     * )
     */
    private $password;
// ...
```
> note: "password" is same as "hiddenRepeat", but it gets encrypted by configured password encryption method

#### Example for array value:

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="json")
     * @RegisterCommand(
     *     field="array"
     * )
     */
    private $roles = [];
// ...
```

#### Example for list value:

```php
// src/Entity/User.php
// ...
    /**
     * @RegisterCommand(
     *     options={"ROLE_USER", "ROLE_ADMIN", "ROLE_SUPER_ADMIN"},
     *     field="list"
     * )
     * @ORM\Column(type="json")
     */
    private $roles = [];
// ...
```
> this uses additional parameter "options" where you can specify multiple available options from which you can pick one or more at registering process

#### Example for datetime value:

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="datetime")
     * @RegisterCommand(
     *     field="datetime"
     * )
     */
    private $datetime;
// ...
```

#### Example for date value:

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="datetime")
     * @RegisterCommand(
     *     field="date"
     * )
     */
    private $date;
// ...
```

#### Example for yesNo value:

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="boolean")
     * @RegisterCommand(
     *     field="yesNo"
     * )
     */
    private $date;
// ...
```

#### Additional options:

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
     * @RegisterCommand(
     *     field="string",
     *     userIdentifier=true,
     *     question="Type your email"
     * )
     */
    private $email;
//...

```

### Configuration with pre-defined values

If you want to define default value instead of asking for one you can use these settings   

#### Example for boolean value

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="boolean")
     * @RegisterCommand(
     *      valueBoolean=true
     * )
     */
    private $boolean;
// ...
```

#### Example for string value

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @RegisterCommand(
     *      valueString="some string"
     * )
     */
    private $string;
// ...
```

#### Example for password value

> This value gets automatically encrypted by current password encryption method
```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @RegisterCommand(
     *      valuePassword="P4$$w0rd"
     * )
     */
    private $password;
// ...
```

#### Example for array value

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="json")
     * @RegisterCommand(
     *      valueArray={"ROLE_ADMIN"}
     * )
     */
    private $roles;
// ...
```

#### Example for int value

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="integer")
     * @RegisterCommand(
     *      valueInt=420
     * )
     */
    private $integer;
// ...
```

#### Example for float value

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="float")
     * @RegisterCommand(
     *      valueFloat=420.69
     * )
     */
    private $float;
// ...
```

#### Example for datetime value with current time

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="datetime")
     * @RegisterCommand(
     *      valueDateTime="now"
     * )
     */
    private $roles;
// ...
```

> For "valueDateTime" you can specify same value as for DateTime PHP funcition

#### Example for datetime value with defined value

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="datetime")
     * @RegisterCommand(
     *      valueDateTime="2020-04-20T16:20:00.69Z"
     * )
     */
    private $date;
// ...
```

## Finally, you are ready to register some users.

Execute this command:
```console
$ bin/console user:register
```

To list all existing users execute this:
```console
$ bin/console user:list
```

To jump to exact page execute this:
```console
$ bin/console user:list {page_number}
```

example for page 2:
```console
$ bin/console user:list 2
```
To change maximum rows in table use option -l or --limit:

```console
$ bin/console user:list -l {table_limit}
```
example for showing maximum 5 rows:

```console
$ bin/console user:list -l 5
```
To change maximum width of each column use option -w or --col-width:

```console
$ bin/console user:list -w {table_limit}
```
example for col width 32 characters:

```console
$ bin/console user:list -w 32
```
