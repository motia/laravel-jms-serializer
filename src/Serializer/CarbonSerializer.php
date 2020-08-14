<?php

namespace Cone\LaravelJMSSerializer\Serializer;

use Carbon\Carbon;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;

class CarbonSerializer implements SubscribingHandlerInterface
{
  public static function getSubscribingMethods()
  {
    return [
      [
        'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
        'format'    => 'json',
        'type'      => \DateTime::class,
        'method'    => 'serializeDateTime',
      ],
      [
        'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
        'format'    => 'json',
        'type'      => \Illuminate\Support\Carbon::class,
        'method'    => 'serializeCarbon',
      ],
      [
        'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
        'format'    => 'json',
        'type'      => \Carbon\Carbon::class,
        'method'    => 'serializeCarbon',
      ],
    ];
  }

  /**
   * Serialize \DateTime object to ISO to browser format
   *
   * @return String
   */
   public function serializeDateTime(
    JsonSerializationVisitor $visitor,
    \DateTime $dateTime,
    array $type,
    Context $context
  ) {
    return $this->serializeCarbon($visitor, Carbon::instance($dateTime), $type, $context);
  }

  /**
   * Serialize Carbon object to ISO to browser format
   *
   * @return String
   */
  public function serializeCarbon(
    JsonSerializationVisitor $visitor,
    Carbon $carbon,
    array $type,
    Context $context
  ) {
    return $carbon->format(Carbon::ATOM);
  }
}
