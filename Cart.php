<?php

class Cart {
    private $products = [];

    public function addProduct(Product $product, $quantity) {
        $this->products[] = ['product' => $product, 'quantity' => $quantity];
    }

    public function removeProduct(Product $product) {
        foreach ($this->products as $key => $item) {
            if ($item['product'] === $product) {
                unset($this->products[$key]);
                break;
            }
        }
    }

    public function getProducts() {
        return $this->products;
    }

    public function calculateTotalPrice() {
        $totalPrice = 0;
        foreach ($this->products as $item) {
            $product = $item['product'];
            $quantity = $item['quantity'];

            if ($product instanceof DiscountedProduct) {
                $discountedPrice = $product->getDiscountedPrice();
                $totalPrice += $discountedPrice * $quantity;
            } else {
                $totalPrice += $product->getPrice() * $quantity;
            }
        }
        return $totalPrice;
    }

}
?>
