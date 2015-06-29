<?php

namespace Simgroep\EventSourcing\Example\Application;


class AddProductToBasket
{
    /**
     * @var string
     */
    public $basketId;

    /**
     * @var string
     */
    public $productId;

    /**
     * @param string $basketId
     * @param string $productId
     */
    public function __construct($basketId, $productId)
    {
        $this->basketId = (string) $basketId;
        $this->productId = (string) $productId;
    }
}
