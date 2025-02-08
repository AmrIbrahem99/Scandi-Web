<?php

namespace ScandiWeb\Models\Contract;

use mysqli;

abstract class BaseProductType
{
    private ?int $id = null;
    private string $sku;
    private string $name;
    private float $price;
    private ?int $modelId = null;
    private string $modelType;

    protected string $tableName;
    protected array $specificFields = [];

    public function __construct()
    {
        $this->initializeSpecificFields();
    }

    // Abstract methods to be implemented by child classes
    abstract protected function initializeSpecificFields(): void;
    abstract protected function getSpecificProductData(): array;

    // Setters with validation
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setSku(string $sku): self
    {
        $this->sku = $sku;
        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setPrice(float $price): self
    {
        if ($price < 0) {
            throw new \InvalidArgumentException('Price must be a non-negative number.');
        }
        $this->price = $price;
        return $this;
    }

    public function setModelId(?int $modelId): self
    {
        $this->modelId = $modelId;
        return $this;
    }

    public function setModelType(string $modelType): self
    {
        $this->modelType = $modelType;
        return $this;
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getModelId(): ?int
    {
        return $this->modelId;
    }

    public function getModelType(): string
    {
        return $this->modelType;
    }

    // Insert product into the specific table
    protected function insertSpecificProduct($connection): bool
    {
        if ($this->SelectProduct($connection)) {
            throw new \RuntimeException('Product already exists.');
        }

        $fields = array_keys($this->specificFields);
        $placeholders = array_fill(0, count($fields), '?');
        $values = array_map(fn($field) => $this->{"get" . ucfirst($field)}(), $fields);

        $sql = sprintf(
            "INSERT INTO `%s` (%s) VALUES (%s)",
            $this->tableName,
            implode(', ', $fields),
            implode(', ', $placeholders)
        );

        $stmt = $connection->prepare($sql);
        if (!$stmt) {
            throw new \RuntimeException('Failed to prepare statement: ' . $connection->error);
        }

        $stmt->bind_param(str_repeat('s', count($values)), ...$values);
        if ($stmt->execute()) {
            $this->setModelId((int)$connection->insert_id);
            return $this->insertProduct($connection);
        }

        throw new \RuntimeException('Failed to insert specific product: ' . $stmt->error);
    }

    // Insert product into the products table
    public function insertProduct($connection): bool
    {
        $sql = "INSERT INTO products (SKU, Name, Price, ModelId, ModelType) VALUES (?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);

        if (!$stmt) {
            throw new \RuntimeException('Failed to prepare statement: ' . $conn->error);
        }

        $stmt->bind_param(
            'ssdss',
            $this->sku,
            $this->name,
            $this->price,
            $this->modelId,
            $this->modelType
        );

        if ($stmt->execute()) {
            return true;
        }

        throw new \RuntimeException('Failed to insert product: ' . $stmt->error);
    }

    // Delete product
    public function deleteProduct($connection): bool
    {
        $sql = "DELETE FROM products WHERE Id = ?";
        $stmt = $connection->prepare($sql);

        if (!$stmt) {
            throw new \RuntimeException('Failed to prepare statement: ' . $conn->error);
        }

        $stmt->bind_param('i', $this->id);
        if ($stmt->execute()) {
            return true;
        }

        throw new \RuntimeException('Failed to delete product: ' . $stmt->error);
    }

    // Select product by SKU
    public function selectProduct($connection): ?array
    {
        if (!$connection instanceof mysqli) {
            throw new \RuntimeException('Failed to connect to the database: ' . (is_object($connection) ? get_class($connection) : gettype($connection)));
        }
        $sql = "SELECT * FROM products WHERE SKU = ?";
        $stmt = $connection->prepare($sql);

        if (!$stmt) {
            throw new \RuntimeException('Failed to prepare statement: ' . $connection->error);
        }

        $stmt->bind_param('s', $this->sku);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc() ?: null;
    }

    // Select product by ID
    public function selectProductById($connection): ?array
    {
        $sql = "SELECT * FROM products WHERE Id = ?";
        $stmt = $connection->prepare($sql);

        if (!$stmt) {
            throw new \RuntimeException('Failed to prepare statement: ' . $connection->error);
        }

        $stmt->bind_param('i', $this->id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc() ?: null;
    }

    // Delete specific product
    protected function deleteSpecificProduct($connection): bool
    {
        $product = $this->selectProductById($connection);

        if (!$product) {
            throw new \RuntimeException('Product does not exist.');
        }

        $modelId = $product['ModelId'];
        $this->setModelId((int)$modelId);

        $sql = "DELETE FROM `{$this->tableName}` WHERE Id = ?";
        $stmt = $connection->prepare($sql);

        if (!$stmt) {
            throw new \RuntimeException('Failed to prepare statement: ' . $connection->error);
        }

        $stmt->bind_param('i', $modelId);
        if ($stmt->execute() && $this->deleteProduct($connection)) {
            return true;
        }

        throw new \RuntimeException('Failed to delete specific product: ' . $stmt->error);
    }

    // Get basic product information
    protected function getProduct(): array
    {
        return [
            'id' => $this->getId(),
            'sku' => $this->getSku(),
            'name' => $this->getName(),
            'price' => $this->getPrice(),
        ];
    }
}
