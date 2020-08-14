<?php

use Cone\LaravelJMSSerializer\Serializer\CarbonSerializer;
use Cone\LaravelJMSSerializer\Serializer\LengthAwarePaginatorSerializer;
use Cone\LaravelJMSSerializer\Serializer\UuidSerializer;

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
