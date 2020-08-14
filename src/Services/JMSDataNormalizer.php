<?php

namespace Cone\LaravelJMSSerializer\Services;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Handler\HandlerRegistry;
use Cone\LaravelJMSSerializer\Contracts\DataNormalizer;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class JMSDataNormalizer implements DataNormalizer
{
    /**
     * @var Serializer
     */
    private $serializer;
    private $cacheDir;

    public function __construct($config)
    {
        $this->cacheDir = $config['cacheDir'];

        $this->serializer = SerializerBuilder::create()
            ->addMetadataDir(
                $config['metadata']['path'],
                $config['metadata']['namespace']
            )
            ->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy())
            ->setCacheDir(
                $config['cacheDir']
            )
            ->setDebug($config['debug'])
            ->addDefaultHandlers()
            ->configureHandlers(function (HandlerRegistry $registry) use ($config) {
                foreach ($config['serializers'] as $serializer) {
                    $registry->registerSubscribingHandler(new $serializer());
                }
            })
            ->setExpressionEvaluator(new ExpressionEvaluator(new ExpressionLanguage()))
            ->build();
    }

    function normalize($data, $context = null)
    {
        $context = $context ? $context : $this->defaultContext();
        return $this->serializer->toArray($data, $context);
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        return $this->cacheDir;
    }

    public function defaultContext (): SerializationContext{
        return SerializationContext::create()->setGroups(['Default']);
    }
}
