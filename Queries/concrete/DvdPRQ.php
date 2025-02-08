<?php

namespace ScandiWeb\Queries\concrete;

use ScandiWeb\Models\Concrete\DvdDisc;
use ScandiWeb\Queries\Contract\AbstractPRQ;


class DvdPRQ extends AbstractPRQ
{
    protected function createProductInstance(): DvdDisc
    {
        $dvd = new DvdDisc();
        $dvd->setModelType("Dvd");
        return $dvd;
    }

    protected function setCommonProperties($object, $product): DvdDisc
    {
        $product = parent::setCommonProperties($object, $product);
        $product->setSize($object['size'] ?? $object['Size'] ?? null);
        return $product;
    }
}
