<?php
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

class PhysicalProduct extends AbstractProduct
{
    protected $quantity;

    public function __construct($name, $price, $quantity)
    {
        parent::__construct($name, $price);
        $this->quantity = $quantity;
    }

    public function calculateFinalCost()
    {
        return $this->price * $this->quantity;
    }
}
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

$digitalProduct = new DigitalProduct('Цифровой товар', 20,2);
$physicalProduct = new PhysicalProduct('Физический товар', 20, 2);
$weightedProduct = new WeightedProduct('Товар на вес', 20, 2.5);


echo "Доход от ". $digitalProduct->getName(). ": ". $digitalProduct->calculateFinalCost() . "<br>";
echo "Доход от ". $physicalProduct->getName(). ": ". $digitalProduct->calculateFinalCost() . "<br>";
echo "Доход от ". $weightedProduct ->getName(). ": ". $digitalProduct->calculateFinalCost() . "<br>";



