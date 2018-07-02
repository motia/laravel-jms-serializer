<?php

namespace Motia\LaravelJMSSerializer\Serializer;

use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\VisitorInterface;
use JMS\Serializer\XmlDeserializationVisitor;
use JMS\Serializer\XmlSerializationVisitor;
use JMS\Serializer\Context;
use Carbon\Carbon;
use DateTimeImmutable;
use DateTimeZone;
use DateTime;

/**
 * @licence GNU General Public License v2.0
 * @author autumndev <sayhi@autumndev.co.uk>
 * @link https://github.com/autumndev/jms-serializer-carbon
 */
class CarbonSerializer implements SubscribingHandlerInterface
{
    private $defaultFormat;
    private $defaultTimezone;
    private $xmlCData;

    public static function getSubscribingMethods()
    {
        $methods = array();
        $deserialisationTypes = array(
          Carbon::class,
          \Illuminate\Support\Carbon::class
        );
        $serialisationTypes = array(
          Carbon::class,
          \Illuminate\Support\Carbon::class
        );

        foreach (['json', 'xml', 'yml'] as $format) {

            foreach ($deserialisationTypes as $type) {
                $methods[] = [
                    'type'      => $type,
                    'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                    'format'    => $format,
                ];
            }

            foreach ($serialisationTypes as $type) {
                $key = 'carbon';
                $methods[] = array(
                    'type' => $type,
                    'format' => $format,
                    'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                    'method' => 'serialize'.ucfirst($key),
                );
            }
        }

        return $methods;
    }

    public function __construct($defaultFormat = Carbon::ISO8601, $defaultTimezone = 'UTC', $xmlCData = true)
    {
        $this->defaultFormat = $defaultFormat;
        $this->defaultTimezone = new DateTimeZone($defaultTimezone);
        $this->xmlCData = $xmlCData;
    }

    /**
     * serializes carbon object to ISO 8601 string for JSON
     * serialization
     *
     * @param XmlSerializationVisitor $visitor
     * @param Carbon $date
     * @param array $type
     * @param Context $context
     *
     * @return String
     */
    public function serializeCarbon(
        VisitorInterface $visitor,
        Carbon $date,
        array $type,
        Context $context
    ) {
        if ($visitor instanceof XmlSerializationVisitor && false === $this->xmlCData) {
            return $visitor->visitSimpleString($date->format($this->getFormat($type)), $type, $context);
        }

        $format = $this->getFormat($type);
        if ('U' === $format) {
            return $visitor->visitInteger($date->format($format), $type, $context);
        }

        return $visitor->visitString($date->format($this->getFormat($type)), $type, $context);
    }

    public function deserializeCarbonFromXml(XmlDeserializationVisitor $visitor, $data, array $type)
    {
        if ($this->isDataXmlNull($data)) {
            return null;
        }

        $dateObj = $this->parseDateTime($data, $type);
        return Carbon::instance($dateObj);
    }

    public function deserializeCarbonFromJson(JsonDeserializationVisitor $visitor, $data, array $type)
    {
        if ($this->isDataXmlNull($data)) {
            return null;
        }

        $dateObj = $this->parseDateTime($data, $type);
        return Carbon::instance($dateObj);
    }

    private function parseDateTime($data, array $type, $immutable = false)
    {
        $timezone = isset($type['params'][1]) ? new DateTimeZone($type['params'][1]) : $this->defaultTimezone;
        $format = $this->getFormat($type);

        if ($immutable) {
            $datetime = DateTimeImmutable::createFromFormat($format, (string) $data, $timezone);
        } else {
            $datetime = DateTime::createFromFormat($format, (string) $data, $timezone);
        }

        if (false === $datetime) {
            throw new \RuntimeException(sprintf('Invalid datetime "%s", expected format %s.', $data, $format));
        }

        return $datetime;
    }

    /**
     * @return string
     * @param array $type
     */
    private function getFormat(array $type)
    {
        return isset($type['params'][0]) ? $type['params'][0] : $this->defaultFormat;
    }
}
