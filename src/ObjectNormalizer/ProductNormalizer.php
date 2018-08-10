<?php

namespace App\ObjectNormalizer;

class ProductNormalizer extends EntityNormalize
{
    public function __construct(ClassMetadataFactoryInterface $classMetadataFactory = null, NameConverterInterface $nameConverter = null, PropertyAccessorInterface $propertyAccessor = null, PropertyTypeExtractorInterface $propertyTypeExtractor = null, ClassDiscriminatorResolverInterface $classDiscriminatorResolver = null)
    {
        parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor, $classDiscriminatorResolver);

        ## TODO
        $this->setIgnoredAttributes(['products','__initializer__', '__cloner__', '__isInitialized__']);
    }
}