<?php

namespace Simgroep\EventSourcing\Example\Application;


class PickUpBasket
{
    public $basketId;
    public $productId;

    public function __construct($basketId, $productId)
    {
        $this->basketId = $basketId;
        $this->productId = $productId;
    }
}