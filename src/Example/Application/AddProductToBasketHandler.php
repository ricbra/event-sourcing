<?php

namespace Simgroep\EventSourcing\Example\Application;


final class AddProductToBasketHandler extends BasketHandler
{
    public function handle(AddProductToBasket $command)
    {
        $this->baskets->save(
            $this->baskets->load($command->basketId)->addProduct(
                $command->productId
            )
        );
    }
}
