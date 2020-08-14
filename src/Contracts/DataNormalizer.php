<?php

namespace Cone\LaravelJMSSerializer\Contracts;

use JMS\Serializer\SerializationContext;

interface DataNormalizer
{
    /**
     * @param $data
     * @param SerializationContext
     * @return array
     */
    function normalize($data, $context = null);

    /**
     * @return string
     */
    function getCacheDir();
}
