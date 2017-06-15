<?php
namespace AppBundle\Serializer\Exclusion;

use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Context;

class PropertyListExclusionStrategy implements ExclusionStrategyInterface
{

    private $propertyList;

    public function __construct($propertyList)
    {
        $this->propertyList = $propertyList;
    }

    public function shouldSkipClass(ClassMetadata $metadata, Context $context)
    {
        $context->getVisitor();

        return false;
    }

    public function shouldSkipProperty(PropertyMetadata $property, Context $context)
    {
        $name = $property->serializedName ? : $property->name;

        if ($context->getDepth() == 1 && !in_array($name, $this->propertyList)) {
            return true;
        }

        return false;
    }

}