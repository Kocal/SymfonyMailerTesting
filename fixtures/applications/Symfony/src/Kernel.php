<?php

declare(strict_types=1);

namespace Kocal\SymfonyMailerTesting\Fixtures\Applications\Symfony\App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

if (BaseKernel::MAJOR_VERSION >= 6) {
    abstract class AbstractKernel extends BaseKernel
    {
        use MicroKernelTrait;

        protected const CONFIG_EXTS = '.{php,xml,yaml,yml}';

        protected function configureContainerSpecificSymfonyVersion(ContainerBuilder $container, LoaderInterface $loader)
        {
            $confDir = $this->getProjectDir() . '/config';

            $loader->load($confDir . '/{packages_symfony6+}/*' . self::CONFIG_EXTS, 'glob');
            $loader->load($confDir . '/{packages_symfony6+}/' . $this->environment . '/*' . self::CONFIG_EXTS, 'glob');
        }
    }
} else {
    abstract class AbstractKernel extends BaseKernel
    {
        use MicroKernelTrait;

        protected const CONFIG_EXTS = '.{php,xml,yaml,yml}';

        public function configureRoutes(\Symfony\Component\Routing\RouteCollectionBuilder $routes)
        {
            $confDir = $this->getProjectDir() . '/config';

            $routes->import($confDir . '/{routes}/' . $this->environment . '/*' . self::CONFIG_EXTS, '/', 'glob');
            $routes->import($confDir . '/{routes}/*' . self::CONFIG_EXTS, '/', 'glob');
            $routes->import($confDir . '/{routes}' . self::CONFIG_EXTS, '/', 'glob');
        }

        protected function configureContainerSpecificSymfonyVersion(ContainerBuilder $container, LoaderInterface $loader)
        {
            $confDir = $this->getProjectDir() . '/config';

            $loader->load($confDir . '/{packages_symfony5-}/*' . self::CONFIG_EXTS, 'glob');
            $loader->load($confDir . '/{packages_symfony5-}/' . $this->environment . '/*' . self::CONFIG_EXTS, 'glob');
        }
    }
}

class Kernel extends AbstractKernel
{
    public function registerBundles(): iterable
    {
        $contents = require $this->getProjectDir() . '/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }

    public function getProjectDir(): string
    {
        return \dirname(__DIR__);
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->addResource(new FileResource($this->getProjectDir() . '/config/bundles.php'));
        $container->setParameter('container.dumper.inline_class_loader', \PHP_VERSION_ID < 70400 || $this->debug);
        $container->setParameter('container.dumper.inline_factories', true);
        $confDir = $this->getProjectDir() . '/config';

        $loader->load($confDir . '/{packages}/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{packages}/' . $this->environment . '/*' . self::CONFIG_EXTS, 'glob');
        $this->configureContainerSpecificSymfonyVersion($container, $loader);
        $loader->load($confDir . '/{services}' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{services}_' . $this->environment . self::CONFIG_EXTS, 'glob');
    }
}
