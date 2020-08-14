<?php

namespace Cone\LaravelJMSSerializer;

use Cone\LaravelJMSSerializer\Commands\ClearSerializerCache;
use Cone\LaravelJMSSerializer\Contracts\DataNormalizer;
use Cone\LaravelJMSSerializer\Contracts\ResponseSerializer;
use Cone\LaravelJMSSerializer\Services\JSendSerializer;
use Cone\LaravelJMSSerializer\Services\JMSDataNormalizer;
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
