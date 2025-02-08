<?php

namespace ScandiWeb\Queries\concrete;

use ScandiWeb\Models\Concrete\Book;
use ScandiWeb\Queries\Contract\AbstractPRQ;


class BookPRQ extends AbstractPRQ
{
    protected function createProductInstance(): Book
    {
        $book = new Book();
        $book->setModelType("Book");
        return $book;
    }

    protected function setCommonProperties($object, $product): Book
    {
        $product = parent::setCommonProperties($object, $product);
        $product->setWeight($object['weight'] ?? $object['Weight'] ?? null);
        return $product;
    }
}
