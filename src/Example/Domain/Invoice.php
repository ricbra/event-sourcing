<?php

namespace Simgroep\EventSourcing\Example\Domain;


/**
 * @EventSourcing\AggregateRoot("invoiceId")
 */
final class Invoice
{
    /**
     * @var InvoiceId
     *
     * @EventSourcing\Apply(InvoiceGenerated::invoiceId)
     */
    private $invoiceId;

    /**
     * @var ProductId[]
     *
     * @EventSourcing\Apply(InvoiceGenerated::productIds)
     */
    private $productIds;

    /**
     * @param InvoiceId $invoiceId
     * @param ProductId[] $productIds
     *
     * @return Invoice
     */
    public static function generate(InvoiceId $invoiceId, array $productIds)
    {
        yield new Invoice;
        yield new InvoiceGenerated($invoiceId, $productIds);
    }
}
