
# Jefero Bot Library

Библиотека для удобного создания ботов для множества платформ


## Installation

Install bot common with composer

```bash
  composer require jefero/bot_common
```

## Configuring

```php
class Kernel extends BaseKernel
{
    //Path to library
    private const COMMON_PATH = 'vendor/jefero/bot_common';

    use MicroKernelTrait;

    //Import bot containers
    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../' . self::COMMON_PATH . '/config/{packages}/*.yaml');
        $container->import('../' . self::COMMON_PATH . '/config/{packages}/' . (string)$this->environment . '/*.yaml');
        $container->import('../' . self::COMMON_PATH . '/config/services.yaml');
        $container->import('../' . self::COMMON_PATH . '/config/{services}_' . (string)$this->environment . '.yaml');

        //...other settings
    }

    //Import bot routes
    protected function configureRoutes(RoutingConfigurator $routes): void
    {

        $routes->import('../' . self::COMMON_PATH . '/config/{routes}/' . (string)$this->environment . '/*.yaml');
        $routes->import('../' . self::COMMON_PATH . '/config/{routes}/*.yaml');

        //...other settings
    }

    //Add method for register bot bundles
    public function registerBundles(): iterable
    {
        /** @psalm-suppress UnresolvableInclude */
        $commonBundles = require $this->getProjectDir() . '/' . self::COMMON_PATH . '/config/bundles.php';
        /** @psalm-suppress UnresolvableInclude */
        $currentBundles = require $this->getProjectDir() . '/config/bundles.php';
        $bundles = array_merge($commonBundles, $currentBundles);

        foreach ($bundles as $class => $envs) {
            /** @psalm-suppress UndefinedClass */
            yield new $class();
        }
    }
}

```

Add jefero_common_dir parameter to your services.yaml


```php
parameters:
    jefero_common_dir: '%kernel.project_dir%/vendor/jefero/bot_common'
    #...other params
```