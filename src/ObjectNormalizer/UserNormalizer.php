<?php

namespace App\ObjectNormalizer;

class UserNormalizer extends \Symfony\Component\Serializer\Normalizer\ObjectNormalizer {

	public function __construct(ClassMetadataFactoryInterface $classMetadataFactory = null, NameConverterInterface $nameConverter = null, PropertyAccessorInterface $propertyAccessor = null, PropertyTypeExtractorInterface $propertyTypeExtractor = null, ClassDiscriminatorResolverInterface $classDiscriminatorResolver = null)
	{
		parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor, $classDiscriminatorResolver);

		$this->setIgnoredAttributes(['password', 'salt']);

		// https://symfony.com/doc/current/components/serializer.html#handling-serialization-depth
		$this->setCircularReferenceLimit(0);

		$this->setCircularReferenceHandler(function ($object) {
			return $object->getId();
		});
	}
}