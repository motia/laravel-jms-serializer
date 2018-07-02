<?php

namespace Motia\LaravelJMSSerializer\Serializer;


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

        $shouldSetRoot = null === $visitor->getRoot();

        $itemsKey = $context->attributes->get('itemsKey')->getOrElse('items');
        $data = array(
            'pagination' => [
                'currentPage' => $paginator->currentPage(),
                'perPage' => $paginator->perPage(),
                'total' => $paginator->total(),
                'lastPage' => $paginator->lastPage(),
            ],
            $itemsKey => $visitor->getNavigator()->accept($paginator->items(), $resultsType, $context),
        );

        if ($shouldSetRoot) {
            $visitor->setRoot($data);
        }
    }
}
