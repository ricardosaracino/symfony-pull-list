<?php

namespace App\ObjectNormalizer;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

// TODO RENAME THIS
class EntityNormalize extends ObjectNormalizer {

    public function __construct(ClassMetadataFactoryInterface $classMetadataFactory = null, NameConverterInterface $nameConverter = null, PropertyAccessorInterface $propertyAccessor = null, PropertyTypeExtractorInterface $propertyTypeExtractor = null, ClassDiscriminatorResolverInterface $classDiscriminatorResolver = null)
    {
        parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor, $classDiscriminatorResolver);

        $this->setIgnoredAttributes(['__initializer__', '__cloner__', '__isInitialized__']);

        // https://symfony.com/doc/current/components/serializer.html#handling-serialization-depth
        $this->setCircularReferenceLimit(0);

        $this->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
    }
}