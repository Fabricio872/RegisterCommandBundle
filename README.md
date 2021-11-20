
![GitHub tag (latest by date)](https://img.shields.io/github/v/tag/Fabricio872/RegisterCommand)
![GitHub last commit](https://img.shields.io/github/last-commit/Fabricio872/RegisterCommand)
[![PHP Composer Test and Tag](https://github.com/Fabricio872/RegisterCommandBundle/actions/workflows/php-composer.yml/badge.svg)](https://github.com/Fabricio872/RegisterCommandBundle/actions/workflows/php-composer.yml)
![Packagist Downloads](https://img.shields.io/packagist/dt/Fabricio872/register-command)
![GitHub Repo stars](https://img.shields.io/github/stars/Fabricio872/RegisterCommand?style=social)

# Valuable partners: 

![PhpStorm logo](https://resources.jetbrains.com/storage/products/company/brand/logos/PhpStorm.svg)

Installation
============

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 1: Download the Bundle

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require fabricio872/register-command
```

Applications that don't use Symfony Flex
----------------------------------------

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

 > note: In case of combining Annotations and Attributes of this bundle only Attributes will be used.

 - Documentation for Annotation usage is [here](https://github.com/Fabricio872/RegisterCommandBundle/tree/main/docs/ANNOTATION_EXAMPLES.md) 
 - Documentation for Attributes (PHP 8) usage is [here](https://github.com/Fabricio872/RegisterCommandBundle/tree/main/docs/ATTRUBUTE_EXAMPLES.md) 

## Finally, you are ready to register some users.

Execute this command:
```console
$ bin/console user:register
```

To list all existing users execute this:
```console
$ bin/console user:list
```

 > In list view you can switch to edit mode with 'e' and quit with 'q' option

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
