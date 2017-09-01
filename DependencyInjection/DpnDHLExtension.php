<?php

namespace Dpn\DHLBundle\DependencyInjection;

use Dpn\DHLBundle\Shipment\AuditableBusinessShipmentService;
use Dpn\DHLBundle\Tracker\AuditableShipmentTracking;
use DPN\DHLShipmentTracking\Credentials;
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

        $this->configureShipment($config, $container);
        $this->configureTrackAndTrace($config, $container);

        $this->removeDataCollectorIfNotDebug($config, $container);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function configureShipment($config, ContainerBuilder $container)
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
        if (true === $container->has('dpn.dhl.shipment.business_shipment') && $this->applicationInDebugMode($container)) {
            $serviceDefinition = $container->getDefinition('dpn.dhl.shipment.business_shipment');
            $serviceDefinition->setClass(AuditableBusinessShipmentService::class);
        }
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function configureTrackAndTrace($config, ContainerBuilder $container)
    {
        /*
         * Track and trace credentials.
         *
         * When the sandbox is used, rewire the tnt specific credentials to use the sandbox credentials.
         */
        if (true === $container->has('dpn.dhl.tracking.credentials')) {
            $trackingCredentialsDef = $container->getDefinition('dpn.dhl.tracking.credentials');

            $trackingCredentialsDef->setArgument(0, $config['user']);
            $trackingCredentialsDef->setArgument(1, $config['password']);

            if (true === $config['tracking']['use_sandbox']) {
                $trackingCredentialsDef->setArgument(2, Credentials::ENDPOINT_SANDBOX);
                $trackingCredentialsDef->setArgument(3, 'dhl_entwicklerportal');
                $trackingCredentialsDef->setArgument(4, 'Dhl_123!');
            } else {
                $trackingCredentialsDef->setArgument(2, Credentials::ENDPOINT_PRODUCTION);
                $trackingCredentialsDef->setArgument(3, $config['tracking']['user']);
                $trackingCredentialsDef->setArgument(4, $config['tracking']['password']);
            }
        }

        /*
         * If application is in debug mode, modify definition to create a traceable service
         */
        if (
            true === $container->has('dpn.dhl.tracking.shipment_tracking')
            && true === $this->applicationInDebugMode($container)
        ) {
            $serviceDefinition = $container->getDefinition('dpn.dhl.tracking.shipment_tracking');
            $serviceDefinition->setClass(AuditableShipmentTracking::class);
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

    /**
     * @param ContainerBuilder $container
     *
     * @return bool
     */
    private function applicationInDebugMode(ContainerBuilder $container): bool
    {
        return true === $container->getParameter('kernel.debug');
    }
}
