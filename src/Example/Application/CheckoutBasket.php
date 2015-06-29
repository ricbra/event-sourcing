<?php

namespace Simgroep\EventSourcing\Example\Application;


class CheckoutBasket
{
    /**
     * @var string
     */
    public $basketId;

    /**
     * @var string
     */
    public $invoiceId;

    /**
     * @param string $basketId
     * @param string $invoiceId
     */
    public function __construct($basketId, $invoiceId)
    {
        $this->basketId = (string) $basketId;
        $this->invoiceId = (string) $invoiceId;
    }
}
