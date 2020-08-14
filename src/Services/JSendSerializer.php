<?php

namespace Cone\LaravelJMSSerializer\Services;

use Illuminate\Http\JsonResponse;
use Cone\LaravelJMSSerializer\Contracts\DataNormalizer;
use Cone\LaravelJMSSerializer\Contracts\ResponseSerializer;

class JSendSerializer implements ResponseSerializer
{
    /**
     * @var DataNormalizer
     */
    private $normalizer;

    public function __construct(DataNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function normalize($object, $context = null) {
        if($object === null) {
            return null;
        }
        return $this->normalizer->normalize($object, $context);
    }

    private function respond($status, $data = null, $message = null, $code = 200, $context = null){
        return new JsonResponse(
            $this->serializeResponse($status, $data, $message, $code, $context),
            $code
        );
    }

    private  function serializeResponse($status, $data = null, $message = null, $code = 200, $context = null){
        $compacts = ['status'];
        if(is_array($data) || $data) {
            $data = $this->normalize($data, $context);
            $compacts[] = 'data';
        }
        else if($status === 'error') {
            if($message === 'null') {
                throw new \BadMethodCallException('JSend: $message is required for error responses!');
            }
            $compacts[] = 'message';
            if($code !== null) {
                $compacts[] = 'code';
            }
        }

        return compact($compacts);
    }

    public function success($data = null, $code = 200, $context = null) {
        return $this->respond('success', $data, null, $code, $context);
    }

    public function fail($data = null, $code = 422, $context = null) {
        return $this->respond('fail', $data, null, $code, $context);
    }

     function error($message, $data = null, $code = 500, $context = null) {
        return $this->respond('error', $data, $message, $code, $context);
    }
}
