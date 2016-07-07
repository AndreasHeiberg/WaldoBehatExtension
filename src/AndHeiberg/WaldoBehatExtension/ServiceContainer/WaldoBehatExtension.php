<?php

namespace AndHeiberg\WaldoBehatExtension\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use GuzzleHttp\Client;
use League\Flysystem\Adapter\Local;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class WaldoBehatExtension implements ExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigKey()
    {
        return 'waldo';
    }

    /**
     * {@inheritDoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->arrayNode('filesystem')->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('driver')->defaultValue('local')->end()
                        ->scalarNode('root')->defaultValue('waldo')->end()
                        ->scalarNode('key')->defaultNull()->end()
                        ->scalarNode('secret')->defaultNull()->end()
                        ->scalarNode('region')->defaultNull()->end()
                        ->scalarNode('bucket')->defaultNull()->end()
                    ->end()
                ->end()
                ->arrayNode('screenshotter')->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('driver')->defaultValue('filesystem')->end()
                        ->scalarNode('server')->defaultNull()->end()
                    ->end()
                ->end()
                ->arrayNode('screenshot_comparer')->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('driver')->defaultValue('filesystem')->end()
                        ->scalarNode('fuzz')->defaultValue(20)->end()
                        ->scalarNode('metric')->defaultValue('AE')->end()
                        ->scalarNode('highlight_color')->defaultValue('red')->end()
                        ->scalarNode('server')->defaultNull()->end()
                    ->end()
                ->end()
                ->booleanNode('fail_on_diff')->defaultValue(true)->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {

    }

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {

    }

    /**
     * {@inheritDoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        if ($config['filesystem']) {
            $this->loadFilesystem($container, $config);
        }

        $this->loadScreenshotter($container, $config);
        $this->loadScreenshotComparer($container, $config);
        
        $container->setDefinition('waldo', new Definition('AndHeiberg\WaldoBehatExtension\Waldo', [
            new Reference('waldo.screenshotter'),
            new Reference('waldo.screenshot_comparer')
        ]));

        $this->loadContextInitializer($container);
    }

    private function loadContextInitializer(ContainerBuilder $container)
    {
        $definition = new Definition('AndHeiberg\WaldoBehatExtension\Context\Initializer\WaldoAwareInitializer', [
            new Reference('waldo')
        ]);
        $definition->addTag(ContextExtension::INITIALIZER_TAG, ['priority' => 0]);
        $container->setDefinition('waldo.context_initializer', $definition);
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     *
     * @throws \Exception
     */
    private function loadFilesystem(ContainerBuilder $container, array $config)
    {
        switch ($config['filesystem']['driver']) {
            case 'local':
                $filesystemDriver = new Local(__DIR__ . '/../../../../../../../' . $config['filesystem']['root']);
                break;
            default:
                throw new \Exception('Filesystem Driver not Supported');
        }

        $container->setDefinition('waldo.filesystem', new Definition('League\Flysystem\Filesystem', [
            $filesystemDriver
        ]));
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     *
     * @throws \Exception
     */
    private function loadScreenshotter(ContainerBuilder $container, array $config)
    {
        switch ($config['screenshotter']['driver']) {
            case 'filesystem':
                $container->setDefinition('waldo.screenshotter', new Definition('AndHeiberg\WaldoBehatExtension\Screenshotter\FilesystemScreenshotter', [
                    new Reference('waldo.filesystem')
                ]));
                break;
            case 'waldo_server':
                $container->setDefinition('waldo.screenshotter', new Definition('AndHeiberg\WaldoBehatExtension\Screenshotter\WaldoServerScreenshotter', [
                    new Client,
                    $config['screenshotter']['server']
                ]));
                break;
            default:
                throw new \Exception('Screenshot driver not supported.');
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     *
     * @throws \Exception
     */
    private function loadScreenshotComparer(ContainerBuilder $container, array $config)
    {
        switch ($config['screenshot_comparer']['driver']) {
            case 'filesystem':
                $container->setDefinition('waldo.screenshot_comparer', new Definition('AndHeiberg\WaldoBehatExtension\Comparer\FilesystemScreenshotComparer', [
                    new Reference('waldo.filesystem')
                ]));
                break;
            case 'waldo_server':
                $container->setDefinition('waldo.screenshot_comparer', new Definition('AndHeiberg\WaldoBehatExtension\Comparer\WaldoServerScreenshotComparer', [
                    new Client,
                    $config['screenshot_comparer']['server']
                ]));
                break;
            default:
                throw new \Exception('Screenshot driver not supported.');
        }
    }
}
