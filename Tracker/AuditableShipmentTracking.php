<?php

namespace Dpn\DHLBundle\Tracker;

use DPN\DHLShipmentTracking\ShipmentTracking;

class AuditableShipmentTracking extends ShipmentTracking
{
    /**
     * @var array
     */
    protected $piecesRetrieved = [];

    /**
     * @var array
     */
    protected $piecesDetailRetrieved = [];

    /**
     * @var array
     */
    protected $piecesPublicDetailRetrieved = [];

    /**
     * @return array
     */
    public function getPiecesRetrieved(): array
    {
        return $this->piecesRetrieved;
    }

    /**
     * @return array
     */
    public function getPiecesDetailRetrieved(): array
    {
        return $this->piecesDetailRetrieved;
    }

    /**
     * @return array
     */
    public function getPiecesPublicRetrieved(): array
    {
        return $this->piecesPublicDetailRetrieved;
    }
}
