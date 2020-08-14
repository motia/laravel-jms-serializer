<?php

namespace Cone\LaravelJMSSerializer\Serializer;


use Illuminate\Pagination\LengthAwarePaginator;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;

class LengthAwarePaginatorSerializer implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        return [
            [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format'    => 'json',
                'type'      => LengthAwarePaginator::class,
                'method'    => 'serializePaginator',
            ]
        ];
    }

    public function serializePaginator(
            JsonSerializationVisitor $visitor,
            LengthAwarePaginator $paginator,
            array $type,
            Context $context
    ) {
        $resultsType = array(
            'name' => 'array',
        );

        if (isset($type['params'])) {
            $resultsType['params'] = $type['params'];
        }

        $itemsKey = $context->hasAttribute('itemsKey') ? $context->getAttribute('itemsKey') : 'items';
        return array(
            'pagination' => [
                'currentPage' => intval($paginator->currentPage()),
                'perPage' => intval($paginator->perPage()),
                'total' => intval($paginator->total()),
                'lastPage' => $paginator->lastPage(),
            ],
            $itemsKey => $visitor->visitArray($paginator->items(), $resultsType),
        );
    }
}
