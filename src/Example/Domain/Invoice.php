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
     */
    private function __construct(InvoiceId $invoiceId, array $productIds)
    {
        yield new InvoiceGenerated($invoiceId, $productIds);
    }

    /**
     * @param InvoiceId $invoiceId
     * @param ProductId[] $productIds
     *
     * @return Invoice
     */
    public static function generate(InvoiceId $invoiceId, array $productIds)
    {
        return new self($invoiceId, $productIds);
    }
}
