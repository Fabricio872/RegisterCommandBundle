<?php


namespace Fabricio872\RegisterCommand\Services;

use Doctrine\Common\Annotations\AnnotationReader;
use Fabricio872\RegisterCommand\Annotations\RegisterCommand;
use Fabricio872\RegisterCommand\Serializer\UserEntityNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class StaticMethods
{
    private static $serializer;

    public static function getRegisterCommand(string $userClass, string $propertyName): ?RegisterCommand
    {
        $reader = new AnnotationReader();

        $userReflection = new \ReflectionClass($userClass);
        /** @var ?RegisterCommand $annotation */
        $annotation = $reader->getPropertyAnnotation($userReflection->getProperty($propertyName), RegisterCommand::class);

        if (method_exists($userReflection->getProperty($propertyName), 'getAttributes')) {
            $attributes = $userReflection->getProperty($propertyName)->getAttributes();
            foreach ($attributes as $attribute) {
                if ($attribute->getName() == RegisterCommand::class) {
                    $annotation = $attribute->newInstance();
                }
            }
        }

        return $annotation;
    }

    /**
     * @return Serializer
     */
    public static function getSerializer(): Serializer
    {
        if (!isset(self::$serializer)) {
            $encoders = [new JsonEncoder()];
            $normalizers = [new ObjectNormalizer(), new UserEntityNormalizer()];

            self::$serializer = new Serializer($normalizers, $encoders);
        }
        return self::$serializer;
    }
}
