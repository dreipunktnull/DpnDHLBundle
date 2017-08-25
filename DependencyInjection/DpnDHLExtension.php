<?php

namespace Dpn\DHLBundle\DependencyInjection;

use Dpn\DHLBundle\Shipment\AuditableBusinessShipmentService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @see http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class DpnDHLExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $def = $container->getDefinition('dpn.dhl.credentials');
        $def->replaceArgument(0, $config['testmode']);

        if (false === $config['testmode']) {
            $def->setMethodCalls([
                ['setEpk', [$config['epk']]],
                ['setUser', [$config['user']]],
                ['setPassword', [$config['password']]],
            ]);
        }

        $def->addMethodCall('setApiUser', [$config['api']['user']]);
        $def->addMethodCall('setApiPassword', [$config['api']['password']]);

        $this->configureShipmentService($config, $container);
        $this->removeDataCollectorIfNotDebug($config, $container);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function configureShipmentService($config, ContainerBuilder $container)
    {
        /*
         * Activates the testmode on the client.
         */
        if (true === $container->has('dpn.dhl.shipment.business_shipment') && true === $config['testmode']) {
            $serviceDefinition = $container->getDefinition('dpn.dhl.shipment.business_shipment');
            $serviceDefinition->addMethodCall('setTestMode', [true]);
        }

        /*
         * If application is in debug mode, modify definition to create a traceable service
         */
        if (true === $container->has('dpn.dhl.shipment.business_shipment') && true === $container->getParameter('kernel.debug')) {
            $serviceDefinition = $container->getDefinition('dpn.dhl.shipment.business_shipment');
            $serviceDefinition->setClass(AuditableBusinessShipmentService::class);
        }
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function removeDataCollectorIfNotDebug($config, ContainerBuilder $container)
    {
        if (false === $container->getParameter('kernel.debug')) {
            $container->removeDefinition('dpn.dhl.data_collector.api');
        }
    }
}
