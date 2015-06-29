<?php

namespace Simgroep\EventSourcing\Example\Application;


abstract class BasketHandler
{
    protected $baskets;
    protected $invoices;

    public function __construct(Repository $baskets, Repository $invoices)
    {
        $this->baskets = $baskets;
        $this->invoices = $invoices;
    }
}