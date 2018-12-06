<?php

namespace Motia\LaravelJMSSerializer;

use Motia\LaravelJMSSerializer\Commands\ClearSerializerCache;
use Motia\LaravelJMSSerializer\Contracts\DataNormalizer;
use Motia\LaravelJMSSerializer\Contracts\ResponseSerializer;
use Motia\LaravelJMSSerializer\Services\JSendSerializer;
use Motia\LaravelJMSSerializer\Services\JMSDataNormalizer;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class SerializerServiceProvider extends ServiceProvider
{
  private $configKey = 'serializer';

  public function boot()
  {
    $this->publishes([
      $this->getDefaultConfigFilePath()  => config_path($this->configKey),
    ]);
  }

  public function register()
  {
    $this->mergeConfigFrom(
      $this->getDefaultConfigFilePath(), $this->configKey
    );

    $this->commands([
      ClearSerializerCache::class
    ]);
    AnnotationRegistry::registerLoader('class_exists');
//        AnnotationRegistry::registerAutoloadNamespace(
//            'JMS\Serializer\Annotation',
//            base_path('vendor/jms/serializer/src'));

    $this->app->singleton(DataNormalizer::class, function (Application $app) {
      /** @var \Illuminate\Config\Repository $config */
      $config = $app->make('Illuminate\Config\Repository');
      return new JMSDataNormalizer($config->get('serializer'));
    });

    $this->app->bind(ResponseSerializer::class, JSendSerializer::class);
  }

  private function getDefaultConfigFilePath()
  {
    return __DIR__ . '/config.php';
  }
}
