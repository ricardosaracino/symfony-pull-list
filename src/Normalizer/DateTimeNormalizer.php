<?php
namespace App\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class DateTimeNormalizer
 * @author Julien Deniau <julien.deniau@mapado.com>
 * @see https://gist.github.com/jdeniau/f3461c92e3376b8906db
 */
class DateTimeNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return $object->format(\DateTime::ISO8601);
    }
    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof \DateTime;
    }
}