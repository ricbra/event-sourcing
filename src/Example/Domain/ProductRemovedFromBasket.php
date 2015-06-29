<?php

namespace Simgroep\EventSourcing\Example\Domain;


/**
 * @EventSourcing\PublicEvent
 */
class ProductRemovedFromBasket
{
    /**
     * @var BasketId
     */
    private $basketId;

    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @param BasketId  $basketId
     * @param ProductId $productId
     */
    public function __construct(BasketId $basketId, ProductId $productId)
    {
        $this->basketId = $basketId;
        $this->productId = $productId;
    }

    /**
     * @return BasketId
     */
    public function basketId()
    {
        return $this->basketId;
    }

    /**
     * @return ProductId
     */
    public function productId()
    {
        return $this->productId;
    }
}
