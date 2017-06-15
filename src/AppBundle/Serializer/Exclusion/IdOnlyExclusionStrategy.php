<?php

namespace AppBundle\Serializer\Exclusion;

use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Context;

class IdOnlyExclusionStrategy implements ExclusionStrategyInterface
{
    private $depth;

    public function __construct($depth)
    {
        $this->depth = $depth;
    }

    public function shouldSkipClass(ClassMetadata $metadata, Context $context)
    {
        return false;
    }

    public function shouldSkipProperty(PropertyMetadata $property, Context $context)
    {
        $name = $property->serializedName ? : $property->name;

        if ($context->getDepth() >= $this->depth && 'id' !== $name) {
            return true;
        }

        return false;
    }
}