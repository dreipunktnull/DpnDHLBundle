<?php

namespace Dpn\DHLBundle\Shipment;

use Petschko\DHL\BusinessShipment;
use Petschko\DHL\Credentials;
use Petschko\DHL\Receiver;
use Petschko\DHL\Response;
use Petschko\DHL\Sender;
use Petschko\DHL\Service;
use Petschko\DHL\ShipmentDetails;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BusinessShipmentService implements BusinessShipmentServiceInterface
{
    /**
     * @var Credentials
     */
    protected $dhlCredentials;

    /**
     * @var bool
     */
    protected $testMode;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher, Credentials $dhlCredentials)
    {
        $this->dhlCredentials = $dhlCredentials;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return bool
     */
    public function isTestMode()
    {
        return $this->testMode;
    }

    /**
     * @param bool $testMode
     */
    public function setTestMode($testMode)
    {
        $this->testMode = $testMode;
    }

    /**
     * {@inheritdoc}
     */
    public function buildShipment($reference, Sender $sender, Receiver $receiver, ShipmentDetails $shipmentDetails, $labelResponseType = BusinessShipment::RESPONSE_TYPE_URL)
    {
        $dhl = new BusinessShipment($this->dhlCredentials, $this->testMode, '2.2');
        $dhl->setCustomAPIURL('https://cig.dhl.de/cig-wsdls/com/dpdhl/wsdl/geschaeftskundenversand-api/2.2/geschaeftskundenversand-api-2.2.wsdl');

        $service = new Service();

        $dhl->setSequenceNumber($reference); // Just needed for ajax or such stuff can dynamic an other value
        $dhl->setSender($sender);
        $dhl->setReceiver($receiver); // You can set these Object-Types here: DHL_Filial, DHL_Receiver & DHL_PackStation
        //$dhl->setReturnReceiver($returnReceiver); // Needed if you want print a return label
        $dhl->setService($service);
        $dhl->setShipmentDetails($shipmentDetails);
        //$dhl->setReceiverEmail('receiver@mail.com'); // Needed if you want inform the receiver via mail
        $dhl->setLabelResponseType($labelResponseType);

        return $dhl;
    }

    /** {@inheritdoc} */
    public function createShipment(BusinessShipment $shipment)
    {
        return $shipment->createShipment();
    }

    /**
     * @param string $shipmentNumber
     *
     * @return Response
     */
    public function manifestShipment($shipmentNumber)
    {
        $dhl = new BusinessShipment($this->dhlCredentials, $this->testMode, '2.2');
        $dhl->setCustomAPIURL('https://cig.dhl.de/cig-wsdls/com/dpdhl/wsdl/geschaeftskundenversand-api/2.2/geschaeftskundenversand-api-2.2.wsdl');

        return $dhl->doManifest($shipmentNumber);
    }

    /**
     * @param $shipmentNumber
     *
     * @return Response
     */
    public function cancelShipment($shipmentNumber)
    {
        $dhl = new BusinessShipment($this->dhlCredentials, $this->testMode, '2.2');
        $dhl->setCustomAPIURL('https://cig.dhl.de/cig-wsdls/com/dpdhl/wsdl/geschaeftskundenversand-api/2.2/geschaeftskundenversand-api-2.2.wsdl');

        return $dhl->deleteShipment($shipmentNumber);
    }

    /**
     * Queries the API for a label for a.
     *
     * @param string $shipmentNumber
     *
     * @return null|string
     */
    public function getLabel($shipmentNumber)
    {
        $dhl = new BusinessShipment($this->dhlCredentials, $this->testMode, '2.2');
        $dhl->setCustomAPIURL('https://cig.dhl.de/cig-wsdls/com/dpdhl/wsdl/geschaeftskundenversand-api/2.2/geschaeftskundenversand-api-2.2.wsdl');
        $dhl->setLabelResponseType(BusinessShipment::RESPONSE_TYPE_URL);

        $response = $dhl->getShipmentLabel($shipmentNumber);

        return $response->getLabel();
    }
}
