<?php

namespace App\ObjectNormalizer;

class ProductNormalizer extends \Symfony\Component\Serializer\Normalizer\ObjectNormalizer {

	public function __construct(ClassMetadataFactoryInterface $classMetadataFactory = null, NameConverterInterface $nameConverter = null, PropertyAccessorInterface $propertyAccessor = null, PropertyTypeExtractorInterface $propertyTypeExtractor = null, ClassDiscriminatorResolverInterface $classDiscriminatorResolver = null)
	{
		parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor, $classDiscriminatorResolver);

		$callback = function ($dateTime) {
			return $dateTime instanceof \DateTime ? $dateTime->format(\DateTime::ISO8601) : '';
		};

		$this->setCallbacks(['releasedAt' => $callback, 'createdAt' => $callback, 'updatedAt' => $callback]);

		$this->setIgnoredAttributes(['__initializer__', '__cloner__', '__isInitialized__']);


		// https://symfony.com/doc/current/components/serializer.html#handling-serialization-depth
		$this->setCircularReferenceLimit(0);

		$this->setCircularReferenceHandler(function ($object) {
			return $object->getId();
		});

	}
}