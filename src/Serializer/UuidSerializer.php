<?php

namespace Cone\LaravelJMSSerializer\Serializer;

use Ramsey\Uuid\Uuid;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;

class UuidSerializer implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        return [
            [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format'    => 'json',
                'type'      => Uuid::class,
                'method'    => 'serializeUuid',
            ]
        ];
    }

    /**
     * serializes carbon object to ISO 8601 string for JSON
     * serialization
     *
     * @param XmlSerializationVisitor $visitor
     * @param Uuid $uuid
     * @param array $type
     * @param Context $context
     *
     * @return String
     */
    public function serializeUuid(
        JsonSerializationVisitor $visitor,
        Uuid $uuid,
        array $type,
        Context $context
    ) {
        return $visitor->visitString($uuid->toString(), $type, $context);
    }
}
