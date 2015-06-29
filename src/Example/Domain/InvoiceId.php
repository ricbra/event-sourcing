<?php

namespace Simgroep\EventSourcing\Example\Domain;


/**
 * @EventSourcing\Id
 */
class InvoiceId
{
    /**
     * @var string
     */
    private $id;

    /**
     * @param string $id
     */
    public function __construct($id)
    {
        $this->id = (string) $id;
    }
}
