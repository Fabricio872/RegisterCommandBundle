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
// ...
register_command:
    user_class: App\Entity\User
```

## Configure Entity

To field to be asked from terminal you have to set fields to one of those input types:
"string", "hidden", "hiddenRepeat", "password", "array"

> note: "password" is same s "hiddenRepeat" but it gets encrypted

Example with string value:

```phpt
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
"valueString", "valuePassword", "valueArray", "valueInt", "valueFloat"

> note: "valuePassword" is same as "valueString" but encrypted

Example for "valueArray":

```phpt
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

```phpt
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
