<?php

declare(strict_types=1);

namespace Kocal\SymfonyMailerTesting\Bridge\Symfony\DependencyInjection;

use Kocal\SymfonyMailerTesting\Controller\MailerController;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SymfonyMailerTestingExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yaml');

        if (! interface_exists(\Psr\Http\Message\ResponseInterface::class)) {
            $container->removeDefinition(MailerController::class);
        }
    }
}
