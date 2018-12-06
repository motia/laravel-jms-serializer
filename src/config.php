<?php

use Motia\LaravelJMSSerializer\Serializer\CarbonSerializer;
use Motia\LaravelJMSSerializer\Serializer\LengthAwarePaginatorSerializer;
use Motia\LaravelJMSSerializer\Serializer\UuidSerializer;

return [
    'debug' => false,
    'metadata' => [
        'path' => app_path('Entities'),
        'namespace' => 'App\\Entities'
    ],
    'cacheDir' => storage_path('app/cache/serializer'),
    'serializers' => [
        LengthAwarePaginatorSerializer::class,
        CarbonSerializer::class,
        UuidSerializer::class,
    ]
];
