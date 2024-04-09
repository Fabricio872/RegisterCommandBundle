<?php

declare(strict_types=1);


namespace Fabricio872\RegisterCommand\Services;

use Doctrine\Common\Annotations\AnnotationReader;
use Fabricio872\RegisterCommand\Annotations\RegisterCommand;
use Fabricio872\RegisterCommand\Serializer\UserEntityNormalizer;
use ReflectionClass;
use Stringable;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class StaticMethods
{
    private static ?Serializer $serializer = null;

    public static function getRegisterCommand(string $userClass, string $propertyName): ?RegisterCommand
    {
        $userReflection = new ReflectionClass($userClass);

        if (method_exists($userReflection->getProperty($propertyName), 'getAttributes')) {
            $attributes = $userReflection->getProperty($propertyName)->getAttributes();
            foreach ($attributes as $attribute) {
                if ($attribute->getName() === RegisterCommand::class) {
                    return $attribute->newInstance();
                }
            }
        }

        return null;
    }

    public static function userToArray(mixed $user): array
    {
        $arrayOfUser = [];
        $reflection = new ReflectionClass($user);
        foreach ($reflection->getProperties() as $property) {
            $arrayOfUser[$property->getName()] = self::processProperty($property->getValue($user));
        }
        return $arrayOfUser;
    }
    private static function processProperty($property)
    {
        if ($property instanceof Stringable) {
            return (string)$property;
        }
        if (is_numeric($property)) {
            return $property;
        }
        if (null === $property) {
            return 'NULL';
        }
        if (is_array($property)) {
            return implode(', ', $property);
        }
        if (is_string($property)) {
            return $property;
        }
        if (is_bool($property)) {
            return $property ? 'Yes' : 'No';
        }
        if ($property instanceof DateTime) {
            return $property->format('c');
        }

        return 'Unknown datatype';
    }
    public static function getSerializer(): Serializer
    {
        if (! isset(self::$serializer)) {
            $encoders = [new JsonEncoder()];
            $normalizers = [new ObjectNormalizer()];

            self::$serializer = new Serializer($normalizers, $encoders);
        }
        return self::$serializer;
    }
}
