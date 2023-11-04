<?php

class DiscountedProduct extends Product {
    private $discount;

    public function __construct(Product $product, $discount) {
        parent::__construct($product->getId(), $product->getName(), $product->getPrice(), $product->getDescription(), $product->getImagePath());
        $this->discount = $discount;
    }
    public function __toString()
    {
        $parentString = parent::__toString();
        $discountInfo = "Скидка: " . ($this->discount * 100) . "%<br>";
        $discountedPrice = "Цена со скидкой: " . $this->getDiscountedPrice() . "<br>";
        return $parentString . $discountInfo.$discountedPrice;
    }


    public function getDiscountedPrice() {
        $discountedPrice = $this->getPrice() * (1 - $this->discount);
        return $discountedPrice;
    }
}

