<?php

namespace Simgroep\EventSourcing\Example\Domain;


/**
 * @EventSourcing\PublicEvent
 */
class BasketPickedUp
{
    /**
     * @var BasketId
     */
    private $basketId;

    /**
     * @param BasketId $basketId
     */
    public function __construct(BasketId $basketId)
    {
        $this->basketId = $basketId;
    }

    /**
     * @return BasketId
     */
    public function basketId()
    {
        return $this->basketId;
    }
}
