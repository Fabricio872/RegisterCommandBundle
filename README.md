
![GitHub tag (latest by date)](https://img.shields.io/github/v/tag/Fabricio872/RegisterCommand)
![GitHub last commit](https://img.shields.io/github/last-commit/Fabricio872/RegisterCommand)
[![Build Status](https://travis-ci.org/Fabricio872/RegisterCommand.svg?branch=main)](https://travis-ci.org/Fabricio872/RegisterCommand)
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
If your user entity is not `App\Entity\User` set it in services.yaml as:
```yaml
# config/services.yaml

# ...
# Default configuration for extension with alias: "register_command"
RegisterBundle:

    # Entity for your user
    user_class:           App\Entity\User
```

## Configure Entity

To field to be asked from terminal you have to set fields to one of those input types:
"string", "hidden", "hiddenRepeat", "password", "array", "dateTime"

> note: "password" is same s "hiddenRepeat", but it gets encrypted

Example with string value:

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

> note: "userIdentifier" is not required and has to be set to only one field and value from that field will be used as success message to identify who was registered 

To field to be populated automatically with some default value you can use one of these inputs:
"valueString", "valuePassword", "valueArray", "valueInt", "valueFloat", "valueDateTime"

> note: "valuePassword" is same as "valueString" but encrypted

Example for "valueArray":

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="json")
     * @RegisterCommand(
     *     valueArray={"ROLE_USER"}
     * )
     */
    private $roles = [];
// ...
```

Example for "valuePassword":

```php
// src/Entity/User.php
// ...
    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @RegisterCommand(
     *     valuePassword="defaultPassword"
     * )
     */
    private $password;
// ...
```

To customize asked question you can use parameter "question"

Example:
```php
// src/Entity/User.php
//...
    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @RegisterCommand(
     *     field="string",
     *     userIdentifier=true,
     *     question="Type your email"
     * )
     */
    private $email;
//...

```

For "valueDateTime" you can specify same value as for DateTime PHP funcition

```php
// src/Entity/User.php
// ...
    /**
     * @ORM\Column(type="datetime")
     * @RegisterCommand(
     *      valueDateTime="now"
     * )
     */
    private $date;
// ...
```

Finally, you are ready to register some users.

Execute this command:
```console
$ bin/console user:register
```

To list all existing users execute this:
```console
$ bin/console user:list
```
