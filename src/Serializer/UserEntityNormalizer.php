<?php

namespace Fabricio872\RegisterCommand\Serializer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserEntityNormalizer implements NormalizerInterface
{
    public function supportsNormalization($array, $format = null, array $context = [])
    {
        return is_array($array);
    }

    public function normalize($objects, $format = null, array $context = [])
    {
        $normalized = [];
        foreach ($objects as $object) {
            $normalized[] = $this->processProperty($object);
        }
        return $normalized;
    }

    private function processProperty($property)
    {
        if (is_numeric($property)) {
            return $property;
        }
        if (is_null($property)) {
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
        if ($property instanceof \DateTime) {
            return $property->format('c');
        }

        return 'Unknown datatype';
    }
}
