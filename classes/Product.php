
<?php
// ============================================
// OLMS Product Class
// Represents product information
// ============================================

class Product
{
    // Product attributes
    private $productID;
    private $name;
    private $description;
    private $price;
    private $expiryDate;
    private $quantity;

    // Product ID
    public function getProductID()
    {
        return $this->productID;
    }

    public function setProductID($productID)
    {
        if (
            filter_var(
                $productID,
                FILTER_VALIDATE_INT,
                ["options" => ["min_range" => 1]]
            ) === false
        ) {
            throw new InvalidArgumentException(
                "Product ID must be a positive integer."
            );
        }

        $this->productID = (int) $productID;
    }

    // Product Name
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        if (!is_string($name) || trim($name) === "") {
            throw new InvalidArgumentException(
                "Product name cannot be empty."
            );
        }

        if (strlen($name) > 255) {
            throw new InvalidArgumentException(
                "Product name must not exceed 255 bytes."
            );
        }

        $this->name = trim($name);
    }

    // Product Description
    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        if (!is_string($description) || trim($description) === "") {
            throw new InvalidArgumentException(
                "Product description cannot be empty."
            );
        }

        if (strlen($description) > 255) {
            throw new InvalidArgumentException(
                "Product description must not exceed 255 bytes."
            );
        }

        $this->description = trim($description);
    }

    // Product Price
    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price)
    {
        if (
            !is_scalar($price) ||
            is_bool($price) ||
            !is_numeric($price) ||
            !is_finite((float) $price) ||
            (float) $price <= 0 ||
            (float) $price > 99999999.99
        ) {
            throw new InvalidArgumentException(
                "Product price must be greater than zero."
            );
        }

        $this->price = round((float) $price, 2);
    }

    // Product Expiry Date
    public function getExpiryDate()
    {
        return $this->expiryDate;
    }

    public function setExpiryDate($expiryDate)
    {
        if (!is_string($expiryDate)) {
            throw new InvalidArgumentException(
                "Invalid expiry date."
            );
        }

        $date = DateTimeImmutable::createFromFormat(
            "!Y-m-d",
            $expiryDate
        );

        if (
            !$date ||
            $date->format("Y-m-d") !== $expiryDate
        ) {
            throw new InvalidArgumentException(
                "Expiry date must use YYYY-MM-DD format."
            );
        }

        $this->expiryDate = $expiryDate;
    }

    // Product Quantity
    public function getQuantity()
    {
        return $this->quantity;
    }

    public function setQuantity($quantity)
    {
        if (
            filter_var(
                $quantity,
                FILTER_VALIDATE_INT,
                [
                    "options" => [
                        "min_range" => 0,
                        "max_range" => 2147483647
                    ]
                ]
            ) === false
        ) {
            throw new InvalidArgumentException(
                "Quantity must be a non-negative integer."
            );
        }

        $this->quantity = (int) $quantity;
    }
}
