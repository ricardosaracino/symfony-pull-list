<?php

namespace App\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ConstraintViolationNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $a = [];

        /** @var ConstraintViolation $constraintViolation */
        foreach ($object as $constraintViolation) {
            $a [] = ['message' => $constraintViolation->getMessage(), 'property' => $constraintViolation->getPropertyPath()];
        }

        return $a;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof ConstraintViolationList;
    }
}