<?php
namespace Project\Product;


class DigitalProduct extends AbstractProduct
{
    protected $quantity;
    public function __construct($name, $price, $quantity)
    {
        parent::__construct($name, $price);
        $this->quantity = $quantity;
    }
    public function calculateFinalCost()
    {
        return $this->price/2 * $this->quantity;
    }
}