<?php
namespace Project\Product;
abstract class AbstractProduct
{
    protected $name;

    public function getName()
    {
        return $this->name;
    }

    public function getPrice()
    {
        return $this->price;
    }
    protected $price;

    public function __construct($name, $price)
    {
        $this->name = $name;
        $this->price = $price;
    }

    abstract public function calculateFinalCost();

}