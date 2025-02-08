<?php
namespace ScandiWeb\Models\Concrete;
use ScandiWeb\Models\Contract\BaseProductType;

class DvdDisc extends BaseProductType
{
    private $size;

    protected function initializeSpecificFields(): void
    {
        $this->tableName = 'dvdDisc';
        $this->specificFields = ['size' => 'float'];
        $this->setModelType('Dvd');
    }

    public function setSize($size): DvdDisc
    {
        $this->size = $size;
        return $this;
    }

    public function getSize()
    {
        return $this->size;
    }

    protected function getSpecificProductData(): array
    {
        return [
            'size' => $this->getSize(),
            'ModelType' => $this->getModelType()
        ];
    }

    public function insertDvdDisc($connection): ?bool
    {
        return $this->insertSpecificProduct($connection);
    }

    public function deleteDVDDiscProduct($connection): ?bool
    {
        return $this->deleteSpecificProduct($connection);
    }

    public function getDVDDiscProduct(): array
    {
        $baseProduct = parent::getProduct();
        return array_merge($baseProduct, $this->getSpecificProductData());
    }
}
