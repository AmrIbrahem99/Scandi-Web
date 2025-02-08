<?php

namespace ScandiWeb\Queries\concrete;


use ScandiWeb\Models\Concrete\Furniture;
use ScandiWeb\Queries\Contract\AbstractPRQ;

class FurniturePRQ extends AbstractPRQ
{
    protected function createProductInstance(): Furniture
    {
        $furniture = new Furniture();
        $furniture->setModelType("Furniture");
        return $furniture;
    }

    protected function setCommonProperties($object, $product): Furniture
    {
        $product = parent::setCommonProperties($object, $product);
        $product->setHeight($object['height'] ?? $object['Height'] ?? null);
        $product->setWidth($object['width'] ?? $object['Width'] ?? null);
        $product->setLength($object['length'] ?? $object['Length'] ?? null);
        return $product;
    }
}