<?php

namespace Dpn\DHLBundle\Shipment;

use Petschko\DHL\BusinessShipment;
use Petschko\DHL\Receiver;
use Petschko\DHL\Response;
use Petschko\DHL\Sender;
use Petschko\DHL\ShipmentDetails;

interface BusinessShipmentServiceInterface
{
    /**
     * @param string          $reference
     * @param Sender          $sender
     * @param Receiver        $receiver
     * @param ShipmentDetails $shipmentDetails
     * @param string          $labelResponseType
     *
     * @return BusinessShipment
     */
    public function buildShipment($reference, Sender $sender, Receiver $receiver, ShipmentDetails $shipmentDetails, $labelResponseType);

    /**
     * @param BusinessShipment $shipment
     *
     * @return Response
     */
    public function createShipment(BusinessShipment $shipment);

    /**
     * @param string $shipmentNumber
     *
     * @return Response
     */
    public function cancelShipment($shipmentNumber);

    /**
     * @param string $shipmentNumber
     *
     * @return Response
     */
    public function manifestShipment($shipmentNumber);

    /**
     * @param string $shipmentNumber
     * @param string $type
     */
    public function getLabel($shipmentNumber, $type = BusinessShipment::RESPONSE_TYPE_URL);
}
