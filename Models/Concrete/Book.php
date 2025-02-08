<?php
namespace ScandiWeb\Models\Concrete;
use ScandiWeb\Models\Contract\BaseProductType;
class Book extends BaseProductType
{
    private $weight;

    protected function initializeSpecificFields(): void
    {
        $this->tableName = 'book';
        $this->specificFields = ['weight' => 'float'];
        $this->setModelType('Book');
    }

    public function setWeight($weight): Book
    {
        $this->weight = $weight;
        return $this;
    }

    public function getWeight()
    {
        return $this->weight;
    }

    protected function getSpecificProductData(): array
    {
        return [
            'weight' => $this->getWeight(),
            'ModelType' => $this->getModelType()
        ];
    }

    public function insertBook($connection): ?bool
    {
        return $this->insertSpecificProduct($connection);
    }

    public function deleteBookProduct($connection): ?bool
    {
        return $this->deleteSpecificProduct($connection);
    }

    public function getBookProduct(): array
    {
        $baseProduct = parent::getProduct();
        return array_merge($baseProduct, $this->getSpecificProductData());
    }
}
