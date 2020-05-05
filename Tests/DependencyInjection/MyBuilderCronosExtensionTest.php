<?php

namespace MyBuilder\Bundle\CronosBundle\Tests\DependencyInjection;

use MyBuilder\Bundle\CronosBundle\DependencyInjection\MyBuilderCronosExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;

class MyBuilderCronosExtensionTest extends TestCase
{
    /** @var MyBuilderCronosExtension */
    private $loader;

    /** @var ContainerBuilder */
    private $container;

    protected function setUp(): void
    {
        $this->container = new ContainerBuilder();
        $this->loader = new MyBuilderCronosExtension();
    }

    /**
     * @dataProvider providerTestConfig
     */
    public function test_config(array $expected, string $file): void
    {
        $this->loader->load($this->getConfig($file), $this->container);

        static::assertEquals($expected, $this->container->getParameter('mybuilder.cronos_bundle.exporter_config'));
    }

    public function providerTestConfig(): array
    {
        return [
            [
                [
                    'executor' => 'php',
                    'console' => '%kernel.root_dir%/../bin/console',
                ],
                'empty.yml',
            ],
            [
                [
                    'key' => 'test',
                    'mailto' => 'config-test@example.com',
                    'path' => '/bin:/usr/local/bin',
                    'executor' => 'php',
                    'console' => 'bin/console',
                    'shell' => '/bin/bash',
                ],
                'full.yml',
            ],
        ];
    }

    /**
     * Load the specified yaml config file.
     */
    private function getConfig(string $fileName): array
    {
        $locator = new FileLocator(__DIR__ . '/config');
        $file = $locator->locate($fileName, null, true);

        $config = Yaml::parse(file_get_contents($file));

        if (null === $config) {
            return [];
        }

        return $config;
    }
}
