<?php

namespace Simgroep\EventSourcing\Example\Domain;


/**
 * @EventSourcing\AggregateRoot("basketId")
 */
final class Basket
{
    /**
     * @var BasketId
     *
     * @EventSourcing\Apply(BasketPickedUp::basketId)
     */
    private $basketId;

    /**
     * @var ProductId[]
     *
     * @EventSourcing\Apply(ProductAddedToBasket::productId, "add")
     * @EventSourcing\Apply(ProductRemovedFromBasket::productId, "remove")
     */
    private $productIds = [];

    /**
     * @param BasketId $basketId
     *
     * @return Basket
     */
    public static function pickUp(BasketId $basketId)
    {
        yield new Basket;
        yield new BasketPickedUp($basketId);
    }

    /**
     * @param ProductId $productId
     *
     * @return void
     */
    public function addProduct(ProductId $productId)
    {
        if ($this->hasProduct($productId)) {
            return;
        }
        yield new ProductAddedToBasket($this->basketId, $productId);
    }

    /**
     * @param ProductId $productId
     *
     * @return void
     */
    public function removeProduct(ProductId $productId)
    {
        if ( ! $this->hasProduct($productId)) {
            return;
        }
        yield new ProductRemovedFromBasket($this->basketId, $productId);
    }

    /**
     * @param ProductId $productId
     *
     * @return boolean
     */
    protected function hasProduct($productId)
    {
        return in_array($productId, $this->productIds);
    }

    /**
     * Check out the cart and receive an invoice
     *
     * @param InvoiceId $invoiceId
     *
     * @return Invoice
     */
    public function checkout(InvoiceId $invoiceId)
    {
        return Invoice::generate($invoiceId, $this->productIds);
    }
}
