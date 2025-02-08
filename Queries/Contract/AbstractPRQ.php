<?php

namespace ScandiWeb\Queries\Contract;

use Exception;
use ReflectionClass;
use ScandiWeb\Queries\Factory\QueriesFactory;

abstract class AbstractPRQ implements PRQ
{
    protected abstract function createProductInstance(): object;

    protected function setCommonProperties($object, $product)
    {
        $product->setSKU($object['sku'] ?? $object['SKU'] ?? null);
        $product->setName($object['name'] ?? $object['Name'] ?? null);
        $product->setPrice($object['price'] ?? $object['Price'] ?? null);
        return $product;
    }

    public function InsertQuery($object, $connection)
    {
        $product = $this->createProductInstance();
        $this->setCommonProperties($object, $product);
        $insertMethod = 'insert' . (new ReflectionClass($product))->getShortName();
        $product->$insertMethod($connection);
    }

    /**
     * @throws Exception
     */
    public function SelectQuery($object, $connection): array
    {
        $modelId = $object['ModelId'];
        $modelType = (new ReflectionClass($this->createProductInstance()))->getShortName();
        $modelQuery = "SELECT * FROM `" .($modelType) . "` WHERE Id = $modelId";
        $modelResult = $connection->query($modelQuery);
        $modelRow = $modelResult->fetch_assoc();
        $product = array_merge($object, $modelRow);
        $setProduct = $this->createProductInstance();
        $this->setCommonProperties($product, $setProduct);
        $setProduct->setId($object['Id'] ?? $object['id'] ?? null);
        $setProduct->setModelId($object['ModelId'] ?? $object['modelId'] ?? null);

        $getMethod = 'get' . (new ReflectionClass($setProduct))->getShortName() . 'Product';
        return $setProduct->$getMethod();
    }
    public function DeleteQuery($object, $connection)
    {
        $product = $this->createProductInstance();
        $product->setId($object[0]['id'] ?? $object['id']);
        $deleteMethod = 'delete' . (new ReflectionClass($product))->getShortName() . 'Product';
        $product->$deleteMethod($connection);
    }
    public static function SelectProducts($connection): array
    {
        $productArray = [];
        $productQuery = "SELECT * FROM products";
        $productResult = $connection->query($productQuery);

        if ($productResult->num_rows > 0) {
            while ($productRow = $productResult->fetch_assoc()) {
                $product = QueriesFactory::createInstance($productRow['ModelType']);
                $productArray[] = $product->SelectQuery($productRow, $connection);
            }
        }

        return $productArray;
    }
}