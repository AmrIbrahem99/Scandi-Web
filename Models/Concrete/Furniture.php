<?php
namespace ScandiWeb\Models\Concrete;
use ScandiWeb\Models\Contract\BaseProductType;
class Furniture extends BaseProductType
{
    private $height;
    private $width;
    private $length;

    protected function initializeSpecificFields(): void
    {
        $this->tableName = 'furniture';
        $this->specificFields = [
            'height' => 'float',
            'width' => 'float',
            'length' => 'float'
        ];
        $this->setModelType('Furniture');
    }

    public function setHeight($height): Furniture
    {
        $this->height = $height;
        return $this;
    }

    public function setWidth($width): Furniture
    {
        $this->width = $width;
        return $this;
    }

    public function setLength($length): Furniture
    {
        $this->length = $length;
        return $this;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getLength()
    {
        return $this->length;
    }

    protected function getSpecificProductData(): array
    {
        return [
            'height' => $this->getHeight(),
            'width' => $this->getWidth(),
            'length' => $this->getLength(),
            'ModelType' => $this->getModelType()
        ];
    }

    public function insertFurniture($connection): ?bool
    {
        return $this->insertSpecificProduct($connection);
    }

    public function deleteFurnitureProduct($connection): ?bool
    {
        return $this->deleteSpecificProduct($connection);
    }

    public function getFurnitureProduct(): array
    {
        $baseProduct = parent::getProduct();
        return array_merge($baseProduct, $this->getSpecificProductData());
    }
}