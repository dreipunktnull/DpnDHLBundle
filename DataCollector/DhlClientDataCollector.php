<?php

namespace Dpn\DHLBundle\DataCollector;

use Dpn\DHLBundle\Shipment\AuditableBusinessShipmentService;
use Dpn\DHLBundle\Tracker\AuditableShipmentTracking;
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
     * @var AuditableShipmentTracking
     */
    private $tracking;

    /**
     * @param AuditableBusinessShipmentService $shipmentService
     * @param AuditableShipmentTracking        $tracking
     */
    public function __construct(AuditableBusinessShipmentService $shipmentService, AuditableShipmentTracking $tracking)
    {
        $this->shipmentService = $shipmentService;
        $this->tracking = $tracking;
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
        $this->data['built'] = $this->shipmentService->getShipmentsBuilt();
        $this->data['created'] = $this->shipmentService->getShipmentsCreated();
        $this->data['canceled'] = $this->shipmentService->getShipmentsCanceled();
        $this->data['manifested'] = $this->shipmentService->getShipmentsManifested();
        $this->data['trackings_getpiece'] = $this->tracking->getPiecesRetrieved();
        $this->data['trackings_getpiecedetail'] = $this->tracking->getPiecesDetailRetrieved();
        $this->data['trackings_getpiecepublic'] = $this->tracking->getPiecesPublicRetrieved();

        $this->data['actions'] = (
            count($this->data['built'])
            + count($this->data['created'])
            + count($this->data['canceled'])
            + count($this->data['manifested'])
            + count($this->data['trackings_getpiece'])
            + count($this->data['trackings_getpiecedetail'])
            + count($this->data['trackings_getpiecepublic'])
        );
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
    public function getBuilt()
    {
        return $this->data['built'];
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

    /**
     * @return array
     */
    public function getTrackingsGetPiece()
    {
        return $this->data['trackings_getpiece'];
    }

    /**
     * @return array
     */
    public function getTrackingsGetPieceDetail()
    {
        return $this->data['trackings_getpiecedetail'];
    }

    /**
     * @return array
     */
    public function getTrackingsGetPiecePublic()
    {
        return $this->data['trackings_getpiecepublic'];
    }
}
