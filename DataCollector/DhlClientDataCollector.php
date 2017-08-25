<?php

namespace Dpn\DHLBundle\DataCollector;

use Dpn\DHLBundle\Shipment\AuditableBusinessShipmentService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class DhlClientDataCollector extends DataCollector
{
    /**
     * @var AuditableBusinessShipmentService
     */
    private $shipmentService;

    /**
     * @param AuditableBusinessShipmentService $shipmentService
     */
    public function __construct(AuditableBusinessShipmentService $shipmentService)
    {
        $this->shipmentService = $shipmentService;
    }

    /**
     * Collects data for the given Request and Response.
     *
     * @param Request    $request   A Request instance
     * @param Response   $response  A Response instance
     * @param \Exception $exception An Exception instance
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data['created'] = $this->shipmentService->getShipmentsCreated();
        $this->data['canceled'] = $this->shipmentService->getShipmentsCanceled();
        $this->data['manifested'] = $this->shipmentService->getShipmentsManifested();

        $this->data['actions'] = (int) (count($this->data['created']) + count($this->data['canceled']) + count($this->data['manifested']));
    }

    /**
     * Returns the name of the collector.
     *
     * @return string The collector name
     */
    public function getName()
    {
        return 'dpn_dhl.api_collector';
    }

    /**
     * Returns the number of interactions with the API.
     *
     * @return int
     */
    public function getActions()
    {
        return $this->data['actions'];
    }

    /**
     * @return array
     */
    public function getCreated()
    {
        return $this->data['created'];
    }

    /**
     * @return array
     */
    public function getManifested()
    {
        return $this->data['manifested'];
    }

    /**
     * @return array
     */
    public function getCanceled()
    {
        return $this->data['canceled'];
    }
}
