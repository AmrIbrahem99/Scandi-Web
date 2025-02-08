<?php

namespace ScandiWeb\Queries\Factory;

use Exception;
use ScandiWeb\Queries\concrete\BookPRQ;
use ScandiWeb\Queries\concrete\DvdPRQ;
use ScandiWeb\Queries\concrete\FurniturePRQ;


class QueriesFactory
{
    public static function createInstance($modelType)
    {
        $classMap = [
            'Book' => BookPRQ::class,
            'Dvd' => DvdPRQ::class,
            'Furniture' => FurniturePRQ::class
        ];

        $className = $classMap[$modelType] ??  new Exception("Unknown model type: $modelType");
        return new $className();
    }
}