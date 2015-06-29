<?php

namespace Simgroep\EventSourcing\Example\Domain;


/**
 * @EventSourcing\PublicEvent
 */
class InvoiceGenerated
{
    /**
     * @var InvoiceId
     */
    private $invoiceId;

    /**
     * @var ProductId[]
     */
    private $productIds;

    /**
     * @param InvoiceId $invoiceId
     * @param ProductId[] $productIds
     */
    public function __construct(InvoiceId $invoiceId, array $productIds)
    {
        $this->invoiceId = $invoiceId;
        $this->productIds = $productIds;
    }

    /**
     * @return InvoiceId
     */
    public function invoiceId()
    {
        return $this->invoiceId;
    }

    /**
     * @return ProductId[]
     */
    public function productIds()
    {
        return $this->productIds;
    }
}
