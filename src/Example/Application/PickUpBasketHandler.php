<?php

namespace Simgroep\EventSourcing\Example\Application;


use Simgroep\EventSourcing\Example\Domain\Basket;

final class PickUpBasketHandler extends BasketHandler
{
    public function handle(PickUpBasket $command)
    {
        $this->baskets->save(Basket::pickUp($command->basketId));
    }
}