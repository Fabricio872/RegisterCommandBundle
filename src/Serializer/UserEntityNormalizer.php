<?php

namespace Fabricio872\RegisterCommand\Serializer;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserEntityNormalizer implements NormalizerInterface
{
    /**
     * @var array
     */
    private $getters;

    public function __construct(array $getters)
    {
        $this->getters = $getters;
    }

    public function supportsNormalization($object, $format = null, array $context = [])
    {
        return $object instanceof UserInterface;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $processedData = [];
        foreach ($this->getters as $getter) {
//            $this->reader->get
            $processedData[] = $this->processProperty($object->$getter());
        }
        return $processedData;
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
        if ($property instanceof \DateTime) {
            return $property->format('c');
        }

        return 'Unknown datatype';
    }
}