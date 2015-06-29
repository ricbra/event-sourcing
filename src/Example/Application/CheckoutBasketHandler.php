<?php

namespace Simgroep\EventSourcing\Example\Application;


class CheckoutBasketHandler extends BasketHandler
{
    public function handle(CheckoutBasket $command)
    {
        $this->invoices->save(
            $this->baskets->load($command->basketId)->checkout()
        );
    }
}
