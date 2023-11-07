<?php
namespace Project\Product;

class WeightedProduct extends AbstractProduct
{
    protected $weight;

    public function __construct($name, $price, $weight)
    {
        parent::__construct($name, $price);
        $this->weight = $weight;
    }

    public function calculateFinalCost()
    {
        return $this->price * $this->weight ;
    }
}