<?php

namespace Dpn\DHLBundle\Shipment;

use Petschko\DHL\BusinessShipment;
use Petschko\DHL\Receiver;
use Petschko\DHL\Response;
use Petschko\DHL\Sender;
use Petschko\DHL\ShipmentDetails;
use Symfony\Component\Stopwatch\Stopwatch;

class AuditableBusinessShipmentService extends BusinessShipmentService
{
    /**
     * @var array
     */
    protected $shipmentsCreated = [];

    /**
     * @var array
     */
    protected $shipmentsCanceled = [];

    /**
     * @var array
     */
    protected $shipmentsManifested = [];

    /** {@inheritdoc} */
    public function createShipment($reference, Sender $sender, Receiver $receiver, ShipmentDetails $shipmentDetails, $labelResponseType = BusinessShipment::RESPONSE_TYPE_URL)
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('create');

        $businessShipment = parent::createShipment($reference, $sender, $receiver, $shipmentDetails, $labelResponseType);

        $ev = $stopwatch->stop('create');

        $this->shipmentsCreated[] = [
            'details' => $shipmentDetails,
            'receiver' => $receiver,
            'reference' => $reference,
            'sender' => $sender,
            'labelResponseType' => $labelResponseType,
            'timing' => $ev,
        ];

        return $businessShipment;
    }

    /** {@inheritdoc} */
    public function cancelShipment($shipmentNumber)
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('cancel');

        /** @var Response $response */
        $response = parent::cancelShipment($shipmentNumber);

        $ev = $stopwatch->stop('cancel');

        $this->shipmentsCanceled[] = [
            'number' => $shipmentNumber,
            'response' => $response,
            'timing' => $ev->getDuration(),
        ];

        return $response;
    }

    /** {@inheritdoc} */
    public function manifestShipment($shipmentNumber)
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('manifest');

        $response = parent::manifestShipment($shipmentNumber);

        $ev = $stopwatch->stop('manifest');

        $this->shipmentsManifested[] = [
            'number' => $shipmentNumber,
            'response' => $response,
            'timing' => $ev->getDuration(),
        ];

        return $response;
    }

    /**
     * @return array
     */
    public function getShipmentsCreated(): array
    {
        return $this->shipmentsCreated;
    }

    /**
     * @return array
     */
    public function getShipmentsCanceled(): array
    {
        return $this->shipmentsCanceled;
    }

    /**
     * @return array
     */
    public function getShipmentsManifested(): array
    {
        return $this->shipmentsManifested;
    }
}
