<?php
declare(strict_types=1);

namespace Digitalnoise\CommandLauncherBundle\DependencyInjection;

use Digitalnoise\CommandLauncher\ParameterResolver;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class CommandLauncherExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../../config')
        );
        $container->registerForAutoconfiguration(ParameterResolver::class)
            ->addTag('digitalnoise.parameter_resolver');
        $loader->load('services.yml');
    }
}
