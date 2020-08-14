# laravel-jms-serializer
A Library for [serializer](https://jmsyst.com/libs/serializer) intergration with Laravel. 
Useful to serialize doctrine entities or any plain objects in general. 

This package injects into your application two service contracts:
- `DataNormalizer`: converts the data to a php array.
- `ResponseSerializer`: normalizes the data using `DataNormalizer` then builds a `Illuminate\Http\Response` object containting it.

The default implemntation of `ResponseSerializer` follows the [jsend api schema](https://github.com/omniti-labs/jsend)

## Installation 

```
composer require cone/laravel-jms-serializer
```
Register the package service provider by adding it to`config/app.php`
```
  ...
    Cone\LaravelJMSSerializer\SerializerServiceProvider::class 
  ...
```

## Usage:

- Inside Controllers
```php
  // MyController.php
  ...
   public function hello(\Cone\LaravelJMSSerializer\Contracts\ResponseSerializer $serializer) {
     ...
    return $serializer->success($object);
  }
```

- The serializer can also indicate errors and failed requests.
```php
    // return a successful response with status 200
    $serializer->success($data = null, $code= 200, $context = null);
    // return a failure response, for example validation
    $serializer->fail($data = null, $code = 422, $context = null);
    // return an error response, for unexpceted errors 
    $serializer->error($message, $data = null, $code = 500, $context = null);
    // return the normalized array of the php objects; equivalent to `DataNormalizer::normalize()`
    $serializer->normalize($object, $context = null);
```

## Data type serializers
The package supports serializing:
- the Laravel pagination object `LengthAwarePaginator`
`['pagination' => ['total' => 5, 'perPage' => 10... ], 'items' => [...] ]
The property `'items' can be renamed using the serializer context `'itemsKey'` property like that:
```
$context = SerializationContext::create()->setAttribute('itemsKey', 'subscribers');
```

The `'items'` property of the array can be renamed using 
- `Ramsey\Uuid\Uuid`
- `Carbon\Carbon`: serialized to a ISO8086 string readable by all browsers.

