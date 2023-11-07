<?php

use Project\Product\DigitalProduct;
use Project\Product\PhysicalProduct;
use Project\Product\WeightedProduct;

require 'Project/Product/AbstractProduct.php';
require 'Project/Product/DigitalProduct.php';
require 'Project/Product/PhysicalProduct.php';
require 'Project/Product/WeightedProduct.php';


$digitalProduct = new DigitalProduct('Цифровой товар', 20,2);
$physicalProduct = new PhysicalProduct('Физический товар', 20, 2);
$weightedProduct = new WeightedProduct('Товар на вес', 20, 2.5);


echo "Доход от ". $digitalProduct->getName(). ": ". $digitalProduct->calculateFinalCost() . PHP_EOL;
echo "Доход от ". $physicalProduct->getName(). ": ". $physicalProduct->calculateFinalCost() . PHP_EOL;
echo "Доход от ". $weightedProduct ->getName(). ": ". $weightedProduct->calculateFinalCost() . PHP_EOL;



